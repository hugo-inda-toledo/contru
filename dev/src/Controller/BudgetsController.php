<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Ghunti\HighchartsPHP\Highchart;
use Ghunti\HighchartsPHP\HighchartJsExpr;
use Cake\Cache\Cache;
use Cake\Log\Log;

/**
 * Budgets Controller
 *
 * @property \App\Model\Table\BudgetsTable $Budgets */
class BudgetsController extends AppController
{
    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Buildings', 'Users']
        ];
        $this->set('budgets', $this->paginate($this->Budgets));
        $this->set('_serialize', ['budgets']);
    }

    /**
     * View method
     *
     * @param string|null $id Budget id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        if (empty($id) && $id == null) {
            $this->Flash->error('No es posible encontrar la información del presupuesto. Por favor, inténtelo nuevamente.');
            return $this->redirect(['controller' => 'buildings', 'action' => 'index']);
        }
        $group_id = $this->request->session()->read('Auth.User.group_id');
        /*
        * Validar que usuario de sesión tenga permisos para el presupuesto
        *
         */
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Budgets->Users->getUserBuildings($this->request->session()->read('Auth.User.id'));
            if (count($user_buildings) > 0) {
                $budget_id = $this->Budgets->find('all',
                    ['conditions' => ['Budgets.building_id' => $user_buildings[0]]])->first();
                if ($budget_id->building_id != $user_buildings[0]) {
                    $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde al Trabajo realizado. Por favor, edite la información de usuario.');
                    return $this->redirect(['controller' => 'users', 'action' => 'index']);
                }
            } else {
                $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                return $this->redirect(['controller' => 'users', 'action' => 'index']);
            }
            $id = $budget_id->id;
        }

        $budget = $this->Budgets->get($id, [
            'contain' => [
                'Buildings' => ['BuildingsUsers' => ['Users']],
                'CurrenciesValues' => ['Currencies'],
                'Users',
                'BudgetApprovals'=> function ($q) {
                    return $q->order(['BudgetApprovals.created ASC']);
                },
                'BudgetApprovals.BudgetStates',
                'BudgetItems'
            ]
        ]);

        $units = $this->Budgets->BudgetItems->Units->find('list', ['limit' => 200])->toArray();

        $this->loadModel('BudgetItems');
        $bi = $this->BudgetItems->find('all',['conditions' => ['budget_id' => $budget->id,'parent_id IS' => null]]);
        $budget_items = array();
        foreach ($bi as $value) {
            $children = $this->BudgetItems
                ->find('children', ['for' => $value->id])
                ->find('threaded')
                ->toArray();
            $budget_items[$value->id] = $value->toArray();
            $budget_items[$value->id]['children'] = $children;
        }
        $this->loadModel('Observations');
        $observations = $this->Observations->find()
            ->where(['Observations.model_id =' => $id, 'Observations.model =' => 'Budgets'])
            ->order(['Observations.created' => 'DESC'])
            ->contain(['Users']);
        //states
        $currentState = $this->Budgets->current_state($id);

        /*
         * Transiciones de estado
         */
        $user_group = $this->request->session()->read('Auth.User.group_id');
        $nextState = null;
        switch ($user_group) {
            case USR_GRP_COORD_PROY:
                ($currentState == 4 || $currentState == 5) ? $nextState = 6 : false;
                break;
            case USR_GRP_GE_FINAN:
                //gerente finanzas
                ($currentState == 1 || $currentState == 2) ? $nextState = 3 : false;
                ($currentState == 4 || $currentState == 5) ? $nextState = 6 : false;
                break;
            case USR_GRP_GE_GRAL:
                //gerente general
                ($currentState == 1 || $currentState == 2 || $currentState == 3) ? $nextState = 4 : false;
                ($currentState == 4 || $currentState == 5) ? $nextState = 6 : false;
                break;
        }
        $nextState = ($currentState == 6) ? 7: $nextState;

        $budget_items_guide_exits = $this->Budgets->getGuideExitsByBudgetItems($budget->id);
        $budget_items_subcontracts = $this->Budgets->getSubcontractsByBudgetItems($budget->id);
        $all_completed_tasks = $this->Budgets->getAllCompletedTasksByWorkerAndSchedulesOrderbyMonth($budget->id);
        $completed_tasks_costs = array();
        $budget_items_completed_tasks_totals = array();
        if (!empty($all_completed_tasks)) {
            $completed_tasks_costs = $this->Budgets->getBudgetItemsCompletedTasksHoursAndCostByWorker($budget->id, $all_completed_tasks);
            // debug($all_completed_tasks);
            // debug($completed_tasks_costs); die;
            $budget_items_completed_tasks_totals = $this->Budgets->getBudgetItemsCompletedTasksTotals($completed_tasks_costs);
        }
        $parent_sum = $this->Budgets->calc_parent_totals($id, $budget_items_guide_exits, $budget_items_completed_tasks_totals, $budget_items_subcontracts);
        //fin states
        //información relacionada partidas
        $budget_items_deals = $this->Budgets->Deals->getTotalDealsOrderByBudgetItem($budget->id);
        $budget_items_bonuses = $this->Budgets->Bonuses->getTotalBonusesOrderByBudgetItem($budget->id);
        $budget_items_progress = $this->BudgetItems->getCurrentProgressValue($budget->id);
        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
        ])->first();
        $states = $this->Budgets->BudgetApprovals->BudgetStates->find('list')->toArray();
        $currency_value = $budget->currencies_values{0}->currency;
        $this->set('budget_id', $budget->id);
        $this->set(compact('units', 'budget_items', 'nextState', 'observations', 'sf_building', 'states', 'budget_items_deals', 'budget_items_bonuses',
            'budget_items_progress', 'parent_sum', 'current_state', 'budget_items_guide_exits', 'budget_items_subcontracts', 'budget_items_completed_tasks_totals', 'currency_value'));
        $this->set('budget', $budget);
        $this->set('_serialize', ['budget']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add($softland_id = null)
    {
        $showDebug = true;
        if(isset($softland_id) && $softland_id != null) {
            $building_associated = $this->Budgets->Buildings->find('all', [
                'conditions' => ['Buildings.softland_id' => $softland_id]
            ])->first();
            if (empty($building_associated) && $building_associated == null) {
                $building = $this->Budgets->Buildings->newEntity();
                $building->softland_id = $softland_id;
                $building->omit = 0;
                $building->active = 1;
                if ($this->Budgets->Buildings->save($building)) {
                    $building_id = $building->id;
                    $this->set('building_id', $building_id);
                } else {
                    $this->Flash->error('Ocurrió un error al configurar el presupuesto. Por favor, inténtelo nuevamente.');
                    return $this->redirect(['controller' => 'buildings', 'action' => 'index']);
                }
            } else {
                //si existe editamos
                $building_id = $building_associated->id;
                $this->set('building_id', $building_id);
            }
            $building = $this->Budgets->Buildings->get($building_id);
            $this->loadModel('SfBuildings');
            $sf_building = $this->SfBuildings->find('all', [
                 'conditions' => ['SfBuildings.CodArn' => $building->softland_id]
            ])->first();
            $this->set(compact('building','sf_building'));

            $budget_exists = $this->Budgets->find('all', [
                'conditions' => ['Budgets.building_id' => $building_id],
                'contain' => ['CurrenciesValues', 'BudgetApprovals', 'Currency']
            ])->first();
            $budget = (empty($budget_exists)) ? $this->Budgets->newEntity() : $budget_exists;

            if ($this->request->is(['patch', 'post', 'put']))
            {
                /*echo '<pre>';
                print_r($this->request->data);
                echo '</pre>';

                die();*/

                if(empty($this->request->data['file']['tmp_name'])) {
                    $this->Flash->error('Debe proporcionar un archivo excel con el presupuesto.');
                    return $this->redirect($this->referer());
                }

                $valid_excel = [
                    'application/vnd.ms-excel',
                    'application/vnd.ms-excel.addin.macroEnabled.12',
                    'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
                    'application/vnd.ms-excel.sheet.macroEnabled.12',
                    'application/vnd.ms-excel.template.macroEnabled.12',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                ];
                if(!in_array($this->request->data['file']['type'], $valid_excel)) {
                    $this->Flash->warning('El archivo no corresponde a formato a excel.');
                    return $this->redirect($this->referer());
                }

                $tienePresupuesto = false;
                if($budget_exists){
                    $budget_obj = $this->Budgets->get($budget->id, [
                        'contain' => [
                            'Buildings' => ['BuildingsUsers' => ['Users']],
                            'CurrenciesValues' => ['Currencies'],
                            'Users',
                            'BudgetApprovals'=> function ($q) {
                                return $q->order(['BudgetApprovals.created ASC']);
                            },
                            'BudgetApprovals.BudgetStates',
                            'BudgetItems'
                        ]
                    ]);

                    $state = end($budget_obj->budget_approvals)->budget_state;
                    if ( count($budget_obj->budget_items) == 0 && $state->id == 1){
                    }else{
                        $tienePresupuesto = true;
                    }
                }

                if ( $tienePresupuesto && !empty($budget_exists) ) {
                    $this->Flash->error('Error: ya existe un presupuesto asociado a esta obra.');
                    return $this->redirect($this->referer());
                }
                else {
                    $budget_id =  (empty($budget_exists)) ? null : $budget_exists->id;

                    $budget->user_created_id = $this->request->session()->read('Auth.User.group_id');
                    $budget = $this->Budgets->patchEntity($budget, $this->request->data);
                    $budget->currency_id = $this->request->data['currencies_values'][0]['currency_id'];
                    $budget->start_value = $this->request->data['currencies_values'][0]['value'];
                    $budget->building->id = $building_id;
                    $budget->general_costs = 0;
                    $budget->created = new \DateTime($this->request->data['budget']['created']);
                    $budget->real_created = date('Y-m-d H:i:s');

                    if ($this->Budgets->save($budget)) {
                        $budget_id = $budget->id;

                        //logica budget_approvals
                        $budget_approval_exists = $this->Budgets->BudgetApprovals->find('all', [
                            'conditions' => ['BudgetApprovals.budget_id' => $budget_id]
                        ])->last();
                        if(empty($budget_approval_exists)) {
                            $budgetApproval = $this->Budgets->BudgetApprovals->newEntity();
                            $budgetApproval->budget_id = $budget_id;
                            $budgetApproval->user_id = $this->request->session()->read('Auth.User.id');
                            $budgetApproval->budget_state_id = 1;
                            $budgetApproval->comment = 'Estado Inicial Presupuesto';
                            debug($budgetApproval);
                            if($this->Budgets->BudgetApprovals->save($budgetApproval)) {
                                // start obs
                                $this->loadModel('Observations');
                                $observation = $this->Observations->newEntity();
                                $observation->model = 'Budgets';
                                $observation->action = 'comment';
                                $observation->model_id = $budgetApproval->budget_id;
                                $observation->user_id = $budgetApproval->user_id;
                                $observation->observation = '[Estado: ' . $this->Budgets->BudgetApprovals->BudgetStates->get($budgetApproval->budget_state_id)->name . '] ' . $budgetApproval->comment;
                                if ($this->Observations->save($observation)) {
                                    ($showDebug) ? debug('observation worked') : false;
                                }
                                // fin obs
                                ($showDebug) ? debug('approval worked') : false;
                            } else {
                                ($showDebug) ? debug('approval no worked') : false;
                                ($showDebug) ? debug($budgetApproval) : false;
                            }
                        }
                        //fin logica budget_approvals

                        //movemos el archivo
                        $nueva_ruta = APP . 'upload_excel' . DS . 'temporal-' . date('Y-m-d_His') . '.xlsx';
                        if(!empty($this->request->data['file']['tmp_name'])) {
                            //validar tipo archivo
                            if(move_uploaded_file($this->request->data['file']['tmp_name'], $nueva_ruta)) {
                                //validar excel
                                $this->request->data['file'] = $nueva_ruta;
                                $datos_excel = null;
                                $datos_excel = $this->Budgets->excel_req_a_array($nueva_ruta,true);

                                if(is_array($datos_excel))
                                {
                                    $session = $this->request->session();
                                    $session->write('tmp.datos_excel', $datos_excel ) ;
                                    $session->write('tmp.file', $nueva_ruta);

                                    return $this->redirect(['action' => 'confirm_excel', $budget_id]);
                                }
                                else
                                {
                                    $this->Flash->error('Debes eliminar las columnas restantes del archivo excel (Desde la columna I hasta la columna '.$datos_excel.')');
                                    return $this->redirect($this->referer());
                                }

                            } else {
                                //no se puede mover archivo
                                debug('temp_name: ' .  $this->request->data['file']['tmp_name']);
                                debug('fail nueva ruta: ' . $nueva_ruta);
                            }
                        }

                        $this->Flash->success('El presupuesto ha sido guardado correctamante.');
                        return $this->redirect(['action' => 'review', $budget_id]);

                    }else{
                        $this->Flash->error("Hay errores");
                    }
                }
            }
            $currencies = $this->Budgets->Currencies->find('list', ['limit' => 200]);
            $this->set(compact('budget', 'currencies'));
            $this->set('_serialize', ['budget']);
        } else {
            $this->Flash->error('No se encontró información sobre la obra. Por favor, inténtelo nuevamente.');
            return $this->redirect(['controller' => 'buildings', 'action' => 'index']);
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Budget id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $budget = $this->Budgets->get($id, [
            'contain' => ['CurrenciesValues', 'BudgetApprovals', 'Buildings']
        ]);

         $building = $budget->building;
         $this->set('building_id', $building->id);
         $this->loadModel('SfBuildings');
         $sf_building = $this->SfBuildings->find('all', [
              'conditions' => ['SfBuildings.CodArn' => $building->softland_id]
         ])->first();
         $this->set(compact('building','sf_building'));

        if ($this->request->is(['patch', 'post', 'put'])) {
            $budget = $this->Budgets->patchEntity($budget, $this->request->data);
            if ($this->Budgets->save($budget)) {
                $this->Flash->success('La configuración del presupuesto fue actualizada');
                return $this->redirect(['controller' => 'buildings', 'action' => 'dashboard', $building->softland_id]);
            } else {
                $this->Flash->error('La configuración del presupuesto no pudo ser actualizada. Por favor, corrija errores y reintente.');
            }
        }
        $currencies = $this->Budgets->Currencies->find('list', ['limit' => 200]);
        $this->set(compact('budget', 'currencies'));
        $this->set('_serialize', ['budget']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Budget id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->autoRender = false;
        //$this->request->allowMethod(['post', 'delete']);
         $budget = $this->Budgets->get($id, [
            'contain' => [
                'Buildings', 'Users', 'Assists', 'Bonuses', 'BudgetApprovals', 'BudgetItems', 'PaymentStatements', 'Schedules'
                ]
            ]);

        $curState = $this->Budgets->current_state($id);
        debug($curState);
        if($curState>2) {
            $this->Flash->error('En el estado actual del presupuesto, no se puede eliminar del sistema.');
            return $this->redirect(['action' => 'dashboard', $budget->building['softland_id']]);
        }
        //borrar approvals
        //$this
        if(!empty($budget->budget_approvals)) {
            foreach($budget->budget_approvals as $ba)
            {
                $this->Budgets->BudgetApprovals->delete($ba);
            }
        }

        if(!empty($budget->budget_items)) {
            foreach($budget->budget_items as $bi)
            {
                $this->Budgets->BudgetItems->delete($bi);
            }
        }

        if ($this->Budgets->delete($budget)) {
            $this->Flash->success('El presupuesto ha sido eliminado del sistema.');
        } else {
            $this->Flash->error('El presupuesto no ha podido ser eliminado, por favor intente nuevamente');
        }
        return $this->redirect(['controller' => 'buildings', 'action' => 'index']);
    }

    public function confirm_excel($id = null) {
        $redirect_action = "review";
        //showDebug = activar o desactivar debugs en la funcion especifica para trackear errores.

        $showDebug = false;
        $session = $this->request->session();
        //debug($session->read('tmp'));
        if ($session->check('tmp.datos_excel')) {
            $datos_excel = $session->read('tmp.datos_excel');
            //unset, por que si
            unset($datos_excel['excel'][1]);

            $tmp_items = array_column($datos_excel['excel'], 'A');
            $last_parent = explode('.',max($tmp_items));
            if(!empty(reset($last_parent))) {
                $last_parent = reset($last_parent);
            } else {
                $last_parent = null;
            }
            $this->set('excels', $datos_excel['excel']);

            $errores = (!empty($datos_excel['errores'])) ? $datos_excel['errores'] : array('No se encontraron errores.');
            $info = (!empty($datos_excel['info'])) ? $datos_excel['info']:[];
            $this->set('errores', $errores);
            $this->set(compact('info'));
        }
        else {
            debug('fail redirect');
            //no esta el excel guardado en sesion, redirect a index.
            //return $this->redirect(['action' => 'index']);
            return $this->redirect(['action' => $redirect_action, $id]);
        }
        $budget = $this->Budgets->get($id, [
            'contain' => ['CurrenciesValues' => ['Currencies'], 'Buildings', 'Users', 'BudgetApprovals', 'BudgetItems']
        ]);
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
        ])->first();

        $this->loadModel('Units');
        $unidades_en_sistema = ( $this->Units->find('list')->toArray() );

        if ($this->request->is(['patch', 'post', 'put'])) {
            $datos_excel = $session->read('tmp.datos_excel');
            if(count($datos_excel['errores']) < 1) {
                //no hay errores se guarda el archivo.
                //logica save a db
                //carga modelo units y revizo si existe el tipo de unidad, si no existe se crea, si existe se asocia.
                //
                //busco ultimo parent gastos generales
                $tmp_items = array_column($datos_excel['excel'], 'A');
                $last_parent = explode('.',max($tmp_items));
                if(!empty(reset($last_parent))) {
                    $last_parent = reset($last_parent);
                } else {
                    $last_parent = null;
                }
                //fin ultimo parent
                $this->loadModel('BudgetItems');
                foreach($datos_excel['excel'] as $k=>$row) {
                    if($k == 1) {
                        continue;
                    }
                    // check si existe unidad, si no existe se crea.
                    $idUnidad = '';

                    if( !in_array( trim($row['C']), $unidades_en_sistema) ) {
                        if($row['C'] != null) {
                            $newUnidad = $this->Units->newEntity(array('name' => trim( $row['C']) , 'description' => trim( $row['C']) ));
                            if($this->Units->save($newUnidad)) {
                                $idUnidad = $newUnidad->Get('id');
                                $unidades_en_sistema[$idUnidad] = trim($row['C']);
                            }
                        } else {
                            /*
                            Nota: no debería llegar nunca a esta asignación,
                            pues en el chequeo de errores del arreglo (Metodo BudgetsTable::excel_req_a_array)
                            se verifica que vengan definidas las unidades para filas con datos
                             */
                            $idUnidad = 1;
                        }
                    }
                    else {
                        $idUnidad = array_search(trim($row['C']), $unidades_en_sistema);
                    }

                    $oldBudgetItemQuery = $this->BudgetItems->find('all')
                    ->where(['BudgetItems.budget_id =' => $id, 'BudgetItems.item =' => $row['A']])
                    ->limit(10);

                    $oldBudgetItem = $oldBudgetItemQuery->first();
                    //debug($oldBudgetItem);
                    if($oldBudgetItem != null) {
                        ($showDebug) ? debug("item ". $row['A'] . " asociado al presupuesto " . $id . " ya existe en BD.") : false;
                        continue;
                    }

                    //comparo row['A'] para saber si el item pertenece al ultimo capitulo gastos generales.
                    $tmp_row_a = explode('.', $row['A']);
                    if(count($tmp_row_a) == 1)  {
                        $parent_gg = true;
                    }  else {
                        $parent_gg = false;
                    }
                    $tmp_row_a = (!empty(reset($tmp_row_a))) ? reset($tmp_row_a) : null;
                    if(!is_null($tmp_row_a) && !is_null($last_parent)) {
                        if($last_parent === $tmp_row_a) {
                            $item_extra = 3;
                        } else {
                            $item_extra = 0;
                        }
                    }

                    $quantity = ($row['D'] == null) ? 0 : $row['D'];
                    $unity_price = ($row['E'] == null) ? 0 : $row['E'];
                    $total_price = ($row['F'] == null) ? 0 : $row['F'];
                    $total_price = (!isset($row['F'])) ? 0 : $row['F'];
                    // $total_uf = (!isset($row['G'])) ? 0 : $row['G'];
                    $total_uf = 0;
                    $comments = (!isset($row['G'])) ? '' : $row['G'];
                    $target_value = (!isset($row['H'])) ? $total_price : $row['H'];
                    if($parent_gg && $item_extra == 3) {
                        $comments = 'Capítulo para registro de Gastos Generales';
                    }
                    $item =  array(
                        'budget_id' => $id,
                        'item' => $row['A'],
                        'description' => $row['B'],
                        'unit_id' => $idUnidad,
                        'quantity' => $quantity,
                        'unity_price' => $unity_price,
                        'total_price' => $total_price,
                        'total_uf' => $total_uf,
                        'comments' => $comments,
                        'disabled' => 0,
                        'extra' => $item_extra,
                        'target_value' => $target_value);

                    $budgetItem = $this->BudgetItems->newEntity($item);

                    //filtro el item para ver si corresponde a hijo o padre
                    $parentIdArr = explode('.', $row['A']);
                    $lastItemChars = array_pop($parentIdArr);
                    $parentId = (!empty($parentIdArr)) ? implode(".",$parentIdArr) : false;
                    //si corresponde a nodo hijo, busco el padre
                    if($parentId != false) {
                        //debug('parentId: ' . $parentId . ' Child: ' .  $row['A']);
                        $parentBudgetItemQuery = $this->BudgetItems->find('all')
                        ->where(['BudgetItems.budget_id =' => $id, 'BudgetItems.item =' => $parentId]);
                        $parentBudgetItem = $parentBudgetItemQuery->first();
                        //asigno la id del padre al hijo como parent_id
                        if($parentBudgetItem != null) {
                            $budgetItem->parent_id = $parentBudgetItem->id;
                            //Actualizo el total de los padres
                            $this->Budgets->updateParentsPrice($budgetItem, $total_price, $total_uf, $target_value);
                        }
                    }

                    if($this->BudgetItems->save($budgetItem)) {
                        ($showDebug) ? debug('item: ' . $row['A'] . ' guardado!') : false;
                    }

                }
                //agrego un nuevo capitulo para controles de cambio, adicionales, item extra.
                $lastItem = (!empty($budgetItem)) ? $budgetItem->item : false;
                $parent = ($lastItem) ? explode('.', $lastItem): false;
                $new_parent = ($parent) ? (reset($parent) + 1) : false;
                if($new_parent) {
                    $item =  array(
                        'budget_id' => $id,
                        'parent_id' => null,
                        'item' => $new_parent,
                        'description' => 'ADICIONALES',
                        'unit_id' => 1,
                        'quantity' => 0,
                        'unity_price' => 0,
                        'total_price' => 0,
                        'total_uf' => 0,
                        'comments' => 'Capítulo para registro de Adicionales',
                        'disabled' => 0,
                        'extra' => 1);
                    $budgetItem = $this->BudgetItems->newEntity($item);
                    if($this->BudgetItems->save($budgetItem)) {
                        ($showDebug) ? debug('item: Adicionales guardado!') : false;
                    }
                }
                // fin capitulo controles de cambio
                //agrego un nuevo capitulo para gastos no considerados.
                $lastItem = (!empty($budgetItem)) ? $budgetItem->item : false;
                $parent = ($lastItem) ? explode('.', $lastItem): false;
                $new_parent = $new_parent + 1;
                if($new_parent) {
                    $item =  array(
                        'budget_id' => $id,
                        'parent_id' => null,
                        'item' => $new_parent,
                        'description' => 'GASTOS NO CONSIDERADOS',
                        'unit_id' => 1,
                        'quantity' => 0,
                        'unity_price' => 0,
                        'total_price' => 0,
                        'total_uf' => 0,
                        'comments' => 'Capítulo para registro de Gastos no considerados',
                        'disabled' => 0,
                        'extra' => 2);
                    $budgetItem = $this->BudgetItems->newEntity($item);
                    if($this->BudgetItems->save($budgetItem)) {
                        ($showDebug) ? debug('item: Gastos no considerados guardado!') : false;
                    }
                }
                // fin capitulo controles de cambio
                $budget->file = ($session->check('tmp.file')) ? $session->read('tmp.file') : null;
                $budget->total_cost = $this->Budgets->calc_total($id,0,0);
                $budget->total_target_value=$this->Budgets->calc_total_target($id,0,0);;
                //$this->Budgets->save($budget);
                if($this->Budgets->save($budget)) {
                    ($showDebug) ? debug("file updated to budget") : false;
                }
                //end logiva save a db
                ($showDebug) ? debug('guardado correctamente, errores: ' . count($datos_excel['errores'])) : false;
                $this->Flash->success('Excel cargado correctamente.');
                 //logica budget_approvals
                $budget_approval_exists = $this->Budgets->BudgetApprovals->find('all', [
                    'conditions' => ['BudgetApprovals.budget_id' => $id]
                ])->last();
                //start cambio estado
                $budgetApproval = $this->Budgets->BudgetApprovals->newEntity();
                $budgetApproval->budget_id = $id;
                $budgetApproval->user_id = $this->request->session()->read('Auth.User.id');
                $budgetApproval->budget_state_id = 2;
                $budgetApproval->comment = 'Estado automatico carga excel.';
                if($this->Budgets->BudgetApprovals->save($budgetApproval)) {
                    ($showDebug) ? debug('approval worked') : false;
                    // start obs
                    $this->loadModel('Observations');
                    $observation = $this->Observations->newEntity();
                    $observation->model = 'Budgets';
                    $observation->action = 'comment';
                    $observation->model_id = $budgetApproval->budget_id;
                    $observation->user_id = $budgetApproval->user_id;
                    $observation->observation = '[Estado: ' . $this->Budgets->BudgetApprovals->BudgetStates->get($budgetApproval->budget_state_id)->name . '] ' . $budgetApproval->comment;
                    if ($this->Observations->save($observation)) {
                        ($showDebug) ? debug('observation worked') : false;
                    }
                    // fin obs
                } else {
                    ($showDebug) ? debug('approval no worked') : false;
                    ($showDebug) ? debug($budgetApproval) : false;
                }
                //fin logica budget_approvals
                $this->Budgets->updateGeneralCosts($id);
                return $this->redirect(['action' => $redirect_action, $id]);
            }
            else {
                //errores de validacion en el archivo, borrar el temporal y mostrar errores.
                ($showDebug) ? debug('se encontraron errores no se puede guardar.') : false;
                $this->Flash->warning('Excel no se pudo guardar, se encontraron errores.');

                //todo borrar el archivo temporal
                //borrar el temporal.
                $nueva_ruta = $session->read('tmp.file');
                if(file_exists($nueva_ruta)) {
                    if(unlink($nueva_ruta)) {
                        ($showDebug) ? debug("borrada: " . $nueva_ruta) : false;
                    }
                    else {
                        ($showDebug) ? debug("fail borrar" . $nueva_ruta) : false;
                    }
                } else {
                    debug('file no longer exisits');
                }
                return $this->redirect(['action' => $redirect_action, $id]);
            }
        //$datos_excel['errores'];
        //$datos_excel = $session->consume('tmp.datos_excel');
        //$confirm = $this->request->data['id']
        //debug($this->request->data['id']);
        }
        $this->set(compact('budget', 'sf_building'));
        $this->set('_serialize', ['budget']);
    }

    public function add_extra($id = null)
    {
         $showDebug = false;
         $datos_excel = array('excel' => array(), 'errores' => array());
         $budget = $this->Budgets->get($id, [
            'contain' => ['BudgetItems', 'BudgetApprovals'=> function ($q) {
                return $q->order(['BudgetApprovals.created ASC']);
            },
            'BudgetApprovals.BudgetStates', 'Buildings']
        ]);
        /*//obtengo el ultimo ID. TODO recomendar ultimo ID como primer nuevo item extra
        $lastItem = (!empty($budget->budget_items)) ? end($budget->budget_items)['item'] : false;
        $parent = ($lastItem) ? explode('.', $lastItem): false;
        $new_parent = ($parent) ? (reset($parent) + 1) : false;
        */

        // busco item padre control de cambio, si existe.
        $budgetItem = $this->Budgets->BudgetItems->find('all')
                        ->where(['BudgetItems.budget_id =' => $id, 'BudgetItems.extra =' => 1, 'BudgetItems.parent_id IS' => null])->first();
        if(!(empty($budgetItem))) {
            $childs = $this->Budgets->BudgetItems->find('children', ['for' => $budgetItem->id])->toArray();
            $lastItem = (!empty($childs)) ? end($childs)->item : $budgetItem->item;
            $parent = ($lastItem) ? explode('.', $lastItem): false;
            $new_parent = (!empty($parent) && (count($parent) > 1)) ? ($parent[0] . '.' . ($parent[1] + 1)) : reset($parent) . '.1';
        } else {
            $lastItem = (!empty($budget->budget_items)) ? end($budget->budget_items)['item'] : false;
            $parent = ($lastItem) ? explode('.', $lastItem): false;
            $new_parent = ($parent) ? (reset($parent) + 1) . '.1' : false;
        }

        $this->loadModel('BudgetItems');
        $units = $this->Budgets->BudgetItems->Units->find('list', ['limit' => 200])->toArray();

        $bi = $this->BudgetItems->find('all',['conditions' => ['budget_id' => $id,'parent_id IS' => null, 'extra' => 1]]);
        $budget_items = array();
        foreach ($bi as $value) {
            $children = $this->BudgetItems
                ->find('children', ['for' => $value->id])
                ->find('threaded')
                ->contain([
                    'Units'
                ])
                ->toArray();
            $budget_items[$value->id] = $value->toArray();
            $budget_items[$value->id]['children'] = $children;
        }
        $states = $this->Budgets->BudgetApprovals->BudgetStates->find('list')->toArray();
        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
        ])->first();

        $this->set(compact('budget_items','states','state', 'sf_building'));

        if ($this->request->is(['patch', 'post', 'put'])) {
            //debug($this->request->data);
            $excel = json_decode($this->request->data['excel'],true);

            foreach($excel['data'] as $k=>&$row) {
                ksort($row);
                if(isset($row['D']) && !empty($row['D'])) {
                    $excel['data'][$k]['D'] = str_replace(".","",$row['D']);
                    $excel['data'][$k]['D'] = (!is_numeric($row['D'])) ? $row['D'] : $row['D'];
                }
                if(isset($row['E']) && !empty($row['E'])) {
                    $excel['data'][$k]['E'] = str_replace(".","",$row['E']);
                    $excel['data'][$k]['E'] = (!is_numeric($row['E'])) ? $row['E'] : $row['E'];
                }

                if(isset($row['F'])  && !empty($row['F'])) {
                    $excel['data'][$k]['F'] = str_replace(".","",$row['F']);
                    $excel['data'][$k]['F'] = (!is_numeric($row['F'])) ? $row['F'] : $row['F'];
                }

                if(isset($row['H'])  && !empty($row['H'])) {
                    $excel['data'][$k]['H'] = str_replace(".","",$row['H']);
                    $excel['data'][$k]['H'] = (!is_numeric($row['H'])) ? $row['H'] : $row['H'];
                }
                if(isset($row)  && empty($row['A']) && empty($row['B']) && empty($row['C']) && empty($row['D']) && empty($row['E']) && empty($row['F']) && empty($row['G']) ) {
                    unset($excel['data'][$k]);
                }
            }
            //agrego un valor en key 0 y lo remuevo, lo mas rapido q encontre para incrementar todas las keys en 1 y asi quedan como key == nº row, para los mensajes de error.
            array_unshift($excel['data'], "remove key 0");
            unset($excel['data'][0]);
            // reviso si el correlativo del primer item en el arreglo existe en db.
            $tmp_first_item = reset($excel['data'])['A'];
            $tmp_expecimen = explode('.', $tmp_first_item);
            $tmp_expecimen[count($tmp_expecimen) - 1] = (end($tmp_expecimen) - 1);
            if(end($tmp_expecimen) == 0) {
                unset($tmp_expecimen[count($tmp_expecimen) - 1]);
            }
            $tmp_before_correlative = implode(".",$tmp_expecimen);
            $tmp_anterior_bi = $this->Budgets->BudgetItems->find('all')
                        ->where(['BudgetItems.budget_id =' => $id, 'BudgetItems.extra =' => 1, 'BudgetItems.item' => $tmp_before_correlative])->first();
            $correlative_on_db = false;
            if(!empty($tmp_anterior_bi)) {
                $correlative_on_db = ($tmp_anterior_bi->item == $tmp_before_correlative) ? true : false;
            }
            $datos_excel = $this->Budgets->excel_req_a_array($excel['data'],false, $correlative_on_db);
            //($showDebug) ? debug($datos_excel['errores']) : false;

            $last_parent = ($new_parent > 1) ? ($new_parent - 1) : $new_parent;
            ($showDebug) ? debug('last_parent: ' . $last_parent) : false;
            ($showDebug) ? debug('new_parent: ' . $new_parent) : false;
            unset($k);
            unset($row);
            // valido que todos los items adicionales pertenezcan al capitulo de control de cambios, si no error.
            if(!empty($budgetItem))  {
                $main_chapter = $budgetItem->item;
            }
            $confirm_info = true;
            foreach($datos_excel['excel'] as $k=>&$row) {
                $current_item = explode('.', $row['A']);
                $current_parent = reset($current_item);
                if($current_parent != $main_chapter) {
                    debug($k);
                    $error_msg = 'Nuevo item adicional en la fila: '. $k . ', item adicional se intento ingresar al capitulo: ' . $current_parent . ' y el Capitulo de Controles de Cambios es el: ' . ($main_chapter);
                    if(!in_array($error_msg, $datos_excel['errores'])) {
                        $datos_excel['errores'][] = $error_msg;
                    }
                }

                if($current_parent <= $last_parent) {
                    $error_msg = 'Nuevo item adicional en la fila: '. $k . ', es menor los items originales del presupuesto. Item extra: ' . $current_parent . ' Ultimo item del presupuesto: ' . ($last_parent);
                    if(!in_array($error_msg, $datos_excel['errores'])) {
                        $datos_excel['errores'][] = $error_msg;
                    }
                }
                $row['C']=strtoupper($row['C']);
                if(!in_array($row['C'], $units)){
                    $confirm_info = false;
                    $datos_excel['info'][] = sprintf("Información fila: %s columna: %s ... %s", $k, "C", "el tipo de Unidad '".$row['C']."' no existe, por ende se creará automáticamente.");
                }
            }
            if(isset($this->request->data['confirm_info']) && $this->request->data['confirm_info']==1){
                $confirm_info=true;
            }
            //logica save a db
            if(count($datos_excel['errores']) < 1 && $confirm_info) {
                //no hay errores se guarda el archivo.
                //logica save a db
                //carga modelo units y revizo si existe el tipo de unidad, si no existe se crea, si existe se asocia.
                $this->loadModel('Units');
                $this->loadModel('BudgetItems');
                unset($k);
                unset($row);
                foreach($datos_excel['excel'] as $k=>$row) {
                   // check si existe unidad, si no existe se crea.
                    $idUnidad = '';
                    if(isset($row['C'])){
                        $queryUnidades = $this->Units->findByName($row['C']);
                        $Unidad = $queryUnidades->first();
                        if(empty($Unidad)) {
                            if($row['C'] != null) {
                                $newUnidad = $this->Units->newEntity(array('name' => $row['C'], 'description' => $row['C']));
                                if($this->Units->save($newUnidad)) {
                                    $idUnidad = $newUnidad->Get('id');
                                }
                            }
                            else {
                                $idUnidad = 1;
                            }
                        }
                        else {
                            $idUnidad = $Unidad->Get('id');
                        }
                    }else{
                        $idUnidad = 1;
                    }
                    $oldBudgetItemQuery = $this->BudgetItems->find('all')
                    ->where(['BudgetItems.budget_id =' => $id, 'BudgetItems.item =' => $row['A']])
                    ->limit(10);

                    $oldBudgetItem = $oldBudgetItemQuery->first();
                    //debug($oldBudgetItem);
                    if($oldBudgetItem != null) {
                        ($showDebug) ? debug("item ". $row['A'] . " asociado al presupuesto " . $id . " ya existe en BD.") : false;
                        continue;
                    }
                    $quantity = (!isset($row['D'])) ? 0 : floatval(str_replace(",", ".", $row['D']));
                    $unity_price = (!isset($row['E'])) ? 0 : floatval(str_replace(",", ".", $row['E']));
                    $total_price = (!isset($row['F'])) ? 0 : floatval(str_replace(",", ".", $row['F']));
                    $comments = (!isset($row['G'])) ? '' : $row['G'];
                    $target_value = (!isset($row['H'])) ? $total_price : floatval(str_replace(",", ".", $row['H']));

                    $item =  array(
                        'budget_id' => $id,
                        'item' => $row['A'],
                        'description' => $row['B'],
                        'unit_id' => $idUnidad,
                        'quantity' => $quantity,
                        'unity_price' => $unity_price,
                        'total_price' => $total_price,
                        'comments' => $comments,
                        'target_value' => $target_value,
                        'disabled' => 0,
                        'extra' => 1);
                    ($k >= 486) ? debug($item) : false;
                    $budgetItem = $this->BudgetItems->newEntity($item);
                    //filtro el item para ver si corresponde a hijo o padre
                    $parentIdArr = explode('.', $row['A']);
                    $lastItemChars = array_pop($parentIdArr);
                    $parentId = (!empty($parentIdArr)) ? implode(".",$parentIdArr) : false;
                    //si corresponde a nodo hijo, busco el padre
                    if($parentId != false) {
                        //debug('parentId: ' . $parentId . ' Child: ' .  $row['A']);
                        $parentBudgetItemQuery = $this->BudgetItems->find('all')
                        ->where(['BudgetItems.budget_id =' => $id, 'BudgetItems.item =' => $parentId])
                        ->limit(10);
                        $parentBudgetItem = $parentBudgetItemQuery->first();
                        //asigno la id del padre al hijo como parent_id
                        if($parentBudgetItem != null) {
                            $budgetItem->parent_id = $parentBudgetItem->id;
                            $this->Budgets->updateParentsPrice($budgetItem, $total_price, 0, $target_value);
                        }
                    }
                    //logica save budget item
                    if($this->BudgetItems->save($budgetItem)) {
                        ($showDebug) ? debug('item: ' . $row['A'] . ' guardado!') : false;
                    }
                }
                // die();
                //end logiva save a db
                ($showDebug) ? debug('guardado correctamente, errores: ' . count($datos_excel['errores'])) : false;
                $this->Flash->success('Adicionales han sido guardados correctamente.');
                // return $this->redirect(['controller' => 'Buildings', 'action' => 'dashboard', $sf_building->CodArn]);
                return $this->redirect(['controller' => 'Budgets', 'action' => 'add_extra', $id]);
            }
            else {
                //errores de validacion en el archivo, borrar el temporal y mostrar errores.
                ($showDebug) ? debug('se encontraron errores no se puede guardar.') : false;
                $this->Flash->warning('Se encontraron errores no se ha podido guardar.');

            }
        }
        $errores = (!empty($datos_excel['errores'])) ? $datos_excel['errores'] : array('No se encontraron errores.');
        $info = (!empty($datos_excel['info'])) ? $datos_excel['info'] : array();
        $this->set('errores', $errores);
        $units = $this->Budgets->BudgetItems->Units->find('list', ['limit' => 200])->toArray();
        //$users = $this->Budgets->Users->find('list', ['limit' => 200]);
        $this->set('new_parent', $new_parent);
        $this->set('datos_excel', $datos_excel);
        $this->set(compact('budget', 'units', 'users', 'info'));
        $this->set('_serialize', ['budget']);
    }

    public function add_expense($id = null)
    {
         $showDebug = false;
         $datos_excel = array('excel' => array(), 'errores' => array());
         $budget = $this->Budgets->get($id, [
            'contain' => ['BudgetItems', 'BudgetApprovals'=> function ($q) {
                return $q->order(['BudgetApprovals.created ASC']);
            },
            'BudgetApprovals.BudgetStates', 'Buildings']
        ]);
        $budgetItem = $this->Budgets->BudgetItems->find('all')
                        ->where(['BudgetItems.budget_id =' => $id, 'BudgetItems.extra =' => 2, 'BudgetItems.parent_id IS' => null])->first();
        if(!(empty($budgetItem))) {
            $childs = $this->Budgets->BudgetItems->find('children', ['for' => $budgetItem->id])->toArray();
            $lastItem = (!empty($childs)) ? end($childs)->item : $budgetItem->item;
            $parent = ($lastItem) ? explode('.', $lastItem): false;
            $new_parent = (!empty($parent) && (count($parent) > 1)) ? ($parent[0] . '.' . ($parent[1] + 1)) : reset($parent) . '.1';
        } else {
            $lastItem = (!empty($budget->budget_items)) ? end($budget->budget_items)['item'] : false;
            $parent = ($lastItem) ? explode('.', $lastItem): false;
            $new_parent = ($parent) ? (reset($parent) + 1) . '.1' : false;
        }

        $this->loadModel('BudgetItems');
        $units = $this->Budgets->BudgetItems->Units->find('list', ['limit' => 200])->toArray();

        $bi = $this->BudgetItems->find('all',['conditions' => ['budget_id' => $id,'parent_id IS' => null, 'extra' => 2]]);
        $budget_items = array();
        foreach ($bi as $value) {
            $children = $this->BudgetItems
                ->find('children', ['for' => $value->id])
                ->find('threaded')
                ->contain([
                    'Units'
                ])
                ->toArray();
            $budget_items[$value->id] = $value->toArray();
            $budget_items[$value->id]['children'] = $children;
        }
        $states = $this->Budgets->BudgetApprovals->BudgetStates->find('list')->toArray();
        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
        ])->first();

        $this->set(compact('budget_items','states','state', 'sf_building'));

        if ($this->request->is(['patch', 'post', 'put'])) {
            //debug($this->request->data);

            $excel = json_decode($this->request->data['excel'],true);

            foreach($excel['data'] as $k=>&$row) {
                ksort($row);
                if(isset($row['D']) && !empty($row['D'])) {
                    $excel['data'][$k]['D'] = str_replace(".","",$row['D']);
                    $excel['data'][$k]['D'] = (!is_numeric($row['D'])) ? $row['D'] : $row['D'];
                }
                if(isset($row['E']) && !empty($row['E'])) {
                    $excel['data'][$k]['E'] = str_replace(".","",$row['E']) ;
                    $excel['data'][$k]['E'] = (!is_numeric($row['E'])) ? $row['E'] : $row['E'];
                }

                if(isset($row['F'])  && !empty($row['F'])) {
                    $excel['data'][$k]['F'] = str_replace(".","",$row['F']);
                    $excel['data'][$k]['F'] = (!is_numeric($row['F'])) ? $row['F'] : $row['F'];
                }
                if(isset($row['H'])  && !empty($row['H'])) {
                    $excel['data'][$k]['H'] = str_replace(".","",$row['H']);
                    $excel['data'][$k]['H'] = (!is_numeric($row['H'])) ? $row['H'] : $row['H'];
                }
                if(isset($row)  && empty($row['A']) && empty($row['B']) && empty($row['C']) && empty($row['D']) && empty($row['E']) && empty($row['F']) && empty($row['G']) ) {
                    unset($excel['data'][$k]);
                }
            }
            //agrego un valor en key 0 y lo remuevo, lo mas rapido q encontre para incrementar todas las keys en 1 y asi quedan como key == nº row, para los mensajes de error.
            array_unshift($excel['data'], "remove key 0");
            unset($excel['data'][0]);
            // reviso si el correlativo del primer item en el arreglo existe en db.
            $tmp_first_item = reset($excel['data'])['A'];
            $tmp_expecimen = explode('.', $tmp_first_item);
            $tmp_expecimen[count($tmp_expecimen) - 1] = (end($tmp_expecimen) - 1);
            if(end($tmp_expecimen) == 0) {
                unset($tmp_expecimen[count($tmp_expecimen) - 1]);
            }
            $tmp_before_correlative = implode(".",$tmp_expecimen);
            $tmp_anterior_bi = $this->Budgets->BudgetItems->find('all')
                        ->where(['BudgetItems.budget_id =' => $id, 'BudgetItems.extra =' => 2, 'BudgetItems.item' => $tmp_before_correlative])->first();
            $correlative_on_db = false;
            if(!empty($tmp_anterior_bi)) {
                $correlative_on_db = ($tmp_anterior_bi->item == $tmp_before_correlative) ? true : false;
            }
            $datos_excel = $this->Budgets->excel_req_a_array($excel['data'],false, $correlative_on_db);
            //($showDebug) ? debug($datos_excel['errores']) : false;

            $last_parent = ($new_parent > 1) ? ($new_parent - 1) : $new_parent;
            ($showDebug) ? debug('last_parent: ' . $last_parent) : false;
            ($showDebug) ? debug('new_parent: ' . $new_parent) : false;
            unset($k);
            unset($row);
            // valido que todos los items adicionales pertenezcan al capitulo de control de cambios, si no error.
            if(!empty($budgetItem))  {
                $main_chapter = $budgetItem->item;
            }
            $confirm_info = true;

            foreach($datos_excel['excel'] as $k=>&$row) {
                $current_item = explode('.', $row['A']);
                $current_parent = reset($current_item);
                if($current_parent != $main_chapter) {
                    debug($k);
                    $error_msg = 'Nuevo item adicional en la fila: '. $k . ', item adicional se intento ingresar al capitulo: ' . $current_parent . ' y el Capitulo de Controles de Cambios es el: ' . ($main_chapter);
                    if(!in_array($error_msg, $datos_excel['errores'])) {
                        $datos_excel['errores'][] = $error_msg;
                    }
                }

                if($current_parent <= $last_parent) {
                    $error_msg = 'Nuevo item adicional en la fila: '. $k . ', es menor los items originales del presupuesto. Item extra: ' . $current_parent . ' Ultimo item del presupuesto: ' . ($last_parent);
                    if(!in_array($error_msg, $datos_excel['errores'])) {
                        $datos_excel['errores'][] = $error_msg;
                    }
                }
                $row['C']=strtoupper($row['C']);
                if(!in_array($row['C'], $units)){
                    $confirm_info = false;
                    $datos_excel['info'][] = sprintf("Información fila: %s columna: %s ... %s", $k, "C", "el tipo de Unidad '".$row['C']."' no existe, por ende se creará automáticamente.");
                }
            }
            if(isset($this->request->data['confirm_info']) && $this->request->data['confirm_info']==1){
                $confirm_info=true;
            }
            //logica save a db
            if(count($datos_excel['errores']) < 1 && $confirm_info) {
                //no hay errores se guarda el archivo.
                //logica save a db
                //carga modelo units y revizo si existe el tipo de unidad, si no existe se crea, si existe se asocia.
                $this->loadModel('Units');
                $this->loadModel('BudgetItems');
                unset($k);
                unset($row);
                foreach($datos_excel['excel'] as $k=>$row) {
                   // check si existe unidad, si no existe se crea.
                    $idUnidad = '';
                    if(isset($row['C'])){
                        $queryUnidades = $this->Units->findByName($row['C']);
                        $Unidad = $queryUnidades->first();
                        if(empty($Unidad)) {
                            if($row['C'] != null) {
                                $newUnidad = $this->Units->newEntity(array('name' => $row['C'], 'description' => $row['C']));
                                if($this->Units->save($newUnidad)) {
                                    $idUnidad = $newUnidad->Get('id');
                                }
                            }
                            else {
                                $idUnidad = 1;
                            }
                        }
                        else {
                            $idUnidad = $Unidad->Get('id');
                        }
                    }else{
                        $idUnidad=1;
                    }

                    $oldBudgetItemQuery = $this->BudgetItems->find('all')
                    ->where(['BudgetItems.budget_id =' => $id, 'BudgetItems.item =' => $row['A']])
                    ->limit(10);

                    $oldBudgetItem = $oldBudgetItemQuery->first();
                    //debug($oldBudgetItem);
                    if($oldBudgetItem != null) {
                        ($showDebug) ? debug("item ". $row['A'] . " asociado al presupuesto " . $id . " ya existe en BD.") : false;
                        continue;
                    }
                    $quantity = (!isset($row['D'])) ? 0 : floatval(str_replace(",", ".", $row['D']));
                    $unity_price = (!isset($row['E'])) ? 0 : floatval(str_replace(",", ".", $row['E']));
                    $total_price = (!isset($row['F'])) ? 0 : floatval(str_replace(",", ".", $row['F']));
                    $comments = (!isset($row['G'])) ? '' : $row['G'];
                    $target_value = (!isset($row['H'])) ? $total_price : floatval(str_replace(",", ".", $row['H']));

                    $item =  array(
                        'budget_id' => $id,
                        'item' => $row['A'],
                        'description' => $row['B'],
                        'unit_id' => $idUnidad,
                        'quantity' => $quantity,
                        'unity_price' => $unity_price,
                        'total_price' => $total_price,
                        'comments' => $comments,
                        'target_value' => $target_value,
                        'disabled' => 0,
                        'extra' => 2);
                    ($k >= 486) ? debug($item) : false;
                    $budgetItem = $this->BudgetItems->newEntity($item);
                    //filtro el item para ver si corresponde a hijo o padre
                    $parentIdArr = explode('.', $row['A']);
                    $lastItemChars = array_pop($parentIdArr);
                    $parentId = (!empty($parentIdArr)) ? implode(".",$parentIdArr) : false;
                    //si corresponde a nodo hijo, busco el padre
                    if($parentId != false) {
                        //debug('parentId: ' . $parentId . ' Child: ' .  $row['A']);
                        $parentBudgetItemQuery = $this->BudgetItems->find('all')
                        ->where(['BudgetItems.budget_id =' => $id, 'BudgetItems.item =' => $parentId])
                        ->limit(10);
                        $parentBudgetItem = $parentBudgetItemQuery->first();
                        //asigno la id del padre al hijo como parent_id
                        if($parentBudgetItem != null) {
                            $budgetItem->parent_id = $parentBudgetItem->id;
                            $this->Budgets->updateParentsPrice($budgetItem, $total_price, 0, $target_value);
                        }
                    }
                    //logica save budget item
                    if($this->BudgetItems->save($budgetItem)) {
                        ($showDebug) ? debug('item: ' . $row['A'] . ' guardado!') : false;
                    }
                }
                //end logiva save a db
                ($showDebug) ? debug('guardado correctamente, errores: ' . count($datos_excel['errores'])) : false;
                $this->Flash->success('Los gastos no considerados han sido guardados correctamente.');
                // return $this->redirect(['controller' => 'Buildings', 'action' => 'dashboard', $sf_building->CodArn]);
                return $this->redirect(['controller' => 'Budgets', 'action' => 'add_expense', $id]);
            }
            else {
                //errores de validacion en el archivo, borrar el temporal y mostrar errores.
                ($showDebug) ? debug('se encontraron errores no se puede guardar.') : false;
                $this->Flash->warning('Se encontraron errores no se ha podido guardar.');

            }
        }
        $errores = (!empty($datos_excel['errores'])) ? $datos_excel['errores'] : array('No se encontraron errores.');
        $info = (!empty($datos_excel['info'])) ? $datos_excel['info'] : array();
        $this->set('errores', $errores);
        $units = $this->Budgets->BudgetItems->Units->find('list', ['limit' => 200])->toArray();
        //$users = $this->Budgets->Users->find('list', ['limit' => 200]);
        $this->set('new_parent', $new_parent);
        $this->set('datos_excel', $datos_excel);
        $this->set(compact('budget', 'units', 'users', 'info'));
        $this->set('_serialize', ['budget']);
    }


    public function disable_item($itemId = null)
    {
        if($itemId) {
            $this->loadModel('BudgetItems');
            $budgetItem = $this->BudgetItems->get($itemId);
            $bic = $this->BudgetItems->find('children', ['for' => $itemId])->all()->toArray();
            $ps = ($budgetItem->disabled === 1) ? 0 : 1;
            array_unshift($bic, $budgetItem);
            $total_items = count($bic);
            foreach($bic as $t) {
                $t->disabled = $ps;
                $txt_action = ($ps === 0) ? 'Habilitada' : 'Deshabilitada';
                if($this->BudgetItems->save($t)) {
                    $total_items--;
                }
            }
            if($total_items === 0) {
                $this->Flash->success('La partida: ' . $budgetItem->item . ' ha sido ' . $txt_action . ' correctamente');
            } else {
                $this->Flash->error('Se produjo un error y el item: ' . $budgetItem->item . ' no pudo ser ' . $txt_action . ',  intente nuevamente');
            }
            $total_cost = $this->Budgets->calc_total($budgetItem->budget_id,0,1);
            $budget = $this->Budgets->get($budgetItem->budget_id);
            $old_total_cost = $budget->total_cost;
            $budget->total_cost = $total_cost;
            if($this->Budgets->save($budget)) {
                $this->loadModel('Observations');
                    $observation = $this->Observations->newEntity();
                    $observation->model = 'Budgets';
                    $observation->action = 'disable_item';
                    $observation->model_id = $budgetItem->budget_id;
                    $observation->user_id =  $this->request->session()->read('Auth.User.id');
                    $observation->observation = '[Cambio Costo Total] Anterior: ' . moneda($old_total_cost) . '  Nuevo: ' . $total_cost . '. La partida: ' . $budgetItem->item . ' fue ' . strtolower($txt_action) . '.';
                    if ($this->Observations->save($observation)) {
                    }
            }
            $unitsQ = $this->Budgets->BudgetItems->Units->find('list', ['limit' => 200]);
            $units = $unitsQ->toArray();
            $this->set(compact('units','budgetItem'));
            return $this->redirect(['action' => 'review', $budgetItem->budget_id]);
        }

    }

    public function item_param($itemId = null)
    {
        if($itemId) {
            $this->loadModel('BudgetItems');
            $budgetItem = $this->BudgetItems->get($itemId);
            if ($this->request->is(['patch', 'post', 'put'])) {
                $updateBi = $this->BudgetItems->patchEntity($budgetItem, $this->request->data);
                if($this->BudgetItems->save($updateBi)) {
                    $this->Flash->success('El item: ' . $updateBi->item . ' ha sido modificado correctamente');
                    return $this->redirect(['action' => 'review', $updateBi->budget_id]);
                } else {
                    $this->Flash->error('Se produjo un error y el item: ' . $updateBi->item . ' no pudo ser modificado,  intente nuevamente');
                }
            }
            $units = $this->Budgets->BudgetItems->Units->find('list', ['limit' => 200])->toArray();
            $leaf = ($this->BudgetItems->childCount($budgetItem) < 1) ? true : false;

            $budget = $this->Budgets->get($budgetItem->budget_id, [
                'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'BudgetItems']
            ]);
            $currentState = $this->Budgets->current_state($budgetItem->budget_id);
            //budget_state 4 en curso

            if(($budgetItem->extra === 0 && $currentState == 4) && ($budgetItem->extra === 1 && $currentState == 6)) {
                $this->Flash->error('El item: ' . $updateBi->item . ' no pudo ser modificado, en el estado actual del presupuesto');
                return $this->redirect(['action' => 'review', $updateBi->budget_id]);
            }
            //información general
            $this->loadModel('SfBuildings');
            $sf_building = $this->SfBuildings->find('all', [
                 'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
            ])->first();



            $this->set(compact('units','budgetItem','leaf', 'budget','sf_building','originalDisabled'));
        }
    }

    public function comment($id = null)
    {
        //
        if($id == null) {
            $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
            return $this->redirect($this->referer());
        }
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Budgets->Users->getUserBuildings($user_id);
            if (count($user_buildings) > 0) {
                $buildings = array_combine($user_buildings,$user_buildings);
                $budget_id = $this->Budgets->find('all', [
                    'conditions' => ['Budgets.building_id' => $user_buildings[0]]
                ])->first();
            } else {
                $this->Flash->info('El usuario no está asociado a ninguna obra. Por favor, edite la información de usuario.');
                return $this->redirect(['controller' => 'users', 'action' => 'index']);
            }
            if (!empty($budget_id->building_id) && $budget_id != null) {
                if ($budget_id->id != $id) {
                    $this->Flash->info('El usuario no está asociado a ninguna obra o esta no corresponde a los tratos. Por favor, edite la información de usuario.');
                    return $this->redirect(['controller' => 'users', 'action' => 'index']);
                }
            } else {
                $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
                return $this->redirect(['action' => 'index']);
            }
        }
        //
        $this->loadModel('Observations');
        $budget_id = $id;
        $observations = $this->Observations->find('all')
                        ->where(['Observations.model_id =' => $id, 'Observations.model =' => 'Budgets'])
                        ->order(['Observations.created' => 'DESC'])
                        ->contain(['Users'])->toArray();
        $observation = $this->Observations->newEntity();
        if ($this->request->is('post')) {
            $observation = $this->Observations->patchEntity($observation, $this->request->data);
            $observation->model = 'Budgets';
            $observation->action = 'comment';
            $observation->model_id = $id;
            $observation->user_id = $this->request->session()->read('Auth.User.id');
            if ($this->Observations->save($observation)) {
                $this->Flash->success('El comentario ha sido guardado correctamente.');
                return $this->redirect(['action' => 'review',$id]);
            } else {
                $this->Flash->error('La observacion no pudo ser grabada. Por favor intente nuevamente.');
            }
        }
        $users = $this->Observations->Users->find('list',
            ['limit' => 200,
            'keyField' => 'id',
            'valueField' => 'full_name'])->toArray();

        $this->set(compact('observation', 'users', 'observations','budget_id'));
        $this->set('_serialize', ['observation']);
    }

    /**
     * Reporte estado global de la obra
     * @param  string $id identificador del presupuesto de obra
     * @return arrays     información de obra relacionada a los modulos del sistema
     * @author Diego De la Cruz <diego.delacruz@ideauno.cl>
     */
    public function global_state($id = '')
    {
        // cucho: si no hay un cache de la vista
        if (($vista_mascara = Cache::read('globalstate_' . $id, 'config_cache_mascara')) === false) {
            // cucho: fin
            $group_id = $this->request->session()->read('Auth.User.group_id');
            if (!empty($id) && $id != null) {
                if ($group_id == USR_GRP_GE_GRAL || $group_id == USR_GRP_GE_FINAN || $group_id == USR_GRP_COORD_PROY) {
                    $budget = $this->Budgets->get($id, [
                        'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'PaymentStatements','PaymentStatements'=>['PaymentStatementStates']],
                    ]);
                    $this->loadModel('SfBuildings');
                    $sf_building = $this->SfBuildings->find('all', [
                         'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
                    ])->first();
                    $utilities_contract = ($budget->total_cost + $budget->general_costs) * ($budget->utilities / 100);
                    $total_contract_currency = round(($budget->total_cost + $budget->general_costs + $utilities_contract) / $budget->currencies_values[0]['value'], 2);
                    // $proyected_contract_cost_month = round($total_contract_currency / $budget->duration, 2);
                    // debug($budget->created->format('d-m-Y'));
                    $now_date = new \DateTime('now');
                    $budget_start_date = new \DateTime($budget->created->format('d-m-Y'));
                    $budget_date_created_months = new \DateTime($budget->created->format('d-m-Y'));
                    $budget_finish_date = new \DateTime($budget->created->format('d-m-Y'));
                    $budget_finish_date->modify('+' . $budget->duration . ' month');
                    $total_days = $this->Budgets->totalDaysBudget($budget_start_date, $budget->duration);
                    $total_days_months = $this->Budgets->totalDaysBudgetMonths($budget_start_date, $budget->duration);
                    $months = $this->Budgets->getListMonthsBudget($budget_date_created_months, $budget->duration);
                    $total_salaries = $this->Budgets->Assists->getTotalSalariesToDate($id, $now_date, $months);
                    $schedules_progress_info = $this->Budgets->getSchedulesProgressInfo($budget->id);
                    $budget_progress_info = $this->Budgets->proyected_progress_budget($budget->id, $schedules_progress_info, $total_days, $total_days_months, $total_contract_currency);
                    $budget_progress_compare_months_info = $this->Budgets->proyected_progress_budget_compare_months($budget->id, $schedules_progress_info, $total_days, $total_days_months, $total_contract_currency);
                    // Estados de EDP, estado 1 no vá
                    $edps_states = $this->Budgets->PaymentStatements->PaymentStatementStates->find()->where(['PaymentStatementStates.id > 1'])->toArray();
                    // IConstruye
                    $iconstruye_stats = $this->Budgets->getIconstruyeStatsByBudgetId($id, $months);
                    // debug($months); //die();
                    // debug($total_days_months); //die();
                    // debug($total_days); //die();
                    // debug($total_contract_currency); //die();
                    // debug($schedules_progress_info); //die();
                    // debug($proyected_contract_cost_month); die();
                    // debug($budget_start_date->format('d-m-Y')); //die();
                    // debug($budget->created->format('d-m-Y')); die();
                    // debug($budget_progress_info);
                    // debug($budget_progress_compare_months_info); die();
                    $this->set(compact('budget', 'now_date', 'budget_start_date', 'budget_finish_date', 'sf_building', 'utilities_contract', 'total_contract_currency', 'total_days', 'total_days_months',
                    'months', 'total_salaries', 'schedules_progress_info', 'budget_progress_info', 'budget_progress_compare_months_info', 'edps_states', 'iconstruye_stats'));
                } else {
                    $this->Flash->info('El perfil de usuario no tiene acceso a esta información.');
                    return $this->redirect(['controller' => 'users', 'action' => 'home']);
                }
            } else {
                $this->Flash->error('Ocurrió un error al buscar la información del presupuesto. Por favor, inténtelo nuevamente.');
                return $this->redirect(['controller' => 'users', 'action' => 'home']);
            }
            // cucho: escribe el render en un cache
            Cache::write('globalstate_' . $id, $this->render(), 'config_cache_mascara');

        } else { // cucho: hay un cache

            // cucho: lee el cache y le hace un render, parece que ultrajo los estandares)
            echo Cache::read('globalstate_' . $id, 'config_cache_mascara');

            // cucho: no hace render
            $this->autoRender = false;
        }
        // cucho: fin

    }

    public function review($budget_id = null)
    {
        $this->loadModel('BudgetItems');
        $this->loadModel('BudgetItemsSchedules');
        $budget = array();
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Budgets->UserCreateds->getUserBuildings($this->request->session()->read('Auth.User.id'));
            if (count($user_buildings) > 0) {
                $budget = $this->Schedules->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates'],
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
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates'],
                    'conditions' => ['Budgets.building_id' => $buildings[0]]
                ])->first();
                $budget_id = $budget->id;
            } else {
                $budget = $this->Budgets->get($budget_id, [
                    'contain' => [
                        'Buildings' => ['BuildingsUsers' => ['Users']], 
                        'CurrenciesValues' => ['Currencies'], 
                        'Currencies' => ['Valoresmonedas' => function ($q) {
                               return $q->limit(1);
                        }], 
                        'Users', 
                        'BudgetApprovals',
                        'BudgetApprovals.BudgetStates'
                    ]
                ]);
            }
        }
        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
        ])->first();
        // Load budgetItems
        $bi = $this->BudgetItems
        ->find('all', [
            'conditions' => ['budget_id' => $budget_id, 'parent_id IS' => null, 'BudgetItems.disabled' => 0]
            ])
        ->contain([
            'Units',
            'Progress' => function ($q) {
                return $q
                    ->order(['Progress.proyected_progress_percent' => 'DESC']);
                }
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
                    },
                'Materials'
            ])
            ->toArray();
            $budget_items[$value->id] = $value->toArray();
            $budget_items[$value->id]['children'] = $children;
        }
        // debug($this->Budgets->updateGeneralCosts($budget_id));
        $this->set(compact('schedule', 'budget_items', 'budget', 'sf_building'));
        $this->set('_serialize', ['schedule']);
        $this->set('budget_id', $budget_id);

        $this->loadModel('Observations');
        $observations = $this->Observations->find('all')
                        ->where(['Observations.model_id =' => $budget_id, 'Observations.model =' => 'Budgets'])
                        ->order(['Observations.created' => 'DESC'])
                        ->contain(['Users'])->toArray();

        $this->set('observations', $observations);
    }

    /**
     * View method
     *
     * @param string|null $id Budget id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function mask($id = null)
    {
        $this->review($id);
    }

    public function details($budget_id = null)
    {
        $this->loadModel('BudgetItems');
        $this->loadModel('BudgetItemsSchedules');
        $budget = array();
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->Budgets->UserCreateds->getUserBuildings($this->request->session()->read('Auth.User.id'));
            if (count($user_buildings) > 0) {
                $budget = $this->Schedules->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates'],
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
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates'],
                    'conditions' => ['Budgets.building_id' => $buildings[0]]
                ])->first();
                $budget_id = $budget->id;
            } else {
                $budget = $this->Budgets->get($budget_id, [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates']
                ]);
            }
        }
        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
        ])->first();
        // Load budgetItems
        $bi = $this->BudgetItems
        ->find('all', [
            'conditions' => ['budget_id' => $budget_id, 'parent_id IS' => null, 'BudgetItems.disabled' => 0]
            ])
        ->contain([
            'Units',
            'Progress' => function ($q) {
                return $q
                    ->order(['Progress.proyected_progress_percent' => 'DESC']);
                }
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
                    },
                'Materials'
            ])
            ->toArray();
            $budget_items[$value->id] = $value->toArray();
            $budget_items[$value->id]['children'] = $children;
        }
        // debug($this->Budgets->updateGeneralCosts($budget_id));
        $this->set(compact('schedule', 'budget_items', 'budget', 'sf_building'));
        $this->set('_serialize', ['schedule']);
        $this->set('budget_id', $budget_id);
    }

}
