<?php

declare(strict_types=1);

namespace Setono\SyliusSnapchatPlugin\Builder;

abstract class Builder implements BuilderInterface
{
    protected array $data = [];

    public function getData(): array
    {
        return $this->data;
    }

    public function getJson(): string
    {
        return json_encode($this->data, \JSON_THROW_ON_ERROR);
    }
}
