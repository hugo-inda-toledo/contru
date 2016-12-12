<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('List Budget Approvals'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Budgets'), ['controller' => 'Budgets', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Budget'), ['controller' => 'Budgets', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="budgetApprovals form large-10 medium-9 columns">
    <?= $this->Form->create($budgetApproval); ?>
    <fieldset>
        <legend><?= __('Add Budget Approval') ?></legend>
        <?php
            echo $this->Form->input('budget_id', ['options' => $budgets]);
            echo $this->Form->input('user_id', ['options' => $users]);
            echo $this->Form->input('budget_state_id');
            echo $this->Form->input('approve');
            echo $this->Form->input('reject');
            echo $this->Form->input('comment');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
