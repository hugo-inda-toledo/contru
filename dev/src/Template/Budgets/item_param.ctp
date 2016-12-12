<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Presupuesto'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>

<!-- Información General -->
<!--<?= $this->Element('info_budget_building'); ?> -->
<!-- Información Específica -->
<?= $this->Element('info_budget_detail'); ?>

<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Configuracion Parametros de Item <?= ($budgetItem->extra === 0) ? 'Original' : 'Extra'; ?> </h3>
    </div>
    <div class="panel-body">
        <div class="column large-12">
            <div class="budgetItems form large-10 medium-9 columns">
            <?= $this->Form->create($budgetItem,['id' => 'params']); ?>
            <fieldset>
                 <div class="col-md-6 col-sm-6">
                    <?php
                    echo $this->Form->input('item',['disabled' => true]);
                    echo $this->Form->input('description',['label' => 'Descripción']);
                    echo $this->Form->input('unit_id', ['options' => $units, 'required' => $leaf,'label' => 'Unidad']);
                    echo $this->Form->input('quantity',['label' => 'Cantidad']);
                    echo $this->Form->input('unity_price',['label' => 'Precio Unitario']);
                    echo $this->Form->input('total_price',['disabled' => true, 'label' => 'Precio Total']);
                    ?>
                </div>
                <div class="col-md-6 col-sm-6">
                    <?php
                    if ($budgetItem->extra === 1) :
                        echo $this->Form->input('general_cost',['label' => 'Costo General']);
                        echo $this->Form->input('utilities',['label' => 'Utilidades']);
                        echo $this->Form->input('retention',['label' => 'Retenciones']);
                        echo $this->Form->input('advance',['label' => 'Avance']);
                    endif; ?>
                </div>
            </fieldset>
            <?= $this->Form->button(__('Guardar')) ?>
            <?= $this->Html->link(
                    'Cancelar',
                    ['controller' => 'budgets', 'action' => 'review', $budgetItem->budget_id],
                    ['class' => 'btn btn-flat btn-link']
                ); ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<script>
$("#quantity").on('input', function () {
    updateform();
});
$("#unity-price").on('input', function () {
    updateform();
});

$("#total-price").on('change', function () {
   updateform();
});
function updateform() {
    var q = $('#quantity').val();
    var p = $('#unity-price').val();
    $('#total-price').val(q * p);
    if ($('#total-price').val() > 0) {
        $('#unit-id').attr('required',true);
    } else {
        $('#unit-id').attr('required',false);
    }
}
</script>