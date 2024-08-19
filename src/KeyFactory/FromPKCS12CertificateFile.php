<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt\KeyFactory;

use Jose\Component\Core\JWK;
use Jose\Component\KeyManagement\JWKFactory;
use Yiisoft\Auth\Jwt\KeyFactoryInterface;

/**
 * Creates JWK from a PKCS12 certificate file.
 *
 * @codeCoverageIgnore
 */
final class FromPKCS12CertificateFile implements KeyFactoryInterface
{
    public function __construct(private readonly string $file, private readonly string $secret)
    {
    }

    public function create(array $additionalValues = []): JWK
    {
        return JWKFactory::createFromPKCS12CertificateFile($this->file, $this->secret, $additionalValues);
    }
}
