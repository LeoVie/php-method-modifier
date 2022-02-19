<?php

namespace LeoVie\PhpMethodModifier\Model\AccessModifier;

/** @psalm-immutable */
class ProtectedModifier implements AccessModifier
{
    private const NAME = 'protected';

    public function getName(): string
    {
        return self::NAME;
    }

    public static function create(): self
    {
        return new self();
    }
}