<?php

namespace LeoVie\PhpMethodModifier\Exception;

class MethodCannotBeModifiedToNonClassContext extends \Exception
{
    public static function create(): self
    {
        return new self('Cannot modify method to non class context.');
    }
}