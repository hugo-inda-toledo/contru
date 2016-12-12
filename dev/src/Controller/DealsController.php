<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\I18n\Time;

/**
 * Deals Controller
 *
 * @property \App\Model\Table\DealsTable $Deals */
class DealsController extends AppController
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
                if ($this->request->params['controller'] == 'Deals' && $current_action != 'add') {
                    $deal = $this->Deals->get($this->request->params['pass'][0]);
                    $current_state = $this->Deals->Budgets->current_budget_state($deal->budget_id);
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
                    $current_state = $this->Deals->Budgets->current_budget_state($this->request->params['pass'][0]);
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
                    if ($this->request->params['controller'] == 'Deals' && $current_action == 'add') {
                        $user_buildings = $this->Deals->Users->getUserBuildings($this->request->session()->read('Auth.User.id'));
                        if (count($user_buildings) > 0) {
                            $budget = $this->Deals->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                                'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                            ])->first();
                            $current_state = $this->Deals->Budgets->current_budget_state($budget->id);
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
        $states = $this->Deals->getStates();
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Deals->Users->getUserBuildings($user_id);
            if (count($user_buildings) > 0) {
                $buildings = array_combine($user_buildings,$user_buildings);
                $budget = $this->Deals->Users->BuildingsUsers->Buildings->Budgets->find('all', [
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
            $buildings = $this->Deals->Budgets->Buildings->getActiveBuildingsWithSoftlandInfo();
            //debug($this->request->session()->read('Config'));
            //echo intval($this->request->session()->read('Config.last_building'));
            $last_building = $this->request->session()->read('Config.last_building');
             if (!empty($this->request->query)) {
                $budget = $this->Deals->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => intval($this->request->session()->read('Config.last_building'))]
                ])->first();
            } else {
                if(!empty($last_building)) {
                    $budget = $this->Deals->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => $last_building]
                ])->first();
                } else {
                    $budget = $this->Deals->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                        'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                        'conditions' => ['Budgets.building_id' => key($buildings)]
                    ])->first();
                }
            }
        }
        //debug($budget);
        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
        ])->first();
        $this->paginate = [
            'conditions' => ['Deals.budget_id' => $budget->id],
            'contain' => ['Budgets', 'Workers', 'Users'],
            'group' => ['Deals.start_date', 'Deals.budget_id','Deals.created']
        ];
        $deals_unique = $deal = $this->Deals->find('all', [
            'contain' => ['Budgets', 'Workers', 'Users', 'DealDetails'],
            'group' => ['Deals.budget_id'],
            'order' => ['Deals.id']
        ])->toArray();
        $deals_unique = $this->Deals->find();
        $deals_unique->select(['total_workers' => $deals_unique->func()->count('Deals.worker_id')])
            ->group(['Deals.budget_id','Deals.created'])
            ->autoFields(true);
        $this->set('deals', $this->paginate($this->Deals));
        $this->set(compact('deals_unique', 'buildings', 'budget', 'sf_building', 'states'));
        $this->set('_serialize', ['deals']);
    }

    /**
     * View method
     *
     * @param string|null $id Deal id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $states = $this->Deals->getStates();
        if($id == null) {
            $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
            return $this->redirect($this->referer());
        }
        $group_id = $this->request->session()->read('Auth.User.group_id');
        $deal = $this->Deals->get($id, [
            'contain' => []
        ]);
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Deals->Users->getUserBuildings($this->request->session()->read('Auth.User.id'));
            if (count($user_buildings) > 0) {
                $buildings = array_combine($user_buildings, $user_buildings);
                $budget_id = $this->Deals->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                ])->first();
            } else {
                $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                return $this->redirect(['controller' => 'users', 'action' => 'index']);
            }
            if (!empty($budget_id->building_id) && $budget_id != null) {
                if ($budget_id->id != $deal->budget_id) {
                    $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde a los tratos. Por favor, edite la información de usuario.');
                    return $this->redirect(['controller' => 'users', 'action' => 'index']);
                }
            } else {
                $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
                return $this->redirect(['action' => 'index']);
            }
        }
        $deals = $this->Deals->find('all', [
            'conditions' => ['Deals.start_date' => $deal->start_date, 'Deals.budget_id' => $deal->budget_id,'Deals.created' => $deal->created],
            'contain' => ['DealDetails', 'DealDetails.BudgetItems','Workers'],
        ]);

        $budget = $this->Deals->Budgets->get($deal->budget_id, [
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
                ->where(['Observations.model_id =' => $id, 'Observations.model =' => 'Deals'])
                ->order(['Observations.created' => 'DESC'])
                ->contain(['Users']);
        $valid_group = array(USR_GRP_GE_FINAN, USR_GRP_JEFE_RRHH, USR_GRP_VISITADOR, USR_GRP_ADMIN_OBRA);
        $approvalStates = $this->Deals->Budgets->BudgetApprovals->BudgetStates->find('list', ['limit' => 200])->toArray();
        $fichas = $this->Deals->Workers->getSoftlandWorkersByBuilding($budget->building_id);
        $workers = array_combine(array_column($fichas, 'ficha'), array_column($fichas, 'nombres'));
        $users = $this->Deals->Users->find('list', ['limit' => 200]);
        $this->set(compact('deal', 'deals', 'budget', 'observations', 'budgetItems', 'workers', 'users','approvalStates','fichas', 'valid_group' , 'states','state', 'sf_building'));
        $this->set('_serialize', ['deal']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add($id = null)
    {
        $deal = $this->Deals->newEntity();
        $states = $this->Deals->getStates();
        $budget = null;
        $buildings = null;
        $user_id = $this->request->session()->read('Auth.User.id');
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Deals->Users->getUserBuildings($user_id);
            if (count($user_buildings) > 0) {
                $buildings = array_combine($user_buildings,$user_buildings);
                $budget = $this->Deals->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
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
                        $this->Flash->info('El usuario no está asociado a la obra que corresponde al trato seleccionado. Por favor, edite la información de usuario.');
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
                $budget = $this->Deals->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => $buildings[0]]
                ])->first();
            } else {
                $budget = $this->Deals->Users->BuildingsUsers->Buildings->Budgets->get($id, [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]]
                ]);
            }
        }
        (empty($id) && $id == null) ? $id = $budget->id : '';
        if ($this->request->is('post')) {
            $this->request->data['start_date'] = new Time($this->request->data['start_date']);
            $this->request->data['state'] = $states[0];
            $cw = count($this->request->data['workers']);
            foreach ($this->request->data['workers'] as $wo) {
                unset($deal);
                //start fields
                $deal = $this->Deals->newEntity();
                $deal->budget_id = $id;
                $deal->state = $states[0];
                $trabajador = $this->Deals->Workers->findBySoftlandId($wo['id'])->first();
                if(is_null($trabajador)){
                    $new_worker = $this->Deals->Workers->newEntity();
                    $new_worker->softland_id = $wo['id'];
                    $trabajador = $this->Deals->Workers->save($new_worker);
                }
                $deal->worker_id = $trabajador->id;
                $deal->description = $this->request->data['description'];
                $deal->amount = $wo['amount'];
                $deal->start_date = $this->request->data['start_date'];
                $deal->user_created_id = $this->request->session()->read('Auth.User.id');
                //end_fields
                if ($this->Deals->save($deal)) {
                    $cw--;
                } else {
                    debug($deal); die();
                }
            }
            if ($cw == 0) {
                $this->Flash->success(__('El trato se ha guardado correctamente en el sistema'));
                $this->redirect(['action' => 'index']);
            }
        }
        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
        ])->first();
        $approvalStates = $this->Deals->Budgets->BudgetApprovals->BudgetStates->find('list', ['limit' => 200])->toArray();
        //$workers = $this->Deals->Workers->find('list', ['limit' => 200]);
        $fichas = $this->Deals->Workers->getSoftlandWorkersByBuilding($budget->building_id);
        //$fichas2 = $this->Deals->Workers->getSoftlandWorkers();
        //debug($fichas2);
        $this->loadModel('Charges');
        $charges = $this->Charges->find('list', [
            'keyField' => 'softland_id',
            'valueField' => 'max_amount_deals']);
        $workers = array_combine(array_column($fichas, 'ficha'), array_column($fichas, 'nombres'));
        $users = $this->Deals->Users->find('list', ['limit' => 200]);
        $currency_value = $budget->currencies_values{0}->currency;
        $this->set(compact('deal', 'budget', 'workers', 'users', 'approvalStates', 'fichas', 'charges','sf_building', 'currency_value'));
        $this->set('_serialize', ['deal']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Deal id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $states = $this->Deals->getStates();
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if($id == null) {
            $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
            return $this->redirect($this->referer());
        }
        $deal = $this->Deals->get($id);
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Deals->Users->getUserBuildings($this->request->session()->read('Auth.User.id'));
            if (count($user_buildings) > 0) {
                $buildings = array_combine($user_buildings,$user_buildings);
                $budget_id = $this->Deals->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                ])->first();
            } else {
                $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                return $this->redirect(['controller' => 'users', 'action' => 'index']);
            }
            if (!empty($budget_id->building_id) && $budget_id != null) {
                if ($budget_id->id != $deal->budget_id) {
                    $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde a los tratos. Por favor, edite la información de usuario.');
                    return $this->redirect(['controller' => 'users', 'action' => 'index']);
                }
            } else {
                $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
                return $this->redirect(['action' => 'index']);
            }
        }
        $deals = $this->Deals->find('all', [
            'conditions' => ['Deals.start_date' => $deal->start_date, 'Deals.budget_id' => $deal->budget_id,'Deals.created' => $deal->created],
            'contain' => ['DealDetails', 'DealDetails.BudgetItems','Workers'],
            'order' => ['Deals.id']
        ]);
        $budget = $this->Deals->Budgets->get($deal->budget_id, [
            'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'BudgetItems', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]]
        ]);
        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
        ])->first();
        //lista de deals para el presupuesto con la misma fecha de ejecucion, presupuesto y fecha de creacion. todos los que no llegan por this->request->data ya deben existir.
        $dealsCheckList = $this->Deals->find('list', [
            'conditions' => ['Deals.start_date' => $deal->start_date, 'Deals.budget_id' => $deal->budget_id, 'Deals.created' => $deal->created],
            'order' => ['Deals.id']
        ])->toArray();

        if ($this->request->is(['patch', 'post', 'put'])) {
            $this->request->data['start_date'] = new Time($this->request->data['start_date']);
            // Se mantiene la fecha de creación, porque se agrupan según este campo
            $created="";
            foreach ($this->request->data['workers'] as $wo) {
                unset($workerDeal);
                $conditions = ['Deals.start_date' => $deal->start_date, 'Deals.budget_id' => $deal->budget_id, 'Deals.worker_id' => $wo['worker_id']];
                if(isset($wo['id'])){
                    $conditions['Deals.id'] = $wo['id'];
                }
                $workerDeal = $this->Deals->find('all', [
                    'conditions' => $conditions,
                    'contain' => ['DealDetails'],
                    'order' => ['Deals.id']
                ])->first();
                if (empty($workerDeal)) {
                    $workerDeal = $this->Deals->newEntity();
                    $trabajador = $this->Deals->Workers->get($wo['worker_id']);
                    $workerDeal->worker_id = $trabajador->id;
                    $workerDeal->budget_id = $deal->budget_id;
                    $workerDeal->state = $states[0];
                }else{
                    $created = $workerDeal->created;
                }
                if($created!=""){
                    $workerDeal->created = $created;
                }
                $workerDeal->description = $this->request->data['description'];
                $workerDeal->start_date = $this->request->data['start_date'];
                $workerDeal->amount = $wo['amount'];
                $workerDeal->user_modified_id = $this->request->session()->read('Auth.User.id');
                //si esta la id del trato, la elimino de la lista de todos los tratos, al final las q sobren se borraran.
                if (isset($wo['id'])) {
                    if (in_array($wo['id'],$dealsCheckList)) {
                        unset($dealsCheckList[array_search($wo['id'],$dealsCheckList)]);
                    }
                }
                // pr($workerDeal);
                // die();
                if ($this->Deals->save($workerDeal)) {
                    //debug('Deal: master of the universe');
                }
                //limpio dealDetails
                if (!empty($wo['partidas'])) {
                    $partidas = array_column($wo['partidas'], 'itemId');
                    $details = $this->Deals->DealDetails->find('all',[
                        'conditions' => ['DealDetails.deal_id' => $workerDeal->id, 'DealDetails.budget_item_id NOT IN' => $partidas]
                    ]);
                } else {
                    $details = $this->Deals->DealDetails->find('all',[
                        'conditions' => ['DealDetails.deal_id' => $workerDeal->id]
                    ]);
                }
                foreach ($details as $d) {
                    if ($this->Deals->DealDetails->delete($d)) {
                        debug('delete detail');
                    } else {
                        debug('delete detail epic fail');
                    }
                }
                if (!empty($wo['partidas'])) {
                    foreach ($wo['partidas'] as $wop) {
                        unset($detail);
                        $detail = $this->Deals->DealDetails->find('all',[
                            'conditions' => ['DealDetails.budget_item_id' => $wop['itemId'], 'DealDetails.deal_id' => $workerDeal->id]
                        ])->first();
                        if (empty($detail)) {
                            $detail = $this->Deals->DealDetails->newEntity();
                        }
                        if ($wop['itemPercent'] != $detail->percentage) {
                            $detail->percentage = $wop['itemPercent'];
                        }
                        $detail->budget_item_id = $wop['itemId'];
                        $detail->deal_id = $workerDeal->id;
                        if ($this->Deals->DealDetails->save($detail)) {
                            debug('updated detail');
                        } else {
                            debug('updated detail epic fail');
                        }

                    }
                }
            }
            //buscar ultimo id deal, verificar si es el mismo de los comentarios
            //si es el mismo y lo voy a borrar, necesito u nuevo deal id para mantener y asociar los comentarios.
            $tratos = array();
            foreach ($deals as $trato) {
                $tratos[] = $trato->id;
            }
            $tratos = array_diff($tratos, $dealsCheckList);
            (!empty($tratos) && $tratos != null) ? $DealsMinId = min($tratos) : $DealsMinId = $tratos[0];
            $this->loadModel('Observations');
            $observation = $this->Observations->find()
                ->where(['Observations.model_id =' => $id, 'Observations.model =' => 'Deals'])
                ->contain(['Users'])->first();
            if (!empty($observation)) {
                if($observation->model_id != $DealsMinId) {
                    $observationsAll = $this->Observations->find()
                        ->where(['Observations.model_id =' => $id, 'Observations.model =' => 'Deals']);
                    foreach ($observationsAll as $obs) {
                        $obs->model_id = $DealsMinId;
                        if ($this->Observations->save($obs)) {
                            debug('obs epic win');
                        } else {
                            debug('obs epic fail');
                        }
                    }
                } else {
                    debug('no need');
                }
            }
            if (!empty($dealsCheckList)) {
                foreach ($dealsCheckList as $de) {
                    unset($remDeal);
                    $remDeal = $this->Deals->get($de);
                    //busco deal details asociados y se borran.
                    $remDealDetails = $this->Deals->DealDetails->find('all',['conditions' => ['DealDetails.deal_id' => $de]]);
                    if (!empty($remDealDetails)) {
                        foreach ($remDealDetails as $remdd) {
                            $this->Deals->DealDetails->delete($remdd);
                        }
                    }
                    if ($this->Deals->delete($remDeal)) {
                        debug('clean trato');
                    } else {
                        debug('fail clean trato');
                    }
                }
            }
            $this->Flash->success(__('El trato ha sido correctamente actualizado.'));
            return $this->redirect(['action' => 'view', $DealsMinId]);
        }
        $budgetItems = $this->Deals->Budgets->BudgetItems->find('list', [
            'keyField' => 'id',
            'valueField' => 'fullItem'
        ])->where(['BudgetItems.disabled' => 0,'BudgetItems.budget_id' => $deal->budget_id]);
        $approvalStates = $this->Deals->Budgets->BudgetApprovals->BudgetStates->find('list', ['limit' => 200])->toArray();
        $fichas = $this->Deals->Workers->getSoftlandWorkersByBuilding($budget->building_id);
        $workers = array_combine(array_column($fichas, 'ficha'), array_column($fichas, 'nombres'));
        foreach ($deals as $de) {
            if (array_key_exists($de->worker->softland_id, $workers)) {
                unset($workers[$de->worker->softland_id]);
            }
        }
        foreach ($fichas as &$f) {
            $trabajador = $this->Deals->Workers->find('all',[
                'conditions' => ['Workers.softland_id' => $f['ficha']]
            ])->first();
            if($trabajador!=null){
                if ($trabajador->softland_id == $f['ficha']) {
                    $f['id'] = $trabajador->id;
                }
            }
        }
        $this->loadModel('Charges');
        $charges = $this->Charges->find('list', [
            'keyField' => 'softland_id',
            'valueField' => 'max_amount_deals'])->toArray();
        $users = $this->Deals->Users->find('list', ['limit' => 200]);
        $this->set(compact('deal', 'deals', 'budget', 'budgetItems', 'workers', 'users', 'approvalStates', 'fichas', 'charges', 'sf_building'));
        $this->set('_serialize', ['deal']);
    }


    /**
     * Delete method
     *
     * @param string|null $id Deal id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $deal = $this->Deals->get($id);
        if ($this->Deals->delete($deal)) {
            $this->Flash->success(__('El trato ha sido actualizado.'));
        } else {
            $this->Flash->error(__('El trato no ha sido actualizado, intentalo nuevamente.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function change_state($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $this->autoRender = false;
         if($id == null) {
            $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
            return $this->redirect($this->referer());
        }
        $group_id = $this->request->session()->read('Auth.User.group_id');
        $deal = $this->Deals->get($id);
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Deals->Users->getUserBuildings($this->request->session()->read('Auth.User.id'));
            if (count($user_buildings) > 0) {
                $buildings = array_combine($user_buildings, $user_buildings);
                $budget_id = $this->Deals->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                ])->first();
            } else {
                $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                return $this->redirect($this->referer());
            }
            if (!empty($budget_id->building_id) && $budget_id != null) {
                if ($budget_id->id != $deal->budget_id) {
                    $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde a los tratos. Por favor, edite la información de usuario.');
                    return $this->redirect($this->referer());
                }
            } else {
                $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
                return $this->redirect($this->referer());
            }
        }
        if ($this->request->data['state']) {
            $deals = $this->Deals->find('all', [
                'conditions' => ['Deals.start_date' => $deal->start_date, 'Deals.budget_id' => $deal->budget_id, 'Deals.created' => $deal->created]
            ])->toArray();
            $message = (isset($this->request->data['comment']) && $this->request->data['comment']!="")?$this->request->data['comment']:'Se modificó el estado.';
            //obs
            $this->loadModel('Observations');
            $observation = $this->Observations->newEntity();
            $observation->model = 'Deals';
            $observation->action = 'comment';
            $observation->user_id = $this->request->session()->read('Auth.User.id');
            $observation->observation = '[Estado: ' . $this->request->data['state'] . '] ' . $message;
            $observation->model_id = $id;
            if ($this->Observations->save($observation)) {
                //debug('epic win');
            }
            foreach($deals as $k => $de) {
                $deals[$k]->state = $this->request->data['state'];
                if($this->Deals->save($deals[$k])) {
                    //debug('Deal: master of the universe');
                }
            }
        }

        $this->Flash->success('Se ha actualizado el estado del trato: '.strtoupper($this->request->data['state']));
        return $this->redirect($this->referer());
    }

    public function comment($id = null)
    {
        if($id == null) {
            $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
            return $this->redirect($this->referer());
        }
        $group_id = $this->request->session()->read('Auth.User.group_id');
        $deal = $this->Deals->get($id);
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Deals->Users->getUserBuildings($this->request->session()->read('Auth.User.id'));
            if (count($user_buildings) > 0) {
                $buildings = array_combine($user_buildings,$user_buildings);
                $budget_id = $this->Deals->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                ])->first();
            } else {
                $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                return $this->redirect(['controller' => 'users', 'action' => 'index']);
            }
            if (!empty($budget_id->building_id) && $budget_id != null) {
                if ($budget_id->id != $deal->budget_id) {
                    $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde a los tratos. Por favor, edite la información de usuario.');
                    return $this->redirect(['controller' => 'users', 'action' => 'index']);
                }
            } else {
                $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
                return $this->redirect(['action' => 'index']);
            }
        }
        $this->loadModel('Observations');
        $deal_id = $id;
        $observations = $this->Observations->find('all')
            ->where(['Observations.model_id =' => $deal_id, 'Observations.model =' => 'Deals'])
            ->order(['Observations.created' => 'DESC'])
            ->contain(['Users'])->toArray();
        $observation = $this->Observations->newEntity();
        //4 gg, 3 gf, 2 cp,5 jefe rrhh,6 visitador, 7 adm obra.
        $group = $this->request->session()->read('Auth.User.group_id');
        $valid_group = array(USR_GRP_COORD_PROY, USR_GRP_GE_GRAL, USR_GRP_GE_FINAN, USR_GRP_JEFE_RRHH, USR_GRP_VISITADOR, USR_GRP_ADMIN_OBRA);
        if (!empty($group)) {
            if (in_array($group, $valid_group)) {
                if ($this->request->is('post')) {
                    $observation = $this->Observations->patchEntity($observation, $this->request->data);
                    $observation->model = $this->modelClass;
                    $observation->action = $this->request->params['action'];
                    $observation->model_id = $deal_id;
                    $observation->user_id = $this->request->session()->read('Auth.User.id');
                    if ($this->Observations->save($observation)) {
                        $this->Flash->success('El comentario ha sido guardado correctamente.');
                        return $this->redirect(['action' => 'view',$id]);
                    } else {
                        $this->Flash->error('The observation could not be saved. Please, try again.');
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
        $this->set(compact('observation', 'users', 'observations','deal_id'));
        $this->set('_serialize', ['observation']);
    }

}



