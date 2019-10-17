<?php

namespace Estoque\Model;

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
 * Description of Saida
 *
 * @author Jessé Rafael das Neves
 */
class Saida {

    const SITUACAO_EM_ELABORACAO  = 1,
          SITUACAO_CONCLUIDA      = 2;

    /** @var Pessoa */
    private $Pessoa;

    public $id;
    public $data;
    public $hora;
    public $situacao;
    public $observacao;
    public $idpessoa;

    private $valorTotal;

    public function exchangeArray(Array $data) {
        $this->id         = !empty($data['id'])         ? $data['id']         : null;
        $this->data       = !empty($data['data'])       ? $data['data']       : null;
        $this->hora       = !empty($data['hora'])       ? $data['hora']       : null;
        $this->situacao   = !empty($data['situacao'])   ? $data['situacao']   : null;
        $this->observacao = !empty($data['observacao']) ? $data['observacao'] : null;
        $this->idpessoa   = !empty($data['idpessoa'])   ? $data['idpessoa']   : null;
    }

    public function getArrayCopy() {
        return [
            'id'         => $this->id,
            'data'       => $this->data,
            'hora'       => $this->hora,
            'situacao'   => $this->situacao,
            'observacao' => $this->observacao,
            'idpessoa'   => $this->idpessoa,
        ];
    }

    public function setPessoa(Pessoa $pessoa) {
        $this->Pessoa = $pessoa;
    }

    public function pessoa(): Pessoa {
        if(!isset($this->Pessoa)) {
            $this->Pessoa = new Pessoa();
        }
        return $this->Pessoa;
    }

    public function setValorTotal($valorTotal) {
        $this->valorTotal = $valorTotal;
    }

    public function getValorTotal() {
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
            'name' => 'observacao',
            'required' => false,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
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

    public function isPermitidoManutencao() {
        return $this->situacao == Entrada::SITUACAO_EM_ELABORACAO;
    }

}
