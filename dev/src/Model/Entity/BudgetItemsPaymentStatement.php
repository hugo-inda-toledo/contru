<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * BudgetItemsPaymentStatement Entity.
 *
 * @property int $id
 * @property int $payment_statement_id
 * @property \App\Model\Entity\PaymentStatement $payment_statement
 * @property int $budget_item_id
 * @property \App\Model\Entity\BudgetItem $budget_item
 * @property float $progress
 * @property float $value_progress
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 */
class BudgetItemsPaymentStatement extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
