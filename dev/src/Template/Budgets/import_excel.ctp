<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Presupuesto'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Importar Presupuesto Inicial</h3>
    </div>
    <div class="panel-body">
    <!-- Panel content -->
        <?= $this->Form->create($budget, ['type' => 'file']); ?>
        <fieldset>
            <legend><?= __('Cargar Excel de Presupuesto') ?></legend>
            <div class="col-md-6 col-sm-6">
            <?php
                
                /*echo $this->Form->input('building_id', ['options' => $buildings]);
                echo $this->Form->input('duration');
                echo $this->Form->input('uf_value');
                echo $this->Form->input('total_cost_uf');
                echo $this->Form->input('comments');
                echo $this->Form->input('user_created_id', ['options' => $users, 'empty' => true]);
                echo $this->Form->input('user_modified_id');
                */
                echo $this->Form->input('file', ['type' => 'file', 'label' => 'Archivo Excel']);
                echo $this->Html->link(
                    'Cancelar',
                    ['controller' => 'buildings', 'action' => 'index'],
                    ['confirm' => 'Seguro que desea cancelar?', 'class' => 'btn btn-flat btn-link pull-right']
                );
            ?>
            <?= $this->Form->button(__('Importar')) ?>
            </div>
        </fieldset>
        <?= $this->Form->end() ?>
    </div>
</div>
