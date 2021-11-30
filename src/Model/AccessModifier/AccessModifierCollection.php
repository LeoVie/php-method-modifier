<?php

namespace LeoVie\PhpMethodModifier\Model\AccessModifier;

class AccessModifierCollection
{
    /** @param \Iterator<int, AccessModifier> $accessModifiers */
    public function __construct(private iterable $accessModifiers)
    {
    }

    /** @return AccessModifier[] */
    public function getAll(): array
    {
        return iterator_to_array($this->accessModifiers);
    }
}