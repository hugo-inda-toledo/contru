<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RenditionItem Entity.
 */
class RenditionItem extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'rendition_id' => true,
        'budget_item_id' => true,
        'product_name' => true,
        'product_total' => true,
        'unit_type' => true,
        'unit_price' => true,
        'quantity' => true,
        'rendition' => true,
        'budget_item' => true,
    ];
}
