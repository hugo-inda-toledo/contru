<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CurrenciesValue Entity.
 */
class CurrenciesValue extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'budget_id' => true,
        'currency_id' => true,
        'value' => true,
        'budget' => true,
        'currency' => true,
    ];
}
