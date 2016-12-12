<?php
namespace App\Model\Table;

use App\Model\Entity\BudgetItemsSchedule;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * BudgetItemsSchedules Model
 *
 * @property \Cake\ORM\Association\BelongsTo $BudgetItems * @property \Cake\ORM\Association\BelongsTo $Schedules */
class BudgetItemsSchedulesTable extends Table
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

        $this->table('budget_items_schedules');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->belongsTo('BudgetItems', [
            'foreignKey' => 'budget_item_id'
        ]);
        $this->belongsTo('Schedules', [
            'foreignKey' => 'schedule_id'
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
            ->add('id', 'valid', ['rule' => 'numeric'])            ->allowEmpty('id', 'create');
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
        $rules->add($rules->existsIn(['budget_item_id'], 'BudgetItems'));
        $rules->add($rules->existsIn(['schedule_id'], 'Schedules'));
        return $rules;
    }
}
