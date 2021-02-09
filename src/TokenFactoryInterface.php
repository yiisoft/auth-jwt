<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt;

/**
 * Token factory creates a token.
 */
interface TokenFactoryInterface
{
    /**
     * Create a token with the payload specified.
     *
     * @param array $payload Payload to make a part of the token.
     * @param string $format Serialization format.
     * @param int|null $signatureIndex The signature index for serialization.
     *
     * @return string Token.
     */
    public function create(array $payload, string $format, ?int $signatureIndex = null): string;
}
