<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RemunerationsReport Entity.
 *
 * @property int $id
 * @property int $status
 * @property int $user_id
 * @property \App\Model\Entity\User $user
 * @property int $month
 * @property int $day_cut
 * @property int $day_cut_prev
 * @property string $path
 * @property \Cake\I18n\Time $created
 */
class RemunerationsReport extends Entity
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
