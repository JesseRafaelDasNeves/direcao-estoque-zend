<?php

namespace Pessoa;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;

/**
 * Configurações do modulo
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
                Model\PessoaTable::class => function($container) {
                    $tableGateway = $container->get(Model\PessoaTableGateway::class);
                    return new Model\PessoaTable($tableGateway);
                },
                Model\PessoaTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    return Module::newTableGatewayPessoa($dbAdapter);
                },
            ],
        ];
    }

    public function getControllerConfig() {
        return [
            'factories' => [
                Controller\PessoaController::class => function($container) {
                    return new Controller\PessoaController(
                        $container->get(Model\PessoaTable::class)
                    );
                },
            ],
        ];
    }

    public static function newTableGatewayPessoa(AdapterInterface $oDbAdapter) {
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Model\Pessoa());
        return new TableGateway('pessoas', $oDbAdapter, null, $resultSetPrototype);
    }

}
