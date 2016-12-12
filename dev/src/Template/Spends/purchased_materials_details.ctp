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
	    	$('td:nth-child(12),th:nth-child(12)').hide();
	        $('td:nth-child(13),th:nth-child(13)').hide();

	        $('#action-currency').click(function() {

	            var titulo = $("#action-currency").text();

	            if(titulo == 'Mostrar valores en '+<?= "'".$budget_item->budget->currencies[0]->name."'";?>)
	           	{
	           		$("#action-currency").html('Ocultar valores en '+<?= "'".$budget_item->budget->currencies[0]->name."'";?>);
	           		$('td:nth-child(12),th:nth-child(12)').show();
	           	 	$('td:nth-child(13),th:nth-child(13)').show();
	            	//$('#row-extend').attr('colspan',10);
	            	$('#action-currency').removeClass('btn-info').addClass('btn-danger');
	           	}
	           	else
	           	{
	           		$("#action-currency").html('Mostrar valores en '+<?= "'".$budget_item->budget->currencies[0]->name."'";?>);
	           		$('td:nth-child(12),th:nth-child(12)').hide();
	            	$('td:nth-child(13),th:nth-child(13)').hide();
	            	//$('#row-extend').attr('colspan',8);
	            	$('#action-currency').removeClass('btn-danger').addClass('btn-info');
	           	}
	        });
	    });
	</script>
<?php endif;?>

<?php $this->assign('title_text', __('Materiales Comprometidos')); ?>

