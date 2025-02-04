<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: MentionParser.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Services\Mention;

class MentionParser extends \Xetaio\Mentions\Parser\MentionParser
{
    protected function replace(array $match): string
    {
        return $match[0];
    }
}
