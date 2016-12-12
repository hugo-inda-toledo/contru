<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('List Currencies Values'), ['action' => 'index']) ?></li>
    </ul>
</div>
<div class="currenciesValues form large-10 medium-9 columns">
    <?= $this->Form->create($currenciesValue); ?>
    <fieldset>
        <legend><?= __('Add Currencies Value') ?></legend>
        <?php
            echo $this->Form->input('budget_id');
            echo $this->Form->input('currency_id');
            echo $this->Form->input('value');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
