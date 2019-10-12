<?php

namespace Auth;

use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'controllers' => [
        'factories' => [
            Controller\AuthController::class => InvokableFactory::class,
        ],
    ],

    'router' => [
        'routes' => [
            'auth' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/auth[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            'auth' => __DIR__ . '/../view',
        ],
    ]
];