<?php
$crv = 1; // Conversion Reference Value
if(isset($the_budget_currency_value)){
    $crv = $the_budget_currency_value;
}
?>
<?php $group_id = $this->request->session()->read('Auth.User.group_id'); ?>
<?php if (!empty($bi['children'])) : ?>
    <?php
    $pSum = (isset($parent_sum[$bi['item']]['budget_total'])) ? $parent_sum[$bi['item']]['budget_total'] : 0;
    $guide_exits_total = (isset($parent_sum[$bi['item']]['guide_exits_total'])) ? $parent_sum[$bi['item']]['guide_exits_total'] : 0;
    $budget_items_subcontracts = (isset($parent_sum[$bi['item']]['subcontracts_total'])) ? $parent_sum[$bi['item']]['subcontracts_total'] : 0;
    $completed_tasks_total = (isset($parent_sum[$bi['item']]['completed_tasks_total'])) ? $parent_sum[$bi['item']]['completed_tasks_total'] : 0;
    $budget_total_available = $pSum - $guide_exits_total - $budget_items_subcontracts - $completed_tasks_total;

    $total_cost = moneda($pSum);
    $budget_goal = '(pendiente)';
    $budget_item_guide_exit = moneda($guide_exits_total);
    $worker_cost = moneda($completed_tasks_total);
    $subcontract_cost = moneda($budget_items_subcontracts);
    $total_available = moneda($budget_total_available);
?>
    <div class="panel panel-<?= $panel_type ?>">
        <div class="panel-heading" role="tab" id="<?= $bi['id'].'_2' ?>">
            <div class="row">
                <div class="col-xs-12 col-md-2">
                    <a class="panel-title" data-toggle="collapse" data-parent="<?= $bi['parent_id'] ?>" href="#<?= $bi['id'] ?>" aria-expanded="false" aria-controls="<?= $bi['id'] ?>"><?php
                        echo ($panel_type == "default") ? '<s>' . $bi['item'] . ' ' . $bi['description'] . ' </s> Deshabilitado ' : $bi['item'] . ' ' . $bi['description']; ?>
                    </a>
                </div>
                <div class="col-xs-12 col-md-8">
                    <div class="row">
                        <div class="col-xs-12 col-md-2 label-info clearfix ">
                            <span class="pull-left">Total:</span> <span class="pull-right"><?=$total_cost;?></span>
                        </div>
                        <div class="col-xs-12 col-md-2 label-success clearfix ">
                            <span class="pull-left">P.O.:</span> <span class="pull-right"><?=$budget_goal;?></span>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <div class="row">
                                <div class="col-xs-12 col-md-4 label-warning clearfix">
                                    <span class="pull-left">G.M.:</span>&nbsp;
                                    <span class="pull-right"><?=$budget_item_guide_exit;?></span>
                                </div>
                                <div class="col-xs-12 col-md-4 label-material-deep-orange-700 clearfix">
                                    <span class="pull-left">G.M.O.:</span>&nbsp;
                                    <span class="pull-right"><?=$worker_cost;?></span>
                                </div>
                                <div class="col-xs-12 col-md-4 label-warning clearfix">
                                    <span class="pull-left">G.S.C.:</span>&nbsp;
                                    <span class="pull-right"><?=$subcontract_cost;?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-2 label-info">
                            <span class="pull-left">Total disponible:</span> <span class="pull-right"><?=$total_available;?></span>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-2 clearfix">
                    <div class="pull-right">
                    <?php
                        if ((($bi['extra'] === 0 && $state->id < 6) || ($bi['extra'] === 1 && $state->id < 6))) :
                            if ($group_id == USR_GRP_GE_GRAL || $group_id == USR_GRP_COORD_PROY || $group_id == USR_GRP_GE_FINAN) :
                                echo ($bi['disabled'] == 0) ?
                                    $this->Html->link(__('Deshabilitar'), ['controller' => 'Budgets', 'action' => 'disable_item', $bi['id']], ['class' => 'confirm btn btn-danger btn-xs inline-button']) :
                                    $this->Html->link(__('Habilitar'), ['controller' => 'Budgets', 'action' => 'disable_item', $bi['id']], ['class' => 'confirm btn btn-success btn-xs inline-button']);
                                if ($bi['extra'] === 1 && $state->id < 6) :
                                    echo $this->Html->link(__('Configurar'), ['controller' => 'Budgets', 'action' => 'item_param', $bi['id']],
                                        ['class' => 'btn btn-primary btn-xs inline-button', 'escape' => false]);
                                endif;
                            endif;
                        endif;
                    ?>
                    </div>
                </div>
            </div>
                 <!-- <i class="mdi-action-settings-applications"></i> -->
        </div>
        <div id="<?= $bi['id'] ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="<?= $bi['id'].'_2' ?>">
            <div class="panel-body">
                <div class="panel-group <?php echo ($panel_type == "default") ? "text-muted" : ""; ?>" id="<?= $bi['id'] ?>" role="tablist" aria-multiselectable="true">
                <?php
                    foreach ($bi['children'] as $children) :
                        $panelType = ($children['disabled'] == 0) ? 'material-blue-grey-500': 'default';
                        if (!empty($children))
                        echo $this->element('acordion_view', ['bi' => $children, 'panel_type' => $panelType, 'state' => $state, 'group_id' => $group_id, 'the_budget_currency_value' => $crv]);
    	            endforeach; ?>
                </div>
    	    </div>
        </div>
    </div>
