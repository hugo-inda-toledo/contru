<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Edit Budget Approval'), ['action' => 'edit', $budgetApproval->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Budget Approval'), ['action' => 'delete', $budgetApproval->id], ['confirm' => __('Are you sure you want to delete # {0}?', $budgetApproval->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Budget Approvals'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Budget Approval'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Budgets'), ['controller' => 'Budgets', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Budget'), ['controller' => 'Budgets', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="budgetApprovals view large-10 medium-9 columns">
    <h2><?= h($budgetApproval->id) ?></h2>
    <div class="row">
        <div class="large-5 columns strings">
            <h6 class="subheader"><?= __('Budget') ?></h6>
            <p><?= $budgetApproval->has('budget') ? $this->Html->link($budgetApproval->budget->id, ['controller' => 'Budgets', 'action' => 'review', $budgetApproval->budget->id]) : '' ?></p>
            <h6 class="subheader"><?= __('User') ?></h6>
            <p><?= $budgetApproval->has('user') ? $this->Html->link($budgetApproval->user->id, ['controller' => 'Users', 'action' => 'view', $budgetApproval->user->id]) : '' ?></p>
        </div>
        <div class="large-2 columns numbers end">
            <h6 class="subheader"><?= __('Id') ?></h6>
            <p><?= $this->Number->format($budgetApproval->id) ?></p>
            <h6 class="subheader"><?= __('Budget State Id') ?></h6>
            <p><?= $this->Number->format($budgetApproval->budget_state_id) ?></p>
            <h6 class="subheader"><?= __('Approve') ?></h6>
            <p><?= $this->Number->format($budgetApproval->approve) ?></p>
            <h6 class="subheader"><?= __('Reject') ?></h6>
            <p><?= $this->Number->format($budgetApproval->reject) ?></p>
        </div>
        <div class="large-2 columns dates end">
            <h6 class="subheader"><?= __('Created') ?></h6>
            <p><?= h($budgetApproval->created) ?></p>
            <h6 class="subheader"><?= __('Modified') ?></h6>
            <p><?= h($budgetApproval->modified) ?></p>
        </div>
    </div>
    <div class="row texts">
        <div class="columns large-9">
            <h6 class="subheader"><?= __('Comment') ?></h6>
            <?= $this->Text->autoParagraph(h($budgetApproval->comment)); ?>

        </div>
    </div>
</div>
