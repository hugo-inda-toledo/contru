<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\I18n\Time;


/**
 * Schedules Controller
 *
 * @property \App\Model\Table\SchedulesTable $Schedules */
class SchedulesController extends AppController
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
            $current_action == 'progress' ||
            $current_action == 'approve_progress' ||
            $current_action == 'reject_progress') {
            if (count($this->request->params['pass']) > 0) {
                if ($this->request->params['controller'] == 'Schedules' && $current_action != 'add') {
                    $schedule = $this->Schedules->get($this->request->params['pass'][0]);
                    $current_state = $this->Schedules->Budgets->current_budget_state($schedule->budget_id);
                    if (empty($current_state) && $current_state == null) {
                        $this->Flash->info('El presupuesto de la obra no está configurado, no puede agregar información adicional.');
                        return $this->redirect(['action' => 'index']);
                    } else {
                         if ($current_state == -1) {
                            $this->Flash->info('La obra está bloqueada, no puede agregar información adicional.');
                            return $this->redirect(['action' => 'index']);
                        } else {
                            if ($current_state < 4 || $current_state == 6) {
                                $this->Flash->info('El presupuesto de la obra se encuentra en estados Pendiente Aprobación o Finalizado, no puede agregar información adicional.');
                                return $this->redirect(['action' => 'index']);
                            }
                        }
                    }
                } else {
                    $current_state = $this->Schedules->Budgets->current_budget_state($this->request->params['pass'][0]);
                    if (isset($current_state) && $current_state == null) {
                        $this->Flash->info('El presupuesto de la obra no está configurado, no puede agregar información adicional.');
                        return $this->redirect(['action' => 'index']);
                    } else {
                         if ($current_state == -1) {
                            $this->Flash->info('La obra está bloqueada, no puede agregar información adicional.');
                            return $this->redirect(['action' => 'index']);
                        } else {
                            if ($current_state < 4 || $current_state == 6) {
                                $this->Flash->info('El presupuesto de la obra se encuentra en estados Pendiente Aprobación o Finalizado, no puede agregar información adicional.');
                                return $this->redirect(['action' => 'index']);
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
        $budget = null;
        $buildings = null;
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Schedules->UserCreateds->getUserBuildings($this->request->session()->read('Auth.User.id'));
            if (count($user_buildings) > 0) {
                $budget = $this->Schedules->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates'],
                    'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                ])->first();
                if ($budget->building_id != $user_buildings[0]) {
                    $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde a la asistencia. Por favor, edite la información de usuario.');
                    return $this->redirect(['controller' => 'users', 'action' => 'index']);
                }
            } else {
                $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                return $this->redirect(['controller' => 'users', 'action' => 'index']);
            }
        } else {
            ///////////////////////////////////////////////////////
            // perfiles de usuarios con acceso a todas las obras //
            ///////////////////////////////////////////////////////
            $buildings = $this->Schedules->Budgets->Buildings->getActiveBuildingsWithSoftlandInfo();
            $last_building = $this->request->session()->read('Config.last_building');
            if (!empty($this->request->query['building_id']) && $this->request->query['building_id'] != null) {
                $budget = $this->Schedules->Budgets->find('all', [
                    'conditions' => ['Budgets.building_id' => $this->request->query['building_id']],
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                ])->first();
            } else {
                if(!empty($last_building)) {
                    $budget = $this->Schedules->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => $last_building]
                ])->first();
                } else {
                    $budget = $this->Schedules->Budgets->find('all', [
                        'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                        'conditions' => ['Budgets.building_id' => key($buildings)]
                    ])->first();
                }
            }
        }
        $this->paginate = [
            'conditions' => ['budget_id' => $budget->id],
            'contain' => ['Progress', 'CompletedTasks', 'Approvals' => [
                'foreignKey' => 'model_id',
                'queryBuilder' => function ($q) {
                    // todos los approve o reject
                    // ordenados por el ultimo creado
                    return $q->where(['Approvals.model' => 'Schedules', 'Approvals.approve' => true])
                        ->order(['Approvals.created' => 'DESC']);
                        //->limit(1);
                    }
                ]
            ],
            'order' => ['Schedules.start_date' => 'DESC']
        ];
         //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
        ])->first();
        $this->loadModel('Approvals');
        $schedules_states = array();
        $schedules_rejects = array();
        foreach ($this->paginate($this->Schedules) as $schedule) {
            $schedules_states[$schedule->id]['advance_state'] = $schedule_state = $this->Schedules->progress_advance_state($schedule->id);
            $schedules_states[$schedule->id]['editable'] = true;
            if ($schedules_states[$schedule->id]['advance_state']) {
                if (count($schedule->approvals) > 0) {
                    foreach ($schedule->approvals as $approval) {
                        if ($approval->group_id == USR_GRP_VISITADOR) {
                            $schedules_states[$schedule->id]['approval_state'][USR_GRP_VISITADOR] = 'Aprobado por Visitador';
                        } elseif ($approval->group_id == USR_GRP_GE_GRAL || $approval->group_id == USR_GRP_GE_FINAN) {
                            $schedules_states[$schedule->id]['approval_state'][USR_GRP_GE_GRAL] = 'Aprobado por Gerente';
                            break;
                        }
                    }
                } else {
                    $schedules_states[$schedule->id]['approval_state'][0] = 'Pendiente Aprobación Visitador';
                }
            } else {
                $schedules_states[$schedule->id]['approval_state'][-1] = 'Pendiente Ingreso de Datos';
            }
            $schedules_rejects[$schedule->id] = $this->Approvals->find('list', [
                'conditions' => ['Approvals.model' => $this->modelClass, 'action' => 'reject_progress', 'Approvals.reject' => true, 'Approvals.model_id' => $schedule->id],
                'keyField' => 'group_id',
                'valueField' => 'user_id'
            ])->toArray();
        }
        $completed_tasks_approvals = $this->Approvals->find('list', [
            'conditions' => ['Approvals.model' => 'CompletedTasks', 'action' => 'approve', 'Approvals.approve' => true],
            'keyField' => 'model_id',
            'valueField' => 'approve'
        ])->toArray();
        $this->set('schedules', $this->paginate($this->Schedules));
        $this->set('_serialize', ['schedules']);
        $this->set(compact('budget', 'buildings', 'schedules_states', 'schedules_rejects', 'sf_building', 'completed_tasks_approvals'));
    }

    /**
     * View method
     *
     * @param string|null $id Schedule id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $units = $this->Schedules->Budgets->BudgetItems->Units->find('list', ['limit' => 200])->toArray();
        $schedule = $this->Schedules->get($id, [
            'contain' => [
                'Progress' => [
                    'strategy' => 'select',
                    'queryBuilder' => function ($q) {
                        return $q->order(['BudgetItems.item' =>'ASC'])->contain(['BudgetItems','UserModifieds']);
                    }
                 ]
            ],
            //'order' =>['Progress.budget_item_id' =>'DESC']
        ]);
        $budget = $this->Schedules->Budgets->find('all', [
            'conditions' => ['Budgets.id' => $schedule->budget_id],
            'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'Currencies', 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates']
        ])->first();
        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
        ])->first();
        //Historial de Aprobaciónes y Rechazos
        $this->loadModel('Approvals');
        $approvals = $this->Approvals->find('all', [
            'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.approve' => true,
             'Approvals.model_id' => $id]
        ]);
        $rejects = $this->Approvals->find('all', [
            'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.reject' => true,
             'Approvals.model_id' => $id]
        ]);
        $this->set(compact('schedule', 'approvals', 'rejects', 'budget', 'sf_building', 'units'));
        $this->set('_serialize', ['schedule']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add($budget_id = null)
    {
        $schedule = $this->Schedules->newEntity();
        $this->loadModel('BudgetItems');
        $this->loadModel('BudgetItemsSchedules');
        $budget = array();
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Schedules->UserCreateds->getUserBuildings($this->request->session()->read('Auth.User.id'));
            if (count($user_buildings) > 0) {
                $budget = $this->Schedules->Budgets->find('all', [
                	'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                ])->first();
                if (empty($budget_id) && $budget_id == null) {
                    $budget_id = $budget->id;
                } else {
                    if ($budget->id != $budget_id) {
                        $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde a la asistencia. Por favor, edite la información de usuario.');
                        return $this->redirect(['controller' => 'users', 'action' => 'index']);
                    }
                }
            } else {
                $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                return $this->redirect(['controller' => 'users', 'action' => 'index']);
            }
        } else {
            if (empty($budget_id) && $budget_id == null) {
                $buildings = $this->Schedules->Budgets->Buildings->getActiveBuildingsWithSoftlandInfo();
                $budget = $this->Schedules->Budgets->find('all', [
                	'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => $buildings[0]]
                ])->first();
                $budget_id = $budget->id;
            } else {
            	$budget = $this->Schedules->Budgets->get($budget_id, [
            		'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]]
        		]);
            }
        }
        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
        ])->first();

        if ($this->request->is('post')) {
            // guardar fecha para input en caso que falle
            $fecha_start = $this->request->data['start_date'];
            //validar que la semana no tenga planificación
            if ($this->Schedules->weekEmpty($budget_id, $this->request->data['start_date'])) {
                $this->request->data['start_date'] = new \DateTime($this->request->data['start_date']);
                $this->request->data['finish_date'] = new \DateTime($fecha_start);
                $this->request->data['finish_date'] = $this->request->data['finish_date']->modify('+4 days');
                $this->request->data['total_days'] = 5 - $this->request->data['holidays_week_quantity'];
                $this->request->data['user_created_id'] = $this->request->session()->read('Auth.User.id');
                $itemsCheckeds = \Cake\Utility\Hash::extract($this->request->data['BudgetItems'], '{n}[id=1]');
                // Validar si esta vacio el valor
                $validaItems=true;
                if(!empty($itemsCheckeds)){
                    foreach($itemsCheckeds AS $val){
                        if($val['proyected_progress_percent']===""){
                            $validaItems=false;
                        }
                    }
                }
                if($validaItems){
                    $schedule = $this->Schedules->patchEntity($schedule, $this->request->data);
                    if ($this->Schedules->save($schedule)) {
                        $schedule_id = $schedule->id;
                        foreach ($this->request->data['BudgetItems'] as $budget_item_id => $value) {
                            // Ej: data
                            // $value => [
                            //     'id' => '1',
                            //     'proyected_progress_percent' => '80',
                            //     'proyected_progress_unit' => '8'
                            // ]
                            //Reviso si está chequiado algun item
                            if ($value['id'] == "1") {
                                $progress = $this->Schedules->Progress->newEntity();
                                $progress->budget_item_id = $budget_item_id;
                                $progress->schedule_id = $schedule_id;
                                $progress->proyected_progress_percent = $value['proyected_progress_percent'];
                                $progress->overall_progress_percent = $value['overall_progress_percent'];
                                /* save user ids */
                                $progress->user_created_id = $this->request->session()->read('Auth.User.id');
                                $progress->user_modified_id = $this->request->session()->read('Auth.User.id');
                                $this->Schedules->Progress->save($progress);
                                // Guardar en budget_items_schedules
                                $budget_i_s = $this->BudgetItemsSchedules->newEntity();
                                $budget_i_s->budget_item_id = $budget_item_id;
                                $budget_i_s->schedule_id = $schedule_id;
                                $saveBudgetItemSchedule = $this->BudgetItemsSchedules->save($budget_i_s);
                                //validamos si guardo
                                if($saveBudgetItemSchedule){
                                    //Si es que guarda se debe actualizar el budget_item con el porcentaje planificado actualizado en percentage_progress, y los padres se actualizarán sumando el ultimo valor
                                    $bi = $this->BudgetItemsSchedules->BudgetItems->find('all', [
                                        'conditions' => [
                                            'BudgetItems.id' => $budget_item_id
                                        ]
                                    ])->first();
                                    $bi->percentage_proyected_progress = $value['proyected_progress_percent'];
                                    $this->BudgetItemsSchedules->BudgetItems->save($bi);
                                    if($this->BudgetItemsSchedules->BudgetItems->save($bi)){
                                        if($bi->parent_id!=null){
                                            $this->BudgetItemsSchedules->BudgetItems->updateParentsPercentageProgress($bi, $value['proyected_progress_percent']);
                                        }
                                    }
                                }
                            }
                        }
                        $this->Flash->success('Se ha guardado la planificación exitosamente');
                        return $this->redirect(['action' => 'index', $budget_id]);
                    } else {
                        // Mantener datos del formulario ingresados. Fecha y Valores
                        $this->request->data['start_date'] = $fecha_start;
                        $this->Flash->error('Ocurrió un error al guardar la planificación. Por favor, inténtelo nuevamente.');
                    }
                }else{
                    $this->request->data['start_date'] = $fecha_start;
                    $this->Flash->error('Ocurrió un error al guardar la planificación. Por favor, inténtelo nuevamente.');
                }
            } else {
                $this->Flash->error('La fecha ingresada ya cuenta con una planificación, o es una fecha inválida.');
            }
        }
        // Load budgetItems
        $bi = $this->BudgetItems->find('all', [
            'conditions' => ['budget_id' => $budget_id, 'parent_id IS' => null, 'BudgetItems.disabled' => 0],
            'contain' =>  ['Units']
            ]);
        $budget_items = array();
        foreach ($bi as $value) {
            $children = $this->BudgetItems
            ->find('children', ['for' => $value->id])
            ->find('threaded')
            // Progress ordenados descendente por avance proyectado
            ->contain([
                'Units',
                'Progress' => function ($q) {
                    return $q
                        ->order(['Progress.proyected_progress_percent' => 'DESC']);
                    }
            ])
            ->toArray();
            $budget_items[$value->id] = $value->toArray();
            $budget_items[$value->id]['children'] = $children;
        }
        $this->set(compact('schedule', 'budget_items', 'budget', 'sf_building'));
        $this->set('_serialize', ['schedule']);
        $this->set('budget_id', $budget_id);
    }

    /**
     * Edit method
     *
     * @param string|null $id Schedule id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->loadModel('BudgetItems');
        $this->loadModel('BudgetItemsSchedules');
        $this->loadModel('Progress');
        $this->loadModel('Approvals');
        if (!empty($id) && $id != null) {
	        $schedule = $this->Schedules->get($id, ['contain' => ['Budgets' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'],
	        	'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]]]]);
	        $group_id = $this->request->session()->read('Auth.User.group_id');
	        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
	            $user_buildings = $this->Schedules->UserCreateds->getUserBuildings($this->request->session()->read('Auth.User.id'));
	            if (count($user_buildings) > 0) {
	                if ($schedule->budget->building_id != $user_buildings[0]) {
	                    $this->Flash->error('La planificación seleccionada no corresponde a la Obra del Usuario');
	                    return $this->redirect(['action' => 'index', $schedule->budget_id]);
	                }
                    $now = new \DateTime('now');
                    if ($now > $schedule->start_date) {
                        $this->Flash->info('La planificación seleccionada no se puede modificar por este perfil, debido a que se encuentra en curso');
                        return $this->redirect(['action' => 'index', $schedule->budget_id]);
                    }
	            }
	        }
	        $budget = $schedule->budget;
	        //información general
            $this->loadModel('SfBuildings');
            $sf_building = $this->SfBuildings->find('all', [
                 'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
            ])->first();
	        $approvals = $this->Approvals->find('all', [
	            'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.approve' => true,
	             'Approvals.model_id' => $id]
	        ]);
	        foreach ($approvals as $approval) {
	            if ($approval['group_id'] == USR_GRP_GE_GRAL && $approval['group_id'] == USR_GRP_GE_FINAN) {
	                $this->Flash->error('La planificación seleccionada contiene un Avance de Obra Aprobado, no es posible editarla');
	                return $this->redirect(['action' => 'index']);
	            }
	        }
	        if ($this->request->is('post') || $this->request->is('put')) {
	            // guardar fecha para input en caso que falle
	            $fecha_start = $this->request->data['start_date'];
	            // validar budget_id del formulario con el del user :TODO
	            if ($this->Schedules->weekEmpty($schedule->budget_id, $this->request->data['start_date'], $schedule->id)) {
	                $this->request->data['start_date'] = new \DateTime($this->request->data['start_date']);
	                $this->request->data['finish_date'] = new \DateTime($fecha_start);
	                $this->request->data['finish_date'] = $this->request->data['finish_date']->modify('+4 days');
	                $this->request->data['total_days'] = 5 - $this->request->data['holidays_week_quantity'];
	                $this->request->data['user_modified_id'] = $this->request->session()->read('Auth.User.id');
	                $schedule = $this->Schedules->patchEntity($schedule, $this->request->data);
	               // Actualizamos la planificación
	                if ($this->Schedules->save($schedule)) {
	                    $schedule_id = $schedule->id;
	                    //Actualizamos los budget items de progresos
	                    foreach ($this->request->data['BudgetItems'] as $budget_item_id => $value) {
	                        // Reviso si fue checkiado 1 => check
	                        if ($value['id']=="1") {
	                            // Tiene progress en la planificación?
	                            if (isset($value['progress_id'])){
	                                //actualizamos el valor del progreso
	                                $progress = $this->Schedules->Progress->get($value['progress_id']);
	                                $progress->proyected_progress_percent = $value['proyected_progress_percent'];
	                                $progress->overall_progress_percent = $value['overall_progress_percent'];
	                                $progress->user_modified_id = $this->request->session()->read('Auth.User.id');
									$this->Schedules->Progress->save($progress);
                                    $bi = $this->BudgetItemsSchedules->BudgetItems->find('all', [
                                        'conditions' => [
                                            'BudgetItems.id' => $budget_item_id
                                        ]
                                    ])->first();
                                    $bi->percentage_proyected_progress = $value['proyected_progress_percent'];
                                    if($this->BudgetItemsSchedules->BudgetItems->save($bi) && $bi->parent_id!=null){
                                        $this->BudgetItemsSchedules->BudgetItems->updateParentsPercentageProgress($bi, $value['proyected_progress_percent']);
                                    }
	                            } else {
	                                // No tiene progress en la planificación
	                                // => Se crea progress
	                                $progress = $this->Schedules->Progress->newEntity();
	                                $progress->budget_item_id = $budget_item_id;
	                                $progress->schedule_id = $schedule_id;
	                                $progress->proyected_progress_percent = $value['proyected_progress_percent'];
	                                $progress->overall_progress_percent = $value['overall_progress_percent'];
	                                $this->Schedules->Progress->save($progress);
	                                /* save user ids */
	                                $progress->user_created_id = $this->request->session()->read('Auth.User.id');
	                                $progress->user_modified_id = $this->request->session()->read('Auth.User.id');
	                                $this->Schedules->Progress->save($progress);
	                                // Guardar en budget_items_schedules
	                                $budget_i_s = $this->BudgetItemsSchedules->newEntity();
	                                $budget_i_s->budget_item_id = $budget_item_id;
	                                $budget_i_s->schedule_id = $schedule_id;
	                                $this->BudgetItemsSchedules->save($budget_i_s);
                                    $bi = $this->BudgetItemsSchedules->BudgetItems->find('all', [
                                        'conditions' => [
                                            'BudgetItems.id' => $budget_item_id
                                        ]
                                    ])->first();
                                    $bi->percentage_proyected_progress = $value['proyected_progress_percent'];
                                    if($this->BudgetItemsSchedules->BudgetItems->save($bi) && $bi->parent_id!=null){
                                        $this->BudgetItemsSchedules->BudgetItems->updateParentsPercentageProgress($bi, $value['proyected_progress_percent']);
                                    }
	                            }
	                        } else { //Si no esta checkiado
	                            // Tiene progress en planificación?
	                            if (isset($value['progress_id'])) {
	                                //Borro progress de la planificación
	                                $progress = $this->Schedules->Progress->get($value['progress_id']);
	                                $this->Schedules->Progress->delete($progress);
	                                //Borrar de Budget_items_schedules
	                                $budget_i_s = $this->Schedules->BudgetItemsSchedules->find('all')
	                                    ->where(['budget_item_id' => $budget_item_id, 'schedule_id' =>$schedule_id])
	                                    ->first();
	                                if($budget_i_s){
	                                   $this->Schedules->BudgetItemsSchedules->delete($budget_i_s);
	                                }
	                                //borrar de completed_tasks
	                                $this->Schedules->CompletedTasks->deleteAll(['budget_item_id' => $budget_item_id, 'schedule_id' => $schedule_id]);
                                    $bi = $this->BudgetItemsSchedules->BudgetItems->find('all', [
                                        'conditions' => [
                                            'BudgetItems.id' => $budget_item_id
                                        ]
                                    ])->first();
                                    $bi->percentage_proyected_progress = 0;
                                    if($this->BudgetItemsSchedules->BudgetItems->save($bi) && $bi->parent_id!=null){
                                        $this->BudgetItemsSchedules->BudgetItems->updateParentsPercentageProgress($bi, 0);
                                    }
	                            }
	                        }
	                    }
	                    $this->Flash->success('La planificación ha sido actualizada.');
	                    return $this->redirect(['action' => 'index',$schedule->budget_id]);
	                } else {
	                    // Mantener datos del formulario ingresados. Fecha y Valores
	                    $this->request->data['start_date'] = $fecha_start;
	                    $this->Flash->error('La planificación no pudo ser actualizada, por favor intente de nuevo.');
	                }
	            } else {
	                $this->Flash->error('La fecha ingresada ya cuenta con una planificación, o es una fecha inválida.');
	            }
	        }
	        // saco los ids de planificaciones anteriores a la actual segun la fecha
	        $fecha_start_planifica = $schedule->start_date->format('Y-m-d 00:00:01');
	        $schedule_ids = $this->Schedules->find('list',['keyField'=>'id','valueField'=>'id'])
	            ->where(['Schedules.budget_id' => $schedule->budget_id, 'Schedules.start_date <=' => $fecha_start_planifica])
	            ->toArray();
	        //Buscamos todos los items de presupuestos y si es que poseen un progreso, lo agregamos
	        $bi = $this->Schedules->BudgetItems->find('all',[
                'conditions' => ['budget_id' => $schedule->budget_id,'parent_id IS' => null],
                'contain' =>  ['Units']
            ]);
	        $budget_items = array();
	        foreach ($bi as $value) {
	            $children = $this->BudgetItems
	            ->find('children', ['for' => $value->id])
	            ->find('threaded')
	            ->contain([
	                'Units',
	                'Progress' => function ($q) use ($schedule_ids) {
	                    // necesito progress desde esta planificacion hacia atras
	                    return $q
	                        ->where(['Progress.schedule_id IN' => $schedule_ids])
	                        ->order(['Progress.schedule_id' => 'DESC']);
	                    }
	            ])
	            ->toArray();
	            $budget_items[$value->id] = $value->toArray();
	            $budget_items[$value->id]['children'] = $children;
	        }
	        $this->set(compact('schedule', 'budget_items', 'budget', 'sf_building'));
	        $this->set('_serialize', ['schedule']);
	        $this->set('budget_id', $schedule->budget_id);
	    }  else {
            $this->Flash->error('Ocurrió un error al obtener información sobre la planificación. Por favor, inténtelo nuevamente');
            return $this->redirect(['action' => 'index']);
         }
    }

    /**
     * Delete method
     *
     * @param string|null $id Schedule id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $schedule = $this->Schedules->get($id);
        if ($this->Schedules->delete($schedule)) {
            $this->Flash->success('Planificación eliminada!.');
        } else {
            $this->Flash->error('Ocurrió un error el eliminar la planificación. Intente más tarde.');
        }
        return $this->redirect($this->referer());
    }

    /**
     * Ingresar Avance de Obra
     * @param  integer $id identificador de planificacion
     * @return void
     */
    public function progress($id = null)
    {
        $this->loadModel('BudgetItems');
        $this->loadModel('BudgetItemsSchedules');
        $this->loadModel('Progress');
        $this->loadModel('Approvals');
        if (!empty($id) && $id != null) {
            $group_id = $this->request->session()->read('Auth.User.group_id');
            if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_VISITADOR || $group_id == USR_GRP_GE_GRAL || $group_id == USR_GRP_GE_FINAN) {
                $units = $this->Schedules->Budgets->BudgetItems->Units->find('list', ['limit' => 200])->toArray();
                $schedule = $this->Schedules->get($id, [
                    'contain' => [
                        'UserCreateds',
                        'UserModifieds',
                        'BudgetItems' => [
                            'Progress' =>
                                function ($q) use ($id) {
                                    return $q
                                        ->where(['Progress.schedule_id' => $id])
                                        ->order(['Progress.modified' => 'DESC']);
                                }
                        ]
                    ]
                ]);
                $budget = $this->Schedules->Budgets->find('all', [
                    'conditions' => ['Budgets.id' => $schedule->budget_id],
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]]
                ])->first();
                $schedule_items = [];

                foreach($schedule->budget_items AS $schedule_item){
                    $schedule_items[$schedule_item->id]=$schedule_item->id;
                }
                $items = $this->Schedules->Budgets->BudgetItems->find('all', [
                    'conditions' => [
                        'BudgetItems.id NOT IN ' => $schedule_items,
                        'BudgetItems.budget_id' => $schedule->budget_id,
                        'BudgetItems.parent_id <>' => 'null'
                    ],
                    'keyField' => 'id',
                    'valueField' => 'item'
                ])->toArray();

                //información general
                $this->loadModel('SfBuildings');
                $sf_building = $this->SfBuildings->find('all', [
                     'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
                ])->first();
                //Aprobaciónes/Rechazos
                $approvals = $this->Approvals->find('all', [
                    'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.approve' => true,
                     'Approvals.model_id' => $id]
                ]);
                $rejects = $this->Approvals->find('all', [
                    'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.reject' => true,
                     'Approvals.model_id' => $id]
                ]);
                foreach ($approvals as $approval) {
                    if ($approval['group_id'] == USR_GRP_GE_GRAL || $approval['group_id'] == USR_GRP_GE_FINAN) {
                        $this->Flash->error('La planificación seleccionada contiene un Avance de Obra Aprobado, no es posible editarla');
                        return $this->redirect(['action' => 'index']);
                    }
                }
                if ($this->request->is('post') || $this->request->is('put')) {
                    // Para activar approval_btn
                    $avance_versus = 0;
                    $schedule_id = $schedule->id;
                    $updated = [];
                    //Actualizamos los progressos (avance real) para los items de la planificación
                    foreach ($this->request->data['BudgetItems'] as $budget_item_id => $value) {
                        //actualizamos el valor del progreso
                        if($value['progress_id']!=""){
                            $progress = $this->Schedules->Progress->get($value['progress_id']);
                        }else{
                            //Se debe agregar como partida no planificada
                            $progress = $this->Schedules->Progress->newEntity();
                            $progress->budget_item_id = $budget_item_id;
                            $progress->schedule_id = $schedule_id;
                            $progress->proyected_progress_percent = 0;
                            $progress->overall_progress_percent = $value['overall_progress_percent'];
                            $this->Schedules->Progress->save($progress);
                            /* save user ids */
                            $progress->user_created_id = $this->request->session()->read('Auth.User.id');
                            $progress->user_modified_id = $this->request->session()->read('Auth.User.id');
                            $this->Schedules->Progress->save($progress);
                            // Guardar en budget_items_schedules
                            $budget_i_s = $this->BudgetItemsSchedules->newEntity();
                            $budget_i_s->budget_item_id = $budget_item_id;
                            $budget_i_s->schedule_id = $schedule_id;
                            $this->BudgetItemsSchedules->save($budget_i_s);
                            $bi = $this->BudgetItemsSchedules->BudgetItems->find('all', [
                                'conditions' => [
                                    'BudgetItems.id' => $budget_item_id
                                ]
                            ])->first();
                            $bi->percentage_proyected_progress = $value['overall_progress_percent'];
                            if($this->BudgetItemsSchedules->BudgetItems->save($bi) && $bi->parent_id!=null){
                                $this->BudgetItemsSchedules->BudgetItems->updateParentsPercentageProgress($bi, $value['overall_progress_percent']);
                            }
                        }
                        // $progress->proyected_progress_percent = $value['proyected_progress_percent'];
                        $progress->overall_progress_percent = $value['overall_progress_percent'];
                        array_push($updated,$this->Schedules->Progress->save($progress));
                        // si avance real es mayor a cero y mayor o igual al proyectado sumo 1;
                        if ($value['overall_progress_percent'] > 0 && ($value['overall_progress_percent'] >= $value['proyected_progress_percent'])) {
                            $avance_versus++;
                        }
                    }
                    // Si la cantidad de progresos es igual a la cantidad de progresos que se completaron,
                    // entonces setiar campo approval_btn en schedules
                    $progresos = $this->Schedules->Progress->find('all')->where(['schedule_id' => $schedule->id])->count();
                    if (count($updated) == $progresos) {
                        if ($avance_versus > 0) {
                            if ($progresos == $avance_versus) {
                                $schedule->approval_btn = true;
                                $schedule->comment = $this->request->data['Schedules']['comment'];
                                $this->Schedules->save($schedule);
                            } else {
                                $schedule->approval_btn = false;
                                $schedule->comment = $this->request->data['Schedules']['comment'];
                                $this->Schedules->save($schedule);
                            }
                        }
                        $this->Flash->success('Avance actualizado correctamente.');
                        return $this->redirect(['action' => 'index', $schedule->budget_id]);
                    } else {
                        // Error save
                        $this->Flash->error('Ocurrió un error al actualizar el avance. Intente más tarde.');
                    }
                }
                $this->set(compact('schedule','approvals', 'rejects', 'budget', 'sf_building', 'units', 'items'));
                $this->set('_serialize', ['schedule']);
                $this->set('budget_id', $schedule->budget_id);
            } else {
                $this->Flash->error('El perfil no tiene permisos necesarios para efectuar esta acción.');
                return $this->redirect(['action' => 'index']);
            }
         } else {
            $this->Flash->error('Ocurrió un error al obtener información sobre la planificación. Por favor, inténtelo nuevamente');
            return $this->redirect(['action' => 'index']);
         }
    }

    public function approve_progress(){
        $this->autoRender = false;
        $this->loadModel('Approvals');
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if (!empty($this->request->data['schedule_id']) && $this->request->data['schedule_id'] != null) {
            if ($group_id == USR_GRP_VISITADOR) {
                $user_buildings = $this->Schedules->UserCreateds->getUserBuildings($this->request->session()->read('Auth.User.id'));
                $schedule = $this->Schedules->get($this->request->data['schedule_id'],
                    ['contain' => ['Progress', 'Budgets']]);
                if (in_array($schedule->budget->building_id, $user_buildings) && count($schedule->progress) > 0) {
                    $existing_approvals = $this->Approvals->find('all', [
                    'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.action' => $this->request->params['action'],
                      'Approvals.model_id' => $this->request->data['schedule_id'], 'Approvals.group_id' => $group_id]
                    ]);
                    if ($existing_approvals->isEmpty()) {
                        $group = $this->Schedules->UserCreateds->Groups->get($group_id);
                        $approval = $this->Approvals->newEntity();
                        $approval->model = $this->modelClass;
                        $approval->model_id = $this->request->data['schedule_id'];
                        $approval->action = $this->request->params['action'];
                        $approval->approve = true;
                        $approval->reject = false;
                        $approval->user_id = $this->request->session()->read('Auth.User.id');
                        $approval->group_id = $group_id;
                        $approval->comment = 'Aprobado por ' . $group->name . ': ' . $this->request->session()->read('Auth.User.first_name') . ' ' .
                         $this->request->session()->read('Auth.User.lastname_f');
                        if ($this->Approvals->save($approval)){
                            $this->Flash->success('El Avance de Obra se aprobó correctamente.');
                        } else {
                            $this->Flash->error('Ocurrió un Error al Aprobar el Avance de Obra.');
                        }
                    } else {
                        $this->Flash->info('El Avance de Obra ya fue aprobado por un visitador.');
                    }
                    return $this->redirect(['action' => 'view', $this->request->data['schedule_id']]);
                }  else {
                    $this->Flash->error('Ocurrió un Error al obtener información del Avance de Obra. Por favor, inténtelo nuevamente');
                    return $this->redirect(['action' => 'index']);
                }
            } elseif ($group_id == USR_GRP_GE_GRAL || $group_id == USR_GRP_GE_FINAN) {
                $schedule = $this->Schedules->get($this->request->data['schedule_id'], ['contain' => ['Progress', 'Budgets']]);
                if (count($schedule->progress) > 0) {
                    $existing_approvals = $this->Approvals->find('all', [
                    'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.action' => $this->request->params['action'],
                      'Approvals.model_id' => $this->request->data['schedule_id'], 'Approvals.group_id' => $group_id]
                    ]);

                    // Validar que no hayan planificaciones anteriores sin aprobar
                    $getPendingsSchedules = $this->Schedules->find('all', [
                        'conditions' => [
                            'start_date < ' => $schedule->start_date,
                            'budget_id' => $schedule->budget_id,
                            'progress_approved' => 0,
                        ]
                    ])->count();

                    if ($existing_approvals->count() == 0 && $getPendingsSchedules == 0) {
                        $group = $this->Schedules->UserCreateds->Groups->get($group_id);
                        $approval = $this->Approvals->newEntity();
                        $approval->model = $this->modelClass;
                        $approval->model_id = $this->request->data['schedule_id'];
                        $approval->action = $this->request->params['action'];
                        $approval->approve = true;
                        $approval->reject = false;
                        $approval->user_id = $this->request->session()->read('Auth.User.id');
                        $approval->group_id = $group_id;
                        $approval->comment = 'Aprobado por ' . $group->name . ': ' . $this->request->session()->read('Auth.User.first_name') . ' ' .
                         $this->request->session()->read('Auth.User.lastname_f');
                        if ($this->Approvals->save($approval)) {
                            $schedule->progress_approved = true;
                            $this->Schedules->save($schedule);
                            foreach($schedule->progress AS $progress){
                                $budget_item_id = $progress->budget_item_id;
                                $bi = $this->Schedules->Progress->BudgetItems->find('all', [
                                    'conditions' => [
                                        'BudgetItems.id' => $budget_item_id
                                    ]
                                ])->first();
                                $bi->percentage_overall_progress = $progress->overall_progress_percent;
                                if($this->Schedules->Progress->BudgetItems->save($bi) && $bi->parent_id!=null){
                                    $this->Schedules->Progress->BudgetItems->updateParentsPercentageProgress($bi, 0, 'percentage_overall_progress');
                                }
                            }
                            $this->Flash->success('El Avance de Obra se aprobó correctamente.');
                        } else {
                            $this->Flash->error('Ocurrió un Error al Aprobar el Avance de Obra.');
                        }
                    } else {
                        if($existing_approvals->count() != 0)
                            $this->Flash->info('El Avance de Obra ya fue aprobado por un Gerente.');
                        if($getPendingsSchedules != 0)
                            $this->Flash->info('Para aprobar esta planificación debe aprobar las semanas anteriores.');
                    }
                    return $this->redirect(['action' => 'view', $this->request->data['schedule_id']]);
                }  else {
                    $this->Flash->error('Ocurrió un Error al obtener información del Avance de Obra. Por favor, inténtelo nuevamente');
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $this->Flash->error('El perfil no tiene permisos necesarios para efectuar esta acción.');
                return $this->redirect(['action' => 'index']);
            }
        } else {
            $this->Flash->error('Ocurrió un Error al obtener información del Avance de Obra. Por favor, inténtelo nuevamente');
            return $this->redirect(['action' => 'index']);
        }

    }

    public function reject_progress() {
        $this->autoRender = false;
        $this->loadModel('Approvals');
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if (!empty($this->request->data['schedule_id']) && $this->request->data['schedule_id'] != null) {
            if ($group_id == USR_GRP_VISITADOR) {
                $user_buildings = $this->Schedules->UserCreateds->getUserBuildings($this->request->session()->read('Auth.User.id'));
                $schedule = $this->Schedules->get($this->request->data['schedule_id'], ['contain' => ['Progress', 'Budgets']]);
                if (in_array($schedule->budget->building_id, $user_buildings) && count($schedule->progress) > 0) {
                    $existing_approvals = $this->Approvals->find('all', [
                        'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.action' => $this->request->params['action'],
                          'Approvals.model_id' => $this->request->data['schedule_id'], 'Approvals.group_id' => $group_id]
                        ]);
                    if ($existing_approvals->isEmpty()) {
                        $group = $this->Schedules->UserCreateds->Groups->get($group_id);
                        $approval = $this->Approvals->newEntity();
                        $approval->model = $this->modelClass;
                        $approval->model_id = $this->request->data['schedule_id'];
                        $approval->action = $this->request->params['action'];
                        $approval->approve = false;
                        $approval->reject = true;
                        $approval->user_id = $this->request->session()->read('Auth.User.id');
                        $approval->group_id = $group_id;
                        $comment = (!empty($this->request->data['comment'])) ? $this->request->data['comment'] : 'Sin Observación';
                        $approval->comment = 'Rechazado por ' . $group->name . ' ' . $this->request->session()->read('Auth.User.first_name') . ' ' .
                         $this->request->session()->read('Auth.User.lastname_f') . ': ' . $comment;
                        if ($this->Approvals->save($approval)) {
                            $this->Flash->success('El Avance de Obra se rechazó correctamente.');
                        } else {
                            $this->Flash->error('Ocurrió un Error al Rechazar el Avance de Obra.');
                        }
                    } else {
                        $this->Flash->info('El Avance de Obra ya fue rechazado por un visitador.');
                    }
                    return $this->redirect(['action' => 'view', $this->request->data['schedule_id']]);
                } else {
                    $this->Flash->error('Ocurrió un Error al obtener información del Avance de Obra. Por favor, inténtelo nuevamente');
                    return $this->redirect(['action' => 'index']);
                }
            } elseif ($group_id == USR_GRP_GE_GRAL || $group_id == USR_GRP_GE_FINAN) {
                $schedule = $this->Schedules->get($this->request->data['schedule_id'], ['contain' => ['Progress', 'Budgets']]);
                if (count($schedule->progress) > 0) {
                     $existing_approvals = $this->Approvals->find('all', [
                    'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.action' => $this->request->params['action'],
                      'Approvals.model_id' => $this->request->data['schedule_id'], 'Approvals.group_id' => $group_id]
                    ]);
                    if ($existing_approvals->count() == 0) {
                        $group = $this->Schedules->UserCreateds->Groups->get($group_id);
                        $approval = $this->Approvals->newEntity();
                        $approval->model = $this->modelClass;
                        $approval->model_id = $this->request->data['schedule_id'];
                        $approval->action = $this->request->params['action'];
                        $approval->approve = false;
                        $approval->reject = true;
                        $approval->user_id = $this->request->session()->read('Auth.User.id');
                        $approval->group_id = $group_id;
                        $comment = (!empty($this->request->data['comment'])) ? $this->request->data['comment'] : 'Sin Observación';
                        $approval->comment = 'Rechazado por ' . $group->name . ' ' . $this->request->session()->read('Auth.User.first_name') . ' ' .
                         $this->request->session()->read('Auth.User.lastname_f') . ': ' . $comment;
                        if ($this->Approvals->save($approval)) {
                            $this->Flash->success('El Avance de Obra se rechazó correctamente.');
                        } else {
                            $this->Flash->error('Ocurrió un Error al Rechazar el Avance de Obra.');
                        }
                    } else {
                        $this->Flash->info('El Avance de Obra ya fue rechazado por un Gerente.');
                    }
                    return $this->redirect(['action' => 'view', $this->request->data['schedule_id']]);
                } else {
                    $this->Flash->error('Ocurrió un Error al obtener información del Avance de Obra. Por favor, inténtelo nuevamente');
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $this->Flash->error('El perfil no tiene permisos necesarios para efectuar esta acción.');
                return $this->redirect(['action' => 'index']);
            }
        } else {
            $this->Flash->error('Ocurrió un Error al obtener información del Avance de Obra. Por favor, inténtelo nuevamente');
            return $this->redirect(['action' => 'index']);
        }
    }
}
