<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt;

/**
 * The interface that creates and decodes JWT Signature.
 */
interface TokenManagerInterface
{
    public function createToken(array $payload): string;

    public function getClaims(string $token): ?array;
}
