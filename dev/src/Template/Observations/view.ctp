<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Edit Observation'), ['action' => 'edit', $observation->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Observation'), ['action' => 'delete', $observation->id], ['confirm' => __('Are you sure you want to delete # {0}?', $observation->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Observations'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Observation'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="observations view large-10 medium-9 columns">
    <h2><?= h($observation->id) ?></h2>
    <div class="row">
        <div class="large-5 columns strings">
            <h6 class="subheader"><?= __('Model') ?></h6>
            <p><?= h($observation->model) ?></p>
            <h6 class="subheader"><?= __('Action') ?></h6>
            <p><?= h($observation->action) ?></p>
            <h6 class="subheader"><?= __('User') ?></h6>
            <p><?= $observation->has('user') ? $this->Html->link($observation->user->id, ['controller' => 'Users', 'action' => 'view', $observation->user->id]) : '' ?></p>
        </div>
        <div class="large-2 columns numbers end">
            <h6 class="subheader"><?= __('Id') ?></h6>
            <p><?= $this->Number->format($observation->id) ?></p>
            <h6 class="subheader"><?= __('Model Id') ?></h6>
            <p><?= $this->Number->format($observation->model_id) ?></p>
        </div>
        <div class="large-2 columns dates end">
            <h6 class="subheader"><?= __('Created') ?></h6>
            <p><?= h($observation->created) ?></p>
            <h6 class="subheader"><?= __('Modified') ?></h6>
            <p><?= h($observation->modified) ?></p>
        </div>
    </div>
    <div class="row texts">
        <div class="columns large-9">
            <h6 class="subheader"><?= __('Observation') ?></h6>
            <?= $this->Text->autoParagraph(h($observation->observation)); ?>

        </div>
    </div>
</div>
