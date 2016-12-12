<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AssistsData Entity.
 *
 * @property int $id
 * @property int $building_id
 * @property \App\Model\Entity\Building $building
 * @property int $softland_id
 * @property \App\Model\Entity\Softland $softland
 * @property string $nombres
 * @property string $appaterno
 * @property string $apmaterno
 * @property string $rut
 * @property string $email
 * @property string $direccion
 * @property string $telefono1
 * @property \Cake\I18n\Time $fecha_nacimiento
 * @property \Cake\I18n\Time $fecha_ingreso
 * @property string $cargo
 * @property \Cake\I18n\Time $vig_desde
 * @property \Cake\I18n\Time $vig_hasta
 */
class AssistsData extends Entity
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
