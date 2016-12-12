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
        <h3 class="panel-title">Reporte Asistencia Mensual de Trabajadores</h3>
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
				<?= $this->Html->link(__('Reporte Cálculo Remuneraciones'), ['action' => 'salaries_report', $budget->id, (!empty($assistance_date)) ? $assistance_date->format('Y-m-d') : ''],
				 ['class' => 'btn btn-material-orange-900 btn-md pull-right']) ?>
            </div>
        </div>
     	<div class="row">
        	<div class="col-lg-12">
		        <h4>Mes Asistencia: <strong><?= $assistance_date->format('F Y'); ?></strong></h4>
		       	<?= $this->Element('assists_types_explanation'); ?>
				<!-- Nav tabs -->
				<ul class="nav nav-tabs btn-material-orange-900" role="tablist">
		  			<li role="presentation" class="active shadow-z-2"><a href="#assist_month_detail" aria-controls="home" role="tab" data-toggle="tab">Asistencia Mensual</a></li>
			  		<li role="presentation"><a href="#monthly_overtime_delays" aria-controls="monthly_overtime_delays" role="tab" data-toggle="tab">Horas Extras y Atrasos</a></li>
			  		<li role="presentation"><a href="#monthly_deals" aria-controls="monthly_deals" role="tab" data-toggle="tab">Tratos</a></li>
			  		<li role="presentation"><a href="#monthly_bonuses" aria-controls="monthly_bonuses" role="tab" data-toggle="tab">Bonos</a></li>
				</ul>
				<!-- Tab panes -->
			    <div class="tab-content">
			      	<div role="tabpanel" class="tab-pane active" id="assist_month_detail">
			      		<div class="table-scroll-1">
			      			<div class="scroller"></div>
			      		</div>
					  	<div class="table-scroll">
						  	<table class="table table-striped table-condensed table-hover assist-detail">
							  	<thead id="header_table">
							  		<tr>
									  	<th>Trabajador</th>
									  	<th>Rut</th>
									  	<th>Cargo</th>
									  	<?php foreach ($month_days as $day) : ?>
										  	<th><?= $day ?></th>
									  	<?php endforeach; ?>
									  	<th>Total Horas</th>
									  	<th>Horas Extras</th>
									  	<th>Horas Atraso</th>
									  	<th>Asist.</th>
									  	<th>Perm.</th>
									  	<th>Fallas</th>
										<th>Lic. Compin</th>
									  	<th>Lic. ACHS</th>
									  	<th>Cesac.</th>
									  	<th>Inc. Trab.</th>
									  	<th>Mov. Pers.</th>
									  	<th>Total Tratos</th>
									  	<th>Total Bonos</th>
								  	</tr>
							  	</thead>
							  	<tbody>
							  	<?php foreach ($assist_month_data as $worker_id => $worker_assists_data) : ?>
							  		<tr>
									  	<td><h6><?= $workers[$worker_id]['nombres'] ?></h6></td>
									  	<td nowrap><h6><?= $workers[$worker_id]['rut'] ?></h6></td>
									  	<td><h6><small><strong><?= $workers[$worker_id]['Cargo']['nombre_cargo'] ?></small></strong></h6></td>
									  	<?php
										foreach ($worker_assists_data['assists'] as $assists) :
										  	if (empty($assists['status'])) : ?>
										  		<td><span class="label label-<?= $assists['class'] ?>"><?= $assists['value'] ?></span></td>
									  		<?php else :  ?>
										  		<td><?= $assists['status'] ?></td>
									  		<?php endif;
								  		endforeach; ?>
								  		<td><strong><?= $worker_assists_data['assist_data']['total_hours'] ?></strong></td>
								  		<td><strong><?= $worker_assists_data['assist_data']['total_overtime_hours'] ?></strong></td>
								  		<td><strong><?= $worker_assists_data['assist_data']['total_delay_hours'] ?></strong></td>
									  	<td><?= $worker_assists_data['assist_data']['total_assists'] ?></td>
									  	<td><?= $worker_assists_data['assist_data']['total_permits'] ?></td>
									  	<td><?= $worker_assists_data['assist_data']['total_fails'] ?></td>
									  	<td><?= $worker_assists_data['assist_data']['total_license_compin'] ?></td>
									  	<td><?= $worker_assists_data['assist_data']['total_license_achs'] ?></td>
									  	<td><?= $worker_assists_data['assist_data']['total_layoffs'] ?></td>
									  	<td><?= $worker_assists_data['assist_data']['total_new_worker'] ?></td>
									  	<td><?= $worker_assists_data['assist_data']['total_worker_movement'] ?></td>
									  	<td><?= $worker_assists_data['deals'] ?></td>
									  	<td><?= $worker_assists_data['bonuses'] ?></td>
							  		</tr>
						  		<?php endforeach; ?>
					  			</tbody>
				  			</table>
			  			</div>
			      	</div>
			      	<div role="tabpanel" class="tab-pane" id="monthly_overtime_delays">
						<div class="table-scroll">
						  	<table class="table table-striped table-condensed table-hover assist-detail">
							  	<thead>
							  		<tr>
									  	<th>Trabajador</th>
									  	<th>Rut</th>
									  	<th>Cargo</th>
									  	<?php foreach ($month_days as $day) : ?>
										  	<th><?= $day ?></th>
									  	<?php endforeach; ?>
									  	<th>Total Horas</th>
									  	<th>Horas Extras</th>
									  	<th>Horas Atraso</th>
									  	<th>Cálculo H.E</th>
								  	</tr>
							  	</thead>
							  	<tbody>
							  	<?php foreach ($assist_month_data as $worker_id => $worker_assists_data) : ?>
							  		<tr>
									  	<td><h6><?= $workers[$worker_id]['nombres'] ?></h6></td>
									  	<td nowrap><h6><?= $workers[$worker_id]['rut'] ?></h6></td>
									  	<td><h6><small><strong><?= $workers[$worker_id]['Cargo']['nombre_cargo'] ?></small></strong></h6></td>
									  	<?php
										foreach ($worker_assists_data['assists'] as $assists) :
										  	if (empty($assists['status'])) : ?>
										  		<td>
                                                    <?php if ($assists['overtime_hours'] > 0 ) :  ?>
			                                           <span class="label label-primary">H.E: <?= $assists['overtime_hours'] ?></span>
                                                    <?php else : ?>
                                                       <span>H.E: <?= $assists['overtime_hours'] ?></span><br>
                                                   <?php endif; ?>
                                                    <?php if ($assists['delay_hours'] > 0 ) :  ?>
                                                       <span class="label label-danger">H.A: <?= $assists['delay_hours'] ?></span>
                                                    <?php else : ?>
                                                       <span>H.A: <?= $assists['delay_hours'] ?></span>
                                                   <?php endif; ?>
												</td>
									  		<?php else :  ?>
										  		<td><?= $assists['status'] ?></td>
									  		<?php endif;
								  		endforeach; ?>
								  		<td><strong><?= $worker_assists_data['assist_data']['total_hours'] ?></strong></td>
								  		<td><strong><?= $worker_assists_data['assist_data']['total_overtime_hours'] ?></strong></td>
								  		<td><strong><?= $worker_assists_data['assist_data']['total_delay_hours'] ?></strong></td>
								  		<td><strong><?= $worker_assists_data['assist_data']['total_overtime_hours'] - $worker_assists_data['assist_data']['total_delay_hours'] ?></strong></td>
							  		</tr>
						  		<?php endforeach; ?>
					  			</tbody>
				  			</table>
			  			</div>
			      	</div>
			      	<div role="tabpanel" class="tab-pane" id="monthly_deals">
                        <div class="table-scroll">
						  	<table class="table table-striped table-condensed table-hover assist-detail">
							  	<thead>
							  		<tr>
									  	<th>Trabajador</th>
									  	<th>Rut</th>
									  	<th>Cargo</th>
									  	<?php foreach ($month_days as $day) : ?>
										  	<th><?= $day ?></th>
									  	<?php endforeach; ?>
									  	<th>Total Tratos</th>
								  	</tr>
							  	</thead>
							  	<tbody>
							  	<?php foreach ($assist_month_data as $worker_id => $worker_assists_data) : ?>
							  		<tr>
									  	<td><h6><?= $workers[$worker_id]['nombres'] ?></h6></td>
									  	<td nowrap><h6><?= $workers[$worker_id]['rut'] ?></h6></td>
									  	<td><h6><small><strong><?= $workers[$worker_id]['Cargo']['nombre_cargo'] ?></small></strong></h6></td>
										<?php foreach ($month_days as $day) : ?>
											<td>
											<?php
												if (!empty($worker_assists_data['monthly_deals'][$day])) :
													foreach ($worker_assists_data['monthly_deals'][$day] as $deal_id => $deal_total) :
														echo '<span class="label label-primary">' . $deal_total . '</span>';
													endforeach;
												else :
													echo 0;
										 		endif; ?>
											</td>
									  	<?php endforeach; ?>
										<td><strong><?= $worker_assists_data['deals'] ?></strong></td>
							  		</tr>
						  		<?php endforeach; ?>
					  			</tbody>
				  			</table>
			  			</div>
			      	</div>
			      	<div role="tabpanel" class="tab-pane" id="monthly_bonuses">
                        <div class="table-scroll">
						  	<table class="table table-striped table-condensed table-hover assist-detail">
							  	<thead>
							  		<tr>
									  	<th>Trabajador</th>
									  	<th>Rut</th>
									  	<th>Cargo</th>
										<?php foreach ($month_days as $day) : ?>
										  	<th><?= $day ?></th>
									  	<?php endforeach; ?>
									  	<th>Total Bonos</th>
								  	</tr>
							  	</thead>
							  	<tbody>
							  	<?php foreach ($assist_month_data as $worker_id => $worker_assists_data) : ?>
							  		<tr>
									  	<td><h6><?= $workers[$worker_id]['nombres'] ?></h6></td>
									  	<td nowrap><h6><?= $workers[$worker_id]['rut'] ?></h6></td>
									  	<td><h6><small><strong><?= $workers[$worker_id]['Cargo']['nombre_cargo'] ?></small></strong></h6></td>
										<?php foreach ($month_days as $day) : ?>
											<td>
											<?php
												if (!empty($worker_assists_data['monthly_bonuses'][$day])) :
													foreach ($worker_assists_data['monthly_bonuses'][$day] as $bonus_id => $bonus_total) :
														echo '<span class="label label-primary">' . $bonus_total . '</span>';
													endforeach;
												else :
													echo 0;
										 		endif;
											endforeach; ?>
										</td>
										<td><strong><?= $worker_assists_data['bonuses'] ?></strong>
							  		</tr>
						  		<?php endforeach; ?>
					  			</tbody>
				  			</table>
			  			</div>
			      	</div>
			    </div>
	       	</div>
	        <?php
	            $group_id = $this->request->session()->read('Auth.User.group_id');
	            echo ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) ?
	             $this->Html->link(__('Volver'), ['action' => 'index', '?' => ['months' => $assistance_date->format('Y_m')]], ['class' => 'btn btn-flat btn-link']) :
	             $this->Html->link(__('Volver'), ['action' => 'index', '?' => ['building_id' => $budget->building_id, 'months' => $assistance_date->format('Y_m')]], ['class' => 'btn btn-flat btn-link']); ?>
		</div>
 	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			$('a[data-toggle="tab"]').parent().removeClass('shadow-z-2');
	  		e.target // newly activated tab
	  		e.relatedTarget // previous active tab
			$(this).parent().addClass('shadow-z-2');
  		});
  		var tableWidth = $(".table-scroll").width();
  		var contentTableWidth = $(".table-scroll > table").width();
  		$(".table-scroll-1").css({
  			'width': tableWidth,
  		});
  		$(".scroller").css({
  			'width': contentTableWidth,
  		});
  		$(".table-scroll-1").scroll(function(){
	        $(".table-scroll")
	            .scrollLeft($(".table-scroll-1").scrollLeft());
	    });
	    $(".table-scroll").scroll(function(){
	        $(".table-scroll-1")
	            .scrollLeft($(".table-scroll").scrollLeft());
	    });
	    /*$(window).scroll(function(){
	        var aTop = $('.assist-detail').position().top;
	        if($(this).scrollTop()>=aTop){
	            $('#header_table').css({
	                'position': 'fixed',
	                'top': '0',
	                'background-color': '#fff'
	            });
	        }else{
	            $('#header_table').css({
	                'position': 'relative'
	            });
	        }
	    });*/
	});

</script>
