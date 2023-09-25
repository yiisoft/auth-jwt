<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt\KeyFactory;

use Jose\Component\Core\JWK;
use Jose\Component\KeyManagement\JWKFactory;
use Yiisoft\Auth\Jwt\KeyFactoryInterface;

/**
 * Creates JWK from a password-protected key file.
 *
 * @codeCoverageIgnore
 */
final class FromKeyFile implements KeyFactoryInterface
{
    public function __construct(private string $file, private string $password)
    {
    }

    public function create(array $additionalValues = []): JWK
    {
        return JWKFactory::createFromKeyFile($this->file, $this->password, $additionalValues);
    }
}
