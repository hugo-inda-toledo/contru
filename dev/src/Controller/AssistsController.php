<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\I18n\Date;

/**
 * Assists Controller
 *
 * @property \App\Model\Table\AssistsTable $Assists */
class AssistsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        //Cargar siempre modelo de Obras de Softland
        $this->loadModel('SfBuildings');
        $this->loadModel('SfWorkers');
        // $this->loadModel('SfWorkersStatus');
        $this->loadModel('SfWorkerBuildings');
    }

    /**
    * beforeFilter
    */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        /*
        //validar que el presupuesto no esté finalizado o sin aprobar
        $current_action = $this->request->params['action'];
        $action_views = ['add', 'edit', 'delete', 'approve', 'reject', 'add_coment', 'assist_month_detail', 'salaries_report'];
        if (in_array($current_action, $action_views)) {
            $current_state = null;
            debug($this->request);
            debug($this->request->params['pass']);
            die();
            if (count($this->request->params['pass']) > 0) {
                $current_state = $this->Assists->Budgets->current_budget_state($this->request->params['pass'][0]);
            } elseif (count($this->request->query) > 0) {
                $budget = $this->Assists->Budgets->find('all', ['conditions' => ['Budgets.building_id' => $this->request->query['building_id']]])->first();
                $current_state = $this->Assists->Budgets->current_budget_state($budget->id);

            }
            debug($budget);
                debug($current_state);
                die();
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
        }*/
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        //asistencia con aprobación y fecha, resumen de datos
        $first_day_of_month = new \DateTime("first day of this month");
        $last_day_of_month = new \DateTime("last day of this month");
        $budget = null;
        $assists_data = array();
        $buildings = null;
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if (!empty($this->request->query)) {
            if (!empty($this->request->query['months'])) {
                $date_filter = explode('_', $this->request->query['months']);
                $first_day_of_month = new \DateTime($date_filter[0] . '-' . $date_filter[1] . '-1');
                $last_day_of_month = new \DateTime($date_filter[0] . '-' . $date_filter[1] . '-1');
                $last_day_of_month = $last_day_of_month->modify('last day of this month');
            }
        }
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Assists->Users->getUserBuildings($this->request->session()->read('Auth.User.id'));
            if (count($user_buildings) > 0) {
                $budget = $this->Assists->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                ])->first();
            } else {
                $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                return $this->redirect(['controller' => 'users', 'action' => 'index']);
            }
            if (!empty($budget->building_id) && $budget != null) {
                if ($budget->building_id != $user_buildings[0]) {
                    $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde a la asistencia. Por favor, edite la información de usuario.');
                    return $this->redirect(['controller' => 'users', 'action' => 'index']);
                }
            } else {
                $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
                return $this->redirect(['action' => 'index']);
            }
        } else { //los demás perfiles
            $buildings = $this->Assists->Budgets->Buildings->getActiveBuildingsWithSoftlandInfo();
            $last_building = $this->request->session()->read('Config.last_building');
            if (!empty($this->request->query)) {
                if (!empty($this->request->query['building_id'])) {
                    $budget = $this->Assists->Budgets->find('all', [
                        'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                        'conditions' => ['Budgets.building_id' => $this->request->query['building_id']]
                    ])->first();
                }
            } else {
                if(!empty($last_building)) {
                    $budget = $this->Assists->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => $last_building]
                ])->first();
                } else {
                    $budget = $this->Assists->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => key($buildings)]
                ])->first();
                }

            }
        }
        $assists_data = [];
        $months = [];
        $sf_building=[];
        if(!empty($budget)){
            //información general
            $this->loadModel('SfBuildings');
            $sf_building = $this->SfBuildings->find('all', [
                 'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
            ])->first();
            $now = Time::now();
            $this->loadModel('Approvals');
            //genera lista de meses de asistencia de ppto.
            $budget_date_created = new \DateTime($budget->created->format('d-m-Y'));
            $budget_date_created_months = new \DateTime($budget->created->format('d-m-Y'));
            //si el primer día del mes es menor a la fecha de creación del ppto, usar esa fecha
            ($first_day_of_month < $budget_date_created) ? $first_day_of_month = $budget_date_created : '';
            while ($first_day_of_month <= $last_day_of_month) {
                $assist = $this->Assists->find('all', [
                    'conditions' => ['Assists.budget_id' => $budget->id, 'Assists.assistance_date >=' => $first_day_of_month->format('Y-m-d 00:00:01'),
                     'Assists.assistance_date <=' => $first_day_of_month->format('Y-m-d 23:59:59')],
                    ]);
                if ($first_day_of_month > $now) {
                    break;
                }
                if (!$assist->isEmpty()) {
                    $assists_data[$first_day_of_month->format('Y-m-d')]['budget_id'] = $assist->first()->budget_id;
                    $assists_data[$first_day_of_month->format('Y-m-d')]['assistance_date'] = $assist->first()->assistance_date;
                    $assists_data[$first_day_of_month->format('Y-m-d')]['total_workers'] = $assist->count();
                    $assists_data[$first_day_of_month->format('Y-m-d')]['status'] = 'Ingresada';
                    $approval = $this->Approvals->find('all', [
                        'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.approve' => true, 'Approvals.model_id' => $assist->first()->budget_id,
                         'Approvals.records_date' => $assist->first()->assistance_date->format('Y-m-d'), 'Approvals.group_id' => USR_GRP_JEFE_RRHH]
                    ])->first();
                    $assists_data[$first_day_of_month->format('Y-m-d')]['approval'] = $approval;
                    $reject = $this->Approvals->find('all', [
                        'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.reject' => true, 'Approvals.model_id' => $assist->first()->budget_id,
                         'Approvals.records_date' => $assist->first()->assistance_date->format('Y-m-d'), 'Approvals.group_id' => USR_GRP_JEFE_RRHH]
                    ])->first();
                    $assists_data[$first_day_of_month->format('Y-m-d')]['reject'] = $reject;
                } else {
                    if ($first_day_of_month->format('w') != 6 && $first_day_of_month->format('w') != 0) {
                        $assists_data[$first_day_of_month->format('Y-m-d')]['status'] = 'Sin ingresar';
                    }
                }
                $first_day_of_month->modify('+1 day');
            }
            $months = $this->Assists->Users->BuildingsUsers->Buildings->Budgets->getListMonthsBudget($budget_date_created_months, $budget->duration);
        }
        $this->set('assists_data', $assists_data);
        $this->set(compact('budget', 'buildings', 'months', 'sf_building'));
    }

    /**
     * View method
     *
     * @param string|null $id Assist id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($budget_id = null, $assistance_date = null)
    {
        if (!empty($assistance_date) && $assistance_date != null) {
            $assistance_date = new Time($assistance_date);
            $morning = $assistance_date->format('Y-m-d 00:00:01');
            $evening = $assistance_date->format('Y-m-d 23:59:59');
        } else {
            $now = Time::now();
            $morning = $now->format('Y-m-d 00:00:01');
            $evening = $now->format('Y-m-d 23:59:59');
        }
        $budget = $this->Assists->Budgets->get($budget_id, [
            'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates',
             'Assists' => ['Workers', 'AssistTypes', 'conditions' => ['Assists.assistance_date >=' => $morning, 'Assists.assistance_date <=' => $evening]], 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]]
        ]);
        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
        ])->first();
        $workers = null;
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Assists->Users->getUserBuildings($this->request->session()->read('Auth.User.id'));
            $budget_id = $budget->id;
            if (!empty($budget_id) && $budget_id != null) {
                if ($budget->building_id != $user_buildings[0]) {
                    $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde a la asistencia. Por favor, edite la información de usuario.');
                    return $this->redirect(['controller' => 'users', 'action' => 'index']);
                }
                // $workers = $this->Assists->Workers->getSoftlandWorkersByBuilding($user_buildings[0]);
            } else {
                $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
                return $this->redirect(['action' => 'index']);
            }
        } else {
            // $workers = $this->Assists->Workers->getSoftlandWorkersByBuilding($budget->building_id);
        }
        (empty($assistance_date) && $assistance_date == null) ? $assistance_date = $budget->assists[0]->assistance_date : '';
        $assists = $this->Assists->assistsOrderByWorkerSoftlandId($budget->assists);
        $workers = $this->Assists->Workers->getWorkersByList($budget->building_id, "'".implode("','",array_keys($assists))."'");
        //filtro vigencia entre fechas de vigencia contrato trabajador
        foreach($workers as $k => $worker) {
            if(($worker['vigDesde'] > $assistance_date) || ($worker['vigHasta'] < $assistance_date) || !isset($assists[$worker['ficha']])) {
                unset($workers[$k]);
            }
        }
        $assist_types = $this->Assists->AssistTypes->find('list')
            ->where(function ($exp, $q) {
                return $exp->notLike('name', '%Asistencia%');
            })->toArray();
        $this->loadModel('Approvals');
        $approval = $this->Approvals->find('all', [
            'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.approve' => true, 'Approvals.model_id' => $budget->id,
             'Approvals.records_date' => $assistance_date->format('Y-m-d'), 'Approvals.group_id' => USR_GRP_JEFE_RRHH]
        ])->first();
        $reject = $this->Approvals->find('all', [
            'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.reject' => true, 'Approvals.model_id' => $budget->id,
             'Approvals.records_date' => $assistance_date->format('Y-m-d'), 'Approvals.group_id' => USR_GRP_JEFE_RRHH]
        ])->first();
        // pr($workers);
        // die();
        $this->set(compact('budget', 'sf_building', 'budget_id', 'assists', 'workers', 'assist_types', 'assistance_date', 'approval', 'reject'));
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add($budget_id = null, $assistance_date = null)
    {
        if (!empty($assistance_date) && $assistance_date != null) {
            $assistance_date = new Time($assistance_date);
            $morning = $assistance_date->format('Y-m-d 00:00:01');
            $evening = $assistance_date->format('Y-m-d 23:59:59');
        } else {
            $assistance_date = Time::now();
            $morning = $assistance_date->format('Y-m-d 00:00:01');
            $evening = $assistance_date->format('Y-m-d 23:59:59');
        }
        if ($assistance_date->format('w') == 6 && $assistance_date->format('w') == 0) {
            $this->Flash->info('La asistencia no se ingresa los Días Sábados o Domingos.');
            return $this->redirect(['action' => 'index']);
        }
        $budget = null;
        $workers = null;
        $assist = $this->Assists->newEntity();
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Assists->Users->getUserBuildings($this->request->session()->read('Auth.User.id'));
            if (count($user_buildings) > 0) {
                $budget = $this->Assists->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }],
                     'Assists' => ['conditions' => ['Assists.assistance_date >=' => $morning, 'Assists.assistance_date <=' => $evening]]],
                    'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                ])->first();
                if ($budget->id != $budget_id) {
                    $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde a la asistencia. Por favor, edite la información de usuario.');
                    return $this->redirect(['controller' => 'users', 'action' => 'index']);
                }
                if (count($budget->assists) > 0) {
                    $this->Flash->info('Ya se ingresó la asistencia hoy, podrá modificarla.');
                    return $this->redirect(['action' => 'edit', $budget->id]);
                }
            } else {
                $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                return $this->redirect(['controller' => 'users', 'action' => 'index']);
            }
        } else {
            $budget = $this->Assists->Budgets->get($budget_id, [
                'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }],
                 'Assists' => ['Workers', 'AssistTypes', 'conditions' => ['Assists.assistance_date >=' => $morning, 'Assists.assistance_date <=' => $evening]]]
            ]);
        }
        if ($this->request->is('post')) {
            $totalWorkersToSave = count($this->request->data['Worker']);
            $totalWorkersSaveds = 0;
            if ($totalWorkersToSave > 0) {
                foreach ($this->request->data['Worker'] as $worker_softland_id => $worker) {
                    //check existe worker
                    //
                    $existing_worker = $this->Assists->Workers->find('all', ['conditions' => ['Workers.softland_id' => $worker_softland_id]])->first();
                    if (is_null($existing_worker)) {
                        $new_worker = $this->Assists->Workers->newEntity();
                        $new_worker->softland_id = $worker_softland_id;
                        if ($this->Assists->Workers->save($new_worker)) {
                            (!$assist->isNew()) ? $assist = $this->Assists->newEntity() : '';
                            $assist->budget_id = $this->request->data['budget_id'];
                            $assist->worker_id = $new_worker->id;
                            $assist->assistance_date = $morning;
                            $assist->overtime = 0;
                            $assist->delay = 0;
                            $assist->user_created_id = $this->request->session()->read('Auth.User.id');
                            if ($this->Assists->save($assist)) {
                                $this->loadModel('AssistsAssistTypes');
                                if (!empty($worker['all_day']) && $worker['all_day']) {
                                    $assists_type_info = $this->AssistsAssistTypes->newEntity();
                                    $assists_type_info->assist_id = $assist->id;
                                    (!empty($worker['assistance'])) ? $assists_type_info->assist_type_id = $worker['assistance'] : $assists_type_info->assist_type_id = $worker['assist_type_id'];
                                    if (!empty($worker['assistance'])) {
                                        $assists_type_info->hours = 9;
                                    } else {
                                        switch ($worker['assist_type_id']) {
                                            case 2:
                                                $assists_type_info->hours = 0;
                                                break;
                                            case 6:
                                                $assists_type_info->hours = 0;
                                                break;
                                             default:
                                                $assists_type_info->hours = 9;
                                                break;
                                        }
                                    }
                                    if ($this->AssistsAssistTypes->save($assists_type_info)) {
                                            // $this->Flash->success('La Asistencia fue guardada correctamente');
                                            $totalWorkersSaveds++;
                                    } else {
                                        // $this->Flash->error('Ocurrió un error al guardar la Asistencia. Por favor, inténtelo nuevamente.');
                                        //todo: clean cascada
                                    }
                                } else {
                                    $totalHalfDay = count($worker);
                                    $totalHalfDaySaveds = 0;
                                    //sum of hours
                                    foreach ($worker as $data_hour) {
                                        $assists_type_info = $this->AssistsAssistTypes->newEntity();
                                        $assists_type_info->assist_id = $assist->id;
                                        (!empty($data_hour['assistance'])) ? $assists_type_info->assist_type_id = $data_hour['assistance'] :
                                         $assists_type_info->assist_type_id = $data_hour['assist_type_id'];
                                        $assists_type_info->hours = $data_hour['hours'];
                                        if ($this->AssistsAssistTypes->save($assists_type_info)) {
                                            // $this->Flash->success('La Asistencia fue guardada correctamente');
                                            $totalHalfDaySaveds++;
                                        } else {
                                            // $this->Flash->error('Ocurrió un error al guardar la Asistencia. Por favor, inténtelo nuevamente.');
                                            //todo: clean cascada
                                        }
                                    }
                                    if($totalHalfDay == $totalHalfDaySaveds){
                                        $totalWorkersSaveds++;
                                    }
                                }
                            } else {
                                //error guardar assist
                                $this->Flash->error('La asistencia no ha sido guardada, intentalo nuevamente');
                            }
                        }
                    } else {
                        //use worker_id
                        (!$assist->isNew()) ? $assist = $this->Assists->newEntity() : '';
                        $assist->budget_id = $this->request->data['budget_id'];
                        $assist->worker_id = $existing_worker->id;
                        $assist->assistance_date = $morning;
                        $assist->overtime = 0;
                        $assist->delay = 0;
                        if ($this->Assists->save($assist)) {
                            $this->loadModel('AssistsAssistTypes');
                            if (!empty($worker['all_day']) && $worker['all_day']) {
                                $assists_type_info = $this->AssistsAssistTypes->newEntity();
                                $assists_type_info->assist_id = $assist->id;
                                (!empty($worker['assistance'])) ? $assists_type_info->assist_type_id = $worker['assistance'] : $assists_type_info->assist_type_id = $worker['assist_type_id'];
                                if (!empty($worker['assistance'])) {
                                    $assists_type_info->hours = 9;
                                } else {
                                    switch ($worker['assist_type_id']) {
                                        case 2:
                                            $assists_type_info->hours = 0;
                                            break;
                                        case 6:
                                            $assists_type_info->hours = 0;
                                            break;
                                         default:
                                            $assists_type_info->hours = 9;
                                            break;
                                    }
                                }
                                if ($this->AssistsAssistTypes->save($assists_type_info)) {
                                    // $this->Flash->success('La Asistencia fue guardada correctamente');
                                    $totalWorkersSaveds++;
                                } else {
                                    // $this->Flash->error('Ocurrió un error al guardar la Asistencia. Por favor, inténtelo nuevamente.');
                                    //todo: clean cascada
                                }
                            } else {
                                $totalHalfDay = count($worker);
                                $totalHalfDaySaveds = 0;
                                //sum of hours
                                foreach ($worker as $data_hour) {
                                    $assists_type_info = $this->AssistsAssistTypes->newEntity();
                                    $assists_type_info->assist_id = $assist->id;
                                    (!empty($data_hour['assistance'])) ? $assists_type_info->assist_type_id = $data_hour['assistance'] :
                                     $assists_type_info->assist_type_id = $data_hour['assist_type_id'];
                                    $assists_type_info->hours = $data_hour['hours'];
                                    if ($this->AssistsAssistTypes->save($assists_type_info)) {
                                        // $this->Flash->success('La Asistencia fue guardada correctamente');
                                        $totalHalfDaySaveds++;
                                    } else {
                                        // $this->Flash->error('Ocurrió un error al guardar la Asistencia. Por favor, inténtelo nuevamente.');
                                        //todo: clean cascada
                                    }
                                }
                                if($totalHalfDay == $totalHalfDaySaveds){
                                    $totalWorkersSaveds++;
                                }
                            }
                        } else {
                            $this->Flash->error('Ocurrió un error al guardar la Asistencia. Por favor, inténtelo nuevamente.');
                        }
                    }
                }
                if($totalWorkersSaveds == $totalWorkersToSave){
                    $this->Flash->success('La Asistencia fue guardada correctamente');
                }else{
                    $this->Flash->error('Ocurrió un error al guardar la Asistencia. Por favor, inténtelo nuevamente.');
                }
            }
            return ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) ?
             $this->redirect(['action' => 'index', '?' => ['months' => $assistance_date->format('Y_m')]]) :
              $this->redirect(['action' => 'index', '?' => ['building_id' => $budget->building_id, 'months' => $assistance_date->format('Y_m')]]);
        }
        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', ['conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]])->first();
        $budget_id = $budget->id;
        // $workers = $this->Assists->Workers->getSoftlandWorkersByBuilding($budget->building_id);
        $workers = $this->Assists->Workers->getSoftlandWorkersByBuildingTest($budget->building_id);
        //filtro vigencia entre fechas de vigencia contrato trabajador
        foreach($workers as $k => $worker) {
            if(($worker['vigDesde'] > $assistance_date->format('Y-m-d H:i:s')) || ($worker['vigHasta'] < $assistance_date->format('Y-m-d H:i:s'))) {
            // if(($worker['vigHasta'] != "9999-12-01 00:00:00") && ($worker['vigHasta'] < $assistance_date->format('Y-m-d H:i:s'))) {
                unset($workers[$k]);
            }
        }
        // pr(count($workers));
        $assist_types = $this->Assists->AssistTypes->find('list')
            ->where(function ($exp, $q) {
                return $exp->notLike('name', '%Asistencia%');
            });
        $this->set(compact('budget', 'sf_building', 'assist', 'budget_id', 'workers', 'assist_types', 'assistance_date'));
    }

    /**
     * Edit method
     *
     * @param string|null $budget_id Budget id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($budget_id = null, $assistance_date = null)
    {
        //Editar asistencias de otra fecha
        if (!empty($assistance_date) && $assistance_date != null) {
            $assistance_date = new Time($assistance_date);
            $morning = $assistance_date->format('Y-m-d 00:00:01');
            $evening = $assistance_date->format('Y-m-d 23:59:59');
        } else {
            $this->Flash->error('La fecha de la asistencia correcta no es válida. Por favor, inténtelo nuevamente.');
            return $this->redirect(['action' => 'index']);
        }
        $budget = null;
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Assists->Users->getUserBuildings($this->request->session()->read('Auth.User.id'));
            if (count($user_buildings) > 0) {
                $budget = $this->Assists->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                        }],
                        'Assists' => [
                            'Workers',
                            'AssistTypes',
                            'conditions' => [
                                'Assists.assistance_date >=' => $morning, 'Assists.assistance_date <=' => $evening
                            ],
                            'order' => [
                                'Assists.id', 'ASC'
                            ]
                        ]
                    ],
                    'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                ])->first();
                if ($budget->building_id != $user_buildings[0]) {
                    $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde a la asistencia. Por favor, edite la información de usuario.');
                    return $this->redirect(['controller' => 'users', 'action' => 'index']);
                }
            } else {
                $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
                return $this->redirect(['action' => 'index']);
            }
        } else {
            $budget = $this->Assists->Budgets->get($budget_id, [
                'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                        }],
                'Assists' => [
                    'Workers',
                    'AssistTypes',
                    'conditions' => [
                        'Assists.assistance_date >=' => $morning, 'Assists.assistance_date <=' => $evening
                    ]
                ]]
            ]);
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $totalWorkersToSave = count($this->request->data['Assists']);
            $totalWorkersSaveds = 0;
            if ($totalWorkersToSave > 0) {
                foreach ($this->request->data['Assists'] as $data_assist_id => $data_assist) {
                    $assist = $this->Assists->get($data_assist_id);
                    $assist->budget_id = $this->request->data['budget_id'];
                    $assist->worker_id = $data_assist['worker_id'];
                    $assist->assistance_date = $morning;
                    $assist->overtime = (!empty($data_assist['overtime'])) ? $data_assist['overtime'] : 0;
                    $assist->delay = (!empty($data_assist['delay'])) ? $data_assist['delay'] : 0;
                    $assist->user_modified_id = $this->request->session()->read('Auth.User.id');
                    if ($this->Assists->save($assist)) {
                        $this->loadModel('AssistsAssistTypes');
                        $this->AssistsAssistTypes->deleteAll(['AssistsAssistTypes.assist_id' => $data_assist_id]);
                        if (!empty($data_assist['all_day']) && $data_assist['all_day']) {
                            $assists_type_info = $this->AssistsAssistTypes->newEntity();
                            $assists_type_info->assist_id = $data_assist_id;
                            (!empty($data_assist['assistance'])) ? $assists_type_info->assist_type_id = $data_assist['assistance'] : $assists_type_info->assist_type_id = $data_assist['assist_type_id'];
                            if (!empty($data_assist['assistance'])) {
                                $assists_type_info->hours = 9;
                            } else {
                                switch ($data_assist['assist_type_id']) {
                                    case 2:
                                        $assists_type_info->hours = 0;
                                        break;
                                    case 6:
                                        $assists_type_info->hours = 0;
                                        break;
                                     default:
                                        $assists_type_info->hours = 9;
                                        break;
                                }
                            }
                            if ($this->AssistsAssistTypes->save($assists_type_info)) {
                                $totalWorkersSaveds++;
                                // $this->Flash->success('La Asistencia fue guardada correctamente');
                            } else {
                                // $this->Flash->error('Ocurrió un error al guardar la Asistencia. Por favor, inténtelo nuevamente.');
                                //todo: clean cascada
                            }
                        } else {
                            ksort($data_assist['half_day']);
                            $totalHalfDay = count($data_assist['half_day']);
                            $totalHalfDaySaveds = 0;
                            //sum of hours
                            foreach ($data_assist['half_day'] as $data_hour) {
                                $assists_type_info = $this->AssistsAssistTypes->newEntity();
                                $assists_type_info->assist_id = $data_assist_id;
                                if(isset($data_hour['assistance']) && !empty($data_hour['assistance'])){
                                    $assists_type_info->assist_type_id = $data_hour['assistance'];
                                }else{
                                    if(!isset($data_hour['assist_type_id'])){
                                        $data_hour['assist_type_id'] = $data_assist['assist_type_id'];
                                    }
                                    $assists_type_info->assist_type_id = $data_hour['assist_type_id'];
                                }
                                $assists_type_info->hours = $data_hour['hours'];
                                if ($this->AssistsAssistTypes->save($assists_type_info)) {
                                    // $this->Flash->success('La Asistencia fue guardada correctamente');
                                    $totalHalfDaySaveds++;
                                } else {
                                    // $this->Flash->error('Ocurrió un error al guardar la Asistencia. Por favor, inténtelo nuevamente.');
                                    //todo: clean cascada
                                }
                            }
                            if($totalHalfDaySaveds==$totalHalfDay){
                                $totalWorkersSaveds++;
                            }
                        }
                    } else {
                        $this->Flash->error('Ocurrió un error al guardar la Asistencia. Por favor, inténtelo nuevamente.');
                        //todo: clean cascada
                    }
                }
                if($totalWorkersSaveds == $totalWorkersToSave){
                    $this->Flash->success('La Asistencia fue guardada correctamente');
                }else{
                    $this->Flash->error('Ocurrió un error al guardar la Asistencia. Por favor, inténtelo nuevamente.');
                }
            } else {
                $this->Flash->error('Ocurrió un error al guardar la Asistencia. Por favor, inténtelo nuevamente.');
            }
            return ( $this->redirect(['action' => 'view', $budget->id, $assistance_date->format('Y-m-d')]) );
        }
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', ['conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]])->first();
        (empty($assistance_date) && $assistance_date == null) ? $assistance_date = $budget->assists[0]->assistance_date : '';
        $assists = $this->Assists->assistsOrderByWorkerSoftlandId($budget->assists);
        // $workers = $this->Assists->Workers->getSoftlandWorkersByBuilding($budget->building_id);
        // $workers = $this->Assists->Workers->getSoftlandWorkersByBuildingTest($budget->building_id);
        // Se deben obtener los trabajadores que fueron ese día, no los actuales de la obra
        $workers = $this->Assists->Workers->getWorkersByList($budget->building_id, "'".implode("','",array_keys($assists))."'");

        foreach($workers as $k => $worker) {
            if(($worker['vigDesde'] > $assistance_date) || ($worker['vigHasta'] < $assistance_date) || !isset($assists[$worker['ficha']])) {
                unset($workers[$k]);
            }
        }
        $assist_types = $this->Assists->AssistTypes->find('list')
            ->where(function ($exp, $q) {
                return $exp->notLike('name', '%Asistencia%');
            })->toArray();
        $this->loadModel('Approvals');
        $reject = $this->Approvals->find('all', [
            'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.reject' => true, 'Approvals.model_id' => $budget->id,
             'Approvals.records_date' => $assistance_date->format('Y-m-d'), 'Approvals.group_id' => USR_GRP_JEFE_RRHH]
        ])->first();
        $this->set(compact('budget', 'sf_building', 'budget_id' ,'assists', 'workers', 'assist_types', 'assistance_date', 'reject'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Assist id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $assist = $this->Assists->get($id);
        if ($this->Assists->delete($assist)) {
            $this->Flash->success('La asistencia ha sido eliminada.');
        } else {
            $this->Flash->error('La asistencia no ha sido eliminada, intentalo nuevamente.');
        }
        return $this->redirect(['action' => 'index']);
    }

     /**
     * Agregar comentario a asistencia para entender la aprobación, rechazo, u error de ingreso.
     * @param string $id identificador trato
     * @author [name] <[<email address>]>
     */
    public function add_comment($budget_id = null, $assistance_date = null)
    {
        //verificar obra tiene presupuesto

        //verificar perfil de usuario

        //buscar asistencia

        //aprobar en cascada
    }

    /**
     * Aprobar asistencia, debe soportar del Jefe RRHH
     * @param  string $id identificador del asistencia
     * @return bool     aprobación
     * @author [name] <[<email address>]>
     */
    public function approve()
    {
        $this->autoRender = false;
        $this->loadModel('Approvals');
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if (!empty($this->request->data['budget_id']) && $this->request->data['budget_id'] != null) {
            if ($group_id == USR_GRP_JEFE_RRHH || $group_id == USR_GRP_GE_GRAL) {
                $assistance_date = new Time($this->request->data['assistance_date']);
                $morning = $assistance_date->format('Y-m-d 00:00:01');
                $evening = $assistance_date->format('Y-m-d 23:59:59');
                $budget = $this->Assists->Budgets->get($this->request->data['budget_id'],
                    ['contain' => ['BudgetApprovals', 'Assists' => ['conditions' => ['Assists.assistance_date >=' => $morning, 'Assists.assistance_date <=' => $evening]]]]);
                // debug($budget->toArray()); //die();
                foreach ($budget->budget_approvals as $budget_approval) {
                    if ($budget_approval['budget_state_id'] == 6) {
                        $this->Flash->info('El presupuesto de la obra se encuentra cerrado');
                        return $this->redirect(['action' => 'index']);
                    }
                }
                if (count($budget->assists) > 0) {
                    $existing_approvals = $this->Approvals->find('all', [
                        'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.action' => $this->request->params['action'],
                          'Approvals.model_id' => $this->request->data['budget_id'], 'Approvals.records_date' => $this->request->data['assistance_date'], 'Approvals.group_id' => $group_id]
                        ]);
                    if ($existing_approvals->count() == 0) {
                        $approval = $this->Approvals->newEntity();
                        $approval->model = $this->modelClass;
                        $approval->action = $this->request->params['action'];
                        $approval->model_id = $this->request->data['budget_id'];
                        $approval->records_date = $this->request->data['assistance_date'];
                        $approval->approve = true;
                        $approval->reject = false;
                        $approval->user_id = $this->request->session()->read('Auth.User.id');
                        $approval->group_id = $group_id;
                        $approval->comment = 'Apobado por Jefe Recursos Humanos: ' . $this->request->session()->read('Auth.User.first_name') . ' ' . $this->request->session()->read('Auth.User.lastname_f');
                        if ($this->Approvals->save($approval)){
                            $this->Flash->success('La Asistencia se aprobó correctamente');
                        } else {
                            $this->Flash->error('Ocurrió un Error al Aprobar la asistencia.');
                        }
                    } else {
                        $this->Flash->info('La Asistencia ya fue aprobada por un Jefe de Recursos Humanos.');
                    }
                }
                return $this->redirect(['action' => 'index', '?' => ['building_id' => $budget->building_id, 'months' => $assistance_date->format('Y_m')]]);
            }
        }
    }

    /**
     * Rechazar asistencia, se podrá rechazar el asistencia, se deberá editar hasta que sea aprobada,
     * sólo aprobada será parte de los cálculos de RRHH
     * @param  string $id identificador asistencia
     * @return bool     rechazo
     * @author [name] <[<email address>]>
     */
    public function reject($budget_id = null, $assistance_date = null)
    {
        $this->autoRender = false;
        $this->loadModel('Approvals');
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if (!empty($this->request->data['budget_id']) && $this->request->data['budget_id'] != null) {
            if ($group_id == USR_GRP_JEFE_RRHH || $group_id == USR_GRP_GE_GRAL) {
                $assistance_date = new Time($this->request->data['assistance_date']);
                $morning = $assistance_date->format('Y-m-d 00:00:01');
                $evening = $assistance_date->format('Y-m-d 23:59:59');
                $budget = $this->Assists->Budgets->get($this->request->data['budget_id'],
                    ['contain' => ['BudgetApprovals', 'Assists' => ['conditions' => ['Assists.assistance_date >=' => $morning, 'Assists.assistance_date <=' => $evening]]]]);
                foreach ($budget->budget_approvals as $budget_approval) {
                    if ($budget_approval['budget_state_id'] == 6) {
                        $this->Flash->info('El presupuesto de la obra se encuentra cerrado');
                        return $this->redirect(['action' => 'index']);
                    }
                }
                if (count($budget->assists) > 0) {
                    $existing_approvals = $this->Approvals->find('all', [
                        'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.action' => $this->request->params['action'],
                          'Approvals.model_id' => $this->request->data['budget_id'], 'Approvals.records_date' => $this->request->data['assistance_date'], 'Approvals.group_id' => $group_id]
                        ]);
                    $existing_rejects = $this->Approvals->find('all', [
                        'conditions' => ['Approvals.model' => $this->modelClass, 'Approvals.action' => $this->request->params['action'],
                          'Approvals.model_id' => $this->request->data['budget_id'], 'Approvals.records_date' => $this->request->data['assistance_date'], 'Approvals.group_id' => $group_id]
                        ]);
                    if ($existing_approvals->count() == 0 && $existing_rejects->count() == 0) {
                        $approval = $this->Approvals->newEntity();
                        $approval->model = $this->modelClass;
                        $approval->action = $this->request->params['action'];
                        $approval->model_id = $this->request->data['budget_id'];
                        $approval->records_date = $this->request->data['assistance_date'];
                        $approval->approve = false;
                        $approval->reject = true;
                        $approval->user_id = $this->request->session()->read('Auth.User.id');
                        $approval->group_id = $group_id;
                        $approval->comment = $this->request->data['Approval']['comment'];
                        if ($this->Approvals->save($approval)){
                            $this->Flash->success('La Asistencia se rechazó correctamente');
                        } else {
                            $this->Flash->error('Ocurrió un Error al rechazar la asistencia.');
                        }
                    } else {
                        $this->Flash->info('La Asistencia ya fue aprobada o rechazada por un Jefe de Recursos Humanos.');
                    }
                }
            }else{
                $this->Flash->error('No tiene permisos para rechazar.');
            }
        }
        return $this->redirect($this->referer());
    }

    /**
     * Función que calcula la información de la asistencia mensual
     */
    public function assist_month_detail()
    {
        $first_day_of_month = new \DateTime("first day of this month");
        $last_day_of_month = new \DateTime("last day of this month");
        $budget = null;
        $workers_assists_data = array();
        $buildings = null;
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if (!empty($this->request->query)) {
            if (!empty($this->request->query['months'])) {
                $date_filter = explode('_', $this->request->query['months']);
                $first_day_of_month = new \DateTime($date_filter[0] . '-' . $date_filter[1] . '-1');
                $last_day_of_month = new \DateTime($date_filter[0] . '-' . $date_filter[1] . '-1');
                $last_day_of_month = $last_day_of_month->modify('last day of this month');
            }
        }
        $assistance_date = new \DateTime($first_day_of_month->format('Y-m-d'));
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Assists->Users->getUserBuildings($this->request->session()->read('Auth.User.id'));
            if (count($user_buildings) > 0) {
                $budget = $this->Assists->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                ])->first();
            } else {
                $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                return $this->redirect(['controller' => 'users', 'action' => 'index']);
            }
            if (!empty($budget->building_id) && $budget != null) {
                if ($budget->building_id != $user_buildings[0]) {
                    $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde a la asistencia. Por favor, edite la información de usuario.');
                    return $this->redirect(['controller' => 'users', 'action' => 'index']);
                }
            } else {
                $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
                return $this->redirect(['action' => 'index']);
            }
        } else { //los demás perfiles
            $buildings = $this->Assists->Budgets->Buildings->getActiveBuildingsWithSoftlandInfo();
            $last_building = $this->request->session()->read('Config.last_building');
            if (!empty($this->request->query)) {
                if (!empty($this->request->query['building_id'])) {
                    $budget = $this->Assists->Budgets->find('all', [
                        'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                        'conditions' => ['Budgets.building_id' => $this->request->query['building_id']]
                    ])->first();
                }
            } else {
                 if(!empty($last_building)) {
                    $budget = $this->Assists->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => $last_building]
                ])->first();
                } else {
                    $budget = $this->Assists->Budgets->find('all', [
                        'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                               return $q->limit(1);
                        }]],
                        'conditions' => ['Budgets.building_id' => key($buildings)]
                    ])->first();
                }
            }
        }
        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
        ])->first();
        $workers = $this->Assists->Workers->getSoftlandWorkersByBuildingWithWorkerId($budget->building_id);
        //genera lista de meses de asistencia de ppto.
        $budget_date_created = new \DateTime($budget->created->format('d-m-Y'));
        $budget_date_created_months = new \DateTime($budget->created->format('d-m-Y'));
        //si el primer día del mes es menor a la fecha de creación del ppto, usar esa fecha
        ($first_day_of_month < $budget_date_created) ? $first_day_of_month = $budget_date_created : '';
        $month_days = $this->Assists->getMonthDays($assistance_date->format('Y-m-d'));
        $assist_month_data = $this->Assists->getMonthAssistsData($budget->id, $first_day_of_month, $last_day_of_month, $workers);
        $months = $this->Assists->Users->BuildingsUsers->Buildings->Budgets->getListMonthsBudget($budget_date_created_months, $budget->duration);
        $this->set(compact('budget', 'assist_month_data', 'buildings', 'months', 'sf_building', 'workers', 'assistance_date', 'month_days'));
    }

    /**
     * Funcion que calcula y guarda las remuneraciones de los trabajadores
     */
    public function salaries_report($budget_id = '', $date_month = '')
    {
        $salaryReport = $this->Assists->Budgets->SalaryReports->newEntity();
        $first_day_of_month = null;
        $last_day_of_month = null;
        $budget = null;
        $workers_assists_data = array();
        $buildings = null;
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if (empty($date_month) || $date_month == null) {
            $first_day_of_month = new \DateTime("first day of this month");
            $last_day_of_month = new \DateTime("last day of this month");
        } else {
            $first_day_of_month = new \DateTime($date_month);
            $last_day_of_month = new \DateTime($date_month);
            $last_day_of_month->modify('last day of this month');
        }
        if (!empty($this->request->query)) {
            if (!empty($this->request->query['months'])) {
                $date_filter = explode('_', $this->request->query['months']);
                $first_day_of_month = new \DateTime($date_filter[0] . '-' . $date_filter[1] . '-1');
                $last_day_of_month = new \DateTime($date_filter[0] . '-' . $date_filter[1] . '-1');
                $last_day_of_month = $last_day_of_month->modify('last day of this month');
            }
        }
        $assistance_date = new \DateTime($first_day_of_month->format('Y-m-d'));
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Assists->Users->getUserBuildings($this->request->session()->read('Auth.User.id'));
            if (count($user_buildings) > 0) {
                $budget = $this->Assists->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates'],
                    'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                ])->first();
            } else {
                $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                return $this->redirect(['controller' => 'users', 'action' => 'index']);
            }
            if ($budget->id != $budget_id) {
                $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde a la asistencia. Por favor, edite la información de usuario.');
                return $this->redirect(['controller' => 'users', 'action' => 'index']);
            }
        } else { //los demás perfiles
            // $buildings = $this->Assists->Budgets->Buildings->getActiveBuildingsWithSoftlandInfo();
            $last_building = $this->request->session()->read('Config.last_building');
            if (!empty($budget_id) || $budget_id != null) {
                $budget = $this->Assists->Budgets->get($budget_id, [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates']
                ]);
            } else {
                if (!empty($this->request->query)) {
                    if (!empty($this->request->query['building_id'])) {
                        $budget = $this->Assists->Budgets->find('all', [
                            'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates'],
                            'conditions' => ['Budgets.building_id' => $this->request->query['building_id']]
                        ])->first();
                    }
                } else {
                    if(!empty($last_building)) {
                        $budget = $this->Assists->Budgets->find('all', [
                            'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates'],
                            'conditions' => ['Budgets.building_id' => $last_building]
                        ])->first();
                    }else{
                        $budget = $this->Assists->Budgets->find('all', [
                            'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates'],
                            'conditions' => ['Budgets.building_id' => key($buildings)]
                        ])->first();
                    }
                }
            }

        }
        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
        ])->first();
        $workers_data = $this->Assists->Workers->getSoftlandWorkersAndRentaInfoByBuildingWithWorkerId($budget->building_id);
        //genera lista de meses de asistencia de ppto.
        $budget_date_created = new \DateTime($budget->created->format('d-m-Y'));
        $budget_date_created_months = new \DateTime($budget->created->format('d-m-Y'));
        //si el primer día del mes es menor a la fecha de creación del ppto, usar esa fecha
        ($first_day_of_month < $budget_date_created) ? $first_day_of_month = $budget_date_created : '';
        $month_days = $this->Assists->getMonthDays($assistance_date->format('Y-m-d'));
        $salaries_month_data = $this->Assists->getMonthSalariesdata($budget->id, $first_day_of_month, $last_day_of_month, $workers_data);
        $months = $this->Assists->Users->BuildingsUsers->Buildings->Budgets->getListMonthsBudget($budget_date_created_months, $budget->duration);
        $this->set(compact('budget', 'salaries_month_data', 'buildings', 'months', 'sf_building', 'workers', 'assistance_date', 'month_days', 'salaryReport'));
        $save_errors = 0;
        if ($this->request->is('post')) {
            if (!empty($this->request->data['Worker'])) {
                foreach ($this->request->data['Worker'] as $worker_id => $worker) {
                    (!$salaryReport->isNew()) ? $salaryReport = $this->Assists->Budgets->SalaryReports->newEntity() : '';
                    $salaryReport->budget_id = $this->request->data['budget_id'];
                    $salaryReport->assistance_date = $assistance_date;
                    $salaryReport->worker_id = $worker['worker_id'];
                    $salaryReport->total_taxable = $worker['Salary']['total_taxable'];
                    $salaryReport->travel_expenses = $worker['Salary']['travel_expenses'];
                    $salaryReport->total_not_taxable = $worker['Salary']['total_not_taxable'];
                    $salaryReport->total_assets = $worker['Salary']['total_assets'];
                    $salaryReport->other_discounts = $worker['Salary']['other_discounts'];
                    $salaryReport->total_discounts = $worker['Salary']['total_discounts'];
                    $salaryReport->liquid_to_pay = $worker['Salary']['liquid_to_pay'];
                    if (!$this->Assists->Budgets->SalaryReports->save($salaryReport)) {
                        $save_errors++;
                    }
                }
                if ($save_errors > 0) {
                    $this->Flash->error('No se guardaron correctamente los datos del reporte de remuneraciones. Por favor, inténtelo nuevamente');
                    return $this->redirect(['controller' => 'salary_reports', 'action' => 'index']);
                } else {
                    $this->Flash->success('Se ha guardado correctamente la información del reporte de remuneraciones');
                    return $this->redirect(['controller' => 'salary_reports', 'action' => 'index']);
                }
            } else {
                $this->Flash->error('No se pudo procesar los datos del reporte de remuneraciones. Por favor, inténtelo nuevamente');
                return $this->redirect(['controller' => 'salary_reports', 'action' => 'index']);
            }
        }
    }
}
