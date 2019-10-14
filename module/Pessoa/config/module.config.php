<?php

namespace Pessoa;

use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    /*'controllers' => [
        'factories' => [
            Controller\PessoaController::class => InvokableFactory::class,
        ],
    ],*/

    'router' => [
        'routes' => [
            'pessoa' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/pessoa[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\PessoaController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            'pessoa' => __DIR__ . '/../view',
        ],
    ]
];
