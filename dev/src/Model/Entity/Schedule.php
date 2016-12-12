<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Schedule Entity.
 */
class Schedule extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'budget_id' => true,
        'name' => true,
        'description' => true,
        'comment' => true,
        'approval_btn' => true,
        'holidays_week_quantity' => true,
        'total_days' => true,
        'start_date' => true,
        'finish_date' => true,
        'user_created_id' => true,
        'user_modified_id' => true,
        'budget' => true,
        'user_created' => true,
        'user_modified' => true,
        'completed_tasks' => true,
        'progress' => true,
    ];
}
