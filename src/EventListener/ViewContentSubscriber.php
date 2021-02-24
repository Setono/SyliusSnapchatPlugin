<?php

declare(strict_types=1);

namespace Setono\SyliusSnapchatPlugin\EventListener;

use Setono\SyliusSnapchatPlugin\Builder\ViewContentBuilder;
use Setono\SyliusSnapchatPlugin\Event\BuilderEvent;
use Setono\SyliusSnapchatPlugin\Tag\SnaptrTag;
use Setono\SyliusSnapchatPlugin\Tag\SnaptrTagInterface;
use Setono\SyliusSnapchatPlugin\Tag\Tags;
use Setono\TagBag\Tag\TagInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Product\Model\ProductInterface;

final class ViewContentSubscriber extends TagSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.product.show' => 'track',
        ];
    }

    public function track(ResourceControllerEvent $event): void
    {
        if (!$this->isShopContext() || !$this->hasPixels()) {
            return;
        }

        $product = $event->getSubject();
        if (!$product instanceof ProductInterface) {
            return;
        }

        $builder = new ViewContentBuilder();
        $builder->addItemId((string) $product->getCode())
        ;

        $this->eventDispatcher->dispatch(new BuilderEvent($builder, $product));

        $this->tagBag->addTag(
            (new SnaptrTag(SnaptrTagInterface::EVENT_VIEW_CONTENT, $builder))
                ->setSection(TagInterface::SECTION_BODY_END)
                ->setName(Tags::TAG_VIEW_CONTENT)
        );
    }
}
