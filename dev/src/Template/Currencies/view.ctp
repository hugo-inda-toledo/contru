<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Edit Currency'), ['action' => 'edit', $currency->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Currency'), ['action' => 'delete', $currency->id], ['confirm' => __('Are you sure you want to delete # {0}?', $currency->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Currencies'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Currency'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Budgets'), ['controller' => 'Budgets', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Budget'), ['controller' => 'Budgets', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="currencies view large-10 medium-9 columns">
    <h2><?= h($currency->name) ?></h2>
    <div class="row">
        <div class="large-5 columns strings">
            <h6 class="subheader"><?= __('Name') ?></h6>
            <p><?= h($currency->name) ?></p>
            <h6 class="subheader"><?= __('Description') ?></h6>
            <p><?= h($currency->description) ?></p>
            <h6 class="subheader"><?= __('Amount') ?></h6>
            <p><?= h($currency->amount) ?></p>
        </div>
        <div class="large-2 columns numbers end">
            <h6 class="subheader"><?= __('Id') ?></h6>
            <p><?= $this->Number->format($currency->id) ?></p>
        </div>
        <div class="large-2 columns dates end">
            <h6 class="subheader"><?= __('Created') ?></h6>
            <p><?= h($currency->created) ?></p>
            <h6 class="subheader"><?= __('Modified') ?></h6>
            <p><?= h($currency->modified) ?></p>
        </div>
    </div>
</div>
<div class="related row">
    <div class="column large-12">
    <h4 class="subheader"><?= __('Related Budgets') ?></h4>
    <?php if (!empty($currency->budgets)): ?>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?= __('Id') ?></th>
            <th><?= __('Building Id') ?></th>
            <th><?= __('Client') ?></th>
            <th><?= __('Duration') ?></th>
            <th><?= __('Uf Value') ?></th>
            <th><?= __('Total Cost Uf') ?></th>
            <th><?= __('Comments') ?></th>
            <th><?= __('File') ?></th>
            <th><?= __('General Costs') ?></th>
            <th><?= __('Utilities') ?></th>
            <th><?= __('Material Contribution') ?></th>
            <th><?= __('Retentions') ?></th>
            <th><?= __('Advances') ?></th>
            <th><?= __('Monthly Lunch') ?></th>
            <th><?= __('Monthly Mobilization') ?></th>
            <th><?= __('Created') ?></th>
            <th><?= __('Modified') ?></th>
            <th><?= __('User Created Id') ?></th>
            <th><?= __('User Modified Id') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
        <?php foreach ($currency->budgets as $budgets): ?>
        <tr>
            <td><?= h($budgets->id) ?></td>
            <td><?= h($budgets->building_id) ?></td>
            <td><?= h($budgets->client) ?></td>
            <td><?= h($budgets->duration) ?></td>
            <td><?= h($budgets->uf_value) ?></td>
            <td><?= h($budgets->total_cost_uf) ?></td>
            <td><?= h($budgets->comments) ?></td>
            <td><?= h($budgets->file) ?></td>
            <td><?= h($budgets->general_costs) ?></td>
            <td><?= h($budgets->utilities) ?></td>
            <td><?= h($budgets->retentions) ?></td>
            <td><?= h($budgets->advances) ?></td>
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
