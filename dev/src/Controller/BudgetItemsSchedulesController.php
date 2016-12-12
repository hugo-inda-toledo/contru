<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * BudgetItemsSchedules Controller
 *
 * @property \App\Model\Table\BudgetItemsSchedulesTable $BudgetItemsSchedules */
class BudgetItemsSchedulesController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('budgetItemsSchedules', $this->paginate($this->BudgetItemsSchedules));
        $this->set('_serialize', ['budgetItemsSchedules']);
    }

    /**
     * View method
     *
     * @param string|null $id Budget Items Schedule id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $budgetItemsSchedule = $this->BudgetItemsSchedules->get($id, [
            'contain' => []
        ]);
        $this->set('budgetItemsSchedule', $budgetItemsSchedule);
        $this->set('_serialize', ['budgetItemsSchedule']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $budgetItemsSchedule = $this->BudgetItemsSchedules->newEntity();
        if ($this->request->is('post')) {
            $budgetItemsSchedule = $this->BudgetItemsSchedules->patchEntity($budgetItemsSchedule, $this->request->data);
            if ($this->BudgetItemsSchedules->save($budgetItemsSchedule)) {
                $this->Flash->success(__('El item de la planificación fue guardado.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El item de la planificación no fue guardado, intentalo nuevamente.'));
            }
        }
        $this->set(compact('budgetItemsSchedule'));
        $this->set('_serialize', ['budgetItemsSchedule']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Budget Items Schedule id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $budgetItemsSchedule = $this->BudgetItemsSchedules->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $budgetItemsSchedule = $this->BudgetItemsSchedules->patchEntity($budgetItemsSchedule, $this->request->data);
            if ($this->BudgetItemsSchedules->save($budgetItemsSchedule)) {
                $this->Flash->success(__('El item de la planificación fue actualizado.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('El item de la planificación no fue actualizado, intentalo nuevamente.'));
            }
        }
        $this->set(compact('budgetItemsSchedule'));
        $this->set('_serialize', ['budgetItemsSchedule']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Budget Items Schedule id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $budgetItemsSchedule = $this->BudgetItemsSchedules->get($id);
        if ($this->BudgetItemsSchedules->delete($budgetItemsSchedule)) {
            $this->Flash->success(__('El item de la planificación fue eliminado.'));
        } else {
            $this->Flash->error(__('El item de la planificación no fue eliminado, intentalo nuevamente.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
