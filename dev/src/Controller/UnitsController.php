<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Units Controller
 *
 * @property \App\Model\Table\UnitsTable $Units */
class UnitsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('units', $this->paginate($this->Units));
        $this->set('_serialize', ['units']);
    }

    /**
     * View method
     *
     * @param string|null $id Unit id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $unit = $this->Units->get($id, [
            'contain' => ['BudgetItems']
        ]);
        $this->set('unit', $unit);
        $this->set('_serialize', ['unit']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $unit = $this->Units->newEntity();
        if ($this->request->is('post')) {
            $unit = $this->Units->patchEntity($unit, $this->request->data);
            if ($this->Units->save($unit)) {
                $this->Flash->success('La unidad ha sido guardada.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('La unidad no ha sido guardada, intentalo nuevamente.');
            }
        }
        $this->set(compact('unit'));
        $this->set('_serialize', ['unit']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Unit id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $unit = $this->Units->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $unit = $this->Units->patchEntity($unit, $this->request->data);
            if ($this->Units->save($unit)) {
                $this->Flash->success('La unidad ha sido actualizada.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('La unidad no ha sido actualizada, intentalo nuevamente.');
            }
        }
        $this->set(compact('unit'));
        $this->set('_serialize', ['unit']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Unit id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $unit = $this->Units->get($id);
        if ($this->Units->delete($unit)) {
            $this->Flash->success('La unidad ha sido eliminada.');
        } else {
            $this->Flash->error('La unidad no ha sido eliminada, intentalo nuevamente.');
        }
        return $this->redirect(['action' => 'index']);
    }
}
