<?php
use Cake\Core\Configure;
// elementos estandares de la vista
$this->assign('title_text',  __('Panel de Control - ').__('Obra ').$sf_building->CodArn.' - '.$sf_building->DesArn);
// $this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>

<style>
    .progress {
        height: 22px;
    }

    .glyphicon { margin-right:10px; }
    .panel-body table tr td { padding-left: 15px }
    .panel-body .table {margin-bottom: 0px; }
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        border-top: 0px;
    }
    .separator-row{border-top: 2px solid #ddd;}

    #big-buttons a {
        color: #000;
    }
</style>

<?php echo $this->Html->css('admin_lte/AdminLTE.css');?>
<?php echo $this->Html->css('font_awesome/font-awesome.css');?>
<?php echo $this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css');?>
<?= $this->Html->script('buildings.dashboard.js') ?>

<?php
    $group_id = $this->request->session()->read('Auth.User.group_id');
    if (!empty($buildings_budgets[$sf_building->CodArn]))
    {
        $verPpto = ['controller' => 'budgets', 'action' => 'review', $buildings_budgets[$sf_building->CodArn]['budget_id']];
    }

    //$currency_value = (isset($currency_value))?$currency_value->id:'default';
    if($budget)
    {
        echo $this->Form->hidden('currency_value_id', ['value' => $budget->currency_id]);
        $state = end($building->budget->budget_approvals)->budget_state;
    }
?>

