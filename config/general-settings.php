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
    ],
];
