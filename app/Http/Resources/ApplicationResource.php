<?php

namespace App\Http\Resources;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/** @mixin Application */
class ApplicationResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        /** @var Collection<string,string> $settings */
        $settings = new Collection($this->more_configs);

        return [
            'application' => [
                'site_name' => $this->site_name,
                'site_description' => $this->site_description,
            ],

            'settings' => $settings->except('maintenance'),
            'maintenance' => [
                'enabled' => $this->more_configs['maintenance']['maintenance_mode'] ?? false,
            ],
        ];
    }
}
