<?php

declare(strict_types=1);

namespace App\DBAL\Types\Enum;

enum UserRoleTypeEnum: string
{
    case ROLE_USER = 'ROLE_USER';
    case ROLE_ADMIN = 'ROLE_ADMIN';

    public static function fromName(string $name): self
    {
        foreach (self::cases() as $role) {
            if ($name === $role->name) {
                return $role;
            }
        }
        throw new \ValueError("$name is not a valid backing value for enum ".self::class);
    }
}
