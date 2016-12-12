<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\View\View;
use Cake\Filesystem\File;
use Cake\Network\Email\Email;
use Cake\Utility\Hash;
use App\View\Helper\AccessHelper;

/**
 * PaymentStatements Controller
 *
 * @property \App\Model\Table\PaymentStatementsTable $PaymentStatements */
class PaymentStatementsController extends AppController
{
    /**
     * [beforeFilter description]
     * @param  Event  $event [description]
     * @return [type]        [description]
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        //validar que el presupuesto no esté finalizado o sin aprobar
        $current_action = $this->request->params['action'];
        if ($current_action == 'add' ||
            $current_action == 'edit' ||
            $current_action == 'delete' ||
            //$current_action == 'add_continuos' ||
            $current_action == 'approved_or_rejected') {
            if (count($this->request->params['pass']) > 0) {
                if ($this->request->params['controller'] == 'PaymentStatements' && $current_action != 'add') {
                    $payment_statement = $this->PaymentStatements->get($this->request->params['pass'][0]);
                    $current_state = $this->PaymentStatements->Budgets->current_budget_state($payment_statement->budget_id);
                    if (empty($current_state) && $current_state == null) {
                        $this->Flash->info('El presupuesto de la obra no está configurado, no puede agregar información adicional.');
                        return $this->redirect(['action' => 'index']);
                    } else {
                        if ($current_state == -1) {
                            $this->Flash->info('La obra está bloqueada, no puede agregar información adicional.');
                            return $this->redirect(['action' => 'index']);
                        } else {
                            if ( in_array($current_state, [1, 2, 3, 6, 7, 9, 10]) ) {
                                $this->Flash->info('El presupuesto de la obra se encuentra en estados Pendiente Aprobación o Finalizado, no puede agregar información adicional.');
                                return $this->redirect(['action' => 'index']);
                            }
                        }
                    }
                } else {
                    $current_state = $this->PaymentStatements->Budgets->current_budget_state($this->request->params['pass'][0]);
                    if (empty($current_state) && $current_state == null) {
                        $this->Flash->info('El presupuesto de la obra no está configurado, no puede agregar información adicional.');
                        return $this->redirect(['action' => 'index']);
                    } else {
                        if ($current_state == -1) {
                            $this->Flash->info('La obra está bloqueada, no puede agregar información adicional.');
                            return $this->redirect(['action' => 'index']);
                        } else {
                            if ( in_array($current_state, [1, 2, 3, 6, 7, 9, 10]) ) {
                                $this->Flash->info('El presupuesto de la obra se encuentra en estados Pendiente Aprobación o Finalizado, no puede agregar información adicional.');
                                return $this->redirect(['action' => 'index']);
                            }
                        }
                    }
                }
            }
        }
    }


    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
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
            $user_buildings = $this->PaymentStatements->Users->getUserBuildings($this->request->session()->read('Auth.User.id'));
            if (count($user_buildings) > 0) {
                $budget = $this->PaymentStatements->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
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
            $buildings = $this->PaymentStatements->Budgets->Buildings->getActiveBuildingsWithSoftlandInfo();
            $last_building = $this->request->session()->read('Config.last_building');
            if(!empty($this->request->query)){
                if(!empty($this->request->query['building_id'])){
                    $budget = $this->PaymentStatements->Budgets->find('all', [
                        'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                        'conditions' => ['Budgets.building_id' => $this->request->query['building_id']]
                    ])->first();
                }
            }
            else{
                 if(!empty($last_building)) {
                    $budget = $this->PaymentStatements->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => $last_building]
                ])->first();
                } else {
                     $budget = $this->PaymentStatements->Budgets->find('all', [
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
        //permiso para link aprobar/rechazar
        //solo para gerente general y gerente finanzas.
        $permisos['gerentes'] = false;
        if (in_array($this->request->session()->read('Auth.User.group_id'), [USR_GRP_GE_GRAL, USR_GRP_GE_FINAN])) {
            $permisos['gerentes'] = true;
        }

        $this->paginate = [
            'conditions' => ['budget_id'=>$budget->id, 'payment_statement_parent_id IS NULL'],
            'contain' => ['Budgets'=>['Currencies'], 'Users','PaymentStatementStates'],
            'order' => ['PaymentStatements.created' => 'DESC'],
        ];
        $paymentStatements = $this->paginate($this->PaymentStatements)->toArray();
        foreach ($paymentStatements as $key=>$ps) {
            //Validar que tenga nueva versión, si la tiene, se debe mostrar esa
            $getLastVersion = $this->PaymentStatements->find('all', [
                'conditions' =>[
                    'PaymentStatements.payment_statement_parent_id' => $ps->id
                ],
                'contain' => ['Budgets'=>['Currencies'], 'Users','PaymentStatementStates'],
                'order' => ['PaymentStatements.version_number' => 'DESC'],
            ])->first();
            if(!empty($getLastVersion)){
                $paymentStatements[$key] = $getLastVersion;
            }
        }
        $this->set(compact('budget', 'buildings','permisos', 'sf_building', 'paymentStatements'));
    }

    /**
     * View method
     *
     * @param string|null $id Payment Statement id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $budget = null;
        $buildings = null;
        $extraRow=['avance_porcentaje'=>0,'avance_monto'=>0,'previo_porcentaje'=>0,'previo_monto'=>0, 'presente_porcentaje'=>0,'presente_monto'=>0, ];
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if (empty($id) && $id == null) {
            $this->Flash->error('Ocurrió un error al obtener la información de la generación de Estado de Pago. Por favor, inténtelo nuevamente');
            return $this->redirect(['action' => 'index']);
        }
        $paymentStatement = $this->PaymentStatements->get($id, [
                'contain' => [
                    'Budgets' => [
                        'Buildings' =>['BuildingsUsers'=>['Users']],
                        'CurrenciesValues' => ['Currencies']
                    ],
                    'Users',
                    'PaymentStatementStates'
                ]
            ]);
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->PaymentStatements->Users->getUserBuildings($this->request->session()->read('Auth.User.id'));
            if (count($user_buildings) > 0) {
                $budget = $this->PaymentStatements->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                ])->first();
                if (!empty($budget) && $budget != null) {
                    if ($budget->id != $paymentStatement->budget_id) {
                        $this->Flash->error('El estado de pago no corresponde a la obra asociada al usuario');
                        return $this->redirect(['action' => 'index']);
                    }
                } else {
                    $this->Flash->error('El usuario no está asociado a la Obra del Estado de Pago');
                    return $this->redirect(['action' => 'index']);
                }
            }
        }
        ($budget == null) ? $budget = $this->PaymentStatements->Budgets->get($paymentStatement->budget_id, [
           'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]]]) : '';

        $contract = [];
        $contract['costo_directo'] = ($budget->total_cost);
        $contract['general_costs'] = ($budget->general_costs);
        $contract['utilities'] = ($budget->total_cost + $budget->general_costs) * ($budget->utilities / 100);
        $contract['total_currency'] = ($budget->total_cost + $budget->general_costs + $contract['utilities']);
        $this->set(compact('contract'));

        $extraRow['total_cost'] =$budget->total_cost;

        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
        ])->first();
        // payment_statemes_budget
        $payment_statements_ids = $this->PaymentStatements->find('list', [
            'keyField' => 'id',
            'valueField' => 'gloss',
            'conditions' => ['PaymentStatements.budget_id' => $budget->id],
            // 'contain' => ['BudgetItemsPaymentStatements' => ['BudgetItems']],
            'order' => 'created DESC'])->toArray();

        $budget_id = $budget->id;
        //completed budget_items
        $paymentStatement_budgetItems_completed = $this->PaymentStatements->BudgetItemsPaymentStatements->find('list', [
            'keyField' => 'budget_item_id',
            'valueField' => 'progress',
            'conditions' => ['BudgetItemsPaymentStatements.payment_statement_id IN' => array_keys($payment_statements_ids), 'BudgetItemsPaymentStatements.progress' => 100],
        ])->toArray();

        $last_payment_statement = $this->PaymentStatements->find('all')->where(['PaymentStatements.id <>' => $id, 'PaymentStatements.client_approval' => 1])->order(['PaymentStatements.id' => 'DESC'])->select(['id'])->first();

        $paymentStatement_budgetItems_completed_all = array();

        if(!is_null($last_payment_statement))
        {
            $paymentStatement_budgetItems_completed_all = $this->PaymentStatements->BudgetItemsPaymentStatements->find('all', [
                'conditions' => ['BudgetItemsPaymentStatements.progress' => 100],
                'contain' => [
                    'PaymentStatements' => function ($q) use ($budget_id){
                       return $q
                            ->where(['PaymentStatements.budget_id' => $budget_id, 'PaymentStatements.client_approval' => 1])
                            ->order(['PaymentStatements.id' => 'DESC']);
                    }
                ],
                'order' => ['BudgetItemsPaymentStatements.budget_item_id' => 'ASC']
            ])->toArray();
        }

        


        /*echo '<pre>';
        print_r($paymentStatement_budgetItems_completed_all);
        echo '</pre>';*/

        $payment_statements = $this->PaymentStatements->find('all', [
            'conditions' => ['PaymentStatements.budget_id' => $budget->id, 'payment_statement_parent_id IS NULL'],
            //'contain' => ['BudgetItemsPaymentStatements' => ['BudgetItems']],
            'order' => 'created ASC'])->toArray();

        $noButtons=false;
        foreach ($payment_statements as $key=>$ps) {
            //Validar que tenga nueva versión, si la tiene, se debe mostrar esa
            $getLastVersion = $this->PaymentStatements->find('all', [
                'conditions' =>[
                    'PaymentStatements.payment_statement_parent_id' => $ps->id
                ],
                'contain' => ['Budgets'=>['Currencies'], 'Users','PaymentStatementStates'],
                'order' => ['PaymentStatements.version_number' => 'DESC'],
            ])->first();
            if(!empty($getLastVersion)){
                $payment_statements[$key] = $getLastVersion;
                // Validar si el actual estado de pago tiene versión actualizada, en caso que tenga no deben mostrar acciones
                if(
                    $getLastVersion->version_number > $paymentStatement->version_number &&
                    (($getLastVersion->payment_statement_parent_id == $paymentStatement->payment_statement_parent_id) ||
                    ($paymentStatement->id == $getLastVersion->payment_statement_parent_id && $getLastVersion->version_number > $paymentStatement->version_number))
                ){
                    $noButtons=true;
                }
            }
        }

        $paymentStatement_budgetItems = $this->PaymentStatements->BudgetItemsPaymentStatements->find('all', [
            'conditions' => ['BudgetItemsPaymentStatements.payment_statement_id' => $paymentStatement->id],
        ])->toArray();
        $paymentStatement_budgetItems_ordered = array();
        $list_budget_items = array();
        //debug($paymentStatement_budgetItems); //die();
        foreach ($paymentStatement_budgetItems as $paymentStatement_budgetItem) {
            $paymentStatement_budgetItems_ordered[$paymentStatement_budgetItem->budget_item_id] = $paymentStatement_budgetItem;
            $list_budget_items[] = $paymentStatement_budgetItem->budget_item_id;
            $extraRow['avance_porcentaje'] += $paymentStatement_budgetItem->progress;
            $extraRow['avance_monto'] += $paymentStatement_budgetItem->progress_value;
            $extraRow['previo_porcentaje'] += $paymentStatement_budgetItem->previous_progress;
            $extraRow['previo_monto'] += $paymentStatement_budgetItem->previous_progress_value;
            $extraRow['presente_porcentaje'] += $paymentStatement_budgetItem->overall_progress;
            $extraRow['presente_monto'] += $paymentStatement_budgetItem->overall_progress_value;
        }
        

        /*echo '<pre>';
        print_r($extraRow);
        echo '</pre>';*/

        //$extraRow['avance_monto'] = $extraRow['avance_monto'] + $extraRow['previo_monto'];

