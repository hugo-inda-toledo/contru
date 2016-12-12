<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Bonus Entity.
 */
class Bonus extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'budget_id' => true,
        'bonus_details_id' => true,
        'worker_id' => true,
        'state' => true,
        'description' => true,
        'amount' => true,
        'user_created_id' => true,
        'user_modified_id' => true,
        'budget' => true,
        'worker' => true,
        'bonus_details' => true,
        'bonus' => true,
        'user_modified' => true,
    ];
}
