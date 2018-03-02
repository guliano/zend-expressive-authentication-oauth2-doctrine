<?php

return [
    'driver' => [
        'orm_default' => [
            'class' => Doctrine\ORM\Mapping\Driver\XmlDriver::class,
            'paths' => [
                realpath(__DIR__ . '/mapping/'),
            ],
        ],
    ],
];
