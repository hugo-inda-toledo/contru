<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Edit Budget State'), ['action' => 'edit', $budgetState->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Budget State'), ['action' => 'delete', $budgetState->id], ['confirm' => __('Are you sure you want to delete # {0}?', $budgetState->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Budget States'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Budget State'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Budget Approvals'), ['controller' => 'BudgetApprovals', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Budget Approval'), ['controller' => 'BudgetApprovals', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="budgetStates view large-10 medium-9 columns">
    <h2><?= h($budgetState->name) ?></h2>
    <div class="row">
        <div class="large-5 columns strings">
            <h6 class="subheader"><?= __('Name') ?></h6>
            <p><?= h($budgetState->name) ?></p>
        </div>
        <div class="large-2 columns numbers end">
            <h6 class="subheader"><?= __('Id') ?></h6>
            <p><?= $this->Number->format($budgetState->id) ?></p>
        </div>
        <div class="large-2 columns dates end">
            <h6 class="subheader"><?= __('Created') ?></h6>
            <p><?= h($budgetState->created) ?></p>
            <h6 class="subheader"><?= __('Modified') ?></h6>
            <p><?= h($budgetState->modified) ?></p>
        </div>
    </div>
    <div class="row texts">
        <div class="columns large-9">
            <h6 class="subheader"><?= __('Description') ?></h6>
            <?= $this->Text->autoParagraph(h($budgetState->description)); ?>

        </div>
    </div>
</div>
<div class="related row">
    <div class="column large-12">
    <h4 class="subheader"><?= __('Related BudgetApprovals') ?></h4>
    <?php if (!empty($budgetState->budget_approvals)): ?>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?= __('Id') ?></th>
            <th><?= __('Budget Id') ?></th>
            <th><?= __('User Id') ?></th>
            <th><?= __('Budget State Id') ?></th>
            <th><?= __('Approve') ?></th>
            <th><?= __('Reject') ?></th>
            <th><?= __('Comment') ?></th>
            <th><?= __('Created') ?></th>
            <th><?= __('Modified') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
        <?php foreach ($budgetState->budget_approvals as $budgetApprovals): ?>
        <tr>
            <td><?= h($budgetApprovals->id) ?></td>
            <td><?= h($budgetApprovals->budget_id) ?></td>
            <td><?= h($budgetApprovals->user_id) ?></td>
            <td><?= h($budgetApprovals->budget_state_id) ?></td>
            <td><?= h($budgetApprovals->approve) ?></td>
            <td><?= h($budgetApprovals->reject) ?></td>
            <td><?= h($budgetApprovals->comment) ?></td>
            <td><?= h($budgetApprovals->created) ?></td>
            <td><?= h($budgetApprovals->modified) ?></td>

            <td class="actions">
                <?= $this->Html->link(__('View'), ['controller' => 'BudgetApprovals', 'action' => 'view', $budgetApprovals->id]) ?>
                <?= $this->Html->link(__('Edit'), ['controller' => 'BudgetApprovals', 'action' => 'edit', $budgetApprovals->id]) ?>
                <?= $this->Form->postLink(__('Delete'), ['controller' => 'BudgetApprovals', 'action' => 'delete', $budgetApprovals->id], ['confirm' => __('Are you sure you want to delete # {0}?', $budgetApprovals->id)]) ?>
            </td>
        </tr>

        <?php endforeach; ?>
    </table>
    <?php endif; ?>
    </div>
</div>
