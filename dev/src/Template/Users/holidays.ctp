<?php
// elementos estandares de la vista
$this->assign('title_text', __('Vacaciones'));
$this->assign('title_icon', 'local-airport');
$buttons = array();
$buttons[] = ['title' => __('Solicitar Vacaciones'), 'class' => 'primary', 'icon' => 'plus', 'link' => '/vacations/add'];
if ($has_record) {
    $buttons[] = ['title' => __('Historial moderaciones'), 'class' => 'primary', 'icon' => 'alarm-on', 'link' => '/vacations/record/'];
}
if ($pending_moderation_vacations) {
    $buttons[] = ['title' => __('Moderar vacaciones'), 'class' => 'primary', 'icon' => 'clipboard', 'link' => '/vacations/moderate/'];
}
$this->set('buttons', $buttons);
?>

Usted posee <b><?= h($actual_user->vacation_pending_days) ?></b> días pendientes de vacaciones.

<?php if (!$vacations) : ?>
	No hay vacaciones para visualizar
<?php else : ?>
	<table data-order='[[0, "desc" ]]' class="dataTable">
	    <thead>
	        <tr>
	        	<th><?= __('Creado') ?></th>
	        	<th><?= __('Modificado') ?> </th>
	        	<th><?= __('Usuario') ?> </th>
	        	<th><?= __('Días Pendientes') ?></th>
	        	<th><?= __('Días Solicitados') ?></th>
	        	<th><?= __('Inicio vacación') ?></th>
	        	<th><?= __('Estado') ?></th>
	            <th class="actions"><?= __('Acciones') ?></th>
	        </tr>
	    </thead>
	    <tbody>
	        <?php foreach ($vacations as $vacation) : ?>
				<tr>
					<td><?= h($vacation->created) ?></td>
					<td><?= h(($vacation->modified == $vacation->created) ? 'No ha sido modificado' : $vacation->modified); ?></td>
					<td><?= h($vacation->user->name . ' ' . $vacation->user->last_name); ?></td>
					<td><?= h($vacation->user->vacation_pending_days) ?></td>
					<td><?= h($vacation->vacation_days) ?></td>
					<td><?= h($vacation->start) ?></td>
	            	<td><?= h($vacation_flux_status[$vacation->vacation_flux_statu_id]) ?></td>
	            	<td class="actions">
	            		<div class="split-button">
	            			<?= $this->Html->link(__('Ver'), ['controller' => 'Vacations', 'action' => 'view', $vacation->id], ['class' => 'button small-button']) ?>
	            			<?php $can_edit = ($vacation->user_creator_id == $actual_user->id) && ($vacation->vacation_flux_statu_id == VACATION_FLUX_STATUS_PENDING); ?>
	            			<?php if ($can_edit) : ?>
	            					<button class="split dropdown-toggle "></button>
	            					<ul class="split-content d-menu" data-role="dropdown">
	            						<!-- Si puede editar ó eliminar -->
	            					    <?php if ($can_edit) : ?>
	            					    	<li><?= $this->Html->link(__('Editar'), ['controller' => 'Vacations','action' => 'edit', $vacation->id]) ?></li>
	            					    	<li><?= $this->Form->postLink(__('Eliminar'), ['controller' => 'Vacations','action' => 'delete', $vacation->id], ['confirm' => __('Está seguro que desea eliminar la solicitud de vacaciones?')]) ?></li>
	            					    <?php endif; ?>           			               			    
	            					</ul>
	            			<?php endif; ?>
	            		</div>
	            	</td>
	        </tr>
	        <?php endforeach; ?>
	    </tbody>
	</table>
<?php endif; ?>