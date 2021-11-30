<?php

namespace LeoVie\PhpMethodModifier\Model\MethodContext;

class FreeMethodContext implements MethodContext
{
    public static function create(): self
    {
        return new self();
    }
}