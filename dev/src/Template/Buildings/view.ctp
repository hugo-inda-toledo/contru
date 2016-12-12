<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Edit Building'), ['action' => 'edit', $building->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Building'), ['action' => 'delete', $building->id], ['confirm' => __('Are you sure you want to delete # {0}?', $building->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Buildings'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Building'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Budgets'), ['controller' => 'Budgets', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Budget'), ['controller' => 'Budgets', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="buildings view large-10 medium-9 columns">
    <h2><?= h($building->id) ?></h2>
    <div class="row">
        <div class="large-5 columns strings">
            <h6 class="subheader"><?= __('Name') ?></h6>
            <p><?= h($building->name) ?></p>
            <h6 class="subheader"><?= __('Description') ?></h6>
            <p><?= h($building->description) ?></p>
        </div>
        <div class="large-2 columns numbers end">
            <h6 class="subheader"><?= __('Id') ?></h6>
            <p><?= $this->Number->format($building->id) ?></p>
            <h6 class="subheader"><?= __('Softland Id') ?></h6>
            <p><?= $this->Number->format($building->softland_id) ?></p>
        </div>
        <div class="large-2 columns dates end">
            <h6 class="subheader"><?= __('Created') ?></h6>
            <p><?= h($building->created) ?></p>
        </div>
    </div>
</div>
<div class="related row">
    <div class="column large-12">
    <h4 class="subheader"><?= __('Related Budgets') ?></h4>
    <?php if (!empty($building->budgets)): ?>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?= __('Id') ?></th>
            <th><?= __('Building Id') ?></th>
            <th><?= __('Client') ?></th>
            <th><?= __('Duration') ?></th>
            <th><?= __('Uf Value') ?></th>
            <th><?= __('Total Cost Uf') ?></th>
            <th><?= __('Comments') ?></th>
            <th><?= __('Created') ?></th>
            <th><?= __('Modified') ?></th>
            <th><?= __('User Created Id') ?></th>
            <th><?= __('User Modified Id') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
        <?php foreach ($building->budgets as $budgets): ?>
        <tr>
            <td><?= h($budgets->id) ?></td>
            <td><?= h($budgets->building_id) ?></td>
            <td><?= h($budgets->client) ?></td>
            <td><?= h($budgets->duration) ?></td>
            <td><?= h($budgets->uf_value) ?></td>
            <td><?= h($budgets->total_cost_uf) ?></td>
            <td><?= h($budgets->comments) ?></td>
            <td><?= h($budgets->created) ?></td>
            <td><?= h($budgets->modified) ?></td>
            <td><?= h($budgets->user_created_id) ?></td>
            <td><?= h($budgets->user_modified_id) ?></td>

            <td class="actions">
                <?= $this->Html->link(__('View'), ['controller' => 'Budgets', 'action' => 'review', $budgets->id]) ?>
                <?= $this->Html->link(__('Edit'), ['controller' => 'Budgets', 'action' => 'edit', $budgets->id]) ?>
                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Budgets', 'action' => 'delete', $budgets->id], ['confirm' => __('Are you sure you want to delete # {0}?', $budgets->id)]) ?>
            </td>
        </tr>

        <?php endforeach; ?>
    </table>
    <?php endif; ?>
    </div>
</div>
