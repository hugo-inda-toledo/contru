<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Approval Entity.
 */
class Approval extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'model' => true,
        'action' => true,
        'approve' => true,
        'reject' => true,
        'user_id' => true,
        'comment' => true,
        'user' => true,
        'model_id' => true,
        'group_id' => true
    ];
}
