<?php

namespace Fornecedor;

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
                Model\FornecedorTable::class => function($container) {
                    $tableGateway = $container->get(Model\FornecedorTableGateway::class);
                    return new Model\FornecedorTable($tableGateway);
                },
                Model\FornecedorTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new Model\FornecedorResultSet($dbAdapter);
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Fornecedor());
                    return new TableGateway('fornecedores', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }

    public function getControllerConfig() {
        return [
            'factories' => [
                Controller\FornecedorController::class => function($container) {
                    return new Controller\FornecedorController(
                        $container->get(Model\FornecedorTable::class)
                    );
                },
            ],
        ];
    }

}
