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
<?= $this->Html->script('bonuses.add.js') ?>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Agregar Bono</h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <?= $this->Element('info_budget_building'); ?>
        <?= $this->Form->create($bonus); ?>
        <div class="row">
            <div class="col-md-6 col-sm-6"><?php
                echo $this->Form->input('start_date', ['type' => 'text', 'data-type' => 'datetimepicker', 'label' => 'Fecha del Bono']);
                // echo $this->Html->link(__('Seleccionar Trabajadores'), ['#'], ['id' => 'formWorker', 'class' => 'btn btn-sm btn-material-orange-900']);
            ?></div>
            <div class="col-md-6 col-sm-6">
                <?=$this->Form->input('description', ['label' => 'Descripción']);?>
            </div>
        </div>
        <h3>Trabajadores</h3>
        <table class="table table-striped table-hover table-item">
            <col width="2%">
            <col width="10%">
            <col width="30%">
            <col width="10%">
            <col width="20%">
            <thead>
                 <tr>
                    <th>°</th>
                    <th><?= h('Rut'); ?> </th>
                    <th><?= h('Nombre Trabajador'); ?> </th>
                    <th><?= h('Cargo'); ?> </th>
                    <th><?= h('Monto'); ?> </th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($fichas)){
                    $c=0;
                    foreach($fichas AS $ficha){
                        $c++;
                        $max_bonus = isset($charges[$ficha['Cargo']['cod_cargo']])?$charges[$ficha['Cargo']['cod_cargo']]:0;
                        ?>
                        <tr>
                            <td><?=$c;?></td>
                            <td><?=$ficha['rut'];?></td>
                            <td><?=$ficha['nombres'];?></td>
                            <td><?=$ficha['Cargo']['nombre_cargo'];?></td>
                            <td>
                                <input class="form-control" type="hidden" name="workers[<?=$ficha['ficha'];?>][id]" id="state" value="<?=$ficha['ficha'];?>">
                                <input class="form-control ldz_numeric text-right" type="text" name="workers[<?=$ficha['ficha'];?>][amount]" required="required" id="amount-<?=$ficha['ficha'];?>" max="<?=$max_bonus;?>">
                            </td>
                        </tr>
                        <?php
                    }
                } ?>
            </tbody>
        </table>

        <?= $this->Form->button(__('Guardar'), ['data-send-form' => 'true']) ?>
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
                <?php echo $this->Form->input('worker_id', ['options' => $workers, 'multiple' => 'multiple', 'label' => 'Seleccionar Trabajadores']); ?>
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
                <?= $this->Html->link(
                    'Confirmar',
                    ['controller' => 'bonuses', 'action' => 'index'],
                    ['class' => 'btn btn-material-orange-900']
                ); ?>
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
                <h4 class="modal-title">Eliminar a trabajador del bono</h4>
            </div>
            <div class="modal-body">
                <?php echo '¿ Esta seguro que desea eliminar al trabajador del bono? '; ?>
            </div>
            <div class="modal-footer">
                <button id="workerRemove" type="button" class="btn btn-material-orange-900">Aceptar</button>
                <button type="button" class="btn-flat btn-link" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>