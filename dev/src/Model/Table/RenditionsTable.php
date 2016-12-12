<?php
namespace App\Model\Table;

use App\Model\Entity\Rendition;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Renditions Model
 */
class RenditionsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('renditions');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Budgets', [
            'foreignKey' => 'budget_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_created_id'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_modified_id'
        ]);
        $this->hasMany('RenditionItems', [
            'foreignKey' => 'rendition_id'
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
            ->add('total_items', 'valid', ['rule' => 'numeric'])
            ->requirePresence('total_items', 'create')
            ->notEmpty('total_items');
            
        $validator
            ->add('total', 'valid', ['rule' => 'numeric'])
            ->requirePresence('total', 'create')
            ->notEmpty('total');

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
        $rules->add($rules->existsIn(['user_created_id'], 'Users'));
        $rules->add($rules->existsIn(['user_modified_id'], 'Users'));
        return $rules;
    }
}
