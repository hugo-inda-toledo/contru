<?php
use Cake\I18n\Date;
echo $this->Html->script('jquery.progresstimer');

// elementos estandares de la vista
$this->assign('title_text', __('Módulo De Gastos'));
$this->assign('title_icon', 'groups');
$buttons = array();
$this->set('buttons', $buttons);
$theSign = trim(getSignByCurrencyId($budget->currency_id));
?>
<style>
	table.dataTable.table-condensed>thead>tr>th {
	    padding-right: 0;
	}

	div.myClass {
	    overflow-x: auto;
	    white-space: nowrap;
	}
	
	#col-scroll-class{  /* TWBS v2 */
	    display: inline-block;
	    float: none; /* Very important */
	}

	.tabla-mascara th, .tabla-mascara td{
		white-space: nowrap;
		font-size: 10px;
	}

	.pointer{ 
		cursor: pointer; 
	}

	.progress {
        height: 22px;
    }

    .other_currency{
    	display: block;
    }
</style>

<?php echo $this->Html->link(__('Volver'), ['controller' => 'buildings', 'action' => 'dashboard', $sf_building->CodArn], array('class' => 'btn btn-sm btn-primary text-right'));?>

<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-material-blue-grey-700">
		  	<div class="panel-heading">
		  		<div class="row">
			  		<div class="col-sm-6">
			  			 <h3 class="panel-title">Máscara</h3>
			  			 <?php
			  			 	$date = new Date($budget->cached_datetime);
			  			 	echo $this->Html->tag('small', __('Fecha de actualización: ').$date->format('d-m-Y H:i:s'), array('class' => 'text-right')); 
			  			 ?>
			  		</div>
		  		</div>
		    </div>
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-12">
						<div class="table-responsive">
							<table class="table table-hover table-bordered tabla-mascara" id="budget_review">
								<!--<col width="4%">
					            <col width="30%">
					            <col width="2%">
					            <col width="9%">
					            <col width="10%">
					            <col width="10%">
					            <col width="10%">
					            <col width="10%">
					            <col width="10%">
					            <col width="10%">
					            <col width="10%">-->
					            <thead id="header_budget">
					                <tr>
					            		<th class="text-center">Partidas</th>
					            		<th class="danger text-center" colspan="4">Contrato</th>
					            		<th class="danger text-center">Objetivo</th>
					            		<th class="success text-center" colspan="3">Cuantificación</th>
					            		<th class="primary text-center">Saldo Ppto Obj<br>- Comp</th>
					            		<th class="warning text-center">Dif Pptop Obj <br>V/S Gastado</th>
					            		<th class="warning text-center">Dif Ppto contrato <br>V/S Gastado</th>
					            		<th class="info text-center" colspan="3">Avances de obra</th>
					            	</tr>
					                <tr>
					                    <th>Descripción</th>
					                    <th class="text-left"><?= __('Unidad') ?></th>
					                    <th class="text-right"><?= __('Cantidad') ?></th>
					                    <th class="text-right"><?= __('Precio Unitario') ."<br>($theSign)"; ?></th>
					                    <th class="text-right"><?= __('Precio Total')."<br>($theSign)"; ?> </th>
					                    <th class="text-right"><?php echo __('Presup. objetivo')."<br>($theSign)"; ?> </th>
					                    <th class="text-left">Comprometido</th>
					                    <th class="text-left">Gastado</th>
					                    <th class="text-left">Facturado</th>
					                    <th class="text-left"></th>
					                    <th class="text-left"></th>
					                    <th class="text-left"></th>
					                    <th class="text-left">Avance <br>proyectado</th>
					                    <th class="text-left">Avance de<br>obra real</th>
					                    <th class="text-left">Avance financiero<br>(EP de venta)</th>
					                </tr>
					            </thead>
					            <tbody>
					                <?php
					                foreach ($budget_items as $bi) :

					                    if(isset($this->request->query['extra'])){
					                        if($this->request->query['extra']=="0"){
					                            if($bi['parent_id'] == null && ($bi['extra'] == 1 || $bi['extra'] == 2)){
					                                continue;
					                            }
					                        }else{
					                            if($bi['extra'] != $this->request->query['extra']){
					                                continue;
					                            }
					                        }
					                    }

					                    echo $this->element('budget_overview', ['bi' => $bi, 'the_budget_currency_value' => $budget->currencies_values[0]['value'], 'includeTotal' => true]);

					                ?>
					                <?php endforeach; ?>
					            </tbody>
					         </table>
					    </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<?= $this->Html->script('schedules.add'); ?>
<script>
	$(document).ready(function(){
	    $(window).scroll(function(){
	        var aTop = $('.dataTable').position().top;
	        if($(this).scrollTop()>=aTop){
	            $('#header_budget').css({
	                'position': 'fixed',
	                'top': '0',
	                'background-color': '#fff'
	            });
	        }else{
	            $('#header_budget').css({
	                'position': 'relative'
	            });
	        }
	    });
	});
</script>
