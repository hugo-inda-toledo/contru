<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Presupuesto'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>
<?= $this->Html->script('budgets.view.js') ?>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title"><?= $sf_building['DesArn'] ?> / <?= $sf_building['CodArn']; ?></h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <?php $group_id = $this->request->session()->read('Auth.User.group_id');
        $currency_value = (isset($currency_value))?$currency_value->id:'default';
        echo $this->Form->hidden('currency_value_id', ['value' => $currency_value]);
        $state = end($budget->budget_approvals)->budget_state;
        if ($group_id == USR_GRP_COORD_PROY || $group_id == USR_GRP_GE_GRAL || $group_id == USR_GRP_GE_FINAN) :
            if (count($budget->budget_items) > 0) :
                if ($state->id < 6 ) :
                    echo $this->Html->link(__('Agregar Adicionales'), ['action' => 'add_extra', $budget->id], ['class' => 'btn btn-sm btn-material-orange-900']);
                    echo $this->Html->link(__('Agregar Gastos no Considerados'), ['action' => 'add_expense', $budget->id], ['class' => 'btn btn-sm btn-material-orange-900']);
                elseif ($state->id < 4 ) :
                    echo $this->Html->link(__('Remover todos los Items'),
                     ['controller' => 'budget_items', 'action' => 'remove_all', $budget->id], ['id' => 'formDeleteItems','class' => 'btn btn-sm btn-danger']);
                endif;
            else :
                if (count($budget->budget_items) == 0 && $state->id == 1) :
                    echo $this->Html->link(__('Cargar excel Presupuesto'), ['action' => 'add', $budget->building->softland_id], ['class' => 'btn btn-sm btn-material-orange-900']);
                endif;
            endif;
        endif;
        if (($group_id == USR_GRP_COORD_PROY && $state->id <= 2) || ($group_id == USR_GRP_GE_GRAL && $state->id <= 2)) :
            echo $this->Html->link(__('Eliminar presupuesto'), ['action' => 'delete', $budget->id], ['id' => 'formDelete', 'class' => 'btn btn-sm btn-danger']);
        endif;
        echo $this->Html->link(__('Comentar'), ['action' => 'comment', $budget->id], ['id' => 'formComment', 'class' => 'btn btn-sm btn-material-orange-900']);
        if ((($group_id == USR_GRP_GE_GRAL) || ($group_id == USR_GRP_GE_FINAN)) && $nextState < 7 ) :
            if ($nextState == 3 || $nextState == 4) :
                $textState = __('Aprobar');
            elseif ($nextState == 6) :
                $textState = __('Finalizar');
            endif;
            echo $this->Html->link($textState, ['controller' => 'budgetApprovals', 'action' => 'change', $budget->id], ['id' => 'formState', 'class' => 'btn btn-sm btn-material-orange-900']);
        endif;
        if ($group_id == USR_GRP_COORD_PROY && $nextState == 6) :
            $textState = __('Finalizar');
            echo $this->Html->link($textState, ['controller' => 'budgetApprovals', 'action' => 'change', $budget->id], ['id' => 'formState', 'class' => 'btn btn-sm btn-material-orange-900']);
        endif; ?>
        <!-- Información General -->
        <?= $this->Element('info_budget_building'); ?>
    <?php if (!empty($budget->budget_items)) : ?>
        <div class="row">
            <div class="col-xs-12 col-md-8">
                <h3>
                    Detalle del Presupuesto: Partidas de la Obra
                </h3>
            </div>
            <div class="col-md-4">
				<a href="javascript:void(0)" class="btn btn-sm btn-link btn-flat pull-right save_all_target_value">Guardar valores objetivos</a>
                <a href="javascript:void(0)" class="btn btn-sm btn-link btn-flat pull-right toggle-open">Expandir Partidas</a>
            </div>
        </div>        
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="false">
        <?php $tag_extra = 0; ?>
            <?php foreach ($budget_items as $bi) :
                $panelType = ($bi['disabled'] == 0) ? 'material-blue-grey-700' : 'warning';
                if($bi['extra'] == 1 && $tag_extra == 0) :
                    echo '<div class="container-fluid"><h4>Partidas Adicionales</h4></div>';
                    $tag_extra = 1;
                endif;
 				//if($bi['extra'] == 0 || $bi['extra'] == 1):
                    echo $this->element('acordion_view',['bi' => $bi, 'panel_type' => $panelType, 'state' => $state]);
                //endif;
            endforeach; ?>
        </div>
    <?php endif; ?>
        <!-- Información Específica -->
        <?= $this->Element('info_budget_detail'); ?>
        <!-- Comentarios -->
        <div class="panel panel-default">
            <div class="panel-body comments">
                <h4><strong>Comentarios</strong></h4>
                <?php
                if (count($observations)) :
                    foreach ($observations as $keyobs => $observation) : ?>
                    <div <?php if($keyobs % 2 != 0):?>class="bg-success"<?php endif; ?> style="">
                        <span class="label label-default"><?= h($observation->created->format('d-m-Y H:m')) ?></span>
                        <h5><strong><?= h($observation->user->full_name . ': ') ?></strong><?= h($observation->observation) ?></h5>
                        <hr>
                    </div>
                    <?php endforeach;
                endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modales -->
<div id="modalComment" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Agregar Comentario</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(null, [
                    'url' => ['controller' => 'Budgets', 'action' => 'comment', $budget->id]
                ]); ?>
                <fieldset>
                    <?php
                        echo $this->Form->input('observation', ['type' => 'textarea', 'escape' => false]);
                    ?>
                </fieldset>
            </div>
            <div class="modal-footer">
                <?= $this->Form->button(__('Guardar')) ?>
                <?= $this->Form->end() ?>
                <button type="button" class="btn btn-flat btn-link" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<div id="modalState" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Confirmación</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(null, [
                    'url' => ['controller' => 'BudgetApprovals', 'action' => 'change', $budget->id]
                ]); ?>
                <fieldset>
                    <?php
                        echo $this->Form->label('¿Está seguro de ' . $textState . ' el presupuesto?');
                        echo $this->Form->input('budget_state_id', ['label' => 'Estado del presupuesto','type' => 'hidden', 'value' => $nextState]);
                        echo "'<br /> ";
                        echo $this->Form->label('El Presupuesto tendrá el siguiente estado: ' . @$states[$nextState]);
                        echo $this->Form->input('comment', ['label' => 'Comentario']);
                    ?>
                </fieldset>
            </div>
            <div class="modal-footer">
                <?= $this->Form->button(__('Confirmar')) ?>
                <?= $this->Form->end() ?>
                <button type="button" class="btn btn-flat btn-link" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<div id="modalDelete" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Confirmación</h4>
            </div>
            <div class="modal-body">
                ¿Está seguro que desea eliminar el presupuesto del Sistema?
            </div>
            <div class="modal-footer">
                <?= $this->Form->postLink('Confirmar', ['action' => 'delete', $budget->id], ['class' => 'btn btn-material-orange-900']) ?>
                <button type="button" class="btn btn-flat btn-link" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<div id="modalDeleteItems" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Confirmación</h4>
            </div>
            <div class="modal-body">
                ¿Está seguro que desea eliminar las partidas al Presupuesto?
            </div>
            <div class="modal-footer">
                <?= $this->Html->link(__('Confirmar'), ['controller' => 'budget_items', 'action' => 'remove_all', $budget->id], ['class' => 'btn btn-material-orange-900']); ?>
                <button type="button" class="btn btn-flat btn-link" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->Element('modal_ajax'); ?>
