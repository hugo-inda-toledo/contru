<?php
namespace App\Model\Table;

use App\Model\Entity\BudgetItemsPaymentStatement;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * BudgetItemsPaymentStatements Model
 *
 * @property \Cake\ORM\Association\BelongsTo $PaymentStatements
 * @property \Cake\ORM\Association\BelongsTo $BudgetItems
 */
class BudgetItemsPaymentStatementsTable extends Table
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

        $this->table('budget_items_payment_statements');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('PaymentStatements', [
            'foreignKey' => 'payment_statement_id',
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
        $rules->add($rules->existsIn(['payment_statement_id'], 'PaymentStatements'));
        $rules->add($rules->existsIn(['budget_item_id'], 'BudgetItems'));
        return $rules;
    }
}
