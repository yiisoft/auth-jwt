<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt;

use Jose\Component\Core\JWK;

/**
 * Key factory creates a JSON Web Key.
 *
 * @see https://tools.ietf.org/html/rfc7517
 */
interface KeyFactoryInterface
{
    /**
     * Create a key with additional values.
     *
     *
     * @return JWK JSON Web Key.
     */
    public function create(array $additionalValues = []): JWK;
}
