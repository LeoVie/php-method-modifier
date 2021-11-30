<?php

namespace LeoVie\PhpMethodModifier\Model;

use LeoVie\PhpMethodModifier\Model\MethodContext\MethodContext;

class Method
{
    private function __construct(
        private string        $code,
        private MethodContext $methodContext
    )
    {
    }

    public static function create(string $code, MethodContext $methodContext): self
    {
        return new self($code, $methodContext);
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getMethodContext(): MethodContext
    {
        return $this->methodContext;
    }

    public function canBeModifiedToNonClassContext(): bool
    {
        return !str_contains($this->code, '$this');
    }
}