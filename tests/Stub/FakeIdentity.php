<?php

declare(strict_types=1);

namespace Yiisoft\Auth\Jwt\Tests\Stub;

use Yiisoft\Auth\IdentityInterface;

final class FakeIdentity implements IdentityInterface
{
    private ?string $id;

    public function __construct(?string $id)
    {
        $this->id = $id;
    }

    public function getId(): ?string
    {
        return $this->id;
    }
}
