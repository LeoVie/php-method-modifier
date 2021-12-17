<?php

namespace LeoVie\PhpMethodModifier\Service;

use LeoVie\PhpMethodModifier\Exception\MethodCannotBeModifiedToNonClassContext;
use LeoVie\PhpMethodModifier\Model\AccessModifier\AccessModifier;
use LeoVie\PhpMethodModifier\Model\AccessModifier\AccessModifierCollection;
use LeoVie\PhpMethodModifier\Model\Method;
use LeoVie\PhpMethodModifier\Model\MethodContext\ClassMethodContext;
use LeoVie\PhpMethodModifier\Model\MethodContext\FreeMethodContext;

class MethodModifierService
{
    private const ACCESS_MODIFIER_PATTERN = '@^(\S+)@';

    public function __construct(
        private AccessModifierCollection $accessModifierCollection
    )
    {
    }

    public function buildMethod(string $code): Method
    {
        $code = trim($code);
        $accessModifier = $this->extractAccessModifier($code);
        if ($accessModifier === null) {
            return Method::create(
                $code,
                FreeMethodContext::create()
            );
        }

        return Method::create(
            $code,
            ClassMethodContext::create($accessModifier)
        );
    }

    private function extractAccessModifier(string $code): ?AccessModifier
    {
        preg_match(self::ACCESS_MODIFIER_PATTERN, $code, $matches);
        if (!array_key_exists(0, $matches)) {
            return null;
        }

        foreach ($this->accessModifierCollection->getAll() as $accessModifier) {
            if ($matches[0] === $accessModifier->getName()) {
                return $accessModifier;
            }
        }

        return null;
    }

    public function modifyMethodToNonClassContext(Method $method): Method
    {
        $methodContext = $method->getMethodContext();
        if ($methodContext instanceof FreeMethodContext) {
            return $this->removeStaticModifier($method);
        }
        /** @var ClassMethodContext $methodContext */
        $methodContext = $method->getMethodContext();

        if (!$method->canBeModifiedToNonClassContext()) {
            throw MethodCannotBeModifiedToNonClassContext::create();
        }

        return $this->removeStaticModifier(
            Method::create(
                $this->removeAccessModifier($method->getCode(), $methodContext),
                FreeMethodContext::create()
            )
        );
    }

    private function removeAccessModifier(string $methodCode, ClassMethodContext $methodContext): string
    {
        return trim(
            \Safe\preg_replace(
                \Safe\sprintf('@^%s@', $methodContext->getAccessModifier()->getName()),
                '',
                $methodCode
            )
        );
    }

    private function removeStaticModifier(Method $method): Method
    {
        return Method::create(
            trim(
                \Safe\preg_replace('@^static @', '', $method->getCode())
            ),
            $method->getMethodContext()
        );
    }
}