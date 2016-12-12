<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Cache\Cache;

/**
 * Buildings Controller
 *
 * @property \App\Model\Table\BuildingsTable $Buildings */
class BuildingsController extends AppController
{

    public $paginate = [
        'limit' => 50,
        'order' => [
            'CodArn' => 'desc'
        ]
    ];

    public function initialize()
    {
        parent::initialize();
        //Cargar siempre modelo de Obras de Softlandasd
        $this->loadModel('SfBuildings');
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        //hacer vinculo con obras internas y presupuesto
        // $active_workers_buildings = $this->Buildings->Budgets->Assists->Workers->checkBuildingsWithWorkersActive();
        $buildings_budgets = $this->Buildings->getBuildingsWithBudgets();

        $omit_buildings = $this->Buildings->find('list', [
            'keyField' => 'softland_id',
            'valueField' => 'softland_id',
        ])->where(['Buildings.omit' => true]);

        $not_buildings = array('001' => '001', '002' => '002', '004' => '004');
        if ($omit_buildings->isEmpty()) {
            $sf_buildings = $this->SfBuildings->find('all', [
                'conditions' => ['codArn IS NOT NULL', 'codArn NOT IN' =>  $not_buildings]]);
        } else {
            $ignore_buildings = Hash::merge($not_buildings, $omit_buildings->toArray());
            $sf_buildings = $this->SfBuildings->find('all')->where(['SfBuildings.CodArn NOT IN' => $ignore_buildings]);
        }

        $this->set('buildings_budgets', $buildings_budgets);
        $this->set(compact('buildings_budgets'));
        $this->set('sf_buildings', $this->paginate($sf_buildings));
        // $this->set(compact('buildings_budgets', 'sf_buildings', 'active_workers_buildings'));
        $this->set('_serialize', ['sf_buildings']);
    }

    /**
     * View method
     *
     * @param string|null $id Building id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $building = $this->Buildings->get($id, [
            'contain' => ['Budgets']
        ]);
        debug($building);
        debug($building['softland_id']);
        $this->loadModel('SfBuildings');
        $sf_buildings = $this->SfBuildings->find('all', [
            // 'conditions' => ['SfBuildings.CodArn' => $building['softland_id']]

        ]);
        debug($sf_buildings->toArray()); die();

        $this->set('building', $building);
        $this->set('_serialize', ['building']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $building = $this->Buildings->newEntity();
        if ($this->request->is('post')) {
            $building = $this->Buildings->patchEntity($building, $this->request->data);
            if ($this->Buildings->save($building)) {
                $this->Flash->success('La obra ha sido guardada.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('La obra no ha sido guardada, intentalo nuevamente.');
            }
        }
        $this->set(compact('building'));
        $this->set('_serialize', ['building']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Building id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($softland_id = null)
    {
        $building = $this->Buildings->find('all', [
             'conditions' => ['Buildings.softland_id' => $softland_id]
        ])->first();
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $building['softland_id']]
        ])->first();
        if ($this->request->is(['patch', 'post', 'put'])) {
            $building = $this->Buildings->patchEntity($building, $this->request->data);
            if ($this->Buildings->save($building)) {
                $this->Flash->success('La obra ha sido actualizada.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('La obra no ha sido acxtualizada, intentalo nuevamente.');
            }
        }
        $this->set(compact('building','sf_building'));
        $this->set('_serialize', ['building']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Building id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $building = $this->Buildings->get($id);
        if ($this->Buildings->delete($building)) {
            $this->Flash->success('La obra ha sido eliminada.');
        } else {
            $this->Flash->error('La obra no ha sido eliminada, intentalo nuevamente.');
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Ignorar una obra de softland
     * @param  string $softland_id codigo de obra de softland
     * @return [type]              [description]
     */
    public function ignore_building($softland_id = '')
    {
        //Buscar registro building propio con softland id
        $building_associated = $this->Buildings->find('all', [
            'conditions' => ['Buildings.softland_id' => $softland_id]
        ])->first();
        if (!empty($building_associated) && $building_associated != null) { //si existe editamos
            $this->request->data['omit'] = 1;
            $building_associated = $this->Buildings->patchEntity($building_associated, $this->request->data);
            if ($this->Buildings->save($building_associated)) {
                $this->Flash->success('La obra ha sido ignorada correctamente');
            } else {
                $this->Flash->error('Ocurrió un error al ignorar la Obra. Por favor, inténtelo nuevamente.');
            }
        } else { //sino creamos registro con ignore true
            $building = $this->Buildings->newEntity();
            $building->softland_id = $softland_id;
            $building->omit = 1;
            $building->active = 1;
            if ($this->Buildings->save($building)) {
                $this->Flash->success('La obra ha sido ignorada correctamente');
            } else {
                $this->Flash->error('Ocurrió un error al ignorar la Obra. Por favor, inténtelo nuevamente.');
            }
        }
        return $this->redirect(['action' => 'current', 'none']);
    }

