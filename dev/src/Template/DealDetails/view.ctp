<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Edit Deal Detail'), ['action' => 'edit', $dealDetail->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Deal Detail'), ['action' => 'delete', $dealDetail->id], ['confirm' => __('Are you sure you want to delete # {0}?', $dealDetail->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Deal Details'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Deal Detail'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Deals'), ['controller' => 'Deals', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Deal'), ['controller' => 'Deals', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Budget Items'), ['controller' => 'BudgetItems', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Budget Item'), ['controller' => 'BudgetItems', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="dealDetails view large-10 medium-9 columns">
    <h2><?= h($dealDetail->id) ?></h2>
    <div class="row">
        <div class="large-5 columns strings">
            <h6 class="subheader"><?= __('Deal') ?></h6>
            <p><?= $dealDetail->has('deal') ? $this->Html->link($dealDetail->deal->id, ['controller' => 'Deals', 'action' => 'view', $dealDetail->deal->id]) : '' ?></p>
            <h6 class="subheader"><?= __('Budget Item') ?></h6>
            <p><?= $dealDetail->has('budget_item') ? $this->Html->link($dealDetail->budget_item->name, ['controller' => 'BudgetItems', 'action' => 'view', $dealDetail->budget_item->id]) : '' ?></p>
        </div>
        <div class="large-2 columns numbers end">
            <h6 class="subheader"><?= __('Id') ?></h6>
            <p><?= $this->Number->format($dealDetail->id) ?></p>
            <h6 class="subheader"><?= __('Percentage') ?></h6>
            <p><?= $this->Number->format($dealDetail->percentage) ?></p>
            <h6 class="subheader"><?= __('User Created Id') ?></h6>
            <p><?= $this->Number->format($dealDetail->user_created_id) ?></p>
            <h6 class="subheader"><?= __('User Modified Id') ?></h6>
            <p><?= $this->Number->format($dealDetail->user_modified_id) ?></p>
        </div>
        <div class="large-2 columns dates end">
            <h6 class="subheader"><?= __('Created') ?></h6>
            <p><?= h($dealDetail->created) ?></p>
            <h6 class="subheader"><?= __('Modified') ?></h6>
            <p><?= h($dealDetail->modified) ?></p>
        </div>
    </div>
</div>
