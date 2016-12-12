<?php
namespace App\Model\Table;

use App\Model\Entity\BudgetItem;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

use Cake\ORM\TableRegistry;

/**
 * BudgetItems Model
 */
class BudgetItemsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('budget_items');
        $this->displayField('name');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->addBehavior('Tree');
        $this->belongsTo('Budgets', [
            'foreignKey' => 'budget_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('ParentBudgetItems', [
            'className' => 'BudgetItems',
            'foreignKey' => 'parent_id'
        ]);
        $this->belongsTo('Units', [
            'foreignKey' => 'unit_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('ChildBudgetItems', [
            'className' => 'BudgetItems',
            'foreignKey' => 'parent_id'
        ]);
        $this->hasMany('CompletedTasks', [
            'foreignKey' => 'budget_item_id'
        ]);
        $this->hasMany('BonusDetails', [
            'foreignKey' => 'budget_item_id'
        ]);
        $this->hasMany('DealDetails', [
            'foreignKey' => 'budget_item_id'
        ]);
        $this->hasMany('GuideEntries', [
            'foreignKey' => 'budget_item_id'
        ]);
        $this->hasMany('GuideExits', [
            'foreignKey' => 'budget_item_id'
        ]);
        $this->hasMany('Subcontracts', [
            'foreignKey' => 'budget_item_id'
        ]);
        $this->hasMany('Invoices', [
            'foreignKey' => 'budget_item_id'
        ]);
        $this->hasMany('PurchaseOrders', [
            'foreignKey' => 'budget_item_id'
        ]);
        $this->hasMany('Progress', [
            'foreignKey' => 'budget_item_id'
        ]);
        $this->hasMany('BudgetItemsSchedules', [
            'foreignKey' => 'budget_item_id',
            'dependent' => true
        ]);
        $this->belongsToMany('Schedules', [
            'through' => 'BudgetItemsSchedules',
        ]);
        $this->hasMany('BudgetItemsPaymentStatements', [
            'className' => 'BudgetItemsPaymentStatements',
            'foreignKey' => 'budget_item_id'
        ]);

        $this->hasMany('Materials', [
            'className' => 'IcMaterial',
            'foreignKey' => 'IDPARTIDA'
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
            ->add('lft', 'valid', ['rule' => 'numeric'])
            //->requirePresence('lft', 'create')
            ->allowEmpty('lft');

        $validator
            ->add('rght', 'valid', ['rule' => 'numeric'])
            //->requirePresence('rght', 'create')
            ->allowEmpty('rght');

        $validator
            ->requirePresence('item', 'create')
            ->notEmpty('item');

        $validator
            ->requirePresence('description', 'create')
            ->notEmpty('description');

        $validator
            ->add('quantity', 'valid', ['rule' => 'numeric'])
            //->requirePresence('quantity', 'create')
            ->allowEmpty('quantity');

        $validator
            ->add('unity_price', 'valid', ['rule' => 'numeric'])
            //->requirePresence('unity_price', 'create')
            ->allowEmpty('unity_price');

        $validator
            ->add('total_price', 'valid', ['rule' => 'numeric'])
            //->requirePresence('total_price', 'create')
            ->allowEmpty('total_price');

        $validator
            ->add('total_uf', 'valid', ['rule' => 'numeric'])
            //->requirePresence('total_uf', 'create')
            ->allowEmpty('total_uf');

        $validator
            ->allowEmpty('comments');

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
        //$rules->add($rules->existsIn(['parent_id'], 'ParentBudgetItems'));
        //$rules->add($rules->existsIn(['unit_id'], 'Units'));
        return $rules;
    }

    /**
     * Calculo de la sumatoria de un item, si tiene hijos retorna la sumatoria de los hijos, si no solo el valor del item.
     *
     * @param $id del item
     * @return sumatoria total de sub items.
     */

    public function calc_total($id = null)
    {
        $sum_p = 0;
        $ChildQ = $this->find('children', ['for' => $id]);
        $childs = $ChildQ->all()->toArray();
        if(empty($childs)) {
            $bi = $this->get($id);
            $sum_p = $bi->total_price;
        }
        else {
            foreach($childs as $t) { $sum_p = $sum_p + $t->total_price; }
        }
        return $sum_p;
    }

    /**
     * Funcion para borrar items.
     *
     * @param $id del item
     * @param $type tipo de item, 0 todos, 1 originales, 2 extras.
     * @return sumatoria total de sub items.
     */

    public function remove_all($id = null, $type = 0)
    {
        switch ($type) {
            case 0:
                $bi = $this->find('all',['conditions' => ['budget_id' => $id,'parent_id IS' => null]]);
                break;
            case 1:
                $bi = $this->find('all',['conditions' => ['budget_id' => $id,'parent_id IS' => null,'extra' => 0]]);
                break;
            case 2:
                $bi = $this->find('all',['conditions' => ['budget_id' => $id,'parent_id IS' => null,'extra' => 1]]);
                break;
        }
        foreach($bi as $item) {
            debug($item);
            //$this->delete($bi);
        }
    }

    /**
     * Obtiene el progreso actual de la partida
     * @param  int $budget_item_id identificador de partida
     * @return float porcentaje de progreso
     * @author Diego De la Cruz <diego.delacruz@ideauno.cl>
     */
    public function getCurrentProgressValue($budget_id = '', $historial = 0)
    {
        $schedules = $this->BudgetItemsSchedules->Schedules->find('list', ['conditions' => ['Schedules.budget_id' => $budget_id]]);

        if( count($schedules->toArray()) == 0 ){
            return array();
        }

        if($historial === 1) {
            //con historial por caledarización
            $completed_tasks = $this->Progress->find('list', [
                'conditions' => ['Progress.schedule_id IN' => array_keys($schedules->toArray())],
                'order' => 'Progress.created DESC',
                'keyField' => 'schedule_id',
                'valueField' => 'overall_progress_percent',
                'groupField' => 'budget_item_id'
                ]);
        } else {
            //sin historial
            $completed_tasks = $this->Progress->find('list', [
                'conditions' => ['Progress.schedule_id IN' => array_keys($schedules->toArray())],
                'order' => 'Progress.created DESC',
                'keyField' => 'budget_item_id',
                'valueField' => 'overall_progress_percent',
                // 'groupField' => 'budget_item_id' //activar para historial
                ]);
        }

        //debug($completed_tasks->toArray());
        return ($completed_tasks->toArray()); die();
    }

    /**
     * Obtiene el valor total de horas trabajadas por partida para todo el presupuesto
     * @param  int $budget_id identificador del presupuesto
     * @return int valor total de trabajo realizado
     * @author Diego De la Cruz <diego.delacruz@ideauno.cl>
     */
    public function getTotalCompletedTasksValue($budget_id='')
    {
        // TODO: SACAR VALOR POR CARGO | SUELDO PROMEDIO
        $schedules = $this->BudgetItemsSchedules->Schedules->find('list', ['conditions' => ['Schedules.budget_id' => $budget_id]]);
        $completed_tasks = $this->CompletedTasks->find('list', [
            'conditions' => ['CompletedTasks.schedule_id IN' => array_keys($schedules->toArray())],
            'keyField' => 'schedule_id',
            'valueField' => 'worker_id',
            'groupField' => 'worker_id'
            ]);
        $completed_tasks_hours = array();
        $budget_items_completed_tasks = array();
        foreach ($completed_tasks as $worker_id => $schedules) {
            foreach ($schedules as $schedule_id => $worker) {
                $completed_tasks_hours = $this->CompletedTasks->Workers->getTaskHoursByWorkerIdOrderByBudgetItem($worker, $schedule_id);
                // debug($completed_tasks_hours);
                foreach ($completed_tasks_hours as $budget_item_id => $hours) {
                    (empty($budget_items_completed_tasks[$budget_item_id])) ? $budget_items_completed_tasks[$budget_item_id] = 0 : '';
                    $budget_items_completed_tasks[$budget_item_id] += $hours;
                }
            }
        }
        //debug($budget_items_completed_tasks); die();
        return ($budget_items_completed_tasks);
    }

    /**
     * [borra_asistencia description]
     * @param  [type] $obra_id [description]
     * @return [type]          [description]
     * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
     */
    public function borra_asistencia($obra_id) {

        // carga el modelo de la asistencia
        $this->Assists = TableRegistry::get('Assists');

        // arreglo con la lista de las asistencias d ela obra
        $asists = $this->Assists->find('list', [
            'conditions' => ['budget_id' => $obra_id],
            'keyField' => 'id',
            'valueField' => 'id'
            ])->toArray();

        // carga modelo del tipo de la asistenca
        $this->AssistsAssistTypes = TableRegistry::get('AssistsAssistTypes');

        // elimina el estado de la asistencia
        if (! empty($asists)) $this->AssistsAssistTypes->deleteAll(['assist_id IN' => $asists]);

        // elimina las asistencias
        $this->Assists->deleteAll(['budget_id IN' => $obra_id]);
    }


    /**
     * [borra_estadospago description]
     * @param  [type] $obra_id [description]
     * @return [type]          [description]
     * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
     */
    public function borra_estadospago($obra_id) {

        // lista de partidas de la obra
        $bi = $this->find('list', [
            'conditions' => ['budget_id' => $obra_id],
            'keyField' => 'id',
            'valueField' => 'id'
            ])->toArray();

        // carga el modelo que tiene el estado de pago de cada item
        $this->BudgetItemsPaymentStatements = TableRegistry::get('BudgetItemsPaymentStatements');

        // elimina el estado de la asistencia
        if (! empty($bi)) $this->BudgetItemsPaymentStatements->deleteAll(['budget_item_id IN' => $bi]);

        // carga los estados de pago
        $this->PaymentStatements = TableRegistry::get('PaymentStatements');

        // lista de estados de pago de la obra
        $edp = $this->PaymentStatements->find('list', [
            'conditions' => ['budget_id' => $obra_id],
            'keyField' => 'id',
            'valueField' => 'id'
            ])->toArray();

        // carga el modelo las versiones del estado de pago
        $this->PaymentStatementApprovals = TableRegistry::get('PaymentStatementApprovals');

        // borra las versiones del estado de pago de la obra
        if (! empty($edp)) $this->PaymentStatementApprovals->deleteAll(['payment_statement_id IN' => $edp]);

        // borra los estados de pago de la obra
        $this->PaymentStatements->deleteAll(['budget_id' => $obra_id]);
    }

    /**
     * [borra_bonos description]
     * @param  [type] $obra_id [description]
     * @return [type]          [description]
     * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
     */
    public function borra_bonos($obra_id) {

        // lista de partidas de la obra
        $bi = $this->find('list', [
            'conditions' => ['budget_id' => $obra_id],
            'keyField' => 'id',
            'valueField' => 'id'
            ])->toArray();

        // carga el modelo los detalles de los bonos
        $this->BonusDetails = TableRegistry::get('BonusDetails');

        // borra el detalle de los bonos
        if (! empty($bi)) $this->BonusDetails->deleteAll(['budget_item_id IN' => $bi]);

        // carga el modelo los bonos
        $this->Bonuses = TableRegistry::get('Bonuses');

        // borra los bonos de la obra
        $this->Bonuses->deleteAll(['budget_id' => $obra_id]);
    }

    /**
     * [borra_planificaciones description]
     * @param  [type] $obra_id [description]
     * @return [type]          [description]
     * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
     */
    public function borra_planificaciones($obra_id) {

        // carga modelo de las planificaciones
        $this->Schedules = TableRegistry::get('Schedules');

        // lista de planificaciones de la obra
        $planif = $this->Schedules->find('list', [
            'conditions' => ['budget_id' => $obra_id],
            'keyField' => 'id',
            'valueField' => 'id'
            ])->toArray();

        // carga el modelo de los progresos
        $this->Progress = TableRegistry::get('Progress');

        // borra los progresos
        if (! empty($planif)) $this->Progress->deleteAll(['schedule_id IN' => $planif]);

        // carga el modelo de los trabajos realizados
        $this->CompletedTasks = TableRegistry::get('CompletedTasks');

        // borra los trabajos realizados
        if (! empty($planif)) $this->CompletedTasks->deleteAll(['schedule_id IN' => $planif]);

        // borra las planificaciones
        $this->Schedules->deleteAll(['budget_id' => $obra_id]);
    }

    /**
     * Actualiza los campos percentage_progress de los padres del item enviado
     * se obtendra la cantidad de dinero en base al porcentaje especificado del item
     * luego se deberán sumar los items y según eso se calcula con el costo total del padre a guardar y se obtiene el porcentaje
     * @param  Object $bi         Item a buscar
     * @param  int $percentage_update porcentaje a actualizar
     * @param  string $field Campo a actualizar (que paja hacer 2 funciones xd)
     * @return bool              True
     * @author Gabriel Rebolledo <gabriel.rebolledo@ideauno.cl>
     **/
    public function updateParentsPercentageProgress($pbi, $percentage_update, $field='percentage_proyected_progress'){
        $parentBudgetItem = $this->find('all', [
            'conditions' => [
                'BudgetItems.id' => $pbi->parent_id
            ]
        ])->first();
        if($parentBudgetItem!=null){
            $childrensTree = $this->BudgetItemsSchedules->BudgetItems->find('children', ['for' => $parentBudgetItem->id])
                ->find('threaded')
                ->toArray();
            $total_percentage=0;
            if($childrensTree!=null){
                foreach($childrensTree AS $a){
                    $total_percentage += ($a->{$field}*$a->total_price)/100;
                }
            }
            if($parentBudgetItem->total_price!=null){
                $percentage_parent = ($total_percentage*100)/$parentBudgetItem->total_price;
                $parentBudgetItem->{$field} = $percentage_parent;
                $this->save($parentBudgetItem);
                if($parentBudgetItem->parent_id!=null){
                    self::updateParentsPercentageProgress($parentBudgetItem, $percentage_update, $field);
                }
            }
        }
        return true;
    }

    public function injectGlobalQuantification($budget_item = array(), $commited_materials = null, $spent_materials = null, $invoiced_materials = null, $commited_subcontracts = null, $spent_subcontracts = null, $invoiced_subcontracts = null)
    {
        if($budget_item != null)
        {
            //Global
            $budget_item->commited = $commited_materials + $commited_subcontracts;
            $budget_item->spent = $spent_materials + $spent_subcontracts;
            $budget_item->invoiced = $invoiced_materials + $invoiced_subcontracts;

            //Materials
            $budget_item->commited_materials = $commited_materials;
            $budget_item->spent_materials = $spent_materials;
            $budget_item->invoiced_materials = $invoiced_materials;

            //Subcontracts
            $budget_item->commited_subcontracts = $commited_subcontracts;
            $budget_item->spent_subcontracts = $spent_subcontracts;
            $budget_item->invoiced_subcontracts = $invoiced_subcontracts;

            $this->save($budget_item);     
        }
    }

    public function injectQuantificationDetails($budget_item = array())
    {
        $commited = 0;
        $spent = 0;
        $invoiced = 0;

        $commited_materials = 0;
        $spent_materials = 0;
        $invoiced_materials = 0;

        $commited_subcontracts = 0;
        $spent_subcontracts = 0;
        $invoiced_subcontracts = 0;

        foreach($budget_item->child_budget_items as $bi)
        {
            //Global
            $commited += $bi->commited;
            $spent += $bi->spent;
            $invoiced += $bi->invoiced;

            //Materials
            $commited_materials += $bi->commited_materials;
            $spent_materials += $bi->spent_materials;
            $invoiced_materials += $bi->invoiced_materials;

            //Subcontracts
            $commited_subcontracts += $bi->commited_subcontracts;
            $spent_subcontracts += $bi->spent_subcontracts;
            $invoiced_subcontracts += $bi->invoiced_subcontracts;
        }

        $budget_item->commited = $commited;
        $budget_item->spent = $spent;
        $budget_item->invoiced = $invoiced;

        $budget_item->commited_materials = $commited_materials;
        $budget_item->spent_materials = $spent_materials;
        $budget_item->invoiced_materials = $invoiced_materials;

        $budget_item->commited_subcontracts = $commited_subcontracts;
        $budget_item->spent_subcontracts = $spent_subcontracts;
        $budget_item->invoiced_subcontracts = $invoiced_subcontracts;

        $this->save($budget_item);
    }
}
