<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Recursos Humanos'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>
<style>
.table-scroll-1, .table-scroll{overflow-x: scroll; overflow-y:hidden;}
.table-scroll-1{height: 20px; }
.scroller {height: 20px; }
</style>
<div class="panel panel-material-blue-grey-700">
    <!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Reporte Remuneraciones de Trabajadores</h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <?= $this->Element('info_budget_building'); ?>
        <div class="row">
            <div class="col-lg-6">
            <?php
	            $group_id = $this->request->session()->read('Auth.User.group_id');
	            if ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) : ?>
	                <?php echo $this->Form->create('Budgets', ['class' => 'form-horizontal', 'type' => 'get']); ?>
	                    <div class="col-lg-12">
	                        <div class="col-lg-6">
	                            <?php echo $this->Form->input('months', ['label' => 'Mes', 'empty' => 'Seleccione un mes', 'options' => $months,
	                             'value' => (!empty($this->request->query['months']) ? $this->request->query['months'] : (!empty($assistance_date)) ? $assistance_date->format('Y_m') : '')]); ?>
	                        </div>
	                        <div class="col-lg-6">
	                            <?php echo $this->Form->button('Buscar', ['type' => 'submit']); ?>
	                        </div>
	                    </div>
                <?php echo $this->Form->end();
	            else :
	                echo $this->Form->create('Budgets', ['class' => 'form-horizontal', 'type' => 'get']); ?>
	                    <div class="col-lg-12">
	                        <div class="col-lg-6">
	                            <?php echo $this->Form->input('building_id', ['label' => 'Área de Negocio', 'empty' => 'Seleccione una Obra', 'options' => $buildings, 'value' => $budget->building_id]); ?>
	                        </div>
	                        <div class="col-lg-4">
	                            <?php echo $this->Form->input('months', ['label' => 'Mes', 'empty' => 'Seleccione una Fecha', 'options' => $months,
	                             'value' => (!empty($this->request->query['months']) ? $this->request->query['months'] : (!empty($assistance_date)) ? $assistance_date->format('Y_m') : '')]); ?>
	                        </div>
	                        <div class="col-lg-2">
	                            <?php echo $this->Form->button('Buscar', ['type' => 'submit']); ?>
	                        </div>
	                    </div>
	                <?php echo $this->Form->end();
           		endif; ?>
            </div>
            <div class="col-lg-6">
            	<?= $this->Html->link(__('Asistencia Mensual'), ['action' => 'assist_month_detail', $budget->id, (!empty($assistance_date)) ? $assistance_date->format('Y-m-d') : ''],
		       	 ['class' => 'btn btn-material-orange-900 pull-right btn-md']) ?>
            </div>
        </div>
     	<div class="row">
        	<div class="col-lg-12">
		        <h4>Mes Asistencia: <strong><?= $assistance_date->format('F Y'); ?></strong></h4>
		       	<?= $this->Element('assists_types_explanation'); ?>
	      		<div class="table-scroll-1">
	      			<div class="scroller"></div>
	      		</div>
		        <div class="table-scroll">
			        <table class="table table-striped table-condensed table-hover table-bordered assist-detail">
			            <thead>
			                <tr>
			                	<th>N°</th>
			                    <th>Nombre</th>
			                    <!-- <th>Rut</th> -->
			                    <th>Cargo</th>
								<th>Fecha Ingreso</th>
			                    <?php foreach ($month_days as $day) : ?>
			                    	<th><?= $day ?></th>
			                    <?php endforeach; ?>
			                    <th class="success">D.T.</th>
			                    <th class="success">Sueldo Base</th>
			                    <th class="success">Sueldo Mes</th>
			                    <th class="success">Horas Extras</th>
			                    <th class="success">Valor H.Extra</th>
			                    <th class="success">Pago H.Extra</th>
								<th class="success">Bonos</th>
								<th class="success">Tratos</th>
			                    <th class="success">Gratificación</th> <!-- input -->
			                    <th class="success">Otros Imponibles</th> <!-- input -->
			                    <th class="success">Aguinaldo</th> <!-- input -->
			                    <th class="success">Total Imponible</th>
			                    <th class="info">Asig. Familiar</th>
			                    <th class="info">Asig. Familiar R</th>
			                    <th class="info">Asig. Movilización</th>
			                    <th class="info">Movilización</th>
			                    <th class="info">Asig. Colación</th>
			                    <th class="info">Colación</th>
			                    <th class="info">Viático (ingresar)</th> <!-- input -->
			                    <th class="info">V</th>
			                    <th class="info">T</th>
			                    <th class="info">B</th>
			                    <th class="info">VM2</th>
			                    <th class="info">VM</th>
			                    <th class="danger">Total No Imponible</th>
			                    <th class="danger">Total Haberes</th>
			                    <th class="warining">AFP</th>
			                    <th class="warining">AFP %</th>
			                    <th class="warining">AFP Valor</th>
			                    <th class="warining">Salud</th>
			                    <th class="warining">Isapre</th>
			                    <th class="warining">Isapre Dif.</th>
			                    <th class="warining">Isapre Valor</th>
			                    <th class="warining">Seg. Cesantía</th>
			                    <th class="warining">Imp. Único</th>
			                    <th class="warining">Aguinaldo pagado</th>
			                    <th class="warining">Anticipo</th> <!-- input -->
			                    <th class="warining">Otros Descuentos</th> <!-- input -->
			                    <th class="warining">Total Desc.</th>
			                    <th class="active">Líquido a Pago</th>
			                </tr>
			            </thead>
			            <tbody>
			                <?php
			                $i=0;
							echo $this->Form->create($salaryReport);
							echo $this->Form->hidden('budget_id', ['value' => $budget->id]);
			                foreach ($salaries_month_data as $worker_id => $worker_salary_data) :
								if (!is_int($worker_id)) :
					                break;
					            endif;
					            $i++;
								echo $this->Form->hidden('Worker.' . $worker_id . '.worker_id', ['value' => $worker_id]); ?>
			                    <tr>
			                    	<td><h6><?= $i; ?></h6></td>
			                        <td><h6><?= $worker_salary_data['nombres'] ?></h6></td>
			                        <!-- <td nowrap><h6><?= $worker_salary_data['rut'] ?></h6></td> -->
			                        <td><h6><small><strong><?= $worker_salary_data['Cargo']['nombre_cargo'] ?></strong></small></h6></td>
									<td nowrap><h6><?php //echo $worker_salary_data['Ficha']['fechaIngreso']->format('d-m-Y') ?></h6></td>
		                			<?php
			                			foreach ($worker_salary_data['Assists']['assists'] as $assists) :
		                					if (empty($assists['status'])) : ?>
		                						<td>
	                								<span class="label label-<?= $assists['class'] ?>"><?= $assists['value'] ?></span>
		                						</td>
		                				<?php else :  ?>
		                					<td><?= $assists['status'] ?></td>
		                				<?php endif;
		                			endforeach; ?>
			                		<td class="success" nowrap><?= round($worker_salary_data['Salary']['days_worked'], 0) /*. ' (' . $worker_salary_data['Salary']['total_calculated_hours'] . ' horas)'*/; ?></td>
			                		<td class="success" nowrap><?= moneda($worker_salary_data['Salary']['base_salary']) ?></td>
			                		<td class="success" nowrap><?= moneda($worker_salary_data['Salary']['month_salary']) ?></td>
			                		<td class="success" nowrap><?= $worker_salary_data['Salary']['month_overtime_hours'] ?></td>
			                		<td class="success" nowrap><?= moneda($worker_salary_data['Salary']['overtime_hours_value']) ?></td>
			                		<td class="success" nowrap><?= moneda($worker_salary_data['Salary']['overtime_payout']) ?></td>
			                		<td class="success" nowrap><?= moneda($worker_salary_data['Assists']['bonuses'])?></td>
			                		<td class="success" nowrap><?= moneda($worker_salary_data['Assists']['deals'])?></td>
			                		<td class="success" nowrap><?= moneda($worker_salary_data['Salary']['gratification'])?></td>
			                		<td class="success" nowrap><?= moneda($worker_salary_data['Salary']['other_taxables'])?></td>
			                		<td class="success" nowrap><?= moneda($worker_salary_data['Salary']['year_end_bonus'])?></td>
			                		<td class="success total_taxable" nowrap>
										<strong><?= moneda($worker_salary_data['Salary']['total_taxable'])?></strong>
										<?= $this->Form->hidden('Worker.' . $worker_id . '.Salary.total_taxable', ['label' => false, 'type' => 'number', 'min' => 0, 'data-type' => 'total_taxable',
											'data-original' => $worker_salary_data['Salary']['total_taxable'], 'value' => $worker_salary_data['Salary']['total_taxable']]); ?>
									</td>
			                		<td class="info" nowrap><?= moneda($worker_salary_data['Salary']['allocation_family']) ?></td>
			                		<td class="info" nowrap><?= moneda($worker_salary_data['Salary']['allocation_family_retro'])?></td>
			                		<td class="info" nowrap><?= moneda($worker_salary_data['Salary']['allocation_mobilization'])?></td>
			                		<td class="info" nowrap><?= moneda($worker_salary_data['Salary']['mobilization'])?></td>
			                		<td class="info" nowrap><?= moneda($worker_salary_data['Salary']['allocation_lunch'])?></td>
			                		<td class="info" nowrap><?= moneda($worker_salary_data['Salary']['lunch'])?></td>
			                		<td class="info travel_expenses" nowrap>
										<?= $this->Form->input('Worker.' . $worker_id . '.Salary.travel_expenses', ['label' => false, 'type' => 'number', 'min' => 0, 'data-type' => 'travel_expenses',
											'data-original' => $worker_salary_data['Salary']['travel_expenses'], 'value' => [$worker_salary_data['Salary']['travel_expenses']]
										]);?>
									</td>
									<td class="info" nowrap>&nbsp;N/A</td>
									<td class="info" nowrap>&nbsp;N/A</td>
									<td class="info" nowrap>&nbsp;N/A</td>
									<td class="info" nowrap>&nbsp;N/A</td>
									<td class="info" nowrap>&nbsp;N/A</td>
									<td class="danger total_not_taxable" nowrap>
										<span><?= moneda($worker_salary_data['Salary']['total_not_taxable']); ?></span>
										<?= $this->Form->hidden('Worker.' . $worker_id . '.Salary.total_not_taxable', ['label' => false, 'type' => 'number', 'min' => 0, 'data-type' => 'total_not_taxable',
											'data-original' => $worker_salary_data['Salary']['total_not_taxable'], 'value' => $worker_salary_data['Salary']['total_not_taxable']]); ?>
									</td>
									<td class="danger total_assets" nowrap>
										<span><?= moneda($worker_salary_data['Salary']['total_assets']);?></span>
										<?= $this->Form->hidden('Worker.' . $worker_id . '.Salary.total_assets', ['label' => false, 'type' => 'number', 'min' => 0, 'data-type' => 'total_assets',
											'data-original' => $worker_salary_data['Salary']['total_assets'], 'value' => $worker_salary_data['Salary']['total_assets']]); ?>
									</td>
									<td class="warning" nowrap><?= $worker_salary_data['Salary']['afp_name'] ?></td>
									<td class="warning" nowrap><?= $worker_salary_data['Salary']['afp_percent'] ?></td>
									<td class="warning" nowrap><?= moneda($worker_salary_data['Salary']['afp_discount']) ?></td>
									<td class="warning" nowrap><?= moneda($worker_salary_data['Salary']['health_discount']) ?></td>
									<td class="warning" nowrap><?= $worker_salary_data['Salary']['isapre_name'] ?></td>
									<td class="warning" nowrap><?= $worker_salary_data['Salary']['isapre_diff'] ?></td>
									<td class="warning" nowrap><?= $worker_salary_data['Salary']['isapre_discount'] ?></td>
									<td class="warning" nowrap><?= 0 ?></td>
                                    <td class="warning" nowrap><?= moneda($worker_salary_data['Salary']['unique_tax']) ?></td>
									<td class="warning" nowrap><?= moneda($worker_salary_data['Salary']['year_end_bonus_paid']) ?></td>
									<td class="warning" nowrap><?= moneda($worker_salary_data['Salary']['advances']) ?></td>
                                    <td class="warning other_discounts" nowrap>
                                        <?= $this->Form->input('Worker.' . $worker_id . '.Salary.other_discounts', ['label' => false, 'type' => 'number', 'min' => 0, 'data-type' => 'other_discounts',
											'data-original' => 0, 'value' => 0
										]); //TODO: valor real ?>
                                    </td>
									<td class="warning total_discounts" nowrap>
										<span><?= moneda($worker_salary_data['Salary']['total_discounts']); ?></span>
										<?= $this->Form->hidden('Worker.' . $worker_id . '.Salary.total_discounts', ['label' => false, 'type' => 'number', 'min' => 0, 'data-type' => 'total_discounts',
											'data-original' => $worker_salary_data['Salary']['total_discounts'], 'value' => $worker_salary_data['Salary']['total_discounts']]); ?>
									</td>
									<td class="active liquid_to_pay" nowrap>
										<span><?= moneda($worker_salary_data['Salary']['liquid_to_pay']); ?></span>
										<?= $this->Form->hidden('Worker.' . $worker_id . '.Salary.liquid_to_pay', ['label' => false, 'type' => 'number', 'min' => 0, 'data-type' => 'liquid_to_pay',
											'data-original' => $worker_salary_data['Salary']['liquid_to_pay'], 'value' => $worker_salary_data['Salary']['liquid_to_pay']]); ?>
									</td>
			                    </tr>
			                <?php endforeach; ?>
			            </tbody>
                  	</table>
              	</div>
		        <?php
				echo $this->Form->button(__('Guardar'));
				echo $this->Form->end();
	            $group_id = $this->request->session()->read('Auth.User.group_id');
	            echo ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) ?
             	$this->Html->link(__('Volver'), ['action' => 'index', '?' => ['months' => $assistance_date->format('Y_m')]], ['class' => 'btn btn-flat btn-link']) :
             	$this->Html->link(__('Volver'), ['action' => 'index', '?' => ['building_id' => $budget->building_id, 'months' => $assistance_date->format('Y_m')]], ['class' => 'btn btn-flat btn-link']); ?>
		 	</div>
  		</div>
	</div>
</div>
<?= $this->Html->script('assists.salaries_report'); ?>
