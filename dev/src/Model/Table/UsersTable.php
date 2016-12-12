<?php
namespace App\Model\Table;

use App\Model\Entity\User;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 */
class UsersTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        
        $this->table('users');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Groups', [
            'foreignKey' => 'group_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Approvals', [
            'foreignKey' => 'user_id'
        ]);

        $this->hasMany('BudgetApprovals', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Observations', [
            'foreignKey' => 'user_id'
        ]);        
        $this->hasMany('BuildingsUsers', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasOne('Currency');

        $this->belongsToMany('Groups', [
            'through' => 'UsersGroups'
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
            ->add('email', 'valid', ['rule' => 'email'])
            ->requirePresence('email', 'create')
            ->notEmpty('email');
            
        $validator
            ->requirePresence('password', 'create')
            ->notEmpty('password');
            
        $validator
            ->requirePresence('first_name', 'create')
            ->notEmpty('first_name');
            
        $validator
            ->requirePresence('lastname_f', 'create')
            ->notEmpty('lastname_f');
            
        $validator
            ->allowEmpty('lastname_m');
            
        $validator
            ->allowEmpty('celphone');
            
        $validator
            ->allowEmpty('address');

        $validator
            ->allowEmpty('temp_pass');
            
        $validator
            ->add('active', 'valid', ['rule' => 'numeric'])
            ->requirePresence('active', 'create')
            ->notEmpty('active');

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
        $rules->add($rules->isUnique(['email']));
        $rules->add($rules->existsIn(['group_id'], 'Groups'));
        // $rules->add($rules->existsIn(['user_creator_id'], 'Users'));
        // $rules->add($rules->existsIn(['user_modifier_id'], 'Users'));
        // $rules->add($rules->existsIn(['group_id'], 'Users'));
        return $rules;
    }

    /**
     * Obtiene las obras asociadas al usuario
     * @param  int $user_id identificador del usuario
     * @return array          lista de obras: id's
     * @author Diego De la Cruz <diego.delacruz@ideauno.cl>
     */
    public function getUserBuildings($user_id = '')
    {
        if (!empty($user_id) && $user_id != null) {
            $buildings_user = $this->BuildingsUsers->find('all',['conditions' => ['BuildingsUsers.user_id' => $user_id]]);
            $buildings_id = array();
            foreach ($buildings_user as $building_user) {
                array_push($buildings_id, $building_user['building_id']);
            }
            return $buildings_id;
        } else {
            return null;
        }
    }
}
