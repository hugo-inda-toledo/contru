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
	    	$('td:nth-child(11),th:nth-child(11)').hide();
	        $('td:nth-child(12),th:nth-child(12)').hide();


	        $('#action-currency').click(function() {

	            var titulo = $("#action-currency").text();

	            if(titulo == 'Mostrar valores en '+<?= "'".$budget_item->budget->currencies[0]->name."'";?>)
	           	{
	           		$("#action-currency").html('Ocultar valores en '+<?= "'".$budget_item->budget->currencies[0]->name."'";?>);
	           		$('td:nth-child(11),th:nth-child(11)').show();
	           	 	$('td:nth-child(12),th:nth-child(12)').show();
	            	//$('#row-extend').attr('colspan',10);
	            	$('#action-currency').removeClass('btn-info').addClass('btn-danger');
	           	}
	           	else
	           	{
	           		$("#action-currency").html('Mostrar valores en '+<?= "'".$budget_item->budget->currencies[0]->name."'";?>);
	           		$('td:nth-child(11),th:nth-child(11)').hide();
	            	$('td:nth-child(12),th:nth-child(12)').hide();
	            	//$('#row-extend').attr('colspan',8);
	            	$('#action-currency').removeClass('btn-danger').addClass('btn-info');
	           	}
	        });
	    });
	</script>
<?php endif;?>

<?php $this->assign('title_text', __('Subcontratos Gastados')); ?>

