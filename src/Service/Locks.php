<?php

declare(strict_types=1);

namespace App\Service;

interface Locks
{
    public const CREATE_NEW_PART = 'create_new_part';
    public const PARSING_PARTS_MODEL = 'parsing_parts_model';
    public const PARSING_PARTS_NODE = 'parsing_parts_node';
}
