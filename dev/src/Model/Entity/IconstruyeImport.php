<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * IconstruyeImport Entity.
 */
class IconstruyeImport extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'file_id' => true,
        'file_name' => true,
        'transaction_lines' => true,
        'user_uploader_id' => true,
        'file' => true,
        'user_uploader' => true,
        'guide_entries' => true,
        'guide_exits' => true,
        'invoices' => true,
        'purchase_orders' => true,
    ];
}
