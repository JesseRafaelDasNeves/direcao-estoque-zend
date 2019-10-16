<?php

namespace Fornecedor;

use Zend\Router\Http\Segment;

return [
    /*'controllers' => [
        'factories' => [
            Controller\FornecedorController::class => InvokableFactory::class,
        ],
    ],*/

    'router' => [
        'routes' => [
            'fornecedor' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/fornecedor[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\FornecedorController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            'fornecedor' => __DIR__ . '/../view',
        ],
    ]
];
