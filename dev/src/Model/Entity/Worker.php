<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Worker Entity.
 */
class Worker extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'softland_id' => true,
        'assists' => true,
        'bonuses' => true,
        'completed_tasks' => true,
        'deals' => true,
    ];
}
