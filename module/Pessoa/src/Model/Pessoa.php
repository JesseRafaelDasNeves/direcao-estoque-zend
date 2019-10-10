<?php

namespace Pessoa\Model;

use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;

/**
 * @author Jessé Rafael das Neves
 */
class Pessoa implements InputFilterAwareInterface {

    const TIPO_FISICA   = 1,
          TIPO_JURIDICA = 2;

    public $id;
    public $nome;
    public $cpfcnpj;
    public $tipo;

    private $inputFilter;

    public function exchangeArray(Array $data) {
        $this->id      = !empty($data['id'])      ? $data['id']      : null;
        $this->tipo    = !empty($data['tipo'])    ? $data['tipo']    : null;
        $this->nome    = !empty($data['nome'])    ? $data['nome']    : null;
        $this->cpfcnpj = !empty($data['cpfcnpj']) ? $data['cpfcnpj'] : null;
    }

    public function getArrayCopy()
    {
        return [
            'id'      => $this->id,
            'tipo'    => $this->tipo,
            'nome'    => $this->nome,
            'cpfcnpj' => $this->cpfcnpj,
        ];
    }

    public static function getListaTipo(int $iTipo = null) {
        return Array(
            self::TIPO_FISICA   => 'Física',
            self::TIPO_JURIDICA => 'Jurídica'
        );
    }

    public function getInputFilter(): InputFilterInterface {
        if (isset($this->inputFilter)) {
            return $this->inputFilter;
        }

        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'id',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'nome',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 3,
                        'max' => 200,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'cpfcnpj',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'max' => 15,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'tipo',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter): InputFilterAwareInterface {
        throw new DomainException(sprintf(
            '%s does not allow injection of an alternate input filter',
            __CLASS__
        ));
    }

}
