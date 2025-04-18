<?php

use ninshikiProject\GeneralSettings\Enums\TypeFieldEnum;

return [
    'show_application_tab' => true,
    'expiration_cache_config_time' => 60,
    'show_custom_tabs' => true,
    'custom_tabs' => [
        'notifications' => [
            'label' => 'Notifications',
            'icon' => 'heroicon-o-bell-alert',
            'columns' => 4,
            'fields' => [
                'mention_user' => [
                    'type' => TypeFieldEnum::Boolean,
                    'label' => 'Mention User',
                ],
                'invitation' => [
                    'type' => TypeFieldEnum::Boolean,
                    'label' => 'User Invitation',
                ],
                'recognized' => [
                    'type' => TypeFieldEnum::Boolean,
                    'label' => 'User Recognized',
                ],
            ],
        ],
        'gift' => [
            'label' => 'Gift',
            'icon' => 'heroicon-o-gift',
            'columns' => 4,
            'fields' => [
                'enable' => [
                    'type' => TypeFieldEnum::Toggle,
                    'label' => 'Enabled',
                ],
                'count_limit' => [
                    'type' => TypeFieldEnum::Text,
                    'label' => 'Limit Count',
                    'placeholder' => '10',
                    'required' => true,
                    'rules' => 'required|numeric|min:1',
                ],
                'frequency' => [
                    'type' => TypeFieldEnum::Select,
                    'label' => 'Frequency',
                    'placeholder' => 'Select Frequency',
                    'required' => true,
                    'options' => \App\Enum\GiftFrequencyEnum::class,
                ],
                'exchange_rate' => [
                    'type' => TypeFieldEnum::Text,
                    'label' => 'Coins Conversion Rate',
                    'help' => 'This will be used when converting the gift coins to wallet.',
                    'placeholder' => '0.50',
                    'required' => true,
                    'rules' => 'required|numeric|decimal:0,2',
                ],
            ],
        ],
        'maintenance' => [
            'label' => 'Maintenance',
            'icon' => 'heroicon-o-wrench-screwdriver',
            'columns' => 1,
            'fields' => [
                'maintenance_mode' => [
                    'type' => TypeFieldEnum::Toggle,
                    'label' => 'Maintenance Mode',
                ],
            ],
        ],
    ],
];
