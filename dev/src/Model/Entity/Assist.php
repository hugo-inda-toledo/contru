<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Assist Entity.
 *
 * @property int $id
 * @property int $budget_id
 * @property \App\Model\Entity\Budget $budget
 * @property int $worker_id
 * @property \App\Model\Entity\Worker $worker
 * @property int $overtime
 * @property int $delay
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property int $user_created_id
 * @property int $user_modified_id
 * @property \App\Model\Entity\User $user
 */
class Assist extends Entity
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
