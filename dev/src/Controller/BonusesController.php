<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\I18n\Time;

/**
 * Bonuses Controller
 *
 * @property \App\Model\Table\BonusesTable $Bonuses */
class BonusesController extends AppController
{
    /**
    * beforeFilter
    */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        //validar que el presupuesto no esté finalizado o sin aprobar
        $current_action = $this->request->params['action'];
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if ($current_action == 'add' ||
            $current_action == 'edit' ||
            $current_action == 'delete' ||
            $current_action == 'change_state' ||
            $current_action == 'comment') {
            if (count($this->request->params['pass']) > 0) {
                if ($this->request->params['controller'] == 'Bonuses' && $current_action != 'add') {
                    $deal = $this->Bonuses->get($this->request->params['pass'][0]);
                    $current_state = $this->Bonuses->Budgets->current_budget_state($deal->budget_id);
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
                    $current_state = $this->Bonuses->Budgets->current_budget_state($this->request->params['pass'][0]);
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
                }
            } else {
                if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
                    if ($this->request->params['controller'] == 'Bonuses' && $current_action == 'add') {
                        $user_buildings = $this->Bonuses->Users->getUserBuildings($this->request->session()->read('Auth.User.id'));
                        if (count($user_buildings) > 0) {
                            $buildings = array_combine($user_buildings,$user_buildings);
                            $budget = $this->Bonuses->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                                'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                            ])->first();
                            $current_state = $this->Bonuses->Budgets->current_budget_state($budget->id);
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
                            $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                            return $this->redirect(['controller' => 'users', 'action' => 'index']);
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
        $user_id = $this->request->session()->read('Auth.User.id');
        $group_id = $this->request->session()->read('Auth.User.group_id');
        $states = $this->Bonuses->getStates();
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Bonuses->Users->getUserBuildings($user_id);
            if (count($user_buildings) > 0) {
                $buildings = array_combine($user_buildings,$user_buildings);
                $budget = $this->Bonuses->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
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
                    $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde a los tratos. Por favor, edite la información de usuario.');
                    return $this->redirect(['controller' => 'users', 'action' => 'index']);
                }
            } else {
                $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
                return $this->redirect(['action' => 'index']);
            }
        } else { //los demás perfiles
            $buildings = $this->Bonuses->Budgets->Buildings->getActiveBuildingsWithSoftlandInfo();
            $last_building = $this->request->session()->read('Config.last_building');
            if (!empty($this->request->query)) {
                $budget = $this->Bonuses->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => $this->request->query['building_id']]
                ])->first();
            } else {
                 if(!empty($last_building)) {
                    $budget = $this->Bonuses->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => $last_building]
                ])->first();
                } else {
                    $budget = $this->Bonuses->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                        'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
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
        //
         $this->paginate = [
            'conditions' => ['Bonuses.budget_id' => $budget->id],
            'contain' => ['Budgets', 'Workers', 'Users'],
            'group' => ['Bonuses.budget_id','Bonuses.created']
        ];
        $bonuses_unique = $bonus = $this->Bonuses->find('all', [
            'contain' => ['Budgets', 'Workers', 'Users', 'BonusDetails'],
            'group' => ['Bonuses.budget_id'],
            'order' => ['Bonuses.id']
        ])->toArray();

        $bonuses_unique = $this->Bonuses->find();
        $bonuses_unique->select(['total_workers' => $bonuses_unique->func()->count('Bonuses.worker_id')])
            ->group(['Bonuses.budget_id','Bonuses.created'])
            ->autoFields(true);

        //
        $this->set('bonuses', $this->paginate($this->Bonuses));
        $this->set(compact('bonuses_unique', 'buildings', 'budget', 'sf_building', 'states'));
        $this->set('_serialize', ['bonuses']);
    }

    /**
     * View method
     *
     * @param string|null $id Bonus id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $states = $this->Bonuses->getStates();
        $user_id = $this->request->session()->read('Auth.User.id');
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if($id == null) {
            $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
            return $this->redirect($this->referer());
        }
        $bonus = $this->Bonuses->get($id);
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Bonuses->Users->getUserBuildings($user_id);
            if (count($user_buildings) > 0) {
                $buildings = array_combine($user_buildings,$user_buildings);
                $budget_id = $this->Bonuses->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                ])->first();
            } else {
                $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                return $this->redirect(['controller' => 'users', 'action' => 'index']);
            }
            if (!empty($budget_id->building_id) && $budget_id != null) {
                if ($budget_id->id != $bonus->budget_id) {
                    $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde a los tratos. Por favor, edite la información de usuario.');
                    return $this->redirect(['controller' => 'users', 'action' => 'index']);
                }
            } else {
                $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
                return $this->redirect(['action' => 'index']);
            }
        }
        $bonuses = $this->Bonuses->find('all', [
            'conditions' => ['Bonuses.created' => $bonus->created, 'Bonuses.budget_id' => $bonus->budget_id],
            'contain' => ['BonusDetails', 'BonusDetails.BudgetItems','Workers'],
        ]);
        $budget = $this->Bonuses->Budgets->get($bonus->budget_id, [
            'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'BudgetItems', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]]
        ]);
        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
        ])->first();
        $this->loadModel('Observations');
        $observations = $this->Observations->find()
            ->where(['Observations.model_id =' => $id, 'Observations.model =' => 'Bonuses'])
            ->order(['Observations.created' => 'DESC'])
            ->contain(['Users']);
        $valid_group = array(USR_GRP_COORD_PROY, USR_GRP_GE_GRAL, USR_GRP_GE_FINAN, USR_GRP_JEFE_RRHH, USR_GRP_VISITADOR, USR_GRP_ADMIN_OBRA);
        $approvalStates = $this->Bonuses->Budgets->BudgetApprovals->BudgetStates->find('list', ['limit' => 200])->toArray();
        $fichas = $this->Bonuses->Workers->getSoftlandWorkersByBuilding($budget->building_id);
        $workers = array_combine(array_column($fichas, 'ficha'), array_column($fichas, 'nombres'));
        $users = $this->Bonuses->Users->find('list', ['limit' => 200]);
        $this->set(compact('bonus', 'bonuses', 'budget', 'budgetItems', 'workers', 'users', 'approvalStates', 'fichas', 'valid_group', 'sf_building', 'states', 'observations'));
        $this->set('_serialize', ['bonus']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add($id = null)
    {
        $bonus = $this->Bonuses->newEntity();
        $states = $this->Bonuses->getStates();
        $user_id = $this->request->session()->read('Auth.User.id');
        $group_id = $this->request->session()->read('Auth.User.group_id');
        $budget = null;
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Bonuses->Users->getUserBuildings($user_id);
            if (count($user_buildings) > 0) {
                $buildings = array_combine($user_buildings, $user_buildings);
                $budget = $this->Bonuses->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'BudgetItems', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                ])->first();
            } else {
                $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                return $this->redirect(['controller' => 'users', 'action' => 'index']);
            }
            if (!empty($id) && $id != null) {
                if (!empty($budget->id)) {
                    if ($budget->id != $id) {
                        $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde a los tratos. Por favor, edite la información de usuario.');
                        return $this->redirect(['controller' => 'users', 'action' => 'index']);
                    }
                } else {
                    $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
                    return $this->redirect(['action' => 'index']);
                }
            }
        } else {
            if (empty($id) && $id == null) {
                $buildings = $this->Deals->Users->BuildingsUsers->Buildings->getActiveBuildingsWithSoftlandInfo();
                $budget = $this->Bonuses->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => $buildings[0]]
                ])->first();
            } else {
                $budget = $this->Bonuses->Users->BuildingsUsers->Buildings->Budgets->get($id, [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]]
                ]);
            }
        }
        if ($this->request->is('post')) {
            (empty($id) && $id == null) ? $id = $budget->id : '';
            $cw = count($this->request->data['workers']);
            $created = date('Y-m-d H:i:s');
            $this->request->data['start_date'] = new Time(str_replace("/", "-", $this->request->data['start_date']));
            $this->request->data['state'] = $states[0];
            foreach($this->request->data['workers'] as $wo) {
                unset($bonus);
                if($wo['amount']!=0){
                    //start fields
                    $bonus = $this->Bonuses->newEntity();
                    $bonus->budget_id = $id;
                    $bonus->state = $states[0];
                    $trabajador = $this->Bonuses->Workers->findBySoftlandId($wo['id'])->first();
                    if(is_null($trabajador)){
                        $new_worker = $this->Bonuses->Workers->newEntity();
                        $new_worker->softland_id = $wo['id'];
                        $trabajador = $this->Bonuses->Workers->save($new_worker);
                    }
                    $bonus->worker_id = $trabajador->id;
                    $bonus->description = $this->request->data['description'];
                    $bonus->start_date = $this->request->data['start_date'];
                    $bonus->amount = $wo['amount'];
                    $bonus->created = $created;
                    $bonus->user_created_id = $this->Auth->user('id');
                    //end_fields
                    if ($this->Bonuses->save($bonus)) {
                        $cw--;
                    }
                }else{
                    $cw--;
                }
            }
            if($cw == 0) {
                $this->Flash->success(__('El bono ha sido correctamente ingresado al sistema.'));
                $this->redirect(['action' => 'index']);
            }
        }
        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
        ])->first();

        $date = date('Y-m');

        $approvalStates = $this->Bonuses->Budgets->BudgetApprovals->BudgetStates->find('list', ['limit' => 200])->toArray();
        $fichas = $this->Bonuses->Workers->getSoftlandWorkersByBuildingTest($budget->building_id);
        foreach($fichas as $k => $ficha) {
            if(($date > $ficha['vigHasta'])) {
                unset($fichas[$k]);
            }
        }
        $this->loadModel('Charges');
        $charges = $this->Charges->find('list', [
            'keyField' => 'softland_id',
            'valueField' => 'max_amount_bonus'])->toArray();
        $workers = array_combine(array_column($fichas, 'ficha'), array_column($fichas, 'nombres'));
        $users = $this->Bonuses->Users->find('list', ['limit' => 200]);
        $this->set(compact('bonus', 'budget', 'workers', 'users', 'approvalStates', 'fichas', 'charges','sf_building'));
        $this->set('_serialize', ['bonus']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Bonus id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $states = $this->Bonuses->getStates();
        $user_id = $this->request->session()->read('Auth.User.id');
        $group_id = $this->request->session()->read('Auth.User.group_id');
        $budget = null;
        if($id == null) {
            $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
            return $this->redirect($this->referer());
        }
        $bonus = $this->Bonuses->get($id);
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Bonuses->Users->getUserBuildings($user_id);
            if (count($user_buildings) > 0) {
                $budget = $this->Bonuses->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                ])->first();
            } else {
                $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                return $this->redirect(['controller' => 'users', 'action' => 'index']);
            }
            if (!empty($budget->building_id) && $budget != null) {
                if ($budget->id != $bonus->budget_id) {
                    $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde a los tratos. Por favor, edite la información de usuario.');
                    return $this->redirect(['controller' => 'users', 'action' => 'index']);
                }
            } else {
                $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
                return $this->redirect(['action' => 'index']);
            }
        }
        $bonuses = $this->Bonuses->find('all', [
            'conditions' => ['Bonuses.created' => $bonus->created, 'Bonuses.budget_id' => $bonus->budget_id],
            'contain' => ['BonusDetails', 'BonusDetails.BudgetItems','Workers'],
            'order' => ['Bonuses.id']
        ]);
        $budget = $this->Bonuses->Budgets->get($bonus->budget_id, [
            'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'BudgetItems', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]]
        ]);
        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
        ])->first();
        //lista de deals para el presupuesto con la misma fecha. todos los que no llegan por this->request->data ya deben existir.
        $bonusesCheckList = $this->Bonuses->find('list', [
            'conditions' => ['Bonuses.created' => $bonus->created, 'Bonuses.budget_id' => $bonus->budget_id],
            'order' => ['Bonuses.id']
        ])->toArray();

        if ($this->request->is(['patch', 'post', 'put'])) {
            $this->request->data['start_date'] = new Time($this->request->data['start_date']);
            foreach($this->request->data['workers'] as $wo) {
                unset($workerBonus);
                if($wo['amount']!=""){
                    $workerBonus = $this->Bonuses->find('all', [
                        'conditions' => ['Bonuses.created' => $bonus->created, 'Bonuses.budget_id' => $bonus->budget_id, 'Bonuses.worker_id' => $wo['worker_id']],
                        'contain' => ['BonusDetails'],
                        'order' => ['Bonuses.id']
                    ])->first();

                    if(empty($workerBonus)) {
                        $workerBonus = $this->Bonuses->newEntity();
                        $trabajador = $this->Bonuses->Workers->get($wo['worker_id']);
                        $workerBonus->worker_id = $trabajador->id;
                        $workerBonus->budget_id = $bonus->budget_id;
                        $workerBonus->state = $states[0];
                        $workerBonus->description = $bonus->description;
                        $workerBonus->created = $bonus->created;
                    }
                    $workerBonus->amount = $wo['amount'];
                    $workerBonus->start_date = $this->request->data['start_date'];
                    $workerBonus->user_modified_id = $this->request->session()->read('Auth.User.id');
                    //$workerDeal->state = 'Aprobado';
                    if(isset($wo['id'])) {
                        if(in_array($wo['id'],$bonusesCheckList)) {
                            unset($bonusesCheckList[array_search($wo['id'],$bonusesCheckList)]);
                        }
                    }
                    if($this->Bonuses->save($workerBonus)) {
                        //debug('Deal: master of the universe');
                    }
                    //limpio dealDetails
                    if(!empty($wo['partidas'])) {
                        $partidas = array_column($wo['partidas'],'itemId');
                        $details = $this->Bonuses->BonusDetails->find('all',[
                            'conditions' => ['BonusDetails.bonus_id' => $workerBonus->id, 'BonusDetails.budget_item_id NOT IN' => $partidas]
                        ]);
                    } else {
                        $details = $this->Bonuses->BonusDetails->find('all',[
                            'conditions' => ['BonusDetails.bonus_id' => $workerBonus->id]
                        ]);
                    }
                    foreach($details as $d) {
                        if($this->Bonuses->BonusDetails->delete($d)) {
                            debug('delete detail');
                        } else {
                            debug('delete detail epic fail');
                        }
                    }

                    if(!empty($wo['partidas'])) {
                        foreach($wo['partidas'] as $wop) {
                            unset($detail);
                            $detail = $this->Bonuses->BonusDetails->find('all',[
                                'conditions' => ['BonusDetails.budget_item_id' => $wop['itemId'], 'BonusDetails.bonus_id' => $workerBonus->id]
                            ])->first();
                            if(empty($detail)) {
                                $detail = $this->Bonuses->BonusDetails->newEntity();
                            }
                            if($wop['itemPercent'] != $detail->percentage) {
                                $detail->percentage = $wop['itemPercent'];
                            }
                            $detail->budget_item_id = $wop['itemId'];
                            $detail->bonus_id = $workerBonus->id;
                            if($this->Bonuses->BonusDetails->save($detail)) {
                                debug('updated detail');
                            } else {
                                debug('updated detail epic fail');
                            }

                        }
                    }
                }
            }
            //buscar ultimo id deal, verificar si es el mismo de los comentarios
            //si es el mismo y lo voy a borrar, necesito u nuevo deal id para mantener y asociar los comentarios.
            $bonos = array();
            foreach($bonuses as $bono) {
                $bonos[] = $bono->id;
            }
            $bonos = array_diff($bonos, $bonusesCheckList);
            $BonusesMinId = min($bonos);
            $this->loadModel('Observations');
            $observation = $this->Observations->find()
                            ->where(['Observations.model_id =' => $id, 'Observations.model =' => 'Bonuses'])
                            ->contain(['Users'])->first();
            if(!empty($observation)) {
                if($observation->model_id != $BonusesMinId) {
                    $observationsAll = $this->Observations->find()
                                ->where(['Observations.model_id =' => $id, 'Observations.model =' => 'Bonuses']);
                    foreach($observationsAll as $obs) {
                        $obs->model_id = $BonusesMinId;
                        if($this->Observations->save($obs)) {
                            debug('obs epic win');
                        } else {
                            debug('obs epic fail');
                        }
                    }
                } else {
                    debug('no need');
                }
            }
            if(!empty($bonusesCheckList)) {
                foreach($bonusesCheckList as $de) {
                    unset($remDeal);
                    $remDeal = $this->Bonuses->get($de);
                    //busco deal details asociados y se borran.
                    $remBonusDetails = $this->Bonuses->BonusDetails->find('all',['conditions' => ['BonusDetails.bonus_id' => $de]]);
                    if(!empty($remBonusDetails)) {
                        foreach($remBonusDetails as $remdd) {
                            $this->Bonuses->BonusDetails->delete($remdd);
                        }
                    }
                    if($this->Bonuses->delete($remDeal)) {
                        debug('clean trato');
                    } else {
                        debug('fail clean trato');
                    }
                }
            }
            $this->Flash->success(__('El bono ha sido correctamente actualizado.'));
            return $this->redirect(['action' => 'view', $BonusesMinId]);
        }
        $budgetItems = $this->Bonuses->Budgets->BudgetItems->find('list', [
                'keyField' => 'id',
                'valueField' => 'fullItem'
            ])->where(['BudgetItems.disabled' => 0,'BudgetItems.budget_id' => $bonus->budget_id]);

        $approvalStates = $this->Bonuses->Budgets->BudgetApprovals->BudgetStates->find('list', ['limit' => 200])->toArray();
        // $fichas = $this->Bonuses->Workers->getSoftlandWorkersByBuilding($budget->building_id);
        $fichas = $this->Bonuses->Workers->getSoftlandWorkersByBuildingTest($budget->building_id);
        $date = date('Y-m');
        foreach($fichas as $k=>&$f) {
            if(($date > $f['vigHasta'])) {
                unset($fichas[$k]);
            }else{
                $trabajador = $this->Bonuses->Workers->find('all',[
                    'conditions' => ['Workers.softland_id' => $f['ficha']]
                ])->first();

                // No funcaba antes cuando el worker no existía, ahora se validará y se agregará al trabajador
                if(is_null($trabajador)){
                    $new_worker = $this->Bonuses->Workers->newEntity();
                    $new_worker->softland_id = $f['ficha'];
                    $trabajador = $this->Bonuses->Workers->save($new_worker);
                }
                $worker_bonus = $this->Bonuses->find('all', [
                    'conditions' => [
                        'Bonuses.created' => $bonus->created->format('Y-m-d H:i:s'),
                        'Bonuses.budget_id' => $bonus->budget_id,
                        'Bonuses.worker_id' => $trabajador->id
                    ]
                ])->first();
                if(!empty($worker_bonus)){
                    $f['bonus_id'] = $worker_bonus->id;
                    $f['amount'] = $worker_bonus->amount;
                }
                $f['worker_id'] = $trabajador->id;
            }
        }
        $this->loadModel('Charges');
        $charges = $this->Charges->find('list', [
                                                'keyField' => 'softland_id',
                                                'valueField' => 'max_amount_bonus'])->toArray();
        $users = $this->Bonuses->Users->find('list', ['limit' => 200]);
        $this->set(compact('bonus', 'bonuses', 'budget', 'budgetItems', /*'workers', */'users', 'approvalStates', 'fichas', 'charges','sf_building'));
        $this->set('_serialize', ['bonus']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Bonus id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $user_id = $this->request->session()->read('Auth.User.id');
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if($id == null) {
            $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
            return $this->redirect($this->referer());
        }
        $bonus = $this->Bonuses->get($id);
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Bonuses->Users->getUserBuildings($user_id);
            if (count($user_buildings) > 0) {
                $buildings = array_combine($user_buildings,$user_buildings);
                $budget_id = $this->Bonuses->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                ])->first();
            } else {
                $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                return $this->redirect(['controller' => 'users', 'action' => 'index']);
            }
            if (!empty($budget_id->building_id) && $budget_id != null) {
                if ($budget_id->id != $bonus->budget_id) {
                    $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde a los tratos. Por favor, edite la información de usuario.');
                    return $this->redirect(['controller' => 'users', 'action' => 'index']);
                }
            } else {
                $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
                return $this->redirect(['action' => 'index']);
            }
        }
        $this->request->allowMethod(['post', 'delete']);
        if ($this->Bonuses->delete($bonus)) {
            $this->Flash->success(__('El bono ha sido eliminado.'));
        } else {
            $this->Flash->error(__('El bono no ha sido eliminado, intentalo denuevo.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function change_state($id = null)
    {
        $user_id = $this->request->session()->read('Auth.User.id');
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if($id == null) {
            $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
            return $this->redirect($this->referer());
        }
        $bonus = $this->Bonuses->get($id);
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Bonuses->Users->getUserBuildings($user_id);
            if (count($user_buildings) > 0) {
                $buildings = array_combine($user_buildings,$user_buildings);
                $budget_id = $this->Bonuses->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                ])->first();
            } else {
                $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                return $this->redirect(['controller' => 'users', 'action' => 'index']);
            }
            if (!empty($budget_id->building_id) && $budget_id != null) {
                if ($budget_id->id != $bonus->budget_id) {
                    $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde a los tratos. Por favor, edite la información de usuario.');
                    return $this->redirect(['controller' => 'users', 'action' => 'index']);
                }
            } else {
                $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
                return $this->redirect(['action' => 'index']);
            }
        }
        $this->request->allowMethod(['post', 'delete']);
        $this->autoRender = false;
        if($this->request->data['state']){
            $bonus = $this->Bonuses->get($id, []);
            $bonuses = $this->Bonuses->find('all', [
                'conditions' => ['Bonuses.created' => $bonus->created, 'Bonuses.budget_id' => $bonus->budget_id]
            ])->toArray();
            //obs
            $this->loadModel('Observations');
            $observation = $this->Observations->newEntity();
            $observation->model = 'Bonuses';
            $observation->action = 'comment';
            $observation->user_id = $this->request->session()->read('Auth.User.id');
            $observation->observation = '[Estado: ' . $this->request->data['state'] . '] ' . 'Se modificó el estado.';
            $observation->model_id = $id;
            if ($this->Observations->save($observation)) {
                debug('epic win');
            }
            foreach($bonuses as $k => $de) {

                $bonuses[$k]->state = $this->request->data['state'];
                if($this->Bonuses->save($bonuses[$k])) {
                    //debug('Deal: master of the universe');
                }
            }
        }
        return $this->redirect(['action' => 'view', $id]);
    }

    public function comment($id = null)
    {
        $user_id = $this->request->session()->read('Auth.User.id');
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if($id == null) {
            $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
            return $this->redirect($this->referer());
        }
        $bonus = $this->Bonuses->get($id);
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Bonuses->Users->getUserBuildings($user_id);
            if (count($user_buildings) > 0) {
                $buildings = array_combine($user_buildings,$user_buildings);
                $budget_id = $this->Bonuses->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                ])->first();
            } else {
                $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                return $this->redirect(['controller' => 'users', 'action' => 'index']);
            }
            if (!empty($budget_id->building_id) && $budget_id != null) {
                if ($budget_id->id != $bonus->budget_id) {
                    $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde a los tratos. Por favor, edite la información de usuario.');
                    return $this->redirect(['controller' => 'users', 'action' => 'index']);
                }
            } else {
                $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
                return $this->redirect(['action' => 'index']);
            }
        }
        $this->loadModel('Observations');
        $bonus_id = $id;
        $observations = $this->Observations->find('all')
                        ->where(['Observations.model_id =' => $bonus_id, 'Observations.model =' => 'Bonuses'])
                        ->order(['Observations.created' => 'DESC'])
                        ->contain(['Users'])->toArray();
        $observation = $this->Observations->newEntity();
        $group = $this->request->session()->read('Auth.User.group_id');
        $valid_group = array(2,3,4,5,6,7);
        if(!empty($group)) {
            if(in_array($group, $valid_group)) {
                if ($this->request->is('post')) {
                    $observation = $this->Observations->patchEntity($observation, $this->request->data);
                    $observation->model = 'Bonuses';
                    $observation->action = 'comment';
                    $observation->model_id = $bonus_id;
                    $observation->user_id = $this->request->session()->read('Auth.User.id');
                    if ($this->Observations->save($observation)) {
                        $this->Flash->success('El comentario ha sido guardado correctamente.');
                        return $this->redirect(['action' => 'view',$id]);
                    } else {
                        $this->Flash->error('La observation no ha sido eliminada, intentalo nuevamente.');
                    }
                }
            } else {
                $this->Flash->error('Usted no tiene permisos para comentar en este modulo.');
                return $this->redirect(['action' => 'view', $id]);
            }
        }
        $users = $this->Observations->Users->find('list',
            ['limit' => 200,
            'keyField' => 'id',
            'valueField' => 'full_name'])->toArray();

        $this->set(compact('observation', 'users', 'observations','bonus_id'));
        $this->set('_serialize', ['observation']);
    }


    /**
     * Agregar comentario a bono para entender la aprobación, rechazo, u error de ingreso.
     * @param string $id identificador bono
     * @author [name] <[<email address>]>
     */
    public function add_comment($id='')
    {
        //verificar obra tiene presupuesto

        //verificar perfil de usuario

        //buscar bono

        //aprobar en cascada
    }

    /**
     * Aprobar bonos, debe soportar desde Jefe RRHH hasta Gerente General, dependiendo del monto
     * @param  string $id identificador del bono
     * @return bool     aprobación
     * @author [name] <[<email address>]>
     */
    public function approve($id='')
    {
        //verificar obra tiene presupuesto

        //verificar perfil de usuario

        //buscar bono

        //aprobar en cascada
    }

    /**
     * Rechazar bono, se podrá rechazar el bono, omitiendo la información de este al presupuesto de la obra y cálculos de RRHH
     * @param  string $id identificador bono
     * @return bool     rechazo
     * @author [name] <[<email address>]>
     */
    public function reject($id='')
    {
        //verificar obra tiene presupuesto

        //verificar perfil de usuario

        //buscar bono

        //rechazar en cascada
    }
}
