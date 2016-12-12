<?php
namespace App\Model\Table;

use App\Model\Entity\Progres;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Progress Model
 */
class ProgressTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('progress');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('BudgetItems', [
            'foreignKey' => 'budget_item_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Schedules', [
            'foreignKey' => 'schedule_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('PaymentStatements', [
            'foreignKey' => 'payment_statement_id'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_created_id'
        ]);
        $this->belongsTo('UserModifieds', [
            'className' => 'Users',
            'foreignKey' => 'user_modified_id'
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
            ->add('installed_items_quantity', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('installed_items_quantity');
            
        $validator
            ->add('user_modified', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('user_modified');

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
        $rules->add($rules->existsIn(['budget_item_id'], 'BudgetItems'));
        $rules->add($rules->existsIn(['schedule_id'], 'Schedules'));
        $rules->add($rules->existsIn(['payment_statement_id'], 'PaymentStatements'));
        $rules->add($rules->existsIn(['user_created_id'], 'Users'));
        return $rules;
    }

    
    /**
    *  Actualiza todos los progress setiando approved = true, de manera de aprobar los progress
    *  de una planificación.
    *
    *  @param $schedule_id: id de Planificación que esta siendo aprobada
    *  @return true/false
    *  
    **/
    
    public function approvedProgressInSchedules($schedule_id){        
        return $this->updateAll(['approved'=> true,'modified' => new \DateTime()],['schedule_id'=> $schedule_id]);
    }


    /**
    *  Actualiza todos los progress setiando approved = false (igual cero), de manera de rechazar los progress
    *  de una planificación.
    *
    *  @param $schedule_id: id de Planificación que esta siendo rechazada
    *  @return true/false
    *  
    **/
    
    public function rejectedProgressInSchedules($schedule_id){
        return $this->updateAll(['approved'=> false, 'modified' => new \DateTime()], ['schedule_id'=> $schedule_id]);
    }


    /**
    *  Actualiza todos los progress aprobados de un listado de Planificaciónes
    *  Setiando payment_statement_id     
    *
    *  @param $payment_id: id de Estado de Pago.
    *  @param $schedule_ids: ids de Planificación de Planificaciónes de un ppto.
    *  @return true/false
    *  
    **/
    
    public function setPayment($payment_id, $schedule_ids){
        return $this->updateAll(
                ['payment_statement_id' => $payment_id, 'modified' => new \DateTime()], // set
                ['payment_statement_id IS' => null, 'schedule_id IN' => $schedule_ids] // conditions
            );
    }



}
