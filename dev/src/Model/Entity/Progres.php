<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Progres Entity.
 */
class Progres extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'budget_item_id' => true,
        'schedule_id' => true,
        'payment_statement_id' => true,
        'overall_progress_percent' => true,
        'proyected_progress_percent' => true,
        'worked_items_quantity' => true,
        'user_created_id' => true,
        'user_modified' => true,
        'budget_item' => true,
        'schedule' => true,
        'payment_statement' => true,
        'user' => true,
        'approved' => true
    ];
}
