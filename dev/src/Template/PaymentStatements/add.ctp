<style>
    body {
        font-size: 12px;
    }

    b, strong {
        font-weight: normal;
    }
</style>
<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo Estados de Pago'));
$this->assign('title_icon', 'groups');
$buttons = array();
$this->set('buttons', $buttons);
?>

<div class="super-content">
    <?= $this->Form->create($paymentStatement); ?>
    <div class="panel panel-material-blue-grey-700">
        <div class="panel-heading">
            <h3 class="panel-title">Nuevo Estado de Pago</h3>
        </div><!-- end .panel-heading -->
        <div class="panel-body">
            <?= $this->Element('info_budget_building'); ?>
            <?= $this->Element('info_budget_detail'); ?>
            <div class="row">
                <div class="col-sm-3 col-md-3">
                    <?= $this->Form->input('gloss', ['type' => 'text', 'label' => 'Glosa del Estado de Pago', 'placeholder' => 'Ingrese glosa de Estado de Pago']) ?>
                    <?= $this->Form->input('currency_value_to_date', [
                        'templates' => [
                            'input' => '<input class="form-control text-right ldz_numeric_no_sign" type="{{type}}" name="{{name}}" {{attrs}}>',
                        ], 
                        'required' => true, 
                        'step' => 'any', 
                        'type' => 'text', 
                        'label' => 'Ingresar Valor Moneda', 
                        'min' => '1',
                        'placeholder' => moneda($budget->currencies[0]->valoresmonedas[0]->currency_value),
                        'default' => $budget->currencies[0]->valoresmonedas[0]->currency_value
                    ] ) ?>
                </div>
                <div class="col-sm-3 col-md-3">
                    <?php //$this->Form->input('presentation_date', ['type' => 'text', 'label' => 'Presentación Estimada', 'placeholder' => 'Ingrese fecha']) ?>
                    <?= $this->Form->input('presentation_date', ['type' => 'text', 'data-type' => 'datetimepicker', 'label' => 'Presentación Estimada', 'placeholder' => 'Ingrese fecha', 'data-date-format' => 'DD-MM-YYYY']);?>
                </div>
                <div class="col-sm-3 col-md-3">
                    <?php //$this->Form->input('billing_date', ['type' => 'text', 'label' => 'Factura Estimada', 'placeholder' => 'Ingrese fecha']) ?>
                    <?= $this->Form->input('billing_date', ['type' => 'text', 'data-type' => 'datetimepicker', 'label' => 'Factura Estimada', 'placeholder' => 'Ingrese fecha', 'data-date-format' => 'DD-MM-YYYY']);?>
                </div>
                <div class="col-sm-3 col-md-3">
                    <?php //$this->Form->input('estimation_pay_date', ['type' => 'text', 'label' => 'Pago Estimado', 'placeholder' => 'Ingrese fecha']) ?>
                    <?= $this->Form->input('estimation_pay_date', ['type' => 'text', 'data-type' => 'datetimepicker', 'label' => 'Pago Estimado', 'placeholder' => 'Ingrese fecha', 'data-date-format' => 'DD-MM-YYYY']);?>
                </div>
            </div>
            <?php $unidad_moneda = $budget['currencies_values'][0]['currency']['name']; ?>
            <div class="panel panel-default">
                <div class="panel-body">
                    <?php if (!empty($budget_items)) : ?>
                    <button id="btn-toggle-items" type="button" class="btn btn-flat btn-link">Items Originales</button>
                    <div id="originales">
                        <h4>Partidas del Presupuesto de la Obra</h4>
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="false">
                            <?php
                            foreach ($budget_items as $bi) :
                                $panelType = ($bi['disabled'] == 0) ? 'material-blue-grey-700' : 'warning';
                                if($bi['extra'] == 0) :
                                    echo $this->element('budget_items_edp_add', ['bi' => $bi, 'panel_type' => $panelType, 'unidad_moneda' => $unidad_moneda]);
                                endif;
                            endforeach; ?>
                        </div>
                    </div>
                    <div id="adicionales" style="Display: none;">
                        <h4>Partidas Adicionales del Presupuesto de la Obra</h4>
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="false">
                            <?php
                            foreach ($budget_items as $bi) :
                                $panelType = ($bi['disabled'] == 0) ? 'material-blue-grey-700' : 'warning';
                                if($bi['extra'] == 1) :
                                    echo $this->element('budget_items_edp_add', ['bi' => $bi, 'panel_type' => $panelType, 'unidad_moneda' => $unidad_moneda]);
                                endif;
                            endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?= $this->Form->button('Guardar') ?>
            <?= $this->Html->link(__('Volver'), ['controller' => 'payment_statements', 'action' => 'index', '?' => ['building_id'=>$budget->building_id]], ['class' => 'btn btn-flat btn-link']) ?>
            <input type="hidden" name="unidad_moneda" id="unidad_moneda" value="<?=$unidad_moneda; ?>">
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
<?= $this->Html->script('payment_statements.add'); ?>

<?php //debug($budget_items);?>
