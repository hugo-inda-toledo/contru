<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Subcontract Entity.
 *
 * @property int $id
 * @property int $building_id
 * @property \App\Model\Entity\Building $building
 * @property string $rut
 * @property string $name
 * @property string $description
 * @property float $amount
 * @property string $unit_type
 * @property float $price
 * @property float $total
 * @property float $partial_amount
 * @property float $partial_total
 * @property float $balance_due
 * @property float $payment_statement_total
 * @property \Cake\I18n\Time $date
 * @property string $json
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 */
class Subcontract extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
