<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * History Entity.
 */
class History extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'user_id' => true,
        'group_id' => true,
        'model' => true,
        'method' => true,
        'text' => true,
        'data' => true,
        'user' => true,
    ];
}
