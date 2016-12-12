<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * RenditionItems Controller
 *
 * @property \App\Model\Table\RenditionItemsTable $RenditionItems */
class RenditionItemsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Renditions', 'BudgetItems']
        ];
        $this->set('renditionItems', $this->paginate($this->RenditionItems));
        $this->set('_serialize', ['renditionItems']);
    }

    /**
     * View method
     *
     * @param string|null $id Rendition Item id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $renditionItem = $this->RenditionItems->get($id, [
            'contain' => ['Renditions', 'BudgetItems']
        ]);
        $this->set('renditionItem', $renditionItem);
        $this->set('_serialize', ['renditionItem']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $renditionItem = $this->RenditionItems->newEntity();
        if ($this->request->is('post')) {
            $renditionItem = $this->RenditionItems->patchEntity($renditionItem, $this->request->data);
            if ($this->RenditionItems->save($renditionItem)) {
                $this->Flash->success('El item de la rendición ha sido guardado.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('El item de la rendición no ha sido guardado, intentalo nuevamente.');
            }
        }
        $renditions = $this->RenditionItems->Renditions->find('list', ['limit' => 200]);
        $budgetItems = $this->RenditionItems->BudgetItems->find('list', ['limit' => 200]);
        $this->set(compact('renditionItem', 'renditions', 'budgetItems'));
        $this->set('_serialize', ['renditionItem']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Rendition Item id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $renditionItem = $this->RenditionItems->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $renditionItem = $this->RenditionItems->patchEntity($renditionItem, $this->request->data);
            if ($this->RenditionItems->save($renditionItem)) {
                $this->Flash->success('El item de la rendición ha sido actualizado.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('El item de la rendición no ha sido actualizado.');
            }
        }
        $renditions = $this->RenditionItems->Renditions->find('list', ['limit' => 200]);
        $budgetItems = $this->RenditionItems->BudgetItems->find('list', ['limit' => 200]);
        $this->set(compact('renditionItem', 'renditions', 'budgetItems'));
        $this->set('_serialize', ['renditionItem']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Rendition Item id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $renditionItem = $this->RenditionItems->get($id);
        if ($this->RenditionItems->delete($renditionItem)) {
            $this->Flash->success('El item de la rendición ha sido eliminado.');
        } else {
            $this->Flash->error('El item de la rendición no ha sido eliminado, intentalo nuevamente.');
        }
        return $this->redirect(['action' => 'index']);
    }
}
