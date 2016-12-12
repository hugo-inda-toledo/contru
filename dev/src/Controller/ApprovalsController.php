<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Approvals Controller
 *
 * @property \App\Model\Table\ApprovalsTable $Approvals */
class ApprovalsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users']
        ];
        $this->set('approvals', $this->paginate($this->Approvals));
        $this->set('_serialize', ['approvals']);
    }

    /**
     * View method
     *
     * @param string|null $id Approval id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $approval = $this->Approvals->get($id, [
            'contain' => ['Users']
        ]);
        $this->set('approval', $approval);
        $this->set('_serialize', ['approval']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $approval = $this->Approvals->newEntity();
        if ($this->request->is('post')) {
            $approval = $this->Approvals->patchEntity($approval, $this->request->data);
            if ($this->Approvals->save($approval)) {
                $this->Flash->success('La aprobación ha sido guardada.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('La aprobación no ha sido guardada, intentalo de nuevo.');
            }
        }
        $users = $this->Approvals->Users->find('list', ['limit' => 200]);
        $this->set(compact('approval', 'users'));
        $this->set('_serialize', ['approval']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Approval id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $approval = $this->Approvals->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $approval = $this->Approvals->patchEntity($approval, $this->request->data);
            if ($this->Approvals->save($approval)) {
                $this->Flash->success('La aprobación ha sido actualizada.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('La aprobación no ha sido actualizada, intentalo de nuevo.');
            }
        }
        $users = $this->Approvals->Users->find('list', ['limit' => 200]);
        $this->set(compact('approval', 'users'));
        $this->set('_serialize', ['approval']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Approval id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $approval = $this->Approvals->get($id);
        if ($this->Approvals->delete($approval)) {
            $this->Flash->success('La aprobación ha sido eliminada.');
        } else {
            $this->Flash->error('La aprobación no ha sido eliminada, intentalo de nuevo.');
        }
        return $this->redirect(['action' => 'index']);
    }
}
