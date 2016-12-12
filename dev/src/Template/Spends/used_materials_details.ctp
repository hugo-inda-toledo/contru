<?php use Cake\I18n\Date;?>
<style>
	.header-table{
		width: 15%;
	}

	.body-table{
		width: 35%;
	}

	.tabla-mascara th, .tabla-mascara td{
		white-space: nowrap;
		font-size: 11px;
	}

	.tabla-mascara-main th, .tabla-mascara-main td{
		white-space: nowrap;
		font-size: 13px;
	}

	.header-without-border{
		border-top: none;
	}
</style>

<?php if($budget_item->budget->currencies[0]->sbif_api_keyword != 'peso'):?>
	<script type="text/javascript">
	    $(document).ready(function() {
	    	$('td:nth-child(9),th:nth-child(9)').hide();
	        $('td:nth-child(10),th:nth-child(10)').hide();


	        $('#action-currency').click(function() {

	            var titulo = $("#action-currency").text();

	            if(titulo == 'Mostrar valores en '+<?= "'".$budget_item->budget->currencies[0]->name."'";?>)
	           	{
	           		$("#action-currency").html('Ocultar valores en '+<?= "'".$budget_item->budget->currencies[0]->name."'";?>);
	           		$('td:nth-child(9),th:nth-child(9)').show();
	           	 	$('td:nth-child(10),th:nth-child(10)').show();
	            	//$('#row-extend').attr('colspan',10);
	            	$('#action-currency').removeClass('btn-info').addClass('btn-danger');
	           	}
	           	else
	           	{
	           		$("#action-currency").html('Mostrar valores en '+<?= "'".$budget_item->budget->currencies[0]->name."'";?>);
	           		$('td:nth-child(9),th:nth-child(9)').hide();
	            	$('td:nth-child(10),th:nth-child(10)').hide();
	            	//$('#row-extend').attr('colspan',8);
	            	$('#action-currency').removeClass('btn-danger').addClass('btn-info');
	           	}
	        });
	    });
	</script>
<?php endif;?>

<?php $this->assign('title_text', __('Materiales Gastados')); ?>

<div class="panel panel-success">
    <div class="panel-heading">
    	<div class="row">
			<div class="col-sm-1">
				<?php echo $this->Html->link(__('Volver'), '/spends/overview/'.$budget_item->budget_id, array('class' => 'btn btn-sm btn-primary'));?>
			</div>
			<div class="col-sm-9">
				Resumen de materiales usados para la partida <?= $this->Html->tag('strong', $budget_item->item.' - '.$budget_item->description); ?><br><small><i>Esto considera todos los items que han sido consumidos para la partida <?= $budget_item->item;?></i></small>
			</div>
			<?php if($budget_item->budget->currencies[0]->sbif_api_keyword != 'peso'):?>
				<div class="col-sm-2">
					<?php echo $this->Html->link(__('Mostrar valores en '.$budget_item->budget->currencies[0]->name), 'javascript:void(0);', array('id' => 'action-currency', 'class' => 'btn btn-sm btn-info pull-right text-right'));?>
				</div>
			<?php endif;?>
		</div>
    </div>

    <?php if(count($materials) != 0):?>
	    <div class="panel-body">
	        <table class="table tabla-mascara table-hover" id="budget_review">
	            <thead id="header_budget">
	                <tr>
	                    <th class="info">#</th>
	                    <th class="info">Descripción</th>
	                    <th class="info">Fecha del gasto</th>
	                    <th class="info">Solicitado por</th>
	                    <th class="info">Fecha de aprobación</th>
	                    <th class="text-right info"><?= __('Cantidad') ?></th>
	                    <th class="text-right info"><?= __('Precio Unitario'); ?></th>
	                    <th class="text-right info"><?= __('Precio Total'); ?> </th>
	                    <?php if($budget_item->budget->currencies[0]->sbif_api_keyword != 'peso'):?>
							<th class="text-right warning">Valor de <?= $budget_item->budget->currencies[0]->name;?></th>
							<th class="text-right warning">Total en <?= $budget_item->budget->currencies[0]->name;?></th>
						<?php endif;?>
	                </tr>
	            </thead>
	            <tbody>
	            	<?php 
	            		$x = 1;
	            		$total_price = 0;
	            		$currency_total_price = 0;
	            	?>
	            	<?php foreach($materials as $material):?>
	            		<tr>
		                    <td><?= $x?></td>
		                    <td><?= $material->DESCRIPCION; ?></td>
		                    <td><?= $material->ic_consumo->FECHACREACION->format('d-m-Y'); ?></td>
		                    <td><?= $material->ic_consumo->USUARIOSOLICITA; ?></td>
		                    <td>
		                    	<?php 
		                    		$date = new Date($material->ic_consumo->FECHADOCORIGEN); 
		                    		echo $date->format('d-m-Y');
		                    	?>
		                    </td>
		                    <td class="text-right"><?= moneda($material->CANTIDAD); ?></td>
		                    <td class="text-right"><?= moneda($material->COSTOUNITARIO); ?></td>
		                    <td class="text-right"><?= moneda($material->COSTOLINEA); ?></td>

		                    <?php if($budget_item->budget->currencies[0]->sbif_api_keyword != 'peso'):?>

		                    	<?php
		                    		$currency_total = $material->COSTOLINEA / $material->ic_consumo->currency_day_value;
		                    		$currency_total_price += $currency_total;
		                    	?>
		                    	<td class="text-right warning">
									<?= moneda($material->ic_consumo->currency_day_value); ?>
								</td>
								<td class="text-right warning">
									<?= moneda($currency_total); ?>
								</td>
		                    <?php endif;?>
		                </tr>
		                <?php 
		                	$x++;
		                	$total_price =  $total_price + $material->COSTOLINEA;
		               	?>

	            	<?php endforeach;?>

	                <tr>
	                    <td></td>
	                    <td></td>
	                    <td></td>
	                    <td></td>
	                    <td></td>
	                    <td></td>
	                    <td class="text-right">
	                    	<?= $this->Html->tag('strong', __('Total neto')); ?>
	                    </td>
	                    <td class="text-right">
	                    	<?= $this->Html->tag('strong', moneda($total_price)); ?>
	                    </td>
	                    <td></td>
	                    <td class="text-right">
	                    	<?php echo $this->Html->tag('strong', moneda($currency_total_price).' '.$budget_item->budget->currencies[0]->plural_name); ?>
	                    </td>
	                </tr>
	            </tbody>
	        </table>
	    </div>
	<?php else:?>

		<?php echo $this->Html->tag('h2', __('No existen materiales para esta partida'), array('class' => 'text-danger text-center'));?>
		<br>

	<?php endif;?>
</div>