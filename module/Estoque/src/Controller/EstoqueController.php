<?php

namespace Estoque\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Auth\Model\Auth;
use Zend\View\Model\ViewModel;
use Estoque\Model\EstoqueTable;
use Estoque\Model\Estoque;

/**
 * Description of EstoqueController
 *
 * @author Jessé Rafael das Neves
 */
class EstoqueController extends AbstractActionController {

    private $table;

    public function __construct(EstoqueTable $table) {
        $this->table = $table;
    }

    public function qtdeestoqueprodutoAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        $idProduro = $this->params()->fromRoute('idproduto', 0);
        $estoque  = $this->table->firstEstoqueByProduto($idProduro);

        echo $estoque ? $estoque->quantidade : 0;
        die();
    }

    public function movimentacoesAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }

        $estoques = [];

        foreach ($this->table->fetchAll() as $estoque) {
            $estoques[$estoque->id]['estoque']  = $estoque;

            foreach ($this->getMovimentacoesByEstoque($estoque) as $movimentacao) {
                $estoques[$estoque->id]['movimentacoes'][] = $movimentacao;
            }
        }

        $fnOrdena = function ($a, $b) {
            $dataA = (int) str_replace('-', '', $a['data']);
            $dataB = (int) str_replace('-', '', $b['data']);

            if ($dataA == $dataB) {
                return 0;
            }
            return ($dataA < $dataB) ? -1 : 1;
        };

        foreach ($estoques as $idEstoque => $dadosEstoque) {
            $movimentacoes = $estoques[$idEstoque]['movimentacoes'];
            usort($movimentacoes, $fnOrdena);
            $estoques[$idEstoque]['movimentacoes'] = $movimentacoes;
        }

        return new ViewModel(['estoques' => $estoques]);
    }

    private function getMovimentacoesByEstoque(Estoque $oEstoque) {
        $estoqueTable = new \Estoque\Model\EstoqueTable(\Estoque\Module::newTableGatewayEstoque($this->table->getAdapter()));
        return $estoqueTable->getMovimentacoesByIdEstoque($oEstoque->id);
    }

}
