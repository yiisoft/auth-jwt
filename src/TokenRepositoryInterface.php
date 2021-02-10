<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt;

/**
 * Token repository is getting a list of claims for a token.
 */
interface TokenRepositoryInterface
{
    /**
     * Get claims from a token.
     * Claims are statements about an entity (typically, the user) and additional data.
     *
     * @param string $token Token to get claims from.
     * @param string|null $format
     *
     * @return array|null
     */
    public function getClaims(string $token, ?string &$format = null): ?array;
}
