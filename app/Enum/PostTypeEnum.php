<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: PostTypeEnum.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Enum;

enum PostTypeEnum: string
{
    case System = 'system';
    case User = 'user';
}
