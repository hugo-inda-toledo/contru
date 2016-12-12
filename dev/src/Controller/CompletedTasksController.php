<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use App\View\Helper\AccessHelper;

/**
 * CompletedTasks Controller
 *
 * @property \App\Model\Table\CompletedTasksTable $CompletedTasks */
class CompletedTasksController extends AppController
{
    /**
    * beforeFilter
    */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        //validar que el presupuesto no esté finalizado o sin aprobar
        $current_action = $this->request->params['action'];
        if ($current_action == 'add' ||
            $current_action == 'edit' ||
            $current_action == 'delete' ||
            $current_action == 'approve' ||
            $current_action == 'reject' ||
            $current_action == 'add_comment') {
            if (count($this->request->params['pass']) > 0) {
                if ($this->request->params['controller'] == 'CompletedTasks') {
                    $schedule = $this->CompletedTasks->Schedules->get($this->request->params['pass'][0]);
                    $current_state = $this->CompletedTasks->Schedules->Budgets->current_budget_state($schedule->budget_id);
                    if (empty($current_state) && $current_state == null) {
                        $this->Flash->info('El presupuesto de la obra no está configurado, no puede agregar información adicional.');
                        return $this->redirect(['controller' => 'schedules', 'action' => 'index']);
                    } else {
                         if ($current_state == -1) {
                            $this->Flash->info('La obra está bloqueada, no puede agregar información adicional.');
                            return $this->redirect(['controller' => 'schedules', 'action' => 'index']);
                        } else {
                            if ($current_state < 4 || $current_state == 6) {
                                $this->Flash->info('El presupuesto de la obra se encuentra en estados Pendiente Aprobación o Finalizado, no puede agregar información adicional.');
                                return $this->redirect(['controller' => 'schedules', 'action' => 'index']);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $access = new AccessHelper(new \Cake\View\View());

        $budget = null;
        $buildings = null;
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if ($access->verifyAccessByKeyword('admin_obra') ==  true || $access->verifyAccessByKeyword('asistente_rrhh') == true || $access->verifyAccessByKeyword('oficina_tecnica') == true) {
            $user_buildings = $this->CompletedTasks->Schedules->UserCreateds->getUserBuildings($this->request->session()->read('Auth.User.id'));
            if (count($user_buildings) > 0) {
                $budget = $this->CompletedTasks->Schedules->Budgets->find('all',[
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => $user_buildings[0]]])->first();
                if ($budget->building_id != $user_buildings[0]) {
                    $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde al Trabajo realizado. Por favor, edite la información de usuario.');
                    return $this->redirect(['controller' => 'users', 'action' => 'index']);
                }
            } else {
                $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                return $this->redirect(['controller' => 'users', 'action' => 'index']);
            }
        } else {
            $buildings = $this->CompletedTasks->Schedules->Budgets->Buildings->getActiveBuildingsWithSoftlandInfo();
            $last_building = $this->request->session()->read('Config.last_building');
            if (!empty($this->request->query)) {
                $budget = $this->CompletedTasks->Schedules->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => $this->request->query['building_id']]
                ])->first();
            } else {
                 if(!empty($last_building)) {
                    $budget = $this->CompletedTasks->Schedules->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => $last_building]
                ])->first();    
                } else {
                    $budget = $this->CompletedTasks->Schedules->Budgets->find('all', [
                        'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                        'conditions' => ['Budgets.building_id' => key($buildings)]
                    ])->first();
                }
            }
        }
        $schedules = $this->CompletedTasks->Schedules->find('all')
            ->where(['Schedules.budget_id' => $budget->id])
            ->join([
                'table' => 'completed_tasks',
                'alias' => 'CompletedTasks',
                'conditions' => ['CompletedTasks.schedule_id = Schedules.id']
            ])
            ->group(['Schedules.id'])
            ->contain(['Progress.BudgetItems', 'CompletedTasks.Workers']);
        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
        ])->first();

        $this->loadModel('Groups');
        $visitador = $this->Groups->find('all')
                        ->where(['Groups.group_keyword' => 'visitador'])
                        ->first();

        $approvals = array();
        $rejects = array();
        $this->loadModel('Approvals');
        foreach ($schedules as $schedule) {
            
            $approval = $this->Approvals->find('all', [
                'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.approve' => 1,
                 'Approvals.model_id' => $schedule->id, 'Approvals.group_id' => $visitador->id]
            ]);
            $approvals[$schedule->id] =  $approval;
            $reject = $this->Approvals->find('all', [
                'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.reject' => 1,
                 'Approvals.model_id' => $schedule->id, 'Approvals.group_id' => $visitador->id]
            ]);
            $rejects[$schedule->id] =  $reject;
        }
        // $completed_tasks_approvals = $this->CompletedTasks->getApprovals();
        $this->set('schedules', $this->paginate($schedules));
        $this->set('_serialize', ['schedules']);
        $this->set(compact('budget', 'buildings', 'approvals', 'rejects', 'sf_building'));
    }

    /**
     * View method
     *
     * @param string|null $id Completed Task id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($schedule_id = null)
    {
        $budget = null;
        $buildings = null;
        $schedule = null;
        $workers = null;
        $units = $this->CompletedTasks->Schedules->Budgets->BudgetItems->Units->find('list', ['limit' => 200])->toArray();
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if ($access->verifyAccessByKeyword('admin_obra') ==  true || $access->verifyAccessByKeyword('asistente_rrhh') == true || $access->verifyAccessByKeyword('oficina_tecnica') == true) {
            $user_buildings = $this->CompletedTasks->Schedules->UserCreateds->getUserBuildings($this->request->session()->read('Auth.User.id'));
            if (count($user_buildings) > 0) {
                $budget = $this->CompletedTasks->Schedules->Budgets->find('all', ['conditions' => ['Budgets.building_id' => $user_buildings[0]],
                 'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]]])->first();
                if (!empty($schedule_id) && $schedule_id != null) {
                    $schedule = $this->CompletedTasks->Schedules->get($schedule_id, ['contain' => ['Budgets' => [], 'CompletedTasks' => ['BudgetItems', 'Workers']]]);
                    if ($schedule->budget->building_id != $user_buildings[0]) {
                        $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde al Trabajo realizado. Por favor, edite la información de usuario.');
                        return $this->redirect(['controller' => 'users', 'action' => 'index']);
                    }
                } else {
                    $this->Flash->info('Ocurrió un error al cargar la información del Trabajo Realizado. Por favor, inténtelo nuevamente');
                    return $this->redirect(['controller' => 'completed_tasks', 'action' => 'index', $budget->id]);
                }
            } else {
                $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                return $this->redirect(['controller' => 'users', 'action' => 'index']);
            }
        } else {
            $buildings = $this->CompletedTasks->Schedules->Budgets->Buildings->getActiveBuildingsWithSoftlandInfo();
            if (!empty($schedule_id) && $schedule_id != null) {
                $schedule = $this->CompletedTasks->Schedules->get($schedule_id, ['contain' => ['Budgets', 'CompletedTasks' => ['BudgetItems', 'Workers']]]);
            } else {
                $this->Flash->info('Ocurrió un error al cargar la información del Trabajo Realizado. Por favor, inténtelo nuevamente');
                return $this->redirect(['controller' => 'schedules', 'action' => 'index']);
            }
            $budget = $this->CompletedTasks->Schedules->Budgets->get($schedule->budget_id, [
             'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]]]);
        }
        if (count($schedule->completed_tasks) > 0) {
            $completed_tasks_data = array();
            $completed_tasks_hours = array();
            foreach ($schedule->completed_tasks as $completed_task) {
                $workers_tasks_hours = $this->CompletedTasks->Workers->getTaskHoursByWorkerId($completed_task['worker']['id'], $schedule->id);
                $completed_tasks_data[$completed_task['worker_id']][] = $completed_task;
                $completed_tasks_hours[$completed_task['worker_id']]['tasks_hours'] = $workers_tasks_hours;
            }
            //información general
            $this->loadModel('Groups');
            $visitador = $this->Groups->find('all')
                            ->where(['Groups.group_keyword' => 'visitador'])
                            ->first();
            $this->loadModel('SfBuildings');
            $sf_building = $this->SfBuildings->find('all', ['conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]])->first();
            $this->loadModel('Approvals');
            $approvals = $this->Approvals->find('all', [
                'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.approve' => 1,
                 'Approvals.model_id' => $schedule->id, 'Approvals.group_id' => $visitador->id]
            ]);
            $rejects = $this->Approvals->find('all', [
                'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.reject' => 1,
                 'Approvals.model_id' => $schedule->id, 'Approvals.group_id' => $visitador->id]
            ]);
            $workers = $this->CompletedTasks->Workers->getSoftlandWorkersByBuildingWithWorkerId($budget->building_id);
            $this->set(compact('budget', 'sf_building', 'schedule_id', 'schedule', 'workers', 'completed_tasks_data', 'completed_tasks_hours', 'approvals', 'rejects', 'units'));
        }
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add($schedule_id = null)
    {
        $completedTask = $this->CompletedTasks->newEntity();
        $budget = null;
        $buildings = null;
        $schedule = null;
        $workers = null;
        $units = $this->CompletedTasks->Schedules->Budgets->BudgetItems->Units->find('list', ['limit' => 200])->toArray();
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if ($access->verifyAccessByKeyword('admin_obra') ==  true || $access->verifyAccessByKeyword('asistente_rrhh') == true || $access->verifyAccessByKeyword('oficina_tecnica') == true) {
            $user_buildings = $this->CompletedTasks->Schedules->UserCreateds->getUserBuildings($this->request->session()->read('Auth.User.id'));
            if (count($user_buildings) > 0) {
                if (empty($schedule_id) && is_null($schedule_id)) {
                    //usar la última schedule agregada
                    $budget = $this->CompletedTasks->Schedules->Budgets->find('all', ['conditions' => ['Budgets.building_id' => $user_buildings[0]],
                     'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates']])->first();
                    $schedule_id = $this->CompletedTasks->Schedules->find('all',
                        ['conditions' => ['Schedules.budget_id' => $budget->id]])->last();
                    $exists_completed_tasks = $this->CompletedTasks->find('all', ['conditions' => ['CompletedTasks.schedule_id' => $schedule_id->id]])->count();
                    if ($exists_completed_tasks > 0) {
                        $this->Flash->info('Ya se ingresó el Trabajo Realizado para está planificación. Puede editarlo');
                        return $this->redirect(['controller' => 'completed_tasks', 'action' => 'edit', $schedule_id->id]);
                    }
                    $schedule = $this->CompletedTasks->Schedules->get($schedule_id->id, ['contain' => ['Budgets', 'Progress' => ['BudgetItems']]]);
                } else {
                    $budget = $this->CompletedTasks->Schedules->Budgets->find('all', ['conditions' => ['Budgets.building_id' => $user_buildings[0]],
                     'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates']])->first();
                    $exists_completed_tasks = $this->CompletedTasks->find('all', ['conditions' => ['CompletedTasks.schedule_id' => $schedule_id]])->count();
                    if ($exists_completed_tasks > 0) {
                        $this->Flash->info('Ya se ingresó el Trabajo Realizado para está planificación. Puede editarlo');
                        return $this->redirect(['controller' => 'completed_tasks', 'action' => 'edit', $schedule_id]);
                    }
                    $schedule = $this->CompletedTasks->Schedules->get($schedule_id, ['contain' => ['Budgets', 'Progress' => ['BudgetItems']]]);
                    if ($schedule->budget->building_id != $user_buildings[0]) {
                        $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde al Trabajo realizado. Por favor, edite la información de usuario.');
                        return $this->redirect(['controller' => 'users', 'action' => 'index']);
                    }
                }
            } else {
                $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                return $this->redirect(['controller' => 'users', 'action' => 'index']);
            }
        } else {
            $buildings = $this->CompletedTasks->Schedules->Budgets->Buildings->getActiveBuildingsWithSoftlandInfo();
            if (!empty($schedule_id) && $schedule_id != null) {
                $schedule = $this->CompletedTasks->Schedules->get($schedule_id, ['contain' => ['Budgets', 'Progress' => ['BudgetItems']]]);
            } else {
                $this->Flash->info('Ocurrió un error al cargar la información del Trabajo Realizado. Por favor, inténtelo nuevamente');
                return $this->redirect(['controller' => 'schedules', 'action' => 'index']);
            }
            $budget = $this->CompletedTasks->Schedules->Budgets->get($schedule->budget_id, [
             'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates']]);
        }
        $workers = $this->CompletedTasks->Workers->getSoftlandWorkersWithAssistsByBuilding($schedule->budget->building_id, $schedule->start_date, $schedule->finish_date);
        if (empty($workers) || is_null($workers)) {
            $this->Flash->info('Los Trabajadores de la obra no tienen asistencias positivas para la fecha de la planificación del Trabajo Realizado.');
            return $this->redirect(['action' => 'index']);
        }
        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', ['conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]])->first();
        $this->set(compact('budget', 'sf_building', 'schedule_id', 'schedule', 'workers', 'completedTask', 'units'));
        if ($this->request->is('post')) {
            if (!empty($this->request->data['CompletedTasks'])) {
                foreach ($this->request->data['CompletedTasks'] as $completed_task) {
                    foreach ($completed_task['worker_softland_id'] as $worker_softland_id) {
                        if (!empty($worker_softland_id)) {
                            $worker_id = $this->CompletedTasks->Workers->find('all', ['conditions' => ['Workers.softland_id' => $worker_softland_id]])->first();
                            (!$completedTask->isNew()) ? $completedTask = $this->CompletedTasks->newEntity() : '';
                            $completedTask->schedule_id = $completed_task['schedule_id'];
                            $completedTask->budget_item_id = $completed_task['budget_item_id'];
                            $completedTask->worker_id = $worker_id->id;
                            $completedTask->user_created_id = $this->request->session()->read('Auth.User.id');
                            if ($this->CompletedTasks->save($completedTask)) {
                                $this->Flash->success('Se ha guardado correctamente el Trabajo Realizado');
                            } else {
                                //TODO algún trabajador no se guarda, redirigir a edit
                                $this->Flash->error('Ocurrió un error al guardar el Trabajo Realizado. Por Favor, inténtelo nuevamente');
                            }
                        }
                    }
                }
                return $this->redirect(['controller' => 'completed_tasks', 'action' => 'edit', $completedTask->id]);
            } else {
                $this->Flash->error('Ocurrió un error al guardar el Trabajo Realizado. Por Favor, inténtelo nuevamente');
            }
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Completed Task id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($schedule_id = null)
    {
        $budget = null;
        $buildings = null;
        $schedule = null;
        $workers = null;
        $units = $this->CompletedTasks->Schedules->Budgets->BudgetItems->Units->find('list', ['limit' => 200])->toArray();

        $this->loadModel('Groups');
        $visitador = $this->Groups->find('all')
                        ->where(['Groups.group_keyword' => 'visitador'])
                        ->first();

        $this->loadModel('Approvals');
        $approvals = $this->Approvals->find('all', [
            'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.approve' => 1,
             'Approvals.model_id' => $schedule_id, 'Approvals.group_id' => $visitador->id]
        ]);
        if (count($approvals) > 0) {
            $this->Flash->info('No es posible editar un trabajo realizado que fue aprobado por un visitador.');
            return $this->redirect(['action' => 'view', $schedule_id]);
        }
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->CompletedTasks->Schedules->UserCreateds->getUserBuildings($this->request->session()->read('Auth.User.id'));
            if (count($user_buildings) > 0) {
                $budget = $this->CompletedTasks->Schedules->Budgets->find('all', ['conditions' => ['Budgets.building_id' => $user_buildings[0]],
                 'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]]])->first();
                if (!empty($schedule_id) && $schedule_id != null) {
                    $schedule = $this->CompletedTasks->Schedules->get($schedule_id,
                        ['contain' => ['CompletedTasks' => ['BudgetItems', 'Workers']]]);
                    if ($schedule->budget->building_id != $user_buildings[0]) {
                        $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde al Trabajo realizado. Por favor, edite la información de usuario.');
                        return $this->redirect(['controller' => 'users', 'action' => 'index']);
                    }
                } else {
                    $this->Flash->info('Ocurrió un error al cargar la información del Trabajo Realizado. Por favor, inténtelo nuevamente');
                    return $this->redirect(['controller' => 'schedules', 'action' => 'index']);
                }
            } else {
                $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                return $this->redirect($this->referer());
            }
        } else {
            $buildings = $this->CompletedTasks->Schedules->Budgets->Buildings->getActiveBuildingsWithSoftlandInfo();
            if (!empty($schedule_id) && $schedule_id != null) {
                $schedule = $this->CompletedTasks->Schedules->get($schedule_id, ['contain' => ['CompletedTasks' => ['BudgetItems', 'Workers']]]);
           } else {
                $this->Flash->info('Ocurrió un error al cargar la información del Trabajo Realizado. Por favor, inténtelo nuevamente');
                return $this->redirect(['controller' => 'schedules', 'action' => 'index']);
            }
            $budget = $this->CompletedTasks->Schedules->Budgets->get($schedule->budget_id, [
             'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]]]);
        }
        $workers = $this->CompletedTasks->Workers->getSoftlandWorkersWithAssistsByBuilding($budget->building_id, $schedule->start_date, $schedule->finish_date);
        if (empty($workers) || is_null($workers)) {
            $this->Flash->info('Los Trabajadores de la obra no tienen asistencias positivas para la fecha de la planificación del Trabajo Realizado.');
            return $this->redirect(['action' => 'index']);
        }
        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', ['conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]])->first();
        if (count($schedule->completed_tasks) > 0) {
            $completed_tasks_data = array();
            foreach ($schedule->completed_tasks as $completed_task) {
                $completed_tasks_data[$completed_task['budget_item_id']][] = $completed_task;
            }
        }
        $this->set(compact('budget', 'sf_building', 'schedule_id', 'schedule', 'workers', 'completed_tasks_data', 'units'));
        if ($this->request->is(['patch', 'post', 'put'])) {
            if (!empty($this->request->data['CompletedTasks'])) {
                foreach ($this->request->data['CompletedTasks'] as $budget_item_id => $completed_task) {
                    $this->CompletedTasks->deleteAll(['budget_item_id' => $budget_item_id, 'schedule_id' => $completed_task['schedule_id']]);
                    foreach ($completed_task['worker_softland_id'] as $worker_softland_id) {
                        $worker_id = $this->CompletedTasks->Workers->find('all', ['conditions' => ['Workers.softland_id' => $worker_softland_id]])->first();
                        $completedTask = $this->CompletedTasks->newEntity();
                        $completedTask->schedule_id = $completed_task['schedule_id'];
                        $completedTask->budget_item_id = $budget_item_id;
                        $completedTask->worker_id = $worker_id->id;
                        $completedTask->budget_item_percentage = $completed_task[$worker_softland_id]['worker_percentage'];
                        $completedTask->user_created_id = $this->request->session()->read('Auth.User.id');
                        $completedTask->user_modified_id = $this->request->session()->read('Auth.User.id');
                        if ($this->CompletedTasks->save($completedTask)) {
                            $this->Flash->success('Se ha guardado correctamente el Trabajo Realizado');
                        } else {
                            //TODO algún trabajador no se guarda, redirigir a edit
                            $this->Flash->error('Ocurrió un error al guardar el Trabajo Realizado. Por Favor, inténtelo nuevamente');
                        }
                    }
                }
                return $this->redirect(['controller' => 'completed_tasks', 'action' => 'index']);
            } else {
                $this->Flash->error('Ocurrió un error al guardar el Trabajo Realizado. Por Favor, inténtelo nuevamente');
            }
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Completed Task id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $completedTask = $this->CompletedTasks->get($id);
        if ($this->CompletedTasks->delete($completedTask)) {
            $this->Flash->success('El trabajo realizado fue eliminado');
        } else {
            $this->Flash->error('El trabajo realizado no fue eliminado, intentalo nuevamente.');
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Agregar comentario a trabajo realizado para entender la aprobación, rechazo, u error de ingreso.
     * @param string $id identificador trabajo realizado
     * @author [name] <[<email address>]>
     */
    public function add_comment($id='')
    {
        //verificar obra tiene presupuesto

        //verificar perfil de usuario

        //buscar trabajo realizado

        //aprobar en cascada
    }

    /**
     * Aprobar trabajo realizados, debe soportar desde Jefe RRHH hasta Gerente General, dependiendo del monto
     * @param  string $id identificador del trabajo realizado
     * @return bool     aprobación
     * @author [name] <[<email address>]>
     */
    public function approve()
    {
        $this->autoRender = false;
        $access = new AccessHelper(new \Cake\View\View());

        $this->loadModel('Approvals');
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if (!empty($this->request->data['schedule_id']) && $this->request->data['schedule_id'] != null) {
            if ($access->verifyLevel(6) == true) {
                //echo 'oli';
                $user_buildings = $this->CompletedTasks->Schedules->UserCreateds->getUserBuildings($this->request->session()->read('Auth.User.id'));
                $schedule = $this->CompletedTasks->Schedules->get($this->request->data['schedule_id'],
                    ['contain' => ['CompletedTasks', 'Budgets']]);

                /*echo '<pre>';
                print_r($user_buildings);
                echo '</pre>';

                echo '<pre>';
                print_r($schedule);
                echo '</pre>';*/

                if (/*in_array($schedule->budget->building_id, $user_buildings) && */count($schedule->completed_tasks) > 0) {

                    //echo 'holi2';
                    $existing_approvals = $this->Approvals->find('all', [
                        'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.action' => $this->request->params['action'],
                          'Approvals.model_id' => $this->request->data['schedule_id'], 'Approvals.group_id' => $group_id]
                        ]);
                    if ($existing_approvals->count() == 0) {
                        //echo 'oli3';
                        $approval = $this->Approvals->newEntity();
                        $approval->model = $this->modelClass;
                        $approval->model_id = $this->request->data['schedule_id'];
                        $approval->action = $this->request->params['action'];
                        $approval->approve = true;
                        $approval->reject = false;
                        $approval->user_id = $this->request->session()->read('Auth.User.id');
                        $approval->group_id = $group_id;
                        $approval->comment = 'Aprobado por Visitador: ' . $this->request->session()->read('Auth.User.first_name') . ' ' . $this->request->session()->read('Auth.User.lastname_f');
                        if ($this->Approvals->save($approval)){
                            $this->Flash->success('El Trabajo Realizado se aprobó correctamente.');
                        } else {
                            $this->Flash->error('Ocurrió un Error al Aprobar el trabajo realizado.');
                        }
                    } else {
                        $this->Flash->info('El trabajo realizado ya fue aprobado por un visitador.');
                    }
                    return $this->redirect(['action' => 'view', $this->request->data['schedule_id']]);
                }
            }
        }
    }

    /**
     * Rechazar trabajo realizado, se podrá rechazar el trabajo realizado, omitiendo la información de este al presupuesto de la obra y cálculos de RRHH
     * @param  string $id identificador trabajo realizado
     * @return bool     rechazo
     * @author [name] <[<email address>]>
     */
    public function reject()
    {
        $this->autoRender = false;
        $this->loadModel('Approvals');

        $access = new AccessHelper(new \Cake\View\View());

        $group_id = $this->request->session()->read('Auth.User.group_id');
        if (!empty($this->request->data['schedule_id']) && $this->request->data['schedule_id'] != null) {
            if ($access->verifyLevel(6) == true) {
                $user_buildings = $this->CompletedTasks->Schedules->UserCreateds->getUserBuildings($this->request->session()->read('Auth.User.id'));
                $schedule = $this->CompletedTasks->Schedules->get($this->request->data['schedule_id'],
                    ['contain' => ['CompletedTasks', 'Budgets']]);
                if (/*in_array($schedule->budget->building_id, $user_buildings) && */count($schedule->completed_tasks) > 0) {
                    $existing_approvals = $this->Approvals->find('all', [
                        'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.action' => $this->request->params['action'],
                          'Approvals.model_id' => $this->request->data['schedule_id'], 'Approvals.group_id' => $group_id]
                        ]);
                    if ($existing_approvals->count() == 0) {
                        $approval = $this->Approvals->newEntity();
                        $approval->model = $this->modelClass;
                        $approval->model_id = $this->request->data['schedule_id'];
                        $approval->action = $this->request->params['action'];
                        $approval->approve = false;
                        $approval->reject = true;
                        $approval->user_id = $this->request->session()->read('Auth.User.id');
                        $approval->group_id = $group_id;
                        $approval->comment = (!empty($this->request->data['Approval']['comment'])) ? $this->request->data['Approval']['comment'] :
                        'Rechazado por Visitador: ' . $this->request->session()->read('Auth.User.first_name') . ' ' . $this->request->session()->read('Auth.User.lastname_f');
                        if ($this->Approvals->save($approval)) {
                            $this->Flash->success('El Trabajo Realizado se rechazó correctamente.');
                        } else {
                            $this->Flash->error('Ocurrió un Error al Rechazar el trabajo realizado.');
                        }
                    } else {
                        $this->Flash->info('El trabajo realizado ya fue rechazado por un visitador.');
                    }
                    return $this->redirect(['action' => 'view', $this->request->data['schedule_id']]);
                }
            }
        }
    }
}
