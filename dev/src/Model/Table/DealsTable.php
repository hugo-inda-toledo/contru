<?php
namespace App\Model\Table;

use App\Model\Entity\Deal;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Deals Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Budgets * @property \Cake\ORM\Association\BelongsTo $Workers * @property \Cake\ORM\Association\BelongsTo $Users * @property \Cake\ORM\Association\BelongsTo $UserModifieds * @property \Cake\ORM\Association\HasMany $DealDetails */
class DealsTable extends Table
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

        $this->table('deals');
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
        $this->hasMany('DealDetails', [
            'foreignKey' => 'deal_id'
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
            ->add('state', 'rule_states', ['rule' => ['inList', ['Pendiente Aprobación Admin Obra', 'Pendiente Aprobación Visitador', 'Pendiente Aprobación Jefe RRHH' , 'Aprobado', 'Rechazado', 'Finalizado'], false]]);
        $validator
            ->requirePresence('description', 'create')            ->notEmpty('description');
        $validator
            ->add('amount', 'valid', ['rule' => 'numeric'])            ->requirePresence('amount', 'create')            ->notEmpty('amount');
        $validator
            ->add('start_date', 'valid', ['rule' => 'datetime'])            ->requirePresence('start_date', 'create')            ->notEmpty('start_date');
        $validator
            ->add('end_date', 'valid', ['rule' => 'datetime']);
        $validator
            ->allowEmpty('comment');
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
        $rules->add($rules->existsIn(['user_created_id'], 'Users'));
        $rules->add($rules->existsIn(['user_modified_id'], 'Users'));
        return $rules;
    }

    public function getStates() {
        return array(0 => 'Pendiente Aprobación Admin Obra', 1 => 'Pendiente Aprobación Visitador', 2 => 'Pendiente Aprobación Jefe RRHH', 3 => 'Aprobado', 4 => 'Rechazado', 5 => 'Finalizado');
    }

    /**
     * Obtiene el total de todos los tratos por partida
     * @param  int $budget_id identificador presupuesto
     * @return array totales por partida
     * @author Diego De la Cruz <diego.delacruz@ideauno.cl>
     */
    public function getTotalDealsOrderByBudgetItem($budget_id = '')
    {
        $states = $this->getStates();
        $valid_states = array($states[3] => $states[3], $states[5] => $states[5]);
        $deals = $this->find('all', ['conditions' => ['Deals.budget_id' => $budget_id, 'Deals.state IN' => $valid_states], 'contain' => ['DealDetails']])->toArray();
        $budget_items_deals_totals = array();
        foreach ($deals as $deal) {
            if (count($deal['deal_details']) > 0) {
                foreach ($deal['deal_details'] as $detail) {
                    (empty($budget_items_deals_totals[$detail['budget_item_id']])) ? $budget_items_deals_totals[$detail['budget_item_id']] = 0 : '';
                    $budget_items_deals_totals[$detail['budget_item_id']] += ($detail['percentage'] / 100) * $deal['amount'];
                }
            }
        }
        return ($budget_items_deals_totals);
    }
}
