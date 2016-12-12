<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('New Deal Detail'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Deals'), ['controller' => 'Deals', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Deal'), ['controller' => 'Deals', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Budget Items'), ['controller' => 'BudgetItems', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Budget Item'), ['controller' => 'BudgetItems', 'action' => 'add']) ?></li>
    </ul>
</div>
<div class="dealDetails index large-10 medium-9 columns">
    <table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('id') ?></th>
            <th><?= $this->Paginator->sort('deal_id') ?></th>
            <th><?= $this->Paginator->sort('budget_items_id') ?></th>
            <th><?= $this->Paginator->sort('percentage') ?></th>
            <th><?= $this->Paginator->sort('created') ?></th>
            <th><?= $this->Paginator->sort('modified') ?></th>
            <th><?= $this->Paginator->sort('user_created_id') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($dealDetails as $dealDetail): ?>
        <tr>
            <td><?= $this->Number->format($dealDetail->id) ?></td>
            <td>
                <?= $dealDetail->has('deal') ? $this->Html->link($dealDetail->deal->id, ['controller' => 'Deals', 'action' => 'view', $dealDetail->deal->id]) : '' ?>
            </td>
            <td>
                <?= $dealDetail->has('budget_item') ? $this->Html->link($dealDetail->budget_item->name, ['controller' => 'BudgetItems', 'action' => 'view', $dealDetail->budget_item->id]) : '' ?>
            </td>
            <td><?= $this->Number->format($dealDetail->percentage) ?></td>
            <td><?= h($dealDetail->created) ?></td>
            <td><?= h($dealDetail->modified) ?></td>
            <td><?= $this->Number->format($dealDetail->user_created_id) ?></td>
            <td class="actions">
                <?= $this->Html->link(__('View'), ['action' => 'view', $dealDetail->id]) ?>
                <?= $this->Html->link(__('Edit'), ['action' => 'edit', $dealDetail->id]) ?>
                <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $dealDetail->id], ['confirm' => __('Are you sure you want to delete # {0}?', $dealDetail->id)]) ?>
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
