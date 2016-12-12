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
	            	$('#row-extend').attr('colspan',10);
	            	$('#action-currency').removeClass('btn-info').addClass('btn-danger');
	           	}
	           	else
	           	{
	           		$("#action-currency").html('Mostrar valores en '+<?= "'".$budget_item->budget->currencies[0]->name."'";?>);
	           		$('td:nth-child(11),th:nth-child(11)').hide();
	            	$('td:nth-child(12),th:nth-child(12)').hide();
	            	$('#row-extend').attr('colspan',8);
	            	$('#action-currency').removeClass('btn-danger').addClass('btn-info');
	           	}
	        });
	    });
	</script>
<?php endif;?>

<?php $this->assign('title_text', __('Subcontratos Comprometidos')); ?>

<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<div class="row">
					<div class="col-sm-1">
						<?php echo $this->Html->link(__('Volver'), '/spends/overview/'.$budget_item->budget_id, array('class' => 'btn btn-sm btn-primary'));?>
					</div>
					<div class="col-sm-9">
						Resumen de subcontratos para la partida <?= $this->Html->tag('b', $budget_item->item.' - '.$budget_item->description); ?><br><small><i>Esto considera todos los items comprometidos de los subcontratos que esten asociados a la partida <?= $budget_item->item;?></i></small>
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
								<th class="text-right info">Fecha del Subcontrato</th>
								<th class="text-left info">Tipo</th>
								<th class="text-left info">Subcontratista</th>
								<th class="text-right info">Precio Unitario</th>
								<th class="text-right info">Cantidad</th>
								<th class="text-left info">Unidad</th>
								<th class="text-left info">Descripción</th>
								<th class="text-right info">Total Contrato</th>
								<?php if($budget_item->budget->currencies[0]->sbif_api_keyword != 'peso'):?>
									<th class="text-right warning">Valor de <?= $budget_item->budget->currencies[0]->name;?></th>
									<th class="text-right warning">Total Contrato en <?= $budget_item->budget->currencies[0]->name;?></th>
								<?php endif;?>
								<th class="text-right info">Total avance</th>
								<th class="text-right info">Saldo</th>
							</tr>
						</thead>
						<tbody>
							<?php $grand_total = 0; $total_advanced = 0; $x=1; $currency_grand_total = 0;?>
							<?php foreach($subcontratos as $subcontrato):?>
								<?php foreach($subcontrato['SubcontratoItem'] as $item):?>
									<tr>
										<td class="text-left"><?= $x; ?></td>
										<td class="text-right">
											<?= $this->Html->link(
												$subcontrato['Subcontrato']->NUMDOC, 
												'#'.$subcontrato['Subcontrato']->NUMDOC,
												array(
													'onclick' => 'Javascript:openAccordion('.$subcontrato['Subcontrato']['IDDOC'].')'
												)
											);?>	
										</td>
										<td class="text-right"><?= $subcontrato['Subcontrato']->FECHACREACION->format('d-m-Y'); ?></td>
										<td class="text-left"><?= $subcontrato['Subcontrato']->ic_subcontrato_tipo->DESCRIPCION;?></td>
										<td class="text-left"><?= $subcontrato['Subcontrato']->ic_subcontrato_consolidado->EMPVRAZONSOCIAL;?></td>
										<td class="text-right"><?= moneda($item['Data']->MONTOUNITARIO); ?></td>
										<td class="text-right">
											<?php
												$porc = 0;
												$porc = ($item['Distribucion'][0]->VALOR / 100) * $item['Data']->CANTIDAD;
												echo moneda($porc); 
											?>
										</td>
										<td class="text-left"><?= $item['Data']->ic_uom->DESCRIPCORTA; ?></td>
										<td class="text-left"><?= $item['Data']->DESCRIPCION; ?></td>

										<?php
											$total_resu = 0;
											$total_resu = $porc * $item['Data']->MONTOUNITARIO;
										?>

										<td class="text-right"><?= moneda($total_resu); ?></td>

										<?php
											if($budget_item->budget->currencies[0]->sbif_api_keyword != 'peso')
											{
												//echo $this->cell('Spend::showAndTransformValues', [$subcontrato['Subcontrato']->FECHACREACION->format('Y-m-d'), $budget_item->budget->currencies[0]->sbif_api_keyword, $total_resu]);
												$currency_total = $total_resu / $subcontrato['Subcontrato']['currency_day_value'];

												$currency_grand_total += $currency_total;

												echo '<td class="text-right warning">'.moneda($subcontrato['Subcontrato']['currency_day_value']).'</td>';
												echo '<td class="text-right warning">'.moneda($currency_total).'</td>';
											}
										?>

										<!--<td class="text-right">
											<?php 
												if($item['Data']->CANTIDADAVANCE == 0)
												{
													echo moneda($item['Data']->CANTIDADAVANCE).'%';
												}
												else
												{
													$advanced_quantity = (100 / $item['Data']->CANTIDADAVANCE) * $porc;
													echo moneda($advanced_quantity).'%'; 
												}
											?>
										</td>-->
			  							<td class="text-right">
			  								<?php
			  									$advanced = 0;
			  									$advanced = ($item['Distribucion'][0]->VALOR / 100) * $item['Data']->MONTOAVANCE;
			  									echo moneda($advanced); 
			  								?>
			  							</td>
			  							<td class="text-right"><?= moneda($total_resu - (($item['Distribucion'][0]->VALOR / 100) * $item['Data']->MONTOAVANCE)); ?></td>
									</tr>

									<?php
										$grand_total += $total_resu;
										$total_advanced += $advanced;
										$x++;
									?>

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
									<?php echo $this->Html->tag('strong', moneda($grand_total), array('class' => 'text-right', 'style' => 'margin:0px;')); ?>
								</td>
								<td></td>
								<td class="text-right">
									<?php echo $this->Html->tag('strong', moneda($currency_grand_total).' '.$budget_item->budget->currencies[0]->plural_name, array('class' => 'text-right', 'style' => 'margin:0px;')); ?>
								</td>
								<td class="text-right">
									<?php echo $this->Html->tag('strong', moneda($total_advanced), array('class' => 'text-right', 'style' => 'margin:0px;')); ?>
								</td>
								<td class="text-right">
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<br>

