<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('New Budget Approval'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Budgets'), ['controller' => 'Budgets', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Budget'), ['controller' => 'Budgets', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="budgetApprovals index large-10 medium-9 columns">
    <table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('id') ?></th>
            <th><?= $this->Paginator->sort('budget_id') ?></th>
            <th><?= $this->Paginator->sort('user_id') ?></th>
            <th><?= $this->Paginator->sort('budget_state_id') ?></th>
            <th><?= $this->Paginator->sort('approve') ?></th>
            <th><?= $this->Paginator->sort('reject') ?></th>
            <th><?= $this->Paginator->sort('created') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($budgetApprovals as $budgetApproval): ?>
        <tr>
            <td><?= $this->Number->format($budgetApproval->id) ?></td>
            <td>
                <?= $budgetApproval->has('budget') ? $this->Html->link($budgetApproval->budget->id, ['controller' => 'Budgets', 'action' => 'review', $budgetApproval->budget->id]) : '' ?>
            </td>
            <td>
                <?= $budgetApproval->has('user') ? $this->Html->link($budgetApproval->user->id, ['controller' => 'Users', 'action' => 'view', $budgetApproval->user->id]) : '' ?>
            </td>
            <td><?= $this->Number->format($budgetApproval->budget_state_id) ?></td>
            <td><?= $this->Number->format($budgetApproval->approve) ?></td>
            <td><?= $this->Number->format($budgetApproval->reject) ?></td>
            <td><?= h($budgetApproval->created) ?></td>
            <td class="actions">
                <?= $this->Html->link(__('View'), ['action' => 'view', $budgetApproval->id]) ?>
                <?= $this->Html->link(__('Edit'), ['action' => 'edit', $budgetApproval->id]) ?>
                <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $budgetApproval->id], ['confirm' => __('Are you sure you want to delete # {0}?', $budgetApproval->id)]) ?>
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
