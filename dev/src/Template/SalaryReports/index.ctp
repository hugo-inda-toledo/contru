<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Recursos Humanos'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
    <!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Listado Reportes Remuneraciones de Trabajadores</h3>
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
            	<?= $this->Html->link(__('Asistencia Mensual'), ['controller' => 'assists', 'action' => 'assist_month_detail', $budget->id, (!empty($assistance_date)) ? $assistance_date->format('Y-m-d') : ''],
		       	 ['class' => 'btn btn-material-orange-900 pull-right btn-md']) ?>
            </div>
        </div>
		<div class="row">
        	<div class="col-lg-12">
		        <h4>Mes Asistencia: <strong><?= $assistance_date->format('F Y'); ?></strong></h4>
				<hr>
				<?php if (!$salary_reports->isEmpty()) : ?>
					<table cellpadding="0" cellspacing="0" class="table table-striped table-hover">
						<thead>
							<tr>
								<th>Trabajador</th>
								<th>Total imponible</th>
								<th>Viáticos</th>
								<th>Total no imponible</th>
								<th>Total Haberes</th>
								<th>Otros Descuentos</th> <!-- input -->
			                    <th>Total Descuentos</th>
			                    <th>Líquido a Pago</th>
								<th><?= __('Acciones') ?></th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($salary_reports->toArray() as $salary_report) : ?>
							<tr>
								<td><?= $workers[$salary_report['worker_id']]['nombres'] ?></td>
								<td nowrap><?= moneda($salary_report['total_taxable']) ?></td>
								<td nowrap><?= moneda($salary_report['travel_expenses']) ?></td>
								<td nowrap><?= moneda($salary_report['total_not_taxable']) ?></td>
								<td nowrap><?= moneda($salary_report['total_assets']) ?></td>
								<td nowrap><?= moneda($salary_report['other_discounts']) ?></td>
								<td nowrap><?= moneda($salary_report['total_discounts']) ?></td>
								<td nowrap><?= moneda($salary_report['liquid_to_pay']) ?></td>
								<td>

								</td>
							</tr>
						<?php endforeach;  ?>
						</tbody>
					</table>
				<?php else : ?>
					<h4>Sin reportes de remuneraciones para este mes</h4>
				<?php endif; ?>
    			</div>
    		</div>
    	</div>
    </div>
</div>
