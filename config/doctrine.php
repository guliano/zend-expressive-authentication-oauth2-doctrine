<?php

return [
    'doctrine' => [
        'driver' => [
            'orm_default' => [
                'paths' => [
                    'class' => Doctrine\ORM\Mapping\Driver\XmlDriver::class,
                    __DIR__ . '/mapping/'
                ],
            ],
        ],
    ],
];
