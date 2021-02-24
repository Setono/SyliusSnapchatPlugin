<?php

declare(strict_types=1);

namespace Setono\SyliusSnapchatPlugin\Builder;

use function assert;

/**
 * @mixin Builder
 */
trait PriceAwareBuilderTrait
{
    public function setCurrency(string $currency): self
    {
        assert($this instanceof Builder);

        $this->data['currency'] = $currency;

        return $this;
    }

    public function setValue(float $value): self
    {
        assert($this instanceof Builder);

        $this->data['price'] = $value;

        return $this;
    }
}
