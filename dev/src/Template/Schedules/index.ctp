<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Planificaciones Semanales'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">
            Lista de Planificaciones Semanales
        </h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <?= $this->Element('info_budget_building'); ?>
        <div class="row">
            <div class="col-lg-6">
              <?php $group_id = $this->request->session()->read('Auth.User.group_id');
              if ($group_id != USR_GRP_ADMIN_OBRA && $group_id != USR_GRP_ASIS_RRHH && $group_id != USR_GRP_OFI_TEC) :
                  echo $this->Element('building_filter'); // coloca un menu
               endif; ?>
            </div>
            <div class="col-lg-6">
                <?= $this->Html->link(__('Agregar Nueva Planificación Semanal'), ['action' => 'add', $budget->id], ['class' => 'btn btn-material-orange-900 pull-right btn-md']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
            <?php if (!empty($schedules->toArray())): ?>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th><?= $this->Paginator->sort('name','Nombre') ?></th>
                            <th><?= $this->Paginator->sort('start_date','Fecha Inicio') ?></th>
                            <th>Estado Avance</th>
                            <th class="actions"><?= __('Acciones') ?></th>
                        </tr>
                    </thead>
	                <tbody>
	                <?php foreach ($schedules->toArray() as $schedule): ?>
	                    <tr>
	                        <td>
                                <span class="label label-success" data-toggle="tooltip" data-placement="top" data-original-title="Partidas en esta planificación"><?= count($schedule->progress) ?></span>
                                <span data-toggle="tooltip" data-placement="top" data-original-title="Descripción: <?= h($schedule->description) ?>"><?= h($schedule->name) ?></span></td>
	                        <td><?= $this->Time->format($schedule->start_date,'dd-MM-Y') ?></td>
	                        <td>
	                            <?php
	                                echo reset($schedules_states[$schedule->id]['approval_state']);
	                                if (empty($schedules_states[$schedule->id]['approval_state'][USR_GRP_GE_GRAL])) :
	                                    if (empty($schedules_states[$schedule->id]['approval_state'][USR_GRP_VISITADOR])) :
	                                        echo (!empty($schedules_rejects[$schedule->id][USR_GRP_VISITADOR])) ? '<br><span class="label label-danger">Rechazado por Visitador</span>' : '';
	                                    endif;
	                                    echo (!empty($schedules_rejects[$schedule->id][USR_GRP_GE_GRAL])) ? '<br><span class="label label-danger">Rechazado por Gerente General</span>' : '';
	                                    echo (!empty($schedules_rejects[$schedule->id][USR_GRP_GE_FINAN])) ? '<br><span class="label label-danger">Rechazado por Gerente Finanzas</span>' : '';
	                                endif;
	                             ?>
	                        </td>
	                        <td class="actions">
	                            <div class="btn-group">
	                                <?= $this->Html->link(__('Ver'), ['controller' => 'schedules', 'action' => 'view', $schedule->id], ['class' => 'btn btn-xs btn-material-orange-900']) ?>
	                                <a href="#" data-target="#" class="btn btn-xs btn-material-orange-900 dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
	                                <ul class="dropdown-menu">
	                                    <li>
	                                        <?php if (!empty($schedules_states[$schedule->id]['approval_state'][-1]) || !empty($schedules_states[$schedule->id]['approval_state'][0])) :
	                                        	$now = new \DateTime('now');
	                    						if (($group_id == USR_GRP_ADMIN_OBRA || $group_id == USR_GRP_OFI_TEC || $group_id == USR_GRP_ASIS_RRHH) && $now < $schedule->start_date) :
	                                            	echo $this->Html->link(__('Editar'), ['controller' => 'schedules', 'action' => 'edit', $schedule->id]);
	                                            elseif ($group_id == USR_GRP_COORD_PROY || $group_id == USR_GRP_GE_FINAN || $group_id == USR_GRP_GE_GRAL) :
	                                            	echo $this->Html->link(__('Editar Planificación'), ['controller' => 'schedules', 'action' => 'edit', $schedule->id]);
	                                            endif;
	                                        endif; ?>
	                                    </li>
	                                    <li>

	                                    </li>
	                                    <li>
	                                    <?php
	                                    if (count($schedule->completed_tasks) > 0) :
	                                     	echo $this->Html->link(__('Ver Trabajo Realizado'), ['controller' => 'completed_tasks', 'action' => 'view', $schedule->id]);
	                                    	echo (empty($completed_tasks_approvals[$schedule->id])) ?
	                                    		$this->Html->link(__('Editar Trabajo Realizado'), ['controller' => 'completed_tasks', 'action' => 'edit', $schedule->id]) : '';
	                                 	else :
	                                         echo $this->Html->link(__('Agregar Trabajo Realizado'), ['controller' => 'completed_tasks', 'action' => 'add', $schedule->id]);
	                                     endif; ?>
	                                    </li>
	                                </ul>
	                            </div>
                                <?php
                                if (empty($schedules_states[$schedule->id]['approval_state'][USR_GRP_GE_GRAL])) :
                                      if (!empty($schedules_states[$schedule->id]['approval_state'][-1])) :
                                          echo $this->Html->link(__('Ingresar Avance de Obra'), ['controller' => 'schedules', 'action' => 'progress', $schedule->id], ['class' => 'btn btn-xs btn-material-orange-900']);
                                      elseif (!empty($schedules_states[$schedule->id]['approval_state'][0])) :
                                          echo $this->Html->link(__('Editar Avance de Obra'), ['controller' => 'schedules', 'action' => 'progress', $schedule->id], ['class' => 'btn btn-xs btn-material-orange-900']);
                                      elseif (!empty($schedules_rejects[$schedule->id][USR_GRP_GE_GRAL])) :
                                          echo $this->Html->link(__('Editar Avance de Obra'), ['controller' => 'schedules', 'action' => 'progress', $schedule->id], ['class' => 'btn btn-xs btn-material-orange-900']);
                                      endif;
                                endif; ?>

	                            <?php if ($schedules_states[$schedule->id]['advance_state']) :
	                                if (empty($schedules_states[$schedule->id]['approval_state'][USR_GRP_GE_GRAL])) :
                                        if ($group_id == USR_GRP_VISITADOR && !empty($schedules_states[$schedule->id]['approval_state'][0])) : ?>
	                                        <a href="#" class="btn btn-xs btn-success approve-progress" data-schedule-id="<?php echo $schedule->id ?>">Aprobar</a>
	                                     <?php elseif ($group_id == USR_GRP_GE_GRAL || $group_id == USR_GRP_GE_FINAN && !empty($schedules_states[$schedule->id]['approval_state'][USR_GRP_VISITADOR])) : ?>
	                                        <a href="#" class="btn btn-xs btn-success approve-progress" data-schedule-id="<?php echo $schedule->id ?>">Aprobar</a>
	                                        <?php /*echo (!empty($schedules_rejects[$schedule->id][USR_GRP_GE_GRAL]) || !empty($schedules_rejects[$schedule->id][USR_GRP_GE_FINAN])) ? '' :
	                                         '<a href="#" class="btn btn-xs btn-danger reject-progress" data-schedule-id="' . $schedule->id . '">Rechazar</a>';*/
	                                    endif;
	                                endif;
	                            endif; ?>
	                        </td>
	                    </tr>
	                	<?php endforeach; ?>
	                </tbody>
                </table>
                <?= $this->Element('paginador'); ?>
            <?php else: ?>
                <h4>No hay planificaciones de semana disponibles.</h4>
            <?php endif ?>
            </div>
        </div>
    </div>
</div>


<!-- Modal for Approve Progress -->
<div class="modal fade" id="approval-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <?php echo $this->Form->create(null, ['url' => ['controller' => 'Schedules', 'action' => 'approve_progress'], ['id' => 'approval-progress-form']]) ?>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          <span class="sr-only">Cerrar</span>
        </button>
        <h4 class="modal-title" id="myModalLabel">Aprobar Avances de Obra en Planificación</h4>
      </div>
      <div class="modal-body">
        <p>¿Está seguro de Aprobar los avances en la Planificación seleccionada?</p>
        <?= $this->Form->hidden('schedule_id', ['id' => 'approval-id']); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-flat btn-link" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-material-orange-900 btn-approval">Aprobar</button>
      </div>
      <?php echo $this->Form->end(); ?>
    </div>
  </div>
</div>

<!-- Modal for Reject Progress -->
<div class="modal fade" id="reject-modal" tabindex="-1" role="dialog" aria-labelledby="rejectProgress" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <?php echo $this->Form->create(null, ['url' => ['controller' => 'Schedules', 'action' => 'reject_progress'], ['id' => 'reject-progress-form']]) ?>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          <span class="sr-only">Cerrar</span>
        </button>
        <h4 class="modal-title" id="rejectProgress">Rechazar Avance de Obra</h4>
      </div>
      <div class="modal-body">
        <p>¿Está seguro de desea Rechazar el avance de Obra?</p>
        <div style="margin-top: 20px;"></div>
        <?= $this->Form->input('comment',['type'=>'textarea','label'=>false, 'placeholder'=>'Ingrese su comentario...', 'id'=> 'reject-comment']) ?>
        <?= $this->Form->hidden('schedule_id', ['id' => 'reject-progress-id']); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-material-orange-900 btn-reject-progress">Rechazar</button>
      </div>
      <?php echo $this->Form->end(); ?>
    </div>
  </div>
</div>

<?= $this->Html->script('schedules.index'); ?>