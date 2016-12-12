<?php
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Obras'));
// $this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">
            Lista de Obras Ignoradas
        </h3>
    </div>
    <div class="panel-body">
        <div class="row hidden-xs">
            <div class="col-sm-12">
                <?php
                $this->Paginator->templates([
                    'sort' => '<a class="btn btn-flat" href="{{url}}">{{text}}</a>',
                    'sortAsc' => '<a class="btn btn-flat btn-primary asc" href="{{url}}">{{text}}</a>',
                    'sortDesc' => '<a class="btn btn-flat btn-primary desc" href="{{url}}">{{text}}</a>',
                    'sortAscLocked' => '<a class="btn btn-flat btn-primary asc locked" href="{{url}}">{{text}}</a>',
                    'sortDescLocked' => '<a class="btn btn-flat btn-primary desc locked" href="{{url}}">{{text}}</a>',
                ]);
                ?>
                <?= $this->Paginator->sort('DesArn', __('Ordenar por Nombre A-Z'), ['class' => 'btn btn-flat']) ?>
                <?= $this->Paginator->sort('CodArn', __('Ordenar por Código 0-9'), ['class' => 'btn btn-flat']) ?>

                <?= $this->Html->link(__('Lista Obras Habilitadas'), ['action' => 'index'], ['class' => 'btn btn-md btn-flat btn-material-orange-900 pull-right']) ?>

            </div>
        </div>
        <div class="row hidden-xs">
            <?php foreach ($sf_buildings as $building):
                $urlBuilding = $this->Url->build(['controller' => 'buildings', 'action' => 'dashboard', $building->CodArn]);
                $urlBuilding = $this->Url->build(['controller' => 'buildings', 'action' => 'current', $building->CodArn, true])
            ?>
                <div class="col-sm-6 col-lg-4">
                    <div class="card" style="height: 284px; margin: 10px 1px; max-height: 284px;">
                        <div class="card-height-indicator"></div>
                        <div class="card-content">
                            <div class="card-image">
                                <?=$this->Html->image('planos.png', ['class' => 'card-img-top']);?>
                                <div class="card-image-headline text-left" style="background-color:rgba(238,238,238,0.85); color: #111; font-size: 2rem; padding: 0px 5px;">
                                    <p><?= h($building->DesArn) ?>&nbsp;<span class="badge"><?= $this->Number->format($building->CodArn) ?></span></p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <?php $group_id = $this->request->session()->read('Auth.User.group_id');
                                    if ($group_id == USR_GRP_GE_GRAL || $group_id == USR_GRP_COORD_PROY || $group_id == USR_GRP_GE_FINAN) :
                                        echo $this->Html->link(__('Habilitar'),
                                         ['controller' => 'buildings', 'action' => 'enable_building', $building->CodArn],
                                         ['class' => 'btn btn-material-orange-900']);
                                    endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="row visible-xs-block">
            <div class="col-xs-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
                <!-- Panel content -->
                <?= $this->Html->link(__('Lista Obras Habilitadas'), ['action' => 'index'], ['class' => 'btn btn-md btn-flat btn-material-orange-900 pull-right']) ?>


                    <?php if ($sf_buildings->count() > 0) : ?>
                        <?php
                        $this->Paginator->templates([
                            'sort' => '<a class="" href="{{url}}">{{text}}</a>',
                            'sortAsc' => '<a class=" asc" href="{{url}}">{{text}}</a>',
                            'sortDesc' => '<a class=" desc" href="{{url}}">{{text}}</a>',
                            'sortAscLocked' => '<a class=" asc locked" href="{{url}}">{{text}}</a>',
                            'sortDescLocked' => '<a class=" desc locked" href="{{url}}">{{text}}</a>',
                        ]);
                        ?>
                        <?php if ($sf_buildings->count() > 0) : ?>
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th><?= $this->Paginator->sort('DesArn', __('Obra')) ?> (<?= $this->Paginator->sort('CodArn', __('Área Negocio')) ?>)</th>
                                        <!-- <th><?= $this->Paginator->sort('created') ?></th> -->
                                        <th class="actions"><?= __('Acciones') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($sf_buildings as $building): ?>
                                    <tr>
                                        <td><?= h($building->DesArn) ?> &nbsp;<span class="badge"><?= $this->Number->format($building->CodArn) ?></span></td>
                                        <!-- TODO: Agregar cliente -->
                                        <!-- <td><?= h($building->created) ?></td> -->
                                        <td class="actions">
                                            <?php $group_id = $this->request->session()->read('Auth.User.group_id');
                                            if ($group_id == USR_GRP_GE_GRAL || $group_id == USR_GRP_COORD_PROY || $group_id == USR_GRP_GE_FINAN) :
                                                echo $this->Html->link(__('Habilitar'),
                                                 ['controller' => 'buildings', 'action' => 'enable_building', $building->CodArn],
                                                 ['class' => 'btn btn-sm btn-material-orange-900']);
                                            endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <h4>No hay Obras Ignoradas</h4>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>

                <?= $this->Html->link(__('Volver'), ['action' => 'index'], ['class' => 'btn btn-flat btn-link']) ?>
            </div>
        </div>

    </div>
</div>
