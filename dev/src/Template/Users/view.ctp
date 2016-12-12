<?php
    // elementos estandares de la vista
    $this->assign('title_text', __('Módulo Usuarios'));
    $this->assign('title_icon', 'users');
    $buttons = array();
    // $buttons[] = ['title' => __('Cuentas usuarios'), 'class' => 'primary', 'icon' => 'users', 'link' => '/users/listEditStatus'];
    // $buttons[] = ['title' => __('Editar'), 'class' => 'primary', 'icon' => 'pencil', 'link' => '/users/edit/' . $user->id];
    $this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Ver Usuario</h3>
    </div>
    <div class="panel-body">
    <!-- Panel content -->
         <div class="col-sm-6 col-md-6">
             <dl class="dl-horizontal">        
                <dt><?= __('Nombre: ') ?></dt>
                <dd><?= h($user->first_name) ?></dd>                                
                <dt><?= __('Apellido Paterno: ') ?></dt>
                <dd><?= h($user->lastname_f) ?></dd>                                
                <dt><?= __('Apellido Materno: ') ?></dt>
                <dd><?= h($user->lastname_m) ?></dd>                          
                <dt><?= __('Email: ') ?></dt>
                <dd><?= h($user->email) ?></dd>                                
                <dt><?= __('Teléfono: ') ?></dt>
                <dd><?= h($user->celphone) ?></dd>                                
                <dt><?= __('Dirección: ') ?></dt>
                <dd><?= h($user->address) ?></dd>                                
                <dt><?= __('Estado: ') ?></dt>
                <dd><?= ($user->active) ? 'Activo' : 'Bloqueado' ?></dd>
                <?php if (!empty($user->building_id)) : ?>        
                    <dt><?= __('Obras: ') ?></dt>
                    <dd>
                        <?php $user_buildings = '';
                        foreach ($user->building_id as $key => $b_id) : 
                            $user_buildings = $user_buildings . $buildings[$b_id] . ', '; ?>
                        <?php endforeach; ?>                                
                        <?php echo substr($user_buildings, 0, (strlen($user_buildings) - 2)); ?>
                    </dd>
                <?php endif; ?>                                
                <dt><?= __('Último ingreso sistema: ') ?></dt>
                <dd><?= ($user->last_login) ?></dd>
            </table>
             <?= $this->Html->link(__('Volver'), ['action' => 'index'], ['class' => 'btn btn-flat btn-link']) ?>
        </div>
        <div class="col-sm-6 col-md-6">
            <?php foreach($user->groups as $group):?>
                <?= $this->Html->tag('h4', $group->name);?>
                <ul>
                    <?php foreach($group->permissions as $perm):?>
                        <li>
                            <?= $this->Html->tag('strong', $perm->permission_name); ?><br>
                            <?= $this->Html->tag('i', $perm->permission_description); ?>
                        </li>
                    <?php endforeach;?>  
                </ul>
            <?php endforeach;?>    
        </div>
    </div>
</div>

