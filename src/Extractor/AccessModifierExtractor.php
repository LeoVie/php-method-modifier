<?php

declare(strict_types=1);

namespace LeoVie\PhpMethodModifier\Extractor;

use LeoVie\PhpMethodModifier\Model\AccessModifier\AccessModifier;
use LeoVie\PhpMethodModifier\Model\AccessModifier\AccessModifierCollection;

class AccessModifierExtractor
{
    private const ACCESS_MODIFIER_PATTERN = '@^(\S+)@';

    public function __construct(
        private AccessModifierCollection $accessModifierCollection
    )
    {
    }

    public function extract(string $code): ?AccessModifier
    {
        preg_match(self::ACCESS_MODIFIER_PATTERN, $code, $matches);
        $potentialAccessModifier = array_shift($matches);

        if ($potentialAccessModifier === null) {
            return null;
        }

        foreach ($this->accessModifierCollection->getAll() as $accessModifier) {
            if (strtolower($potentialAccessModifier) === $accessModifier->getName()) {
                return $accessModifier;
            }
        }

        return null;
    }
}