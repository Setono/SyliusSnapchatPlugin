<?php

declare(strict_types=1);

namespace Setono\SyliusSnapchatPlugin\Event;

use Setono\SyliusSnapchatPlugin\Builder\BuilderInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class BuilderEvent extends Event
{
    private BuilderInterface $builder;

    /** @var mixed|null */
    private $subject;

    /**
     * @param mixed|null $subject
     */
    public function __construct(BuilderInterface $builder, $subject = null)
    {
        $this->builder = $builder;
        $this->subject = $subject;
    }

    public function getBuilder(): BuilderInterface
    {
        return $this->builder;
    }

    /**
     * @return mixed|null
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
