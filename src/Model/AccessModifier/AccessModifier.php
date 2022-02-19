<?php

namespace LeoVie\PhpMethodModifier\Model\AccessModifier;

/** @psalm-immutable */
interface AccessModifier
{
    public function getName(): string;
}