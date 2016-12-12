<?php
namespace App\Model\Table;

use App\Model\Entity\PaymentStatementApproval;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Network\Session;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * PaymentStatementApprovals Model
 *
 * @property \Cake\ORM\Association\BelongsTo $PaymentStatements * @property \Cake\ORM\Association\BelongsTo $PaymentStatementStates * @property \Cake\ORM\Association\BelongsTo $Users */
class PaymentStatementApprovalsTable extends Table
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

        $this->table('payment_statement_approvals');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('PaymentStatements', [
            'foreignKey' => 'payment_statement_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('PaymentStatementStates', [
            'foreignKey' => 'payment_statement_state_id',
            'joinType' => 'INNER'
        ]);
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
            ->add('id', 'valid', ['rule' => 'numeric'])            ->allowEmpty('id', 'create');
        $validator
            ->requirePresence('description', 'create')            ->notEmpty('description');
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
        $rules->add($rules->existsIn(['payment_statement_id'], 'PaymentStatements'));
        $rules->add($rules->existsIn(['payment_statement_state_id'], 'PaymentStatementStates'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        return $rules;
    }



    public function addState($payment_statement_id,$payment_statement_state_id,$description){

                $session = new Session();

                //crear payment_statement_approval para guardar la historia de los estados que va tomando
                $payment_app = $this->newEntity();
                $payment_app->user_id = $session->read('Auth.User.id');
                $payment_app->payment_statement_id = $payment_statement_id;
                $payment_app->payment_statement_state_id = $payment_statement_state_id;
                $payment_app->description = $description;
                return $this->save($payment_app);
    }




}
