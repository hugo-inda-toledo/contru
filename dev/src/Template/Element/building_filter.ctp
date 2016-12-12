<?php  if (empty($last_building_info)) : // si hay una obra seteada, se esconde el filtro ?>
<?php echo $this->Form->create('Budgets', ['class' => 'form-horizontal', 'type' => 'get']); ?>
<div class="col-lg-6">
    <?php echo $this->Form->input('building_id', ['label' => 'Área de Negocio', 'value' => $budget->building_id, 'empty' => 'Seleccione una Obra']); ?>
</div>
<?php
echo $this->Form->button('Buscar', ['type' => 'submit']);
echo $this->Form->end();
?>

<?php endif; ?>