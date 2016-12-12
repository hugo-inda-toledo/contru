<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * BudgetApprovals Controller
 *
 * @property \App\Model\Table\BudgetApprovalsTable $BudgetApprovals */
class BudgetApprovalsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Budgets', 'Users', 'BudgetStates']
        ];
        $this->set('budgetApprovals', $this->paginate($this->BudgetApprovals));
        $this->set('_serialize', ['budgetApprovals']);
    }

    /**
     * View method
     *
     * @param string|null $id Budget Approval id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $budgetApproval = $this->BudgetApprovals->get($id, [
            'contain' => ['Budgets', 'Users', 'Budget_States']
        ]);
        $this->set('budgetApproval', $budgetApproval);
        $this->set('_serialize', ['budgetApproval']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $budgetApproval = $this->BudgetApprovals->newEntity();
        if ($this->request->is('post')) {
            $budgetApproval = $this->BudgetApprovals->patchEntity($budgetApproval, $this->request->data);
            if ($this->BudgetApprovals->save($budgetApproval)) {
                $this->Flash->success(__('La aprobación del presupuesto ha sido guardada.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('La aprobación del presupuesto no ha sido guardada, intentalo nuevamente.'));
            }
        }
        $budgets = $this->BudgetApprovals->Budgets->find('list', ['limit' => 200]);
        $users = $this->BudgetApprovals->Users->find('list', ['limit' => 200]);
        $budgetStates = $this->BudgetApprovals->BudgetStates->find('list', ['limit' => 200]);
        $this->set(compact('budgetApproval', 'budgets', 'users', 'budgetStates'));
        $this->set('_serialize', ['budgetApproval']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Budget Approval id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $budgetApproval = $this->BudgetApprovals->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $budgetApproval = $this->BudgetApprovals->patchEntity($budgetApproval, $this->request->data);
            if ($this->BudgetApprovals->save($budgetApproval)) {
                $this->Flash->success(__('La aprobación del presupuesto ha sido actualizada.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('La aprobación del presupuesto no ha sido actualizada, intentalo de nuevo.'));
            }
        }
        $budgets = $this->BudgetApprovals->Budgets->find('list', ['limit' => 200]);
        $users = $this->BudgetApprovals->Users->find('list', ['limit' => 200]);
        $budgetStates = $this->BudgetApprovals->BudgetStates->find('list', ['limit' => 200]);
        $this->set(compact('budgetApproval', 'budgets', 'users', 'budgetStates'));
        $this->set('_serialize', ['budgetApproval']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Budget Approval id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $budgetApproval = $this->BudgetApprovals->get($id);
        if ($this->BudgetApprovals->delete($budgetApproval)) {
            $this->Flash->success(__('La aprobación del presupuesto ha sido eliminada.'));
        } else {
            $this->Flash->error(__('La aprobación del presupuesto no ha sido eliminada, intentalo de nuevo.'));
        }
        return $this->redirect(['action' => 'index']);
    }


     /**
     * change state method
     *
     * @param string|null $id Budget Approval id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function change($id = null)
    {
        //TODO permisos quien puede cambiar que estado
        $budget = $this->BudgetApprovals->Budgets->get($id, [
            'contain' => ['BudgetApprovals']
        ])->toArray();
        $budgetApproval = $this->BudgetApprovals->newEntity();
        $budgetApproval->budget_id = $id;
        $budgetApproval->user_id = $this->request->session()->read('Auth.User.id');
        if ($this->request->is(['patch', 'post', 'put'])) {
            $budgetApproval = $this->BudgetApprovals->patchEntity($budgetApproval, $this->request->data);
            if ($this->BudgetApprovals->save($budgetApproval)) {
                $this->Flash->success(__('La aprobación del presupuesto ha sido guardada.'));
                $this->loadModel('Observations');
                $observation = $this->Observations->newEntity();
                $observation->model = 'Budgets';
                $observation->action = 'comment';
                $observation->model_id = $budgetApproval->budget_id;
                $observation->user_id = $budgetApproval->user_id;
                $observation->observation = '[Estado: ' . $this->BudgetApprovals->BudgetStates->get($budgetApproval->budget_state_id)->name . '] ' . $budgetApproval->comment;
                if ($this->Observations->save($observation)) {
                    $this->Flash->success('El estado del presupuesto ha cambiado a: " ' . $this->BudgetApprovals->BudgetStates->get($budgetApproval->budget_state_id)->name . ' " .');
                    return $this->redirect(['controller' => 'Budgets', 'action' => 'review', $budgetApproval->budget_id]);
                } else {
                    $this->Flash->error('La observación no ha sido guardada, intentalo de nuevo.');
                }
            } else {
                $this->Flash->error(__('La aprobación del presupuesto no ha sido guardada.'));
            }
        }
        $currentState = $this->BudgetApprovals->Budgets->current_state($id);
        //$budgetStates = $this->BudgetApprovals->BudgetStates->find('list', ['limit' => 200,'conditions' => ['BudgetStates.id >' => $currentState]])->toArray();
        $budgetStates = $this->BudgetApprovals->BudgetStates->find('list', ['limit' => 200])->toArray();
        $this->set(compact('budgetApproval', 'budgetStates'));
        $this->set('_serialize', ['budgetApproval']);
    }

}
