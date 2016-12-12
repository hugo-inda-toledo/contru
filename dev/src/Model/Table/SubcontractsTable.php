<?php
namespace App\Model\Table;

use App\Model\Entity\Subcontract;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Subcontracts Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Buildings
 */
class SubcontractsTable extends Table
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

        $this->table('subcontracts');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');
        $this->belongsTo('IconstruyeImports', [
            'foreignKey' => 'iconstruye_import_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('BudgetItems', [
            'foreignKey' => 'budget_item_id',
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
            ->requirePresence('rut', 'create')
            ->notEmpty('rut');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->requirePresence('description', 'create')
            ->notEmpty('description');

        $validator
            ->add('amount', 'valid', ['rule' => 'numeric'])
            ->requirePresence('amount', 'create')
            ->notEmpty('amount');

        $validator
            ->requirePresence('unit_type', 'create')
            ->notEmpty('unit_type');

        $validator
            ->add('price', 'valid', ['rule' => 'numeric'])
            ->requirePresence('price', 'create')
            ->notEmpty('price');

        $validator
            ->add('total', 'valid', ['rule' => 'numeric'])
            ->requirePresence('total', 'create')
            ->notEmpty('total');

        $validator
            ->add('partial_amount', 'valid', ['rule' => 'numeric'])
            ->requirePresence('partial_amount', 'create')
            ->notEmpty('partial_amount');

        $validator
            ->add('partial_total', 'valid', ['rule' => 'numeric'])
            ->requirePresence('partial_total', 'create')
            ->notEmpty('partial_total');

        $validator
            ->add('balance_due', 'valid', ['rule' => 'numeric'])
            ->requirePresence('balance_due', 'create')
            ->notEmpty('balance_due');

        $validator
            ->add('payment_statement_total', 'valid', ['rule' => 'numeric'])
            ->requirePresence('payment_statement_total', 'create')
            ->notEmpty('payment_statement_total');

        $validator
            ->add('date', 'valid', ['rule' => 'datetime'])
            ->requirePresence('date', 'create')
            ->notEmpty('date');

        $validator
            ->requirePresence('json', 'create')
            ->notEmpty('json');

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
        $rules->add($rules->existsIn(['building_id'], 'Buildings'));
        return $rules;
    }
}
