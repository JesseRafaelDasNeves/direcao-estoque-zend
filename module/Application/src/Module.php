<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

class Module {
    const VERSION = '3.0.3-dev';

    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap_(\Zend\Mvc\MvcEvent $e) {
        //Cria o translator
        $translator = new \Zend\Mvc\I18n\Translator(new \Zend\I18n\Translator\Translator());

        //Adiciona o arquivo de tradução
        $translator->addTranslationFile(
            'phpArray',
            //__DIR__ . '/../../vendor/zendframework/zendframework/resources/languages/pt_BR/Zend_Validate.php',
            __DIR__ . '/../../vendor/zendframework/zend-i18n-resources/languages/de/Zend_Validate.php',
            'default',
            'pt_BR'
        );

        //Define o tradutor padrão dos validadores
        \Zend\Validator\AbstractValidator::setDefaultTranslator($translator);
    }

}
