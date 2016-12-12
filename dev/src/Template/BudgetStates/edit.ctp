<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $budgetState->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $budgetState->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Budget States'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Budget Approvals'), ['controller' => 'BudgetApprovals', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Budget Approval'), ['controller' => 'BudgetApprovals', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="budgetStates form large-10 medium-9 columns">
    <?= $this->Form->create($budgetState); ?>
    <fieldset>
        <legend><?= __('Edit Budget State') ?></legend>
        <?php
            echo $this->Form->input('name');
            echo $this->Form->input('description');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
