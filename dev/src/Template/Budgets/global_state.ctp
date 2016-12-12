<?php
use Ghunti\HighchartsPHP\Highchart;
use Ghunti\HighchartsPHP\HighchartJsExpr;
use Ghunti\HighchartsPHP\HighchartOption;
// elementos estandares de la vista
$this->assign('title_text', __('Módulo de Presupuestos'));
$this->assign('title_icon', 'users');
$buttons = array();
// $buttons[] = ['title' => __(''), 'class' => 'primary', 'icon' => 'plus', 'link' => '/groups/add'];
$this->set('buttons', $buttons);
?>
<div class="panel panel-material-blue-grey-700">
    <!-- panel heading -->
    <div class="panel-heading">
        <h3 class="panel-title">Ver Estado Actual Obra</h3>
    </div>
    <div class="panel-body">
        <!-- Panel content -->
        <?= $this->Element('info_budget_building'); ?>
        <?= $this->Element('info_budget_detail'); ?>
        <!-- Indicadores claves acumulados a la fecha -->
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h3 class="panel-title"><strong>Indicadores Claves del Presupuesto acumulados a la fecha: <?= $now_date->format('d-m-Y') ?></strong></h3>
            </div>
            <div class="panel-body">
                <!-- Panel content -->
                <table class="table table-striped">
                    <tr>
                        <td><h4>Total Costo Directo: </h4></td>
                        <td><h4><strong><?= moneda($budget->total_cost) ?></strong></h4></td>
                    </tr>
                    <tr>
                        <td><h4>Total Meta: </h4></td>
                        <td><h4><strong>(Pendiente desarrollo)</strong></h4></td>
                    </tr>
                    <tr>
                        <td><h4>Total Gasto Materiales: </h4></td>
                        <td><h4><strong><?= moneda($iconstruye_stats['sum_product_total']) ?></strong></h4></td>
                    </tr>
                    <tr>
                        <td><h4>Total Gasto Mano Obra: </h4></td>
                        <td><h4><strong><?= moneda($total_salaries) ?></strong></h4></td>
                    </tr>
                    <tr>
                        <td><h4>Total Gasto Subcontrato: </h4></td>
                        <td><h4><strong>(Pendiente desarrollo)</strong></h4></td>
                    </tr>
                </table>
                <h4>Llevamos gastado <strong><?= moneda($iconstruye_stats['sum_product_total'] + $total_salaries) ?></strong> del total del presupuesto de
                    <strong><?= moneda($budget->total_cost) ?></strong>, con meta <strong>$[Pendiente]</strong></h4>
            </div>
        </div>
        <!-- Avance proyectado sumatoria total obra -->
        <div class="panel panel-default">
            <div class="panel-body">
                <h4><strong>Avance Proyectado del Presupuesto </strong></h4>
                <h5><?= '<strong>Fecha Inicio: </strong>' . $budget->created->format('d-m-Y') . ' - <strong>Fecha Término: </strong>' . $budget_finish_date->format('d-m-Y') ?></h5>
                <p>Gráfico que muestra el avance en UF proyectado desde Fecha Inicio a Fin de la Obra. Los valores corresponden a la progresión mes a mes del acumulado total de la obra hasta la fecha actual.</p>
                <?php
                    $chart_progress_budget = new Highchart();
                    $chart_progress_budget->chart = array('renderTo' => 'progress_budget');
                    $chart_progress_budget->title = array('text' => 'Avance Proyectado y Real del Presupuesto por Mes');
                    $chart_progress_budget->subtitle->text = 'Avance a la fecha actual';
                    $chart_progress_budget->credits->enabled = false;
                    $chart_progress_budget->xAxis->categories = array_values($months);
                    $oneYaxis = new HighchartOption();
                    $oneYaxis->labels->formatter = new HighchartJsExpr("function() { return this.value +' días'; }");
                    $oneYaxis->labels->style->color = "#FF9800";
                    $oneYaxis->title->text = "Días Hábiles";
                    $oneYaxis->title->style->color = "#FF9800";
                    $oneYaxis->opposite = true;
                    $twoYaxis = new HighchartOption();
                    $twoYaxis->gridLineWidth = 1;
                    $twoYaxis->title->text = "Total " . $budget->currencies_values[0]['currency']['name'];
                    $twoYaxis->title->style->color = "#4572A7";
                    $twoYaxis->labels->formatter = new HighchartJsExpr("function() { return this.value +' " . $budget->currencies_values[0]['currency']['name'] . "'; }");
                    $twoYaxis->labels->style->color = "#4572A7";
                    $twoYaxis->opposite = false;
                    $chart_progress_budget->yAxis = array($oneYaxis, $twoYaxis);
                    $chart_progress_budget->tooltip->formatter = new HighchartJsExpr(
                        "function() {
                        var unit = {
                          'Días Hábiles': ' días',
                          'Avance Proyectado': ' " . $budget->currencies_values[0]['currency']['name'] . "',
                          'Avance Planificado': ' " . $budget->currencies_values[0]['currency']['name'] . "',
                          'Avance Real': ' " . $budget->currencies_values[0]['currency']['name'] . "',
                        }[this.series.name];
                        return '' + this.x +': '+ this.y +' '+ unit; }");
                    $chart_progress_budget->chart->zoomType = "xy";
                    $chart_progress_budget->series[] = array(
                        'name' => "Avance Proyectado",
                        'color' => "#4572A7",
                        'type' => "column",
                        'yAxis' => 1,
                        'data' => array_values($budget_progress_info['proyected_progress_budget'])
                    );
                    $chart_progress_budget->series[] = array(
                        'name' => "Avance Planificado",
                        'color' => "#2196F3",
                        'type' => "column",
                        'yAxis' => 1,
                        'data' => array_values($budget_progress_info['proyected_progress_schedules'])
                    );
                    $chart_progress_budget->series[] = array(
                        'name' => "Avance Real",
                        'color' => "#69D830",
                        'type' => "column",
                        'yAxis' => 1,
                        'data' => array_values($budget_progress_info['overall_progress_schedules'])
                    );
                    $chart_progress_budget->series[] = array(
                        'name' => "Días Hábiles",
                        'color' => "#FF9800",
                        'type' => "spline",
                        'data' => array_values($total_days_months)
                    );
                 ?>
                 <div id="progress_budget" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
            </div>
        </div>
        <!-- Avance proyectado sumatoria total obra -->
        <div class="panel panel-default">
            <div class="panel-body">
                <h4><strong>Avance Proyectado del Presupuesto comparativo por Mes (en base a días hábiles)</strong></h4>
                <h5><?= '<strong>Fecha Inicio: </strong>' . $budget->created->format('d-m-Y') . ' - <strong>Fecha Término: </strong>' . $budget_finish_date->format('d-m-Y') ?></h5>
                <p>Gráfico que muestra el avance del presupuesto en UF, proyectado desde Fecha Inicio a Fin de la Obra, para cada mes en base a los días hábiles. Los valores de avance corresponde al acumulado del mes (planificado y real), el proyectado en base a la cantidad de días hábiles del mes.</p>
                <?php
                    $chart_progress_month_compare = new Highchart();
                    $chart_progress_month_compare->chart = array('renderTo' => 'progress_month_compare');
                    $chart_progress_month_compare->title = array('text' => 'Avance Proyectado y Real del Presupuesto por Mes');
                    $chart_progress_month_compare->subtitle->text = 'Avance a la fecha actual';
                    $chart_progress_month_compare->xAxis->categories = array_values($months);
                    $chart_progress_month_compare->yAxis->title->text = 'Total ' . $budget->currencies_values[0]['currency']['name'];
                    $chart_progress_month_compare->tooltip->enabled = true;
                    $chart_progress_month_compare->credits->enabled = false;
                    $chart_progress_month_compare->plotOptions->line->dataLabels->enabled = true;
                    $chart_progress_month_compare->plotOptions->line->enableMouseTracking = true;
                    $firstYaxis = new HighchartOption();
                    $firstYaxis->gridLineWidth = 0;
                    $firstYaxis->title->text = "Días Hábiles";
                    $firstYaxis->title->style->color = "#FF9800";
                    $firstYaxis->labels->formatter = new HighchartJsExpr("function() {return this.value +' días'; }");
                    $firstYaxis->labels->style->color = "#FF9800";
                    $firstYaxis->opposite = true;
                    $fourthYaxis = new HighchartOption();
                    $fourthYaxis->gridLineWidth = 1;
                    $fourthYaxis->title->text = "Total UF";
                    $fourthYaxis->title->style->color = "#4572A7";
                    $fourthYaxis->labels->formatter = new HighchartJsExpr("function() { return this.value +' " . $budget->currencies_values[0]['currency']['name'] . "'; }");
                    $fourthYaxis->labels->style->color = "#4572A7";
                    $fourthYaxis->opposite = false;
                    $chart_progress_month_compare->yAxis = array($firstYaxis, $fourthYaxis);
                    $chart_progress_month_compare->tooltip->formatter = new HighchartJsExpr(
                        "function() {
                        var unit = {
                          'Días Hábiles': ' días',
                          'Avance Proyectado': ' " . $budget->currencies_values[0]['currency']['name'] . "',
                          'Avance Planificado': ' " . $budget->currencies_values[0]['currency']['name'] . "',
                          'Avance Real': ' " . $budget->currencies_values[0]['currency']['name'] . "',
                        }[this.series.name];
                        return '' + this.x +': '+ this.y +' '+ unit; }");
                    $chart_progress_month_compare->series[] = array(
                        'type' => "column",
                        'name' => "Avance Proyectado",
                        'color' => "#4572A7",
                        'yAxis' => 1,
                        'data' => array_values($budget_progress_compare_months_info['proyected_progress_budget'])
                    );
                    $chart_progress_month_compare->series[] = array(
                        'type' => "column",
                        'name' => "Avance Planificado",
                        'color' => "#2196F3",
                        'yAxis' => 1,
                        'data' => array_values($budget_progress_compare_months_info['proyected_progress_schedules'])
                    );
                    $chart_progress_month_compare->series[] = array(
                        'type' => "column",
                        'name' => "Avance Real",
                        'color' => "#69D830",
                        'yAxis' => 1,
                        'data' => array_values($budget_progress_compare_months_info['overall_progress_schedules'])
                    );
                    $chart_progress_month_compare->series[] = array(
                        'type' => "spline",
                        'name' => "Días Hábiles",
                        'color' => "#FF9800",
                        'data' => array_values($total_days_months)
                    );
                 ?>
                 <div id="progress_month_compare" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
            </div>
        </div>
        <!-- Detalle Avance Planificaciones obra -->
        <div class="panel panel-default">
            <div class="panel-body">
                    <div class="col-sm-6"><h4><strong>Información Planificaciones de obra </strong></h4></div>
                    <div class="col-sm-6 text-right"><?= $this->Html->link('Ver planificaciones',['controller'=>'schedules','action'=>'index','?'=>['building_id' => $budget->building_id]],['class'=>'btn btn-xs btn-material-orange-900']) ?></div>
                    <div class="col-sm-12">
                        <h5><strong>Total Planificaciones a la fecha: </strong><?= $schedules_progress_info['total_schedules'] ?></h5>
                        <p>Gráfico que muestra el avance del presupuesto en UF, comparando el avance planificado versus el avance real de cada planificación. Además, se agrega la información del número total de partidas de cada planificación.</p>
                   </div>

                <?php
                    $schedules_info = array();
                    $proyected_progress_values = array();
                    $overall_progress_values = array();
                    $total_days_values = array();
                    $total_budget_items = array();
                    foreach ($schedules_progress_info as $schedule_progress_info) :
                        if (count($schedule_progress_info) > 1) :
                            $schedules_info['names'][$schedule_progress_info['name']] = $schedule_progress_info['start_date']->format('d/m/Y') . ':  ' . $schedule_progress_info['name']
                             . ' (Días: ' . $schedule_progress_info['total_days'] . ')';
                            $schedules_info['proyected_progress'][$schedule_progress_info['name']] = $schedule_progress_info['proyected_progress'];
                            $schedules_info['overall_progress'][$schedule_progress_info['name']] = $schedule_progress_info['overall_progress'];
                            $schedules_info['total_days'][$schedule_progress_info['name']] = $schedule_progress_info['total_days'];
                            $schedules_info['total_budget_items'][$schedule_progress_info['name']] = $schedule_progress_info['total_budget_items'];
                        endif;
                    endforeach;
                    $chart_schedules_progress = new Highchart();
                    $chart_schedules_progress->chart->renderTo = "schedules_progress";
                    $chart_schedules_progress->credits->enabled = false;
                    $chart_schedules_progress->title->text = "Detalle Planificaciones - Avance de Obra (Proyectado y Real en " . $budget->currencies_values[0]['currency']['name'] . ")";
                    $chart_schedules_progress->xAxis->categories = array_values($schedules_info['names']);
                    $secondaryYaxis = new HighchartOption();
                    $secondaryYaxis->gridLineWidth = 0;
                    $secondaryYaxis->title->text = "";
                    $secondaryYaxis->labels->formatter = new HighchartJsExpr("function() { return ; }");
                    $secondaryYaxis->opposite = false;
                    $tertiaryYaxis = new HighchartOption();
                    $tertiaryYaxis->gridLineWidth = 1;
                    $tertiaryYaxis->title->text = "Total UF";
                    $tertiaryYaxis->title->style->color = "#4572A7";
                    $tertiaryYaxis->labels->formatter = new HighchartJsExpr("function() {return this.value +' " . $budget->currencies_values[0]['currency']['name'] . "'; }");
                    $tertiaryYaxis->labels->style->color = "#4572A7";
                    $tertiaryYaxis->opposite = false;
                    $chart_schedules_progress->yAxis = array($secondaryYaxis, $tertiaryYaxis);
                    $chart_schedules_progress->tooltip->formatter = new HighchartJsExpr(
                        "function() {
                        var unit = {
                          'Total Partidas': ' partidas',
                          'Avance Planificado': ' " . $budget->currencies_values[0]['currency']['name'] . "',
                          'Avance Real': ' " . $budget->currencies_values[0]['currency']['name'] . "',
                        }[this.series.name];
                        return '' + this.x +': '+ this.y +' '+ unit; }");
                    $chart_schedules_progress->series[] = array(
                        'name' => "Avance Planificado",
                        'color' => "#2196F3",
                        'type' => "column",
                        'yAxis' => 1,
                        'data' => array_values($schedules_info['proyected_progress'])
                    );
                    $chart_schedules_progress->series[] = array(
                        'name' => "Avance Real",
                        'color' => "#69D830",
                        'type' => "column",
                        'yAxis' => 1,
                        'data' => array_values($schedules_info['overall_progress'])
                    );
                    $chart_schedules_progress->series[] = array(
                        'name' => "Total Partidas",
                        'color' => "#AA4643",
                        'type' => "spline",
                        'data' => array_values($schedules_info['total_budget_items']),
                        'marker' => array(
                            'enabled' => false
                        ),
                        'dashStyle' => "shortdot"
                    );
                ?>
                <div class="col-sm-12">
                    <div id="schedules_progress" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                </div>
            </div>
        </div>
        <!-- Chart EDPs -->
        <?php
            $edps_total_acc = array('total'=>0,'neto'=>0,'iva'=>0,'direct_cost'=>0,'liquid_pay'=>0,'total_currency'=>0,'total_cost'=>0);
            // genero fechas y data
            $edps_value = array_map(function(){return 0;},$months);
            $edp_dates = $months;
            // Seteo estados EDP en cero
            foreach ($edps_states as $key => $edp_state) {
               $edp_states_list[$edp_state['id']]['qty'] = 0;
               $edp_states_list[$edp_state['id']]['desc'] =$edp_state['description'];
            }
            // contar edp segun tipo
            foreach ($budget['payment_statements'] as $key => $payment) {
                // estado del EDP
                $payment_state = $payment['payment_statement_state']['id'];
                // + 1 al estado
                $edp_states_list[$payment_state]['qty'] += 1;
                //Total acumulado en EDP
                $edps_total_acc['liquid_pay'] += $payment['liquid_pay'];
                $edps_total_acc['total_currency'] += round($payment['total'] / $budget->currencies_values[0]['value'],2);
                $edps_total_acc['total'] += $payment['total'];
                $edps_total_acc['neto'] += $payment['total_net'];
                $edps_total_acc['iva'] += $payment['tax'];
                $edps_total_acc['direct_cost'] += $payment['total_direct_cost'] * $budget->currencies_values[0]['value'];
                //$edps_total_acc['total_cost'] += $payment['total_cost'];
                // Fechas y Montos
                $fecha = $payment['billing_date']->format('Y_m');
                $edp_value = $payment['total_cost'];
                // agrupo segun mes
                if(isset($edp_dates[$fecha])){
                    //sumo al mes correspondiente
                    $edps_value[$fecha] += $edp_value;
                }
                else{
                    //creo fecha en array
                    $edps_value[$fecha] = $edp_value;
                    $edp_dates[$fecha] = $payment['billing_date']->format('F - Y');
                }
            }
            // edp acumulados
            $edps_value_acc = array();
            foreach (array_values($edps_value) as $i => $edp_acc) {
                if($i > 0 && $edp_acc > 0){
                    $edps_value_acc[] =  $edps_value_acc[$i-1] + $edp_acc;
                }
                else{
                    $edps_value_acc[] = $edp_acc;
                }
            }
        ?>
        <div class="panel panel-default">
            <div class="panel-body">
                    <div class="col-sm-6"><h4><strong>Estados de Pago</strong></h4></div>
                    <div class="col-sm-6 text-right"><?= $this->Html->link('Ver Estados de Pago',['controller'=>'payment_statements','action'=>'index', '?'=>['building_id' =>$budget->building_id]],['class'=>'btn btn-xs btn-material-orange-900']) ?></div>
                    <div class="col-md-6 col-sm-6">
                        <dl class="dl-horizontal">
                            <dt>Cantidad Edp a la Fecha:</dt>
                            <dd><?= count($budget['payment_statements']); ?></dd>
                            <dt>Total Neto:</dt>
                            <dd><?= moneda($edps_total_acc['neto']); ?></dd>
                            <dt>Total IVA:</dt>
                            <dd><?= moneda($edps_total_acc['iva']); ?></dd>
                            <dt>Total EDP:</dt>
                            <dd><?= moneda($edps_total_acc['total']); ?></dd>
                            <dt>Total Costo Directo:</dt>
                            <dd><?= moneda($edps_total_acc['direct_cost']); ?></dd>
                            <?php // Utilidad  acumulada Total y Gastos Generales acumulados Total, se saca del PPTO ?>
                        </dl>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <dl class="dl-horizontal text-normal">
                            <?php foreach ($edp_states_list as $key => $edp_sl): ?>
                                <dt><?= $edp_sl['desc']; ?>:</dt>
                                <dd><?= $edp_sl['qty']; ?></dd>
                            <?php endforeach ?>
                        </dl>
                    </div>
                <?php // Listado de EDP con LINK ?>
                <div class="col-sm-12" style="margin-top:20px;">
                    <table class="table table-striped">
                        <tr>
                            <th>EDP</th>
                            <th>Total [CLP]</th>
                            <th>Presente EDP [UF]</th>
                            <th>Detalle</th>
                        </tr>
                        <?php foreach ($budget['payment_statements'] as $key => $edp): ?>
                        <tr>
                            <td><?= 'EDP '.($key+1); ?></td>
                            <td><?= moneda($edp['total']); ?></td>
                            <td><?= round($edp['total_cost'],2); ?></td>
                            <td><?= $this->Html->link('Ver',['controller'=>'payment_statements','action'=>'view',$edp['id']],['class'=>'btn btn-xs btn-material-orange-900']) ?></td>
                        </tr>
                        <?php endforeach ?>
                    </table>
                </div>
                <?php
                $char_epds_1 = new Highchart();
                $char_epds_1->chart->renderTo = "edp_1";
                $char_epds_1->title->text = 'EDP Total contra Ppto. Proyectado';
                $char_epds_1->credits->enabled = false;
               // Meses en X
                $char_epds_1->xAxis->categories = array_values($edp_dates);
                // hover
                $char_epds_1->tooltip->formatter = new HighchartJsExpr("function() {
                        return '' + this.y;
                    }"
                );
                // Uf en Y
                $char_epds_1->yAxis->title->text = "Total UF";
                //  Serie valor total EDP
                $char_epds_1->series[] = array(
                    'type' => "column",
                    'name' => "EDP Real",
                    'color' => "#69D830",
                    'title' => 'UF',
                    'data' => array_values($edps_value)
                );
                // avance proyectado
                $char_epds_1->series[] = array(
                    'name' => "EDP Presupuesto Proyectado",
                    'type' => "column",
                    'color' => "#2196F3",
                    'data' => array_values($budget_progress_compare_months_info['proyected_progress_budget'])
                );
                // Avance proyectado acumulado
                $char_epds_2 = new Highchart();
                $char_epds_2->chart->renderTo = "edp_2";
                $char_epds_2->title->text = 'EDP Acumulados - Real contra Proyectado';
                $char_epds_2->credits->enabled = false;
               // Meses en X
                $char_epds_2->xAxis->categories = array_values($edp_dates);
                // hover
                $char_epds_2->tooltip->formatter = new HighchartJsExpr("function() {
                        return '' + this.y;
                    }"
                );
                // Uf en Y
                $char_epds_2->yAxis->title->text = "Total UF";
                //  Serie EDP Acumulado
                $char_epds_2->series[] = array(
                    'type' => "column",
                    'name' => "EDP Real Acumulado",
                    'color' => "#69D830",
                    'title' => 'UF',
                    'data' => array_values($edps_value_acc)
                );
                $char_epds_2->series[] = array(
                    'name' => "EDP Proyectado Acumulado",
                    'type' => "column",
                    'data' => array_values($budget_progress_info['proyected_progress_budget'])
                );
             ?>
                <div id="edp_1"></div>
                <br>
                <div id="edp_2"></div>
            </div>
        </div>
    <!-- Iconstruye -->
    <div class="panel panel-default">
        <div class="panel-body">
                <div class="col-sm-6"><h4><strong>Gastos de Materiales - Salida de Bodega</strong></h4></div>
                <div class="col-sm-6 text-right">
                    <?= $this->Html->link('Ver gastos de Materiales',['controller'=>'iconstruye_imports','action'=>'index'],['class'=>'btn btn-xs btn-material-orange-900']); ?>
                </div>

            <div class="col-sm-12">
                <dl class="dl-horizontal">
                    <dt>Cantidad Registros:</dt>
                    <dd><?= $iconstruye_stats['total_imported_items']; ?></dd>
                    <dt>Total suma items:</dt>
                    <dd><?= moneda($iconstruye_stats['sum_product_total']); ?></dd>
                </dl>
            </div>

            <br>
            <div id="ic_gastos"></div>

            <?php
                $char_ic = new Highchart();
                $char_ic->chart->renderTo = "ic_gastos";
                $char_ic->title->text = 'Gastos de Materiales - Salida de Bodega Mensual';
                $char_ic->credits->enabled = false;
                // Meses en X
                $char_ic->xAxis->categories = array_keys($iconstruye_stats['iconstruye_data']);
                // hover
                $char_ic->tooltip->formatter = new HighchartJsExpr("function() {
                        return '' + this.y;
                    }"
                );
                // Uf en Y
                $char_ic->yAxis->title->text = "Total CLP";
                //  Serie valor total EDP
                $char_ic->series[] = array(
                    'type' => "column",
                    'name' => "Costo materiales salida",
                    'color' => "#69D830",
                    'title' => 'CLP',
                    'data' => array_values($iconstruye_stats['iconstruye_data'])
                );
             ?>
        </div>
    </div>







        <?= $this->Html->link(__('Volver'), ['action' => 'index'], ['class' => 'btn btn-flat btn-link']) ?>
    </div>
</div>
<script>
    <?= '$(document).ready(function() {' .
        $chart_progress_budget->render('chart') .
        $chart_progress_month_compare->render('chart') .
        $chart_schedules_progress->render('chart') .
        $char_epds_1->render('chart') .
        $char_epds_2->render('chart') .
        $char_ic->render('chart') . '});' ?>
</script>
