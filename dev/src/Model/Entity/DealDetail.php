<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DealDetail Entity.
 *
 * @property int $id * @property int $deal_id * @property \App\Model\Entity\Deal $deal * @property int $budget_items_id * @property \App\Model\Entity\BudgetItem $budget_item * @property int $percentage * @property \Cake\I18n\Time $created * @property \Cake\I18n\Time $modified * @property int $user_created_id * @property \App\Model\Entity\UserCreated $user_created * @property int $user_modified_id * @property \App\Model\Entity\UserModified $user_modified */
class DealDetail extends Entity
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
        'deal_id' => true,
        'budget_item_id' => true,
        'percentage' => true,
        'created' => true,
        'modified' => true,
        'user_created_id' => true,
        'user_modified_id' => true,
    ];
}
