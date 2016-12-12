<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Importaciones Iconstruye'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">
            Lista de Importaciones de Iconstruye
        </h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <!-- <h4>No disponible - En Desarollo</h4>-->

        <table class="table table-striped table-hover table-condensed">
	        <col width="5%">
	        <col width="5%">
	        <col width="25%">
	        <col width="10%">
	        <col width="10%">
	        <col width="20%">
	        <col width="10%">
	        <tr>
	            <th><?= __('id') ?></th>
	            <th><?= __('Tipo') ?></th>
	            <th><?= __('Archivo') ?></th>
	            <th><?= __('Registros Nuevos') ?></th>
	            <th><?= __('Usuario') ?></th>
	            <th><?= __('Fecha') ?></th>
	            <th><?= __('Acciones') ?></th>
	        </tr>
		<?php foreach($iconstruyeImports as $ic): ?>
			<tr>
				<td> <?php echo $ic->id; ?> </td>
				<td> <?php echo ($ic->type == 'subcontracts') ? 'Subcontratos' : 'Guía Salida' ?> </td>
				<td> <?php echo $ic->file_name; ?> </td>
				<td> <?php echo $ic->transaction_lines; ?> </td>
				<td> <?php echo $ic->user_uploader->FullName; ?> </td>
				<td> <?php echo $ic->created; ?> </td>
				<td> <?= $this->Html->link('Ver', ['action' => 'view', $ic->id, $ic->type]) ?> </td>

			</tr>
        <?php endforeach; ?>
        </table>
        <?= $this->Element('paginador'); ?>
         <?= $this->Html->link(
            'Volver atras',
            $this->request->referer(),
            ['id' => 'confirmcancel',
            'class' => "btn btn-material-orange-900"]
        ); ?>
    </div>
</div>
