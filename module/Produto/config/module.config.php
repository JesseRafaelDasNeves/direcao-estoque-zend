<?php

namespace Produto;

use Zend\Router\Http\Segment;

return [
    /*'controllers' => [
        'factories' => [
            Controller\ProdutoController::class => InvokableFactory::class,
        ],
    ],*/

    'router' => [
        'routes' => [
            'produto' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/produto[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ProdutoController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            'produto' => __DIR__ . '/../view',
        ],
    ]
];
