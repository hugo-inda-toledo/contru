<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Charge Entity.
 *
 * @property int $id * @property int $softland_id * @property \App\Model\Entity\Softland $softland * @property string $name * @property int $amount * @property \Cake\I18n\Time $created * @property \Cake\I18n\Time $modified */
class Charge extends Entity
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
