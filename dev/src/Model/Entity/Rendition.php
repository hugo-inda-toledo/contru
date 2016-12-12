<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Rendition Entity.
 */
class Rendition extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'budget_id' => true,
        'total_items' => true,
        'total' => true,
        'user_created_id' => true,
        'user_modified_id' => true,
        'budget' => true,
        'user' => true,
        'rendition_items' => true,
    ];
}
