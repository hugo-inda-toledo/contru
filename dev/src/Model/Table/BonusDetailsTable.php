<?php
namespace App\Model\Table;

use App\Model\Entity\DealDetail;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DealDetails Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Deals * @property \Cake\ORM\Association\BelongsTo $BudgetItems * @property \Cake\ORM\Association\BelongsTo $UserCreateds * @property \Cake\ORM\Association\BelongsTo $UserModifieds */
class BonusDetailsTable extends Table
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

        $this->table('bonus_details');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Bonuses', [
            'foreignKey' => 'bonus_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('BudgetItems', [
            'foreignKey' => 'budget_item_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_created_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_modified_id',
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
            ->add('id', 'valid', ['rule' => 'numeric'])            ->allowEmpty('id', 'create');
        $validator
            ->add('percentage', 'valid', ['rule' => 'numeric'])            ->requirePresence('percentage', 'create')            ->notEmpty('percentage');
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
        $rules->add($rules->existsIn(['bonus_id'], 'Bonuses'));
        $rules->add($rules->existsIn(['budget_items_id'], 'BudgetItems'));
        $rules->add($rules->existsIn(['user_created_id'], 'Users'));
        $rules->add($rules->existsIn(['user_modified_id'], 'Users'));
        return $rules;
    }
}
