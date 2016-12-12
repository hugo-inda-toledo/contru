<?=
// elementos estandares de la vista
$this->assign('title_text', __('Editar Trato'));
$this->assign('title_icon', 'groups');
$buttons = array();
// $buttons[] = ['title' => __('Todos los perfiles'), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/index'];
$this->set('buttons', $buttons);
?>
<script type="text/javascript">
    var fichas = <?= json_encode($fichas); ?>;
    var charges = <?= json_encode($charges); ?>;
</script>
<?= $this->Html->script('deals.edit.js') ?>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Editar Trato</h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <?= $this->Form->create($deal, ['class' => 'form_submit']); ?>
        <div class="row">
            <div class="col-md-4 col-sm-4">
                <?php
                    echo $this->Form->input('start_date', ['type' => 'text', 'data-type' => 'datetimepicker', 'label' => 'Fecha de Trato', 'value' => $deal->start_date->format('d-m-Y')]);
                    echo $this->Form->input('description', ['label'=>'Descripción']);
                    echo $this->Html->link(__('Seleccionar Trabajadores'), ['#'], ['id' => 'formWorker', 'class' => 'btn btn-sm btn-material-orange-900']);
                ?>
            </div>
            <div class="col-md-8 col-sm-8">
                <?= $this->Element('info_budget_building'); ?>
            </div>
        </div>
        <h4>Trabajadores Asociados</h4>
        <table id='workerstable'>
            <thead>
                <tr>
                    <th></th>
                    <th><?= h('Rut'); ?> </th>
                    <th><?= h('Nombre Trabajador'); ?> </th>
                    <th><?= h('Cargo'); ?> </th>
                    <th><?= h('Monto'); ?> </th>
                    <th><?= h('Partidas'); ?> </th>
                </tr>
            </thead>
            <tbody>
            <?php $j = 0; ?>
            <?php $i = 0; ?>
            <?php foreach($deals as $de) : ?>
                <?php $trabajador = $fichas[array_search($de->worker->softland_id, array_column($fichas, 'ficha'))]; ?>
                <tr <?= 'id="trWorker-' . $de->worker_id . '" worker="' . $de->worker->softland_id . '" workername="' . $trabajador['nombres'] . '"'; ?> >
                    <td><button type="button" class="removebutton" title="Quitar trabajador del trato">X</button></td>
                    <td><?= $trabajador['rut']; ?> </td>
                    <td><?= $trabajador['nombres']; ?> </td>
                    <td><?= $trabajador['Cargo']['nombre_cargo']; ?> </td>
                    <td>
                    <?php $max_deal = (isset($charges[$trabajador['Cargo']['cod_cargo']])) ? $charges[$trabajador['Cargo']['cod_cargo']] : null; ?>
                    <?php echo $this->Form->input('workers[' . $j . '][id]', ['label' => false, 'value' => $de->id, 'type' => 'hidden']); ?>
                    <?php echo $this->Form->input('workers[' . $j . '][worker_id]', ['label' => false, 'value' => $de->worker_id, 'type' => 'hidden']); ?>
                    <?php echo $this->Form->input('workers[' . $j . '][amount]', [
                        'templates' => [
                            'input' => '<input class="form-control ldz_numeric text-right" type="{{type}}" name="{{name}}" {{attrs}}>',
                        ],
                        'label' => false,
                        'type' => 'text',
                        'max' => $max_deal,
                        'value' => $de->amount
                    ]); ?></td>
                    <td <?= 'id="tdWorker-' . $de->worker_id . '-items"'; ?> >
                    <?php echo $this->Html->link(__('Agregar'), ['#formItems'], ['id' => 'formItems', 'class' => 'formItems btn btn-sm btn-material-orange-900', 'data-worker-id' => $de->worker_id]); ?>
                    <?php if(!empty($de->deal_details)) : ?>
                        <?php $i = 0; ?>
                        <?php foreach($de->deal_details as $k => $da) : ?>
                            <div id="<?= 'div-worker' . $de->worker_id . '-item' . $da->budget_items_id ?>" class="workerItems" workerid="<?= $de->worker_id?>" itemid="<?= $da->budget_items_id?>">
                                <input class="form-control" type="hidden" name="<?= 'workers[' . $j . '][partidas][' . $i . '][itemId]' ?>" value="<?= $da->budget_item_id ?>">
                                <input class="form-control" type="hidden" name="<?= 'workers[' . $j . '][partidas][' . $i . '][itemPercent]' ?>" value="<?= $da->percentage?>">
                                <input class="form-control" type="hidden" name="<?= 'workers[' . $j . '][partidas][' . $i . '][itemOnly]' ?>" value="<?= $da->budget_item->item  . ' ' . $da->budget_item->description ?>">
                                <?= $this->Html->link('P: '. $da->budget_item->item . ' / ' . $da->percentage . '%',
                                    ['controller' => 'BudgetItems', 'action' => 'view', $da->budget_item->id],
                                    ['id' => 'worker-' . $de->worker_id . 'item-'. $da->budget_item_id, 'data-target' => '#modal_ajax', 'data-toggle' => '#modal_ajax']); ?>
                            </div>
                            <?php $i++; ?>
                        <?php endforeach ?>
                    <?php endif ?>
                    </td>
                </tr>
                <?php $j++; ?>
            <?php endforeach;?>
            </tbody>
        </table>
        <p class="text-danger error-message" style="display: none;"></p>
        <?php echo $this->Form->button(__('Guardar'), ['type' => 'submit']) ?>
        <?= $this->Html->link('Cancelar', ['action' => 'index'], ['class' => 'btn btn-flat btn-link']); ?>
        <?= $this->Form->end() ?>
    </div>
