<?php

namespace LeoVie\PhpMethodModifier\Model\AccessModifier;

use Iterator;

/** @psalm-immutable */
class AccessModifierCollection
{
    /** @param Iterator<int, AccessModifier> $accessModifiers */
    public function __construct(private iterable $accessModifiers)
    {
    }

    /** @return Iterator<int, AccessModifier> */
    public function getAll(): Iterator
    {
        return $this->accessModifiers;
    }
}