<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt\KeyFactory;

use Jose\Component\Core\JWK;
use Jose\Component\KeyManagement\JWKFactory;
use Yiisoft\Auth\Jwt\KeyFactoryInterface;

final class FromSecret implements KeyFactoryInterface
{
    private string $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    public function create(array $additionalValues = []): JWK
    {
        return JWKFactory::createFromSecret($this->secret, $additionalValues);
    }
}
