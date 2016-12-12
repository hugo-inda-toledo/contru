<?php
    $this->assign('title_text', __('Editar mis datos'));
    $this->assign('title_icon', 'user');
 ?>

<div class="row">
    <?= $this->Form->create($user); ?>
    <div class="row cells2">
        <?php
            echo $this->Form->input('name', array( 'label'=>'Nombre'));
            echo $this->Form->input('last_name', array('label'=>'Apellido'));
        ?>
    </div>
    <div class="row cells2">
        <?php
            echo $this->Form->input('birth_date', array('type' => 'date','label'=>'Fecha de nacimiento'));
            echo $this->Form->input('email', array('type' => 'email', 'label'=>'Correo electronico'));
        ?>
    </div>
    <div class="row cells2">
        <?php
            echo $this->Form->input('phone', array('type' => 'tel', 'label'=>'Telefono'));
            echo $this->Form->input('annex', array('label'=>'Anexo'));
        ?>
    </div>
    <?= $this->Form->button('<span class="mif-floppy-disk"></span> '. __('Guardar')) ?>
<?= $this->Html->link(__('Cancelar'), ['#'], ['class' => 'place-right']) ?>
    <?= $this->Form->end() ?>
</div>
