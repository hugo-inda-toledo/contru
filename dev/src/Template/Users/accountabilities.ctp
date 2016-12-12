<?php
// elementos estandares de la vista
$this->assign('title_text', __('Rendiciones de cuenta'));
$this->assign('title_icon', 'stack');
$buttons = array();
$buttons[] = ['title' => __('Nueva rendición de cuenta'), 'class' => 'primary', 'icon' => 'plus', 'link' => '/renditions/add/'];
if ($has_record) {
    $buttons[] = ['title' => __('Historial moderaciones'), 'class' => 'primary', 'icon' => 'alarm-on', 'link' => '/renditions/record/'];
}
if ($pending_moderation_renditions) {
    $buttons[] = ['title' => __('Moderar rendiciones'), 'class' => 'primary', 'icon' => 'clipboard', 'link' => '/renditions/moderate/'];
}
$this->set('buttons', $buttons);
?>

<?php if (!$renditions) : ?>
	No hay rendiciones para visualizar
<?php else : ?>
	<table data-order='[[0, "desc" ]]' class="dataTable">
	    <thead>
	        <tr>
	        	<th><?= __('Creado') ?></th>
	        	<th><?= __('Modificado') ?> </th>
	        	<th><?= __('Usuario') ?> </th>
	        	<th><?= __('Título') ?></th>
	        	<th><?= __('Monto') ?></th>
	        	<th><?= __('Estado') ?></th>
	        	<th><?= __('Archivo') ?></th>
	            <th class="actions"><?= __('Actions') ?></th>
	        </tr>
	    </thead>
	    <tbody>
	        <?php foreach ($renditions as $rendition) : ?>
				<tr>
					<td><?= h($rendition->created) ?></td>
					<td><?= h(($rendition->modified == $rendition->created) ? 'No ha sido modificado' : $rendition->modified); ?></td>
					<td><?= h($rendition->user->name . ' ' . $rendition->user->last_name); ?></td>
	            	<td><?= h($rendition->title) ?></td>
	            	<td><?= h(moneda($rendition->amount)) ?></td>
	            	<td><?= h($rendition_flux_status[$rendition->rendition_flux_statu_id]) ?></td>
	            	<td><?= h((!empty($rendition->files)) ? 'Sí' : 'No') ?></td>
	            	<td class="actions">
	            		<div class="split-button">
	            			<?= $this->Html->link(__('Ver'), ['controller' => 'Renditions', 'action' => 'view', $rendition->id], ['class' => 'button small-button']) ?>
	            			<?php $can_edit = ($rendition->user_creator_id == $actual_user->id) && ($rendition->rendition_flux_statu_id == RENDITION_FLUX_STATUS_PENDING); ?>
	            			<?php $has_file = !empty($rendition->files); ?>
	            			<?php if ($can_edit || $has_file) : ?>
	            					<button class="split dropdown-toggle "></button>
	            					<ul class="split-content d-menu" data-role="dropdown">
	            						<!-- Si puede editar ó eliminar -->
	            					    <?php if ($can_edit) : ?>
	            					    	<li><?= $this->Html->link(__('Editar'), ['controller' => 'Renditions','action' => 'edit', $rendition->id]) ?></li>
	            					    	<li><?= $this->Form->postLink(__('Eliminar'), ['controller' => 'Renditions','action' => 'delete', $rendition->id], ['confirm' => __('Está seguro que desea eliminar la rendición "{0}"?', $rendition->title)]) ?></li>
	            					    <?php endif; ?>
	            					    <!-- Si posee archivo -->
	            					    <?php if ($has_file) : ?>
	            					    	<li><?= $this->Form->postLink(__('Descargar Adjunto'), ['controller' => 'Renditions','action' => 'download', $rendition->id]) ?></li>
	            					    	<li><?= $this->Form->postLink(__('Eliminar Adjunto'), ['controller' => 'Renditions','action' => 'delete_file', $rendition->id], ['confirm' => __('Está seguro que desea eliminar el archivo de la rendición "{0}"?', $rendition->title)]) ?></li>
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