<?php

declare(strict_types=1);

namespace Setono\SyliusSnapchatPlugin\Tag;

use Setono\TagBag\Tag\TwigTagInterface;

interface SnaptrTagInterface extends TwigTagInterface
{
    public const EVENT_ADD_TO_CART = 'ADD_CART';

    public const EVENT_PURCHASE = 'PURCHASE';

    public const EVENT_VIEW_CONTENT = 'VIEW_CONTENT';
}
