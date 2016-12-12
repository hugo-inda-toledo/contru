<?php
// elementos estandares de la vista
$this->assign('title_text', __('Crear Planificación'));
$this->assign('title_icon', 'groups');
$buttons = array();
$this->set('buttons', $buttons);
?>

<div class="groups form large-10 medium-9 columns">
    <?= $this->Form->create($schedule); ?>
    <fieldset>
        <?php
            echo $this->Form->input('name',['label' => 'Nombre']);
            echo $this->Form->input('description',['label' => 'Descripción']);
            echo $this->Form->hidden('budget_id',['value' => $budget_id]);
            echo $this->Form->input('total_days',['label' => 'Cantidad de dias']);
            echo $this->Form->input('start_date', array ('type' => 'date', 'class' => 'place-right', 'name' => 'start_date' , 'label' => 'Fecha de Inicio')); 
            echo $this->Form->input('finish_date',['name' => 'finish_date' ,'label' => 'Fecha Fin']);

        ?>
    </fieldset>
    <h3>Detalle de los presupuestos </h3>
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="false">
        <?php foreach ($budget_items as $bi): ?>
            <?php echo $this->element('acordion',['bi' => $bi, 'panel_type' => 'default']) ?>
        <?php endforeach ?>    
    </div>
    <?= $this->Form->button(__('Guardar')) ?>
    <?= $this->Form->end() ?>
</div>
