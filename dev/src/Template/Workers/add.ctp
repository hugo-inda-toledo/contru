<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('List Workers'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Assists'), ['controller' => 'Assists', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Assist'), ['controller' => 'Assists', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Bonuses'), ['controller' => 'Bonuses', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Bonus'), ['controller' => 'Bonuses', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Completed Tasks'), ['controller' => 'CompletedTasks', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Completed Task'), ['controller' => 'CompletedTasks', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Deals'), ['controller' => 'Deals', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Deal'), ['controller' => 'Deals', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="workers form large-10 medium-9 columns">
    <?= $this->Form->create($worker); ?>
    <fieldset>
        <legend><?= __('Add Worker') ?></legend>
        <?php
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
