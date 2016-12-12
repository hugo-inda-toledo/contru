<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('New Unit'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Budget Items'), ['controller' => 'BudgetItems', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Budget Item'), ['controller' => 'BudgetItems', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="units index large-10 medium-9 columns">
    <table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('id') ?></th>
            <th><?= $this->Paginator->sort('name') ?></th>
            <th><?= $this->Paginator->sort('description') ?></th>
            <th><?= $this->Paginator->sort('created') ?></th>
            <th><?= $this->Paginator->sort('modified') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($units as $unit): ?>
        <tr>
            <td><?= $this->Number->format($unit->id) ?></td>
            <td><?= h($unit->name) ?></td>
            <td><?= h($unit->description) ?></td>
            <td><?= h($unit->created) ?></td>
            <td><?= h($unit->modified) ?></td>
            <td class="actions">
                <?= $this->Html->link(__('View'), ['action' => 'view', $unit->id]) ?>
                <?= $this->Html->link(__('Edit'), ['action' => 'edit', $unit->id]) ?>
                <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $unit->id], ['confirm' => __('Are you sure you want to delete # {0}?', $unit->id)]) ?>
            </td>
        </tr>

    <?php endforeach; ?>
    </tbody>
    </table>
    <?= $this->Element('paginador'); ?>
</div>
