<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('New Currencies Value'), ['action' => 'add']) ?></li>
    </ul>
</div>
<div class="currenciesValues index large-10 medium-9 columns">
    <table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('id') ?></th>
            <th><?= $this->Paginator->sort('budget_id') ?></th>
            <th><?= $this->Paginator->sort('currency_id') ?></th>
            <th><?= $this->Paginator->sort('value') ?></th>
            <th><?= $this->Paginator->sort('created') ?></th>
            <th><?= $this->Paginator->sort('modified') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($currenciesValues as $currenciesValue): ?>
        <tr>
            <td><?= $this->Number->format($currenciesValue->id) ?></td>
            <td><?= $this->Number->format($currenciesValue->budget_id) ?></td>
            <td><?= $this->Number->format($currenciesValue->currency_id) ?></td>
            <td><?= $this->Number->format($currenciesValue->value) ?></td>
            <td><?= h($currenciesValue->created) ?></td>
            <td><?= h($currenciesValue->modified) ?></td>
            <td class="actions">
                <?= $this->Html->link(__('View'), ['action' => 'view', $currenciesValue->id]) ?>
                <?= $this->Html->link(__('Edit'), ['action' => 'edit', $currenciesValue->id]) ?>
                <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $currenciesValue->id], ['confirm' => __('Are you sure you want to delete # {0}?', $currenciesValue->id)]) ?>
            </td>
        </tr>

    <?php endforeach; ?>
    </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
</div>
