<?=
// elementos estandares de la vista
$this->assign('title_text', __('Módulo Reportes de Remuneraciones'));
$this->assign('title_icon', 'groups');
$buttons = array();
// $buttons[] = ['title' => __('Todos los perfiles'), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/index'];
$this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
<!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Ver Detalles Reporte Remuneración solicitado</h3>
    </div>
    <div class="panel-body">
        <!-- Información General -->
        <?= $this->Element('info_budget_building'); ?>
        <div class="row">
            <div class="col-md-6 col-sm-6">
                <h3>Información Reporte</h3>
                <blockquote>
                    <h5><strong><?= 'Fecha solicitud reporte: '; ?></strong><?= $remunerationsReport->created->format('d-m-Y H:i'); ?></h5>
                    <h5><strong><?= 'Estado: '; ?></strong><?= $status[$remunerationsReport->status]; ?></h5>
                    <h5><strong><?= 'Mes: '; ?></strong><?= $remunerationsReport->month; ?></h5>
                    <h5><strong><?= 'Día de Corte: '; ?></strong><?= $remunerationsReport->day_cut; ?></h5>
                    <h5><strong><?= 'Día de Corte Previo: '; ?></strong><?= $remunerationsReport->day_cut_prev; ?></h5>
                    <h5><strong><?= 'Progreso: '; ?></strong></h5>
                    <?= $remunerationsReport->progress; ?>%
                    <div class="progress">
                        <div class="progress-bar" style="width: <?=$remunerationsReport->progress;?>%;"></div>
                    </div>
                    <?php if($remunerationsReport->progress>=100){
                        echo $this->Html->link('Descargar archivo',"/".$remunerationsReport->path);
                    } ?>
                </blockquote>
            </div>
        </div>
        <?= $this->Html->link('Volver', ['controller' => 'remunerations_reports', 'action' => 'index'], ['class' => 'btn btn-flat btn-link']); ?>
    </div>
</div>
