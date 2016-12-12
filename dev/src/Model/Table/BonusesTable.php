<?php
namespace App\Model\Table;

use App\Model\Entity\Bonus;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Bonuses Model
 */
class BonusesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('bonuses');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Budgets', [
            'foreignKey' => 'budget_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Workers', [
            'foreignKey' => 'worker_id',
            'joinType' => 'INNER'
        ]);
         $this->belongsTo('Users', [
            'foreignKey' => 'user_created_id'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_modified_id'
        ]);
        $this->hasMany('BonusDetails', [
            'foreignKey' => 'bonus_id'
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
            ->add('amount', 'valid', ['rule' => 'numeric'])
            ->requirePresence('amount', 'create')
            ->notEmpty('amount');
        $validator
            ->requirePresence('description', 'create')            ->notEmpty('description');
        $validator
            ->add('state', 'rule_states', ['rule' => ['inList', ['Pendiente Aprobación Admin Obra', 'Pendiente Aprobación Visitador', 'Pendiente Aprobación Jefe RRHH' , 'Aprobado', 'Rechazado', 'Finalizado'], false]]);
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
        $rules->add($rules->existsIn(['worker_id'], 'Workers'));
        // $rules->add($rules->existsIn(['user_created_id'], 'Bonuses'));
        $rules->add($rules->existsIn(['user_created_id'], 'Users'));
        $rules->add($rules->existsIn(['user_modified_id'], 'Users'));
        return $rules;
    }

    public function getStates() {
        return array(0 => 'Pendiente Aprobación Admin Obra', 1 => 'Pendiente Aprobación Visitador', 2 => 'Pendiente Aprobación Jefe RRHH', 3 => 'Aprobado', 4 => 'Rechazado', 5 => 'Finalizado');
    }

    /**
     * Obtiene el total de todos los bonos por partida
     * @param  int $budget_id identificador presupuesto
     * @return array totales por partida
     * @author Diego De la Cruz <diego.delacruz@ideauno.cl>
     */
    public function getTotalBonusesOrderByBudgetItem($budget_id = '')
    {
        $states = $this->getStates();
        $valid_states = array($states[3] => $states[3], $states[5] => $states[5]);
        $bonuses = $this->find('all', ['conditions' => ['Bonuses.budget_id' => $budget_id, 'Bonuses.state IN' => $valid_states], 'contain' => ['BonusDetails']])->toArray();
        $budget_items_bonuses_totals = array();
        foreach ($bonuses as $bonus) {
            if (count($bonus['bonus_details']) > 0) {
                foreach ($bonus['bonus_details'] as $detail) {
                    (empty($budget_items_bonuses_totals[$detail['budget_item_id']])) ? $budget_items_bonuses_totals[$detail['budget_item_id']] = 0 : '';
                    $budget_items_bonuses_totals[$detail['budget_item_id']] += ($detail['percentage'] / 100) * $bonus['amount'];
                }
            }
        }
        return ($budget_items_bonuses_totals);
    }
}
