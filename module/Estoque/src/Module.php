<?php

namespace Estoque;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;

/**
 * Description of Module
 *
 * @author Jessé Rafael das Neves
 */
class Module implements ConfigProviderInterface, ServiceProviderInterface, ControllerProviderInterface {

    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig() {
        return [
            'factories' => [
                Model\EntradaTable::class => function($container) {
                    $tableGateway = $container->get(Model\EntradaTableGateway::class);
                    return new Model\EntradaTable($tableGateway);
                },
                Model\EntradaTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    return Module::newTableGatewayEntrada($dbAdapter);
                },

                Model\ItemEntradaTable::class => function($container) {
                    $tableGateway = $container->get(Model\ItemEntradaTableGateway::class);
                    return new Model\ItemEntradaTable($tableGateway);
                },
                Model\ItemEntradaTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    return Module::newTableGatewayItemEntrada($dbAdapter);
                },

                Model\SaidaTable::class => function($container) {
                    $tableGateway = $container->get(Model\SaidaTableGateway::class);
                    return new Model\SaidaTable($tableGateway);
                },
                Model\SaidaTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    return Module::newTableGatewaySaida($dbAdapter);
                },

                Model\ItemSaidaTable::class => function($container) {
                    $tableGateway = $container->get(Model\ItemSaidaTableGateway::class);
                    return new Model\ItemSaidaTable($tableGateway);
                },
                Model\ItemSaidaTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    return Module::newTableGatewayItemSaida($dbAdapter);
                },

                Model\EstoqueTable::class => function($container) {
                    $tableGateway = $container->get(Model\EstoqueTableGateway::class);
                    return new Model\EstoqueTable($tableGateway);
                },
                Model\EstoqueTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    return Module::newTableGatewayEstoque($dbAdapter);
                },
            ],
        ];
    }

    public function getControllerConfig() {
        return [
            'factories' => [
                Controller\EntradaController::class => function($container) {
                    return new Controller\EntradaController(
                        $container->get(Model\EntradaTable::class)
                    );
                },
                Controller\SaidaController::class => function($container) {
                    return new Controller\SaidaController(
                        $container->get(Model\SaidaTable::class)
                    );
                },
                Controller\ItemEntradaController::class => function($container) {
                    return new Controller\ItemEntradaController(
                        $container->get(Model\ItemEntradaTable::class)
                    );
                },
                Controller\ItemSaidaController::class => function($container) {
                    return new Controller\ItemSaidaController(
                        $container->get(Model\ItemSaidaTable::class)
                    );
                },
                Controller\EstoqueController::class => function($container) {
                    return new Controller\EstoqueController(
                        $container->get(Model\EstoqueTable::class)
                    );
                },
            ],
        ];
    }

    public static function newTableGatewayEntrada(AdapterInterface $dbAdapter) {
        $resultSetPrototype = new Model\EntradaResultSet($dbAdapter);
        $resultSetPrototype->setArrayObjectPrototype(new Model\Entrada());
        return new TableGateway('entradas', $dbAdapter, null, $resultSetPrototype);
    }

    public static function newTableGatewaySaida(AdapterInterface $dbAdapter) {
        $resultSetPrototype = new Model\SaidaResultSet($dbAdapter);
        $resultSetPrototype->setArrayObjectPrototype(new Model\Saida());
        return new TableGateway('saidas', $dbAdapter, null, $resultSetPrototype);
    }

    public static function newTableGatewayItemEntrada(AdapterInterface $dbAdapter, $carregaFilhos = true) {
        $resultSetPrototype = $carregaFilhos ? new Model\ItemEntradaResultSet($dbAdapter) : new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Model\ItemEntrada());
        return new TableGateway('itensentrada', $dbAdapter, null, $resultSetPrototype);
    }

    public static function newTableGatewayItemSaida(AdapterInterface $dbAdapter, $carregaFilhos = true) {
        $resultSetPrototype = $carregaFilhos ? new Model\ItemSaidaResultSet($dbAdapter) : new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Model\ItemSaida());
        return new TableGateway('itens_saida', $dbAdapter, null, $resultSetPrototype);
    }

    public static function newTableGatewayEstoque(AdapterInterface $dbAdapter, $carregaFilhos = true) {
        $resultSetPrototype = $carregaFilhos ? new Model\EstoqueResultSet($dbAdapter) : new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Model\Estoque());
        return new TableGateway('estoques', $dbAdapter, null, $resultSetPrototype);
    }

}
