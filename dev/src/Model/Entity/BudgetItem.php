<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * BudgetItem Entity.
 */
class BudgetItem extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'budget_id' => true,
        'parent_id' => true,
        'lft' => true,
        'rght' => true,
        'item' => true,
        'description' => true,
        'unit_id' => true,
        'quantity' => true,
        'unity_price' => true,
        'total_price' => true,
        'total_uf' => true,
        'comments' => true,
        'disabled' => true,
        'extra' => true,
        'general_cost' => true,
        'utilities' => true,
        'retention' => true,
        'advance' => true,
        'commited' => true,
        'spent' => true,
        'invoiced' => true,
        'commited_materials' => true,
        'spent_materials' => true,
        'invoiced_materials' => true,
        'commited_subcontracts' => true,
        'spent_subcontracts' => true,
        'invoiced_subcontracts' => true,
        'diff_obj_vs_comp' => true,
        'diff_obj_vs_gast' => true,
        'diff_ppto_vs_comprometido' => true,
        'budget' => true,
        'parents_budget_item' => true,
        'unit' => true,
        'child_budget_items' => true,
        'completed_tasks' => true,
        'deals' => true,
        'guide_entries' => true,
        'guide_exits' => true,
        'invoices' => true,
        'progress' => true,
        'purchase_orders' => true,
        'rendition_items' => true,
        'target_value' => true,
    ];

    protected function _getFullItem()
    {
        return $this->_properties['item'] . '  ' . $this->_properties['description'];
    }

    protected function _getItemType()
    {
        $tmp_type = '';
        switch($this->_properties['extra']) {
            case 0:
                $tmp_type = 'original';
                break;
            case 1:
                $tmp_type = 'adicional';
                break;
            case 2:
                $tmp_type = 'gasto no considerado';
                break;
            case 3:
                $tmp_type = 'gasto general';
                break;
        }

        return $tmp_type;
    }
}
