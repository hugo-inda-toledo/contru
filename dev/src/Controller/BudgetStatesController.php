<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * BudgetStates Controller
 *
 * @property \App\Model\Table\BudgetStatesTable $BudgetStates */
class BudgetStatesController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('budgetStates', $this->paginate($this->BudgetStates));
        $this->set('_serialize', ['budgetStates']);
    }

    /**
     * View method
     *
     * @param string|null $id Budget State id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $budgetState = $this->BudgetStates->get($id, [
            'contain' => ['BudgetApprovals']
        ]);
        $this->set('budgetState', $budgetState);
        $this->set('_serialize', ['budgetState']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $budgetState = $this->BudgetStates->newEntity();
        if ($this->request->is('post')) {
            $budgetState = $this->BudgetStates->patchEntity($budgetState, $this->request->data);
            if ($this->BudgetStates->save($budgetState)) {
                $this->Flash->success('El estado del presupuesto fue guardado.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('El estado del presupuesto no fue guardado, intentalo nuevamente.');
            }
        }
        $this->set(compact('budgetState'));
        $this->set('_serialize', ['budgetState']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Budget State id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $budgetState = $this->BudgetStates->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $budgetState = $this->BudgetStates->patchEntity($budgetState, $this->request->data);
            if ($this->BudgetStates->save($budgetState)) {
                $this->Flash->success('El estado del presupuesto fue actualizado.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('El estado del presupuesto no fue actualizado, intentalo nuevamente.');
            }
        }
        $this->set(compact('budgetState'));
        $this->set('_serialize', ['budgetState']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Budget State id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $budgetState = $this->BudgetStates->get($id);
        if ($this->BudgetStates->delete($budgetState)) {
            $this->Flash->success('El estado del presupuesto fue eliminado.');
        } else {
            $this->Flash->error('El estado del presupuesto no fue eliminado, intentalo nuevamente.');
        }
        return $this->redirect(['action' => 'index']);
    }
}
