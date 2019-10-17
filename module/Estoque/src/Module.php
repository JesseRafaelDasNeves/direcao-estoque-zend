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
            ],
        ];
    }

    public static function newTableGatewayEntrada(AdapterInterface $dbAdapter) {
        $resultSetPrototype = new Model\EntradaResultSet($dbAdapter);
        $resultSetPrototype->setArrayObjectPrototype(new Model\Entrada());
        return new TableGateway('entradas', $dbAdapter, null, $resultSetPrototype);
    }

}
