<?php
namespace App\Model\Table;

use App\Model\Entity\SalaryReport;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SalaryReports Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Budgets
 * @property \Cake\ORM\Association\BelongsTo $Workers
 */
class SalaryReportsTable extends Table
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

        $this->table('salary_reports');
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
            ->add('assistance_date', 'valid', ['rule' => 'datetime'])
            ->requirePresence('assistance_date', 'create')
            ->notEmpty('assistance_date');

        $validator
            ->add('total_taxable', 'valid', ['rule' => 'numeric'])
            ->requirePresence('total_taxable', 'create')
            ->notEmpty('total_taxable');

        $validator
            ->add('total_not_taxable', 'valid', ['rule' => 'numeric'])
            ->requirePresence('total_not_taxable', 'create')
            ->notEmpty('total_not_taxable');

        $validator
            ->add('total_assets', 'valid', ['rule' => 'numeric'])
            ->requirePresence('total_assets', 'create')
            ->notEmpty('total_assets');

        $validator
            ->add('travel_expenses', 'valid', ['rule' => 'numeric'])
            ->requirePresence('travel_expenses', 'create')
            ->notEmpty('travel_expenses');

        $validator
            ->add('other_discounts', 'valid', ['rule' => 'numeric'])
            ->requirePresence('other_discounts', 'create')
            ->notEmpty('other_discounts');

        $validator
            ->add('total_discounts', 'valid', ['rule' => 'numeric'])
            ->requirePresence('total_discounts', 'create')
            ->notEmpty('total_discounts');

        $validator
            ->add('liquid_to_pay', 'valid', ['rule' => 'numeric'])
            ->requirePresence('liquid_to_pay', 'create')
            ->notEmpty('liquid_to_pay');

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
}
