<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Renditions Controller
 *
 * @property \App\Model\Table\RenditionsTable $Renditions */
class RenditionsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Budgets', 'Users']
        ];
        $this->set('renditions', $this->paginate($this->Renditions));
        $this->set('_serialize', ['renditions']);
    }

    /**
     * View method
     *
     * @param string|null $id Rendition id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $rendition = $this->Renditions->get($id, [
            'contain' => ['Budgets', 'Users', 'RenditionItems']
        ]);
        $this->set('rendition', $rendition);
        $this->set('_serialize', ['rendition']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $rendition = $this->Renditions->newEntity();
        if ($this->request->is('post')) {
            $rendition = $this->Renditions->patchEntity($rendition, $this->request->data);
            if ($this->Renditions->save($rendition)) {
                $this->Flash->success('La rendición ha sido guardada.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('La rendición no ha sido guardada, intentalo nuevamente.');
            }
        }
        $budgets = $this->Renditions->Budgets->find('list', ['limit' => 200]);
        $users = $this->Renditions->Users->find('list', ['limit' => 200]);
        $this->set(compact('rendition', 'budgets', 'users'));
        $this->set('_serialize', ['rendition']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Rendition id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $rendition = $this->Renditions->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $rendition = $this->Renditions->patchEntity($rendition, $this->request->data);
            if ($this->Renditions->save($rendition)) {
                $this->Flash->success('La rendición ha sido actualizada.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('La rendición no ha sido actualizada, intentalo nuevamente.');
            }
        }
        $budgets = $this->Renditions->Budgets->find('list', ['limit' => 200]);
        $users = $this->Renditions->Users->find('list', ['limit' => 200]);
        $this->set(compact('rendition', 'budgets', 'users'));
        $this->set('_serialize', ['rendition']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Rendition id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $rendition = $this->Renditions->get($id);
        if ($this->Renditions->delete($rendition)) {
            $this->Flash->success('La rendición ha sido eliminada.');
        } else {
            $this->Flash->error('La rendición no ha sido eliminada, intentalo nuevamente.');
        }
        return $this->redirect(['action' => 'index']);
    }
}
