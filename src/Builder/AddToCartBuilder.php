<?php

declare(strict_types=1);

namespace Setono\SyliusSnapchatPlugin\Builder;

final class AddToCartBuilder extends Builder
{
    use ItemsIdsAwareBuilderTrait,
        PriceAwareBuilderTrait
    ;
}
