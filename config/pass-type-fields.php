<?php

return [
    'apple' => [
        'generic' => [
            'field_groups' => ['header', 'primary', 'secondary', 'auxiliary', 'back'],
            'constraints' => [
                'header' => ['max' => 3],
                'primary' => ['max' => 3],
                'secondary' => ['max' => 4],
                'auxiliary' => ['max' => 4],
                'back' => ['max' => null],
                'requires' => [],
            ],
        ],
        'coupon' => [
            'field_groups' => ['header', 'primary', 'secondary', 'auxiliary', 'back'],
            'constraints' => [
                'header' => ['max' => 3],
                'primary' => ['max' => 3],
                'secondary' => ['max' => 4],
                'auxiliary' => ['max' => 4],
                'back' => ['max' => null],
                'requires' => [],
            ],
        ],
        'eventTicket' => [
            'field_groups' => ['header', 'primary', 'secondary', 'auxiliary', 'back'],
            'constraints' => [
                'header' => ['max' => 3],
                'primary' => ['max' => 3],
                'secondary' => ['max' => 4],
                'auxiliary' => ['max' => 4],
                'back' => ['max' => null],
                'requires' => [],
            ],
        ],
        'storeCard' => [
            'field_groups' => ['header', 'primary', 'secondary', 'auxiliary', 'back'],
            'constraints' => [
                'header' => ['max' => 3],
                'primary' => ['max' => 3],
                'secondary' => ['max' => 4],
                'auxiliary' => ['max' => 4],
                'back' => ['max' => null],
                'requires' => [],
            ],
        ],
        'boardingPass' => [
            'field_groups' => ['header', 'primary', 'secondary', 'auxiliary', 'back'],
            'constraints' => [
                'header' => ['max' => 3],
                'primary' => ['max' => 3],
                'secondary' => ['max' => 4],
                'auxiliary' => ['max' => 4],
                'back' => ['max' => null],
                'requires' => ['transitType'],
            ],
        ],
        'stampCard' => [
            'field_groups' => ['header', 'primary', 'secondary', 'auxiliary', 'back'],
            'constraints' => [
                'header' => ['max' => 3],
                'primary' => ['max' => 3],
                'secondary' => ['max' => 4],
                'auxiliary' => ['max' => 4],
                'back' => ['max' => null],
                'requires' => [],
            ],
        ],
    ],
    'google' => [
        'generic' => [
            'field_groups' => ['header', 'primary', 'secondary', 'auxiliary', 'back'],
            'constraints' => [
                'header' => ['max' => 3],
                'primary' => ['max' => 3],
                'secondary' => ['max' => 4],
                'auxiliary' => ['max' => 4],
                'back' => ['max' => null],
                'requires' => [],
            ],
        ],
        'offer' => [
            'field_groups' => ['header', 'primary', 'secondary', 'auxiliary', 'back'],
            'constraints' => [
                'header' => ['max' => 3],
                'primary' => ['max' => 3],
                'secondary' => ['max' => 4],
                'auxiliary' => ['max' => 4],
                'back' => ['max' => null],
                'requires' => [],
            ],
        ],
        'loyalty' => [
            'field_groups' => ['header', 'primary', 'secondary', 'auxiliary', 'back'],
            'constraints' => [
                'header' => ['max' => 3],
                'primary' => ['max' => 3],
                'secondary' => ['max' => 4],
                'auxiliary' => ['max' => 4],
                'back' => ['max' => null],
                'requires' => [],
            ],
        ],
        'eventTicket' => [
            'field_groups' => ['header', 'primary', 'secondary', 'auxiliary', 'back'],
            'constraints' => [
                'header' => ['max' => 3],
                'primary' => ['max' => 3],
                'secondary' => ['max' => 4],
                'auxiliary' => ['max' => 4],
                'back' => ['max' => null],
                'requires' => [],
            ],
        ],
        'boardingPass' => [
            'field_groups' => ['header', 'primary', 'secondary', 'auxiliary', 'back'],
            'constraints' => [
                'header' => ['max' => 3],
                'primary' => ['max' => 3],
                'secondary' => ['max' => 4],
                'auxiliary' => ['max' => 4],
                'back' => ['max' => null],
                'requires' => ['transitType'],
            ],
        ],
        'transit' => [
            'field_groups' => ['header', 'primary', 'secondary', 'auxiliary', 'back'],
            'constraints' => [
                'header' => ['max' => 3],
                'primary' => ['max' => 3],
                'secondary' => ['max' => 4],
                'auxiliary' => ['max' => 4],
                'back' => ['max' => null],
                'requires' => ['transitType'],
            ],
        ],
        'stampCard' => [
            'field_groups' => ['header', 'primary', 'secondary', 'auxiliary', 'back'],
            'constraints' => [
                'header' => ['max' => 3],
                'primary' => ['max' => 3],
                'secondary' => ['max' => 4],
                'auxiliary' => ['max' => 4],
                'back' => ['max' => null],
                'requires' => [],
            ],
        ],
    ],
];
