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
            <h3 class="panel-title">Editar Estado de Pago</h3>
        </div><!-- end .panel-heading -->
        <div class="panel-body">
            <?php if($paymentStatement->draft == 1 && !is_null($paymentStatement->client_approval)):?>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <div class="alert alert-warning text-center" role="alert" style="font-size:18px;"><i class="glyphicon glyphicon-pencil" style="font-size:35px;"></i> <strong>Este estado de pago ha sido rechazado por el cliente con la siguiente justificación: <i><?= $paymentStatement->decline_obs; ?></i>.</strong>
                        <br>
                        Corrígelo para enviarlo nuevamente a aprobación
                        </div>
                        <?= $this->Html->link(__('Enviar para aprobación'), ['controller' => 'payment_statements', 'action' => 'accept', $paymentStatement->id], [ 'confirm' => __('Estas seguro de enviar el estado #{0} para aprobación?', $paymentStatement->id), 'class' => 'btn btn-lg btn-primary text-center']) ?>
                    </div>
                </div>
                <br>
            <?php endif;?>

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
                    ] ) ?>
                </div>

                <div class="col-sm-3 col-md-3">
                    <?= $this->Form->input('presentation_date', ['type' => 'text', 'data-type' => 'datetimepicker', 'label' => 'Presentación Estimada', 'placeholder' => 'Ingrese fecha', 'data-date-format' => 'DD-MM-YYYY', 'value' => $this->Time->format($paymentStatement->presentation_date, 'dd-MM-Y')]);?>
                </div>
                <div class="col-sm-3 col-md-3">
                    <?= $this->Form->input('billing_date', ['type' => 'text', 'data-type' => 'datetimepicker', 'label' => 'Factura Estimada', 'placeholder' => 'Ingrese fecha', 'data-date-format' => 'DD-MM-YYYY', 'value' => $this->Time->format($paymentStatement->billing_date, 'dd-MM-Y')]);?>
                </div>
                <div class="col-sm-3 col-md-3">
                    <?= $this->Form->input('estimation_pay_date', ['type' => 'text', 'data-type' => 'datetimepicker', 'label' => 'Pago Estimado', 'placeholder' => 'Ingrese fecha', 'data-date-format' => 'DD-MM-YYYY', 'value' => $this->Time->format($paymentStatement->estimation_pay_date, 'dd-MM-Y')]);?>
                </div>

            </div>
            <?php $unidad_moneda = $budget['currencies_values'][0]['currency']['name']; ?>
            <div class="panel panel-default">
                <div class="panel-body">
                    <h4>Partidas del Presupuesto de la Obra</h4>
                    <?php if (!empty($budget_items)) : ?>
                        <?php $type_bi = ($type_payment_statement == 'originales') ? 0 : 1; ?>
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="false">
                            <?php
                            foreach ($budget_items as $bi) :
                                $panelType = ($bi['disabled'] == 0) ? 'material-blue-grey-700' : 'warning';
                                if($bi['extra'] == $type_bi) :
                                    echo $this->element('budget_items_edp_edit', ['bi' => $bi, 'panel_type' => $panelType, 'unidad_moneda' => $unidad_moneda]);
                                endif;
                            endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?= $this->Form->button('Guardar') ?>
            <?= $this->Html->link(__('Volver'), ['controller' => 'payment_statements', 'action' => 'view', $paymentStatement->id], ['class' => 'btn btn-flat btn-link']) ?>
            <?= $this->Html->link(__('Eliminar'), ['controller' => 'payment_statements', 'action' => 'delete', $paymentStatement->id], ['confirm' => __('Estas seguro de eliminar el estado de pago [{0}]?', $paymentStatement->gloss), 'class' => 'btn btn-xs btn-danger pull-right']) ?>
            <input type="hidden" name="unidad_moneda" id="unidad_moneda" value="<?=$unidad_moneda; ?>">
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
<?= $this->Html->script('payment_statements.add'); ?>
