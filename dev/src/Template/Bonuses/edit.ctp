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
<?= $this->Html->script('bonuses.edit.js') ?>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Editar Bono</h3>
    </div>
    <div class="panel-body">
        <?= $this->Element('info_budget_building'); ?>
        <!-- Panel content -->
        <?= $this->Form->create($bonus); ?>
        <div class="row">
            <div class="col-md-6 col-sm-6">
                <?=$this->Form->input('start_date', ['type' => 'text', 'data-type' => 'datetimepicker', 'label' => 'Fecha de Bono', 'value' => $bonus->start_date->format('d-m-Y')]);?>
                <blockquote>
                    <h5><?= '<strong>Descripción: </strong>' .  $bonus->description;  ?></h5>
                    <?php //echo $this->Html->link(__('Seleccionar Trabajadores'), ['#'], ['id' => 'formWorker', 'class' => 'btn btn-sm btn-material-orange-900']); ?>
                </blockquote>
            </div>
        </div>
        <h4>Trabajadores Asociados</h4>
        <table id='workerstable'>
             <tr>
                <th></th>
                <th><?= h('Rut'); ?> </th>
                <th><?= h('Nombre Trabajador'); ?> </th>
                <th><?= h('Cargo'); ?> </th>
                <th><?= h('Monto'); ?> </th>
            </tr>
            <tbody>



                <?php if(!empty($fichas)){
                    $c=0;
                    foreach($fichas AS $ficha){
                        $c++;
                        $amount = (isset($ficha['amount']))?$ficha['amount']:'';
                        $max_bonus = isset($charges[$ficha['Cargo']['cod_cargo']])?$charges[$ficha['Cargo']['cod_cargo']]:0;
                        ?>
                        <tr>
                            <td><?=$c;?></td>
                            <td><?=$ficha['rut'];?></td>
                            <td><?=$ficha['nombres'];?></td>
                            <td><?=$ficha['Cargo']['nombre_cargo'];?></td>
                            <td>
                                <?php
                                if(isset($ficha['id'])){
                                    // echo $this->Form->input('workers[' . $ficha['ficha'] . '][worker_id]', ['label' => false, 'value' => $ficha['worker_id'], 'type' => 'hidden']);
                                }
                                if(isset($ficha['bonus_id'])){
                                    echo $this->Form->input('workers[' . $ficha['ficha'] . '][id]', ['label' => false, 'value' => $ficha['bonus_id'], 'type' => 'hidden']);
                                }
                                ?>
                                <input class="form-control" type="hidden" name="workers[<?=$ficha['ficha'];?>][worker_id]" id="state" value="<?=$ficha['worker_id'];?>">
                                <input class="form-control ldz_numeric text-right" type="text" name="workers[<?=$ficha['ficha'];?>][amount]" required="required" id="amount-<?=$ficha['ficha'];?>" max="<?=$max_bonus;?>" value="<?=$amount;?>">
                            </td>
                        </tr>
                        <?php
                    }
                } ?>


                <?php /* $j = 0; ?>
                <?php $i = 0; ?>
                <?php foreach ($bonuses as $de) : ?>
                    <?php $trabajador = $fichas[array_search($de->worker->softland_id, array_column($fichas, 'ficha'))]; ?>
                    <tr <?= 'id="trWorker-' . $de->worker_id . '" worker="' . $de->worker->softland_id . '" workername="' . $trabajador['nombres'] . '"'; ?> >
                        <td><button type="button" class="removebutton" title="Quitar trabajador del bono">X</button></td>
                        <td><?= $trabajador['rut']; ?> </td>
                        <td><?= $trabajador['nombres']; ?> </td>
                        <td><?= $trabajador['Cargo']['nombre_cargo']; ?> </td>
                        <td>
                            <?php
                            $max_bonus = (isset($charges[$trabajador['Cargo']['cod_cargo']])) ? $charges[$trabajador['Cargo']['cod_cargo']] : null;
                            echo $this->Form->input('workers[' . $j . '][id]', ['label' => false, 'value' => $de->id, 'type' => 'hidden']);
                            echo $this->Form->input('workers[' . $j . '][worker_id]', ['label' => false, 'value' => $de->worker_id, 'type' => 'hidden']);
                            echo $this->Form->input('workers[' . $j . '][amount]', [
                                'templates' => [
                                    'input' => '<input class="form-control ldz_numeric text-right" type="{{type}}" name="{{name}}" {{attrs}}>',
                                ],
                                'label' => false,
                                'type' => 'text',
                                'max' => $max_bonus,
                                'value' => $de->amount
                            ]);
                            ?>
                        </td>
                    </tr>
                    <?php $j++; ?>
                <?php endforeach; */?>
            </tbody>
        </table>
        <?= $this->Form->button(__('Guardar'), ['data-send-form' => 'true']) ?>
        <?= $this->Html->link(
            'Cancelar',
            ['controller' => 'bonuses', 'action' => 'index'],
            ['class' => 'btn btn-flat btn-link']
        ); ?>
        <?= $this->Form->end() ?>
    </div>
</div>

<?php /*<div id="modalWorkers" class="modal">
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
                <?php echo $this->Form->input('worker_id', ['options' => $workers, 'multiple' => true,'class' => 'select2','label' => 'Seleccionar Trabajadores']); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-flat btn-link" data-dismiss="modal">Confirmar</button>
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
                <?php echo $this->Form->input('worker_id', ['type' => 'hidden']); ?>
                <?php echo $this->Form->input('budgetItems_id', ['options' => $budgetItems, 'label' => 'Partida','class' => "form-control floating-label", 'placeholder' => 'Seleccione una partida','data-hint' => "Seleccione una partida" ]); ?>
                <?php echo $this->Form->input('percent', ['label' => 'Porcentaje valor trato','type' => 'number','class' => "form-control floating-label", 'placeholder' => "El porcentaje no puede ser mayor a 100",'data-hint' => "El porcentaje no puede ser mayor a 100" ]); ?>


            </div>
            <div class="modal-footer">
                <button id="budgetItemAdd" type="button" class="btn btn-material-orange-900">Agregar</button>
                <button type="button" class="btn btn-flat btn-link" data-dismiss="modal">Cerrar</button>
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
                <?php echo 'esta seguro que desea eliminar esta partida asignada al trabajador? '; ?>
            </div>
            <div class="modal-footer">
                <button id="budgetItemRemove" type="button" class="btn btn-material-orange-900" data-dismiss="modal">Aceptar</button>
                <button type="button" class="btn btn-flat btn-link" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

*/ ?>
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
<?= $this->Element('modal_ajax'); ?>