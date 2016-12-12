<?php
namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;

/**
 * Charges Controller
 *
 * @property \App\Model\Table\SalaryReportsTable $SalaryReports */
class SalaryReportsController extends AppController
{
    /**
    * beforeFilter
    */
    // public function beforeFilter(Event $event)
    // {
    //     parent::beforeFilter($event);
    //     //validar que el presupuesto no esté finalizado o sin aprobar
    //     $current_action = $this->request->params['action'];
    //     $action_views = ['index'];
    //     if (in_array($current_action, $action_views)) {
    //         $current_state = null;
    //         if (count($this->request->params['pass']) > 0) {
    //             $current_state = $this->SalaryReports->Budgets->current_budget_state($this->request->params['pass'][0]);
    //         } elseif (count($this->request->query) > 0) {
    //             $budget = $this->SalaryReports->Budgets->find('all', ['conditions' => ['Budgets.building_id' => $this->request->query['building_id']]])->first();
    //             $current_state = $this->SalaryReports->Budgets->current_budget_state($budget->id);
    //         }
    //         if (empty($current_state) && $current_state == null) {
    //             $this->Flash->info('El presupuesto de la obra no está configurado, no puede agregar información adicional.');
    //             return $this->redirect(['controller' => 'assists', 'action' => 'index']);
    //         } else {
    //             if ($current_state == -1) {
    //                 $this->Flash->info('La obra está bloqueada, no puede agregar información adicional.');
    //                 return $this->redirect(['controller' => 'assists', 'action' => 'index']);
    //             } else {
    //                 if ($current_state < 4 || $current_state == 6) {
    //                     $this->Flash->info('El presupuesto de la obra se encuentra en estados Pendiente Aprobación o Finalizado, no puede agregar información adicional.');
    //                     return $this->redirect(['controller' => 'assists', 'action' => 'index']);
    //                 }
    //             }
    //         }
    //     }
    // }


    /**
     * Index method
     *
     * @return void
     */
    public function index()
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
            $user_buildings = $this->SalaryReports->Budgets->Users->getUserBuildings($this->request->session()->read('Auth.User.id'));
            if (count($user_buildings) > 0) {
                $budget = $this->SalaryReports->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates'],
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
            $buildings = $this->SalaryReports->Budgets->Buildings->getActiveBuildingsWithSoftlandInfo();
               $last_building = $this->request->session()->read('Config.last_building');
            if (!empty($this->request->query)) {
                if (!empty($this->request->query['building_id'])) {
                    $budget = $this->SalaryReports->Budgets->find('all', [
                        'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates'],
                        'conditions' => ['Budgets.building_id' => $this->request->query['building_id']]
                    ])->first();
                }
            } else {
                 if(!empty($last_building)) {
                    $budget = $this->SalaryReports->Budgets->find('all', [
                    'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates'],
                    'conditions' => ['Budgets.building_id' => $last_building]
                ])->first();    
                } else {
                    $budget = $this->SalaryReports->Budgets->find('all', [
                        'contain' => ['Buildings' => ['BuildingsUsers' => ['Users']], 'CurrenciesValues' => ['Currencies'], 'Users', 'BudgetApprovals', 'BudgetApprovals.BudgetStates'],
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
        $workers = $this->SalaryReports->Workers->getSoftlandWorkersByBuildingWithWorkerId($budget->building_id);
        //genera lista de meses de asistencia de ppto.
        $budget_date_created = new \DateTime($budget->created->format('d-m-Y'));
        $budget_date_created_months = new \DateTime($budget->created->format('d-m-Y'));
        //si el primer día del mes es menor a la fecha de creación del ppto, usar esa fecha
        ($first_day_of_month < $budget_date_created) ? $first_day_of_month = $budget_date_created : '';
        // $month_days = $this->SalaryReports->Budgets->Assists->getMonthDays($assistance_date->format('Y-m-d'));
        // $assist_month_data = $this->SalaryReports->getMonthAssistsData($budget->id, $first_day_of_month, $last_day_of_month, $workers);
        $salary_reports = $this->SalaryReports->find('all', [
            'conditions' => ['SalaryReports.budget_id' => $budget->id, 'SalaryReports.assistance_date' => $assistance_date]
        ]);
        $months = $this->SalaryReports->Budgets->getListMonthsBudget($budget_date_created_months, $budget->duration);
        $this->set(compact('budget', 'assist_month_data', 'buildings', 'months', 'sf_building', 'workers', 'assistance_date', 'salary_reports'));
    }



}
