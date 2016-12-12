<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * PurchaseOrders Controller
 *
 * @property \App\Model\Table\PurchaseOrdersTable $PurchaseOrders */
class PurchaseOrdersController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['IconstruyeImports', 'BudgetItems']
        ];
        $this->set('purchaseOrders', $this->paginate($this->PurchaseOrders));
        $this->set('_serialize', ['purchaseOrders']);
    }

    /**
     * View method
     *
     * @param string|null $id Purchase Order id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $purchaseOrder = $this->PurchaseOrders->get($id, [
            'contain' => ['IconstruyeImports', 'BudgetItems']
        ]);
        $this->set('purchaseOrder', $purchaseOrder);
        $this->set('_serialize', ['purchaseOrder']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $purchaseOrder = $this->PurchaseOrders->newEntity();
        if ($this->request->is('post')) {
            $purchaseOrder = $this->PurchaseOrders->patchEntity($purchaseOrder, $this->request->data);
            if ($this->PurchaseOrders->save($purchaseOrder)) {
                $this->Flash->success('La orden de compra ha sido guardada.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('La orden de compra no ha sido guardada, intentalo nuevamente.');
            }
        }
        $iconstruyeImports = $this->PurchaseOrders->IconstruyeImports->find('list', ['limit' => 200]);
        $budgetItems = $this->PurchaseOrders->BudgetItems->find('list', ['limit' => 200]);
        $this->set(compact('purchaseOrder', 'iconstruyeImports', 'budgetItems'));
        $this->set('_serialize', ['purchaseOrder']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Purchase Order id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $purchaseOrder = $this->PurchaseOrders->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $purchaseOrder = $this->PurchaseOrders->patchEntity($purchaseOrder, $this->request->data);
            if ($this->PurchaseOrders->save($purchaseOrder)) {
                $this->Flash->success('La orden de compra ha sido actualizada.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('La orden de compra no ha sido actualizada, intentalo nuevamente.');
            }
        }
        $iconstruyeImports = $this->PurchaseOrders->IconstruyeImports->find('list', ['limit' => 200]);
        $budgetItems = $this->PurchaseOrders->BudgetItems->find('list', ['limit' => 200]);
        $this->set(compact('purchaseOrder', 'iconstruyeImports', 'budgetItems'));
        $this->set('_serialize', ['purchaseOrder']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Purchase Order id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $purchaseOrder = $this->PurchaseOrders->get($id);
        if ($this->PurchaseOrders->delete($purchaseOrder)) {
            $this->Flash->success('La orden de compra ha sido eliminada.');
        } else {
            $this->Flash->error('La orden de compra no ha sido eliminada, intentalo nuevamente.');
        }
        return $this->redirect(['action' => 'index']);
    }
}
