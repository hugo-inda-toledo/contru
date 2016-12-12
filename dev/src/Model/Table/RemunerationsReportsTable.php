<?php
namespace App\Model\Table;

use App\Model\Entity\RemunerationsReport;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RemunerationsReports Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 */
class RemunerationsReportsTable extends Table
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

        $this->table('remunerations_reports');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

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
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->integer('month')
            ->requirePresence('month', 'create')
            ->notEmpty('month');

        $validator
            ->integer('day_cut')
            ->requirePresence('day_cut', 'create')
            ->notEmpty('day_cut');

        $validator
            ->integer('day_cut_prev')
            ->allowEmpty('day_cut_prev');

        $validator
            ->allowEmpty('path');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        return $rules;
    }


    var $status = [
        0=>'En Espera',
        1=>'En Proceso',
        2=>'Finalizada',
        3=>'ERROR'
    ];
}
