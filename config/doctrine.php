<?php

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\Driver\XmlDriver;

return [
    'driver' => [
        'orm_default' => [
            'class' => MappingDriverChain::class,
            'drivers' => [
                'Zend\Expressive\Authentication\OAuth2\Entity' => 'oauth2_entity',
                'Zend\Expressive\Authentication\OAuth2\Doctrine\Entity' => 'oauth2_user_entity',
            ],
        ],
        'oauth2_entity' => [
            'class' => Doctrine\ORM\Mapping\Driver\XmlDriver::class,
            'paths' => [
                realpath(__DIR__ . '/mapping/'),
            ]
        ],
        'oauth2_user_entity' => [
            'class' => Doctrine\ORM\Mapping\Driver\XmlDriver::class,
            'paths' => [
                realpath(__DIR__ . '/../data/'),
            ]
        ],
    ],
];
