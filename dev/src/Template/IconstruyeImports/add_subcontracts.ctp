<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Importaciones Iconstruye'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
    <div class="panel-heading">
        <h3 class="panel-title">Importación Subcontratos Iconstruye</h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <?= $this->Form->create($iconstruyeImport, ['type' => 'file']); ?>
            <h3>Por favor seleccione el archivo para la importación</h3>
            <div class="col-md-6 col-sm-6">
                <?= $this->Form->input('file', ['type' => 'file', 'label' => 'Archivo Excel']); ?>
                <?= $this->Html->link('Volver', ['controller' => 'buildings', 'action' => 'index'], ['class' => 'btn btn-flat btn-link pull-right']); ?>
                <?= $this->Form->button(__('Cargar')) ?>
            </div>
        <?= $this->Form->end() ?>
    </div>
</div>
