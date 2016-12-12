<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Subcontracts Controller
 *
 * @property \App\Model\Table\SubcontractsTable $Subcontracts
 */
class SubcontractsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Buildings']
        ];
        $this->set('subcontracts', $this->paginate($this->Subcontracts));
        $this->set('_serialize', ['subcontracts']);
    }

    /**
     * View method
     *
     * @param string|null $id Subcontract id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $subcontract = $this->Subcontracts->get($id, [
            'contain' => ['Buildings']
        ]);
        $this->set('subcontract', $subcontract);
        $this->set('_serialize', ['subcontract']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $subcontract = $this->Subcontracts->newEntity();
        if ($this->request->is('post')) {
            $subcontract = $this->Subcontracts->patchEntity($subcontract, $this->request->data);
            if ($this->Subcontracts->save($subcontract)) {
                $this->Flash->success(__('El subcontrato ha sido guardado.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El subcontrato no ha sido guardado, intentalo nuevamente.'));
            }
        }
        $buildings = $this->Subcontracts->Buildings->find('list', ['limit' => 200]);
        $this->set(compact('subcontract', 'buildings'));
        $this->set('_serialize', ['subcontract']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Subcontract id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $subcontract = $this->Subcontracts->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $subcontract = $this->Subcontracts->patchEntity($subcontract, $this->request->data);
            if ($this->Subcontracts->save($subcontract)) {
                $this->Flash->success(__('El subcontrato ha sido actualizado.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El subcontrato no ha sido guardado, intentalo nuevamente.'));
            }
        }
        $buildings = $this->Subcontracts->Buildings->find('list', ['limit' => 200]);
        $this->set(compact('subcontract', 'buildings'));
        $this->set('_serialize', ['subcontract']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Subcontract id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $subcontract = $this->Subcontracts->get($id);
        if ($this->Subcontracts->delete($subcontract)) {
            $this->Flash->success(__('El subcontrato ha sido eliminado.'));
        } else {
            $this->Flash->error(__('El subcontrato no ha sido eliminado, intentalo nuevamente.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
