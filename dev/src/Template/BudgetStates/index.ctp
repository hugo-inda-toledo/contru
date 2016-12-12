<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('New Budget State'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Budget Approvals'), ['controller' => 'BudgetApprovals', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Budget Approval'), ['controller' => 'BudgetApprovals', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="budgetStates index large-10 medium-9 columns">
    <table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('id') ?></th>
            <th><?= $this->Paginator->sort('name') ?></th>
            <th><?= $this->Paginator->sort('created') ?></th>
            <th><?= $this->Paginator->sort('modified') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($budgetStates as $budgetState): ?>
        <tr>
            <td><?= $this->Number->format($budgetState->id) ?></td>
            <td><?= h($budgetState->name) ?></td>
            <td><?= h($budgetState->created) ?></td>
            <td><?= h($budgetState->modified) ?></td>
            <td class="actions">
                <?= $this->Html->link(__('View'), ['action' => 'view', $budgetState->id]) ?>
                <?= $this->Html->link(__('Edit'), ['action' => 'edit', $budgetState->id]) ?>
                <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $budgetState->id], ['confirm' => __('Are you sure you want to delete # {0}?', $budgetState->id)]) ?>
            </td>
        </tr>

    <?php endforeach; ?>
    </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
</div>
