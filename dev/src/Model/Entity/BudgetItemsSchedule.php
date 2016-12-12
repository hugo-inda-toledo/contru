<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * BudgetItemsSchedule Entity.
 *
 * @property int $id * @property int $budget_item_id * @property \App\Model\Entity\BudgetItem $budget_item * @property int $schedule_id * @property \App\Model\Entity\Schedule $schedule */
class BudgetItemsSchedule extends Entity
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
