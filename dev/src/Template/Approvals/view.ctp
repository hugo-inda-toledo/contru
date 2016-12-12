<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Edit Approval'), ['action' => 'edit', $approval->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Approval'), ['action' => 'delete', $approval->id], ['confirm' => __('Are you sure you want to delete # {0}?', $approval->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Approvals'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Approval'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="approvals view large-10 medium-9 columns">
    <h2><?= h($approval->id) ?></h2>
    <div class="row">
        <div class="large-5 columns strings">
            <h6 class="subheader"><?= __('Model') ?></h6>
            <p><?= h($approval->model) ?></p>
            <h6 class="subheader"><?= __('Action') ?></h6>
            <p><?= h($approval->action) ?></p>
            <h6 class="subheader"><?= __('User') ?></h6>
            <p><?= $approval->has('user') ? $this->Html->link($approval->user->id, ['controller' => 'Users', 'action' => 'view', $approval->user->id]) : '' ?></p>
        </div>
        <div class="large-2 columns numbers end">
            <h6 class="subheader"><?= __('Id') ?></h6>
            <p><?= $this->Number->format($approval->id) ?></p>
            <h6 class="subheader"><?= __('Approve') ?></h6>
            <p><?= $this->Number->format($approval->approve) ?></p>
            <h6 class="subheader"><?= __('Reject') ?></h6>
            <p><?= $this->Number->format($approval->reject) ?></p>
        </div>
        <div class="large-2 columns dates end">
            <h6 class="subheader"><?= __('Created') ?></h6>
            <p><?= h($approval->created) ?></p>
            <h6 class="subheader"><?= __('Modified') ?></h6>
            <p><?= h($approval->modified) ?></p>
        </div>
    </div>
    <div class="row texts">
        <div class="columns large-9">
            <h6 class="subheader"><?= __('Comment') ?></h6>
            <?= $this->Text->autoParagraph(h($approval->comment)); ?>

        </div>
    </div>
</div>
