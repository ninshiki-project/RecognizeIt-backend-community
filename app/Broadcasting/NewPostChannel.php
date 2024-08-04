<?php

namespace App\Broadcasting;

use App\Models\User;

class NewPostChannel
{
    public function __construct() {}

    public function join(User $user): array|bool
    {
        return true;
    }
}