<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-success">
			<div class="panel-heading">
				<div class="row">
					<div class="col-sm-1">
						<?php echo $this->Html->link(__('Volver'), '/spends/overview/'.$budget_item->budget_id, array('class' => 'btn btn-sm btn-primary'));?>
					</div>
					<div class="col-sm-9">
						Resumen de ordenes de compra para la partida <?= $this->Html->tag('strong', $budget_item->item.' - '.$budget_item->description); ?><br><small><i>Esto considera todos los items de las ordenes de compra que esten asociados a la partida <?= $budget_item->item;?></i></small>
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
					<table class="table tabla-mascara">
						<thead>
							<tr>
								<th class="text-left info">#</th>
								<th class="text-left info">Nº Orden de compra</th>
								<th class="text-left info">Fecha de Orden de Compra</th>
								<th class="text-right info">Código</th>
								<th class="text-left info">Descripción</th>
								<th class="text-left info">Unidad</th>
								<th class="text-right info">Precio Unit.</th>
								<th class="text-right info">Cantidad</th>
								<th class="text-right info">Sub Total</th>
								<th class="text-right info">Descuento</th>
								<th class="text-right info">Total</th>
								<?php if($budget_item->budget->currencies[0]->sbif_api_keyword != 'peso'):?>
									<th class="text-right warning">Valor de <?= $budget_item->budget->currencies[0]->name;?></th>
									<th class="text-right warning">Total en <?= $budget_item->budget->currencies[0]->name;?></th>
								<?php endif;?>
							</tr>
						</thead>
						<tbody>
							<?php $grand_total = 0; $x=1; $currency_grand_total = 0;?>

							<?php foreach($ordenes_compra as $orden_compra):?>
								<?php foreach($orden_compra['OrdenCompraItem'] as $item):?>
									<tr>
										<td class="text-left"><?= $x;?></td>
										<td class="text-left"><?= $this->Html->link($orden_compra['OrdenCompra']->NUMOC, '#'.$orden_compra['OrdenCompra']->NUMOC);?></td>
										<td class="text-left"><?= $orden_compra['OrdenCompra']->FECHACREACION->format('d-m-Y');?></td>
										<td class="text-right"><?= $item['Data']->IDARTICULO;?></td>
										
										
										<td class="text-left"><?= $item['Data']->NOMBARTICULO; ?></td>
										<td class="text-left"><?= $item['Data']->ic_uom->DESCRIPCORTA; ?></td>
										
										<td class="text-right"><?= moneda($item['Data']->MONTOUNITARIO); ?></td>
										<td class="text-right">
											<?php //debug($item['Distribucion']);?>
											<?php $quantity = 0;?>
											<?php foreach($item['Distribucion'] as $distr): ?>
													<?php
														if($distr->TIPODISTRIB == 1)
														{
															$quantity = ($distr->VALOR / 100) * $item['Data']->CANTIDAD;
														}
														else
														{
															$quantity = $distr->VALOR;
														}
													?>
											<?php endforeach; ?>

											<?= moneda($quantity);?>
										</td>
										<td class="text-right">
											<?php
												$sub_total = 0;
												
												foreach($item['Distribucion'] as $distr)
												{
													$sub_total = $item['Data']->MONTOUNITARIO * $quantity;
												}

												echo moneda($sub_total); 
											?>	
										</td>
										<td class="text-right">
											<?php
												if($item['Data']->ic_orden_compra_item_descuento != null)
												{
													echo moneda($item['Data']->ic_orden_compra_item_descuento->VALOR);
												}
												else
												{
													echo moneda(0);
												}
											?>
										</td>
										<td class="text-right">
											<?php
												if($item['Data']->ic_orden_compra_item_descuento != null)
												{
													$total = $sub_total - $item->ic_orden_compra_item_descuento->VALOR;
													
													echo moneda($total);
												}
												else
												{
													$total = $sub_total;
													echo moneda($total);
												}

												if($budget_item->budget->currencies[0]->sbif_api_keyword != 'peso' && $total != 0)
												{
													$currency_total = 0;
													$currency_total = $total / $item['Data']->currency_day_value;

													$currency_grand_total += $currency_total;
												}

												$grand_total += $total;
											?>
										</td>

										<?php if($budget_item->budget->currencies[0]->sbif_api_keyword != 'peso' && $total != 0):?>
											<td class="text-right warning">
												<?= moneda($item['Data']->currency_day_value); ?>
											</td>
											<td class="text-right warning">
												<?= moneda($currency_total); ?>
											</td>
										<?php endif;?>
									</tr>
									<?php $x++;?>
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
								<td></td>
								<th class="text-right">
									<?php echo $this->Html->tag('strong', 'TOTAL NETO'); ?>
								</th>
								<td class="text-right">
									<?php echo $this->Html->tag('strong', moneda($grand_total)); ?>
								</td>
								<td></td>
								<td class="text-right">
									<?php echo $this->Html->tag('strong', moneda($currency_grand_total).' '.$budget_item->budget->currencies[0]->plural_name); ?>
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
	<?php foreach($ordenes_compra as $orden_compra):?>

		<?php $grand_total = 0;?>
		<?php $discount = 0;?>
		<?php $net_total = 0;?>
		<?php $tax = 0;?>
		<div class="col-sm-12" id="<?= $orden_compra['OrdenCompra']->NUMOC?>">
			<div class="panel panel-material-blue-grey-700">
				<div class="panel-heading">Orden de compra N° <?= $orden_compra['OrdenCompra']->NUMOC; ?></div>

				<table class="table tabla-mascara">
					<tr>
						<th class="text-left info header-table">Nombre orden de compra</th>
						<td class="text-left body-table"><?= $orden_compra['OrdenCompra']->NOMOC; ?></td>
						<th class="text-left info header-table"></th>
						<td class="text-left body-table"></td>
					</tr>
					<tr>
						<th class="text-left info header-table">Porcentaje de IVA</th>
						<td class="text-left body-table"><?= moneda($orden_compra['OrdenCompra']->PORCIMPUESTO).'%'; ?></td>
						<th class="text-left info header-table"></th>
						<td class="text-left body-table"></td>
					</tr>
					<tr>
						<th class="text-left info header-table">Otros Impuestos</th>
						<td class="text-left body-table"><?= moneda($orden_compra['OrdenCompra']->otrosimpuestos); ?></td>
						<th class="text-left info header-table">Notas Complementarias</th>
						<td class="text-left body-table"></td>
					</tr>
					<tr>
						<th class="text-left info header-table">Orden de compra con anticipo</th>
						<td class="text-left body-table"></td>
						<th class="text-left info header-table"></th>
						<td class="text-left body-table"></td>
					</tr>
					<tr>
						<th class="text-left info header-table">Contrato Proveedores</th>
						<td class="text-left body-table">
							<?php
								if(isset($orden_compra['OrdenCompra']->ic_orden_compra_consolidado->EMPVNOMBREF))
								{
									echo $orden_compra['OrdenCompra']->ic_orden_compra_consolidado->EMPVNOMBREF;
								}
							?>
						</td>
						<th class="text-left info header-table">Giro Proveedor</th>
						<td class="text-left body-table"></td>
					</tr>
					<tr>
						<th class="text-left info header-table">Representante legal proveedor</th>
						<td class="text-left body-table"></td>
						<th class="text-left info header-table">Rut Representante legal proveedor</th>
						<td class="text-left body-table"></td>
					</tr>
					<tr>
						<th class="text-left info header-table">Plazo de confidencialidad</th>
						<td class="text-left body-table"></td>
						<th class="text-left info header-table">Plazo de garantia de productos</th>
						<td class="text-left body-table"></td>
					</tr>
					<tr>
						<th class="text-left info header-table">Plazo de entrega</th>
						<td class="text-left body-table"></td>
						<th class="text-left info header-table">Etapas de entrega</th>
						<td class="text-left body-table"></td>
					</tr>
					<tr>
						<th class="text-left info header-table">Anticipo</th>
						<td class="text-left body-table"></td>
						<th class="text-left info header-table">Forma de pago anticipo</th>
						<td class="text-left "></td>
					</tr>
					<tr>
						<th class="text-left info header-table">Porcentaje anticipo</th>
						<td class="text-left body-table"></td>
						<th class="text-left info header-table">Monto del anticipo</th>
						<td class="text-left body-table"></td>
					</tr>
					<tr>
						<th class="text-left info header-table">Tipo de garantia</th>
						<td class="text-left body-table"></td>
						<th class="text-left info header-table">Serie cheque</th>
						<td class="text-left body-table"></td>
					</tr>
					<tr>
						<th class="text-left info header-table">Banco</th>
						<td class="text-left body-table"></td>
						<th class="text-left info header-table"></th>
						<td class="text-left body-table"></td>
					</tr>
				</table>

				<br><br>

				<div class="panel-body">
					<div class="table-responsive">
						<table class="table tabla-mascara">
							<thead>
								<tr>
									<th class="text-left info">Código</th>
									<th class="text-right info">Cantidad</th>
									<th class="text-left info">Unidad</th>
									<th class="text-left info">Descripción</th>
									<th class="text-left info">Glosa</th>
									<th class="text-right info">Fecha entrega</th>
									<th class="text-right info">Días despacho</th>
									<th class="text-left info">Código C.C</th>
									<th class="text-left info">Cuenta de Costo</th>
									<th class="text-left info">Partida</th>
									<th class="text-right info">Precio Unit.</th>
									<th class="text-right info">Sub Total</th>
									<th class="text-right info">Descuento</th>
									<th class="text-right info">Valor Total</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($orden_compra['OrdenCompraItem'] as $item):?>
									<tr>
										<td class="text-left"><?= $item['Data']->IDARTICULO; ?></td>
										<td class="text-right">
											<?php $quantity = 0;?>
											<?php foreach($item['Distribucion'] as $distr): ?>
													<?php
														if($distr->TIPODISTRIB == 1)
														{
															$quantity = ($distr->VALOR / 100) * $item['Data']->CANTIDAD;
														}
														else
														{
															$quantity = $distr->VALOR;
														}
													?>
											<?php endforeach; ?>

											<?= moneda($quantity);?>
										</td>
										<td class="text-left"><?= $item['Data']->ic_uom->DESCRIPCORTA; ?></td>
										<td class="text-left"><?= $item['Data']->NOMBARTICULO; ?></td>
										<td class="text-left"><?= $item['Data']->GLOSA; ?></td>
										<td class="text-right">
											<?php 
												$date = new Date($item['Data']->FECHADESPACHO);
												echo $date->format('d-m-Y');
											?>
										</td>
										<td class="text-right"><?= $item['Data']->DIASDESPACHO; ?></td>
										<td class="text-left">

											<?php foreach($item['Distribucion'] as $distr): ?>

												<?php if(count($item['Distribucion']) > 1):?>

													<?= $distr->IDCENTCOSTO; ?>

												<?php else:?>

													<?= $distr->IDCENTCOSTO; ?><br>

												<?php endif;?>

											<?php endforeach; ?>
										</td>
										<td class="text-left">
											<?php foreach($item['Distribucion'] as $distr): ?>

												<?php if(count($item['Distribucion']) > 1):?>

													<?= $budget_item->description.' U: '.$quantity; ?>
													
												<?php else:?>

													<?= $budget_item->description.' U:'.$quantity; ?><br>

												<?php endif;?>

											<?php endforeach; ?>
										</td>
										<td class="text-left"><?= $item['Data']->DESCRIPPARTIDA; ?></td>
										<td class="text-right"><?= moneda($item['Data']->MONTOUNITARIO); ?></td>
										<td class="text-right">
											<?php
												$subtotal = 0;
												
												foreach($item['Distribucion'] as $distr)
												{
													$subtotal = $item['Data']->MONTOUNITARIO * $quantity;
												}

												echo moneda($subtotal); 
											?>	
										</td>
										<td class="text-right">
											<?php
												if($item['Data']->ic_orden_compra_item_descuento != null)
												{
													echo moneda($item['Data']->ic_orden_compra_item_descuento->VALOR);

													$discount += $item['Data']->ic_orden_compra_item_descuento->VALOR;
												}
												else
												{
													echo moneda(0);
												}
											?>
										</td>
										<td class="text-right">
											<?php
												$total = 0;

												if($item['Data']->ic_orden_compra_item_descuento != null)
												{
													$total = $subtotal - $item->ic_orden_compra_item_descuento->VALOR;
													echo moneda($total);
												}
												else
												{
													$total = $subtotal;
													echo moneda($total);
												}

												$grand_total += $total;
											?>
										</td>
									</tr>
								<?php endforeach;?>
							</tbody>
						</table>
					</div>
					<br><br>
					<table class="table tabla-mascara">
						<tr>
							<th class="text-right" colspan="14">
								<?php echo $this->Html->tag('strong', 'Monto total'); ?>
							</th>
							<td class="text-right header-table">
								<?php echo $this->Html->tag('strong', moneda($grand_total), array('class' => 'text-info')); ?>
							</td>
						</tr>
						<tr>
							<th class="text-right" colspan="14">
								<?php echo $this->Html->tag('strong', 'Descuentos'); ?>
							</th>
							<td class="text-right header-table">
								<?php echo $this->Html->tag('strong', moneda($discount), array('class' => 'text-info')); ?>
							</td>
						</tr>
						<tr>
							<th class="text-right" colspan="14">
								<?php echo $this->Html->tag('strong', 'Cargos'); ?>
							</th>
							<td class="text-right header-table">
								<?php 
									if($item['Data']->ic_orden_compra_cargo != null)
									{
										echo $this->Html->tag('strong', moneda($item['Data']->ic_orden_compra_cargo->VALOR), array('class' => 'text-info'));
									}
									else
									{
										echo $this->Html->tag('strong', moneda(0), array('class' => 'text-info'));
									}
								?>
							</td>
						</tr>
						<tr>
							<th class="text-right" colspan="14">
								<?php echo $this->Html->tag('strong', 'Total neto'); ?>
							</th>
							<td class="text-right header-table">
								<?php 
									if($item['Data']->ic_orden_compra_cargo != null)
									{
										$net_total = ($grand_total - $discount) + $item['Data']->ic_orden_compra_cargo->VALOR;
									}
									else
									{
										$net_total = ($grand_total - $discount);
									}

									echo $this->Html->tag('strong', moneda($net_total), array('class' => 'text-info'));
								?>
							</td>
						</tr>

						<tr>
							<th class="text-right" colspan="14">
								<?php echo $this->Html->tag('strong', 'IVA ('.$orden_compra['OrdenCompra']->PORCIMPUESTO.'%)'); ?>
							</th>
							<td class="text-right header-table">
								<?php 
									$tax = ($orden_compra['OrdenCompra']->PORCIMPUESTO/100) * $net_total;

									echo $this->Html->tag('strong', moneda($tax), array('class' => 'text-info'));
								?>
							</td>
						</tr>

						<tr>
							<th class="text-right" colspan="14">
								<?php echo $this->Html->tag('strong', 'Total'); ?>
							</th>
							<td class="text-right header-table">
								<?php 
									echo $this->Html->tag('strong', moneda($net_total + $tax), array('class' => 'text-info'));
								?>
							</td>
						</tr>


					</table>
				</div>
			</div>
		</div>
	<?php endforeach;?>
</div>

<?php //debug($ordenes_compra);?>