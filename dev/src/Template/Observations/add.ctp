<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('List Observations'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="observations form large-10 medium-9 columns">
    <?= $this->Form->create($observation); ?>
    <fieldset>
        <legend><?= __('Add Observation') ?></legend>
        <?php
            echo $this->Form->input('model');
            echo $this->Form->input('action');
            echo $this->Form->input('model_id',['type' => 'text']);
            echo $this->Form->input('user_id', ['options' => $users]);
            echo $this->Form->input('observation');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
