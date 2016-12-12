<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Perfiles'));
$this->assign('title_icon', 'users');
$buttons = array();
$buttons[] = ['title' => __('Ver Perfil'), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Información Perfil <?= h($group->name) ?></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3">
                        <dl class="dl-horizontal">
                            <dt><?= __('Nombre') ?></dt>
                            <dd><?= h($group->name) ?></dd>
                            <dt><?= __('Descripción') ?></dt>
                            <dd><?= h($group->description) ?></dd>
                            <dt><?= __('Nivel') ?></dt>
                            <dd><?= h($group->level) ?></dd>
                            <dt><?= __('Fecha creación') ?></dt>
                            <dd><?= h($group->created) ?></dd>
                            <dt><?= __('Fecha modificación') ?></dt>
                            <dd><?= h($group->modified) ?></dd>
                        </dl>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6 col-md-offset-3">
                        <div class="well">
                            <?= $this->Html->tag('h4', 'Permisos asignados');?>
                            <dl>
                                <?php foreach($group->permissions as $perm):?>
                                    
                                    <dt><?= $this->Html->tag('strong', $perm->permission_name); ?></dt>
                                    <dd><?= $this->Html->tag('i', $perm->permission_description); ?></dd>
                                    <br>
                                    
                                <?php endforeach;?>
                            </dl>
                        </div>
                        
                    
                    </div>
                </div>                
            </div>
        </div>        
        
         <?= $this->Html->link(__('Volver'), ['action' => 'index'], ['class' => 'btn btn-flat btn-link']) ?>
    </div>  
</div>
