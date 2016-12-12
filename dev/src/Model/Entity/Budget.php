<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Budget Entity.
 */
class Budget extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'building_id' => true,
        'duration' => true,
        'uf_value' => true,
        'total_cost_uf' => true,
        'comments' => true,
        'general_costs' => true,
        'utilities' => true,
        'retentions' => true,
        'advances' => true,
        'user_created_id' => true,
        'user_modified_id' => true,
        'building' => true,
        'user' => true,
        'user_modified' => true,
        'assists' => true,
        'bonuses' => true,
        'budget_approvals' => true,
        'budget_items' => true,
        'currencies_values' => true,
        'payment_statements' => true,
        'renditions' => true,
        'schedules' => true,
    ];
}
