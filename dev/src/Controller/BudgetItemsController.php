<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
/**
 * BudgetItems Controller
 *
 * @property \App\Model\Table\BudgetItemsTable $BudgetItems */
class BudgetItemsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        /*
        $this->paginate = [
            'contain' => ['Budgets', 'ParentBudgetItems', 'Units']
        ];
        */
        $this->paginate = [
            'limit' => 5,
            'contain' => ['Budgets']
        ];
        $this->set('budgetItems', $this->paginate($this->BudgetItems));
        $this->set('_serialize', ['budgetItems']);
    }

    /**
     * View method
     *
     * @param string|null $id Budget Item id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->viewBuilder()->layout('ajax');
        if (!empty($id) && $id != null) {
            $budgetItem = $this->BudgetItems->get($id, [
                'contain' => ['Budgets', 'Units', 'Progress', 'DealDetails', 'BonusDetails']
            ]);
            $deal_ids = array();
            foreach ($budgetItem->deal_details as $deal_detail) {
                if (!array_search($deal_detail->deal_id, $deal_ids)) {
                    $deal_ids[] = $deal_detail->deal_id;
                }
            }
            $states_deals = $this->BudgetItems->DealDetails->Deals->getStates();
            $dealStates = array($states_deals[3], $states_deals[5]);
            $has_deals = false;
            if( count($deal_ids) > 0 ){
                $deals = $this->BudgetItems->DealDetails->Deals->find('all', [
                    'conditions' => ['Deals.id IN' => $deal_ids, 'Deals.state IN' => $dealStates],
                    'contain' => ['Workers'],
                    'order' => ['start_date ASC']
                ]);
                $has_deals = true;
            }
            $bonus_ids = array();
            foreach ($budgetItem->bonus_details as $bonus_detail) {
                if (!array_search($bonus_detail->bonus_id, $bonus_ids)) {
                    $bonus_ids[] = $bonus_detail->bonus_id;
                }
            }
            $states_bonuses = $this->BudgetItems->BonusDetails->Bonuses->getStates();
            $bonusStates = array($states_bonuses[3], $states_bonuses[5]);
            $has_bonuses = false;
            if( count($bonus_ids) > 0 ){
                $bonuses = $this->BudgetItems->BonusDetails->Bonuses->find('all', [
                    'conditions' => ['Bonuses.id IN' => $bonus_ids, 'Bonuses.state IN' => $bonusStates],
                    'contain' => ['Workers']
                ]);
            }
            $fichas = $this->BudgetItems->DealDetails->Deals->Workers->getSoftlandWorkersByBuilding($budgetItem->budget->building_id);
            $workers = array_combine(array_column($fichas, 'ficha'), array_column($fichas, 'nombres'));
            $progress = $this->BudgetItems->getCurrentProgressValue($budgetItem->budget_id);
            $budgetSchedules = $this->BudgetItems->BudgetItemsSchedules->find('all', [
                'conditions' => ['BudgetItemsSchedules.budget_item_id' => $budgetItem->id],
                'contain' => [
                    'Schedules',
                    'Schedules.Progress' => function ($q)  use ($budgetItem) {
                        return $q->where(['Progress.budget_item_id' => $budgetItem->id]);
                    },
                    'Schedules.CompletedTasks'  => function ($q)  use ($budgetItem) {
                        return $q->where(['CompletedTasks.budget_item_id' => $budgetItem->id]);
                    },
                ]
            ]);
            $task_hours = array();
            $allTaskHoursByBudgetItems = array();
            foreach ($budgetSchedules as $budgetSchedule) {
                foreach ($budgetSchedule->schedule->completed_tasks as $completed_task) {
                    $allTaskHoursByBudgetItems = $this->BudgetItems->DealDetails->Deals->Workers->getTaskHoursByWorkerIdOrderByBudgetItem($completed_task->worker_id, $completed_task->schedule_id);
                    foreach ($allTaskHoursByBudgetItems as $all_budget_item_id => $hours) {
                        if ($all_budget_item_id == $id) {
                            $task_hours[$completed_task->worker_id][$completed_task->schedule_id] = $hours;
                        }
                    }
                }
            }
            if( count($fichas) > 0 ){
                $trabajadores = $this->BudgetItems->DealDetails->Deals->Workers->find('all', [
                    'conditions' => ['Workers.softland_id IN' => array_column($fichas,'ficha')]
                ]);
            }
            $this->set(compact('budgetItem', 'has_deals', 'deals', 'workers', 'has_bonuses', 'bonuses', 'progress', 'budgetSchedules', 'trabajadores', 'task_hours'));
            $this->set('_serialize', ['budgetItem']);
        } else {
            $this->Flash->error('Ocurrió un error al buscar la información de la Partida de Presupuesto seleccionada. Por favor, inténtenlo nuevamente');
            return $this->redirect($this->referer());
        }
    }

    /**
     * Funcion que guarda el valor objetivo de la partida por AJAX
     */
    public function target_value()
    {
        $this->autoRender = false;
        $response_object = array();
		if ($this->request->is('ajax')) {
			if (isset($this->request->data['target_value']) && !empty($this->request->data['target_value']) &&
             isset($this->request->data['budget_item_id']) && !empty($this->request->data['budget_item_id'])) {
                $budgetItem = $this->BudgetItems->get($this->request->data['budget_item_id']);
                if (!empty($budgetItem)) {
                    $budgetItem->target_value = $this->request->data['target_value'];
                    if ($this->BudgetItems->save($budgetItem)) {
                        $response_object['status'] = 'ok';
                        $response_object['id'] = $budgetItem->id;
                        $response_object['message'] = 'El valor objetivo de la partida se guardó correctamente';
                    } else {
                        $response_object['status'] = 'fail';
                        $response_object['date'] = $budgetItem->id;
                        $response_object['message'] = 'El valor objetivo de la partida no se guardó correctamente. Por favor, inténtelo nuevamente.';
                    }
                } else {
                    $response_object['status'] = 'fail';
                    $response_object['date'] = $budgetItem->id;
                    $response_object['message'] = 'La información del servidor no se pudo procesar. Por favor, inténtelo nuevamente.';
                }
            }
            echo json_encode($response_object);
		}
    }

    /**
     * Funcion que guarda el valor objetivo de la partida por AJAX
     */
    public function multiple_target_value()
    {
        $this->autoRender = false;
        $response_object = array();
        if ($this->request->is('ajax') && $this->request->is('post')) {
            $data = $this->request->data;
            $saveds = array();
            $nosaveds = array();
            foreach($data AS $d){
                $budgetItem = $this->BudgetItems->get($d['id']);
                if(!empty($budgetItem)){
                    $budgetItem->target_value = $d['target_value'];
                    if (!$this->BudgetItems->save($budgetItem)) {
                        $nosaveds[] = $budgetItem->id;
                    }else{
                        $saveds[]= $budgetItem->id;
                    }
                }
            }
            if(empty($nosaveds)){
                $response_object['status'] = 'ok';
                $response_object['ids'] = json_encode($saveds);
                $response_object['message'] = 'Los valores de objetivos fueron guardados con éxito.';
            }else{
                $response_object['status'] = 'fail';
                $response_object['ids'] = json_encode($nosaveds);
                $response_object['message'] = 'Los valores objetivos marcados en rojo no pudieron ser guardados correctamente. Por favor, inténtelo nuevamente';
            }
            echo json_encode($response_object);
        }
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        if ($this->request->is('post')) {

            $budgetItem = $this->BudgetItems->patchEntity($budgetItem, $this->request->data);

            if ($this->BudgetItems->save($budgetItem)) {
                $this->Flash->success('The budget item has been saved.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('The budget item could not be saved. Please, try again.');
            }

        }
        $budgets = $this->BudgetItems->Budgets->find('list', ['limit' => 200]);
        $parentBudgetItems = $this->BudgetItems->ParentBudgetItems->find('list', ['limit' => 200]);
        $units = $this->BudgetItems->Units->find('list', ['limit' => 200]);
        $this->set(compact('budgetItem', 'budgets', 'parentBudgetItems', 'units'));
        $this->set('_serialize', ['budgetItem']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Budget Item id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $budgetItem = $this->BudgetItems->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $budgetItem = $this->BudgetItems->patchEntity($budgetItem, $this->request->data);
            if ($this->BudgetItems->save($budgetItem)) {
                $this->Flash->success('El item ha sido actualizado.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('El item no ha sido actualizado, intentalo nuevamente.');
            }
        }
        $budgets = $this->BudgetItems->Budgets->find('list', ['limit' => 200]);
        $parentBudgetItems = $this->BudgetItems->ParentBudgetItems->find('list', ['limit' => 200]);
        $units = $this->BudgetItems->Units->find('list', ['limit' => 200]);
        $this->set(compact('budgetItem', 'budgets', 'parentBudgetItems', 'units'));
        $this->set('_serialize', ['budgetItem']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Budget Item id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $budgetItem = $this->BudgetItems->get($id);
        if ($this->BudgetItems->delete($budgetItem)) {
            $this->Flash->success('El item ha sido Eliminado.');
        } else {
            $this->Flash->error('El item no pudo ser eliminado, por favor intente nuevamente.');
        }
        return $this->redirect(['action' => 'index']);
    }



    public function remove_all($id = null, $type = 0)
    {
        //$this->request->allowMethod(['post', 'delete']);
        $showDebug = true;
        switch ($type) {
            case 0:
                $bi = $this->BudgetItems->find('all',['conditions' => ['budget_id' => $id]]);
                break;
            case 1:
                $bi = $this->BudgetItems->find('all',['conditions' => ['budget_id' => $id, 'extra' => 0]]);
                break;
            case 2:
                $bi = $this->BudgetItems->find('all',['conditions' => ['budget_id' => $id, 'extra' => 1]]);
                break;
        }
        foreach($bi as $item) {
            debug($item);
            if ($this->BudgetItems->delete($item)) {
                $this->Flash->success('El item: ' . $item->id . ' ha sido Eliminado.');
            } else {
                $this->Flash->error('El item no pudo ser eliminado, por favor intente nuevamente.');
            }
        }

        // cucho: esta linea está de mas, ña comento
        // $bi = $this->BudgetItems->find('all',['conditions' => ['budget_id' => $id]]);
        $biQ = $bi->all()->toArray();
        debug($biQ);
        die("asx");
        //die("a");
        if(empty($biQ)) {
            $this->Flash->success('Los items han sido eliminados.');
            //logica budget_approvals
            $budgetApproval = $this->BudgetItems->Budgets->BudgetApprovals->newEntity();
            $budgetApproval->budget_id = $id;
            $budgetApproval->user_id = $this->request->session()->read('Auth.User.id');
            $budgetApproval->budget_state_id = 1;
            $budgetApproval->comment = 'Se eliminaron todas las Partidas';
            if($this->BudgetItems->Budgets->BudgetApprovals->save($budgetApproval)) {
                 // start obs
                $this->loadModel('Observations');
                $observation = $this->Observations->newEntity();
                $observation->model = 'Budgets';
                $observation->action = 'comment';
                $observation->model_id = $budgetApproval->budget_id;
                $observation->user_id = $budgetApproval->user_id;
                $observation->observation = '[Estado: ' . $this->BudgetItems->Budgets->BudgetApprovals->BudgetStates->get($budgetApproval->budget_state_id)->name . '] ' . $budgetApproval->comment;
                if ($this->Observations->save($observation)) {
                    ($showDebug) ? debug('observation worked') : false;
                }
                ($showDebug) ? debug('approval worked') : false;
            } else {
                ($showDebug) ? debug('approval no worked') : false;
                ($showDebug) ? debug($budgetApproval) : false;
            }
            //fin logica budget_approvals
        } else {
            $this->Flash->error('Los items no han podido ser eliminados, por favor intente nuevamente.');
        }
        return $this->redirect(['controller' => 'budgets', 'action' => 'review', $id]);
    }

    /**
     * [reset_obra description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
     */
    public function reset_obra($id, $omit=true) {

        // borra la asistencia
        $this->BudgetItems->borra_asistencia($id);

        // borra estados de pago
        $this->BudgetItems->borra_estadospago($id);

        // borra estados de pago
        $this->BudgetItems->borra_bonos($id);

        // borra las planificaciones
        $this->BudgetItems->borra_planificaciones($id);


        // borrar aprobaciones
        $this->loadModel('BudgetApprovals');
        $this->BudgetApprovals->deleteAll(['budget_id' => $id]);

        // busca la lista de busget items
        $bi = $this->BudgetItems->find('list', [
            'conditions' => ['budget_id' => $id],
            'keyField' => 'id',
            'valueField' => 'id'
            ])->toArray();

        // borra las guias de salida
        $this->loadModel('GuideExits');
        if (! empty($bi)) $this->GuideExits->deleteAll(['budget_item_id IN' => $bi]);

        // borra los budgetitems
        $this->BudgetItems->deleteAll(['budget_id' => $id]);

        // borra el budget: presupuesto
        $this->loadModel('Budgets');
        $budgets = $this->Budgets->find('list', [
            'conditions' => ['id' => $id],
            'keyField' => 'building_id',
            'valueField' => 'building_id'
            ])->toArray();


        $this->Budgets->deleteAll(['id' => $id]);

        // se borran los datos de la obra
        $this->loadModel('Buildings');
        $building_associated = $this->Buildings->find('all', [
            'conditions' => ['id IN' => $budgets]
        ])->first();
        $building_associated->address = '';
        $building_associated->client = '';
        $building_associated->omit = ($omit=="false")?false:true;
        $this->Buildings->save($building_associated);

        // volver a c

        $message = "Los datos de la obra han sido eliminados.";
        $message.=($omit=="false")?' Obra en la "Lista obras ignoradas".':'';
        $this->Flash->success('Los datos de la obra han sido eliminados.');
        return $this->redirect('/buildings/index');
    }

}
