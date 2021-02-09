<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt\KeyFactory;

use Jose\Component\Core\JWK;
use Jose\Component\KeyManagement\JWKFactory;
use Yiisoft\Auth\Jwt\KeyFactoryInterface;

/**
 * @codeCoverageIgnore
 */
final class FromPKCS12CertificateFile implements KeyFactoryInterface
{
    private string $file;
    private string $secret;

    public function __construct(string $file, string $secret)
    {
        $this->file = $file;
        $this->secret = $secret;
    }

    public function create(array $additionalValues = []): JWK
    {
        return JWKFactory::createFromPKCS12CertificateFile($this->file, $this->secret, $additionalValues);
    }
}
