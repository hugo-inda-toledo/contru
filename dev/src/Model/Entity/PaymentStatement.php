<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PaymentStatement Entity.
 */
class PaymentStatement extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'budget_id' => true,
        'total_cost' => true,
        'overall_progress' => true,
        'contract_value' => true,
        'paid_to_date' => true,
        'progress_present_payment_statement' => true,
        'balance_due' => true,
        'discount_retentions' => true,
        'discount_advances' => true,
        'liquid_pay' => true,
        'currency_value_to_date' => true,
        'total_net' => true,
        'tax' => true,
        'total' => true,
        'user_created_id' => true,
        'user_modified_id' => true,
        'budget' => true,
        'user' => true,
        'user_modified' => true,
        'payment_statement_state_id' => true,
        'progress' => true,
        'draft' => true,
        'first_approval' => true,
        'second_approval' => true,
        'third_approval' => true,
        'email_sent' => true,
        'client_approval' => true,
        'decline_obs' => true,
        'total_direct_cost' => true,
        'total_direct_cost_to_date' => true,
        'total_direct_cost_last' => true,
        'total_direct_cost_present' => true,
        'total_percent_to_date' => true,
        'total_percent_last' => true,
        'total_percent_present' => true
    ];
}