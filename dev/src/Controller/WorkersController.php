<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Cache\Cache;

/**
 * Workers Controller
 *
 * @property \App\Model\Table\WorkersTable $Workers */
class WorkersController extends AppController
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
     * Index method
     *
     * @return void
     */
    public function index($cod = null)
    {
        $cache_name = 'workers_index_001';
        if ($this->request->is('post')) {
            $codArn = $this->request->data['SfWorkerBuildings']['codArn'];
            $cache_name = 'workers_index_'.$codArn;
        }
        elseif($cod != null)
        {
            $codArn = $cod;
            $cache_name = 'workers_index_'.$codArn;
        }
        // cucho: si no hay un cache de la vista
        if (($vista_mascara = Cache::read($cache_name, 'config_cache_mascara')) === false) {
            // cucho: fin

            if ($this->request->is('post')) {
                $codArn = $this->request->data['SfWorkerBuildings']['codArn'];
                $last_search = $codArn;
                /*En caso que venga mes y dia de corte se debe crear un registro de reporte en la base de datos*/
                if(isset($this->request->data['RemunerationsReports']['month']) && $this->request->data['RemunerationsReports']['month']!="" && isset($this->request->data['RemunerationsReports']['day_cut']) && $this->request->data['RemunerationsReports']['day_cut']!=""){
                    if($codArn!=""){
                        $buildingTable = TableRegistry::get('Buildings');
                        $remunerationReportsTable = TableRegistry::get('RemunerationsReports');
                        $building = $buildingTable->find('all', [
                            'conditions' => ['Buildings.softland_id' => $codArn]
                        ])->first();
                        $this->request->data['RemunerationsReports']['user_id'] = $this->Auth->user('id');
                        $this->request->data['RemunerationsReports']['building_id'] = $building->id;
                        $this->request->data['RemunerationsReports']['codArn'] = $codArn;
                        $remunerationReports = $remunerationReportsTable->newEntity($this->request->data['RemunerationsReports']);

                        if ($remunerationReportsTable->save($remunerationReports)) {
                            $this->Flash->success('El reporte se está generando, una vez finalizado podrá descargarlo desde acá.');
                            return $this->redirect(['controller' => 'remunerations_reports', 'action'=>'index']);
                            unset($this->request->data['RemunerationsReports']);
                        }
                    }
                }
            } else {
                $codArn = '001';
                $last_search = $codArn;
            }
            $this->loadModel('Buildings');
            $last_building = $this->request->session()->read('Config.last_building');
             if(!empty($last_building)) {
                $budget = $this->Buildings->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates'],
                    'conditions' => ['Budgets.building_id' => $last_building]
                ])->first();
            }else{
                return $this->redirect(['controller'=>'buildings', 'action' => 'dashboard', $codArn]);
            }
            $this->set(compact('last_search'));
            // debug($codArn); die();
            $buildings_budgets = $this->Buildings->getBuildingsWithBudgets();
            $omit_buildings = $this->Buildings->find('list', [
                'keyField' => 'softland_id',
                'valueField' => 'softland_id',
            ])->where(['Buildings.omit' => true]);
            $not_buildings = array('001' => '001', '002' => '002', '004' => '004');
            if ($omit_buildings->isEmpty()) {
                $sf_buildings = $this->SfBuildings->find('list', [
                    'conditions' => ['codArn IS NOT NULL', 'codArn NOT IN' => ['001', '002', '004']]]);
            } else {
                $ignore_buildings = Hash::merge($not_buildings, $omit_buildings->toArray());
                $sf_buildings = $this->SfBuildings->find('list')->where(['SfBuildings.CodArn NOT IN' => $ignore_buildings]);
            }

            /*$building_workers = $this->SfWorkerBuildings->find('all', [
                'conditions' => ['SfWorkerBuildings.codArn' => $codArn]
            ]);
            $array_fichas_building = array();
            foreach ($building_workers as $building_worker) {
                $array_fichas_building[] = $building_worker->ficha;
            }*/
            // debug($array_fichas_building); //die();
            // $workers_status = $this->SfWorkersStatus->find()
            //     ->where(['SfWorkers.ficha IN' => $array_fichas_building]);
            //     debug($workers_status->toArray());
            //

            $sf_workers = $this->Workers->getSoftlandWorkersByBuildingTest($budget->building_id);

            $date = date('Y-m');
            foreach($sf_workers as $k => $ficha) {
                // if(($date > $ficha['vigHasta'])) {
                if($ficha['vigHasta'] != "9999-12-01 00:00:00"){
                    unset($sf_workers[$k]);
                }
                // var_dump($ficha['vigHasta']);
            }
            // pr(count($sf_workers));
            /*pr($array_fichas_building);
            die();
            $sf_workers = [];
            if( count($array_fichas_building) > 0 )
            {
                $sf_workers = $this->SfWorkers->find()
                    ->where(['SfWorkers.ficha IN' => $array_fichas_building])
                    ->order(['nombres' => 'ASC']);
                    //->where(['SfWorkers.ficha IN' => $array_fichas_building, 'SfWorkers.fechaFiniquito' => '99991231']);
                    //->where(['SfWorkers.ficha IN' => $array_fichas_building, 'SfWorkers.fechaFiniquito' => '9999-12-31 00:00:00.000']);
                // debug($query->toArray()); die();
            }*/
            // pr($sf_workers);
            // die();
            $this->set('sf_workers', $sf_workers);
            // $this->set('_serialize', ['sf_workers']);
            $this->set(compact('sf_buildings'));
            // cucho: escribe el render en un cache
            Cache::write($cache_name, $this->render(), 'config_cache_mascara');

        } else { // cucho: hay un cache
            if ($this->request->is('post')) {
                $codArn = $this->request->data['SfWorkerBuildings']['codArn'];
                $last_search = $codArn;
                /*En caso que venga mes y dia de corte se debe crear un registro de reporte en la base de datos*/
                if(isset($this->request->data['RemunerationsReports']['month']) && $this->request->data['RemunerationsReports']['month']!="" && isset($this->request->data['RemunerationsReports']['day_cut']) && $this->request->data['RemunerationsReports']['day_cut']!=""){
                    if($codArn!=""){
                        $buildingTable = TableRegistry::get('Buildings');
                        $remunerationReportsTable = TableRegistry::get('RemunerationsReports');
                        $building = $buildingTable->find('all', [
                            'conditions' => ['Buildings.softland_id' => $codArn]
                        ])->first();
                        $this->request->data['RemunerationsReports']['user_id'] = $this->Auth->user('id');
                        $this->request->data['RemunerationsReports']['building_id'] = $building->id;
                        $this->request->data['RemunerationsReports']['codArn'] = $codArn;
                        $remunerationReports = $remunerationReportsTable->newEntity($this->request->data['RemunerationsReports']);

                        if ($remunerationReportsTable->save($remunerationReports)) {
                            $this->Flash->success('El reporte se está generando, una vez finalizado podrá descargarlo desde acá.');
                            return $this->redirect(['controller' => 'remunerations_reports', 'action'=>'index']);
                            unset($this->request->data['RemunerationsReports']);
                        }
                    }
                }
            }
            // cucho: lee el cache y le hace un render, parece que ultrajo los estandares)
            echo Cache::read($cache_name, 'config_cache_mascara');

            // cucho: no hace render
            $this->autoRender = false;
        }
        // cucho: fin
    }

    /**
     * [view description]
     * @param  [type] $ficha [description]
     * @return [type]        [description]
     * @author Gabriel <gabriel.rebolledo@ideauno.cl>
     */
    public function view($ficha = null)
    {
        if ($this->request->is('post')) {
            if(!empty($this->request->data['worker']['ficha']) && !empty($this->request->data['SfWorkerBuildings']['codArn'])) {
                $codArn = $this->request->data['SfWorkerBuildings']['codArn'];
                $last_search = $codArn;
                $this->set(compact('last_search'));


                $worker_info = $this->Workers->getSoftlandWorkerAndRentaInfoByWorkerId($this->request->data['worker']['ficha']);
                $worker_info = reset($worker_info);
                $worker_payments = $this->Workers->getLastRentaInfoByWorkerId($this->request->data['worker']['ficha']);

                if(isset($this->request->data['month']) && $this->request->data['month']!="" && isset($this->request->data['day_cut']) && $this->request->data['day_cut']!=""){
                    // Sueldo Base: H001
                    $days_month=30;
                    $month = $this->request->data['month'];
                    $worker = $this->Workers->find('all', [
                        'conditions' => [
                            'Workers.softland_id' => $this->request->data['worker']['ficha']
                        ],
                        'recursive' => -1
                    ])->first();
                    $ingresosExtra = $this->Workers->getTotalBonusAndDeals($worker->id, $month);
                    // pr($worker_info);
                    $assists = $this->Workers->getAssistsApprovalByMonthAndWorkerId($month, date('Y'), $worker->id, $this->request->data['day_cut'], $worker_info['fechaIngreso']->format('Y-m-d'));
                    $search_results=[];
                    $sb=0;

                    $search_results_prev=[];

                    $infoPago=$worker_payments;
                    if(empty($worker_payments)){
                        //Buscar si tiene seteado alguna variable
                        $infoPago = $this->Workers->getLastRentaInfoByWorkerId($this->request->data['worker']['ficha'], "H001", $month);
                    }
                    $search_results = $this->Workers->generateSearchResult($this->request->data, $worker, $worker_info, $assists, $ingresosExtra, $infoPago);
                    // pr($assists);
                    // die();
                    $this->set(compact('search_results'));
                }

                // die();

                if(empty($worker_info['ficha']) || !isset($worker_info['ficha'])) {
                    $this->Flash->info('No se encontro informacion del trabajador en softland.');
                    return $this->redirect(['controller' => 'workers', 'action' => 'index']);
                }
                if(!empty($worker_info['nombres'] && !empty($worker_info['appaterno']))) {
                    $worker_info['nombres'] = str_replace($worker_info['appaterno'], '', $worker_info['nombres']);
                }
                if(!empty($worker_info['nombres'] && !empty($worker_info['apmaterno']))) {
                    $worker_info['nombres'] = str_replace($worker_info['apmaterno'], '', $worker_info['nombres']);
                }
                $this->set(compact('worker', 'worker_info','worker_payments'));
                $this->set('_serialize', ['worker']);
            }
        } else {
            $this->Flash->info('No se encontro informacion del trabajador en softland.');
            return $this->redirect(['controller' => 'workers', 'action' => 'index']);
        }

    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $worker = $this->Workers->newEntity();
        if ($this->request->is('post')) {
            $worker = $this->Workers->patchEntity($worker, $this->request->data);
            if ($this->Workers->save($worker)) {
                $this->Flash->success('El trabajador ha sido guardado.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('El trabajador no ha sido guardado, intentalo nuevamente.');
            }
        }
        $this->set(compact('worker'));
        $this->set('_serialize', ['worker']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Worker id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $worker = $this->Workers->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $worker = $this->Workers->patchEntity($worker, $this->request->data);
            if ($this->Workers->save($worker)) {
                $this->Flash->success('El trabajador ha sido actualizado.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('El trabajador no ha sido actualizado, intentalo nuevamente.');
            }
        }
        $this->set(compact('worker'));
        $this->set('_serialize', ['worker']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Worker id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $worker = $this->Workers->get($id);
        if ($this->Workers->delete($worker)) {
            $this->Flash->success('El trabajador ha sido eliminado.');
        } else {
            $this->Flash->error('El trabajador no ha sido eliminado, intentalo nuevamente.');
        }
        return $this->redirect(['action' => 'index']);
    }
}
