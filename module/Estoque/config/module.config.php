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
            'saida' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/saida[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\SaidaController::class,
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
            'item-saida' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/saida[/:idsaida]/item-saida[/:action[/:id]]',
                    'constraints' => [
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'idsaida' => '[0-9]+',
                        'id'      => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ItemSaidaController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'estoque-produto' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/estoque-produto[/:action[/:idproduto]]',
                    'constraints' => [
                        'action'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'idproduto' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\EstoqueController::class,
                        'action'     => 'qtdeestoqueproduto',
                    ],
                ],
            ],
            'movimentacoes-estoque' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/estoque/movimentacoes',
                    'defaults' => [
                        'controller' => Controller\EstoqueController::class,
                        'action'     => 'movimentacoes',
                    ],
                ],
            ],
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            'entrada'      => __DIR__ . '/../view',
            'saida'        => __DIR__ . '/../view',
            'item-entrada' => __DIR__ . '/../view',
            'item-saida'   => __DIR__ . '/../view',
        ],
    ]
];
