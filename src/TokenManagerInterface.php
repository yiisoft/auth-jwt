<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt;

/**
 * Token manager creates a token and is getting a list of clams for it.
 */
interface TokenManagerInterface
{
    /**
     * Create a token with the payload specified.
     *
     * @param array $payload Payload to make a part of the token.
     *
     * @return string Token.
     */
    public function createToken(array $payload): string;

    /**
     * Get claims from a token.
     * Claims are statements about an entity (typically, the user) and additional data.
     *
     * @param string $token Token to get claims from.
     *
     * @return array|null
     */
    public function getClaims(string $token): ?array;
}
