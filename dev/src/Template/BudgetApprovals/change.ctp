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
        <h3 class="panel-title">Cambiar estado del presupuesto</h3>
    </div>
    <div class="panel-body">

        <div class="budgetApprovals form large-10 medium-9 columns">
            <?= $this->Form->create($budgetApproval); ?>
            <fieldset>
                <?php
                    echo $this->Form->input('budget_state_id',['label' => 'Estado']);
                    echo $this->Form->input('comment', ['label' => 'Comentario']);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Aprobar')) ?>
            <?= $this->Html->link(
                    'Cancelar',
                    $this->request->referer(),
                    ['class' => "btn btn-danger"]
                ); ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
