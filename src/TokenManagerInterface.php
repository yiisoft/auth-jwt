<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt;

/**
 * The interface that creates and decodes JWT Signature.
 */
interface TokenManagerInterface
{
    /**
     * @param array $payload
     * @return string
     */
    public function createToken(array $payload): string;

    /**
     * @param string $token
     * @return array|null
     */
    public function getClaims(string $token): ?array;
}
