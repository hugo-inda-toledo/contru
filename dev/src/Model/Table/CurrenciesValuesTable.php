<?php
namespace App\Model\Table;

use App\Model\Entity\CurrenciesValue;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CurrenciesValues Model
 */
class CurrenciesValuesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('currencies_values');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Budgets', [
            'foreignKey' => 'budget_id'
        ]);
        $this->belongsTo('Currencies', [
            'foreignKey' => 'currency_id'
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
            
        $extra = 'Some additional value needed inside the closure';
        $validator
            ->add('value', [
                'nonZero' => [
                    'rule' => function ($value, $context) use ($extra) {
                        // Custom logic that returns true/false
                        return ($value == 0) ? false : true;
                    },
                    'message' => 'value cannot be zero'
                ],
                'numeric' => [
                    'rule' => 'numeric',
                    'message' => 'value must be numeric'
                ]
            ])->requirePresence('value');

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
        $rules->add($rules->existsIn(['currency_id'], 'Currencies'));
        return $rules;
    }
}
