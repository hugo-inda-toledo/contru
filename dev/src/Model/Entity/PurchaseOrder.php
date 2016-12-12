<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PurchaseOrder Entity.
 */
class PurchaseOrder extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'iconstruye_import_id' => true,
        'budget_item_id' => true,
        'oc_number' => true,
        'oc_date' => true,
        'cost_center' => true,
        'product_code' => true,
        'product_name' => true,
        'amount' => true,
        'unit_price' => true,
        'unit_type' => true,
        'product_total' => true,
        'sub_total' => true,
        'discount' => true,
        'tax' => true,
        'total_net' => true,
        'total_cost' => true,
        'total_cost_uf' => true,
        'iconstruye_import' => true,
        'budget_item' => true,
    ];
}
