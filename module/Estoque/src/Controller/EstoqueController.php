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
        /*if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }*/

        $idProduro = $this->params()->fromRoute('idproduto', 0);
        $estoque  = $this->table->firstEstoqueByProduto($idProduro);

        echo $estoque ? $estoque->quantidade : 0;
        die();
    }

    public function movimentacoesAction() {
        if(!Auth::check()) {
            return $this->redirect()->toRoute('auth');
        }
        $estoques      = [];
        $movimentacoes = [];

        foreach ($this->table->fetchAll() as $estoque) {
            $estoques[]                  = $estoque;
            $movimentacoes[$estoque->id] = $this->getMovimentacoesByEstoque($estoque);
        }

        return new ViewModel(['estoques' => $estoques, 'movimentacoes' => $movimentacoes]);
    }

    private function getMovimentacoesByEstoque(Estoque $oEstoque) {
        $estoqueTable  = new \Estoque\Model\EstoqueTable(\Estoque\Module::newTableGatewayEstoque($this->table->getAdapter()));
        $movimentacoes = [];

        foreach ($estoqueTable->getMovimentacoesByIdEstoque($oEstoque->id) as $movimentacao) {
            $movimentacoes[] = $movimentacao;
        }

        usort($movimentacoes, function ($a, $b) {
            $dataA = (int) str_replace('-', '', $a['data']);
            $dataB = (int) str_replace('-', '', $b['data']);

            if ($dataA == $dataB) {
                return 0;
            }
            return ($dataA < $dataB) ? -1 : 1;
        });

        return $movimentacoes;
    }

}
