<?php

declare(strict_types=1);

namespace Setono\SyliusSnapchatPlugin\Builder;

interface BuilderInterface
{
    public const CONTENT_TYPE_PRODUCT = 'product';

    public const CONTENT_TYPE_PRODUCT_GROUP = 'product_group';

    public function getData(): array;

    public function getJson(): string;
}
