<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * BudgetApproval Entity.
 */
class BudgetApproval extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'budget_id' => true,
        'user_id' => true,
        'budget_state_id' => true,
        'approve' => true,
        'reject' => true,
        'comment' => true,
        'budget' => true,
        'user' => true,
    ];
}
