<?php

namespace LeoVie\PhpMethodModifier\Model\AccessModifier;

/** @psalm-immutable */
class AccessModifierCollection
{
    /** @param iterable<int, AccessModifier> $accessModifiers */
    public function __construct(private iterable $accessModifiers)
    {
    }

    /** @return iterable<int, AccessModifier> */
    public function getAll(): iterable
    {
        return $this->accessModifiers;
    }
}