<?php
$this->assign('title_text', __('Módulo Presupuestos'));
$this->assign('title_icon', 'users');
?>
<div class="panel panel-material-blue-grey-700">
    <!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Agregar Informacion Adicional</h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <div class="col-lg-6 col-md-6">
            <h3>Obra: <?= h($sf_building->DesArn) ?></h3>
            <?= $this->Form->create($building); ?>
            <fieldset>
                <?php
                    echo $this->Form->input('address', ['label' => 'Dirección']);
                    echo $this->Form->input('client', ['label' => 'Cliente']);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Guardar')) ?>
            <?= $this->Html->link('Volver', ['controller' => 'Buildings', 'action' => 'index'], ['id' => 'formState', 'class' => 'btn btn-flat btn-link']); ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
