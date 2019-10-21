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
use Fornecedor\Model\Fornecedor;

/**
 * Description of Entrada
 *
 * @author Jessé Rafael das Neves
 */
class Entrada implements InputFilterAwareInterface {

    const SITUACAO_EM_ELABORACAO  = 1,
          SITUACAO_CONCLUIDA      = 2;

    /** @var Fornecedor */
    private $Fornecedor;

    public $id;
    public $data;
    public $hora;
    public $situacao;
    public $numero_nota;
    public $observacao;
    public $idfornecedor;

    private $valorTotal;
    private $fornecedorPersistido = false;
    private $vlrTotalPersistido   = false;

    public function exchangeArray(Array $data) {
        $this->id           = !empty($data['id'])           ? $data['id']           : null;
        $this->data         = !empty($data['data'])         ? $data['data']         : null;
        $this->hora         = !empty($data['hora'])         ? $data['hora']         : null;
        $this->situacao     = !empty($data['situacao'])     ? $data['situacao']     : null;
        $this->numero_nota  = !empty($data['numero_nota'])  ? $data['numero_nota']  : null;
        $this->observacao   = !empty($data['observacao'])   ? $data['observacao']   : null;
        $this->idfornecedor = !empty($data['idfornecedor']) ? $data['idfornecedor'] : null;
    }

    public function getArrayCopy() {
        return [
            'id'           => $this->id,
            'data'         => $this->data,
            'hora'         => $this->hora,
            'situacao'     => $this->situacao,
            'numero_nota'  => $this->numero_nota,
            'observacao'   => $this->observacao,
            'idfornecedor' => $this->idfornecedor,
        ];
    }

    public function setFornecedor(Fornecedor $Fornecedor) {
        $this->Fornecedor = $Fornecedor;
    }

    public function fornecedor(): Fornecedor {
        if(!$this->fornecedorPersistido && !is_null($this->idfornecedor)) {

            $oFornecedorTable = new \Fornecedor\Model\FornecedorTable(\Fornecedor\Module::newTableGatewayFornecedor(\Estoque\Module::getDbAdapter()));
            $this->Fornecedor = $oFornecedorTable->getFornecedor($this->idfornecedor);
            $this->fornecedorPersistido = true;
        }

        if(!isset($this->Fornecedor)) {
            $this->Fornecedor = new Fornecedor();
        }

        return $this->Fornecedor;
    }

    public function setValorTotal($valorTotal) {
        $this->valorTotal = $valorTotal;
    }

    public function getValorTotal() {
        if(!$this->vlrTotalPersistido && !is_null($this->id)) {
            $itemEntradaTable = new \Estoque\Model\ItemEntradaTable(\Estoque\Module::newTableGatewayItemEntrada(\Estoque\Module::getDbAdapter(), false));
            $this->valorTotal = $itemEntradaTable->somaValorTotalByEntrada($this->id);
            $this->vlrTotalPersistido = true;
        }

        return $this->valorTotal;
    }

    public static function getListaSituacao() {
        return Array(
            self::SITUACAO_EM_ELABORACAO => 'Em Elaboração',
            self::SITUACAO_CONCLUIDA     => 'Concluída'
        );
    }

    public function getDestricaoSituacao() {
        $aLista = self::getListaSituacao();
        return !empty($aLista[$this->situacao]) ? $aLista[$this->situacao] : null;
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
            'name' => 'data',
            'required' => true,
        ]);

        $inputFilter->add([
            'name' => 'hora',
            'required' => true,
        ]);

        $inputFilter->add([
            'name' => 'situacao',
            'required' => false,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'numero_nota',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'observacao',
            'required' => false,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'idfornecedor',
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

    public function isPermitidoManutencao() {
        return $this->situacao == Entrada::SITUACAO_EM_ELABORACAO;
    }

    public function dataFormatada() {
        return date_format(date_create($this->data), 'd/m/Y');
    }

}
