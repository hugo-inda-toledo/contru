<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CompletedTask Entity.
 */
class CompletedTask extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'schedule_id' => true,
        'budget_item_id' => true,
        'worker_id' => true,
        'hours_worked' => true,
        'completed_percent' => true,
        'installed_items_quantity' => true,
        'comment' => true,
        'schedule' => true,
        'budget_item' => true,
        'worker' => true,
    ];
}
