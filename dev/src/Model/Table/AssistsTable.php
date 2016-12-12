<?php
namespace App\Model\Table;

use App\Model\Entity\Assist;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Assists Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Budgets
 * @property \Cake\ORM\Association\BelongsTo $Workers
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $UserModifieds
 * @property \Cake\ORM\Association\BelongsToMany $AssistTypes
 */
class AssistsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('assists');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Budgets', [
            'foreignKey' => 'budget_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Workers', [
            'foreignKey' => 'worker_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_created_id'
        ]);
        $this->belongsTo('UserModifieds', [
            'foreignKey' => 'user_modified_id'
        ]);
        $this->belongsToMany('AssistTypes', [
            'foreignKey' => 'assist_id',
            'targetForeignKey' => 'assist_type_id',
            'joinTable' => 'assists_assist_types'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');

        $validator
            ->add('overtime', 'valid', ['rule' => 'numeric'])
            ->requirePresence('overtime', 'create')
            ->notEmpty('overtime');

        $validator
            ->add('delay', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('delay');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['budget_id'], 'Budgets'));
        $rules->add($rules->existsIn(['worker_id'], 'Workers'));
        return $rules;
    }

    /**
     * Función que ordena array de asistencias por softland id del trabajador como llave
     * @param  array_object $assists información de asistencias por presupuesto y fecha
     * @return array ordenado de asistencias por softland_id del trabajador
     */
    public function assistsOrderByWorkerSoftlandId($assists = '')
    {
        if (count($assists) > 0) {
            $ordered_assists = array();
            foreach ($assists as $key => $assist) {
                $ordered_assists[$assist->worker->softland_id] = $assist->toArray();
            }
            return $ordered_assists;
        }
        return null;
    }

    /**
     * Genera una lista de los días del mes
     * @param  datetime $first_day_month primer día del mes
     * @return array                  días del mes
     * @author Diego De la Cruz <diego.delacruz@ideauno.cl>
     */
    public function getMonthDays($first_day_month = '')
    {
        $first_day_month = new \DateTime($first_day_month);
        $last_day_month = new \DateTime($first_day_month->format('Y-m-d'));
        $last_day_month = $last_day_month->modify('last day of this month');
        $month_days = array();
        while ($first_day_month <= $last_day_month) {
            array_push($month_days, $first_day_month->format('d'));
            $first_day_month->modify('+1 day');
        }
        return $month_days;
    }

    /**
     * Cálcula la información del mes de asistencia para los trabajadores
     * @param  int $budget_id                 identificador del presupuesto
     * @param  datetime $first_day_of_month   primer día del mes
     * @param  datetime $last_day_of_month    último día del mes
     * @param  array $workers                 trabajadores
     * @return array                          información de asistencia del mes
     * @author Diego De la Cruz <diego.delacruz@ideauno.cl>
     */
    public function getMonthAssistsData($budget_id = '', $first_day_of_month = '', $last_day_of_month = '', $workers = '')
    {
        $first_day_of_month_param = new \DateTime($first_day_of_month->format('Y-m-d'));
        $workers_assists_data = array();
        foreach ($workers as $worker_id => $worker) {
            if (!is_int($worker_id)) {
                break;
            }
            $first_day_of_month = new \DateTime($first_day_of_month_param->format('Y-m-d'));
            $last_day_of_month = new \DateTime($first_day_of_month->format('Y-m-d'));
            $last_day_of_month = $last_day_of_month->modify('last day of this month');
            $total_hours = 0;
            $total_overtime_hours = 0;
            $total_delay_hours = 0;
            $total_assists = 0;
            $total_permits = 0;
            $total_fails = 0;
            $total_license_achs = 0;
            $total_license_compin = 0;
            $total_layoffs = 0;
            $total_worker_movement = 0;
            $total_new_worker = 0;
            $total_deals_amount = 0;
            $total_bonuses_amount = 0;
            $deals_data = $this->Workers->Deals->find('all', [
                'contain' => ['DealDetails'], 'conditions' => ['Deals.budget_id' => $budget_id, 'Deals.worker_id' => $worker_id,'Deals.state' => 'Finalizado',
                 'Deals.start_date >=' => $first_day_of_month->format('Y-m-d 00:00:01'), 'Deals.start_date <=' => $last_day_of_month->format('Y-m-d 23:59:59')]]);
            $bonuses_data = $this->Workers->Bonuses->find('all', [
                'contain' => ['BonusDetails'], 'conditions' => ['Bonuses.budget_id' => $budget_id, 'Bonuses.worker_id' => $worker_id, 'Bonuses.state' => 'Finalizado',
                 'Bonuses.created >=' => $first_day_of_month->format('Y-m-d 00:00:01'), 'Bonuses.created <=' => $last_day_of_month->format('Y-m-d 23:59:59')]]);
            // debug($deals_data->toArray());
            // debug($bonuses_data->toArray()); die;
            $monthly_deals = array();
            $monthly_bonuses = array();
            if (!$deals_data->isEmpty()) {
                foreach ($deals_data->toArray() as $deal) {
                    $total_deals_amount += $deal['amount'];
                    $monthly_deals[$deal['worker_id']][$deal['start_date']->format('d')][$deal['id']] = $deal['amount'];
                }
            }
            $workers_assists_data[$worker_id]['deals'] = $total_deals_amount;
            if (!$bonuses_data->isEmpty()) {
                foreach ($bonuses_data->toArray() as $bonus) {
                    $total_bonuses_amount += $bonus['amount'];
                    $monthly_bonuses[$bonus['worker_id']][$bonus['created']->format('d')][$bonus['id']] = $bonus['amount'];
                }
            }
            $workers_assists_data[$worker_id]['bonuses'] = $total_bonuses_amount;
            while ($first_day_of_month <= $last_day_of_month) {
                $assist_data = $this->find('all', [
                    'contain' => ['AssistTypes'], 'conditions' => ['Assists.budget_id' => $budget_id, 'Assists.worker_id' => $worker_id,
                     'Assists.assistance_date >=' => $first_day_of_month->format('Y-m-d 00:00:01'), 'Assists.assistance_date <=' => $first_day_of_month->format('Y-m-d 23:59:59')],
                    ])->first();
                if (!empty($assist_data)) {
                    $total_overtime_hours += $assist_data['overtime'];
                    $total_delay_hours += $assist_data['delay'];
                    $assist_print = array();
                    foreach ($assist_data['assist_types'] as $assist_type) {
                        $total_hours += $assist_type->_joinData['hours'];
                        switch ($assist_type->_joinData['assist_type_id']) {
                            case 1:
                                $total_assists++;
                                $assist_print['value'] = 'A: ' . $assist_type->_joinData['hours'];
                                $assist_print['class'] = 'default';
                                break;
                            case 2:
                                $total_fails++;
                                $assist_print['value'] = 'F: ' . $assist_type->_joinData['hours'];
                                $assist_print['class'] = 'danger';
                                break;
                            case 3:
                                $total_permits++;
                                $assist_print['value'] = 'P: ' . $assist_type->_joinData['hours'];
                                $assist_print['class'] = 'success';
                                break;
                            case 4:
                                $total_license_compin++;
                                $assist_print['value'] = 'LA: ' . $assist_type->_joinData['hours'];
                                $assist_print['class'] = 'primary';
                                break;
                            case 5:
                                $total_license_achs++;
                                $assist_print['value'] = 'LC: ' . $assist_type->_joinData['hours'];
                                $assist_print['class'] = 'primary';
                                break;
                            case 6:
                                $total_layoffs++;
                                $assist_print['value'] = 'C: ' . $assist_type->_joinData['hours'];
                                $assist_print['class'] = 'info';
                                break;
                            case 7:
                                $total_worker_movement++;
                                $assist_print['value'] = 'MP: ' . $assist_type->_joinData['hours'];
                                $assist_print['class'] = 'warning';
                                break;
                            case 8:
                                $total_new_worker++;
                                $assist_print['value'] = 'NT: ' . $assist_type->_joinData['hours'];
                                $assist_print['class'] = 'warning';
                                break;
                        }
                        $workers_assists_data[$worker_id]['assists'][$first_day_of_month->format('d')] = $assist_print;
                        $workers_assists_data[$worker_id]['assists'][$first_day_of_month->format('d')]['overtime_hours'] = $assist_data['overtime'];
                        $workers_assists_data[$worker_id]['assists'][$first_day_of_month->format('d')]['delay_hours'] = $assist_data['delay'];
                    }
                } else {
                    if ($first_day_of_month->format('w') != 6 && $first_day_of_month->format('w') != 0) {
                        $workers_assists_data[$worker_id]['assists'][$first_day_of_month->format('d')]['status'] = 'S/I';
                    } else {
                        $workers_assists_data[$worker_id]['assists'][$first_day_of_month->format('d')]['status'] = 'NH';
                        $total_hours += 9;
                    }
                }
                $first_day_of_month->modify('+1 day');
            }
            // debug($workers_assists_data); die;
            $workers_assists_data[$worker_id]['assist_data']['total_hours'] = $total_hours;
            $workers_assists_data[$worker_id]['assist_data']['total_overtime_hours'] = $total_overtime_hours;
            $workers_assists_data[$worker_id]['assist_data']['total_delay_hours'] = $total_delay_hours;
            $workers_assists_data[$worker_id]['assist_data']['total_assists'] = $total_assists;
            $workers_assists_data[$worker_id]['assist_data']['total_permits'] = $total_permits;
            $workers_assists_data[$worker_id]['assist_data']['total_fails'] = $total_fails;
            $workers_assists_data[$worker_id]['assist_data']['total_license_achs'] = $total_license_achs;
            $workers_assists_data[$worker_id]['assist_data']['total_license_compin'] = $total_license_compin;
            $workers_assists_data[$worker_id]['assist_data']['total_layoffs'] = $total_layoffs;
            $workers_assists_data[$worker_id]['assist_data']['total_worker_movement'] = $total_worker_movement;
            $workers_assists_data[$worker_id]['assist_data']['total_new_worker'] = $total_new_worker;
            (!empty($monthly_deals[$worker_id])) ? $workers_assists_data[$worker_id]['monthly_deals'] = $monthly_deals[$worker_id] : '';
            (!empty($monthly_bonuses[$worker_id])) ? $workers_assists_data[$worker_id]['monthly_bonuses'] = $monthly_bonuses[$worker_id] : '';
        }
        return $workers_assists_data;
    }

    /**
     * Cálcula la información del mes de remuneraciones para los trabajadores
     * @param  int $budget_id                 identificador del presupuesto
     * @param  datetime $first_day_of_month   primer día del mes
     * @param  datetime $last_day_of_month    último día del mes
     * @param  array $workers_data            trabajadores e información de renta
     * @return array                          información de asistencia del mes
     * @author Diego De la Cruz <diego.delacruz@ideauno.cl>
     */
    public function getMonthSalariesdata($budget_id = '', $first_day_of_month = '', $last_day_of_month = '', $workers_data = '')
    {
        $assists_month_data = $this->getMonthAssistsData($budget_id, $first_day_of_month, $last_day_of_month, $workers_data);
        // debug($workers_data); //die;
        foreach ($workers_data as $worker_id => &$worker) {
            if (!is_int($worker_id)) {
                break;
            }
            if(!isset($worker['VariablesRenta']) || (isset($worker['VariablesRenta']) && empty($worker['VariablesRenta'])) ){
                continue;
            }
            $days_worked = 0;
            $total_calculated_hours = 0;
            $worker_assists = $assists_month_data[$worker_id];
            $month_overtime_hours = ($worker_assists['assist_data']['total_overtime_hours'] - $worker_assists['assist_data']['total_delay_hours']);
            $total_calculated_hours = $worker_assists['assist_data']['total_hours'] + $month_overtime_hours;
            if ($month_overtime_hours < 0) {
                $days_worked = ($worker_assists['assist_data']['total_hours'] + $month_overtime_hours) / 9; //9 horas jornada laboral
                $month_overtime_hours = 0;
            } else {
                $days_worked = $worker_assists['assist_data']['total_hours'] / 9; //9 horas jornada laboral
            }
            $base_salary = $worker['VariablesRenta']['H001']['valor'];
            $overtime_hours_value = round((($worker['VariablesRenta']['H001']['valor'] / 30) * 0.155555555555556) * 1.5, 0);
            $overtime_payout = round($month_overtime_hours * $overtime_hours_value, 0);
            $gratification = $worker['VariablesRenta']['H004']['valor'];
            $month_salary = round(($worker['VariablesRenta']['H001']['valor'] / 30) * $days_worked, 0);
            $other_taxables = (empty($worker['VariablesRenta']['H015'])) ? 0 : $worker['VariablesRenta']['H015']['valor']; //otros imponibles
            $year_end_bonus = (empty($worker['VariablesRenta']['H009'])) ? 0 : $worker['VariablesRenta']['H009']['valor']; //aguinaldo
            $total_taxable = $month_salary + $overtime_payout + $worker_assists['bonuses'] + $worker_assists['deals'] + $gratification + $year_end_bonus + $other_taxables; //aguinaldo
            $allocation_family = (empty($worker['VariablesRenta']['H016'])) ? 0 : $worker['VariablesRenta']['H016']['valor']; //ASIG. FAMILIAR
            $allocation_family_retro = (empty($worker['VariablesRenta']['H017'])) ? 0 : $worker['VariablesRenta']['H017']['valor']; //ASIG. FAM. RETROACTIVA
            $allocation_mobilization = (empty($worker['VariablesRenta']['P086'])) ? 0 : $worker['VariablesRenta']['P086']['valor']; //ASIG. MOVILIZACION
            $mobilization = (empty($worker['VariablesRenta']['H018'])) ? 0 : $worker['VariablesRenta']['H018']['valor']; //MOVILIZACION
            $allocation_lunch = (empty($worker['VariablesRenta']['P087'])) ? 0 : $worker['VariablesRenta']['P087']['valor']; //ASIG. COLACION
            $lunch = (empty($worker['VariablesRenta']['H019'])) ? 0 : $worker['VariablesRenta']['H019']['valor']; //COLACION
            $travel_expenses = (empty($worker['VariablesRenta']['H020'])) ? 0 : $worker['VariablesRenta']['H020']['valor']; //VIATICO
            $year_end_bonus_paid = (empty($worker['VariablesRenta']['D029'])) ? 0 : $worker['VariablesRenta']['D029']['valor']; //AGUINALDO PAGADO
            $advances = (empty($worker['VariablesRenta']['D026'])) ? 0 : $worker['VariablesRenta']['D026']['valor']; //ANTICIPOS
            $total_not_taxable = $allocation_family + $allocation_family_retro + $mobilization + $lunch + $travel_expenses; //TOTAL NO IMPONIBLE
            $total_assets = $total_taxable + $total_not_taxable; //TOTAL HABERES
            $afp_percent = '';
            $afp_name = '';
            $afp_discount = 0;
            $health_discount = 0;
            $isapre_name = ''; //nombre isapre
            $isapre_diff = 0;
            $isapre_discount = 0;
            foreach ($workers_data['Afps'] as $afp) {
                ($worker['AFP'] == $afp['CodAFP']) ? $afp_name = $afp['nombre'] : '';
            }
            if ($worker['AFP'] != '00') { //tiene afp D003
                $afp_percent = (empty($worker['VariablesRenta']['P080'])) ? 0 : $worker['VariablesRenta']['P080']['valor']; //%AFP
                $afp_discount = round($total_taxable * ($afp_percent / 100), 0);
            }
            if ($worker['Isapre'] == 'no') {
                $isapre_name = $workers_data['Isapres']['00'];
            } else {
                //calculos
                $isapre_name = $workers_data['Isapres'][$worker['Isapre']];
            }
            $health_discount = round($total_taxable * (7/100), 0);
            $unique_tax = $total_taxable - $afp_discount - $health_discount - $isapre_discount;
            $total_discounts = $afp_discount + $health_discount + $isapre_discount + $year_end_bonus_paid + $advances; // TODO: agregar otros valores de softland
            unset($workers_data[$worker_id]['Assists']['assist_data']);
            $workers_data[$worker_id]['Assists'] = $worker_assists;
            $workers_data[$worker_id]['Salary']['days_worked'] = $days_worked;
            $workers_data[$worker_id]['Salary']['total_calculated_hours'] = $total_calculated_hours;
            $workers_data[$worker_id]['Salary']['base_salary'] = $base_salary;
            $workers_data[$worker_id]['Salary']['month_salary'] = $month_salary;
            $workers_data[$worker_id]['Salary']['month_total_hours'] = $days_worked * 9;
            $workers_data[$worker_id]['Salary']['month_overtime_hours'] = $month_overtime_hours;
            $workers_data[$worker_id]['Salary']['overtime_hours_value'] = $overtime_hours_value;
            $workers_data[$worker_id]['Salary']['overtime_payout'] = $overtime_payout;
            $workers_data[$worker_id]['Salary']['gratification'] = $gratification;
            $workers_data[$worker_id]['Salary']['other_taxables'] = $other_taxables;
            $workers_data[$worker_id]['Salary']['year_end_bonus'] = $year_end_bonus;
            $workers_data[$worker_id]['Salary']['total_taxable'] = $total_taxable;
            $workers_data[$worker_id]['Salary']['allocation_family'] = $allocation_family;
            $workers_data[$worker_id]['Salary']['allocation_family_retro'] = $allocation_family_retro;
            $workers_data[$worker_id]['Salary']['allocation_mobilization'] = $allocation_mobilization;
            $workers_data[$worker_id]['Salary']['mobilization'] = $mobilization;
            $workers_data[$worker_id]['Salary']['allocation_lunch'] = $allocation_lunch;
            $workers_data[$worker_id]['Salary']['lunch'] = $lunch;
            $workers_data[$worker_id]['Salary']['travel_expenses'] = $travel_expenses;
            $workers_data[$worker_id]['Salary']['total_not_taxable'] = $total_not_taxable;
            $workers_data[$worker_id]['Salary']['total_assets'] = $total_assets;
            $workers_data[$worker_id]['Salary']['afp_name'] = $afp_name;
            $workers_data[$worker_id]['Salary']['afp_percent'] = $afp_percent;
            $workers_data[$worker_id]['Salary']['afp_discount'] = $afp_discount;
            $workers_data[$worker_id]['Salary']['health_discount'] = $health_discount;
            $workers_data[$worker_id]['Salary']['isapre_name'] = $isapre_name;
            $workers_data[$worker_id]['Salary']['isapre_diff'] = $isapre_diff;
            $workers_data[$worker_id]['Salary']['isapre_discount'] = $isapre_discount;
            $workers_data[$worker_id]['Salary']['year_end_bonus_paid'] = $year_end_bonus_paid;
            $workers_data[$worker_id]['Salary']['advances'] = $advances;
            $workers_data[$worker_id]['Salary']['unique_tax'] = $unique_tax;
            $workers_data[$worker_id]['Salary']['total_discounts'] = $total_discounts;
            $workers_data[$worker_id]['Salary']['liquid_to_pay'] = $total_assets - $total_discounts;
            // debug($workers_data); die;
        }
        unset($workers_data['CodAfpsPrev']);
        unset($workers_data['Isapres']);
        unset($workers_data['Afps']);
        return $workers_data;
    }

    /**
     * Obtiene la sumatoria total de las remuneraciones a la fecha
     */
    public function getTotalSalariesToDate($budget_id = '', $current_date = '', $months = '')
    {
        // budget
        $budget = $this->Budgets->get($budget_id);
        // meses
        $months = $this->Budgets->getListMonthsBudget($budget->created, $budget->duration);
        // workers
        $workers_data = $this->Workers->getSoftlandWorkersAndRentaInfoByBuildingWithWorkerId($budget->building_id);
        $total_salaries = 0;
        foreach ($months as $key_month => $month) {
            $date = explode('_', $key_month);
            $first_day_of_month = new \DateTime($date[0] . '-' . $date[1] . '-01');
            $last_day_of_month = new \DateTime($first_day_of_month->format('Y-m-d'));
            $last_day_of_month->modify('last day of this month');
            // remuneraciones
            $salaries_month_data = $this->getMonthSalariesdata($budget->id, $first_day_of_month, $last_day_of_month, $workers_data);
            foreach ($salaries_month_data as $worker_id => $worker_salary_data) {
                if (!is_int($worker_id)) {
                    break;
                }
                $total_salaries += $worker_salary_data['Salary']['total_assets'];
            }
            if ($key_month == $current_date->format('Y_m')) {
                break;
            }
        }
        return $total_salaries;
    }





    public function getMonthSalariesdatatest($budget_id = '', $first_day_of_month = '', $last_day_of_month = '', $workers_data = '')
    {
        $assists_month_data = $this->getMonthAssistsData($budget_id, $first_day_of_month, $last_day_of_month, $workers_data);
        // debug($workers_data); //die;
        foreach ($workers_data as $worker_id => &$worker) {
            if (!is_int($worker_id)) {
                break;
            }
            if(!isset($worker['VariablesRenta']) || (isset($worker['VariablesRenta']) && empty($worker['VariablesRenta'])) ){
                continue;
            }
            $days_worked = 0;
            $total_calculated_hours = 0;
            $worker_assists = $assists_month_data[$worker_id];
            $month_overtime_hours = ($worker_assists['assist_data']['total_overtime_hours'] - $worker_assists['assist_data']['total_delay_hours']);
            $total_calculated_hours = $worker_assists['assist_data']['total_hours'] + $month_overtime_hours;
            if ($month_overtime_hours < 0) {
                $days_worked = ($worker_assists['assist_data']['total_hours'] + $month_overtime_hours) / 9; //9 horas jornada laboral
                $month_overtime_hours = 0;
            } else {
                $days_worked = $worker_assists['assist_data']['total_hours'] / 9; //9 horas jornada laboral
            }
            $base_salary = $worker['VariablesRenta']['H001']['valor'];
            $overtime_hours_value = round((($worker['VariablesRenta']['H001']['valor'] / 30) * 0.155555555555556) * 1.5, 0);
            $overtime_payout = round($month_overtime_hours * $overtime_hours_value, 0);
            $gratification = $worker['VariablesRenta']['H004']['valor'];
            $month_salary = round(($worker['VariablesRenta']['H001']['valor'] / 30) * $days_worked, 0);
            $other_taxables = (empty($worker['VariablesRenta']['H015'])) ? 0 : $worker['VariablesRenta']['H015']['valor']; //otros imponibles
            $year_end_bonus = (empty($worker['VariablesRenta']['H009'])) ? 0 : $worker['VariablesRenta']['H009']['valor']; //aguinaldo
            $total_taxable = $month_salary + $overtime_payout + $worker_assists['bonuses'] + $worker_assists['deals'] + $gratification + $year_end_bonus + $other_taxables; //aguinaldo
            $allocation_family = (empty($worker['VariablesRenta']['H016'])) ? 0 : $worker['VariablesRenta']['H016']['valor']; //ASIG. FAMILIAR
            $allocation_family_retro = (empty($worker['VariablesRenta']['H017'])) ? 0 : $worker['VariablesRenta']['H017']['valor']; //ASIG. FAM. RETROACTIVA
            $allocation_mobilization = (empty($worker['VariablesRenta']['P086'])) ? 0 : $worker['VariablesRenta']['P086']['valor']; //ASIG. MOVILIZACION
            $mobilization = (empty($worker['VariablesRenta']['H018'])) ? 0 : $worker['VariablesRenta']['H018']['valor']; //MOVILIZACION
            $allocation_lunch = (empty($worker['VariablesRenta']['P087'])) ? 0 : $worker['VariablesRenta']['P087']['valor']; //ASIG. COLACION
            $lunch = (empty($worker['VariablesRenta']['H019'])) ? 0 : $worker['VariablesRenta']['H019']['valor']; //COLACION
            $travel_expenses = (empty($worker['VariablesRenta']['H020'])) ? 0 : $worker['VariablesRenta']['H020']['valor']; //VIATICO
            $year_end_bonus_paid = (empty($worker['VariablesRenta']['D029'])) ? 0 : $worker['VariablesRenta']['D029']['valor']; //AGUINALDO PAGADO
            $advances = (empty($worker['VariablesRenta']['D026'])) ? 0 : $worker['VariablesRenta']['D026']['valor']; //ANTICIPOS
            $total_not_taxable = $allocation_family + $allocation_family_retro + $mobilization + $lunch + $travel_expenses; //TOTAL NO IMPONIBLE
            $total_assets = $total_taxable + $total_not_taxable; //TOTAL HABERES
            $afp_percent = '';
            $afp_name = '';
            $afp_discount = 0;
            $health_discount = 0;
            $isapre_name = ''; //nombre isapre
            $isapre_diff = 0;
            $isapre_discount = 0;
            foreach ($workers_data['Afps'] as $afp) {
                ($worker['AFP'] == $afp['CodAFP']) ? $afp_name = $afp['nombre'] : '';
            }
            if ($worker['AFP'] != '00') { //tiene afp D003
                $afp_percent = (empty($worker['VariablesRenta']['P080'])) ? 0 : $worker['VariablesRenta']['P080']['valor']; //%AFP
                $afp_discount = round($total_taxable * ($afp_percent / 100), 0);
            }
            if ($worker['Isapre'] == 'no') {
                $isapre_name = $workers_data['Isapres']['00'];
            } else {
                //calculos
                $isapre_name = $workers_data['Isapres'][$worker['Isapre']];
            }
            $health_discount = round($total_taxable * (7/100), 0);
            $unique_tax = $total_taxable - $afp_discount - $health_discount - $isapre_discount;
            $total_discounts = $afp_discount + $health_discount + $isapre_discount + $year_end_bonus_paid + $advances; // TODO: agregar otros valores de softland
            unset($workers_data[$worker_id]['Assists']['assist_data']);
            $workers_data[$worker_id]['Assists'] = $worker_assists;
            $workers_data[$worker_id]['Salary']['days_worked'] = $days_worked;
            $workers_data[$worker_id]['Salary']['total_calculated_hours'] = $total_calculated_hours;
            $workers_data[$worker_id]['Salary']['base_salary'] = $base_salary;
            $workers_data[$worker_id]['Salary']['month_salary'] = $month_salary;
            $workers_data[$worker_id]['Salary']['month_total_hours'] = $days_worked * 9;
            $workers_data[$worker_id]['Salary']['month_overtime_hours'] = $month_overtime_hours;
            $workers_data[$worker_id]['Salary']['overtime_hours_value'] = $overtime_hours_value;
            $workers_data[$worker_id]['Salary']['overtime_payout'] = $overtime_payout;
            $workers_data[$worker_id]['Salary']['gratification'] = $gratification;
            $workers_data[$worker_id]['Salary']['other_taxables'] = $other_taxables;
            $workers_data[$worker_id]['Salary']['year_end_bonus'] = $year_end_bonus;
            $workers_data[$worker_id]['Salary']['total_taxable'] = $total_taxable;
            $workers_data[$worker_id]['Salary']['allocation_family'] = $allocation_family;
            $workers_data[$worker_id]['Salary']['allocation_family_retro'] = $allocation_family_retro;
            $workers_data[$worker_id]['Salary']['allocation_mobilization'] = $allocation_mobilization;
            $workers_data[$worker_id]['Salary']['mobilization'] = $mobilization;
            $workers_data[$worker_id]['Salary']['allocation_lunch'] = $allocation_lunch;
            $workers_data[$worker_id]['Salary']['lunch'] = $lunch;
            $workers_data[$worker_id]['Salary']['travel_expenses'] = $travel_expenses;
            $workers_data[$worker_id]['Salary']['total_not_taxable'] = $total_not_taxable;
            $workers_data[$worker_id]['Salary']['total_assets'] = $total_assets;
            $workers_data[$worker_id]['Salary']['afp_name'] = $afp_name;
            $workers_data[$worker_id]['Salary']['afp_percent'] = $afp_percent;
            $workers_data[$worker_id]['Salary']['afp_discount'] = $afp_discount;
            $workers_data[$worker_id]['Salary']['health_discount'] = $health_discount;
            $workers_data[$worker_id]['Salary']['isapre_name'] = $isapre_name;
            $workers_data[$worker_id]['Salary']['isapre_diff'] = $isapre_diff;
            $workers_data[$worker_id]['Salary']['isapre_discount'] = $isapre_discount;
            $workers_data[$worker_id]['Salary']['year_end_bonus_paid'] = $year_end_bonus_paid;
            $workers_data[$worker_id]['Salary']['advances'] = $advances;
            $workers_data[$worker_id]['Salary']['unique_tax'] = $unique_tax;
            $workers_data[$worker_id]['Salary']['total_discounts'] = $total_discounts;
            $workers_data[$worker_id]['Salary']['liquid_to_pay'] = $total_assets - $total_discounts;
            // debug($workers_data); die;
        }
        unset($workers_data['CodAfpsPrev']);
        unset($workers_data['Isapres']);
        unset($workers_data['Afps']);
        return $workers_data;
    }


}
