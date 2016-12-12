<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('New Observation'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="observations index large-10 medium-9 columns">
    <table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('id') ?></th>
            <th><?= $this->Paginator->sort('model') ?></th>
            <th><?= $this->Paginator->sort('action') ?></th>
            <th><?= $this->Paginator->sort('model_id') ?></th>
            <th><?= $this->Paginator->sort('user_id') ?></th>
            <th><?= $this->Paginator->sort('created') ?></th>
            <th><?= $this->Paginator->sort('modified') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($observations as $observation): ?>
        <tr>
            <td><?= $this->Number->format($observation->id) ?></td>
            <td><?= h($observation->model) ?></td>
            <td><?= h($observation->action) ?></td>
            <td><?= $this->Number->format($observation->model_id) ?></td>
            <td>
                <?= $observation->has('user') ? $this->Html->link($observation->user->id, ['controller' => 'Users', 'action' => 'view', $observation->user->id]) : '' ?>
            </td>
            <td><?= h($observation->created) ?></td>
            <td><?= h($observation->modified) ?></td>
            <td class="actions">
                <?= $this->Html->link(__('View'), ['action' => 'view', $observation->id]) ?>
                <?= $this->Html->link(__('Edit'), ['action' => 'edit', $observation->id]) ?>
                <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $observation->id], ['confirm' => __('Are you sure you want to delete # {0}?', $observation->id)]) ?>
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