</div>

<div id="modalWorkers" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Listado de Trabajadores</h4>
            </div>
            <div class="modal-body">
            <div id="noworkers">
                <?php echo "No se encontraron trabajadores disponibles para esta Obra."; ?>
            </div>
                <?php echo $this->Form->input('worker_id', ['options' => $workers, 'multiple' => true,'class' => 'select2','label' => 'Trabajador']); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-material-orange-900" data-dismiss="modal">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<div id="modalItems" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Listado de Partidas</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->input('worker_id', ['type' => 'hidden']); ?>
                <?= $this->Form->input('budgetItems_id', ['options' => $budgetItems, 'label' => 'Partida','class' => "form-control floating-label", 'placeholder' => 'Seleccione una partida','data-hint' => "Seleccione una partida" ]); ?>
                <?= $this->Form->input('percent', ['label' => 'Porcentaje valor trato','type' => 'number','class' => "form-control floating-label", 'placeholder' => "El porcentaje no puede ser mayor a 100",'data-hint' => "El porcentaje no puede ser mayor a 100" ]); ?>

            </div>
            <div class="modal-footer">
                <button id="budgetItemAdd" type="button" class="btn btn-material-orange-900">Agregar</button>
                <button type="button" class="btn-flat btn-link" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div id="modalRemoveItem" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Eliminar partida a Trabajador</h4>
            </div>
            <div class="modal-body">
                <?php echo '¿Está seguro que desea eliminar esta partida asignada al Trabajador?'; ?>
            </div>
            <div class="modal-footer">
                <button id="budgetItemRemove" type="button" class="btn btn-material-orange-900">Aceptar</button>
                <button type="button" class="btn-flat btn-link" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div id="modalRemoveWorker" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Eliminar a trabajador del trato</h4>
            </div>
            <div class="modal-body">
                <?php echo '¿Está seguro que desea eliminar al trabajador del Trato? '; ?>
            </div>
            <div class="modal-footer">
                <button id="workerRemove" type="button" class="btn btn-material-orange-900">Aceptar</button>
                <button type="button" class="btn-flat btn-link" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<?= $this->Element('modal_ajax'); ?>