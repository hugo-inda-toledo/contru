<?php
namespace App\Model\Table;

use App\Model\Entity\Schedule;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Schedules Model
 */
class SchedulesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('schedules');
        $this->displayField('name');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Budgets', [
            'foreignKey' => 'budget_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('UserCreateds', [
            'className' => 'Users',
            'foreignKey' => 'user_created_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('UserModifieds', [
            'className' => 'Users',
            'foreignKey' => 'user_modified_id'
        ]);
        $this->hasMany('CompletedTasks', [
            'foreignKey' => 'schedule_id'
        ]);
        $this->hasMany('Progress', [
            'foreignKey' => 'schedule_id',
            'dependent' => true
        ]);
        $this->hasMany('BudgetItemsSchedules', [
            'foreignKey' => 'schedule_id',
            'dependent' => true
        ]);
        $this->belongsToMany('BudgetItems', [
            'through' => 'BudgetItemsSchedules',
        ]);
        $this->hasMany('Approvals', [
            'foreignKey' => false
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
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        // $validator
        //     ->requirePresence('description', 'create')
        //     ->notEmpty('description');

        // $validator
        //     ->add('total_days', 'valid', ['rule' => 'numeric'])
        //     ->requirePresence('total_days', 'create')
        //     ->notEmpty('total_days');

        $validator
            ->add('start_date', 'valid', ['rule' => 'datetime'])
            ->requirePresence('start_date', 'create')
            ->notEmpty('start_date');

        // $validator
        //     ->add('finish_date', 'valid', ['rule' => 'datetime'])
        //     ->requirePresence('finish_date', 'create')
        //     ->notEmpty('finish_date');

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
        $rules->add($rules->existsIn(['user_created_id'], 'UserCreateds'));
        $rules->add($rules->existsIn(['user_modified_id'], 'UserModifieds'));
        return $rules;
    }

    /**
     * Método que comprueba si la semana está ocupada por otra planificación
     * @param  string $start_date fecha de inicio del usuario
     * @return bool true o false si existen resultados
     */
    public function weekEmpty($budget_id = '', $start_date = '', $id = null)
    {
        $week_avaliability = false;
        if (empty($budget_id) && $budget_id == null || empty($start_date) && $start_date == null) {
            return $week_avaliability;
        } else {
            $user_date = strtotime( date('Y-m-d H:m:s', strtotime($start_date)));
            $week_start = date('Y-m-d 00:00:00', strtotime('monday this week', $user_date));
            $week_end = date('Y-m-d 23:59:59', strtotime('friday this week', $user_date));
            if (!is_null($id)) {
               $query = $this->find()
                    ->where(['Schedules.budget_id' => $budget_id, 'Schedules.start_date >=' => $week_start, 'Schedules.finish_date <=' => $week_end, 'Schedules.id !=' => $id]);
            } else {
                $query = $this->find()
                    ->where(['Schedules.budget_id' => $budget_id, 'Schedules.start_date >=' => $week_start, 'Schedules.finish_date <=' => $week_end]);
            }
            $schedules = $query->count();
            ($schedules > 0) ? $week_avaliability = false : $week_avaliability = true;
            return $week_avaliability;
        }
    }

    /**
     * Verifica que un avance tenga avances
     * @param  int $schedule_id identificador de planificación
     * @return array              estado del avance
     */
    public function progress_advance_state($schedule_id = '')
    {
        $schedule = $this->get($schedule_id, ['contain' => ['Progress']]);
        $advance_state = false;
        foreach ($schedule->progress as $progress) {
            $advance_state = ($progress->created != $progress->modified && $progress->overall_progress_percent > 0) ? true : false;
            if ($advance_state) {
                break;
            }
        }
        return $advance_state;
    }
}
