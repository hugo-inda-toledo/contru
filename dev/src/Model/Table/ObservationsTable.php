<?php
namespace App\Model\Table;

use App\Model\Entity\Observation;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Observations Model
 */
class ObservationsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('observations');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        
        /*$this->belongsTo('Models', [
            'foreignKey' => 'model_id',
            'joinType' => 'INNER'
        ]);*/
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
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
            ->requirePresence('model', 'create')
            ->notEmpty('model');
            
        $validator
            ->requirePresence('action', 'create')
            ->notEmpty('action');
            
        $validator
            ->requirePresence('observation', 'create')
            ->notEmpty('observation');

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
        //$rules->add($rules->existsIn(['model_id'], 'Models'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        return $rules;
    }
}
