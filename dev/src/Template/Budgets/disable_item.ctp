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
        <h3 class="panel-title">Editar Parametros de Items Adicionales</h3>
    </div>
    <div class="panel-body">
        <div class="column large-12">
            <div class="budgetItems form large-10 medium-9 columns">
            <?= $this->Form->create($budgetItem); ?>
            <fieldset>
                <?php
                    echo $this->Form->input('item',['disabled' => true]);
                    echo $this->Form->input('description',['disabled' => true]);
                    echo $this->Form->input('unit_id', ['options' => $units,['disabled' => true]]);
                    echo $this->Form->input('quantity',['disabled' => true]);
                    echo $this->Form->input('unity_price',['disabled' => true]);
                    echo $this->Form->input('total_price',['disabled' => true]);
                    echo $this->Form->input('general_cost');
                    echo $this->Form->input('utilities');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>

    </div>
</div>