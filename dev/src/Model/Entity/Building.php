<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Building Entity.
 */
class Building extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'client' => true,
        'address' => true,
        'softland_id' => true,
        'created' => true,
        'omit' => true,
        'active' => true,
        'modified' => true
    ];
}
