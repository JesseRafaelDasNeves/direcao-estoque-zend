<?php

namespace Produto\Model;

use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;

/**
 * Description of Produto
 *
 * @author Jessé Rafael das Neves
 */
class Produto implements InputFilterAwareInterface {

    public $id;
    public $nome;
    public $unidade;
    public $categoria;
    public $descricao;

    const UNIDADE           = 1,
          UNIDADE_LITRO     = 2,
          UNIDADE_KILOGRAMA = 3,
          UNIDADE_SACO      = 4,
          UNIDADE_CAIXA     = 5;

    const CATEGORIA_LIMPEZA         = 'limpeza',
          CATEGORIA_CONSTRUCAO      = 'construcao',
          CATEGORIA_ESCRITORIO      = 'escritorio',
          CATEGORIA_REMEDIO         = 'remedio',
          CATEGORIA_MOVEIS          = 'moveis',
          CATEGORIA_ELETRODOMESTICO = 'eletrodomestico',
          CATEGORIA_ESCOLAR         = 'escolar';

    public function exchangeArray(Array $data) {
        $this->id        = !empty($data['id'])        ? $data['id']        : null;
        $this->nome      = !empty($data['nome'])      ? $data['nome']      : null;
        $this->unidade   = !empty($data['unidade'])   ? $data['unidade']   : null;
        $this->categoria = !empty($data['categoria']) ? $data['categoria'] : null;
        $this->descricao = !empty($data['descricao']) ? $data['descricao'] : null;
    }

    public function getArrayCopy() {
        return [
            'id'        => $this->id,
            'nome'      => $this->nome,
            'unidade'   => $this->unidade,
            'categoria' => $this->categoria,
            'descricao' => $this->descricao,
        ];
    }

    public static function getListaUnidade() {
        return Array(
            self::UNIDADE           => 'UN',
            self::UNIDADE_LITRO     => 'LT',
            self::UNIDADE_KILOGRAMA => 'KL',
            self::UNIDADE_SACO      => 'SC',
            self::UNIDADE_CAIXA     => 'CX',
        );
    }

    public static function getListaCategoria() {
        return Array(
            self::CATEGORIA_LIMPEZA         => 'Limpeza',
            self::CATEGORIA_CONSTRUCAO      => 'Construção',
            self::CATEGORIA_ESCRITORIO      => 'Escritório',
            self::CATEGORIA_REMEDIO         => 'Remédio',
            self::CATEGORIA_MOVEIS          => 'Móveis',
            self::CATEGORIA_ELETRODOMESTICO => 'Elétrodomestico',
            self::CATEGORIA_ESCOLAR         => 'Escolar',
        );
    }

    public function getDestricaoUnidade() {
        $aLista = self::getListaUnidade();
        return !empty($aLista[$this->unidade]) ? $aLista[$this->unidade] : null;
    }

    public function getDestricaoCategoria() {
        $aLista = self::getListaCategoria();
        return !empty($aLista[$this->categoria]) ? $aLista[$this->categoria] : null;
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
            'name' => 'unidade',
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
                        'max' => 150,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'categoria',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
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
