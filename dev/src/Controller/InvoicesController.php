<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Invoices Controller
 *
 * @property \App\Model\Table\InvoicesTable $Invoices */
class InvoicesController extends AppController
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
        $this->set('invoices', $this->paginate($this->Invoices));
        $this->set('_serialize', ['invoices']);
    }

    /**
     * View method
     *
     * @param string|null $id Invoice id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $invoice = $this->Invoices->get($id, [
            'contain' => ['IconstruyeImports', 'BudgetItems']
        ]);
        $this->set('invoice', $invoice);
        $this->set('_serialize', ['invoice']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $invoice = $this->Invoices->newEntity();
        if ($this->request->is('post')) {
            $invoice = $this->Invoices->patchEntity($invoice, $this->request->data);
            if ($this->Invoices->save($invoice)) {
                $this->Flash->success('La factura ha sido guardada.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('La factura no ha sido guardada, intentalo nuevamente.');
            }
        }
        $iconstruyeImports = $this->Invoices->IconstruyeImports->find('list', ['limit' => 200]);
        $budgetItems = $this->Invoices->BudgetItems->find('list', ['limit' => 200]);
        $this->set(compact('invoice', 'iconstruyeImports', 'budgetItems'));
        $this->set('_serialize', ['invoice']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Invoice id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $invoice = $this->Invoices->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $invoice = $this->Invoices->patchEntity($invoice, $this->request->data);
            if ($this->Invoices->save($invoice)) {
                $this->Flash->success('La factura ha sido actualizada.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('La factura no ha sido actualizada, intentalo nuevamente.');
            }
        }
        $iconstruyeImports = $this->Invoices->IconstruyeImports->find('list', ['limit' => 200]);
        $budgetItems = $this->Invoices->BudgetItems->find('list', ['limit' => 200]);
        $this->set(compact('invoice', 'iconstruyeImports', 'budgetItems'));
        $this->set('_serialize', ['invoice']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Invoice id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $invoice = $this->Invoices->get($id);
        if ($this->Invoices->delete($invoice)) {
            $this->Flash->success('La factura ha sido eliminada.');
        } else {
            $this->Flash->error('La factura no ha sido eliminada, intentalo nuevamente.');
        }
        return $this->redirect(['action' => 'index']);
    }
}
