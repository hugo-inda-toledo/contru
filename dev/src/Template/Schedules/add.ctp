<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo Planificación'));
$this->assign('title_icon', 'groups');
$buttons = array();
$this->set('buttons', $buttons);
$theSign = trim(getSignByCurrencyId($budget->currencies_values{0}->currency->id));
?>
<style>
table.dataTable.table-condensed>thead>tr>th {
    padding-right: 0;

}
table.dataTable input{
    text-align: right;
}
</style>

<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Agregar Planificación Semanal (Lunes - Viernes)</h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <?= $this->Element('info_budget_building'); ?>
        <?= $this->Form->create($schedule); ?>
        <fieldset>
            <div class="col-md-6 col-sm-6">
                <?php
                    echo $this->Form->input('name', ['label' => 'Nombre']);
                    echo $this->Form->input('description', ['label' => 'Descripción']);
                    echo $this->Form->hidden('budget_id', ['value' => $budget_id]);
                ?>
            </div>
            <div class="col-md-6 col-sm-6">
                <?php
                    echo $this->Form->input('start_date', ['type' => 'text', 'data-type' => 'datetimepicker', 'label' => 'Fecha de Inicio', 'value' => '']);
                    echo $this->Form->input('holidays_week_quantity', ['label' => 'Cantidad días feriados semana', 'min' => 0, 'max' => 5, 'value' => (isset($this->request->data['holidays_week_quantity'])?$this->request->data['holidays_week_quantity']:0)]);
                ?>
            </div>
        </fieldset>
        <h3>Detalle Partidas de Presupuesto <button type="button" class="btn btn-right btn-default btn-raised btn-sm mb-10" id="show-all">Mostrar todos</button></h3>
        <table class="table table-condensed table-hover table-item">
            <col width="2%">
            <col width="20%">
            <col width="2%">
            <col width="4%">
            <col width="7%">
            <col width="5%">
            <col width="10%">
            <col width="7%">
            <col width="5%">
            <col width="5%">
            <col width="10%">
            <col width="5%">
            <col width="5%">
            <thead id="header_budget">
                <tr>
                    <th></th>
                    <th>Descripción</th>
                    <th class="text-left"><?= __('Unidad') ?></th>
                    <th class="text-right"><?= __('Cantidad') ?></th>
                    <th class="text-right"><?= __('Precio Unitario') ." ($theSign)"; ?></th>
                    <th class="text-right"><?= __('Precio Total')." ($theSign)"; ?> </th>
                    <th class="text-right">Avance Proyectado Anterior [%]</th>
                    <th class="text-right">Avance Proyectado [%]</th>
                    <th class="text-right">Avance Proyectado [UNIDAD]</th>
                    <th class="text-right">Avance Proyectado [<?=$theSign;?>]</th>
                    <th class="text-right">Avance Real Anterior [%]</th>
                    <th class="text-right">Avance Real [UNIDAD]</th>
                    <th class="text-right">Avance Real Anterior [<?=$theSign; ?>]</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($budget_items as $bi) :
                    if(in_array($bi['extra'], array(0,1))):
                        echo $this->element('acordion_schedule_add', ['bi' => $bi]);
                    endif;
                endforeach; ?>
                <tr class="totales">
                    <td></td>
                    <td><b>Total</b></td>
                    <td class="text-left"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right">
                        <span class="suma_proyectado ldz_numeric_no_sign"></span>
                    </td>
                    <td class="text-right"></td>
                    <td class="text-center">
                        <span class="ldz_numeric_no_sign"></span>
                    </td>
                    <td class="text-right suma_real_monto ldz_numeric_no_sign"></td>
                </tr>
            </tbody>
         </table>
        <?= $this->Form->button(__('Guardar')) ?>
        <?= $this->Html->link(__('Cancelar'), ['action' => 'index', $budget_id], ['class' => 'btn btn-flat btn-link']) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
<?= $this->Html->script('schedules.add'); ?>
