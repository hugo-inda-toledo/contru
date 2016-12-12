<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GuideExit Entity.
 */
class GuideExit extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'iconstruye_import_id' => true,
        'budget_item_id' => true,
        'uid' => true,
        'voucher' => true,
        'date_system' => true,
        'product_code' => true,
        'product_name' => true,
        'amount' => true,
        'unit_price' => true,
        'unit_type' => true,
        'product_total' => true,
        'json' => true,
        'observations' => true,
    ];
}
