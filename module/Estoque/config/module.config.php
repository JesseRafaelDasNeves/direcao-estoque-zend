<?php

namespace Estoque;

use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'entrada' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/entrada[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\EntradaController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'item-entrada' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/entrada[/:identrada]/item-entrada[/:action[/:id]]',
                    'constraints' => [
                        'action'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'identrada' => '[0-9]+',
                        'id'        => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ItemEntradaController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            'entrada'      => __DIR__ . '/../view',
            'item-entrada' => __DIR__ . '/../view',
        ],
    ]
];
