<?php

namespace LeoVie\PhpMethodModifier\Model\AccessModifier;

class PrivateModifier implements AccessModifier
{
    private const NAME = 'private';

    public function getName(): string
    {
        return self::NAME;
    }

    public static function create(): self
    {
        return new self();
    }
}