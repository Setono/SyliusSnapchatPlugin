<?php

declare(strict_types=1);

namespace Setono\SyliusSnapchatPlugin\Builder;

final class PurchaseBuilder extends Builder
{
    use ItemsIdsAwareBuilderTrait,
        PriceAwareBuilderTrait
    ;
}
