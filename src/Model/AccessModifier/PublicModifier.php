<?php

namespace LeoVie\PhpMethodModifier\Model\AccessModifier;

/** @psalm-immutable */
class PublicModifier implements AccessModifier
{
    private const NAME = 'public';

    public function getName(): string
    {
        return self::NAME;
    }

    public static function create(): self
    {
        return new self();
    }
}