<?php else : ?>
	<div class="container-fluid <?php echo ($bi['disabled'] == 1) ? 'text-muted' : '' ?>" style="<?php echo ($bi['disabled'] == 1) ? 'background-color: #eaeaea' : '' ?>">
        <?php $guide_exit_total = (!empty($budget_items_guide_exits[$bi['id']])) ? $budget_items_guide_exits[$bi['id']] : 0; ?>
        <?php $subcontract_total = (!empty($budget_items_subcontracts[$bi['id']])) ? $budget_items_subcontracts[$bi['id']] : 0; ?>
        <div class="row">
            <div class="col-sm-8 col-md-8">
                <?php echo ($bi['disabled'] == 1) ? '<s><h3 class="panel-title">' . $bi['item'] .' '. $bi['description'] . '</h3></s>' :
                 '<h3 class="panel-title">' . $bi['item'].' '.$bi['description'] . '</h3>';?>
            </div>
            <div class="col-md-4 col-sm-4 text-center">
            <?php if (!empty($budget_items_progress[$bi['id']])) : ?>
                <strong>Avance: </strong><?= $budget_items_progress[$bi['id']]?>%
                <div class="progress">
                    <?php if ($budget_items_progress[$bi['id']] == 100) : ?>
                        <div class="progress-bar progress-bar-success" style="width: <?= $budget_items_progress[$bi['id']] ?>%"></div>
                    <?php else : ?>
                        <div class="progress-bar progress-bar-material-orange-<?= substr($budget_items_progress[$bi['id']], 0, 1) ?>00" style="width: <?= $budget_items_progress[$bi['id']] ?>%"></div>
                    <?php endif; ?>
                </div>
            <?php else : ?>
                <strong>Avance: 0%</strong>
            <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-4">
                <div class="row no-margin">
                    <div class="col-xs-12 col-material-blue-grey-100">
                        <div class="row bg-success">
                            <div class="col-xs-12 col-sm-6"><strong><?= __('P/U') ?>:</strong></div>
                            <div class="col-xs-12 col-sm-6 text-right"><?= moneda($bi['unity_price']) ?></div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-material-blue-grey-100">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6"><strong><?= __('Cantidad') ?>:</strong></div>
                            <div class="col-xs-12 col-sm-6 text-right"><?= moneda($bi['quantity']) ?></div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-material-blue-grey-100">
                        <div class="row bg-success">
                            <div class="col-xs-12 col-sm-6"><strong><?= __('Total') ?>:</strong></div>
                            <div class="col-xs-12 col-sm-6 text-right"><?= moneda($bi['total_price']) ?></div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6"><strong><?= __('Unidad') ?>:</strong></div>
                            <div class="col-xs-12 col-sm-6 text-right"><?= (!empty($units[$bi['unit_id']])) ? h($units[$bi['unit_id']]) : '' ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4">
                <div class="row no-margin">
                    <div class="col-xs-12 col-material-blue-grey-100">
                        <div class="row bg-success">
                            <div class="col-xs-12 col-sm-7"><strong><?= __('Gastos Materiales') ?>:</strong></div>
                            <div class="col-xs-12 col-sm-5 text-right"><?= moneda($guide_exit_total) ?></div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-material-blue-grey-100">
                        <div class="row">
                            <div class="col-xs-12 col-sm-7"><strong><?= __('Gastos Subcontratos') ?>:</strong></div>
                            <div class="col-xs-12 col-sm-5 text-right"><?= moneda($subcontract_total) ?></div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-material-blue-grey-100">
                        <div class="row bg-success">
                            <div class="col-xs-12 col-sm-7"><strong><?= __('Gastos Mano Obra') ?>:</strong></div>
                            <div class="col-xs-12 col-sm-5 text-right"><?= (!empty($budget_items_completed_tasks_totals[$bi['id']])) ? moneda($budget_items_completed_tasks_totals[$bi['id']]) : moneda(0) ?></div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-material-blue-grey-100">
                        <div class="row">
                            <div class="col-xs-12 col-sm-7"><strong><?= __('Tratos') ?>:</strong></div>
                            <div class="col-xs-12 col-sm-5 text-right"><?=  (!empty($budget_items_deals[$bi['id']])) ? moneda($budget_items_deals[$bi['id']]) : moneda(0) ?></div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-material-blue-grey-100">
                        <div class="row bg-success">
                            <div class="col-xs-12 col-sm-7"><strong><?= __('Bonos') ?>:</strong></div>
                            <div class="col-xs-12 col-sm-5 text-right"><?= (!empty($budget_items_bonuses[$bi['id']])) ? moneda($budget_items_bonuses[$bi['id']]) : moneda(0) ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-md-4 text-right">
                <div class="row">
                    <?php
                    $group_id = $this->request->session()->read('Auth.User.group_id');
                    if ($group_id == USR_GRP_COORD_PROY || $group_id == USR_GRP_GE_GRAL || $group_id == USR_GRP_GE_FINAN) : ?>
                        <div class="col-md-4">
                            <h4>Valor Objetivo:
                                <!-- <span class="target-value"><?= moneda($bi['target_value']) ?></span> -->
                            </h4>                           
                        </div>
                        <div class="col-md-8"><?php
                            $target_value = ($bi['target_value']!==null)?$bi['target_value']:0;
                            echo $this->Form->input('budgetitems.'.$bi['id'].'.target_value', [
                                'templates' => [
                                    'input' => '<input class="form-control text-right ldz_numeric" type="{{type}}" name="{{name}}" {{attrs}}>',
                                ],
                                'label' => false,
                                'div' => false,
                                'type' => 'text',
                                // 'step' => '0.01',
                                'value' => $target_value,
                                'data-class' => 'input_multiple_target_value',
                                'data-budgetitemid' => $bi['id'],
                                'data-oldvalue' => $target_value,
                                'data-total' => $bi['total_price']
                            ]);
                        ?></div>
                        <div class="col-md-12">
                                <?php
                                $target_btn = '<a href="javascript:void(0)" class="btn btn-info inline-button target-value" data-id="' . $bi['id'] . '" data-value="' . $bi['target_value'] . '" data-total="' . $bi['total_price'] . '">Valor Objetivo</a>';
                                ?>
                    <?php endif; ?>
                            <div class="btn-group-vertical" role="group">
                            <?= (!empty($target_btn_xs)) ? $target_btn_xs : ''; ?>
                            <?= (!empty($target_btn)) ? $target_btn : ''; ?>
                            <?php
                                echo $this->Html->link(__('Detalle'), ['controller' => 'BudgetItems', 'action' => 'view', $bi['id']],
                                    ['class' => 'btn btn-material-orange-900', 'data-target' => '#modal_ajax', 'data-toggle' => '#modal_ajax']);
                                if (($bi['extra'] === 0 && $state->id < 6) || ($bi['extra'] === 1 && $state->id < 6) || ($bi['extra'] === 2 && $state->id < 6)) :
                                    echo ($bi['disabled'] == 0) ?
                                        $this->Html->link(__('Deshabilitar'), ['controller' => 'Budgets', 'action' => 'disable_item', $bi['id']], ['class' => 'confirm btn btn-danger inline-button ']) :
                                        $this->Html->link(__('Habilitar'), ['controller' => 'Budgets', 'action' => 'disable_item', $bi['id']], ['class' => 'confirm btn btn-success inline-button']);
                                if ($bi['extra'] === 1 && $state->id < 6) :
                                    echo $this->Html->link(__('Configurar'), ['controller' => 'Budgets', 'action' => 'item_param', $bi['id']],
                                        ['class' => 'btn btn-primary inline-button', 'escape' => false]);
                                endif;
                                if ($bi['extra'] == '2' && $state->id < 6) :
                                    echo $this->Html->link(__('Configurar'), ['controller' => 'Budgets', 'action' => 'item_param', $bi['id']],
                                        ['class' => 'btn btn-primary inline-button', 'escape' => false]);
                                endif;
                            endif; ?>
                            </div>
                        </div>
                </div>
            </div>
        </div>
        <hr>
    </div>
<?php endif; ?>
