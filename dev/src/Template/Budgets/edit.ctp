<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Presupuesto'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>
<style>
.download_excel{
    /*display: block;*/
    margin-left: 10px;
    /*margin-top: -10px;*/
}
.download_excel:hover, .download_excel:focus{
    color: #009688;
}
</style>
<div class="panel panel-material-blue-grey-700">
    <div class="panel-heading">
        <h3 class="panel-title">Configurar Presupuesto Inicial</h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <?= $this->Form->create($budget); ?>
        <div class="row">
            <div class="col-sm-12 col-md-6">
                    <h3>Obra: <?= h($sf_building->DesArn) ?></h3>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="col-md-6 col-sm-6">
                                <?= $this->Form->hidden('building.id', ['value' => $building_id]);?>
                                <?= $this->Form->hidden('building.softland_id', ['value' => $building->softland_id]);?>
                                <?= $this->Form->input('building.client', ['label' => 'Mandante', 'value' => $building->client]);?>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <?= $this->Form->input('building.address', ['label' => 'Dirección','value' => $building->address]);?>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="col-sm-12 col-md-6">
                    <h3>Moneda</h3>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="col-sm-6"><?php
                                $tipoMoneda = isset($budget->currencies_values[0]->currency_id) ? $budget->currencies_values[0]->currency_id : 1;
                                $valorMoneda = isset($budget->currencies_values[0]->value) ? $budget->currencies_values[0]->value : 1;
                                echo $this->Form->input('currencies_values.0.currency_id', ['options' => $currencies, 'label' => 'Tipo de Moneda', 'default' => $tipoMoneda]);
                            ?></div>
                            <div class="col-sm-6"><?php
                            echo $this->Form->input('currencies_values.0.value', [
                                'templates' => [
                                    'input' => '<input class="form-control text-right ldz_numeric_no_sign" type="{{type}}" name="{{name}}" {{attrs}}>',
                                ],
                                'type' => 'text', 'step' => 'any', 'label' => 'Valor en CLP', 'value' => $valorMoneda]);
                            ?></div>
                        </div>
                    </div>
            </div>
        </div>
            <h3>Datos Presupuesto</h3>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="col-md-6 col-sm-6">
                    <div class="row">
                        <?=$this->Form->input('general_costs', ['type' => 'hidden', 'label' => 'Total Gastos Generales']);?>
                        <div class="col-xs-12 col-sm-6 col-lg-3"><?=$this->Form->input('duration', [
                                'templates' => [
                                    'input' => '<input class="form-control text-right" type="{{type}}" name="{{name}}" {{attrs}}>',
                                ],
                                'type' => 'number', 'label' => 'Duración Obra (Meses)']);?></div>
                        <div class="col-xs-12 col-sm-6 col-lg-3"><?=$this->Form->input('advances', [
                                'templates' => [
                                    'input' => '<input class="form-control text-right" type="{{type}}" name="{{name}}" {{attrs}}>',
                                ],
                                'type' => 'number', 'step' => 'any', 'label' => 'Anticipo (%)']);?></div>
                        <div class="col-xs-12 col-sm-6 col-lg-3"><?=$this->Form->input('retentions', [
                                'templates' => [
                                    'input' => '<input class="form-control text-right" type="{{type}}" name="{{name}}" {{attrs}}>',
                                ],
                                'type' => 'number', 'step' => 'any', 'label' => 'Retenciones (%)']);?></div>
                        <div class="col-xs-12 col-sm-6 col-lg-3"><?=$this->Form->input('utilities', [
                                'templates' => [
                                    'input' => '<input class="form-control text-right" type="{{type}}" name="{{name}}" {{attrs}}>',
                                ],
                                'type' => 'number', 'step' => 'any', 'label' => 'Utilidades (%)']);?></div>
                    </div>
                    <?php
                        echo $this->Form->hidden('building_id', ['value' => $building_id]);
                    ?>
                    </div>
                    <div class="col-md-6 col-sm-6">
                    <?php
                        echo $this->Form->input('comments', ['type' => 'textarea', 'label' => 'Observaciones']);
                     ?>
                    </div>
                </div>
            </div>
            <div>
                <?= $this->Form->button(__('Guardar'), ['id' => 'save_button']) ?>
                <?php
                    echo $this->Html->link(__('Cancelar'), '#',
                        [
                            'id' => 'cancelar_button',
                            'class' => 'btn btn-default btn-link'
                        ]
                    );
                ?>
            </div>
        <?= $this->Form->end() ?>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="cancelar" id="cancelar">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">¿Seguro que desea cancelar?</h4>
      </div>
      <div class="modal-body">
        <p>volverá al listado de obras</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">No deseo cancelar</button>
        <?= $this->Form->postLink('Sí, cancelar y volver al listado', ['controller' => 'buildings', 'action' => 'index'], ['class' => 'btn btn-primary']) ?>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
