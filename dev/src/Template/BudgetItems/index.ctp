<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('New Budget Item'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Budgets'), ['controller' => 'Budgets', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Budget'), ['controller' => 'Budgets', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Units'), ['controller' => 'Units', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Unit'), ['controller' => 'Units', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Completed Tasks'), ['controller' => 'CompletedTasks', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Completed Task'), ['controller' => 'CompletedTasks', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Deals'), ['controller' => 'Deals', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Deal'), ['controller' => 'Deals', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Guide Entries'), ['controller' => 'GuideEntries', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Guide Entry'), ['controller' => 'GuideEntries', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Guide Exits'), ['controller' => 'GuideExits', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Guide Exit'), ['controller' => 'GuideExits', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Invoices'), ['controller' => 'Invoices', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Invoice'), ['controller' => 'Invoices', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Progress'), ['controller' => 'Progress', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Progres'), ['controller' => 'Progress', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Purchase Orders'), ['controller' => 'PurchaseOrders', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Purchase Order'), ['controller' => 'PurchaseOrders', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Rendition Items'), ['controller' => 'RenditionItems', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Rendition Item'), ['controller' => 'RenditionItems', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="budgetItems index large-10 medium-9 columns">
    <table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('id') ?></th>
            <th><?= $this->Paginator->sort('budget_id') ?></th>
            <th><?= $this->Paginator->sort('parent_id') ?></th>
            <th><?= $this->Paginator->sort('item') ?></th>
            <th><?= $this->Paginator->sort('name') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($budgetItems as $budgetItem): ?>
        <tr>
            <td><?= $this->Number->format($budgetItem->id) ?></td>
            <td>
                <?= $budgetItem->has('budget') ? $this->Html->link($budgetItem->budget->id, ['controller' => 'Budgets', 'action' => 'review', $budgetItem->budget->id]) : '' ?>
            </td>
            <td><?= $this->Number->format($budgetItem->parent_id) ?></td>
            <td><?= h($budgetItem->item) ?></td>
            <td><?= h($budgetItem->description) ?></td>
            <td class="actions">
                <?= $this->Html->link(__('View'), ['action' => 'view', $budgetItem->id]) ?>
                <?= $this->Html->link(__('Edit'), ['action' => 'edit', $budgetItem->id]) ?>
                <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $budgetItem->id], ['confirm' => __('Are you sure you want to delete # {0}?', $budgetItem->id)]) ?>
            </td>
        </tr>

    <?php endforeach; ?>
    </tbody>
    </table>
    <?= $this->Element('paginador'); ?>
</div>
