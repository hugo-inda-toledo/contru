<?=
// elementos estandares de la vista
$this->assign('title_text', __('Módulo Recursos Humanos'));
$this->assign('title_icon', 'groups');
$buttons = array();
// $buttons[] = ['title' => __('Todos los perfiles'), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/index'];
$this->set('buttons', $buttons);
?>

<script>
$(document).ready(function(){
    $( "#formComment" ).click(function( event ) {
        event.preventDefault();
        $('#modalComment').modal('show');
    });
    //cambiamos el atributo onclick de los postlink con la clase approve
    //guardamos el nombre del form en un nuevo atributo data-form
    $('a.confirm_reason').each(function(index, el) {
        var btn_onclick = $(this).attr('onclick').split('.');
        $(this).attr('data-form', btn_onclick[1]);
        $(this).attr('onclick', false);
    });
    $(document).on('click', '.confirm_reason', function(event) {
        var _this = $(this);
        event.preventDefault();
        bootbox.confirm({
            title: "Confirme por favor",
            message: '<p>¿Está seguro que desea cambiar el estado de este registro?</p>' +
                '<div class="row"><div class="col-xs-12 col-md-12">'+
                    '<label for="'+_this.attr('data-form')+'_comment" class="col-md-12 col-xs-12">Favor indicar razón de rechazo</label>'+
                    '<textarea id="'+_this.attr('data-form')+'_comment" name="comment" class="col-md-12 col-xs-12" style="resize: none;"></textarea>'+
                '</div></div>',
            buttons: {
                confirm: {
                    label: "Aceptar",
                    className: "btn btn-material-orange-900",
                },
                cancel: {
                    label: "Cancelar",
                    className: "btn btn-flat btn-link",
                }
            },
            callback: function(result){
                if(result) {
                    inputComment='<input name="comment" type="hidden" value="'+$('#'+_this.attr('data-form')+'_comment').val()+'">';
                    $('form[name="' + _this.attr('data-form') + '"').append(inputComment);
                    $('form[name="' + _this.attr('data-form') + '"').submit();
                    // _this.closest('form').submit();
                }
            }
        });
    });
});
</script>
<div class="panel panel-material-blue-grey-700">
    <!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Ver Detalles Trato</h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <?= $this->Element('info_budget_building'); ?>
        <div class="row">
            <div class="col-md-6 col-sm-6">
                <h3>Información Trato</h3>
                <blockquote>
                    <h5><strong><?= 'Estado del trato: '; ?></strong><?= $deal->state; ?>
                    <h5><strong><?= 'Fecha de Inicio: '; ?></strong><?= $deal->start_date->format('d-m-Y'); ?>
                    <h5><strong><?= 'Descripción: '; ?></strong><?= $deal->description; ?>
                </blockquote>
                <?php
                $group = $this->request->session()->read('Auth.User.group_id');
                //flujo stados
                if($group == USR_GRP_ADMIN_OBRA) :
                    if($deal->state == $states[0]) :
                        echo $this->Form->postLink(__('Aprobar'), ['action' => 'change_state', $deal->id], ['class' => 'btn btn-sm btn-material-orange-900 approve', 'data' => ['state' => $states[1]]]);
                        echo $this->Form->postLink(__('Rechazar'), ['action' => 'change_state', $deal->id], ['class' => 'btn btn-sm btn-danger confirm', 'data' => ['state' => $states[4]]]);
                    endif;
                endif;
                if($group == USR_GRP_VISITADOR) :
                    if($deal->state == $states[1]) :
                        echo $this->Form->postLink(__('Aprobar'), ['action' => 'change_state', $deal->id], ['class' => 'btn btn-sm btn-material-orange-900 approve', 'data' => ['state' => $states[2]]]);
                        echo $this->Form->postLink(__('Rechazar'), ['action' => 'change_state', $deal->id], ['class' => 'btn btn-sm btn-danger confirm', 'data' => ['state' => $states[4]]]);
                    endif;
                endif;
                if(in_array($group, array(USR_GRP_GE_FINAN, USR_GRP_JEFE_RRHH, USR_GRP_JEFE_RRHH, USR_GRP_GE_GRAL)) && in_array($deal->state ,array($states[0], $states[1], $states[2]))) :
                    echo $this->Form->postLink(__('Aprobar'), ['action' => 'change_state', $deal->id], ['class' => 'btn btn-sm btn-material-orange-900 approve', 'data' => ['state' => $states[3]]]);
                    echo $this->Form->postLink(__('Rechazar'), ['action' => 'change_state', $deal->id], ['class' => 'btn btn-sm btn-danger confirm_reason', 'data' => ['state' => $states[4]]]);
                endif;
                if(in_array($group, array(USR_GRP_GE_FINAN, USR_GRP_JEFE_RRHH, USR_GRP_ADMIN_OBRA, USR_GRP_GE_GRAL)) && in_array($deal->state ,array($states[3]))) :
                    echo $this->Form->postLink(__('Finalizar'), ['action' => 'change_state', $deal->id], ['class' => 'btn btn-sm btn-material-orange-900 confirm', 'data' => ['state' => $states[5]]]);
                endif; ?>
            </div>
        </div>
        <h4>Trabajadores Seleccionados</h4>
        <table id='workerstable'>
             <tr>
                <th><?= h('Rut'); ?> </th>
                <th><?= h('Nombre Trabajador'); ?> </th>
                <th><?= h('Cargo'); ?> </th>
                <th><?= h('Valor'); ?> </th>
                <th><?= h('Partidas'); ?> </th>
            </tr>
            <tbody>
            <?php foreach($deals as $de) : ?>
                <?php $trabajador = $fichas[array_search($de->worker->softland_id, array_column($fichas, 'ficha'))]; ?>
                <tr <?= 'id="trWorker-' . $de->worker_id . '"'; ?> >
                    <td><?= $trabajador['rut']; ?> </td>
                    <td><?= $trabajador['nombres']; ?> </td>
                    <td><?= $trabajador['Cargo']['nombre_cargo']; ?> </td>
                    <td>$ <?= moneda($de->amount); ?> </td>
                    <td <?= 'id="tdWorker-' . $de->worker_id . '-items"'; ?> >
                    <?php
                    if (!empty($de->deal_details)) :
                        foreach ($de->deal_details as $k => $da) : ?>
                            <div id="<?php echo 'div-worker' . $da['id'] . '-item' . $da->budget_item->id ?>" class="workerItems" workerid="<?= $da['id'] ?>" itemid="<?= $da->budget_items_id?>">
                                <?= $this->Html->link('P: '. $da->budget_item->item . ' / ' . $da->percentage . '%',
                                    ['controller' => 'BudgetItems', 'action' => 'view', $da->budget_item->id], ['data-target' => '#modal_ajax', 'data-toggle' => '#modal_ajax']); ?>
                            </div>
                        <?php
                        endforeach;
                    endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <!-- Comentarios -->
        <div class="panel panel-default">
            <div class="panel-body comments">
                <?php
                $valid_group = array(USR_GRP_COORD_PROY, USR_GRP_GE_GRAL, USR_GRP_GE_FINAN, USR_GRP_JEFE_RRHH, USR_GRP_VISITADOR, USR_GRP_ADMIN_OBRA);
                if (!empty($group)) :
                    if (in_array($group, $valid_group)) :
                        echo $this->Html->link(__('Comentar'), ['action' => 'comment', $deal->id], ['id' => 'formComment', 'class' => 'btn btn-sm pull-right btn-material-orange-900']);
                    endif;
                endif; ?>
                <h4>Comentarios</h4>
                <?php
                if (count($observations)) :
                    foreach ($observations as $o): ?>
                        <span class="label label-default"><?= h($o->created->format('d-m-Y H:m')) ?></span>
                        <h5><strong><?= h($o->user->full_name . ': ') ?></strong><?= h($o->observation) ?></h5>
                        <hr>
                    <?php
                    endforeach;
                endif; ?>
            </div>
        </div>
        <?= $this->Html->link('Volver', ['controller' => 'Deals', 'action' => 'index'], ['class' => 'btn btn-flat btn-link']); ?>
    </div>
</div>
<div id="modalComment" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Agregar Comentario</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(null, [
                    'url' => ['controller' => 'Deals', 'action' => 'comment', $deal->id]
                ]); ?>
                <fieldset>
                    <?php
                        echo $this->Form->input('observation', ['type' => 'textarea', 'escape' => false, 'label' => 'Ingresar comentario']);
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
<?= $this->Element('modal_ajax'); ?>