<div class="row">
    <div class="col-sm-8">
        <div class="row" id="big-buttons">
            <?php if (empty($buildings_budgets[$sf_building->CodArn])):?>
                
                <?php if ($this->Access->verifyAction('Budgets', 'add') == true): ?>
                    <div class="col-md-6 col-sm-12">
                        <?= 
                            $this->Html->link(
                                $this->Html->div('info-box', 
                                    $this->Html->tag('span', $this->Html->tag('i', '', array('class' => 'ion-android-add-circle')), array('class' => 'info-box-icon bg-green')).
                                    $this->Html->div('info-box-content', 
                                        $this->Html->tag('span', __('Presupuesto'), array('class' => 'info-box-text')).
                                        $this->Html->tag('span', __('Agregar Presupuesto'), array('class' => 'info-box-number'))
                                    )
                                ),
                                ['controller' => 'budgets', 'action' => 'add', $parche_softland_id],
                                array(
                                    'escape' => false
                                )
                            );
                        ?>
                    </div>
                <?php endif;?>

                <?php if ($this->Access->verifyAction('Buildings', 'ignore_building') == true): ?>
                    <!--<div class="col-md-6 col-sm-12">
                        <?php 
                            /*echo $this->Html->link(
                                $this->Html->div('info-box', 
                                    $this->Html->tag('span', $this->Html->tag('i', '', array('class' => 'ion-android-remove-circle')), array('class' => 'info-box-icon bg-red')).
                                    $this->Html->div('info-box-content', 
                                        $this->Html->tag('span', __('Obra'), array('class' => 'info-box-text')).
                                        $this->Html->tag('span', __('Ignorar obra'), array('class' => 'info-box-number'))
                                    )
                                ),
                                ['controller' => 'buildings', 'action' => 'ignore_building', $parche_softland_id],
                                array(
                                    'escape' => false
                                )
                            );*/
                        ?>
                    </div>-->
                <?php endif;?>

            <?php else:?>

                <!--<div class="col-sm-4">
                    <?php 
                        /*$this->Html->link(
                            $this->Html->div('info-box', 
                                $this->Html->tag('span', $this->Html->tag('i', '', array('class' => 'fa fa-building')), array('class' => 'info-box-icon bg-default')).
                                $this->Html->div('info-box-content', 
                                    $this->Html->tag('span', __('Obra'), array('class' => 'info-box-text')).
                                    $this->Html->tag('span', __('Estado Actual Obra'), array('class' => 'info-box-number'))
                                )
                            ),
                            //['controller' => 'budgets', 'action' => 'global_state', $buildings_budgets[$sf_building->CodArn]['budget_id']],
                            'javascript:void(0);',
                            array(
                                'escape' => false,
                                'style' => 'cursor:not-allowed'
                            )
                        );*/
                    ?>
                </div>-->

                

                <?php if ($this->Access->verifyAction('Workers', 'index') == true): ?>
                    <!--<div class="col-sm-4">
                        <?php 
                            /*$this->Html->link(
                                $this->Html->div('info-box', 
                                    $this->Html->tag('span', $this->Html->tag('i', '', array('class' => 'ion-android-contacts')), array('class' => 'info-box-icon bg-aqua')).
                                    $this->Html->div('info-box-content', 
                                        $this->Html->tag('span', __('Obra'), array('class' => 'info-box-text')).
                                        $this->Html->tag('span', __('Ver Trabajadores'), array('class' => 'info-box-number'))
                                    )
                                ),
                                '/workers/index/'.$sf_building->CodArn,
                                array(
                                    'escape' => false
                                )
                            );*/
                        ?>
                    </div>-->
                <?php endif;?>

                <?php if ($this->Access->verifyAction('Buildings', 'ignore_building') == true): ?>
                    <!--<div class="col-sm-4">
                        <?php 
                            /*echo $this->Html->link(
                                $this->Html->div('info-box', 
                                    $this->Html->tag('span', $this->Html->tag('i', '', array('class' => 'ion-android-remove-circle')), array('class' => 'info-box-icon bg-red')).
                                    $this->Html->div('info-box-content', 
                                        $this->Html->tag('span', __('Obra'), array('class' => 'info-box-text')).
                                        $this->Html->tag('span', __('Ignorar obra'), array('class' => 'info-box-number'))
                                    )
                                ),
                                ['controller' => 'buildings', 'action' => 'ignore_building', $parche_softland_id],
                                array(
                                    'escape' => false
                                )
                            );*/
                        ?>
                    </div>-->
                <?php endif;?>

                <?php if ($this->Access->verifyAction('Budgets', 'review') == true): ?>
                    <div class="col-sm-4">
                        <?= 
                            $this->Html->link(
                                $this->Html->div('info-box', 
                                    $this->Html->tag('span', $this->Html->tag('i', '', array('class' => 'fa fa-file-text')), array('class' => 'info-box-icon bg-aqua')).
                                    $this->Html->div('info-box-content', 
                                        $this->Html->tag('span', __('Presupuesto'), array('class' => 'info-box-text')).
                                        $this->Html->tag('span', __('Ver Presupuesto'), array('class' => 'info-box-number'))
                                    )
                                ),
                                $verPpto,
                                array(
                                    'escape' => false,
                                )
                            );
                        ?>
                    </div>

                    <!--<div class="col-sm-4">
                        <?php 
                            /*echo $this->Html->link(
                                $this->Html->div('info-box', 
                                    $this->Html->tag('span', $this->Html->tag('i', '', array('class' => 'fa fa-file-text')), array('class' => 'info-box-icon bg-aqua')).
                                    $this->Html->div('info-box-content', 
                                        $this->Html->tag('span', __('Presupuesto'), array('class' => 'info-box-text')).
                                        $this->Html->tag('span', __('Ver Gastos Generales'), array('class' => 'info-box-number'))
                                    )
                                ),
                                array_merge($verPpto, ['?'=>['extra'=>'3']]),
                                array(
                                    'escape' => false,
                                )
                            );*/
                        ?>
                    </div>-->

                    <div class="col-sm-4">
                        <?= 
                            $this->Html->link(
                                $this->Html->div('info-box', 
                                    $this->Html->tag('span', $this->Html->tag('i', '', array('class' => 'fa fa-file-text')), array('class' => 'info-box-icon bg-aqua')).
                                    $this->Html->div('info-box-content', 
                                        $this->Html->tag('span', __('Presupuesto'), array('class' => 'info-box-text')).
                                        $this->Html->tag('span', __('Ver Adicionales'), array('class' => 'info-box-number'))
                                    )
                                ),
                                array_merge($verPpto, ['?'=>['extra'=>'1']]),
                                array(
                                    'escape' => false,
                                )
                            );
                        ?>
                    </div>
                <?php endif;?>


                <?php 
                    /*if ($this->Access->verifyLevel(9) == true || $this->Access->verifyAccessByKeyword('gerente_finanzas'))
                    {
                        if (count($budget->budget_items) > 0)
                        {
                            if ($state->id < 6 )
                            {
                                if ($this->Access->verifyAction('Budgets', 'add_extra') == true)
                                {
                                    echo '<div class="col-sm-4">'.$this->Html->link(
                                        $this->Html->div('info-box', 
                                            $this->Html->tag('span', $this->Html->tag('i', '', array('class' => 'ion-android-add-circle')), array('class' => 'info-box-icon bg-green')).
                                            $this->Html->div('info-box-content', 
                                                $this->Html->tag('span', __('Presupuesto'), array('class' => 'info-box-text')).
                                                $this->Html->tag('span', __('Agregar Adicionales'), array('class' => 'info-box-number'))
                                            )
                                        ),
                                        ['controller' => 'budgets', 'action' => 'add_extra', $budget->id],
                                        array(
                                            'escape' => false,
                                        )
                                    ).'</div>';
                                }
                            }
                        }
                    }*/
                ?>

                <?php if ($this->Access->verifyAction('Budgets', 'review') == true): ?>
                    <div class="col-sm-4">
                        <?= 
                            $this->Html->link(
                                $this->Html->div('info-box', 
                                    $this->Html->tag('span', $this->Html->tag('i', '', array('class' => 'fa fa-file-text')), array('class' => 'info-box-icon bg-aqua')).
                                    $this->Html->div('info-box-content', 
                                        $this->Html->tag('span', __('Presupuesto'), array('class' => 'info-box-text')).
                                        $this->Html->tag('span', __('Ver Gastos No Considerados'), array('class' => 'info-box-number'))
                                    )
                                ),
                                array_merge($verPpto, ['?'=>['extra'=>'2']]),
                                array(
                                    'escape' => false,
                                )
                            );
                        ?>
                    </div>
                <?php endif;?>


                <?php 
                    /*if ($this->Access->verifyLevel(9) == true || $this->Access->verifyAccessByKeyword('gerente_finanzas'))
                    {
                        if (count($budget->budget_items) > 0)
                        {
                            if ($state->id < 6 )
                            {
                                if ($this->Access->verifyAction('Budgets', 'add_expense') == true)
                                {
                                    echo '<div class="col-sm-4">'.$this->Html->link(
                                        $this->Html->div('info-box', 
                                            $this->Html->tag('span', $this->Html->tag('i', '', array('class' => 'ion-android-add-circle')), array('class' => 'info-box-icon bg-green')).
                                            $this->Html->div('info-box-content', 
                                                $this->Html->tag('span', __('Presupuesto'), array('class' => 'info-box-text')).
                                                $this->Html->tag('span', __('Agregar Gastos No Contemplados'), array('class' => 'info-box-number'))
                                            )
                                        ),
                                        ['controller' => 'budgets', 'action' => 'add_expense', $budget->id],
                                        array(
                                            'escape' => false,
                                        )
                                    ).'</div>';
                                }
                                
                            }
                        }
                    }*/
                ?>


                <?php 
                    if ($this->Access->verifyAccessByKeyword('gerente_finanzas') == true || $this->Access->verifyLevel(9) == true)
                    {
                        if (count($budget->budget_items) == 0 && $state->id == 1)
                        {
                            if ($this->Access->verifyAction('Budgets', 'add') == true)
                            {
                                echo '<div class="col-sm-4">'.$this->Html->link(
                                    $this->Html->div('info-box', 
                                        $this->Html->tag('span', $this->Html->tag('i', '', array('class' => 'fa fa-upload')), array('class' => 'info-box-icon bg-green')).
                                        $this->Html->div('info-box-content', 
                                            $this->Html->tag('span', __('Presupuesto'), array('class' => 'info-box-text')).
                                            $this->Html->tag('span', __('Cargar excel Presupuesto'), array('class' => 'info-box-number'))
                                        )
                                    ),
                                    ['controller' => 'budgets', 'action' => 'add', $budget->building->softland_id],
                                    array(
                                        'escape' => false,
                                    )
                                ).'</div>';
                            }
                            
                        }
                    }
                ?>


                <?php 
                    if ($this->Access->verifyAccessByKeyword('gerente_finanzas') == true || $this->Access->verifyLevel(9) == true)
                    {
                        if (count($budget->budget_items) > 0)
                        {
                            if ($state->id < 4 )
                            {
                                if ($this->Access->verifyAction('BudgetItems', 'remove_all') == true)
                                {
                                    echo '<div class="col-sm-4">'.$this->Html->link(
                                        $this->Html->div('info-box', 
                                            $this->Html->tag('span', $this->Html->tag('i', '', array('class' => 'fa fa-ban')), array('class' => 'info-box-icon bg-red')).
                                            $this->Html->div('info-box-content', 
                                                $this->Html->tag('span', __('Presupuesto'), array('class' => 'info-box-text')).
                                                $this->Html->tag('span', __('Remover todos los Items'), array('class' => 'info-box-number'))
                                            )
                                        ),
                                        ['controller' => 'budget_items', 'action' => 'remove_all', $budget->id],
                                        array(
                                            'escape' => false,
                                        )
                                    ).'</div>';
                                }
                                
                            }
                        }
                    }
                ?>

                <?php if ($this->Access->verifyLevel(9) == true && $state->id <= 2):?>

                    <?php if ($this->Access->verifyAction('BudgetItems', 'reset_obra') == true): ?>
                        <!--<div class="col-sm-4">
                            <?php 
                                /*echo$this->Html->link(
                                    $this->Html->div('info-box', 
                                        $this->Html->tag('span', $this->Html->tag('i', '', array('class' => 'fa fa-pencil-square-o')), array('class' => 'info-box-icon bg-red')).
                                        $this->Html->div('info-box-content', 
                                            $this->Html->tag('span', __('Presupuesto'), array('class' => 'info-box-text')).
                                            $this->Html->tag('span', __('Eliminar presupuesto'), array('class' => 'info-box-number'))
                                        )
                                    ),
                                    ['controller' => 'budget_items', 'action' => 'reset_obra', $buildings_budgets[$sf_building->CodArn]['budget_id'], "false"],
                                    array(
                                        'escape' => false,
                                        'class' => 'confirm'
                                    )
                                );*/
                            ?>
                        </div>-->
                    <?php endif;?>

                <?php endif;?>


                <?php if (!empty($buildings_budgets[$sf_building->CodArn])):?>
                    <?php foreach($menu AS $m):?>
                        <?php if(array_search($m['title'], $menus_to_show)!==false):?>

                                <?php 
                                    foreach($m['items'] AS $menu_item)
                                    {
                                        $words = explode('_', $menu_item['controller']);
                                        $controller = '';

                                        if(count($words) > 1)
                                        {
                                            foreach($words as $key => $value)
                                            {
                                                $controller .= ucwords($value);
                                            }
                                        }
                                        else
                                        {
                                            $controller = ucwords($menu_item['controller']);
                                        }

                                        if ($this->Access->verifyAction($controller, $menu_item['action']) == true)
                                        {
                                            $link = ['controller' => $menu_item['controller'], 'action' => $menu_item['action']];

                                            //echo 'cont: '.$menu_item['controller'].' action:'.$menu_item['action'].'<br>';
                                            

                                            if(isset($menu_item['extra']))
                                            {
                                                $link[] = $menu_item['extra'];
                                            }

                                            if($menu_item['action'] == 'add')
                                            {
                                                /*echo '<div class="col-sm-4">'.$this->Html->link(
                                                    $this->Html->div('info-box', 
                                                        $this->Html->tag('span', $this->Html->tag('i', '', array('class' => 'ion-android-add-circle')), array('class' => 'info-box-icon bg-green')).
                                                        $this->Html->div('info-box-content', 
                                                            $this->Html->tag('span', __($m['title']), array('class' => 'info-box-text')).
                                                            $this->Html->tag('span', __($menu_item['title']), array('class' => 'info-box-number'))
                                                        )
                                                    ),
                                                    $link,
                                                    array(
                                                        'escape' => false,
                                                    )
                                                ).'</div>';*/
                                            }
                                            else
                                            {
                                                if($menu_item['title'] == 'Tratos' || $menu_item['title'] == 'Bonos')
                                                {
                                                    //echo 'cont: '.$menu_item['controller'].' action:'.$menu_item['action'].'<br>';
                                                    /*echo '<div class="col-sm-4">'.$this->Html->link(
                                                        $this->Html->div('info-box', 
                                                            $this->Html->tag('span', $this->Html->tag('i', '', array('class' => 'ion-clipboard')), array('class' => 'info-box-icon bg-aqua')).
                                                            $this->Html->div('info-box-content', 
                                                                $this->Html->tag('span', __($m['title']), array('class' => 'info-box-text')).
                                                                $this->Html->tag('span', __($menu_item['title']), array('class' => 'info-box-number'))
                                                            )
                                                        ),
                                                        $link,
                                                        array(
                                                            'escape' => false,
                                                        )
                                                    ).'</div>';*/
                                                }
                                                elseif($menu_item['action'] != 'assist_month_detail')
                                                {
                                                    //echo 'cont: '.$menu_item['controller'].' action:'.$menu_item['action'].'<br>';
                                                    echo '<div class="col-sm-4">'.$this->Html->link(
                                                        $this->Html->div('info-box', 
                                                            $this->Html->tag('span', $this->Html->tag('i', '', array('class' => 'ion-clipboard')), array('class' => 'info-box-icon bg-aqua')).
                                                            $this->Html->div('info-box-content', 
                                                                $this->Html->tag('span', __($m['title']), array('class' => 'info-box-text')).
                                                                $this->Html->tag('span', __($menu_item['title']), array('class' => 'info-box-number'))
                                                            )
                                                        ),
                                                        $link,
                                                        array(
                                                            'escape' => false,
                                                        )
                                                    ).'</div>';
                                                }
                                            }
                                        }
                                    }
                                ?>
                        <?php endif;?>
                    <?php endforeach;?>
                <?php endif;?>


                <?php if($budget):?>
                    <div class="col-sm-4">

                        <?php if ($this->Access->verifyAction('Spends', 'overview') == true): ?>
                                <?= 
                                    $this->Html->link(
                                        $this->Html->div('info-box', 
                                            $this->Html->tag('span', $this->Html->tag('i', '', array('class' => 'fa fa-money')), array('class' => 'info-box-icon bg-aqua')).
                                            $this->Html->div('info-box-content', 
                                                $this->Html->tag('span', __('Gastos'), array('class' => 'info-box-text')).
                                                $this->Html->tag('span', __('Control de Presupuesto'), array('class' => 'info-box-number'))
                                            )
                                        ),
                                        '/spends/overview/'.$budget->id,
                                        array(
                                            'escape' => false
                                        )
                                    );
                                ?>
                        <?php endif;?>

                    </div>
                <?php endif;?>

                <?php if (!empty($buildings_budgets[$sf_building->CodArn])):?>

                    <?php
                        if ((($this->Access->verifyAccessByKeyword('gerente_general') == true) || ($this->Access->verifyAccessByKeyword('gerente_finanzas') == true) || ($this->Access->verifyAccessByKeyword('coordinador_proyectos') == true)) &&  (($nextState == 6) || ($nextState < 7 )))
                        {
                            if ($this->Access->verifyAction('BudgetApprovals', 'change') == true)
                            {
                                if ($nextState == 3 || $nextState == 4)
                                {
                                    $textState = __('Aprobar Presupuesto');
                                    $iconState = 'fa fa-check-square-o';

                                    echo '<div class="col-sm-4">'.$this->Html->link(
                                        $this->Html->div('info-box', 
                                            $this->Html->tag('span', $this->Html->tag('i', '', array('class' => $iconState)), array('class' => 'info-box-icon bg-yellow')).
                                            $this->Html->div('info-box-content', 
                                                $this->Html->tag('span', __('Acciones'), array('class' => 'info-box-text')).
                                                $this->Html->tag('span', __($textState), array('class' => 'info-box-number'))
                                            )
                                        ),
                                        ['controller' => 'budgetApprovals', 'action' => 'change', $budget->id],
                                        array(
                                            'escape' => false,
                                        )
                                    ).'</div>';
                                }
                                /*elseif ($nextState == 6)
                                {
                                    $textState = __('Finalizar');
                                    $iconState = 'fa fa-flag-checkered';
                                }*/

                                /*echo '<div class="col-sm-4">'.$this->Html->link(
                                    $this->Html->div('info-box', 
                                        $this->Html->tag('span', $this->Html->tag('i', '', array('class' => $iconState)), array('class' => 'info-box-icon bg-yellow')).
                                        $this->Html->div('info-box-content', 
                                            $this->Html->tag('span', __('Acciones'), array('class' => 'info-box-text')).
                                            $this->Html->tag('span', __($textState), array('class' => 'info-box-number'))
                                        )
                                    ),
                                    ['controller' => 'budgetApprovals', 'action' => 'change', $budget->id],
                                    array(
                                        'escape' => false,
                                    )
                                ).'</div>';*/
                            }
                        }
                    ?>
                <?php endif;?>
            <?php endif;?>

        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-material-blue-grey-700">
                <!-- panel heading -->
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Información
                        </h3>
                    </div>
                    <div class="panel-body">
                    <!-- Panel content -->
                        <div class="row">
                            <div class="col-sm-12"><?php
                                if(!empty($budget)) {
                                    echo $this->Element('info_budget_building');
                                    echo $this->Element('info_budget_detail');
                                }else{
                                    echo "No se encontraron presupuestos asociados a la obra";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel-group" id="accordion">

                    <?php if (!empty($buildings_budgets[$sf_building->CodArn])):?>

                        <div class="panel panel-material-blue-grey-700">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"><span class="fa fa-building">
                                    </span> Obra</a>
                                </h4>
                            </div>
                            <div id="collapseOne" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <table class="table">

                                        <?php if ($this->Access->verifyAccessByKeyword('gerente_finanzas') == true || $this->Access->verifyLevel(9) == true) : ?>
                                                <!--<tr>
                                                    <td>
                                                        <?php //echo $this->Html->link(__('Estado Actual Obra'), /*['controller' => 'budgets', 'action' => 'global_state', $buildings_budgets[$sf_building->CodArn]['budget_id']]*/ 'javascript:void(0);', ['class' => 'btn btn-block btn-sm btn-default', 'disabled' => 'disabled']); ?>
                                                    </td>
                                                </tr>-->
                                        <?php endif; ?>

                                        <?php if ($this->Access->verifyAction('Workers', 'index') == true): ?>
                                            <!--<tr>
                                                <td>
                                                    <?php /*echo $this->Form->postLink(__('Ver Trabajadores'), ['controller' => 'workers', 'action' => 'index'], ['data' => array('SfWorkerBuildings' => array('codArn' => $sf_building->CodArn)), 'class' => 'btn btn-block btn-sm btn-primary']);*/ ?>
                                                </td>
                                            </tr>-->
                                        <?php endif;?>

                                        <?php if (!empty($buildings_budgets[$sf_building->CodArn])):?>

                                            <?php if ($this->Access->verifyAction('Buildings', 'edit') == true): ?>
                                                <tr class="separator-row">
                                                    <td>
                                                        <?= $this->Html->link(__('Editar Información Obra'), ['controller' => 'buildings', 'action' => 'edit', $sf_building->CodArn], ['class' => 'btn btn-block btn-sm btn-material-orange-900', 'escape' => false]); ?>
                                                    </td>
                                                </tr>
                                            <?php endif;?>

                                             <?php if ($this->Access->verifyAction('Buildings', 'change_active') == true): ?>
                                                <tr>
                                                    <td>
                                                        <?php 
                                                            if($buildings_budgets[$sf_building->CodArn]['active'])
                                                            {
                                                                echo $this->Html->link(__('Bloquear Obra'), ['action' => 'change_active', $sf_building->CodArn], ['class' => 'btn btn-block btn-sm btn-material-orange-900 confirm']);
                                                            }
                                                            else
                                                            {
                                                                echo $this->Html->link(__('Activar Obra'), ['action' => 'change_active', $sf_building->CodArn], ['class' => 'btn btn-block btn-sm btn-material-orange-900 confirm']);
                                                            }
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php endif;?>

                                        <?php else:?>

                                             <?php if ($this->Access->verifyAction('Buildings', 'ignore_building') == true): ?>
                                                <tr>
                                                    <td>
                                                        <?= $this->Html->link( __('Ignorar Obra'), ['controller' => 'buildings', 'action' => 'ignore_building', $parche_softland_id], ['class' => 'btn btn-block btn-sm btn-material-orange-900 confirm', 'escape' => false]); ?>
                                                    </td>
                                                </tr>
                                            <?php endif;?>

                                        <?php endif;?>

                                        <?php if(isset($buildings_budgets[$sf_building->CodArn]['budget_id'])): ?>

                                            <?php if ($this->Access->verifyAction('BudgetItems', 'reset_obra') == true): ?>
                                                <tr>
                                                    <td>
                                                        <?= $this->Html->link(__('Resetear obra'), ['controller' => 'budget_items', 'action' => 'reset_obra', $buildings_budgets[$sf_building->CodArn]['budget_id']], ['class' => 'btn btn-block btn-sm btn-material-red-800 confirm']); ?>
                                                    </td>
                                                </tr>
                                            <?php endif;?>

                                        <?php endif;?>

                                        <?php if (!empty($buildings_budgets[$sf_building->CodArn])):?>

                                            <?php
                                                if ((($this->Access->verifyAccessByKeyword('gerente_general') == true) || ($this->Access->verifyAccessByKeyword('gerente_finanzas') == true) || ($this->Access->verifyAccessByKeyword('coordinador_proyectos') == true)) && ($nextState < 7 ))
                                                {
                                                    if ($this->Access->verifyAction('BudgetApprovals', 'change') == true)
                                                    {
                                                        if ($nextState == 6)
                                                        {
                                                            $textState = __('Finalizar');

                                                            echo '<tr><td>'.$this->Html->link($textState, ['controller' => 'budgetApprovals', 'action' => 'change', $budget->id], ['id' => 'formState', 'class' => 'btn btn-block btn-sm btn-primary']).'</td></tr>';
                                                        }   
                                                    }
                                                }
                                            ?>

                                        <?php endif;?>

                                    </table>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>

                        <div class="panel panel-material-blue-grey-700">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"><span class="fa fa-building">
                                    </span> Obra</a>
                                </h4>
                            </div>
                            <div id="collapseOne" class="panel-collapse collapse in">
                                <div class="panel-body">
                                    <table class="table">
                                        
                                        <?php if ($this->Access->verifyAction('Budgets', 'add') == true): ?>
                                            <tr>
                                                <td>
                                                    <?= $this->Html->link(__('Agregar Presupuesto'), ['controller' => 'budgets', 'action' => 'add', $parche_softland_id], ['class' => 'btn btn-block btn-sm bg-green ']);?>
                                                </td>
                                            </tr>
                                        <?php endif;?>

                                         <?php if ($this->Access->verifyAction('Buildings', 'ignore_building') == true): ?>
                                            <tr>
                                                <td>
                                                    <?= $this->Html->link( __('Ignorar Obra'), ['controller' => 'buildings', 'action' => 'ignore_building', $parche_softland_id], ['class' => 'btn btn-block btn-sm bg-red confirm', 'escape' => false]); ?>
                                                </td>
                                            </tr>
                                        <?php endif;?>

                                    </table>
                                </div>
                            </div>
                        </div>

                    <?php endif;?>

                    <?php if (!empty($buildings_budgets[$sf_building->CodArn])):?>
                        <div class="panel panel-material-blue-grey-700">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"><span class="fa fa-file-text">
                                    </span> Presupuesto</a>
                                </h4>
                            </div>
                            <div id="collapseTwo" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <table class="table">

                                         <?php if ($this->Access->verifyAction('Budgets', 'review') == true): ?>
                                            <tr>
                                                <td>
                                                    <?= $this->Html->link(__('Ver Presupuesto'), $verPpto, ['class' => 'btn btn-sm btn-primary btn-block']);?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?=$this->Html->link('Ver Gastos Generales', array_merge($verPpto, ['?'=>['extra'=>'3']]), ['class' => 'btn btn-sm btn-primary btn-block']);?>    
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?=$this->Html->link('Ver Adicionales', array_merge($verPpto, ['?'=>['extra'=>'1']]), ['class' => 'btn btn-sm btn-primary btn-block']);?>
                                                </td>
                                            </tr>
                                        <?php endif;?>

                                        <?php if ($this->Access->verifyAction('Budgets', 'review') == true): ?>
                                            <tr>
                                                <td>
                                                    <?=$this->Html->link('Ver Gastos No Considerados', array_merge($verPpto, ['?'=>['extra'=>'2']]), ['class' => 'btn btn-sm btn-primary btn-block']);?>
                                                </td>
                                            </tr>
                                        <?php endif;?>

                                        <?php 
                                            if ($this->Access->verifyAccessByKeyword('gerente_finanzas') == true || $this->Access->verifyLevel(9) == true)
                                            {
                                                if (count($budget->budget_items) > 0)
                                                {
                                                    if ($state->id < 6 )
                                                    {
                                                        if ($this->Access->verifyAction('Budgets', 'add_extra') == true)
                                                        {
                                                            echo '<tr class="separator-row"><td>'.$this->Html->link(__('Agregar Adicionales'), ['controller' => 'budgets', 'action' => 'add_extra', $budget->id], ['class' => 'btn btn-block btn-sm btn-material-orange-900']).'</td></tr>';
                                                        }
                                                    }
                                                }
                                            }
                                        ?>

                                        <?php 
                                            if ($this->Access->verifyAccessByKeyword('gerente_finanzas') == true || $this->Access->verifyLevel(9) == true)
                                            {
                                                if (count($budget->budget_items) > 0)
                                                {
                                                    if ($state->id < 6 )
                                                    {
                                                        if ($this->Access->verifyAction('Budgets', 'add_expense') == true)
                                                        {
                                                            echo '<tr class="separator-row"><td>'.$this->Html->link(__('Agregar Gastos no Considerados'), ['controller' => 'budgets', 'action' => 'add_expense', $budget->id], ['class' => 'btn btn-block btn-sm btn-material-orange-900']).'</td></tr>';
                                                        }
                                                    }
                                                }
                                            }
                                        ?>

                                        <?php if ($this->Access->verifyAction('Budgets', 'edit') == true): ?>
                                            <tr class="separator-row">
                                                <td>
                                                    <?= $this->Html->link(__('Editar Configuración Del Presupuesto'), ['controller' => 'budgets', 'action' => 'edit', $buildings_budgets[$sf_building->CodArn]['budget_id']], ['class' => 'btn btn-block btn-sm btn-primary']);?>
                                                </td>
                                            </tr>
                                        <?php endif;?>

                                        <?php 
                                            if ($this->Access->verifyAccessByKeyword('gerente_finanzas') == true || $this->Access->verifyLevel(9) == true)
                                            {
                                                 if (count($budget->budget_items) == 0 && $state->id == 1)
                                                {
                                                    if ($this->Access->verifyAction('Budgets', 'add') == true)
                                                    {
                                                        echo '<tr><td>'.$this->Html->link(__('Cargar excel Presupuesto'), ['controller' => 'budgets', 'action' => 'add', $budget->building->softland_id], ['class' => 'btn btn-block btn-sm btn-material-orange-900']).'</td></tr>';
                                                    }
                                                }
                                            }
                                        ?>


                                        <?php 
                                            if ($this->Access->verifyAccessByKeyword('gerente_finanzas') == true || $this->Access->verifyLevel(9) == true)
                                            {
                                                if (count($budget->budget_items) > 0)
                                                {
                                                    if ($state->id < 4 )
                                                    {
                                                        if ($this->Access->verifyAction('BudgetItems', 'remove_all') == true)
                                                        {
                                                            echo '<tr><td>'.$this->Html->link(__('Remover todos los Items'), ['controller' => 'budget_items', 'action' => 'remove_all', $budget->id], ['id' => 'formDeleteItems','class' => 'btn btn-block btn-sm btn-danger']).'</td></tr>';
                                                        }
                                                    }
                                                }
                                            }
                                        ?>

                                        <?php if ($this->Access->verifyLevel(9) == true && $state->id <= 2):?>
                                            
                                            <?php if ($this->Access->verifyAction('BudgetItems', 'reset_obra') == true): ?>
                                                <tr>
                                                    <td>
                                                        <?= $this->Html->link(__('Eliminar presupuesto'), ['controller' => 'budget_items', 'action' => 'reset_obra', $buildings_budgets[$sf_building->CodArn]['budget_id'], "false"], ['class' => 'btn btn-block btn-sm btn-material-red-800 confirm']); ?>
                                                    </td>
                                                </tr>
                                            <?php endif;?>

                                        <?php endif;?>


                                        <?php if (!empty($buildings_budgets[$sf_building->CodArn])):?>
                                            <?php if ($this->Access->verifyAction('Budgets', 'comment') == true): ?>
                                                <tr>
                                                    <td>
                                                        <?= $this->Html->link(__('Comentar Presupuesto'), ['controller' => 'budgets', 'action' => 'comment', $budget->id], ['id' => 'formComment', 'class' => 'btn btn-block btn-sm btn-material-orange-900']); ?>
                                                    </td>
                                                </tr>
                                            <?php endif;?>

                                            <?php
                                                if ((($this->Access->verifyAccessByKeyword('gerente_general') == true) || ($this->Access->verifyAccessByKeyword('gerente_finanzas') == true) || ($this->Access->verifyAccessByKeyword('coordinador_proyectos') == true)) &&  (($nextState == 6) || ($nextState < 7 )))
                                                {
                                                    if ($this->Access->verifyAction('BudgetApprovals', 'change') == true)
                                                    {
                                                        if ($nextState == 3 || $nextState == 4)
                                                        {
                                                            $textState = __('Aprobar Presupuesto');

                                                            echo '<tr><td>'.$this->Html->link($textState, ['controller' => 'budgetApprovals', 'action' => 'change', $budget->id], ['id' => 'formState', 'class' => 'btn btn-block btn-sm btn-material-orange-900']).'</td></tr>';
                                                        }
                                                    }
                                                }
                                            ?>
                                        <?php endif;?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif;?>

                    <?php if (!empty($buildings_budgets[$sf_building->CodArn])):?>
                        <?php foreach($menu AS $m):?>
                            <?php if(array_search($m['title'], $menus_to_show)!==false):?>

                                <?php
                                    $iconClass = '';
                                    if($m['title'] == 'Avance')
                                    {
                                        $iconClass = 'ion ion-arrow-graph-up-right';
                                    }
                                    elseif($m['title'] == 'RRHH')
                                    {
                                        
                                        $iconClass = 'glyphicon glyphicon-user';
                                    }
                                ?>
                                <div class="panel panel-material-blue-grey-700">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#<?=$m['title'];?>">
                                            <span class="<?=$iconClass;?>">
                                            </span>
                                            <?=$m['title'];?>
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="<?=$m['title'];?>" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <table class="table">
                                                <?php
                                                    if($m['title'] == 'RRHH')
                                                    {
                                                        if ($this->Access->verifyAction('Workers', 'index') == true)
                                                        {
                                                            echo '<tr><td>'.$this->Form->postLink(__('Ver Trabajadores'), ['controller' => 'workers', 'action' => 'index'], ['data' => array('SfWorkerBuildings' => array('codArn' => $sf_building->CodArn)), 'class' => 'btn btn-block btn-sm btn-primary', 'escape' => false]).'</td></tr>';
                                                        } 
                                                    }

                                                    foreach($m['items'] AS $menu_item)
                                                    {
                                                        $words = explode('_', $menu_item['controller']);
                                                        $controller = '';

                                                        if(count($words) > 1)
                                                        {
                                                            foreach($words as $key => $value)
                                                            {
                                                                $controller .= ucwords($value);
                                                            }
                                                        }
                                                        else
                                                        {
                                                            $controller = ucwords($menu_item['controller']);
                                                        }

                                                        if ($this->Access->verifyAction($controller, $menu_item['action']) == true)
                                                        {
                                                            if($menu_item['action'] != 'assist_month_detail')
                                                            {
                                                                $link = ['controller' => $menu_item['controller'], 'action' => $menu_item['action']];
                                                                $btnClass = 'btn-primary';
                                                                $extraAttr = array();

                                                                if(isset($menu_item['extra']))
                                                                {
                                                                    $link[] = $menu_item['extra'];
                                                                }

                                                                echo '<tr><td>'.$this->Html->link($menu_item['title'], $link, ['escape' => false, 'tabindex' => '-1', 'class' => 'btn btn-sm '.$btnClass.' btn-block', $extraAttr]).'</td></tr>';
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php endif;?>
                        <?php endforeach;?>
                    <?php endif;?>


                    <?php if($budget):?>
                        <?php if ($this->Access->verifyAction('Spends', 'overview') == true): ?>
                            <div class="panel panel-material-blue-grey-700">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseFive"><span class="fa fa-money">
                                        </span> Gastos</a>
                                    </h4>
                                </div>
                                <div id="collapseFive" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <table class="table">
                                            <tr>
                                                <td>
                                                    <?php echo $this->Html->link(__('Control de Presupuesto'), '/spends/overview/'.$budget->id, ['class' => 'btn btn-sm btn-primary btn-block']); ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endif;?>
                    <?php endif;?>
                </div>
            </div>
        </div>
        <?php if (!empty($buildings_budgets[$sf_building->CodArn])):?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="info-box bg-green">
                        <span class="info-box-icon"><i class="ion ion-settings"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Avance Real</span>
                             <span class="info-box-number"><?= $percentages_building['avance_real'];?>%</span>

                            <div class="progress">
                                <div class="progress-bar" style="width: <?= $percentages_building['avance_real'];?>%"></div>
                            </div>
                            <span class="progress-description">
                                Cantidad avanzada de la obra
                            </span>
                        </div>
                    </div>
                    <div class="info-box bg-aqua">
                        <span class="info-box-icon"><i class="ion ion-arrow-graph-up-right"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Avance Proyectado</span>
                            <span class="info-box-number"><?= $percentages_building['avance_proyectado'];?>%</span>

                            <div class="progress">
                                <div class="progress-bar" style="width: <?= $percentages_building['avance_proyectado'];?>%"></div>
                            </div>
                            <span class="progress-description">
                                Cantidad de avance proyectado
                            </span>
                        </div>
                    </div>
                    <div class="info-box bg-yellow">
                        <span class="info-box-icon"><i class="ion ion-cash"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Avance Economico</span>
                            <span class="info-box-number"><?= $percentages_building['avance_economico'];?>%</span>

                            <div class="progress">
                                <div class="progress-bar" style="width: <?= $percentages_building['avance_economico'];?>%"></div>
                            </div>
                              <span class="progress-description">
                                Cantidad de avance economico
                              </span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif;?>

    </div>
</div>

<!-- Modales -->
<div id="modalComment" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Agregar Comentario</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(null, [
                    'url' => ['controller' => 'Budgets', 'action' => 'comment', $budget->id]
                ]); ?>
                <fieldset>
                    <?php
                        echo $this->Form->input('observation', ['label' => 'Comentario', 'type' => 'textarea', 'escape' => false]);
                    ?>
                </fieldset>
            </div>
            <div class="modal-footer">
                <?= $this->Form->button(__('Guardar')) ?>
                <?= $this->Form->end() ?>
                <button type="button" class="btn btn-flat btn-link" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<div id="modalState" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"><?= sprintf('¿Está seguro de ' . $textState . ' el presupuesto?') ?></h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create(null, [
                    'url' => ['controller' => 'BudgetApprovals', 'action' => 'change', $budget->id]
                ]); ?>
                <fieldset>
                    <legend>El Presupuesto tendrá el siguiente estado:
                        <span class="label label-info"><?= @$states[$nextState]; ?></span></legend>
                    <?php
                        echo $this->Form->input('budget_state_id', ['label' => 'Estado del presupuesto','type' => 'hidden', 'value' => $nextState]);
                        echo $this->Form->input('comment', ['label' => 'Comentario', 'type' => 'textarea', 'escape' => false]);
                    ?>
                </fieldset>
            </div>
            <div class="modal-footer">
                <?= $this->Form->button(__('Confirmar')) ?>
                <?= $this->Form->end() ?>
                <button type="button" class="btn btn-flat btn-link" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<div id="modalDelete" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Confirmación</h4>
            </div>
            <div class="modal-body">
                ¿Está seguro que desea eliminar el presupuesto del Sistema?
            </div>
            <div class="modal-footer">
                <?= $this->Form->postLink('Confirmar', ['controller' => 'Budgets', 'action' => 'delete', $budget->id], ['class' => 'btn btn-material-orange-900']) ?>
                <button type="button" class="btn btn-flat btn-link" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<div id="modalDeleteItems" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Confirmación</h4>
            </div>
            <div class="modal-body">
                ¿Está seguro que desea eliminar las partidas al Presupuesto?
            </div>
            <div class="modal-footer">
                <?= $this->Html->link(__('Confirmar'), ['controller' => 'budget_items', 'action' => 'remove_all', $budget->id], ['class' => 'btn btn-material-orange-900']); ?>
                <button type="button" class="btn btn-flat btn-link" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->Element('modal_ajax'); ?>