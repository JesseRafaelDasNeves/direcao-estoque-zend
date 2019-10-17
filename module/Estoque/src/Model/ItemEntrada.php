<?php

namespace Estoque\Model;

use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;
use Produto\Model\Produto;

/**
 * Description of ItemEntrada
 *
 * @author Jessé Rafael das Neves
 */
class ItemEntrada implements InputFilterAwareInterface {

    /** @var Entrada */
    private $Entrada;

    /** @var Produto */
    private $Produto;

    public $id;
    public $quantidade;
    public $valorunitario;
    public $valortotal;
    public $identrada;
    public $idproduto;
    public $idestoque;

    public function exchangeArray(Array $data) {
        $this->id            = !empty($data['id'])            ? $data['id']            : null;
        $this->quantidade    = !empty($data['quantidade'])    ? $data['quantidade']    : null;
        $this->valorunitario = !empty($data['valorunitario']) ? $data['valorunitario'] : null;
        $this->valortotal    = !empty($data['valortotal'])    ? $data['valortotal']    : null;
        $this->identrada     = !empty($data['identrada'])     ? $data['identrada']     : null;
        $this->idproduto     = !empty($data['idproduto'])     ? $data['idproduto']     : null;
        $this->idestoque     = !empty($data['idestoque'])     ? $data['idestoque']     : null;
    }

    public function getArrayCopy() {
        return [
            'id'            => $this->id,
            'quantidade'    => $this->quantidade,
            'valorunitario' => $this->valorunitario,
            'valortotal'    => $this->valortotal,
            'identrada'     => $this->identrada,
            'idproduto'     => $this->idproduto,
            'idestoque'     => $this->idestoque,
        ];
    }

    public function setEntrada(Entrada $Entrada) {
        $this->Entrada = $Entrada;
    }

    public function entrada(): Entrada {
        if(!isset($this->Entrada)) {
            $this->Entrada = new Entrada();
        }
        return $this->Entrada;
    }

    public function setProduto(Produto $produto) {
        $this->Produto = $produto;
    }

    public function produto(): Produto {
        if(!isset($this->Produto)) {
            $this->Produto = new Produto();
        }
        return $this->Produto;
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
            'name' => 'quantidade',
            'required' => true,
            'filters' => [
                ['name' => \Zend\Filter\ToFloat::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'valorunitario',
            'required' => true,
            'filters' => [
                ['name' => \Zend\Filter\ToFloat::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'valortotal',
            'required' => true,
            'filters' => [
                ['name' => \Zend\Filter\ToFloat::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'identrada',
            'required' => false,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'idproduto',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'idestoque',
            'required' => false,
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
