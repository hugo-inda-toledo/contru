<?php
namespace App\Model\Table;

use App\Model\Entity\RenditionItem;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RenditionItems Model
 */
class RenditionItemsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('rendition_items');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Renditions', [
            'foreignKey' => 'rendition_id',
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
            ->requirePresence('product_name', 'create')
            ->notEmpty('product_name');
            
        $validator
            ->add('product_total', 'valid', ['rule' => 'numeric'])
            ->requirePresence('product_total', 'create')
            ->notEmpty('product_total');
            
        $validator
            ->add('unit_type', 'valid', ['rule' => 'numeric'])
            ->requirePresence('unit_type', 'create')
            ->notEmpty('unit_type');
            
        $validator
            ->add('unit_price', 'valid', ['rule' => 'numeric'])
            ->requirePresence('unit_price', 'create')
            ->notEmpty('unit_price');
            
        $validator
            ->add('quantity', 'valid', ['rule' => 'numeric'])
            ->requirePresence('quantity', 'create')
            ->notEmpty('quantity');

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
        $rules->add($rules->existsIn(['rendition_id'], 'Renditions'));
        $rules->add($rules->existsIn(['budget_item_id'], 'BudgetItems'));
        return $rules;
    }
}
