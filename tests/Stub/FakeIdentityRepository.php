<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt\Tests\Stub;

use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;

final class FakeIdentityRepository implements IdentityRepositoryInterface
{
    public function __construct(private ?IdentityInterface $returnIdentity)
    {
    }

    public function findIdentity(string $id): ?IdentityInterface
    {
        return $this->returnIdentity->getId() === $id ? $this->returnIdentity : null;
    }
}
