<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('New Approval'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="approvals index large-10 medium-9 columns">
    <table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('id') ?></th>
            <th><?= $this->Paginator->sort('model') ?></th>
            <th><?= $this->Paginator->sort('action') ?></th>
            <th><?= $this->Paginator->sort('approve') ?></th>
            <th><?= $this->Paginator->sort('reject') ?></th>
            <th><?= $this->Paginator->sort('user_id') ?></th>
            <th><?= $this->Paginator->sort('created') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($approvals as $approval): ?>
        <tr>
            <td><?= $this->Number->format($approval->id) ?></td>
            <td><?= h($approval->model) ?></td>
            <td><?= h($approval->action) ?></td>
            <td><?= $this->Number->format($approval->approve) ?></td>
            <td><?= $this->Number->format($approval->reject) ?></td>
            <td>
                <?= $approval->has('user') ? $this->Html->link($approval->user->id, ['controller' => 'Users', 'action' => 'view', $approval->user->id]) : '' ?>
            </td>
            <td><?= h($approval->created) ?></td>
            <td class="actions">
                <?= $this->Html->link(__('View'), ['action' => 'view', $approval->id]) ?>
                <?= $this->Html->link(__('Edit'), ['action' => 'edit', $approval->id]) ?>
                <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $approval->id], ['confirm' => __('Are you sure you want to delete # {0}?', $approval->id)]) ?>
            </td>
        </tr>

    <?php endforeach; ?>
    </tbody>
    </table>
    <?= $this->Element('paginador'); ?>
</div>
