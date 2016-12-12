<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SalaryReport Entity.
 *
 * @property int $id
 * @property int $budget_id
 * @property \App\Model\Entity\Budget $budget
 * @property int $worker_id
 * @property \App\Model\Entity\Worker $worker
 * @property \Cake\I18n\Time $assistance_date
 * @property int $total_taxable
 * @property int $total_not_taxable
 * @property int $total_assets
 * @property int $travel_expenses
 * @property int $other_discounts
 * @property int $total_discounts
 * @property int $liquid_to_pay
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 */
class SalaryReport extends Entity
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
