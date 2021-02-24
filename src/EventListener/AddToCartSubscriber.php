<?php

declare(strict_types=1);

namespace Setono\SyliusSnapchatPlugin\EventListener;

use Setono\SyliusSnapchatPlugin\Builder\AddToCartBuilder;
use Setono\SyliusSnapchatPlugin\Context\PixelContextInterface;
use Setono\SyliusSnapchatPlugin\Event\BuilderEvent;
use Setono\SyliusSnapchatPlugin\Tag\SnaptrTag;
use Setono\SyliusSnapchatPlugin\Tag\SnaptrTagInterface;
use Setono\SyliusSnapchatPlugin\Tag\Tags;
use Setono\TagBag\Tag\TagInterface;
use Setono\TagBag\TagBagInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class AddToCartSubscriber extends TagSubscriber
{
    private CartContextInterface $cartContext;

    public function __construct(
        TagBagInterface $tagBag,
        PixelContextInterface $pixelContext,
        EventDispatcherInterface $eventDispatcher,
        CartContextInterface $cartContext,
        RequestStack $requestStack,
        FirewallMap $firewallMap
    ) {
        parent::__construct($tagBag, $pixelContext, $eventDispatcher, $requestStack, $firewallMap);

        $this->cartContext = $cartContext;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.order_item.post_add' => [
                'track',
            ],
        ];
    }

    public function track(): void
    {
        if (!$this->isShopContext() || !$this->hasPixels()) {
            return;
        }

        $order = $this->cartContext->getCart();

        if (!$order instanceof OrderInterface) {
            return;
        }

        $builder = new AddToCartBuilder();
        $builder->setCurrency((string) $order->getCurrencyCode())
            ->setValue($this->moneyFormatter->format($order->getTotal()))
        ;

        foreach ($order->getItems() as $item) {
            $variant = $item->getVariant();
            if (null === $variant) {
                continue;
            }

            $builder->addItemId($variant->getCode());
        }

        $this->eventDispatcher->dispatch(new BuilderEvent($builder, $order));

        $this->tagBag->addTag(
            (new SnaptrTag(SnaptrTagInterface::EVENT_ADD_TO_CART, $builder))
                ->setSection(TagInterface::SECTION_BODY_END)
                ->setName(Tags::TAG_ADD_TO_CART)
        );
    }
}
