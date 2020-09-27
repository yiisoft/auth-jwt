<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt;

interface TokenManagerInterface
{
    public function createToken(array $payload): string;
    public function getClaims(string $token): ?array;
}
