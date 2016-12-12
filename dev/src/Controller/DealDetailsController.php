<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * DealDetails Controller
 *
 * @property \App\Model\Table\DealDetailsTable $DealDetails */
class DealDetailsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Deals', 'BudgetItems', 'Users']
        ];
        $this->set('dealDetails', $this->paginate($this->DealDetails));
        $this->set('_serialize', ['dealDetails']);
    }

    /**
     * View method
     *
     * @param string|null $id Deal Detail id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $dealDetail = $this->DealDetails->get($id, [
            'contain' => ['Deals', 'BudgetItems', 'Users']
        ]);
        $this->set('dealDetail', $dealDetail);
        $this->set('_serialize', ['dealDetail']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $dealDetail = $this->DealDetails->newEntity();
        if ($this->request->is('post')) {
            $dealDetail = $this->DealDetails->patchEntity($dealDetail, $this->request->data);
            if ($this->DealDetails->save($dealDetail)) {
                $this->Flash->success(__('El detalle del trato ha sido guardado.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El detalle del trato no ha sido guardado, intentalo nuevamente.'));
            }
        }
        $deals = $this->DealDetails->Deals->find('list', ['limit' => 200]);
        $budgetItems = $this->DealDetails->BudgetItems->find('list', ['limit' => 200]);
        $users = $this->DealDetails->Users->find('list', ['limit' => 200]);
        $this->set(compact('dealDetail', 'deals', 'budgetItems', 'userCreateds'));
        $this->set('_serialize', ['dealDetail']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Deal Detail id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $dealDetail = $this->DealDetails->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $dealDetail = $this->DealDetails->patchEntity($dealDetail, $this->request->data);
            if ($this->DealDetails->save($dealDetail)) {
                $this->Flash->success(__('El detalle del trato ha sido actualizado.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El detalle del trato no ha sido actualizado, intentalo nuevamente.'));
            }
        }
        $deals = $this->DealDetails->Deals->find('list', ['limit' => 200]);
        $budgetItems = $this->DealDetails->BudgetItems->find('list', ['limit' => 200]);
        $users = $this->DealDetails->Users->find('list', ['limit' => 200]);
        $this->set(compact('dealDetail', 'deals', 'budgetItems', 'users'));
        $this->set('_serialize', ['dealDetail']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Deal Detail id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $dealDetail = $this->DealDetails->get($id);
        if ($this->DealDetails->delete($dealDetail)) {
            $this->Flash->success(__('El detalle del trato ha sido eliminado.'));
        } else {
            $this->Flash->error(__('El detalle del trato no ha sido eliminado, intentalo nuevamente.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
