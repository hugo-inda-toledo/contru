<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('List Deal Details'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Deals'), ['controller' => 'Deals', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Deal'), ['controller' => 'Deals', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Budget Items'), ['controller' => 'BudgetItems', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Budget Item'), ['controller' => 'BudgetItems', 'action' => 'add']) ?></li>
    </ul>
</div>
<div class="dealDetails form large-10 medium-9 columns">
    <?= $this->Form->create($dealDetail) ?>
    <fieldset>
        <legend><?= __('Add Deal Detail') ?></legend>
        <?php
            echo $this->Form->input('deal_id', ['options' => $deals]);
            echo $this->Form->input('budget_items_id', ['options' => $budgetItems]);
            echo $this->Form->input('percentage');
            echo $this->Form->input('user_created_id');
            echo $this->Form->input('user_modified_id');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
