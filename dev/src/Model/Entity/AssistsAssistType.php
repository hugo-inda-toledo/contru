<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AssistsAssistType Entity.
 *
 * @property int $id
 * @property int $assist_id
 * @property \App\Model\Entity\Assist $assist
 * @property int $assist_type_id
 * @property \App\Model\Entity\AssistType $assist_type
 * @property int $hours
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 */
class AssistsAssistType extends Entity
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
