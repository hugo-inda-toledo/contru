<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $approval->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $approval->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Approvals'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="approvals form large-10 medium-9 columns">
    <?= $this->Form->create($approval); ?>
    <fieldset>
        <legend><?= __('Edit Approval') ?></legend>
        <?php
            echo $this->Form->input('model');
            echo $this->Form->input('action');
            echo $this->Form->input('approve');
            echo $this->Form->input('reject');
            echo $this->Form->input('user_id', ['options' => $users]);
            echo $this->Form->input('comment');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
