<?php

namespace LeoVie\PhpMethodModifier\Model\MethodContext;

use LeoVie\PhpMethodModifier\Model\AccessModifier\AccessModifier;

class ClassMethodContext implements MethodContext
{
    private function __construct(
        private AccessModifier $accessModifier
    )
    {
    }

    public static function create(AccessModifier $accessModifier): self
    {
        return new self($accessModifier);
    }

    public function getAccessModifier(): AccessModifier
    {
        return $this->accessModifier;
    }
}