var theSign = "$ ";
function autoNumericInit(operation){
    $('input.ldz_numeric').autoNumeric(operation, {
        aSep: '.',
        aDec: ',',
        aSign: theSign
        // aSign: '€ '
    });  //autoNumeric with options being passed

    $('input.ldz_numeric_no_sign').autoNumeric(operation, {
        aSep: '.',
        aDec: ',',
        aSign: ''
    });  //autoNumeric with options being passed
    console.log(theSign);
}

$(document).ready(function(){
    autoNumericInit('init');

    $( "#cancelar_button" ).click(function( event ) {
        event.preventDefault();
        $('#cancelar').modal('show');
    });

    $(document).on('click', '#currencies-values-0-currency-id', function(event) {
        label = $("#general-costs").parent().find(".control-label");

        value = $(this).val();
        if( value == 1 ){
            //PESO
            theSign = "$ ";
            $("#currencies-values-0-value").val(1);
            label.text("Total Gastos Generales (en pesos)");
        }else if( value == 2 ){
            //DOLAR
            theSign = "USD ";
            $("#currencies-values-0-value").val(700);
            label.text("Total Gastos Generales (en dólares)");
        }else if( value == 3 ){
            //UF
            theSign = "UF ";
            $("#currencies-values-0-value").val(24500);
            label.text("Total Gastos Generales (en UF)");
        }
        autoNumericInit('update');
    })

    //bootbox confirm
    $(document).on('click', '#save_button', function(event) {
        var _this = $(this);
        event.preventDefault();
        bootbox.confirm({
            title: "Confirme por favor",
            message: '<p>El presupuesto quedará configurado con los siguientes valores</p>' +
                '<div class="row"><div class="col-xs-12 col-md-8 col-md-offset-2">'+
                    '<div class="row">' +
                    '<div class="col-sm-6">Mandante</div>' +
                    '<div class="col-sm-6 text-right">' + $("#building-client").val() + '</div>' +
                    '</div>' +
                    '<div class="row bg-success">' +
                    '<div class="col-sm-6">Dirección</div>' +
                    '<div class="col-sm-6 text-right">' + $("#building-address").val() + '</div>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div class="col-sm-6">Moneda</div>' +
                    '<div class="col-sm-6 text-right">' + $("#currencies-values-0-currency-id option:selected").text() + '</div>' +
                    '</div>' +
                    '<div class="row bg-success">' +
                    '<div class="col-sm-6">Valor Moneda</div>' +
                    '<div class="col-sm-6 text-right">' + $("#currencies-values-0-value").val() + '</div>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div class="col-sm-6">Duración obra (meses)</div>' +
                    '<div class="col-sm-6 text-right">' + $("#duration").val() + '</div>' +
                    '</div>' +
                    '<div class="row bg-success">' +
                    '<div class="col-sm-6">Anticipo</div>' +
                    '<div class="col-sm-6 text-right">' + $("#advances").val() + '%</div>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div class="col-sm-6">Retenciones</div>' +
                    '<div class="col-sm-6 text-right">' + $("#retentions").val() + '%</div>' +
                    '</div>' +
                    '<div class="row bg-success">' +
                    '<div class="col-sm-6">Utilidades</div>' +
                    '<div class="col-sm-6 text-right">' + $("#utilities").val() + '%</div>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div class="col-sm-6">Observaciones</div>' +
                    '<div class="col-sm-6 text-right">' + $("#comments").val() + '</div>' +
                    '</div>'+
                '</div></div>',
            buttons: {
                confirm: {
                    label: "Aceptar",
                    className: "btn btn-material-orange-900",
                },
                cancel: {
                    label: "Cancelar",
                    className: "btn btn-flat btn-link",
                }
            },
            callback: function(result){
                if(result) {
                    $('input.ldz_numeric, input.ldz_numeric_no_sign').each(function(i){
                        var self = $(this);
                        try{
                            var v = self.autoNumeric('get');
                            self.autoNumeric('destroy');
                            self.val(v);
                        }catch(err){
                            console.log("Not an autonumeric field: " + self.attr("name"));
                        }
                    });
                    _this.closest('form').submit();
                }
            }
        });
    });
});
</script>