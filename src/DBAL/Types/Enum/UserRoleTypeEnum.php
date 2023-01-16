<?php

declare(strict_types=1);

namespace App\DBAL\Types\Enum;

enum UserRoleTypeEnum: string
{
    case ROLE_USER = 'ROLE_USER';
    case ROLE_ADMIN = 'ROLE_ADMIN';
}