    /**
     * Habilitar una obra de softland
     * @param  string $softland_id codigo de obra de softland
     * @return [type]              [description]
     */
    public function enable_building($softland_id = '')
    {
        //Buscar registro building propio con softland id
        $building_associated = $this->Buildings->find('all', [
            'conditions' => ['Buildings.softland_id' => $softland_id]
        ])->first();
        if (!empty($building_associated) && $building_associated != null) { //si existe editamos
            $this->request->data['omit'] = 0;
            $building_associated = $this->Buildings->patchEntity($building_associated, $this->request->data);
            if ($this->Buildings->save($building_associated)) {
                $this->Flash->success('La obra ha sido habilitada correctamente');
            } else {
                $this->Flash->error('Ocurrió un error al habilitar la Obra. Por favor, inténtelo nuevamente.');
            }
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Lista de obras Ignoradas
     * @return [type] [description]
     * @author Diego De la Cruz B. <diego.delacruz@ideauno.cl>
     */
    public function omit_buildings()
    {
        //hacer vinculo con obras internas y presupuesto
        $buildings_budgets = $this->Buildings->getBuildingsWithBudgets();
        $omit_buildings = $this->Buildings->find('list', [
            'keyField' => 'softland_id',
            'valueField' => 'softland_id',
        ])->where(['Buildings.omit' => true]);
        $sf_buildings = $this->SfBuildings->find('all')->where(['SfBuildings.CodArn IN' => $omit_buildings->toArray()]);

        $this->set('buildings_budgets', $buildings_budgets);
        $this->set('sf_buildings', $this->paginate($sf_buildings));
        $this->set('_serialize', ['sf_buildings']);
    }

    /**
     * Cambiar estado activo
     * @return [type] [description]
     * @author Diego De la Cruz B. <diego.delacruz@ideauno.cl>
     */
    public function change_active($softland_id = null)
    {
        //hacer vinculo con obras internas y presupuesto
        $this->autoRender = false;
        if (!empty($softland_id) && $softland_id != null) {
            $group_id = $this->request->session()->read('Auth.User.group_id');
            if ($group_id != USR_GRP_ADMIN_OBRA || $group_id != USR_GRP_ASIS_RRHH || $group_id != USR_GRP_OFI_TEC) {
                $building = $this->Buildings->find('all', [
                    'conditions' => ['Buildings.softland_id' => $softland_id]
                ])->first();
                if (!empty($building) && $building != null) { //si existe editamos
                    ($building->active) ? $this->request->data['active'] = false : $this->request->data['active'] = true;
                    $building = $this->Buildings->patchEntity($building, $this->request->data);
                    if ($this->Buildings->save($building)) {
                        $this->Flash->success('La obra ha sido bloqueada correctamente');
                    } else {
                        $this->Flash->error('Ocurrió un error al bloquear la Obra. Por favor, inténtelo nuevamente.');
                    }
                } else {
                    $building = $this->Buildings->newEntity();
                    $building->softland_id = $softland_id;
                    $building->active = 0;
                    if ($this->Buildings->save($building)) {
                        $this->Flash->success('La obra ha sido bloqueada correctamente');
                    } else {
                        $this->Flash->error('Ocurrió un error al bloquear la Obra. Por favor, inténtelo nuevamente.');
                    }
                }
            }
        } else {
            $this->Flash->error('Ocurrió un error al bloquear la Obra. Por favor, inténtelo nuevamente.');
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Cambiar obra con la que se va a trabajar
     * @param  $softland_id ID softland
     * @param  $sendToDashboard
     * @return [type] [description]
     * @author Diego De la Cruz B. <diego.delacruz@ideauno.cl>
     */
    public function current($softland_id = null, $sendToDashboard = false)
    {
        if(!$sendToDashboard){
            $redirectTo = ['action' => 'index'];
        }else{
            $redirectTo = ['action' => 'dashboard', $softland_id];
        }
        if($softland_id == 'none') {
            $this->request->session()->delete('Config.last_building_info');
            $this->request->session()->delete('Config.last_building');
            $this->Flash->success('La obra seleccionada se ha actualizado correctamente.');
            return $this->redirect($redirectTo);
        }
        //hacer vinculo con obras internas y presupuesto
        $this->autoRender = false;
        if (!empty($softland_id) && $softland_id != null) {
            $group_id = $this->request->session()->read('Auth.User.group_id');
            if ($group_id != USR_GRP_ADMIN_OBRA || $group_id != USR_GRP_ASIS_RRHH || $group_id != USR_GRP_OFI_TEC) {
                $building = $this->Buildings->find('all', [
                    'conditions' => ['Buildings.softland_id' => $softland_id]
                ])->first();
                if (!empty($building) && $building != null) { //si existe dejamos como obra activa
                    $sf_building_info = $this->Buildings->SfBuildings->find('all', [
                        'conditions' => ['codArn' => $softland_id]
                    ])->first();
                    if(!empty($sf_building_info) && $sf_building_info != null) {
                        $this->request->session()->write('Config.last_building_info', $sf_building_info->DesArn);
                        $this->request->session()->write('Config.last_building_sf_id', $sf_building_info->CodArn);
                    }
                    $this->request->session()->write('Config.last_building', $building->id);
                } else {
                    $this->Flash->error('Ocurrió un error al seleccionar la Obra. Por favor, inténtelo nuevamente.');
                }
            }
        } else {
            $this->Flash->error('Ocurrió un error al bloquear la Obra. Por favor, inténtelo nuevamente.');
        }
        return $this->redirect($redirectTo);
        // return $this->redirect(['action' => 'index']);
    }

    public function dashboard($softland_id){
        $this->loadModel('Budgets');
        //se obtiene building
        $building = $this->Buildings->find('all', [
            'conditions' => ['Buildings.softland_id' => $softland_id],
            'contain' => [
                'Budgets' => [
                    'CurrenciesValues' => [
                        'Currencies'
                    ],
                    'Users',
                    'BudgetApprovals' => function ($q) {
                    return $q->order(['BudgetApprovals.created ASC']);
                    },
                    'BudgetApprovals.BudgetStates',
                    'BudgetItems'
                ]
            ],
        ])->first();

        $budget = array();
        if($building->budget != null){
            $budget_id = $building->budget->id;
            $budget = $this->Budgets->get($budget_id, [
                'contain' => [
                    'Buildings' => ['BuildingsUsers' => ['Users']], 
                    'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }], 
                    'Users', 
                    'BudgetApprovals',
                    'BudgetApprovals.BudgetStates', 
                    'BudgetItems'
                ]
            ]);

            //debug($budget);

            $currentState = $this->Budgets->current_state($budget_id);
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

            $states = $this->Budgets->BudgetApprovals->BudgetStates->find('list')->toArray();

            $this->set(compact('nextState', 'states'));
        }

        $menus_to_show = ['Avance de Obra', 'Recursos Humanos', 'Avance', 'RRHH'];
        $menu = \Cake\Core\Configure::read('menu_usuarios.'.$this->request->session()->read('Auth.User.group_id'));
        $percentages_building=array();
        if(!empty($budget)){
            $menus_extras = [
                USR_GRP_GE_GRAL => [ // menú del gerente general
                    '1'  => ['title' => __('Avance'), 'icon' => 'mdi-action-assessment', 'items' => [
                            '0' => [ 'title' => __('Agregar Planificación'), 'controller' => 'schedules', 'action' => 'add', 'extra' =>$budget_id],
                        ]
                    ],
                ]
            ];
            $percentages_building = $this->Buildings->Budgets->getPercentageBudget($budget_id);
        }
        foreach($menu AS $key=>$m){
            if(isset($menus_extras[$this->request->session()->read('Auth.User.group_id')][$key])){
                $menu[$key]['items'] = array_merge($menu[$key]['items'], $menus_extras[$this->request->session()->read('Auth.User.group_id')][$key]['items']);
            }
        }
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $building['softland_id']]
        ])->first();
        $buildings_budgets = $this->Buildings->getBuildingsWithBudgets();
        $this->set(compact('building','sf_building', 'buildings_budgets', 'budget', 'percentages_building', 'menu', 'menus_to_show'));
        $this->set('_serialize', ['building']);
        $this->set('parche_softland_id', $softland_id);
    }
}
