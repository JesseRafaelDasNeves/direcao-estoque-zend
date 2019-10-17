<?php

namespace Produto;

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
                Model\ProdutoTable::class => function($container) {
                    $tableGateway = $container->get(Model\ProdutoTableGateway::class);
                    return new Model\ProdutoTable($tableGateway);
                },
                Model\ProdutoTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    return Module::newTableGatewayProduto($dbAdapter);
                },
            ],
        ];
    }

    public function getControllerConfig() {
        return [
            'factories' => [
                Controller\ProdutoController::class => function($container) {
                    return new Controller\ProdutoController(
                        $container->get(Model\ProdutoTable::class)
                    );
                },
            ],
        ];
    }

    public static function newTableGatewayProduto(AdapterInterface $dbAdapter) {
        $resultSetPrototype = new Model\ProdutoResultSet($dbAdapter);
        $resultSetPrototype->setArrayObjectPrototype(new Model\Produto());
        return new TableGateway('produtos', $dbAdapter, null, $resultSetPrototype);
    }

}
