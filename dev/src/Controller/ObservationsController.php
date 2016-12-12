<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Observations Controller
 *
 * @property \App\Model\Table\ObservationsTable $Observations */
class ObservationsController extends AppController
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
        $this->set('observations', $this->paginate($this->Observations));
        $this->set('_serialize', ['observations']);
    }

    /**
     * View method
     *
     * @param string|null $id Observation id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $observation = $this->Observations->get($id, [
            'contain' => ['Users']
        ]);
        $this->set('observation', $observation);
        $this->set('_serialize', ['observation']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $observation = $this->Observations->newEntity();
        if ($this->request->is('post')) {
            $observation = $this->Observations->patchEntity($observation, $this->request->data);
            if ($this->Observations->save($observation)) {
                $this->Flash->success('El comentario ha sido guardado.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('El comentario no ha sido guardado, intentalo nuevamente.');
            }
        }
        //$models = $this->Observations->Models->find('list', ['limit' => 200]);
        $users = $this->Observations->Users->find('list', ['limit' => 200]);
        $this->set(compact('observation', 'users'));
        $this->set('_serialize', ['observation']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Observation id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $observation = $this->Observations->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $observation = $this->Observations->patchEntity($observation, $this->request->data);
            if ($this->Observations->save($observation)) {
                $this->Flash->success('El comentario ha sido actualizado.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('El comentario no ha sido actualizado, intentalo nuevamente.');
            }
        }
        //$models = $this->Observations->Models->find('list', ['limit' => 200]);
        $users = $this->Observations->Users->find('list', ['limit' => 200]);
        $this->set(compact('observation', 'users'));
        $this->set('_serialize', ['observation']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Observation id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $observation = $this->Observations->get($id);
        if ($this->Observations->delete($observation)) {
            $this->Flash->success('El comentario ha sido eliminado.');
        } else {
            $this->Flash->error('El comentario no ha sido eliminado, intentalo nuevamente.');
        }
        return $this->redirect(['action' => 'index']);
    }
}
