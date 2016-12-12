<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Edit Unit'), ['action' => 'edit', $unit->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Unit'), ['action' => 'delete', $unit->id], ['confirm' => __('Are you sure you want to delete # {0}?', $unit->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Units'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Unit'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Budget Items'), ['controller' => 'BudgetItems', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Budget Item'), ['controller' => 'BudgetItems', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="units view large-10 medium-9 columns">
    <h2><?= h($unit->name) ?></h2>
    <div class="row">
        <div class="large-5 columns strings">
            <h6 class="subheader"><?= __('Name') ?></h6>
            <p><?= h($unit->name) ?></p>
            <h6 class="subheader"><?= __('Description') ?></h6>
            <p><?= h($unit->description) ?></p>
        </div>
        <div class="large-2 columns numbers end">
            <h6 class="subheader"><?= __('Id') ?></h6>
            <p><?= $this->Number->format($unit->id) ?></p>
        </div>
        <div class="large-2 columns dates end">
            <h6 class="subheader"><?= __('Created') ?></h6>
            <p><?= h($unit->created) ?></p>
            <h6 class="subheader"><?= __('Modified') ?></h6>
            <p><?= h($unit->modified) ?></p>
        </div>
    </div>
</div>
<div class="related row">
    <div class="column large-12">
    <h4 class="subheader"><?= __('Related BudgetItems') ?></h4>
    <?php if (!empty($unit->budget_items)): ?>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?= __('Id') ?></th>
            <th><?= __('Budget Id') ?></th>
            <th><?= __('Parent Id') ?></th>
            <th><?= __('Title') ?></th>
            <th><?= __('Lft') ?></th>
            <th><?= __('Rght') ?></th>
            <th><?= __('Name') ?></th>
            <th><?= __('Unit Id') ?></th>
            <th><?= __('Quantity') ?></th>
            <th><?= __('Unity Price') ?></th>
            <th><?= __('Total Price') ?></th>
            <th><?= __('Total Uf') ?></th>
            <th><?= __('Comments') ?></th>
            <th><?= __('Created') ?></th>
            <th><?= __('Modified') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
        <?php foreach ($unit->budget_items as $budgetItems): ?>
        <tr>
            <td><?= h($budgetItems->id) ?></td>
            <td><?= h($budgetItems->budget_id) ?></td>
            <td><?= h($budgetItems->parent_id) ?></td>
            <td><?= h($budgetItems->title) ?></td>
            <td><?= h($budgetItems->lft) ?></td>
            <td><?= h($budgetItems->rght) ?></td>
            <td><?= h($budgetItems->name) ?></td>
            <td><?= h($budgetItems->unit_id) ?></td>
            <td><?= h($budgetItems->quantity) ?></td>
            <td><?= h($budgetItems->unity_price) ?></td>
            <td><?= h($budgetItems->total_price) ?></td>
            <td><?= h($budgetItems->total_uf) ?></td>
            <td><?= h($budgetItems->comments) ?></td>
            <td><?= h($budgetItems->created) ?></td>
            <td><?= h($budgetItems->modified) ?></td>

            <td class="actions">
                <?= $this->Html->link(__('View'), ['controller' => 'BudgetItems', 'action' => 'view', $budgetItems->id]) ?>
                <?= $this->Html->link(__('Edit'), ['controller' => 'BudgetItems', 'action' => 'edit', $budgetItems->id]) ?>
                <?= $this->Form->postLink(__('Delete'), ['controller' => 'BudgetItems', 'action' => 'delete', $budgetItems->id], ['confirm' => __('Are you sure you want to delete # {0}?', $budgetItems->id)]) ?>
            </td>
        </tr>

        <?php endforeach; ?>
    </table>
    <?php endif; ?>
    </div>
</div>
