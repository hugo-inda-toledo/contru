<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Edit Currencies Value'), ['action' => 'edit', $currenciesValue->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Currencies Value'), ['action' => 'delete', $currenciesValue->id], ['confirm' => __('Are you sure you want to delete # {0}?', $currenciesValue->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Currencies Values'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Currencies Value'), ['action' => 'add']) ?> </li>
    </ul>
</div>
<div class="currenciesValues view large-10 medium-9 columns">
    <h2><?= h($currenciesValue->id) ?></h2>
    <div class="row">
        <div class="large-2 columns numbers end">
            <h6 class="subheader"><?= __('Id') ?></h6>
            <p><?= $this->Number->format($currenciesValue->id) ?></p>
            <h6 class="subheader"><?= __('Budget Id') ?></h6>
            <p><?= $this->Number->format($currenciesValue->budget_id) ?></p>
            <h6 class="subheader"><?= __('Currency Id') ?></h6>
            <p><?= $this->Number->format($currenciesValue->currency_id) ?></p>
            <h6 class="subheader"><?= __('Value') ?></h6>
            <p><?= $this->Number->format($currenciesValue->value) ?></p>
        </div>
        <div class="large-2 columns dates end">
            <h6 class="subheader"><?= __('Created') ?></h6>
            <p><?= h($currenciesValue->created) ?></p>
            <h6 class="subheader"><?= __('Modified') ?></h6>
            <p><?= h($currenciesValue->modified) ?></p>
        </div>
    </div>
</div>
