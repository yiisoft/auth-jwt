<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt\KeyFactory;

use Jose\Component\Core\JWK;
use Jose\Component\KeyManagement\JWKFactory;
use Yiisoft\Auth\Jwt\KeyFactoryInterface;

// @codeCoverageIgnore
final class FromCertificateFile implements KeyFactoryInterface
{
    private string $file;

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    public function create(array $additionalValues = []): JWK
    {
        return JWKFactory::createFromCertificateFile($this->file, $additionalValues);
    }
}
