<?php
// elementos estandares de la vista
$this->assign('title_text', __('Estado de Pago'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __('Volver'), 'class' => 'primary', 'icon' => 'plus', 'link' => '/schedules/index/'+$schedule->budget_id];
$this->set('buttons', $buttons);
?>

<div class="panel panel-material-blue-grey-700">
    <!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Agregar Comentario a Estado de Pago</h3>
    </div>
    <div class="panel-body">
        <div class="col-sm-6">
            <?= $this->Form->create($comment); ?>
            <?= $this->Form->input('observation',['type'=>'textarea','label'=>'Comentario','placeholder'=>'Ingrese su comentario...']); ?>
            
            <?= $this->Form->button("Guardar"); ?>        
            <?= $this->Html->link('Volver', ['controller' => 'payment_statements', 'action' => 'index', '?' => ['building_id'=>$paymentStatement->budget->building_id]],['class' => 'btn btn-flat btn-link']) ?>
            <?= $this->Form->end(); ?>

        </div>
        <div class="col-sm-6"></div>        
    </div>  
</div>


<div class="row">
<?php if ($comments): ?>
<div class="list-group">            
 <?php foreach ($comments as $index => $comment): ?>
    <div class="list-group-item">
        <div class="row-action-primary" style="margin-top:20px;"><em><?= $this->Time->format($comment->created,'dd-MM-YYYY'); ?></em></div>
        <div class="row-content">
            <h4 class="list-group-item-heading"><?= $comment->user->fullName; ?></h4>
            <p class="list-group-item-text"><?= $comment->observation; ?></p>
        </div>
        <div class="list-group-separator"></div>
    </div>
 <?php endforeach ?>
</div>    
<?php else: ?>
        <div class="col-lg-12"><h4>No hay comentarios aún</h4></div>        
<?php endif ?>
</div>

