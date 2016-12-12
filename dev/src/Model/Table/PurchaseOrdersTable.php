<?php
namespace App\Model\Table;

use App\Model\Entity\PurchaseOrder;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PurchaseOrders Model
 */
class PurchaseOrdersTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('purchase_orders');
        $this->displayField('id');
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
            ->requirePresence('oc_number', 'create')
            ->notEmpty('oc_number');
            
        $validator
            ->add('oc_date', 'valid', ['rule' => 'datetime'])
            ->requirePresence('oc_date', 'create')
            ->notEmpty('oc_date');
            
        $validator
            ->requirePresence('cost_center', 'create')
            ->notEmpty('cost_center');
            
        $validator
            ->requirePresence('product_code', 'create')
            ->notEmpty('product_code');
            
        $validator
            ->requirePresence('product_name', 'create')
            ->notEmpty('product_name');
            
        $validator
            ->add('amount', 'valid', ['rule' => 'numeric'])
            ->requirePresence('amount', 'create')
            ->notEmpty('amount');
            
        $validator
            ->add('unit_price', 'valid', ['rule' => 'numeric'])
            ->requirePresence('unit_price', 'create')
            ->notEmpty('unit_price');
            
        $validator
            ->requirePresence('unit_type', 'create')
            ->notEmpty('unit_type');
            
        $validator
            ->add('product_total', 'valid', ['rule' => 'numeric'])
            ->requirePresence('product_total', 'create')
            ->notEmpty('product_total');
            
        $validator
            ->add('sub_total', 'valid', ['rule' => 'numeric'])
            ->requirePresence('sub_total', 'create')
            ->notEmpty('sub_total');
            
        $validator
            ->add('discount', 'valid', ['rule' => 'numeric'])
            ->requirePresence('discount', 'create')
            ->notEmpty('discount');
            
        $validator
            ->add('tax', 'valid', ['rule' => 'numeric'])
            ->requirePresence('tax', 'create')
            ->notEmpty('tax');
            
        $validator
            ->add('total_net', 'valid', ['rule' => 'numeric'])
            ->requirePresence('total_net', 'create')
            ->notEmpty('total_net');
            
        $validator
            ->add('total_cost', 'valid', ['rule' => 'numeric'])
            ->requirePresence('total_cost', 'create')
            ->notEmpty('total_cost');
            
        $validator
            ->add('total_cost_uf', 'valid', ['rule' => 'numeric'])
            ->requirePresence('total_cost_uf', 'create')
            ->notEmpty('total_cost_uf');

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
        $rules->add($rules->existsIn(['iconstruye_import_id'], 'IconstruyeImports'));
        $rules->add($rules->existsIn(['budget_item_id'], 'BudgetItems'));
        return $rules;
    }
}
