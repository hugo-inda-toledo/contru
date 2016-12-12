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

<?php $this->assign('title_text', __('Materiales Facturados')); ?>

<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-success">
			<div class="panel-heading">
				<div class="row">
					<div class="col-sm-1">
						<?php echo $this->Html->link(__('Volver'), '/spends/overview/'.$budget_item->budget_id, array('class' => 'btn btn-sm btn-primary'));?>
					</div>
					<div class="col-sm-9">
						Resumen de materiales facturados para la partida <?= $budget_item->description.' ('.$budget_item->item.')'; ?><br><small><i>Esto considera todos los items de las ordenes de compra asociados a la partida <?= $budget_item->item;?> y que además, tengan asociada una factura</i></small>
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
					<table class="table table-hover tabla-mascara">
						<tr>
							<th class="text-left info">#</th>
							<th class="text-right info">Nº Orden de Compra</th>
							<th class="text-right info">Nº Factura</th>
							<th class="text-right info">Fecha de facturación</th>
							<th class="text-left info">Codigo Material</th>
							<th class="text-left info">Nombre del Material</th>
							<th class="text-right info">Unidad</th>
							<th class="text-right info">Precio Unitario</th>
							<th class="text-right info">Cantidad</th>
							<th class="text-right info">Total</th>
							<?php if($budget_item->budget->currencies[0]->sbif_api_keyword != 'peso'):?>
								<th class="text-right warning">Valor de <?= $budget_item->budget->currencies[0]->name;?></th>
								<th class="text-right warning">Total en <?= $budget_item->budget->currencies[0]->name;?></th>
							<?php endif;?>
						</tr>
						<?php $grand_total = 0; $x=1; $currency_grand_total =0;?>
						<?php foreach($real_data as $oc):?>
							<?php foreach($oc['OrdenCompraItem'] as $item):?>
								<tr>
									<td class="text-left">
										<?= $x; ?>
									</td>
									<td class="text-right">
										<?= $oc['OrdenCompra']->NUMOC;?>
									</td>
									<td class="text-right">
										<?php
											foreach($oc['Factura'] as $fact)
											{
												if($oc['OrdenCompra']->ic_respaldo_oc_distribucion->ic_respaldo->ic_factura->IDDOC == $fact->IDDOC && $oc['OrdenCompra']->IDOC == $item['Data']->IDOC)
												{
													echo $fact->NUMFACTURA;
												}
											}
										?>

									</td>
									<td class="text-right">
										<?php
											foreach($oc['Factura'] as $fact)
											{
												if($oc['OrdenCompra']->ic_respaldo_oc_distribucion->ic_respaldo->ic_factura->IDDOC == $fact->IDDOC && $oc['OrdenCompra']->IDOC == $item['Data']->IDOC)
												{
													echo $fact->FECHAEMISION->format('d-m-Y');
												}
											}
										?>

									</td>
									<td class="text-left">
										<?= $item['Data']->IDARTICULO;?>
									</td>
									<td class="text-left">
										<?= $item['Data']->NOMBARTICULO;?>
									</td>
									<td class="text-right">
										<?= $item['Data']->ic_uom->DESCRIPCORTA; ?>
									</td>
									<td class="text-right">
										<?= moneda($item['Data']->MONTOUNITARIO);?>
									</td>
									<td class="text-right">
										<?php
											$cantidad = 0;
											if($oc['OrdenCompraTipo']->DESCRIPCION == 'Orden de Compra')
											{
												$cantidad = (($item['Distribucion']['VALOR'] /100) * $item['Data']->CANTIDAD);
												echo moneda($cantidad);
											}
											elseif($oc['OrdenCompraTipo']->DESCRIPCION == 'Pedido de Materiales')
											{
												$cantidad = $item['Distribucion']['VALOR'];
												echo moneda($cantidad);
											}
										?>
									</td>
									<td class="text-right">
										<?= moneda($item['Distribucion']['PRECIOVALOR']);?>

										<?php 
											$grand_total += $item['Distribucion']['PRECIOVALOR'];
											$x++;
										?>

									</td>
									<?php
										if($budget_item->budget->currencies[0]->sbif_api_keyword != 'peso')
										{
											foreach($oc['Factura'] as $fact)
											{
												if($oc['OrdenCompra']->ic_respaldo_oc_distribucion->ic_respaldo->ic_factura->IDDOC == $fact->IDDOC && $oc['OrdenCompra']->IDOC == $item['Data']->IDOC)
												{
													$currency_total = $item['Distribucion']['PRECIOVALOR'] / $fact->currency_day_value;

													$currency_grand_total += $currency_total;

													echo '<td class="text-right warning">'.moneda($fact->currency_day_value).'</td>';

													echo '<td class="text-right warning">'.moneda($currency_total).'</td>';
												}
											}
										}
									?>
								</tr>
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
							<td class="text-right"><?= $this->Html->tag('strong', 'Total Neto'); ?></td>
							<td class="text-right"><?php echo $this->Html->tag('strong', moneda($grand_total)); ?></td>
							<td></td>
							<td class="text-right">
								<?php echo $this->Html->tag('strong', moneda($currency_grand_total).' '.$budget_item->budget->currencies[0]->plural_name); ?>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>