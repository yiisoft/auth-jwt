<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt\Tests\Stub;

use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;

final class FakeIdentityRepository implements IdentityRepositoryInterface
{
    private ?IdentityInterface $returnIdentity;

    public function __construct(?IdentityInterface $returnIdentity)
    {
        $this->returnIdentity = $returnIdentity;
    }

    public function findIdentity(string $id): ?IdentityInterface
    {
        return $this->returnIdentity->getId() === $id ? $this->returnIdentity : null;
    }

    public function findIdentityByToken(string $token, string $type = null): ?IdentityInterface
    {
        return $this->returnIdentity;
    }
}
