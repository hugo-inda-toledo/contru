<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('List Budget Items'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Budgets'), ['controller' => 'Budgets', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Budget'), ['controller' => 'Budgets', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Units'), ['controller' => 'Units', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Unit'), ['controller' => 'Units', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Completed Tasks'), ['controller' => 'CompletedTasks', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Completed Task'), ['controller' => 'CompletedTasks', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Deals'), ['controller' => 'Deals', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Deal'), ['controller' => 'Deals', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Guide Entries'), ['controller' => 'GuideEntries', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Guide Entry'), ['controller' => 'GuideEntries', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Guide Exits'), ['controller' => 'GuideExits', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Guide Exit'), ['controller' => 'GuideExits', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Invoices'), ['controller' => 'Invoices', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Invoice'), ['controller' => 'Invoices', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Progress'), ['controller' => 'Progress', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Progres'), ['controller' => 'Progress', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Purchase Orders'), ['controller' => 'PurchaseOrders', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Purchase Order'), ['controller' => 'PurchaseOrders', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Rendition Items'), ['controller' => 'RenditionItems', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Rendition Item'), ['controller' => 'RenditionItems', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="budgetItems form large-10 medium-9 columns">
    <?= $this->Form->create($budgetItem); ?>
    <fieldset>
        <legend><?= __('Add Budget Item') ?></legend>
        <?php
            echo $this->Form->input('budget_id', ['options' => $budgets]);
            echo $this->Form->input('parent_id', ['options' => $parentBudgetItems]);
            echo $this->Form->input('lft');
            echo $this->Form->input('rght');
            echo $this->Form->input('item');
            echo $this->Form->input('description');
            echo $this->Form->input('unit_id', ['options' => $units]);
            echo $this->Form->input('quantity');
            echo $this->Form->input('unity_price');
            echo $this->Form->input('total_price');
            echo $this->Form->input('total_uf');
            echo $this->Form->input('comments');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
