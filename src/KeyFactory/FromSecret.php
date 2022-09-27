<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt\KeyFactory;

use Jose\Component\Core\JWK;
use Jose\Component\KeyManagement\JWKFactory;
use Yiisoft\Auth\Jwt\KeyFactoryInterface;

/**
 * Creates JWK from a secret.
 */
final class FromSecret implements KeyFactoryInterface
{
    public function __construct(private string $secret)
    {
    }

    public function create(array $additionalValues = []): JWK
    {
        return JWKFactory::createFromSecret($this->secret, $additionalValues);
    }
}
