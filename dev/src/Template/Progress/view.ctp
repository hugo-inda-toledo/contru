<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Perfiles'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __('Detalles de planificación'), 'class' => 'primary', 'icon' => 'plus', 'link' => '/schedules/view'];
$this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Detalles de planificación</h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-striped table-hover">        
                    <tr>
                        <th><?= __('Titulo') ?></th>
                        <td><?= h($schedule->name) ?></td>                                
                    </tr>  
                    <tr>
                        <th><?= __('Inicio') ?></th>
                        <td><?= $this->Time->format($schedule->start_date,'dd-MM-YYYY'); ?></td>
                    </tr>  
                    <tr>
                        <th><?= __('Fin') ?></th>
                        <td><?= $this->Time->format($schedule->finish_date,'dd-MM-YYYY'); ?></td>
                    </tr>        
                    <tr>
                        <th><?= __('Usuario Creador') ?></th>
                        <td><?= h($schedule->user_created->first_name).' '.h($schedule->user_created->lastname_f).' '.h($schedule->user_created->lastname_m); ?></td>                                
                    </tr>  
                </table>            
            </div>
            <div class="col-md-6">
                <table class="table table-striped table-hover">        
                    <h1>datos de la obra</h1>  
                </table> 
            </div>
        </div>    
    </div>  
</div>
