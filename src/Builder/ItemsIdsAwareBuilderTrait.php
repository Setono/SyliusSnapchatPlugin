<?php

declare(strict_types=1);

namespace Setono\SyliusSnapchatPlugin\Builder;

use Webmozart\Assert\Assert;

/**
 * @mixin Builder
 */
trait ItemsIdsAwareBuilderTrait
{
    /**
     * @param mixed $itemId
     */
    public function addItemId($itemId): self
    {
        \assert($this instanceof Builder);

        Assert::scalar($itemId);

        if (!isset($this->data['item_ids'])) {
            $this->data['item_ids'] = [];
        }

        Assert::isArray($this->data['item_ids']);

        $this->data['item_ids'][] = $itemId;

        return $this;
    }
}
