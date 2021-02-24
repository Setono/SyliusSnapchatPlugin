<?php

declare(strict_types=1);

namespace Setono\SyliusSnapchatPlugin\EventListener;

use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusSnapchatPlugin\Builder\PurchaseBuilder;
use Setono\SyliusSnapchatPlugin\Context\PixelContextInterface;
use Setono\SyliusSnapchatPlugin\Event\BuilderEvent;
use Setono\SyliusSnapchatPlugin\Tag\SnaptrTag;
use Setono\SyliusSnapchatPlugin\Tag\SnaptrTagInterface;
use Setono\SyliusSnapchatPlugin\Tag\Tags;
use Setono\TagBag\Tag\TagInterface;
use Setono\TagBag\TagBagInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class PurchaseSubscriber extends TagSubscriber
{
    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        TagBagInterface $tagBag,
        PixelContextInterface $pixelContext,
        EventDispatcherInterface $eventDispatcher,
        RequestStack $requestStack,
        FirewallMap $firewallMap,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($tagBag, $pixelContext, $eventDispatcher, $requestStack, $firewallMap);

        $this->orderRepository = $orderRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'track',
        ];
    }

    public function track(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$event->isMasterRequest() || !$this->isShopContext($request)) {
            return;
        }

        if (!$request->attributes->has('_route')) {
            return;
        }

        $route = $request->attributes->get('_route');
        if ('sylius_shop_order_thank_you' !== $route) {
            return;
        }

        /** @var mixed $orderId */
        $orderId = $request->getSession()->get('sylius_order_id');

        if (null === $orderId) {
            return;
        }

        $order = $this->orderRepository->find($orderId);
        if (!$order instanceof OrderInterface) {
            return;
        }

        if (!$this->hasPixels()) {
            return;
        }

        $builder = new PurchaseBuilder();
        $builder->setValue($this->moneyFormatter->format($order->getTotal()))
            ->setCurrency((string) $order->getCurrencyCode())
        ;

        foreach ($order->getItems() as $orderItem) {
            $variant = $orderItem->getVariant();
            if (null === $variant) {
                continue;
            }

            $builder->addItemId($variant->getCode());
        }

        $this->eventDispatcher->dispatch(new BuilderEvent($builder, $order));

        $this->tagBag->addTag(
            (new SnaptrTag(SnaptrTagInterface::EVENT_PURCHASE, $builder))
                ->setSection(TagInterface::SECTION_BODY_END)
                ->setName(Tags::TAG_PURCHASE)
        );
    }
}
