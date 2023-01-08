<?php

declare(strict_types=1);

namespace App\DBAL\Types\Enum;

enum UserRoleTypeEnum
{
    case ROLE_USER;
    case ROLE_ADMIN;
//    public const ROLE_USER = 'ROLE_USER';
//    public const ROLE_ADMIN = 'ROLE_ADMIN';
//
//    public static function getValues(): array
//    {
//        return [
//            self::ROLE_USER    => self::ROLE_USER,
//            self::ROLE_ADMIN   => self::ROLE_ADMIN,
//        ];
//    }
//
//    public static function isValid(string $role): bool
//    {
//        return in_array($role, self::getValues());
//    }
}