<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<div class="row">
					<div class="col-sm-1">
						<?php echo $this->Html->link(__('Volver'), '/spends/overview/'.$budget_item->budget_id, array('class' => 'btn btn-sm btn-primary'));?>
					</div>
					<div class="col-sm-9">
						Resumen de subcontratos gastados para la partida <?= $this->Html->tag('b', $budget_item->item.' - '.$budget_item->description); ?><br><small><i>Esto considera los estados de pago aprobados para cada subcontrato</i></small>
					</div>
					<?php if($budget_item->budget->currencies[0]->sbif_api_keyword != 'peso'):?>
						<div class="col-sm-2">
							<?php echo $this->Html->link(__('Mostrar valores en '.$budget_item->budget->currencies[0]->name), 'javascript:void(0);', array('id' => 'action-currency', 'class' => 'btn btn-sm btn-info pull-right text-right'));?>
						</div>
					<?php endif;?>
				</div>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table tabla-mascara table-hover">
						<thead>
							<tr>
								<th class="text-left info">#</th>
								<th class="text-right info">Nº Subcontrato</th>
								<th class="text-right info">Nº Estado Pago</th>
								<th class="text-right info">Fecha del Estado de Pago</th>
								<th class="text-left info">Nombre Subcontrato</th>
								<th class="text-left info">Tipo Documento</th>
								<th class="text-left info">Descripción</th>
								<th class="text-right info">Fecha de Aprobación</th>
								<th class="text-left info">Estado</th>
								<th class="text-right info">Total</th>
								<?php if($budget_item->budget->currencies[0]->sbif_api_keyword != 'peso'):?>
									<th class="text-right warning">Valor de <?= $budget_item->budget->currencies[0]->name;?></th>
									<th class="text-right warning">Total en <?= $budget_item->budget->currencies[0]->name;?></th>
								<?php endif;?>
							</tr>
						</thead>
						<tbody>
							<?php $grand_total = 0; $x=1; $currency_grand_total =0;?>

							<?php foreach($real_data as $sc):?>
								<?php foreach($sc['EstadoPago'] as $ep):?>
									<?php foreach($ep['Items'] as $item):?>
										<tr>
											<td class="text-left"><?= $x; ?></td>
											<td class="text-right"><?= $this->Html->link($sc['Subcontrato']->NUMSUBCONT, '#'.$sc['Subcontrato']->NUMSUBCONT);?></td>
											<td class="text-right">
												<?= $ep['Data']['NUMDOC'];?>	
											</td>
											<td class="text-right"><?= $ep['Data']['FECHACREACION']->format('d-m-Y'); ?></td>
											
											<td class="text-left"><?= $sc['Subcontrato']->NOMDOC; ?></td>
											<td class="text-left">
												<?php
							    					$text_type = explode('EstadoPago', $ep['Data']->ic_tipo_doc->DESCRIPCION);
							    					echo $text_type[1];
							    				?>
											</td>
											
											<td class="text-left">
												<?= $item['Data']->DESCRIPCION; ?>
											</td>
											<td class="text-right">
												<?php
													$date = new Date($ep['Data']['FECHAAPROBACION']);
													echo $date->format('d-m-Y H:i:s'); 
												?>
											</td>
											<td class="text-left">
												<?php
							    					$text_status = explode('EstadoPago', $ep['Data']->ic_estado_doc->DESCRIPCIONC);
							    					echo $text_status[1];
							    				?>
											</td>
											<td class="text-right">
												<?= moneda($item['Distribucion']['PRECIOVALOR']); ?>
												<?php $grand_total += $item['Distribucion']['PRECIOVALOR'];?>
											</td>
											<?php
												if($budget_item->budget->currencies[0]->sbif_api_keyword != 'peso')
												{
													//echo $this->cell('Spend::showAndTransformValues', [$ep['Data']['FECHACREACION']->format('Y-m-d'), $budget_item->budget->currencies[0]->sbif_api_keyword, $item['Distribucion']['PRECIOVALOR']]);
													
													$currency_total = $item['Distribucion']['PRECIOVALOR'] / $ep['Data']['currency_day_value'];
													$currency_grand_total += $currency_total;

													echo '<td class="text-right warning">'.moneda($ep['Data']['currency_day_value']).'</td>';
													echo '<td class="text-right warning">'.moneda($currency_total).'</td>'; 
												}
											?>
										</tr>
										<?php $x++;?>
									<?php endforeach;?>
								<?php endforeach;?>
							<?php endforeach;?>

							<tr>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<th class="text-left">
									<?php echo $this->Html->tag('strong', 'TOTAL NETO'); ?>
								</th>
								<td class="text-right">
									<?php echo $this->Html->tag('strong', moneda($grand_total)); ?>
								</td>
								<td></td>
								<td class="text-right">
									<?php echo $this->Html->tag('strong', moneda($currency_grand_total).' '.$budget_item->budget->currencies[0]->plural_name, array('class' => 'text-right', 'style' => 'margin:0px;')); ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<?php foreach($real_data as $sc):?>
		<?php $total = 0;?>
		<div class="col-sm-12" id="<?= $sc['Subcontrato']->NUMSUBCONT?>">
			<div class="panel panel-material-blue-grey-700">
				<div class="panel-heading">Subcontrato N° <?= $sc['Subcontrato']->NUMSUBCONT.' ['.$sc['Subcontrato']->NOMDOC.']'; ?></div>  <td class="text-center">
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-hover tabla-mascara">
					    	<thead>
					    		<tr>
					    			<th class="text-right">Nº Est. Pago</th>
					    			<th class="text-left">Tipo Documento</th>
					    			<th class="text-right">Descripción</th>
					    			<th class="text-right">Fecha de Aprobación</th>
					    			<th class="text-left">Estado</th>					    			
					    			<th class="text-right">Sub Total</th>
					    		</tr>
					    	</thead>
					    	<tbody>
					    		<?php foreach($sc['EstadoPago'] as $ep):?>
					    			<?php foreach($ep['Items'] as $item):?>
							    		<tr>
							    			<td class="text-right"><?= $ep['Data']['NUMDOC']; ?></td>
							    			<td class="text-left">
							    				<?php
							    					$text_type = explode('EstadoPago', $ep['Data']->ic_tipo_doc->DESCRIPCION);
							    					echo $text_type[1];
							    				?>
							    			</td>
							    			<td class="text-right">
							    				<?= $item['Data']->DESCRIPCION; ?>
							    			</td>
							    			<td class="text-right">
							    				<?php
													$date = new Date($ep['Data']['FECHAAPROBACION']);
													echo $date->format('d-m-Y H:i:s'); 
												?>
							    			</td>
							    			<td class="text-left">
							    				<?php
							    					$text_status = explode('EstadoPago', $ep['Data']->ic_estado_doc->DESCRIPCIONC);
							    					echo $text_status[1];
							    				?>	
							    			</td>
							    			
							    			<td class="text-right"><?= moneda($item['Distribucion']['PRECIOVALOR']); ?></td>
							    			<?php
							    				$total += $item['Distribucion']['PRECIOVALOR'];
							    			?>
							    		</tr>
							    	<?php endforeach;?>
						    	<?php endforeach;?>
						    	<tr>
						    		<th class="text-left" colspan="4">
						    		</th>
									<th class="text-right">
										<?php echo $this->Html->tag('strong', 'Monto total'); ?>
									</th>
									<td class="text-right header-table">
										<?php echo $this->Html->tag('strong', moneda($total)); ?>
									</td>
								</tr>
					    	</tbody>
					  	</table>
					</div>
				</div>
			</div>
		</div>
	<?php endforeach;?>
</div>