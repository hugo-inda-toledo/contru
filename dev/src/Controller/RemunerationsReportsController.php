<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * RemunerationsReports Controller
 *
 * @property \App\Model\Table\RemunerationsReportsTable $RemunerationsReports
 */
class RemunerationsReportsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $budget = null;
        $buildings = null;
        $user_id = $this->request->session()->read('Auth.User.id');
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->RemunerationsReports->Users->getUserBuildings($user_id);
            if (count($user_buildings) > 0) {
                $buildings = array_combine($user_buildings,$user_buildings);
                $budget = $this->RemunerationsReports->Users->BuildingsUsers->Buildings->Budgets->find('all', [
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
            $buildings = $this->RemunerationsReports->Users->BuildingsUsers->Buildings->getActiveBuildingsWithSoftlandInfo();
            $last_building = $this->request->session()->read('Config.last_building');
            if (!empty($this->request->query)) {
                $budget = $this->RemunerationsReports->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => $this->request->query['building_id']]
                ])->first();
            } else {
                 if(!empty($last_building)) {
                    $budget = $this->RemunerationsReports->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => $last_building]
                ])->first();
                } else {
                    $budget = $this->RemunerationsReports->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                        'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                        'conditions' => ['Budgets.building_id' => key($buildings)]
                    ])->first();
                }
            }
        }


        if ($this->request->is('post')) {
            /*En caso que venga mes y dia de corte se debe crear un registro de reporte en la base de datos*/
            if(isset($this->request->data['RemunerationsReports']['month']) && $this->request->data['RemunerationsReports']['month']!="" && isset($this->request->data['RemunerationsReports']['day_cut']) && $this->request->data['RemunerationsReports']['day_cut']!=""){
                $buildingTable = TableRegistry::get('Buildings');
                $remunerationReportsTable = TableRegistry::get('RemunerationsReports');
                $building = $buildingTable->find('all', [
                    'conditions' => ['Buildings.softland_id' => $budget->building['softland_id']]
                ])->first();
                $this->request->data['RemunerationsReports']['user_id'] = $this->Auth->user('id');
                $this->request->data['RemunerationsReports']['building_id'] = $building->id;
                $this->request->data['RemunerationsReports']['codArn'] = $budget->building['softland_id'];
                $remunerationReports = $remunerationReportsTable->newEntity($this->request->data['RemunerationsReports']);

                if ($remunerationReportsTable->save($remunerationReports)) {
                    $this->Flash->success('El reporte se está generando, una vez finalizado podrá descargarlo desde acá.');
                    return $this->redirect(['controller' => 'remunerations_reports', 'action'=>'index']);
                    unset($this->request->data['RemunerationsReports']);
                }
            }
        }


        //información general
        $this->loadModel('SfBuildings');
        $sf_building = $this->SfBuildings->find('all', [
             'conditions' => ['SfBuildings.CodArn' => $budget->building['softland_id']]
        ])->first();

        $this->paginate = [
            'conditions' => [
                'RemunerationsReports.building_id' => $budget->building['id'],
                'RemunerationsReports.user_id' => $user_id
            ],
            'contain' => ['Users'],
            'order' => [
                'RemunerationsReports.id' => 'DESC'
            ]
        ];

        $remunerationsReports = $this->paginate($this->RemunerationsReports);

        $status = $this->RemunerationsReports->status;

        $this->set(compact('remunerationsReports', 'status', 'buildings', 'budget', 'sf_building'));
        $this->set('_serialize', ['remunerationsReports']);
    }

    /**
     * View method
     *
     * @param string|null $id Remunerations Report id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $budget = null;
        $buildings = null;
        $user_id = $this->request->session()->read('Auth.User.id');
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) {
            $user_buildings = $this->RemunerationsReports->Users->getUserBuildings($user_id);
            if (count($user_buildings) > 0) {
                $buildings = array_combine($user_buildings,$user_buildings);
                $budget = $this->RemunerationsReports->Users->BuildingsUsers->Buildings->Budgets->find('all', [
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
            $buildings = $this->RemunerationsReports->Users->BuildingsUsers->Buildings->getActiveBuildingsWithSoftlandInfo();
            $last_building = $this->request->session()->read('Config.last_building');
            if (!empty($this->request->query)) {
                $budget = $this->RemunerationsReports->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals','BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => $this->request->query['building_id']]
                ])->first();
            } else {
                 if(!empty($last_building)) {
                    $budget = $this->RemunerationsReports->Users->BuildingsUsers->Buildings->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates', 'Currencies' => ['Valoresmonedas' => function ($q) {
                           return $q->limit(1);
                    }]],
                    'conditions' => ['Budgets.building_id' => $last_building]
                ])->first();
                } else {
                    $budget = $this->RemunerationsReports->Users->BuildingsUsers->Buildings->Budgets->find('all', [
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
        $remunerationsReport = $this->RemunerationsReports->get($id, [
            'contain' => ['Users']
        ]);

        $status = $this->RemunerationsReports->status;

        $this->set(compact('remunerationsReport', 'status', 'buildings', 'budget', 'sf_building'));
    }


    public function download_file($report_id){
        $user_id = $this->request->session()->read('Auth.User.id');
        $group_id = $this->request->session()->read('Auth.User.group_id');
        if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC || $group_id == USR_GRP_GE_GRAL) {
            $remunerationsReport = $this->RemunerationsReports->get($report_id);
            if(!empty($remunerationsReport)){
                $this->response->file($remunerationsReport->path, ['download' => true]);
                return $this->response;
            }
        }
        $this->Flash->error('Ocurrió un error al buscar la información del archivo. Por favor, inténtelo nuevamente.');
        $this->redirect(['action' => 'index']);
    }


    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
    public function add()
    {
        $remunerationsReport = $this->RemunerationsReports->newEntity();
        if ($this->request->is('post')) {
            $remunerationsReport = $this->RemunerationsReports->patchEntity($remunerationsReport, $this->request->data);
            if ($this->RemunerationsReports->save($remunerationsReport)) {
                $this->Flash->success(__('The remunerations report has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The remunerations report could not be saved. Please, try again.'));
            }
        }
        $users = $this->RemunerationsReports->Users->find('list', ['limit' => 200]);
        $this->set(compact('remunerationsReport', 'users'));
        $this->set('_serialize', ['remunerationsReport']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Remunerations Report id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
    public function edit($id = null)
    {
        $remunerationsReport = $this->RemunerationsReports->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $remunerationsReport = $this->RemunerationsReports->patchEntity($remunerationsReport, $this->request->data);
            if ($this->RemunerationsReports->save($remunerationsReport)) {
                $this->Flash->success(__('The remunerations report has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The remunerations report could not be saved. Please, try again.'));
            }
        }
        $users = $this->RemunerationsReports->Users->find('list', ['limit' => 200]);
        $this->set(compact('remunerationsReport', 'users'));
        $this->set('_serialize', ['remunerationsReport']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Remunerations Report id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $remunerationsReport = $this->RemunerationsReports->get($id);
        if ($this->RemunerationsReports->delete($remunerationsReport)) {
            $this->Flash->success(__('The remunerations report has been deleted.'));
        } else {
            $this->Flash->error(__('The remunerations report could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
     */
}
