<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Observation Entity.
 */
class Observation extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'model' => true,
        'action' => true,
        'model_id' => true,
        'user_id' => true,
        'observation' => true,
        'user' => true,
    ];
}