        $extraRow['avance_porcentaje'] = ($extraRow['avance_monto']*100)/$extraRow['total_cost'];
        $extraRow['previo_porcentaje'] = ($extraRow['previo_monto']*100)/$extraRow['total_cost'];
        $extraRow['presente_porcentaje'] = ($extraRow['presente_monto']*100)/$extraRow['total_cost'];

        /*echo '<pre>';
        print_r($extraRow);
        echo '</pre>';*/

        // pr($extraRow);
        //filtro para saber si es un payment de items originales o items adicionales.
        $type_payment_statement = array('originales' => 0, 'adicionales' => 0);
        $type_payment_statement['originales'] = $tmp_bi = $this->PaymentStatements->Budgets->BudgetItems->find('all', [
            'conditions' => ['id IN' => $list_budget_items, 'BudgetItems.disabled' => 0, 'BudgetItems.extra' => 0]
            ])->count();
        $type_payment_statement['adicionales'] = $tmp_bi = $this->PaymentStatements->Budgets->BudgetItems->find('all', [
            'conditions' => ['id IN' => $list_budget_items, 'BudgetItems.disabled' => 0, 'BudgetItems.extra' => 1]
            ])->count();
        $type_payment_statement = ($type_payment_statement['originales'] > $type_payment_statement['adicionales']) ? 'originales' : 'adicionales';
        $this->set(compact('type_payment_statement'));
        //fin filtro
        // $parent_sum = $this->PaymentStatements->Budgets->calc_parent_totals($paymentStatement->budget_id);
        //$paymentStatement_budgetItems = Hash::combine($paymentStatement_budgetItems, 'BudgetItems.id');
        // Load budgetItems
        $bi = $this->PaymentStatements->Budgets->BudgetItems->find('all', [
            'conditions' => ['budget_id' => $paymentStatement->budget_id, 'parent_id IS' => null, 'BudgetItems.disabled' => 0]
            ]);
        $budget_items = array();
        foreach ($bi as $value) {
            $children = $this->PaymentStatements->Budgets->BudgetItems
            ->find('children', ['for' => $value->id])
            ->find('threaded')
            // Progress ordenados descendente por avance proyectado
            ->contain([
                'Units',
                'Progress' => function ($q) {
                    return $q
                        ->order(['Progress.proyected_progress_percent' => 'DESC']);
                    },
                'BudgetItemsPaymentStatements'/* => function ($q) {
                    return $q
                        ->order(['BudgetItemsPaymentStatements.previous_progress' => 'ASC']);
                    },*/
            ])->toArray();
            $budget_items[$value->id] = $value->toArray();
            $budget_items[$value->id]['children'] = $children;
        }
        // pr($budget_items);
        //bloqueo de botones
        $permisos['approve'] =  false;
        $permisos['reject'] =  false;
        if ($group_id == 4 && $paymentStatement->payment_statement_state_id == 2) {
            $permisos['approve'] =  true;
            $permisos['reject'] =  true;
        } elseif ($group_id == 3 && ($paymentStatement->payment_statement_state_id == 2 || $paymentStatement->payment_statement_state_id ==3 )) {
            $permisos['approve'] =  true;
            $permisos['reject'] =  true;
        }
        // Datos comunes
        // $datos = $this->PaymentStatements->sharedData($budget,$paymentStatement);
        // Items para EDP
        // $budget_items = $this->PaymentStatements->itemsEdpView($paymentStatement);
        // Estados de pagos anteriores
        // $payments = $this->PaymentStatements->find()->where(['budget_id' => $paymentStatement->budget_id, 'id <= ' => $paymentStatement->id])->toArray();
        $this->set(compact('paymentStatement', 'payment_statements', 'budget', 'sf_building', 'paymentStatement_budgetItems_ordered',
          'paymentStatement_budgetItems_completed', 'budget_items', 'permisos', 'extraRow', 'id', 'noButtons', 'paymentStatement_budgetItems_completed_all'));
        $this->set('_serialize', ['paymentStatement']);
    }


    public function history($id){

        $budget = null;
        $buildings = null;
        $extraRow=['avance_porcentaje'=>0,'avance_monto'=>0,'previo_porcentaje'=>0,'previo_monto'=>0, 'presente_porcentaje'=>0,'presente_monto'=>0, ];
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if (empty($id) && $id == null) {
            $this->Flash->error('Ocurrió un error al obtener la información de la generación de Estado de Pago. Por favor, inténtelo nuevamente');
            return $this->redirect(['action' => 'index']);
        }
        $paymentStatement = $this->PaymentStatements->get($id, [
            'contain' => [
                'Budgets' => [
                    'Buildings' =>['BuildingsUsers'=>['Users']],
                    'CurrenciesValues' => ['Currencies']
                ],
                'Users',
                'PaymentStatementStates'
            ]
        ]);
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->PaymentStatements->Users->getUserBuildings($this->request->session()->read('Auth.User.id'));
            if (count($user_buildings) > 0) {
                $budget = $this->PaymentStatements->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Currencies' => ['Valoresmonedas' => function ($q) {return $q->limit(1);}], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates'],
                    'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                ])->first();
                if (!empty($budget) && $budget != null) {
                    if ($budget->id != $paymentStatement->budget_id) {
                        $this->Flash->error('El estado de pago no corresponde a la obra asociada al usuario');
                        return $this->redirect(['action' => 'index']);
                    }
                } else {
                    $this->Flash->error('El usuario no está asociado a la Obra del Estado de Pago');
                    return $this->redirect(['action' => 'index']);
                }
            }
        }
        ($budget == null) ? $budget = $this->PaymentStatements->Budgets->get($paymentStatement->budget_id, [
           'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Currencies' => ['Valoresmonedas' => function ($q) {return $q->limit(1);}], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates']]) : '';

        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
        ])->first();
        $payment_statements = $this->PaymentStatements->find('all', [
            'conditions' => [
                'OR' => [
                    'PaymentStatements.id' => $paymentStatement->payment_statement_parent_id,
                    'PaymentStatements.payment_statement_parent_id' => $paymentStatement->payment_statement_parent_id,
                ]
            ],
            'contain' => ['Users'],
            'order' => 'PaymentStatements.created ASC'])->toArray();

        $this->set(compact('payment_statements', 'budget', 'sf_building'));
    }


    /**
     * Agregar nuevo estado de pago
     * @param  int $budget_id identificadpr de presupuesto
     * @author Diego De la Cruz <diego.delacruz@ideauno.cl>
     */
    public function add($budget_id = '')
    {
        $paymentStatement = $this->PaymentStatements->newEntity();
        $budget = null;
        $buildings = null;
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if (!empty($budget_id) && $budget_id != null) {
            if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
                $user_buildings = $this->PaymentStatements->Users->getUserBuildings($this->request->session()->read('Auth.User.id'));
                if (count($user_buildings) > 0) {
                    $budget = $this->PaymentStatements->Budgets->get($budget_id, [
                        'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates'],
                    ]);
                    if (!empty($budget) && $budget != null) {
                        if ($budget->building_id != $user_buildings[0]) {
                            $this->Flash->error('El presupuesto para el Estado de Pago no corresponde a la Obra asociada al Usuario');
                            return $this->redirect(['action' => 'index']);
                        }
                    } else {
                        $this->Flash->error('El usuario no está asociado a la Obra del Estado de Pago');
                        return $this->redirect(['action' => 'index']);
                    }
                }
            }
            (is_null($budget)) ?
                $budget = $this->PaymentStatements->Budgets->get($budget_id, [
                    'contain' => [
                        'Buildings' => ['BuildingsUsers' => ['Users']], 
                        'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                        }],
                        'CurrenciesValues' => ['Currencies'], 
                        'Users', 
                        'BudgetApprovals',
                        'BudgetApprovals.BudgetStates'
                    ],
                ]) : '';
            //información general
            $this->loadModel('SfBuildings');
            $sf_building = $this->SfBuildings->find('all', [
                 'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
            ])->first();
            // payment_statemes_budget
            $payment_statements_ids = $this->PaymentStatements->find('list', [
                'keyField' => 'id',
                'valueField' => 'gloss',
                'conditions' => ['PaymentStatements.budget_id' => $budget_id],
                // 'contain' => ['BudgetItemsPaymentStatements' => ['BudgetItems']],
                'order' => 'created DESC'])->toArray();
            //last payment_statement
            $payment_statements = $this->PaymentStatements->find('all', [
                'conditions' => ['PaymentStatements.budget_id' => $budget_id, 'PaymentStatements.client_approval' => 1],
                'contain' => ['PaymentStatementStates'],
                'order' => ['PaymentStatements.created' => 'DESC', 'PaymentStatements.version_number' => 'DESC']]);
            $last_payment_statement = $payment_statements->first();


            if ( $last_payment_statement && in_array($last_payment_statement->payment_statement_state->id, [1, 2, 3, 5, 8, 9, 10]) ) {
                //$this->Flash->info('El último estado de pago ingresado se encuentra en estados Pendiente Aprobación o Finalizado, no puede agregar información adicional.');
                //return $this->redirect(['controller' => 'payment_statements', 'action' => 'index', '?' => ['building_id'=>$budget->building_id]]);
            }

            //completed budget_items
            if(count($payment_statements_ids) > 0) {
                /*$paymentStatement_budgetItems_completed = $this->PaymentStatements->BudgetItemsPaymentStatements->find('list', [
                    'keyField' => 'budget_item_id',
                    'valueField' => 'progress',
                    'conditions' => ['BudgetItemsPaymentStatements.payment_statement_id IN' => array_keys($payment_statements_ids), 'BudgetItemsPaymentStatements.progress' => 100],
                ])->toArray();*/
                $paymentStatement_budgetItems_completed_back = $this->PaymentStatements->BudgetItemsPaymentStatements->find('all', [
                    /*'keyField' => 'budget_item_id',
                    'valueField' => 'progress',*/
                    'conditions' => ['BudgetItemsPaymentStatements.payment_statement_id IN' => array_keys($payment_statements_ids), 'BudgetItemsPaymentStatements.progress' => 100],
                ])->toArray();

                $paymentStatement_budgetItems_completed = array();

                foreach($paymentStatement_budgetItems_completed_back as $completed)
                {
                    $paymentStatement_budgetItems_completed[$completed['budget_item_id']]['progress'] = $completed['progress'];
                    $paymentStatement_budgetItems_completed[$completed['budget_item_id']]['previous_progress'] = $completed['previous_progress'];
                    $paymentStatement_budgetItems_completed[$completed['budget_item_id']]['overall_progress'] = $completed['overall_progress'];
                    $paymentStatement_budgetItems_completed[$completed['budget_item_id']]['progress_value'] = $completed['progress_value'];
                    $paymentStatement_budgetItems_completed[$completed['budget_item_id']]['previous_progress_value'] = $completed['previous_progress_value'];
                    $paymentStatement_budgetItems_completed[$completed['budget_item_id']]['overall_progress_value'] = $completed['overall_progress_value'];
                }

            } else {
                $paymentStatement_budgetItems_completed = array();
            }

            /*echo '<pre>';
            print_r($paymentStatement_budgetItems_completed);
            echo '</pre>';*/

            //existing budget_items
            $paymentStatement_budgetItems = $this->PaymentStatements->BudgetItemsPaymentStatements->find('all', [
                'conditions' => ['BudgetItemsPaymentStatements.payment_statement_id' => $last_payment_statement['id']],
            ])->toArray();


            $last_paymentStatement_budgetItems_ordered = array();
            foreach ($paymentStatement_budgetItems as $paymentStatement_budgetItem) {
                $last_paymentStatement_budgetItems_ordered[$paymentStatement_budgetItem->budget_item_id] = $paymentStatement_budgetItem;
            }
            //debug($last_paymentStatement_budgetItems_ordered);

            $parent_sum = $this->PaymentStatements->Budgets->calc_parent_totals($budget_id);
            // Load budgetItems
            $bi = $this->PaymentStatements->Budgets->BudgetItems->find('all', [
                'conditions' => ['budget_id' => $budget_id, 'parent_id IS' => null, 'BudgetItems.disabled' => 0]
                ]);
            $budget_items = array();
            foreach ($bi as $value) {
                $children = $this->PaymentStatements->Budgets->BudgetItems
                ->find('children', ['for' => $value->id])
                ->find('threaded')
                // Progress ordenados descendente por avance proyectado
                ->contain([
                    'Units',
                    'Progress' => function ($q) {
                        return $q
                            ->order(['Progress.proyected_progress_percent' => 'DESC']);
                        }
                ])->toArray();
                $budget_items[$value->id] = $value->toArray();
                $budget_items[$value->id]['children'] = $children;
            }
            $this->set(compact('budget', 'budget_items', 'parent_sum', 'sf_building', 'paymentStatement', 'last_payment_statement', 'last_paymentStatement_budgetItems_ordered',
              'paymentStatement_budgetItems_completed'));
        } else {
            $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente');
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if (!empty($this->request->data) && $this->request->data != null && count($this->request->data['budget_item']) > 0) {
                // cálculos EDP
                // Historial EDP's
                // Avance Presente EDP : % | currency (valor trabajos efectuados presente EDP = acumaldo - anterior) *
                // Avance Anterior EDP : % | currency (valor trabajos efectuados anterior) *
                // Avance Acumulado : % | currency (valor trabajos efectuados a la fecha) *
                // Descuento retenciones: currency valor presente EDP * % retenciones *
                // Descuento devolucion anticipo: currency valor presente EDP * % retenciones *
                // Liquido a pagar : currency  valor presente - descuentos *
                // valor currency budget original *
                // valor currency del EDP add *
                // Total neto : currency Liquido a pagar por currency fecha add *
                // IVA : total neto * 0.19 *
                // Total: total neto + iva *
                // total budget *
                // Total anticipo : total bugdet * % anticipo *
                // pagado a la fecha : valor trabajos efectuados anterior *
                // avance presente EP : valor trabajos efectuados a la fecha *
                // Saldo por pagar :  total budget - total anticipo - pagado a la fecha - avance presente EP
                // 2019935671 + 224212859.48 = 2244148530.48
                // 2244148530.48 / 24627.1 = 91125.16
                $this->request->data['presentation_date'] = new \DateTime($this->request->data['presentation_date']);
                $this->request->data['billing_date'] = new \DateTime($this->request->data['billing_date']);
                $this->request->data['estimation_pay_date'] = new \DateTime($this->request->data['estimation_pay_date']);

                $contract = [];
                $contract['costo_directo'] = ($budget->total_cost);
                $contract['general_costs'] = ($budget->general_costs);
                $contract['utilities'] = ($budget->total_cost + $budget->general_costs) * ($budget->utilities / 100);
                $contract['total_currency'] = ($budget->total_cost + $budget->general_costs + $contract['utilities']);
                $paymentStatement->budget_id = $budget->id;
                $paymentStatement->version_number = 1;
                $paymentStatement->draft = 1;
                $paymentStatement->first_approval = NULL;
                $paymentStatement->second_approval = NULL;
                $paymentStatement->third_approval = NULL;
                $paymentStatement->email_sent = NULL;
                $paymentStatement->client_approval = NULL;
                $paymentStatement->decline_obs = NULL;
                $paymentStatement->contract_value = $contract['total_currency'];
                $paymentStatement->gloss = $this->request->data['gloss'];
                $paymentStatement->presentation_date = $this->request->data['presentation_date'];
                $paymentStatement->billing_date = $this->request->data['billing_date'];
                $paymentStatement->estimation_pay_date = $this->request->data['estimation_pay_date'];
                $paymentStatement->currency_value_to_date = $this->request->data['currency_value_to_date'];
                $paymentStatement->user_created_id = $this->request->session()->read('Auth.User.id');
                $last_edp_payment = (is_null($last_payment_statement)) ? 0 : $last_payment_statement->total_cost;
                $total_direct_cost = 0;
                $total_general_costs = 0;
                $total_utilities = 0;
                $total_cost_currency_edp = 0;
                $budget_items_with_values = 0;
                
                $total_cost_to_date = 0;
                $total_cost_last = 0;
                $total_cost_present = 0;

                $budget_items_array = array();



                if ($this->PaymentStatements->save($paymentStatement)) {
                    foreach ($this->request->data['budget_item'] as $budget_item_id => $budget_item) {
                        if (!empty($budget_item['progress']) && $budget_item['progress'] != null) {
                            $budgetItemPaymentStatement = $this->PaymentStatements->BudgetItemsPaymentStatements->newEntity();
                            $budgetItemPaymentStatement->payment_statement_id = $paymentStatement->id;
                            $budgetItemPaymentStatement->budget_item_id = $budget_item['id'];
                            $budgetItemPaymentStatement->overall_progress_value = ($budget_item['overall_progress_value'] == '') ? 0 : $budget_item['overall_progress_value'];
                            $budgetItemPaymentStatement->overall_progress = ($budget_item['overall_progress'] == '') ? 0 : $budget_item['overall_progress'];
                            $budgetItemPaymentStatement->previous_progress = ($budget_item['previous_progress'] == '') ? 0 : $budget_item['previous_progress'];
                            $budgetItemPaymentStatement->previous_progress_value = ($budget_item['previous_progress_value'] == '') ? 0 : $budget_item['previous_progress_value'];
                            $budgetItemPaymentStatement->progress = ($budget_item['progress'] == '') ? 0 : $budget_item['progress'];
                            $budgetItemPaymentStatement->progress_value = ($budget_item['progress_value'] == '') ? 0 : $budget_item['progress_value'];

                            $total_cost_to_date += ($budget_item['progress_value'] == '') ? 0 : $budget_item['progress_value'];
                            $total_cost_last += ($budget_item['previous_progress_value'] == '') ? 0 : $budget_item['previous_progress_value'];
                            $total_cost_present += ($budget_item['overall_progress_value'] == '') ? 0 : $budget_item['overall_progress_value'];

                            //determinar que porcentaje del costo directo del proyecto representa
                            $percentage = $budget_item['progress_value'] / $contract['costo_directo'];

                            $budget_items_array[$budget_item['id']] = $budget_item['id'];
                            //esto es equivalente a
                            /*
                            $total_cost_currency_edp += $percentage * $contract['total_currency'];
                             */
                            $total_direct_cost += (
                                // $budget_item['progress_value'] == $percentage * $contract['costo_directo']
                                $percentage * $contract['costo_directo']
                            );

                            $total_cost_currency_edp =  $total_cost_currency_edp  + (
                                // $budget_item['progress_value'] == $percentage * $contract['costo_directo']
                                $percentage * $contract['costo_directo'] +
                                $percentage * $contract['general_costs'] +
                                $percentage * $contract['utilities']
                            );



                            if ($this->PaymentStatements->BudgetItemsPaymentStatements->save($budgetItemPaymentStatement)) {
                                $budget_items_with_values++;
                            }
                        }
                    }

                    if ($budget_items_with_values == 0) {
                        // Generar una versión
                        // Se debe crear un registro
                        $this->PaymentStatements->delete($paymentStatement->id);
                        $this->Flash->error('Ocurrió un error al guardar la información ingresada. Por favor, inténtelo nuevamente');
                        return $this->redirect(['action' => 'index']);
                    }

                    

                    /************* SUMA DE LOS VALORES DE LAS PARTIDAS ANTERIORES ************/
                    if(!is_null($last_payment_statement))
                    {
                        $this->loadModel('BudgetItemsPaymentStatements');

                        $last_progress =  $this->BudgetItemsPaymentStatements
                                    ->find('all')
                                    ->where(['BudgetItemsPaymentStatements.payment_statement_id <>' => $paymentStatement->id, 'BudgetItemsPaymentStatements.budget_item_id NOT IN' => array_keys($budget_items_array)])
                                    ->order(['BudgetItemsPaymentStatements.budget_item_id' => 'ASC'])
                                    ->toArray();

                        $last_progress_porc = 0;
                        $last_budget_item_id = 0;
                        $x=0;
                        $y=0;
                        $last_progress_real_data = array();

                        foreach($last_progress as $lp)
                        {
                            if($x == 0)
                            {
                                $last_budget_item_id = $lp->budget_item_id;
                                $last_progress_porc = $lp->progress;

                                $last_progress_real_data[$y] = $lp;
                                $y++;
                                $x++;
                            }
                            else
                            {
                                if($last_budget_item_id == $lp->budget_item_id)
                                {
                                    if($lp->progress > $last_progress_porc)
                                    {
                                        $last_progress_real_data[$y-1] = $lp;
                                        $last_budget_item_id = $lp->budget_item_id;
                                        $last_progress_porc = $lp->progress;
                                    }
                                }
                                else
                                {
                                    $last_progress_real_data[$y] = $lp;
                                    $last_budget_item_id = $lp->budget_item_id;
                                    $last_progress_porc = $lp->progress;

                                    $y++;
                                }
                            }
                            
                        }

                        foreach($last_progress_real_data as $bips)
                        {
                            if($bips->previous_progress == 0)
                            {
                                $total_cost_to_date += ($bips->progress_value == '') ? 0 : $bips->progress_value;
                                $total_cost_last += ($bips->progress_value == '') ? 0 : $bips->progress_value;
                            }
                            else
                            {
                                $total_cost_to_date += ($bips->progress_value == '') ? 0 : $bips->progress_value;
                                $total_cost_last += ($bips->previous_progress_value == '') ? 0 : $bips->previous_progress_value;
                                $total_cost_present += ($bips->overall_progress_value == '') ? 0 : $bips->overall_progress_value;
                            }
                        }
                    }

                    /************************************************************************/

                    $paymentStatement->total_direct_cost_to_date = $total_cost_to_date;
                    $paymentStatement->total_direct_cost_last = $total_cost_last;
                    $paymentStatement->total_direct_cost_present = $total_cost_present;

                    
                    
                    $paymentStatement->total_direct_cost = $total_cost_to_date;



                    $paymentStatement->total_percent_to_date = ($paymentStatement->total_direct_cost_to_date / $contract['costo_directo']) * 100;
                    $paymentStatement->total_percent_last = ($paymentStatement->total_direct_cost_last / $contract['costo_directo']) * 100;
                    $paymentStatement->total_percent_present = ($paymentStatement->total_direct_cost_present / $contract['costo_directo']) * 100;



                    $paymentStatement->progress_present_payment_statement = $paymentStatement->total_direct_cost_to_date + (($contract['general_costs'] * $paymentStatement->total_percent_to_date) / 100) + (($contract['utilities'] * $paymentStatement->total_percent_to_date) / 100);

                    $paymentStatement->paid_to_date = $paymentStatement->total_direct_cost_last + (($contract['general_costs'] * $paymentStatement->total_percent_last) / 100) + (($contract['utilities'] * $paymentStatement->total_percent_last) / 100);

                    $paymentStatement->total_cost = $paymentStatement->total_direct_cost_present + (($contract['general_costs'] * $paymentStatement->total_percent_present) / 100) + (($contract['utilities'] * $paymentStatement->total_percent_present) / 100);


                    $paymentStatement->overall_progress = ($paymentStatement->total_cost * 100) / $contract['total_currency'];
                    
                    $paymentStatement->discount_retentions =  ($budget->retentions / 100) * $paymentStatement->total_cost;
                    $paymentStatement->discount_advances = ($budget->advances / 100) * $paymentStatement->total_cost;
                    $paymentStatement->liquid_pay = $paymentStatement->total_cost - $paymentStatement->discount_advances - $paymentStatement->discount_retentions;


                    $paymentStatement->total_net = round($paymentStatement->liquid_pay * $this->request->data['currency_value_to_date'], 2);
                    $paymentStatement->tax = round($paymentStatement->total_net * 0.19, 2);

                    $paymentStatement->total = round($paymentStatement->total_net + $paymentStatement->tax, 2);
                    //$paymentStatement->paid_to_date = $last_edp_payment;
                    $paymentStatement->balance_due = round($budget->total_cost - ($budget->total_cost * ($budget->retentions / 100)) - $last_edp_payment - $paymentStatement->progress_present_payment_statement);
                    $paymentStatement->payment_statement_state_id = 2;
                    
                    if ($this->PaymentStatements->save($paymentStatement)) {
                        
                        $this->Flash->success('Se ha creado correctamente el estado de pago');
                        return $this->redirect(['action' => 'view', $paymentStatement->id]);
                    }
                    $this->Flash->error('Ocurrió un error al validar la información ingresada. Por favor, inténtelo nuevamente');
                    //return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error('Ocurrió un error al guardar la información ingresada. Por favor, inténtelo nuevamente');
                }
            } else {
                $this->Flash->error('Ocurrió un error al validar la información ingresada. Por favor, inténtelo nuevamente');
                return $this->redirect(['action' => 'index']);
            }
        }
    }


    /**
     * Edit method
     *
     * @param string|null $id Payment Statement id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     * @author Diego De la Cruz B. <diego.delacruz@ideauno.cl>
     */
    public function edit($id = null)
    {
        $budget = null;
        $buildings = null;
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if (!empty($id) && $id != null) {
            //Obtener información del estado de pago
            $paymentStatementTmp = $this->PaymentStatements->get($id);

            // Obtener información del último estado de pago versionado del registro encontrado
            if($paymentStatementTmp->version_number!=null){
                $paymentStatement = $this->PaymentStatements->find('all', [
                    'conditions' => [
                        'PaymentStatements.budget_id' => $paymentStatementTmp->budget_id,
                        'PaymentStatements.payment_statement_parent_id' => $paymentStatementTmp->id,
                        'PaymentStatements.client_approval' => 1
                    ],
                    'order' => [
                        'version_number' => 'DESC'
                    ]
                ])->first();


            }
            if(empty($paymentStatement)) $paymentStatement = $paymentStatementTmp;
            if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
                $user_buildings = $this->PaymentStatements->Users->getUserBuildings($this->request->session()->read('Auth.User.id'));
                if (count($user_buildings) > 0) {
                    $budget = $this->PaymentStatements->Budgets->get($paymentStatement->budget_id, [
                        'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                        }]],
                    ]);
                    if (!empty($budget) && $budget != null) {
                        if ($budget->building_id != $user_buildings[0]) {
                            $this->Flash->error('El presupuesto para el Estado de Pago no corresponde a la Obra asociada al Usuario');
                            return $this->redirect(['action' => 'index']);
                        }
                    } else {
                        $this->Flash->error('El usuario no está asociado a la Obra del Estado de Pago');
                        return $this->redirect(['action' => 'index']);
                    }
                }
            }


            (is_null($budget)) ?
                $budget = $this->PaymentStatements->Budgets->get($paymentStatement->budget_id, [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                        }]],
                ]) : '';
            //información general
            $this->loadModel('SfBuildings');
            $sf_building = $this->SfBuildings->find('all', [
                 'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
            ])->first();
            // payment_statemes_budget
            $payment_statements_ids = $this->PaymentStatements->find('list', [
                'keyField' => 'id',
                'valueField' => 'gloss',
                'conditions' => ['PaymentStatements.budget_id' => $budget->id],
                // 'contain' => ['BudgetItemsPaymentStatements' => ['BudgetItems']],
                'order' => 'created DESC'])->toArray();
            //completed budget_items
            $paymentStatement_budgetItems_completed = $this->PaymentStatements->BudgetItemsPaymentStatements->find('list', [
                'keyField' => 'budget_item_id',
                'valueField' => 'progress',
                'conditions' => ['BudgetItemsPaymentStatements.payment_statement_id IN' => array_keys($payment_statements_ids), 'BudgetItemsPaymentStatements.progress' => 100],
            ])->toArray();
            //existig budget_items
            $paymentStatement_budgetItems = $this->PaymentStatements->BudgetItemsPaymentStatements->find('all', [
                'conditions' => ['BudgetItemsPaymentStatements.payment_statement_id' => $paymentStatement->id],
            ])->toArray();

            //debug($paymentStatement_budgetItems);
            //last payment_statement
            $payment_statements = $this->PaymentStatements->find('all', [
              'conditions' => ['PaymentStatements.budget_id' => $paymentStatement->budget_id, 'PaymentStatements.client_approval' => 1,'NOT' => ['PaymentStatements.id' => $id]],
              // 'contain' => ['BudgetItemsPaymentStatements' => ['BudgetItems']],
              'order' => 'created DESC']);
            $last_payment_statement = $payment_statements->first();
            //debug($last_payment_statement);
            $paymentStatement_budgetItems_ordered = array();
            $list_budget_items = array();
            foreach ($paymentStatement_budgetItems as $paymentStatement_budgetItem) {
                $paymentStatement_budgetItems_ordered[$paymentStatement_budgetItem->budget_item_id] = $paymentStatement_budgetItem;
                $list_budget_items[] = $paymentStatement_budgetItem->budget_item_id;
            }
            //filtro para saber si es un payment de items originales o items adicionales.
            $type_payment_statement = array('originales' => 0, 'adicionales' => 0);
            $type_payment_statement['originales'] = $tmp_bi = $this->PaymentStatements->Budgets->BudgetItems->find('all', [
                'conditions' => ['id IN' => $list_budget_items, 'BudgetItems.disabled' => 0, 'BudgetItems.extra' => 0]
                ])->count();
            $type_payment_statement['adicionales'] = $tmp_bi = $this->PaymentStatements->Budgets->BudgetItems->find('all', [
                'conditions' => ['id IN' => $list_budget_items, 'BudgetItems.disabled' => 0, 'BudgetItems.extra' => 1]
                ])->count();
            $type_payment_statement = ($type_payment_statement['originales'] > $type_payment_statement['adicionales']) ? 'originales' : 'adicionales';
            $this->set(compact('type_payment_statement'));
            //fin filtro
            $parent_sum = $this->PaymentStatements->Budgets->calc_parent_totals($paymentStatement->budget_id);
            // Load budgetItems
            $bi = $this->PaymentStatements->Budgets->BudgetItems->find('all', [
                'conditions' => ['budget_id' => $paymentStatement->budget_id, 'parent_id IS' => null, 'BudgetItems.disabled' => 0]
                ]);
            $budget_items = array();
            foreach ($bi as $value) {
                $children = $this->PaymentStatements->Budgets->BudgetItems
                ->find('children', ['for' => $value->id])
                ->find('threaded')
                // Progress ordenados descendente por avance proyectado
                ->contain([
                    'Units',
                    'Progress' => function ($q) {
                        return $q
                            ->order(['Progress.proyected_progress_percent' => 'DESC']);
                        }
                ])->toArray();
                $budget_items[$value->id] = $value->toArray();
                $budget_items[$value->id]['children'] = $children;
            }
            $this->set(compact('budget', 'budget_items', 'parent_sum', 'sf_building', 'paymentStatement', 'last_payment_statement', 'paymentStatement_budgetItems_ordered',
              'paymentStatement_budgetItems_completed'));
        } else {
            $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente');
            return $this->redirect(['action' => 'index']);
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            if (!empty($this->request->data) && $this->request->data != null && count($this->request->data['budget_item']) > 0) {
                // cálculos EDP
                // Historial EDP's
                // Avance Presente EDP : % | currency (valor trabajos efectuados presente EDP = acumaldo - anterior) *
                // Avance Anterior EDP : % | currency (valor trabajos efectuados anterior) *
                // Avance Acumulado : % | currency (valor trabajos efectuados a la fecha) *
                // Descuento retenciones: currency valor presente EDP * % retenciones *
                // Descuento devolucion anticipo: currency valor presente EDP * % retenciones *
                // Liquido a pagar : currency  valor presente - descuentos *
                // valor currency budget original *
                // valor currency del EDP add *
                // Total neto : currency Liquido a pagar por currency fecha add *
                // IVA : total neto * 0.19 *
                // Total: total neto + iva *
                // total budget *
                // Total anticipo : total bugdet * % anticipo *
                // pagado a la fecha : valor trabajos efectuados anterior *
                // avance presente EP : valor trabajos efectuados a la fecha *
                // Saldo por pagar :  total budget - total anticipo - pagado a la fecha - avance presente EP
                $this->request->data['presentation_date'] = new \DateTime($this->request->data['presentation_date']);
                $this->request->data['billing_date'] = new \DateTime($this->request->data['billing_date']);
                $this->request->data['estimation_pay_date'] = new \DateTime($this->request->data['estimation_pay_date']);
                $contract = [];
                $contract['costo_directo'] = ($budget->total_cost);
                $contract['general_costs'] = ($budget->general_costs);
                $contract['utilities'] = ($budget->total_cost + $budget->general_costs) * ($budget->utilities / 100);
                $contract['total_currency'] = ($budget->total_cost + $budget->general_costs + $contract['utilities']);
                $version_number=($paymentStatement->version_number+1);
                $newVersionPS = $this->PaymentStatements->newEntity();
                $newVersionPS->version_number = $version_number;
                $newVersionPS->payment_statement_parent_id = ($paymentStatement->payment_statement_parent_id=="")?$paymentStatement->id:$paymentStatement->payment_statement_parent_id;
                $newVersionPS->budget_id = $budget->id;
                $newVersionPS->contract_value = $contract['total_currency'];
                $newVersionPS->gloss = $this->request->data['gloss'];
                $newVersionPS->presentation_date = $this->request->data['presentation_date'];
                $newVersionPS->billing_date = $this->request->data['billing_date'];
                $newVersionPS->estimation_pay_date = $this->request->data['estimation_pay_date'];
                $newVersionPS->currency_value_to_date = $this->request->data['currency_value_to_date'];
                $newVersionPS->user_created_id = $this->request->session()->read('Auth.User.id');
                $last_edp_payment = (is_null($last_payment_statement)) ? 0 : $last_payment_statement->total_cost;
                $total_direct_cost = 0;
                $total_cost_currency_edp = 0;
                $budget_items_with_values = 0;
                
                $total_cost_to_date = 0;
                $total_cost_last = 0;
                $total_cost_present = 0;

                if ($this->PaymentStatements->save($newVersionPS)) {

                    $this->loadModel('BudgetItemsPaymentStatements');
                    /*debug($this->request->data['budget_item']);
                    die();*/
                    // $paymentStatementId=$this->PaymentStatements->id;
                    foreach ($this->request->data['budget_item'] as $budget_item_id => $budget_item) {
                        if (!empty($budget_item['progress']) && $budget_item['progress'] != null) {
                            /*if (!empty($budget_item['budget_item_statement_payment_id']) && $budget_item['budget_item_statement_payment_id'] != null) {
                                $budgetItemPaymentStatement = $this->PaymentStatements->BudgetItemsPaymentStatements->get($budget_item['budget_item_statement_payment_id']);
                                $budgetItemPaymentStatement->payment_statement_id = $paymentStatement->id;
                                $budgetItemPaymentStatement->budget_item_id = $budget_item['id'];
                                $budgetItemPaymentStatement->overall_progress_value = ($budget_item['overall_progress_value'] == '') ? 0 : $budget_item['overall_progress_value'];
                                $budgetItemPaymentStatement->overall_progress = ($budget_item['overall_progress'] == '') ? 0 : $budget_item['overall_progress'];
                                $budgetItemPaymentStatement->previous_progress = ($budget_item['previous_progress'] == '') ? 0 : $budget_item['previous_progress'];
                                $budgetItemPaymentStatement->previous_progress_value = ($budget_item['previous_progress_value'] == '') ? 0 : $budget_item['previous_progress_value'];
                                $budgetItemPaymentStatement->progress = ($budget_item['progress'] == '') ? 0 : $budget_item['progress'];
                            } else {*/
                                if(is_null($newVersionPS->client_approval))
                                {
                                    $budgetItemPaymentStatement = $this->BudgetItemsPaymentStatements->newEntity();
                                }
                                else
                                {
                                    $budgetItemPaymentStatement = $this->BudgetItemsPaymentStatements
                                                                ->find('all')
                                                                ->where(['budget_item_id' => $budget_item['id']])
                                                                ->first();
                                }   

                                
                                $budgetItemPaymentStatement->payment_statement_id = $newVersionPS->id;
                                $budgetItemPaymentStatement->budget_item_id = $budget_item['id'];
                                $budgetItemPaymentStatement->overall_progress_value = ($budget_item['overall_progress_value'] == '') ? 0 : $budget_item['overall_progress_value'];
                                $budgetItemPaymentStatement->overall_progress = ($budget_item['overall_progress'] == '') ? 0 : $budget_item['overall_progress'];
                                $budgetItemPaymentStatement->previous_progress = ($budget_item['previous_progress'] == '') ? 0 : $budget_item['previous_progress'];
                                $budgetItemPaymentStatement->previous_progress_value = ($budget_item['previous_progress_value'] == '') ? 0 : $budget_item['previous_progress_value'];
                                $budgetItemPaymentStatement->progress = ($budget_item['progress'] == '') ? 0 : $budget_item['progress'];
                                $budgetItemPaymentStatement->progress_value = ($budget_item['progress_value'] == '') ? 0 : $budget_item['progress_value'];

                                $total_cost_to_date += ($budget_item['progress_value'] == '') ? 0 : $budget_item['progress_value'];
                                $total_cost_last += ($budget_item['previous_progress_value'] == '') ? 0 : $budget_item['previous_progress_value'];
                                $total_cost_present += ($budget_item['overall_progress_value'] == '') ? 0 : $budget_item['overall_progress_value'];
                            // }
                            //determinar que porcentaje del costo directo del proyecto representa
                            $percentage = $budget_item['progress_value'] / $contract['costo_directo'];

                            //esto es equivalente a
                            /*
                            $total_cost_currency_edp += $percentage * $contract['total_currency'];
                             */
                            $total_direct_cost += (
                                // $budget_item['progress_value'] == $percentage * $contract['costo_directo']
                                $percentage * $contract['costo_directo']
                            );
                            $total_cost_currency_edp =  $total_cost_currency_edp  + (
                                // $budget_item['progress_value'] == $percentage * $contract['costo_directo']
                                $percentage * $contract['costo_directo'] +
                                $percentage * $contract['general_costs'] +
                                $percentage * $contract['utilities']
                            );

                            //$total_cost_currency_edp += $budget_item['progress_value'];

                            //debug($budgetItemPaymentStatement);

                            if ($this->BudgetItemsPaymentStatements->save($budgetItemPaymentStatement)) {
                                $budget_items_with_values++;
                            }
                        }
                    }
                    if ($budget_items_with_values == 0) {
                        $this->PaymentStatements->delete($newVersionPS->id);
                        $this->Flash->error('Ocurrió un error al guardar la información ingresada. Por favor, inténtelo nuevamente');
                        return $this->redirect(['action' => 'edit', $id]);
                    }


                    if(!is_null($last_payment_statement))
                    {
                        $this->loadModel('BudgetItemsPaymentStatements');

                        $last_progress =  $this->BudgetItemsPaymentStatements
                                    ->find('all')
                                    ->where(['BudgetItemsPaymentStatements.payment_statement_id <>' => $paymentStatement->id, 'BudgetItemsPaymentStatements.budget_item_id NOT IN' => array_keys($budget_items_array)])
                                    ->order(['BudgetItemsPaymentStatements.budget_item_id' => 'ASC'])
                                    ->toArray();

                        $last_progress_porc = 0;
                        $last_budget_item_id = 0;
                        $x=0;
                        $y=0;
                        $last_progress_real_data = array();

                        foreach($last_progress as $lp)
                        {
                            if($x == 0)
                            {
                                $last_budget_item_id = $lp->budget_item_id;
                                $last_progress_porc = $lp->progress;

                                $last_progress_real_data[$y] = $lp;
                                $y++;
                                $x++;
                            }
                            else
                            {
                                if($last_budget_item_id == $lp->budget_item_id)
                                {
                                    if($lp->progress > $last_progress_porc)
                                    {
                                        $last_progress_real_data[$y-1] = $lp;
                                        $last_budget_item_id = $lp->budget_item_id;
                                        $last_progress_porc = $lp->progress;
                                    }
                                }
                                else
                                {
                                    $last_progress_real_data[$y] = $lp;
                                    $last_budget_item_id = $lp->budget_item_id;
                                    $last_progress_porc = $lp->progress;

                                    $y++;
                                }
                            }
                            
                        }

                        foreach($last_progress_real_data as $bips)
                        {
                            if($bips->previous_progress == 0)
                            {
                                $total_cost_to_date += ($bips->progress_value == '') ? 0 : $bips->progress_value;
                                $total_cost_last += ($bips->progress_value == '') ? 0 : $bips->progress_value;
                            }
                            else
                            {
                                $total_cost_to_date += ($bips->progress_value == '') ? 0 : $bips->progress_value;
                                $total_cost_last += ($bips->previous_progress_value == '') ? 0 : $bips->previous_progress_value;
                                $total_cost_present += ($bips->overall_progress_value == '') ? 0 : $bips->overall_progress_value;
                            }
                        }
                    }

                    $newVersionPS->total_direct_cost_to_date = $total_cost_to_date;
                    $newVersionPS->total_direct_cost_last = $total_cost_last;
                    $newVersionPS->total_direct_cost_present = $total_cost_present;

                    $newVersionPS->total_direct_cost = $total_cost_to_date;


                    $newVersionPS->total_percent_to_date = ($newVersionPS->total_direct_cost_to_date / $contract['costo_directo']) * 100;
                    $newVersionPS->total_percent_last = ($newVersionPS->total_direct_cost_last / $contract['costo_directo']) * 100;
                    $newVersionPS->total_percent_present = ($newVersionPS->total_direct_cost_present / $contract['costo_directo']) * 100;



                    $newVersionPS->progress_present_payment_statement = $newVersionPS->total_direct_cost_to_date + (($contract['general_costs'] * $newVersionPS->total_percent_to_date) / 100) + (($contract['utilities'] * $newVersionPS->total_percent_to_date) / 100);

                    $newVersionPS->paid_to_date = $newVersionPS->total_direct_cost_last + (($contract['general_costs'] * $newVersionPS->total_percent_last) / 100) + (($contract['utilities'] * $newVersionPS->total_percent_last) / 100);

                    $newVersionPS->total_cost = $newVersionPS->total_direct_cost_present + (($contract['general_costs'] * $newVersionPS->total_percent_present) / 100) + (($contract['utilities'] * $newVersionPS->total_percent_present) / 100);


                    $newVersionPS->overall_progress = ($newVersionPS->total_cost * 100) / $contract['total_currency'];


                    $newVersionPS->discount_retentions =  ($budget->retentions / 100) * $newVersionPS->total_cost;
                    $newVersionPS->discount_advances = ($budget->advances / 100) * $newVersionPS->total_cost;
                    $newVersionPS->liquid_pay = $newVersionPS->total_cost - $newVersionPS->discount_advances - $newVersionPS->discount_retentions;


                    $newVersionPS->total_net = round($newVersionPS->liquid_pay * $this->request->data['currency_value_to_date'], 2);
                    $newVersionPS->tax = round($newVersionPS->total_net * 0.19, 2);

                    $newVersionPS->total = round($newVersionPS->total_net + $newVersionPS->tax, 2);
                    //$paymentStatement->paid_to_date = $last_edp_payment;
                    $newVersionPS->balance_due = round($budget->total_cost - ($budget->total_cost * ($budget->retentions / 100)) - $last_edp_payment - $newVersionPS->progress_present_payment_statement);

                    /*$state_modified = false;
                    switch ($newVersionPS->payment_statement_state_id) {
                        case 5:
                            $newVersionPS->payment_statement_state_id = 2;
                            $state_modified = true;
                            break;

                        default:
                            break;
                    }*/
                    if ($this->PaymentStatements->save($newVersionPS)) {
                        //go to view

                        /*if( $state_modified ){
                            $this->PaymentStatements->PaymentStatementApprovals->addState($newVersionPS->id, $newVersionPS->payment_statement_state_id, 'Cambio de estado tras edición');
                        }*/

                        $this->Flash->success('Se ha guardado correctamente el estado de pago');
                        //return $this->redirect(['action' => 'view', $newVersionPS->id]);
                        return $this->redirect(['action' => 'view', $newVersionPS->id]);
                    }
                    $this->Flash->error('Ocurrió un error al validar la información ingresada. Por favor, inténtelo nuevamente');
                    //return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error('Ocurrió un error al guardar la información ingresada. Por favor, inténtelo nuevamente');
                }
            } else {
                $this->Flash->error('Ocurrió un error al validar la información ingresada. Por favor, inténtelo nuevamente');
                return $this->redirect(['action' => 'edit', $id]);
            }
        }
    }

    public function add_ajax($value = '')
    {
        # code...
    }

    /**
     * Add_old method DEPRECATED
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add_old($budget_id = null)
    {

        // Presupuesto
        $budget = $this->PaymentStatements->Budgets->get($budget_id,[
            'contain' => [
                'Buildings' =>['BuildingsUsers'=>['Users']],
                'CurrenciesValues' => ['Currencies']
            ]
        ]);


        $budget_id = $budget->id;

        // Validar que exista avances aprobados
        // sin estado de pago asociado y mayor a cero
        $progress = $this->PaymentStatements->Progress->find()
            ->where(['payment_statement_id IS'=> null, 'overall_progress_percent >' => 0])
            ->join([
                'table' => 'schedules',
                'alias' => 'Schedules',
                'conditions' => ['Progress.schedule_id = Schedules.id', 'Schedules.progress_approved' => true]
            ])
            ->group(['Progress.id'])->toArray();
        if(empty($progress)){
            $this->Flash->error('No existen avances para poder Generar Estado de Pago');
            return $this->redirect(['action'=>'index','?'=>['building_id' => $budget->building_id]]);
        }

        // Datos de Obra
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->get($budget->building->softland_id);
        $visitador = 'n/a';
        $admin_obra = 'n/a';
        foreach ($budget->building->buildings_users as $key => $b_user) {
            if($b_user->user->group_id == 6){
                //Visitador
                $visitador = $b_user->user->full_name;
            }
            elseif($b_user->user->group_id ==7){
                // Admin Obra
                $admin_obra = $b_user->user->full_name;
            }
        }


        // items del presupuesto
        $budget_items = $this->PaymentStatements->itemsEdpAdd($budget_id);

        // step 2
        $paymentStatement = $this->PaymentStatements->newEntity();
        if ($this->request->is('post')) {

            //  Valor de Moneda
            switch (strtolower($budget['currencies_values'][0]['currency']['name'])) {
                case 'peso':
                    //Nothing
                    $moneda_nombre = null;
                    $moneda_actual_valor = 1;
                    break;
                case 'dolar':
                    $moneda_nombre = "dolar";
                    $index_name = "Dolares";
                    break;
                case 'uf':
                    $moneda_nombre = "uf";
                    $index_name = "UFs";
                    break;
                // case 'Euro':
                //     $moneda_nombre = "euro";
                //     break;
                default:
                    // Peso Default ??
                    $moneda_nombre = null;
                    $moneda_actual_valor = 1;
                    break;
            }

            if(!is_null($moneda_nombre)){
                $api_url = "http://api.sbif.cl/api-sbifv3/recursos_api/".$moneda_nombre."?apikey=eb97a85a6f7f5dfb7308612b09917a01852081d3&formato=json";
                $json = file_get_contents($api_url);
                $data = json_decode($json);
                $moneda_actual_valor = $data->{$index_name}[0]->Valor;
                $sin_punto = str_replace('.', '', $moneda_actual_valor);
                $coma_por_punto = str_replace(',','.', $sin_punto);
                $moneda_actual_valor = round($coma_por_punto,2);
            }


            //
            // BUDGET => Presupuesto
            //

            // Porcentajes
            $avance_per_ppto = $budget['advances']/100;
            $retencion_per_ppto = $budget['retentions']/100;
            $utilidad_per_ppto = $budget['utilities']/100;

            // VALOR MONEDA Presupuesto
            $moneda = $budget['currencies_values'][0]['value'];

            // total costo directo ppto. (costo de los items)
            // No incluye gastos generales ni utilidad.
            // supuesto: se ingrese gastos generales en moneda setiada.
            $costo_directo_ppto_moneda = round($budget['total_cost'] / $moneda,2);
            $gastos_generales_ppto_moneda = $budget['general_costs'];
            $utilidad_ppto_moneda = round($utilidad_per_ppto * ($costo_directo_ppto_moneda + $gastos_generales_ppto_moneda),2);
            $costo_total_presupuesto_moneda = $costo_directo_ppto_moneda + $gastos_generales_ppto_moneda + $utilidad_ppto_moneda;
            $total_anticipo_moneda = $costo_total_presupuesto_moneda * $avance_per_ppto;



            // PAYMENT

            // costo_directo_edp basado en items que se avanzó
            $costo_directo_edp = 0;
            $costo_directo_edp_moneda = 0;
            $costo_directo_a_la_fecha_moneda = 0;
            $avance_a_la_fecha_moneda = 0;
            foreach ($this->request->data['BudgetItems'] as $bud_id => $budget_item) {
                // si se avanzo
                if($budget_item['success']==1){
                    //Costo solo en los que se avanzó
                    $costo_directo_edp += $budget_item['monto'];
                    $costo_directo_edp_moneda += $budget_item['monto_moneda'];
                }
                //suma total de avances
                $costo_directo_a_la_fecha_moneda += $budget_item['monto_moneda'];
                $avance_a_la_fecha_moneda += $budget_item['monto_a_la_fecha'];
            }


            // Costo directo edp partido por total del costo directo del ppto.
            // Avance a la fecha en porcentaje
            $avance_per_edp = round($avance_a_la_fecha_moneda/$costo_directo_ppto_moneda,4);
            $paymentStatement->overall_progress = $avance_per_edp * 100;


            // Valor en pesos de la moneda del presupuesto
            $paymentStatement->contract_value_uf = $moneda;

            // costo directo en Moneda => avance de items en moneda para el EDP = avance actual - avance anterior
            $paymentStatement->total_direct_cost = round($costo_directo_a_la_fecha_moneda,2);


            // Necesito avance anterior

            // Avance pendiente Estado de Pago
            // Monto a pagar en el presente estado de pago.
            // Necesito estado de pago anterior.
            // ( Avance de % a la fecha  - Avance Anterior %  ) * monto
            $ultimo_edp = $this->PaymentStatements->find()
                ->where(['budget_id' => $budget_id])->order(['id' => 'DESC'])
                ->first();
            if(empty($ultimo_edp)){
                $ultimo_edp['total_direct_cost'] = 0;
                $ultimo_edp['overall_progress'] = 0;
                $ultimo_edp['advance_present_payent_statement_uf'] = 0;
            }


            // Avance presente - Avance anterior
            $avance_real_edp = $avance_per_edp  - ($ultimo_edp['overall_progress']/100);

            // Gasto general y Utilidad proporcionales al avance real
            $gastos_generales_edp = round($avance_real_edp * $gastos_generales_ppto_moneda,2);
            $utilidad_edp = round($avance_real_edp * $utilidad_ppto_moneda,2);

            // SUMA de costo_directo + gastos generales + utilidad para EDP
            $total_edp_sin_descuento = round($costo_directo_a_la_fecha_moneda + $gastos_generales_edp + $utilidad_edp,2);

            // Retenciones proporcionales. generalmente 5%
            $paymentStatement->discount_retentions_uf = round($total_edp_sin_descuento * $retencion_per_ppto,2);

            // Devolucion de anticipo proporcional Ejemplo: 20%
            // valor presente EDP * porcentaje de anticipo => proporcional del anticipo
            $paymentStatement->discount_refund_advances_uf = round($total_edp_sin_descuento * $avance_per_ppto,2);


            // VALOR DE MONEDA AL DIA
            $paymentStatement->uf_value_to_date = $moneda_actual_valor;


            // Avances acumulados
            // Suponiendo que estan pagados
            $query = $this->PaymentStatements->find();
            $acumulado = $query
                ->select(['sum'=> $query->func()->sum('advance_present_payent_statement_uf')])
                ->where(['budget_id' => $budget_id])
                ->toArray();

            if(!is_null($acumulado[0]['sum'])){
                $avances_acumulados = $acumulado[0]['sum'];
            }
            else{
                $avances_acumulados = 0;
            }


            // AVANCE PRESENTE EDP
            $paymentStatement->advance_present_payent_statement_uf = $total_edp_sin_descuento;

            // PAGO LIQUIDO = neto - descuentos
            // valor_presente_estado_de_pago - descuento_por_retenciones - descuento_devolucion_anticipo
            // pago liquido esta en moneda
            $pago_liquido_uf = $paymentStatement->advance_present_payent_statement_uf - $paymentStatement->discount_retentions_uf - $paymentStatement->discount_refund_advances_uf;
            $paymentStatement->liquid_pay_uf = round($pago_liquido_uf,2);

            // Pagado a la Fecha => avances acumulados a la fecha
            $paymentStatement->paid_to_date_uf = $avances_acumulados;

            // avance acumulado??
            //$paymentStatement->advance_uf = ($total_edp_sin_descuento * $avance_per_ppto)/$moneda;

            // Saldo por pagar
            // valor contrato - anticipo - pagado a la fecha - avance pdte en Moneda ppto.
            $balance = $costo_total_presupuesto_moneda - $total_anticipo_moneda - $avances_acumulados - $paymentStatement->advance_present_payent_statement_uf;
            $paymentStatement->balance_due_uf = round($balance,2);


           // Total neto y en Moneda
           $paymentStatement->total_net = round($pago_liquido_uf * $moneda_actual_valor); //pesos
           $paymentStatement->tax = round($paymentStatement->total_net * 0.19); //pesos
           // neto + tax = total
           $paymentStatement->total = $paymentStatement->total_net + $paymentStatement->tax; //pesos

           // total en moneda
           $paymentStatement->total_cost_uf = round($paymentStatement->total/$moneda_actual_valor,2);

            // id ppto y avance
           $paymentStatement->budget_id = $budget_id;

           // Usuario
           $user_id = $this->request->session()->read('Auth.User.id');
           $paymentStatement->user_created_id = $user_id;
           $paymentStatement->user_modified_id = $user_id;


           // Lista de planificaciones para despues asociar Estado de pago  con avances (progress)
            $lista_de_planificaciones = $this->PaymentStatements->Progress->Schedules->find('list',['keyField' => 'id','valueField' => 'id'])
                                    ->where(['budget_id' => $budget_id])
                                    ->toArray();
            $lista_de_planificaciones = array_keys($lista_de_planificaciones);


           // Datos de EDP

            // Glosa
            $paymentStatement->gloss = $this->request->data['gloss'];
            // Formato Fechas
            $aux = Time::createFromFormat('d-m-Y',$this->request->data['presentation_date'],'America/Santiago');
            $aux = $aux->i18nFormat('YYYY-MM-dd 00:00:00');
            $paymentStatement->presentation_date = $aux;

            $aux = Time::createFromFormat('d-m-Y',$this->request->data['billing_date'],'America/Santiago');
            $aux = $aux->i18nFormat('YYYY-MM-dd 00:00:00');
            $paymentStatement->billing_date = $aux;

            $aux = Time::createFromFormat('d-m-Y',$this->request->data['estimation_pay_date'],'America/Santiago');
            $aux = $aux->i18nFormat('YYYY-MM-dd 00:00:00');
            $paymentStatement->estimation_pay_date = $aux;


            // guardo en session datos para paso 2
            $this->request->session()->write('paymentStatement', $paymentStatement);
            $this->request->session()->write('lista_de_planificaciones', $lista_de_planificaciones);

            $costo_directo = ['costo_directo_clp' =>$costo_directo_edp, 'costo_directo_moneda' =>$costo_directo_edp_moneda,'costo_directo_a_la_fecha'=> $costo_directo_a_la_fecha_moneda];
            $this->request->session()->write('costo_directo', $costo_directo);

            return $this->redirect(['action' => 'add_continuos',$budget_id]);


        }


        $this->set(compact('budget','sf_building','admin_obra','visitador','budget_items','paymentStatement'));
        //$this->set('_serialize', ['paymentStatement']);
        $this->set('budget_id', $budget_id);
    }


    public function add_continuos($budget_id){


        // Presupuesto
        $budget = $this->PaymentStatements->Budgets->get($budget_id,[
            'contain' => [
                'Buildings' =>['BuildingsUsers'=>['Users']],
                'CurrenciesValues' => ['Currencies']
            ]
        ]);

        // Datos de Obra
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->get($budget->building->softland_id);
        $visitador = 'n/a';
        $admin_obra = 'n/a';
        foreach ($budget->building->buildings_users as $key => $b_user) {
            if($b_user->user->group_id == 6){
                //Visitador
                $visitador = $b_user->user->full_name;
            }
            elseif($b_user->user->group_id ==7){
                // Admin Obra
                $admin_obra = $b_user->user->full_name;
            }
        }


        // EDP anterior!
        $ultimo_edp = $this->PaymentStatements->find()
                ->where(['PaymentStatements.budget_id' => $budget_id])
                ->order(['PaymentStatements.id' => 'DESC'])
                ->contain([
                        'Progress' => function($q){
                            return $q
                                ->select(['id', 'overall_progress_percent'])
                                ->order(['Progress.overall_progress_percent' => 'DESC']);
                            }
                    ])
                ->first();

        if(empty($ultimo_edp)){
            $ultimo_edp['overall_progress'] = 0;
            $ultimo_edp['total_direct_cost'] = 0;
            $ultimo_edp['advance_present_payent_statement_uf'] = 0;
        }


        // items del presupuesto
        $budget_items = $this->PaymentStatements->itemsEdpAdd($budget_id);

        // edp en proceso de creacion y lista de planificaciones
        $paymentStatement = $this->request->session()->read('paymentStatement');
        $lista_de_planificaciones = $this->request->session()->read('lista_de_planificaciones');


        $datos['moneda']['nombre']= $budget['currencies_values'][0]['currency']['name'];
        $datos['moneda']['valor'] = $budget['currencies_values'][0]['value'];

        $costo_directo = round($budget['total_cost']/$datos['moneda']['valor'],2);
        $utilidad =  round( ($costo_directo +  $budget['general_costs']) * $budget['utilities']/100,2);
        $total_contrato = $costo_directo + $budget['general_costs'] + $utilidad;

        $datos['contrato']['total'] = $total_contrato;
        $datos['contrato']['anticipo'] = $budget['advances']/100 * $total_contrato;

        // Avance Presente EDP. %edp - $edp_anterior
        $datos['edp']['percent'] = (!empty($ultimo_edp['overall_progress']))? $paymentStatement->overall_progress - $ultimo_edp['overall_progress'] : $paymentStatement->overall_progress;

        $datos['costo_directo']['total_moneda'] = round($budget['total_cost']/$datos['moneda']['valor'],2);
        $datos['costo_directo']['a_la_fecha'] = round($ultimo_edp['total_direct_cost'] + $paymentStatement->total_direct_cost,2);

        $datos['gastos_generales']['total_moneda'] = $budget['general_costs'];
        $datos['gastos_generales']['a_la_fecha'] = round($datos['gastos_generales']['total_moneda'] * $paymentStatement->overall_progress/100,2);
        $datos['gastos_generales']['edp'] = round($datos['gastos_generales']['total_moneda'] * $datos['edp']['percent']/100,2);

        $datos['utilidad']['total_moneda'] = $utilidad;
        $datos['utilidad']['a_la_fecha'] = round($utilidad * $paymentStatement->overall_progress/100,2);
        $datos['utilidad']['edp'] = round($datos['utilidad']['total_moneda'] * $datos['edp']['percent']/100,2);


        $ultimo_edp['gastos_generales_moneda'] = round($datos['gastos_generales']['total_moneda'] * $ultimo_edp['overall_progress']/100,2);
        $ultimo_edp['utilidad_moneda'] = round($datos['utilidad']['total_moneda'] * $ultimo_edp['overall_progress']/100,2);



        if ($this->request->is('post')) {

            $this->PaymentStatements->newEntity();

           //SAVE
           //$paymentStatement = $this->PaymentStatements->patchEntity($paymentStatement, $this->request->data);
            if ($this->PaymentStatements->save($paymentStatement)) {

                //crear payment_statement_approval para guardar la historia de los estados que va tomando
                $this->PaymentStatements->PaymentStatementApprovals->addState($paymentStatement->id,2,'Estado de Pago creado. En espera de aprobación gerente Finanzas');

                //Actualizar los avances de las planificaciones en el PPTO involucrados en la generación
                // del Estado de Pago.
                // Uso de función de actualización masiva definicion en Modelo Progress
                if($this->PaymentStatements->Progress->setPayment($paymentStatement->id,$lista_de_planificaciones)){

                    // borro vairables de session usadas
                    $this->request->session()->delete('paymentStatement');
                    $this->request->session()->delete('lista_de_planificaciones');
                    $this->request->session()->delete('costo_directo');

                   $this->Flash->success('Estado de Pago creado correctamente.');
                   return $this->redirect(['action' => 'index', '?' => ['building_id' => $budget->building_id]]);
                }

            } else {
                $this->Flash->error('Ocurrió un error al crear un Estado de Pago. Intente Nuevamente.');
            }
        }

        $this->set(compact('budget','sf_building','admin_obra','visitador','budget_items','paymentStatement','datos','ultimo_edp'));
        //$this->set('_serialize', ['paymentStatement']);
        $this->set('budget_id', $budget_id);

    }




    /**
     * Delete method
     *
     * @param string|null $id Payment Statement id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $paymentStatement = $this->PaymentStatements->get($id);
        $budget = $this->PaymentStatements->Budgets->get($paymentStatement->budget_id);
        $this->loadModel('Observations');
        if ($this->PaymentStatements->delete($paymentStatement)) {
            $this->PaymentStatements->PaymentStatementApprovals->deleteAll(['payment_statement_id'=>$id]);
            $this->PaymentStatements->BudgetItemsPaymentStatements->deleteAll(['payment_statement_id'=>$id]);
            $this->Observations->deleteAll(['model' => 'PaymentStatements', 'model_id'=>$id]);
            $this->Flash->success('Estado de pago eliminado.');
        } else {
            $this->Flash->error('Ocurrió un error al eliminar el Estado de Pago.');
        }
        return $this->redirect(['action'=>'index']);
    }


    /**
     * sendEmail methop
     * @param  array $excel        ruta del excel generado
     * @param  array $pdf          ruta del pdf generado
     * @param  string $clientEmail correo de cliente
     * @return array $response     Estado del envio
     */
    public function sendEmail($excel = null,$pdf = null,$clientEmail = null)
    {
        $to = $clientEmail;
        $files = [];
        $response = [];
        $response['excel'] = false;
        $response['pdf'] = false;
        $email = new Email('default');
        $email->from(['ldz.cpo@gmail.com' => 'LDZ Constructora'])
        ->to($to)
        ->subject('Estado de Pago');

        // files for atach
        if(!is_null($excel) && isset($excel['path'])){
            $files[$excel['fileName']] =  [
                        'file' => $excel['path'],
                        'mimetype' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ];
            $response['excel'] = true;
        }
        if(!is_null($pdf) && isset($pdf['path'])){
             $files[$pdf['fileName']] =  [
                        'file' => $pdf['path'],
                        'mimetype' => 'application/pdf'
            ];
            $response['pdf'] = true;
        }

        // attach to email
        if(!empty($files)){
            $email->attachments($files);
        }

        try {
            $email->send();
            $response['status'] = true;
        } catch (Exception $e) {
            $response['status'] = false;
            $response['error']  = $e->getMessage();
        }

        return $response;

    }



    /**
     * comment methop
     *
     * @param  integer $id_payment identificador de estado de pago
     * @param  integer $budget_id  identificador de presupuesto
     * @return void  redirect a la misma vista agregando el comentario nuevo
     */
    public function comment($id_payment){

        $this->loadModel('Observations');


        $comments = $this->Observations->find()
            ->contain(['Users'])
            ->where(['model'=>'PaymentStatements','model_id'=>$id_payment,'action'=>'comment'])
            ->order(['Observations.created'=> 'DESC'])
            ->toArray();


        $paymentStatement = $this->PaymentStatements->get($id_payment,[
            'contain' => ['Budgets']
        ]);

        $comment = $this->Observations->newEntity();
        if ($this->request->is('post')) {

            $comment->model = 'PaymentStatements';
            $comment->action = 'comment';
            $comment->user_id = $this->request->session()->read('Auth.User.id');
            $comment->model_id = $id_payment;
            $comment->observation = $this->request->data['observation'];

            //$comment = $this->Observations->patchEntity($comment, $this->request->data);
            if($this->Observations->save($comment)){
                $this->Flash->success('Comentario agregado agregado a Estado de Pago.');
                 return $this->redirect(['action' => 'comment',$paymentStatement->id]);
            }
            else{
                $this->Flash->error('Error: No se agregó el comentario.');
            }
        }

        $this->set(compact('comments','comment','id_payment','paymentStatement'));

    }


    /**
     * approved_or_rejected methop
     * @param  integer $id identificador de Estado de Pago (paymentstatement)
     * @return void
     */
    public function approved_or_rejected($id){
        // check grupo
        $user_id = $this->request->session()->read('Auth.User.id');
        $user_group = $this->request->session()->read('Auth.User.group_id');
        if(!in_array($user_group,[3,4])){
            $this->Flash->error('No tiene permiso para aprobar/rechazar estado de pago');
            return $this->redirect(['action'=>'index']);
        }

        $this->view($id);

        if($this->request->is('post')){
            $paymentStatement = $this->PaymentStatements->get($id, [
                'contain' => [
                    'Budgets' => [
                        'Buildings' =>['BuildingsUsers'=>['Users']],
                        'CurrenciesValues' => ['Currencies']
                    ],
                    'Users',
                    'PaymentStatementStates'
                ]
            ]);

            $action = isset($this->request->data['action']) ? $this->request->data['action'] : null;
            if($action == "approve"){

                //Gerente Finanzas.
                if($user_group == USR_GRP_GE_FINAN){
                    if($paymentStatement->payment_statement_state_id == 2){
                        $paymentStatement->user_modified_id = $user_id;
                        $paymentStatement->payment_statement_state_id = 3;
                        if($this->PaymentStatements->save($paymentStatement)){

                            // registro que aprobacion gerente de finanza pasa a estado espera de aprobacion gerente general
                            $this->PaymentStatements->PaymentStatementApprovals->addState($paymentStatement->id,3, 'Aprobado por gerente de Finanzas. En espera de aprobación gerente General');

                            $this->Flash->success('Estado de Pago Aprobado.');
                            return $this->redirect(['action' => 'index', '?' => ['building_id'=>$paymentStatement->budget->building_id]]);
                        }else{
                            $this->Flash->error('Estado de Pago no pudo ser Aprobado.'.debug($paymentStatement));
                        }
                    }else{
                        $this->Flash->error('Estado actual inconsistente con operación solicitada: quiere aprobar un estado de pago que no espera aprobación');
                    }

                }
                //Gerente General.
                elseif($user_group == USR_GRP_GE_GRAL){
                    if(in_array($paymentStatement->payment_statement_state_id, [1,2,3])){
                        $paymentStatement->user_modified_id = $user_id;
                        $paymentStatement->payment_statement_state_id = 4;
                        if($this->PaymentStatements->save($paymentStatement)){

                            // registro que aprobacion gerente de general
                            $this->PaymentStatements->PaymentStatementApprovals->addState($paymentStatement->id,4,'Aprobado por gerente de General. En espera de envío a cliente');

                            $this->Flash->success('Estado de Pago Aprobado.');
                            return $this->redirect(['action' => 'index', '?' => ['building_id'=>$paymentStatement->budget->building_id]]);
                        }else{
                            $this->Flash->error('Estado de Pago no pudo ser Aprobado.'.debug($paymentStatement));
                        }
                    }else{
                        $this->Flash->error('Estado actual inconsistente con operación solicitada: quiere aprobar un estado de pago que no espera aprobación');
                    }
                }else{
                    $this->Flash->error('Perfil de usuario no puede aprobar/rechazar');
                }

            }
            elseif($action == "reject"){
                //Gerente Finanzas.
                if($user_group == USR_GRP_GE_FINAN){
                    if($paymentStatement->payment_statement_state_id == 2){
                        $paymentStatement->user_modified_id = $user_id;
                        $paymentStatement->payment_statement_state_id = 5;
                        $paymentStatement->comment = $this->request->data['comment'];
                        if($this->PaymentStatements->save($paymentStatement)){

                            // registro que rechazo por gerente de finanzas
                            $this->PaymentStatements->PaymentStatementApprovals->addState($paymentStatement->id,5,'Rechazado por gerente de Finanzas');

                            $this->Flash->success('Estado de Pago Rechazado.');

                            if ( strlen( trim($paymentStatement->comment) ) > 0 ){
                                $this->loadModel('Observations');
                                $comment = $this->Observations->newEntity();
                                $comment->model = 'PaymentStatements';
                                $comment->action = 'comment';
                                $comment->user_id = $this->request->session()->read('Auth.User.id');
                                $comment->model_id = $id;
                                $comment->observation = trim($paymentStatement->comment);

                                if($this->Observations->save($comment)){
                                    $this->Flash->success('Comentario agregado como observación a Estado de Pago.');
                                }
                                else{
                                    $this->Flash->error('Error: No se agregó el comentario.');
                                }
                            }
                            return $this->redirect(['action' => 'index', '?' => ['building_id'=>$paymentStatement->budget->building_id]]);
                        }else{
                            $this->Flash->error('Estado de Pago no pudo ser Rechazado.'.debug($paymentStatement));
                        }
                    }else{
                        $this->Flash->error('Estado actual inconsistente con operación solicitada: quiere rechazar un estado de pago que no espera aprobación');
                    }
                }
                //Gerente General.
                elseif($user_group == USR_GRP_GE_GRAL){
                    if(in_array($paymentStatement->payment_statement_state_id, [1,2,3])){
                        $paymentStatement->user_modified_id = $user_id;
                        $paymentStatement->payment_statement_state_id = 5;
                        $paymentStatement->comment = $this->request->data['comment'];
                        if($this->PaymentStatements->save($paymentStatement)){

                             // registro que rechazo por gerente general
                            $this->PaymentStatements->PaymentStatementApprovals->addState($paymentStatement->id,5,'Rechazado por gerente General');

                            $this->Flash->success('Estado de Pago Rechazado.');

                            if ( strlen( trim($paymentStatement->comment) ) > 0 ){
                                $this->loadModel('Observations');
                                $comment = $this->Observations->newEntity();
                                $comment->model = 'PaymentStatements';
                                $comment->action = 'comment';
                                $comment->user_id = $this->request->session()->read('Auth.User.id');
                                $comment->model_id = $id;
                                $comment->observation = trim($paymentStatement->comment);

                                if($this->Observations->save($comment)){
                                    $this->Flash->success('Comentario agregado como observación a Estado de Pago.');
                                }
                                else{
                                    $this->Flash->error('Error: No se agregó el comentario.');
                                }
                            }
                            return $this->redirect(['action' => 'index', '?' => ['building_id'=>$paymentStatement->budget->building_id]]);
                        }else{
                            $this->Flash->error('Estado de Pago no pudo ser Rechazado.'.debug($paymentStatement));
                        }
                    }else{
                        $this->Flash->error('Estado actual inconsistente con operación solicitada: quiere rechazar un estado de pago que no espera aprobación');
                    }
                }else{
                    $this->Flash->error('Perfil de usuario no puede aprobar/rechazar');
                }
            }
        }

        $this->render( 'view' );
    }


    public function client_approved_or_rejected(){

        $this->autoRender = false;

        if($this->request->is('post')){

            $id = $this->request->data['id'];
            $action = $this->request->data['action'];

            $paymentStatement =  $this->PaymentStatements->get($id,['contain'=>['Budgets']]);

            if ($action == 'approved') {
                $paymentStatement->user_modified_id = $this->request->session()->read('Auth.User.id');
                $paymentStatement->payment_statement_state_id = 7;
                if($this->PaymentStatements->save($paymentStatement)){
                    $this->PaymentStatements->PaymentStatementApprovals->addState($paymentStatement->id,7,'Estado de Pago Aprobado por Cliente');
                    $this->Flash->success('Estado de Pago Aprobado por Cliente.');
                    return $this->redirect(['action' => 'index', '?' => ['building_id'=>$paymentStatement->budget->building_id]]);
                }

            }
            elseif($action == 'rejected'){
                $paymentStatement->user_modified_id = $this->request->session()->read('Auth.User.id');
                $paymentStatement->payment_statement_state_id = 8;
                if($this->PaymentStatements->save($paymentStatement)){
                    if($this->PaymentStatements->PaymentStatementApprovals->addState($paymentStatement->id,8,'Estado de Pago Rechazado por Cliente')){
                       $this->Flash->success('Estado de Pago Rechazado por Cliente.');
                        return $this->redirect(['action' => 'index', '?' => ['building_id'=>$paymentStatement->budget->building_id]]);
                    }
                }
            }
        }
        else{
            // redirect index
             return $this->redirect(['action' => 'index']);
        }

    }



    public function invoice($id){

        $paymentStatement = $this->PaymentStatements->get($id, [
            'contain' => [
                'Budgets' => [
                    'Buildings' =>['BuildingsUsers'=>['Users']],
                    'CurrenciesValues' => ['Currencies']
                ],
                'Users',
                'PaymentStatementStates'
            ]
        ]);

        $this->set(compact('paymentStatement','id'));
    }



    public function send_payment(){


        $this->autoRender = false;

        if($this->request->is('post')){

            // Revisar si existe EDP con ID y Correo
            $id = $this->request->data['id'];
            $clientEmail = $this->request->data['email'];

            /*debug($this->request->data);
            die();*/

            $paymentStatement = $this->PaymentStatements->get($id, [
                'contain' => [
                    'Budgets' => [
                        'Buildings' =>['BuildingsUsers'=>['Users']],
                        'CurrenciesValues' => ['Currencies']
                    ],
                    'Users',
                    'PaymentStatementStates'
                ]
            ]);

            $budget = $paymentStatement->budget;
            $budget_id = $paymentStatement->budget_id;

            // Datos comunes
            $datos = $this->PaymentStatements->sharedData($budget,$paymentStatement);

            // Items para EDP
            $budget_items = $this->PaymentStatements->itemsEdpView($paymentStatement);

            // Estados de pagos anteriores
            $payments = $this->PaymentStatements->find()->where(['budget_id' => $paymentStatement->budget_id,'id <= ' => $paymentStatement->id])->toArray();


            // Datos de Obra
            $this->loadModel('SfBuildings');
            $sf_building = $this->SfBuildings->get($budget->building->softland_id);
            $visitador = 'n/a';
            $admin_obra = 'n/a';
            foreach ($budget->building->buildings_users as $key => $b_user) {
                if($b_user->user->group_id == 6){
                    //Visitador
                    $visitador = $b_user->user->full_name;
                }
                elseif($b_user->user->group_id ==7){
                    // Admin Obra
                    $admin_obra = $b_user->user->full_name;
                }
            }

            $datos['obra']['nombre'] = !is_null($sf_building->DesArn) ? $sf_building->DesArn : 'n/a';
            $datos['obra']['proyecto'] = 'n/a';
            $datos['obra']['cliente'] = !is_null($budget['building']['client']) ? $budget['building']['client'] : 'n/a';
            $datos['obra']['direccion'] = !is_null($budget['building']['address']) ? $budget['building']['address'] : 'n/a';
            $datos['obra']['visitador'] = $visitador;
            $datos['obra']['admin_obra'] = $admin_obra;

            /*debug($paymentStatement);
            die();*/
            // Generate Items
            $row = [];
            $row_items = $this->PaymentStatements->generateItems($rows,$budget_items,$paymentStatement);

            /*debug($datos);
            debug($payments);
            debug($paymentStatement);
            debug($budget);*/
            
            /*echo '<pre>';
            print_r($row_items);
            echo '</pre>';

            die('muere');*/

            // Crear Excel. En modelo PaymentsStatement
            $excel_creado = $this->PaymentStatements->generateExcel($datos,$payments,$paymentStatement,$budget,$row_items);

            // Crear Pdf.
            $pdf_creado = $this->PaymentStatements->generatePdf($datos,$payments,$paymentStatement,$budget,$row_items);

            /*debug($pdf_creado);
            die();*/

            // Enviar Correo con Excel y PDF
            $response = $this->sendEmail($excel_creado, $pdf_creado, $clientEmail);


            if($response['status']){
                // Si todo Ok borrar archivos temporales
                if($response['excel']){
                    unlink ($excel_creado['path']);
                }
                if($response['pdf']){
                    unlink ($pdf_creado['path']);
                }

                // Cambiar estado del estado de pago a enviado a cliente y usuario que modifico.
                //$paymentStatement->payment_statement_state_id = 6;
                $paymentStatement->email_sent = 1;
                $paymentStatement->user_modified_id = $this->request->session()->read('Auth.User.id');
                if($this->PaymentStatements->save($paymentStatement)){

                    //$this->PaymentStatements->PaymentStatementApprovals->addState($paymentStatement->id,6,'Estado de Pago enviado a Cliente');

                    $this->Flash->success('Estado de Pago enviado.');
                }
                else{
                    $this->Flash->error('Error al cambiar estado de Estado de Pago. Intente más tarde.');
                }
            }
            else{
                $this->Flash->error('Ocurrió algun error en el envío. Intente más tarde.');
            }
            // redirect index
            return $this->redirect(['action' => 'index','?'=>['building_id' =>$paymentStatement->budget->building_id]]);
        }

    }

    function accept($id = null)
    {
        $payment_statement = $this->PaymentStatements->get($id);
        $payment_statement->draft = 0;
        $payment_statement->client_approval = null;
        $this->PaymentStatements->save($payment_statement);
        $this->Flash->success('Se ha enviado para aprobación el estado de pago');
        $this->redirect(array('controller' => 'payment_statements', 'action' => 'view', $id));
    }

    /**************************************************/
    /******************APROBACIONES*******************/

    function change_approval($id = null, $type = null)
    {
        if($id != null && $type != null)
        {
            $access = new AccessHelper(new \Cake\View\View());

            $payment_statement = $this->PaymentStatements->get($id);


            switch ($payment_statement->first_approval) {
                case null:
                    switch ($type) {
                        case 'approve':

                            if($access->verifyAccessByKeyword('gerente_general') == true)
                            {
                                $payment_statement->first_approval = 1;
                                $payment_statement->second_approval = 1;
                                $payment_statement->third_approval = 1;
                            }
                            elseif($access->verifyAccessByKeyword('gerente_finanzas') == true)
                            {
                                $payment_statement->first_approval = 1;
                                $payment_statement->second_approval = 1;
                            }
                            else
                            {
                                $payment_statement->first_approval = 1;
                            }
                            
                            break;
                        
                        case 'decline':

                            if($access->verifyAccessByKeyword('gerente_general') == true)
                            {
                                $payment_statement->first_approval = 0;
                                $payment_statement->second_approval = 0;
                                $payment_statement->third_approval = 0;
                            }
                            elseif($access->verifyAccessByKeyword('gerente_finanzas') == true)
                            {
                                $payment_statement->first_approval = 0;
                                $payment_statement->second_approval = 0;
                            }
                            else
                            {
                                $payment_statement->first_approval = 0;
                            }

                            break;
                        
                        default:
                            $payment_statement->first_approval = null;
                            break;
                    }
                    break;

                case 1:
                    switch ($payment_statement->second_approval) {
                        case null:
                            switch ($type) {
                                case 'approve':

                                    if($access->verifyAccessByKeyword('gerente_general') == true)
                                    {
                                        $payment_statement->second_approval = 1;
                                        $payment_statement->third_approval = 1;
                                    }
                                    else
                                    {
                                        $payment_statement->second_approval = 1;
                                    }

                                    break;
                                
                                case 'decline':

                                    if($access->verifyAccessByKeyword('gerente_general') == true)
                                    {
                                        $payment_statement->second_approval = 0;
                                        $payment_statement->third_approval = 0;
                                    }
                                    else
                                    {
                                        $payment_statement->second_approval = 0;
                                    }

                                    break;
                                
                                default:
                                    $payment_statement->second_approval = null;
                                    break;
                            }
                            break;

                        case 1:

                            switch ($payment_statement->third_approval) {
                                case null:
                                    
                                    switch ($type) {
                                        case 'approve':
                                            $payment_statement->third_approval = 1;
                                            break;
                                        
                                        case 'decline':
                                            $payment_statement->third_approval = 0;
                                            break;
                                        
                                        default:
                                            $payment_statement->third_approval = null;
                                            break;
                                    }
                                    break;
                                
                                default:
                                    $payment_statement->third_approval = 0;
                                    break;
                            }

                            break;
                        
                        default:
                            $payment_statement->second_approval = 0;
                            break;
                    }

                    break;
                
                default:
                    $payment_statement->first_approval = 0;
                    break;
            }

            $payment_statement->user_modified_id = $this->request->session()->read('Auth.User.id');

            if($this->PaymentStatements->save($payment_statement))
            {
                $this->Flash->success('Se ha realizado la aprobación al estado de pago satisfactoriamente.');
            }  
            else
            {
                $this->Flash->error('Hubo un error al actualizar el estado de pago.');
            }

            $this->redirect($this->referer());
            
        }
    }

    function clientApproval()
    {
        if($this->request->data != null)
        {
            $payment_statement = $this->PaymentStatements->get($this->request->data['id']);

            if($payment_statement->first_approval == 1 && $payment_statement->second_approval == 1 && $payment_statement->third_approval == 1 && $payment_statement->email_sent == 1 && $payment_statement->draft == 0 && is_null($payment_statement->client_approval))
            {
                switch ($this->request->data['action']) {
                    case 'approve':
                        $payment_statement->client_approval = 1;
                        break;
                    
                    case 'decline':
                        $payment_statement->client_approval = 0;
                        $payment_statement->decline_obs = $this->request->data['obs'];
                        break;
                }

                $payment_statement->user_modified_id = $this->request->session()->read('Auth.User.id');


                if($this->PaymentStatements->save($payment_statement))
                {
                    switch ($this->request->data['action']) {
                        case 'approve':
                            $this->Flash->success('El cliente aprobó el estado de pago.');
                            break;
                        
                        case 'decline':
                            $this->Flash->success('El cliente rechazó el estado de pago.');
                            break;
                    }
                }  
                else
                {
                    $this->Flash->error('Hubo un error al actualizar el estado de pago.');
                }

                $this->redirect($this->referer());
            }
            else
            {
                $this->Flash->error('Hubo un error al actualizar el estado de pago.');
                $this->redirect($this->referer());
            }
        }
    }

    function generateByOld($id =null)
    {
        if($id != null)
        {
            $payment_statement = $this->PaymentStatements->get($id);
            $payment_statement->draft = 1;
            $payment_statement->first_approval = null;
            $payment_statement->second_approval = null;
            $payment_statement->third_approval = null;
            $payment_statement->email_sent = null;
            $this->PaymentStatements->save($payment_statement);
            $this->redirect(['controller' => 'payment_statements', 'action' => 'edit', $id]);
        }
    }
}
