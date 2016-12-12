<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Permission Entity.
 */
class Permission extends Entity
{

	protected $_accessible = [
        'permission_name' => true,
        'permission_description' => true,
        'controller' => true,
        'action' => true
    ];
}
