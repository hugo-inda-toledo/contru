<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo Avance de Obra'));
$this->assign('title_icon', 'groups');
$buttons = array();
$this->set('buttons', $buttons);
$theSign = trim(getSignByCurrencyId($budget->currencies_values{0}->currency->id));
?>
<style>
table.table-progress input{
    text-align: right;
}
</style>

<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Ingresar Avance de Obra</h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <?= $this->Element('info_budget_building'); ?>
        <?php $group_id = $this->request->session()->read('Auth.User.group_id');
        if (!$approvals->isEmpty()) :
            echo '<div class="well well-sm">';
            foreach ($approvals as $approval) :
                echo '<p class="text-success">' . $approval['comment'] . ' | Fecha: ' . $approval['created']->nice() . '</p>';
            endforeach;
            echo '</div>';
            if (!$rejects->isEmpty()) :
                echo '<div class="shadow-z-1">';
                foreach ($rejects as $reject) :
                    echo '<p class="text-danger">Rechazado: ' . $reject['comment'] . ' | Fecha: ' . $reject['created']->nice() . ' | '.
                     $reject->group->name . ': ' . $reject->user->first_name . ' ' . $reject->user->lastname_f . '</p>';
                endforeach;
                echo '</div>';
            endif;
        elseif (!$rejects->isEmpty()) :
            echo '<div class="well well-sm">';
            foreach ($rejects as $reject) :
                echo '<p class="text-danger">Rechazado: ' . $reject['comment'] . ' | Fecha: ' . $reject['created']->nice() . ' | ' .
                $reject->group->name . ': ' . $reject->user->first_name . ' ' . $reject->user->lastname_f . '</p>';
            endforeach;
            echo '</div>';
        endif; ?>
        <?= $this->Form->create($schedule); ?>
            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <?= $this->Form->input('Schedules.comment', ['type' => 'textarea' , 'label' => 'Agregar Comentario', 'placeholder' => 'Ingrese un comentario sobre el avance...']); ?>
                </div>
                <div class="col-md-6 col-sm-6"></div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <h3>Detalle Partidas de Planificiación</h3>
                    <table class="table table-hover table-item table-progress">
                        <thead>
                            <tr>
                                <th>Ítem</th>
                                <th class="text-left">Descripción</th>
                                <th class="text-left">Unidad</th>
                                <th class="text-right">Cantidad</th>
                                <th class="text-right"><?= __('Precio Unitario') ." ($theSign)"; ?></th>
                                <th class="text-right"><?= __('Precio Total')." ($theSign)"; ?> </th>
                                <th class="text-right">Avance Real Anterior %</th>
                                <th class="text-right">Avance Proyectado %</th>
                                <th class="text-right">Avance Proyectado [<?=$theSign; ?>]</th>
                                <th class="text-right">Avance Real [%]</th>
                                <th class="text-right">Avance Real [UNIDAD]</th>
                                <th class="text-right">Avance Real [<?=$theSign; ?>]</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedule->budget_items as $bi): ?>
                                <?php if($bi['extra'] != 2): ?>
                                    <?php echo $this->element('budget_items_progress',['bi' => $bi,'schedule_id' => $schedule->id]) ?>
                                <?php endif; ?>
                            <?php endforeach ?>
                            <tr class="totales">
                                <td></td>
                                <td><b>Total</b></td>
                                <td class="text-left"></td>
                                <td class="text-right"></td>
                                <td class="text-right"></td>
                                <td class="text-right"></td>
                                <td class="text-right"></td>
                                <td class="text-right"></td>
                                <td class="text-right">
                                    <span class="suma_proyectado ldz_numeric_no_sign"></span>
                                </td>
                                <td class="text-right"></td>
                                <td class="text-right">
                                    <span class="ldz_numeric_no_sign"></span>
                                </td>
                                <td class="text-right">
                                    <span class="suma_real_monto ldz_numeric_no_sign"></span>
                                </td>
                            </tr>
                        </tbody>
                     </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <h3>Partidas no Planificadas</h3>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <?php if(!empty($items)){ ?>
                                <select class="form-control" id="items"><?php
                                    echo '<option value="">Seleccione...</option>';
                                    foreach($items AS $item){
                                        if($item['unit_id']!="" && $item['quantity']!="" && $item['unity_price']!="" && $item['percentage_overall_progress']!=100){
                                            $unit=(!empty($units[$item['unit_id']])) ? h($units[$item['unit_id']]):"";
                                            $overall_progress=(isset($item['percentage_overall_progress']) && $item['percentage_overall_progress']!=0) ? $item['percentage_overall_progress']:0;
                                            echo '<option value="'.$item['id'].'" data-unit="'.$unit.'" data-quantity="'.$item['quantity'].'" data-unity-price="'.moneda($item['unity_price']).'" data-total-price="'.$item['total_price'].'" data-item="'.$item['item'].'" data-description="'.$item['description'].'" data-max="'.$item['quantity'].'" data-overall-progress="'.$overall_progress.'">'.
                                                $item['item'].' - '.$item['description'].
                                            '</option>';
                                        }
                                    }
                                ?></select>
                            <?php } ?>
                            <span class="input-group-btn">
                                <?=$this->Html->link('Agregar', '#', ['class' => 'add_no_scheduled btn btn-raised btn-primary']);?>
                            </span>
                        </div>
                    </div>
                    <table class="table table-hover table-item hide">
                        <thead>
                            <tr>
                                <th>Ítem</th>
                                <th>Descripción</th>
                                <th>Unidad</th>
                                <th>Cantidad</th>
                                <th class="text-center">Avance Proyectado %</th>
                                <th class="text-center">Avance Real [%]</th>
                                <th class="text-center">Avance Real [UNIDAD]</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <?= $this->Form->button(__('Guardar')) ?>
            <?= $this->Html->link(__('Cancelar'), ['action' => 'index', $budget_id], ['class' => 'btn btn-flat btn-link']) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
<?= $this->Html->script('schedules.progress'); ?>
