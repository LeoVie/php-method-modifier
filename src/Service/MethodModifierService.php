<?php

namespace LeoVie\PhpMethodModifier\Service;

use LeoVie\PhpMethodModifier\Exception\MethodCannotBeModifiedToNonClassContext;
use LeoVie\PhpMethodModifier\Extractor\AccessModifierExtractor;
use LeoVie\PhpMethodModifier\Model\AccessModifier\AccessModifier;
use LeoVie\PhpMethodModifier\Model\AccessModifier\AccessModifierCollection;
use LeoVie\PhpMethodModifier\Model\Method;
use LeoVie\PhpMethodModifier\Model\MethodContext\ClassMethodContext;
use LeoVie\PhpMethodModifier\Model\MethodContext\FreeMethodContext;

class MethodModifierService
{
    public function __construct(
        private AccessModifierExtractor $accessModifierExtractor
    )
    {
    }

    public function buildMethod(string $code): Method
    {
        $code = trim($code);
        $accessModifier = $this->accessModifierExtractor->extract($code);
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

    public function modifyMethodToNonClassContext(Method $method): Method
    {
        $methodContext = $method->getMethodContext();
        if ($methodContext instanceof FreeMethodContext) {
            return $this->removeStaticModifier($method);
        }

        if (!$method->canBeModifiedToNonClassContext()) {
            throw MethodCannotBeModifiedToNonClassContext::create();
        }

        /** @var ClassMethodContext $methodContext */
        $methodContext = $method->getMethodContext();

        return $this->removeStaticModifier(
            Method::create(
                $this->removeAccessModifier($method->getCode(), $methodContext),
                FreeMethodContext::create()
            )
        );
    }

    private function removeAccessModifier(string $methodCode, ClassMethodContext $methodContext): string
    {
        $code = trim($methodCode);
        /** @var string $codeWithoutAccessModifier */
        $codeWithoutAccessModifier = \Safe\preg_replace(
            \Safe\sprintf('@^%s@', $methodContext->getAccessModifier()->getName()),
            '',
            $code
        );

        return $codeWithoutAccessModifier;
    }

    private function removeStaticModifier(Method $method): Method
    {
        $methodCode = trim($method->getCode());
        /** @var string $codeWithoutStatic */
        $codeWithoutStatic = \Safe\preg_replace('@^static @', '', $methodCode);

        return Method::create(
            $codeWithoutStatic,
            $method->getMethodContext()
        );
    }
}