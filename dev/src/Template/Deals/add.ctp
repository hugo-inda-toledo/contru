<?=
// elementos estandares de la vista
$this->assign('title_text', __('Módulo Recursos Humanos'));
$this->assign('title_icon', 'groups');
$buttons = array();
// $buttons[] = ['title' => __('Todos los perfiles'), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/index'];
$this->set('buttons', $buttons);
?>
<script type="text/javascript">
    var fichas = <?= json_encode($fichas); ?>;
    var charges = <?= json_encode($charges); ?>;
</script>
<?= $this->Html->script('deals.add.js') ?>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Agregar Trato</h3>
    </div>
    <div class="panel-body">
        <?php $currency_value = (isset($currency_value))?$currency_value->id:'default';
        echo $this->Form->hidden('currency_value_id', ['value' => $currency_value]); ?>
        <!-- Panel content -->
        <?= $this->Form->create($deal, ['class' => 'form_submit']); ?>
        <div class="row">
            <div class="col-md-6 col-sm-6">
                <?php
                    echo $this->Form->input('start_date', ['type' => 'text', 'data-type' => 'datetimepicker', 'label' => 'Fecha de Trato', 'autocomplete' => 'off']);
                    echo $this->Form->input('description', ['label'=>'Descripción']);
                    echo $this->Html->link(__('Seleccionar Trabajadores'), ['#'], ['id' => 'formWorker', 'class' => 'btn btn-sm btn-material-orange-900']);
                ?>
            </div>
            <div class="col-md-6 col-sm-6">
                <!-- Información General -->
                <?= $this->Element('info_budget_building'); ?>
            </div>
        </div>
        <table id='workerstable'>
            <thead>
                 <tr>
                    <th></th>
                    <th><?= h('Rut'); ?> </th>
                    <th><?= h('Nombre Trabajador'); ?> </th>
                    <th><?= h('Cargo'); ?> </th>
                    <th><?= h('Monto'); ?> </th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <p class="text-danger error-message" style="display: none;"></p>
        <?php echo $this->Form->button(__('Guardar'), ['type' => 'submit']) ?>
        <button id="btnModalCancelar" type="button" class="btn btn-flat btn-link" data-dismiss="modal">Cancelar</button>
        <?= $this->Form->end() ?>
    </div>
</div>

<div id="modalDate" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Confirmar dia feriado.</h4>
            </div>
            <div class="modal-body">
                <?php echo 'Por favor confirme que el dia seleccionado es feriado.'?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-material-orange-900" data-dismiss="modal">Confirmar</button>
                <button id="nonfestive" type="button" class="btn btn-flat btn-link" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
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
                    <?php echo "no se encontraron trabajadores disponibles para esta obra."; ?>
                </div>
                <?php echo $this->Form->input('worker_id', ['label' => 'Trabajadores', 'options' => $workers, 'multiple' => 'multiple']); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-material-orange-900" data-dismiss="modal">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<div id="modalCancel" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Confirmación</h4>
            </div>
            <div class="modal-body">
                <?php echo "¿ Esta seguro que desea cancelar ?"; ?>
            </div>
            <div class="modal-footer">
                <?= $this->Html->link('Confirmar', ['action' => 'index'], ['class' => 'btn btn-material-orange-900']); ?>
                <button type="button" class="btn btn-flat btn-link" data-dismiss="modal">Cerrar</button>
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
                <?php echo '¿ Esta seguro que desea eliminar al trabajador del trato? '; ?>
            </div>
            <div class="modal-footer">
                <button id="workerRemove" type="button" class="btn btn-material-orange-900">Aceptar</button>
                <button type="button" class="btn-flat btn-link" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>