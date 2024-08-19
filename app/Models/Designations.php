<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Designations extends Model
{
    use \Sushi\Sushi;

    public function getRows(): array
    {
        return [
            ['id' => 1, 'name' => 'Senior Zoho Developer'],
            ['id' => 2, 'name' => 'Human Resource'],
            ['id' => 3, 'name' => 'Project Manager'],
            ['id' => 4, 'name' => 'Delivery Manager'],
            ['id' => 5, 'name' => 'Quality Assurance'],
            ['id' => 6, 'name' => 'Junior Zoho Developer'],
        ];
    }
}
