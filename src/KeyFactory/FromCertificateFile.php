<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt\KeyFactory;

use Jose\Component\Core\JWK;
use Jose\Component\KeyManagement\JWKFactory;
use Yiisoft\Auth\Jwt\KeyFactoryInterface;

/**
 * Creates JWK from a certificate file.
 *
 * @codeCoverageIgnore
 */
final class FromCertificateFile implements KeyFactoryInterface
{
    public function __construct(private string $file)
    {
    }

    public function create(array $additionalValues = []): JWK
    {
        return JWKFactory::createFromCertificateFile($this->file, $additionalValues);
    }
}
