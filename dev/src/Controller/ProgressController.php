<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Progress Controller
 *
 * @property \App\Model\Table\ProgressTable $Progress */
class ProgressController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['BudgetItems', 'Schedules', 'PaymentStatements', 'Users']
        ];
        $this->set('progress', $this->paginate($this->Progress));
        $this->set('_serialize', ['progress']);
    }

    /**
     * View method
     *
     * @param string|null $id Progres id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $progres = $this->Progress->get($id, [
            'contain' => ['BudgetItems', 'Schedules', 'PaymentStatements', 'Users']
        ]);
        $this->set('progres', $progres);
        $this->set('_serialize', ['progres']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $progres = $this->Progress->newEntity();
        if ($this->request->is('post')) {
            $progres = $this->Progress->patchEntity($progres, $this->request->data);
            if ($this->Progress->save($progres)) {
                $this->Flash->success('El progreso ha sido guardado.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('El progreso no ha sido guardado, intentalo nuevamente.');
            }
        }
        $budgetItems = $this->Progress->BudgetItems->find('list', ['limit' => 200]);
        $schedules = $this->Progress->Schedules->find('list', ['limit' => 200]);
        $paymentStatements = $this->Progress->PaymentStatements->find('list', ['limit' => 200]);
        $users = $this->Progress->Users->find('list', ['limit' => 200]);
        $this->set(compact('progres', 'budgetItems', 'schedules', 'paymentStatements', 'users'));
        $this->set('_serialize', ['progres']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Progres id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $progres = $this->Progress->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $progres = $this->Progress->patchEntity($progres, $this->request->data);
            if ($this->Progress->save($progres)) {
                $this->Flash->success('El progreso ha sido actualizado.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('El progreso no ha sido actualizado, intentalo nuevamente.');
            }
        }
        $budgetItems = $this->Progress->BudgetItems->find('list', ['limit' => 200]);
        $schedules = $this->Progress->Schedules->find('list', ['limit' => 200]);
        $paymentStatements = $this->Progress->PaymentStatements->find('list', ['limit' => 200]);
        $users = $this->Progress->Users->find('list', ['limit' => 200]);
        $this->set(compact('progres', 'budgetItems', 'schedules', 'paymentStatements', 'users'));
        $this->set('_serialize', ['progres']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Progres id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $progres = $this->Progress->get($id);
        if ($this->Progress->delete($progres)) {
            $this->Flash->success('El progreso ha sido eliminado.');
        } else {
            $this->Flash->error('El progreso no ha sido eliminado, intentalo nuevamente.');
        }
        return $this->redirect(['action' => 'index']);
    }
}