<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

	<?php foreach($subcontratos as $subcontrato):?>


		<div class="panel panel-material-blue-grey-700" id="<?= $subcontrato['Subcontrato']->NUMDOC;?>">

			<?php 
				$total = 0;
				$porc = 0;
				foreach($subcontrato['SubcontratoItem'] as $item)
				{
					$porc = ($item['Distribucion'][0]->VALOR / 100) * $item['Data']->CANTIDAD;
					$total += $porc * $item['Data']->MONTOUNITARIO;
				}
			?>
			<?php
				echo $this->Html->div('panel-heading',

					$this->Html->tag('h4', 

						$this->Html->link(
							$this->Html->tag('strong', $this->Html->tag('span', '', array('class' => 'glyphicon glyphicon-file')).'  '.$subcontrato['Subcontrato']['NUMDOC'].' '.$subcontrato['Subcontrato']['NOMDOC'].' ', array('class' => 'pull-center accordion-title')).' '.

							$this->Html->tag('span', '$'.moneda($total), array('class' => 'label pull-right label-success hidden-sm hidden-xs')).
							$this->Html->tag('span', $subcontrato['Subcontrato']['ic_subcontrato_consolidado']['EMPVNOMBREF'], array('class' => 'label label-primary pull-right')),

							'#'.$subcontrato['Subcontrato']['IDDOC'], 
								array(
									'escape' => false, 
									'class' => 'collapsed', 
									'role' => 'button', 
									'data-toggle' => 'collapse', 
									'data-parent' => '#accordion', 
									'aria-expanded' => 'false', 
									'aria-controls' => $subcontrato['Subcontrato']['IDDOC']
								)
						),
						array(
							'class' => 'panel-title'
						)
					),

					array(
						'role' => 'tab',
						'id' => 'header-'. $subcontrato['Subcontrato']['IDDOC']
					)
				);
			?>

			<div <?= 'id="'.$subcontrato['Subcontrato']['IDDOC'].'"'?> class="panel-collapse collapse" role="tabpanel" <?= 'aria-labelledby="header-'.$subcontrato['Subcontrato']['IDDOC'].'"'?>>
			  	<div class="panel-body">

			  		<div class="panel panel-default">
					  	<div class="panel-body">
					  		<div class="row">
					  			<div class="col-sm-6">
					  				<dl class="dl-horizontal">
									  	<dt>Nº Subcontrato:</dt>
									  	<dd><?= $subcontrato['Subcontrato']['NUMDOC'];?></dd>

									  	<dt>Centro de gestión:</dt>
									  	<dd><?= $subcontrato['Subcontrato']['ic_subcontrato_consolidado']['ORGCNOMBRE'];?></dd>

									  	<dt>Tipo subcontrato:</dt>
									  	<dd><?= $subcontrato['Subcontrato']['ic_subcontrato_tipo']['DESCRIPCION'];?></dd>
									</dl>
					  			</div>
					  			<div class="col-sm-6">
					  				<dl class="dl-horizontal">
									  	<dt>Nombre subcontratista:</dt>
									  	<dd><?= $subcontrato['Subcontrato']['ic_subcontrato_consolidado']['EMPVNOMBREF'];?></dd>

									  	<dt>Rut subcontratista:</dt>
									  	<dd><?= $subcontrato['Subcontrato']['RUTORGV'];?></dd>
									</dl>
					  			</div>
					  		</div>
					    	
					  	</div>
					</div>
					
					<br>

			    	<div class="table-responsive">
		  				<table class="table tabla-mascara table-hover">
		  					<thead>
		  						<tr>
		  							<th class="text-left info">Descripción</th>
		  							<th class="text-right info">Precio Unitario</th>
		  							<th class="text-right info">Cantidad</th>
		  							<th class="text-left info">Unidad</th>
									<th class="text-right info">Total Contrato</th>
									<!--<th class="text-right info">% De Avance</th>-->
									<th class="text-right info">Total avance</th>
									<th class="text-right info">Saldo</th>
								</tr>
		  					</thead>
		  					<tbody>
		  						<?php $total = 0; $total_advanced = 0;?>
		  						<?php foreach($subcontrato['SubcontratoItem'] as $item):?>
			  						<tr>
			  							<td class="text-left"><?= $item['Data']->DESCRIPCION; ?></td>
			  							<td class="text-right"><?= moneda($item['Data']->MONTOUNITARIO); ?></td>
			  							<td class="text-right">
			  								<?php 
			  									$porc = 0;
												$porc = ($item['Distribucion'][0]->VALOR / 100) * $item['Data']->CANTIDAD;
												echo moneda($porc); 
			  								?>	
			  							</td>
			  							<td class="text-left"><?= $item['Data']->ic_uom->DESCRIPCORTA; ?></td>
			  							
			  							<td class="text-right">
			  								<?php
			  									$total_resu = 0;
												$total_resu = $porc * $item['Data']->MONTOUNITARIO;
												echo moneda($total_resu); 
			  								?>
			  							</td>
			  							<!--<td class="text-right">
			  								<?php 
												if($item['Data']->CANTIDADAVANCE == 0)
												{
													echo moneda($item['Data']->CANTIDADAVANCE).'%';
												}
												else
												{
													$advanced_quantity = (100 / $item['Data']->CANTIDADAVANCE) * $porc;
													echo moneda($advanced_quantity).'%'; 
												}
											?>
			  							</td>-->
			  							<td class="text-right">
			  								<?php
			  									$advanced = 0;
			  									$advanced = ($item['Distribucion'][0]->VALOR / 100) * $item['Data']->MONTOAVANCE;
			  									echo moneda($advanced); 
			  								?>
			  							</td>
			  							<td class="text-right"><?= moneda($total_resu - (($item['Distribucion'][0]->VALOR / 100) * $item['Data']->MONTOAVANCE)); ?></td>
			  						</tr>
			  						<?php 
			  							$total += $total_resu;
			  							$total_advanced += $advanced;
			  						?>

			  					<?php endforeach; ?>

			  					<tr>
		  							<td></td>
		  							<td></td>
		  							<td></td>
		  							<td class="text-left"><?= $this->Html->tag('strong', __('TOTAL NETO'));?></td>
		  							<td class="text-right"><?= $this->Html->tag('strong', moneda($total));?></td>
		  							<td class="text-right"><?= $this->Html->tag('strong', moneda($total_advanced));?></td>
		  							<td></td>
		  						</tr>
		  				 	</tbody>
		  				</table>
					</div>
			  	</div>
			</div>
		</div>

	<?php endforeach;?>
</div>

<script>
	function openAccordion(sub_id)
	{
		$('#'+sub_id).collapse('toggle');

		var $myGroup = $('#accordion');
		$myGroup.on('show.bs.collapse','.collapse', function() {
		    $myGroup.find('.collapse.in').collapse('hide');
		});
	}
</script>