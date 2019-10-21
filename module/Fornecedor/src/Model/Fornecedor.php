<?php

namespace Fornecedor\Model;

use Pessoa\Model\Pessoa;
use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;

/**
 * Description of Fornecedor
 *
 * @author Jessé Rafael das Neves
 */
class Fornecedor implements InputFilterAwareInterface {

    const TIPO_GRANDE_PORTE  = 1,
          TIPO_PEQUENO_PORTE = 2,
          TIPO_MEDIO_PORTE   = 3,
          TIPO_MICRO         = 4;

    /** @var Pessoa */
    private $Pessoa;

    public $id;
    public $tipo;
    public $inscricaoestadual;
    public $idpessoa;

    private $inputFilter;

    public function setPessoa(Pessoa $Pessoa) {
        $this->Pessoa = $Pessoa;
    }

    public function pessoa(): Pessoa {
        if(!isset($this->Pessoa)) {
            $this->Pessoa = new Pessoa();
        }
        return $this->Pessoa;
    }

    public function nomeTipo() {
        $aLista = self::getListaTipo();
        return !empty($aLista[$this->tipo]) ? $aLista[$this->tipo] : null;
    }

    public function exchangeArray(Array $data) {
        $this->id                = !empty($data['id'])                ? $data['id']                : null;
        $this->tipo              = !empty($data['tipo'])              ? $data['tipo']              : null;
        $this->inscricaoestadual = !empty($data['inscricaoestadual']) ? $data['inscricaoestadual'] : null;
        $this->idpessoa          = !empty($data['idpessoa'])          ? $data['idpessoa']          : null;
    }

    public function getArrayCopy() {
        return [
            'id'                => $this->id,
            'tipo'              => $this->tipo,
            'inscricaoestadual' => $this->inscricaoestadual,
            'idpessoa'          => $this->idpessoa,
        ];
    }

    public static function getListaTipo() {
        return Array(
            self::TIPO_GRANDE_PORTE   => 'Grande Porte',
            self::TIPO_PEQUENO_PORTE  => 'Pequeno Porte',
            self::TIPO_MEDIO_PORTE    => 'Médio Porte',
            self::TIPO_MICRO          => 'Micro Empresa',
        );
    }

    public function getInputFilter(): InputFilterInterface {
        if (isset($this->inputFilter)) {
            return $this->inputFilter;
        }

        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'id',
            'required' => false,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'tipo',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'inscricaoestadual',
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
            'name' => 'idpessoa',
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
