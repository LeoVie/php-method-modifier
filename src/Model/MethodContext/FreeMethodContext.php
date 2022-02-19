<?php

namespace LeoVie\PhpMethodModifier\Model\MethodContext;

/** @psalm-immutable */
class FreeMethodContext implements MethodContext
{
    public static function create(): self
    {
        return new self();
    }
}