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
        <h3 class="panel-title">Detalle Mes de Asistencia de Trabajadores</h3>
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
	                             'value' => (!empty($this->request->query['months']) ? $this->request->query['months'] : '')]); ?>
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
	                             'value' => (!empty($this->request->query['months']) ? $this->request->query['months'] : '')]); ?>
	                        </div>
	                        <div class="col-lg-2">
	                            <?php echo $this->Form->button('Buscar', ['type' => 'submit']); ?>
	                        </div>
	                    </div>
	                <?php echo $this->Form->end();
           		endif; ?>
            </div>
            <div class="col-lg-6"></div>
        </div>
    	<h4>
            Mes Asistencia: <strong><?= $assistance_date->format('m Y'); ?></strong>
        </h4>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Nombre Trabajador</th>
                    <th>Rut</th>
                    <th>Cargo</th>
                    <!-- <th>Asistencia</th>
                    <th>Horas extra</th>
                    <th>Horas Atraso</th> -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($workers as $worker) : ?>
                    <tr>
                        <td><?= $worker['nombres'] ?></td>
                        <td style="white-space: nowrap;"><?= $worker['rut'] ?></td>
                        <td><?= $worker['Cargo']['nombre_cargo'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
            $group_id = $this->request->session()->read('Auth.User.group_id');
            echo ($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_ASIS_RRHH || $group_id == USR_GRP_OFI_TEC) ?
             $this->Html->link(__('Cancelar'), ['action' => 'index', '?' => ['months' => $assistance_date->format('Y_m')]], ['class' => 'btn btn-flat btn-link']) :
             $this->Html->link(__('Cancelar'), ['action' => 'index', '?' => ['building_id' => $budget->building_id, 'months' => $assistance_date->format('Y_m')]], ['class' => 'btn btn-flat btn-link']); ?>
    </div>
</div>
