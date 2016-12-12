<?php
// elementos estandares de la vista
$this->assign('title_text', __('Máscara Obra ').$sf_building->CodArn.' - '.$sf_building->DesArn);
$this->assign('title_icon', 'groups');
$buttons = array();
$this->set('buttons', $buttons);
?>

<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Vista Resumida Presupuesto</h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <?= $this->Element('info_budget_building'); ?>
        <h3>Detalle Partidas de Presupuesto <button type="button" class="btn btn-right btn-default btn-raised btn-sm mb-10" id="show-all">Mostrar todos</button></h3>
        <table class="maskTable table table-bordered table-condensed table-hover table-item table-striped ">
            <thead>
                <tr>
                    <th class="text-center" rowspan="2" colspan="1">Descripción</th>
                    <th class="text-center" colspan="5">Contrato</th>
                    <th class="text-center" colspan="1">Objetivo</th>
                    <th class="text-center" colspan="2">(algo en verde)</th>
                    <th class="text-center" colspan="3">(algo en amarillo)</th>
                    <th class="text-center" colspan="3">Avances de obra</th>
                </tr>
                <tr>                    
                    <!-- contrato(4) -->
                    <th class="text-center"><?= __('Unid') ?></th>
                    <th class="text-center"><?= __('Cantidad') ?></th>
                    <th class="text-center"><?= __('Precio Unitario') ?></th>
                    <th class="text-center"><?= __('Precio Total') ?> </th>
                    <th class="text-center"><?= __('Comentario') ?></th>
                    <!-- objetivo(1) -->
                    <th class="text-center"><?= __('Presupuesto Objetivo') ?></th>
                    <!-- verde(2) -->
                    <th class="text-center"><?= __('Comprometido') ?></th>
                    <th class="text-center"><?= __('Saldo Ppto Obj-Comp') ?></th>
                    <!-- amarillo(3) -->
                    <th class="text-center"><?= __('GASTADO (DOCUMENTOS + COMP. DE PAGO DE MANO DE OBRA') ?></th>
                    <th class="text-center"><?= __('DIF PPTO OBJ V/S GASTADO') ?></th>
                    <th class="text-center"><?= __('DIF PPTO CONTRATO  V/S GASTADO') ?></th>
                    <!-- avances de obra(3) -->
                    <th class="text-center"><?= __('Avance de Obra Real') ?></th>
                    <th class="text-center"><?= __('Avance Financiero %') ?></th>
                    <th class="text-center"><?= __('Avance Financiero (EP de Venta)') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($budget_items as $bi) :
                    echo $this->element('budget_mask', ['bi' => $bi, 'the_budget_currency_value' => $budget->currencies_values[0]['value']]);
                endforeach; ?>
            </tbody>
         </table>
         <?= $this->Element('info_budget_detail'); ?>
        <?= $this->Html->link(__('Volver Atras'), ['action' => 'index', $budget_id], ['class' => 'btn btn-flat btn-link']) ?>
    </div>
</div>
<?= $this->Html->script('budgets.mask'); ?>
