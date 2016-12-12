<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Utility\Hash;
use Cake\Core\Configure;

class ReportShell extends Shell
{

    public function initialize()
    {
        parent::initialize();
        $this->loadModel('SfBuildings');
        $this->loadModel('SfWorkers');
        // $this->loadModel('SfWorkersStatus');
        $this->loadModel('SfWorkerBuildings');
        $this->loadModel('RemunerationsReports');
        $this->loadModel('Workers');
        $this->loadModel('SfWorkerCargosPersonal');
        $this->loadModel('SfWorkerCargos');
    }

    public function main()
    {

        // $this->generateReportRemuneracion();
    }

    public function generateReportRemuneracion(){

        $reports = $this->RemunerationsReports->find('all', [
            'conditions' => [
                'status' => 0
            ]
        ]);
        if(!empty($reports)){
            $variables_reports = Configure::read('remuneraciones');
            foreach($reports AS $report){
                try{
                    $nameFile="reporte_remuneracion.xls";
                    $basePath = WWW_ROOT.'files'.DS.'reports'.DS.$report->id;
                    $fullPath = $basePath.DS.$nameFile;
                    if(!file_exists($basePath)){
                        mkdir($basePath);
                    }
                    // 2. -
                    $report->status=1;
                    $report->path=str_replace(WWW_ROOT, "", $fullPath);
                    $this->RemunerationsReports->save($report);
                    $codArn = $report->codArn;
                    $month = $report->month;
                    $year = ($report->year=="")?date('Y'):$report->year;
                    $day_cut = $report->day_cut;
                    $data = ['month' => $month, 'day_cut' => $day_cut];
                    $data['day_cut_prev'] = $report->day_cut_prev;
                    // Topes máximos, se hace por mes porque el valor se entrega según uf
                    $topes_maximos = $this->Workers->getTopesMaximosByMonth($month, $variables_reports['tope_maximo_imponible']);

                    // Sueldo mínimo
                    $sueldo_minimo = $this->Workers->getSueldoMinimo($month);
                    $variables_reports['gratificacion'] = round(($sueldo_minimo/12)*4.75);

                    // 3. -
                    $building_workers = $this->SfWorkerBuildings->find('list', [
                        'conditions' => ['SfWorkerBuildings.codArn' => $codArn],
                        'keyField' => 'ficha',
                        'valueField' => 'ficha'
                    ])->toArray();
                    // $building_workers['11751125'] = '11751125';
                    // $building_workers['8755144'] = '8755144';
                    // $building_workers['12258243'] = '12258243';
                    // $building_workers['17378840'] = '17378840';
                    if(!empty($building_workers)){
                        // 4. -
                        $sf_workers = $this->SfWorkers->find('all', [
                            'conditions' => [
                                'SfWorkers.ficha IN' => $building_workers
                            ],
                            'order' => [
                                'SfWorkers.appaterno' => 'ASC'
                            ]
                        ])->toArray();
                        $sf_workers_cargos_personal = $this->SfWorkerCargosPersonal->find('list')
                            ->where(['SfWorkerCargosPersonal.ficha IN' => $building_workers, 'SfWorkerCargosPersonal.vigHasta' => '99991201'])
                            ->cache(function ($q) {
                                $tmp = $q->where();
                                $tmp =array('sql' => $tmp->sql(), 'params' => $tmp->valueBinder()->bindings());
                                return $q->repository()->alias() . '-' . md5(serialize($tmp));
                            }, 'config_cache_sfworkers')
                            ->toArray();
                        $sf_workers_cargos = $this->SfWorkerCargos->find('list')
                            ->where(['SfWorkerCargos.CarCod IN' => $sf_workers_cargos_personal])
                            ->cache(function ($q) {
                                $tmp = $q->where();
                                $tmp =array('sql' => $tmp->sql(), 'params' => $tmp->valueBinder()->bindings());
                                return $q->repository()->alias() . '-' . md5(serialize($tmp));
                            }, 'config_cache_sfworkers')
                            ->toArray();
                        $softland_info = [];
                        $c=0;
                        $days_month=$variables_reports['dias_contables'];
                        $html = "<body>";
                        $html.='<style>.num {mso-number-format:"\#\,\#\#0\.00_ \;\[Red\]\-\#\,\#\#0\.00\ ";}.text{mso-number-format:"\@";}</style>';
                        $html.="<table>";
                        $html.=$this->generateHeader($month, $year, $data['day_cut_prev']);
                        $html.="<tbody>";

                        foreach($sf_workers AS $sf_w){
                            $bw = $sf_w->ficha;
                            $fechaIngreso = $sf_w->fechaIngreso->format('d-m-Y');
                            $sb=0;
                            $gratificacion=$variables_reports['gratificacion'];
                            $he=0;
                            $c++;
                            $ingresosExtra=['bonos'=>0,'tratos'=>0];
                            $assists=['asistencias'=>$variables_reports['dias_contables'],'faltas'=>0];

                            $html.="<tr>";
                            $data['worker']['ficha'] = $bw;
                            // Información del trabajador desde CPO
                            $worker = $this->Workers->find('all', [
                                'conditions' => [
                                    'Workers.softland_id' => $bw
                                ],
                                'recursive' => -1
                            ])->first();
                            $html.="<td style='text-align: center;'>".$c."</td>";
                            $html.="<td>".utf8_decode($sf_w->nombres)."</td>";
                            $html.="<td>".$sf_w->rut."</td>";
                            $html.="<td>".utf8_decode($sf_workers_cargos[$sf_workers_cargos_personal[$sf_w->ficha]])."</td>";
                            $html.="<td>".$sf_w->fechaIngreso->format('d-m-Y')."</td>";
                            /*Del formato enviado por PH*/
                            $html.="<td> </td>";
                            /*FIN Del formato enviado por PH*/

                            if(!empty($worker)){
                                $ingresosExtra = $this->Workers->getTotalBonusAndDeals($worker->id, $month);
                                $assists = $this->Workers->getAssistsApprovalByMonthAndWorkerId($month, $year, $worker->id, $day_cut, $fechaIngreso, $variables_reports['dias_contables']);
                            }
                            $infoPago = $this->Workers->getLastRentaInfoByWorkerId($bw, "P090", $month);
                            if(empty($infoPago)){
                                //Buscar si tiene seteado alguna variable
                                $infoPago = $this->Workers->getLastRentaInfoByWorkerId($bw, "H001", $month);
                            }
                            if(!empty($infoPago)){
                                if(isset($data['day_cut_prev']) && $data['day_cut_prev']!=""){
                                    $assists_prev=['asistencias'=>$variables_reports['dias_contables'],'faltas'=>0];
                                    if(!empty($worker)){
                                        $assists_prev = $this->Workers->getAssistsApprovalByMonthAndWorkerId($month-1, $year, $worker->id, $data['day_cut_prev'], $fechaIngreso, $variables_reports['dias_contables'], true);
                                    }
                                    $html .= $this->getBodyDaysToReport($month, $year, $assists_prev, $fechaIngreso, 31, $data['day_cut_prev']);
                                    $assists['asistencias'] -= $assists_prev['faltas'];
                                }
                                // aquí se obtiene la información pago del trabajador
                                $wp = (isset($infoPago[$month]))?$infoPago[$month]:end($infoPago);
                                // El sueldo base
                                $sb = $wp['H001']['valor'];
                                // El valor diario po
                                $valor_dia=$sb/$variables_reports['dias_contables'];
                                // El sueldo de mes
                                $sueldo_mes=($valor_dia * $assists['asistencias']);
                                // HORA EXTRA
                                $he=$assists['horas_extras'];
                                // El VALOR HORA EXTRA
                                $valor_he=round(($sb/$variables_reports['dias_contables'] *0.155555555555556*1.5));
                                // El total HORA EXTRA
                                $total_he=($he*$valor_he);
                                // HORA ATRASOS
                                $horas_atrasos=$assists['horas_atrasos'];
                                // El VALOR HORA ATRASOS
                                $valor_horas_atrasos=round(($sb/$variables_reports['dias_contables'] *0.155555555555556));
                                // El total HORA ATRASOS
                                $total_horas_atrasos=($horas_atrasos*$valor_horas_atrasos);
                                // Bonos asistencia (no cacho como calcularlo)
                                $bonos_asistencia = 0;
                                // Aguinaldo
                                $aguinaldo = '';
                                // ASIG FAMILIAR
                                $asig_familiar = '';
                                // ASIG FAMILIAR R
                                $asig_familiar_r = '';
                                // adivina?
                                $movilizacion=(isset($wp['P086']['valor']))?$wp['P086']['valor']:$variables_reports['movilizacion'];
                                // colación
                                $colacion=(isset($wp['P087']['valor']))?$wp['P087']['valor']:$variables_reports['colacion'];
                                // calculo en base a los días contables
                                $total_movilizacion=($movilizacion/$variables_reports['dias_contables'])*$assists['asistencias'];
                                // calculo en base a los días contables
                                $total_colacion=($colacion/$variables_reports['dias_contables'])*$assists['asistencias'];
                                // la suma de colación y movilización
                                $viatico = (isset($wp['H020']['valor']))?$wp['H020']['valor']:0;
                                $total_no_imponible=$total_colacion+$total_movilizacion+$viatico;
                                // sueldo base imponible sin gratificación
                                $sb_imponible_sin_gratificacion = round($sueldo_mes) + $total_he + $bonos_asistencia + $aguinaldo + $ingresosExtra['bonos']+$ingresosExtra['tratos'];
                                /** inicio calculo gratificación **/
                                if($sb_imponible_sin_gratificacion < ($gratificacion*4)){
                                    $gratificacion = round($sb_imponible_sin_gratificacion*0.25);
                                }
                                /** fin calculo gratificación **/
                                // SB imponible
                                $sb_imponible = $sb_imponible_sin_gratificacion + $gratificacion;

                                // Total de haberes
                                $total_haberes = $sb_imponible+$total_no_imponible;
                                // Descuento afp
                                $dcto_afp = $this->Workers->getDctoAfp($data['worker']['ficha']);
                                $dcto_afp['monto'] = round(($sb_imponible*$dcto_afp['porcentaje'])/100);
                                $dcto_afp['monto'] = ($topes_maximos['afp']<$dcto_afp['monto'])?$topes_maximos['afp']:$dcto_afp['monto'];
                                // Descuento fonasa (siempre se debe calcular el 7% que es el legal)
                                $dcto_fonasa['porcentaje'] = $variables_reports['porcentaje_salud'];
                                // Calculo Descuento fonasa
                                $dcto_fonasa['monto'] = round(($sb_imponible*$dcto_fonasa['porcentaje'])/100);
                                // Descuento isapre (si es que tiene)
                                $dcto_isapre = $this->Workers->getDctoSalud($data['worker']['ficha'], $month);
                                // Descuento salud, si el de fonasa es mayor entonces se deja el de fonoasa, sino se deja el de isapre po
                                $dcto_salud = ($dcto_fonasa['monto']>$dcto_isapre['isapre_monto'])?$dcto_fonasa['monto']:$dcto_isapre['isapre_monto'];
                                $dcto_salud = ($topes_maximos['salud']<$dcto_salud)?$topes_maximos['salud']:$dcto_salud;
                                // Descuento ccaf es la fila D030
                                $dcto_ccaf = (isset($wp['D030']['valor']))?$wp['D030']['valor']:0;
                                // Seguro de cesantía
                                $seguro_cesantia = round($sb_imponible*$variables_reports['seguro_cesantia']);
                                $seguro_cesantia = ($topes_maximos['afc']<$seguro_cesantia)?$topes_maximos['afc']:$seguro_cesantia;
                                $aguinaldo='';
                                $anticipo=isset($wp['D026'])?$wp['D026']['valor']:0;
                                $full_ahorro_caja=(isset($wp['D028']['valor']))?$wp['D028']['valor']:0;
                                $pptmo_empresa=(isset($wp['D031']['valor']))?$wp['D031']['valor']:0;
                                $seguro_adicional=(isset($wp['D002']['valor']))?$wp['D002']['valor']:0;
                                $ahorro_afp='';
                                $apv=(isset($wp['D015']['valor']))?$wp['D015']['valor']:0;
                                $otros_dctos='';
                                // Total de descuento
                                $total_descuento = $dcto_salud + $dcto_afp['monto'] + $dcto_ccaf + $seguro_cesantia + $anticipo + $full_ahorro_caja + $pptmo_empresa + $seguro_adicional + $apv;
                                // Sueldo líquido
                                $liquido = $total_haberes-$total_descuento;
                                $impuesto_unico = $sb_imponible-($dcto_salud + $dcto_afp['monto']);
                                // Calculo impuesto a la renta en base a tabla de softland (se aplica en base al imponible- descuentos según mi cotización mensual)
                                $impuesto_renta = $this->Workers->getImptoRentaByAmount($impuesto_unico);
                                $html.="<td></td>";
                                $html .= $this->getBodyDaysToReport($month, $year, $assists, $fechaIngreso, 31);
                                /*Inicio html para hacer solo un fwrite en el archivo en vez de muchos*/
                                // DT
                                $html.="<td>".$assists['asistencias']."</td>";
                                // SB
                                $html.="<td class='num'>".round($sb)."</td>";
                                // SM
                                $html.="<td class='num'>".round($sueldo_mes)."</td>";
                                // HE
                                $html.="<td class='num'>".round($he)."</td>";
                                // VALOR HE
                                $html.="<td class='num'>".round($valor_he)."</td>";
                                // TOTAL HE
                                $html.="<td class='num'>".round($total_he)."</td>";
                                // HA
                                $html.="<td></td>";
                                // VALOR HA
                                $html.="<td class='num'>".round($valor_horas_atrasos)."</td>";
                                // TOTAL HA
                                $html.="<td></td>";
                                // BONOS ASISTENCIA
                                $html.="<td class='num'>".round($bonos_asistencia)."</td>";
                                // BONOS
                                $html.="<td class='num'>".round($ingresosExtra['bonos'])."</td>";
                                // TATOS
                                $html.="<td class='num'>".round($ingresosExtra['tratos'])."</td>";
                                // GRAT
                                $html.="<td class='num'>".round($gratificacion)."</td>";
                                // AGUINALDO
                                $html.="<td></td>";
                                // THI
                                $html.="<td class='num'>".round($sb_imponible)."</td>";
                                // ASIG FAMILIAR
                                $html.="<td></td>";
                                // ASIG FAMILIAR R
                                $html.="<td></td>";
                                // ASIG MOV
                                $html.="<td class='num'>".round($movilizacion)."</td>";
                                // MOV
                                $html.="<td class='num'>".round($total_movilizacion)."</td>";
                                // ASIG COL
                                $html.="<td class='num'>".round($colacion)."</td>";
                                // COL
                                $html.="<td class='num'>".round($total_colacion)."</td>";
                                // VIATICO
                                $html.="<td class='num'>".round($viatico)."</td>";
                                // COL
                                $html.="<td></td>";
                                // COL
                                $html.="<td></td>";
                                // COL
                                $html.="<td></td>";
                                // COL
                                $html.="<td></td>";
                                // COL
                                $html.="<td></td>";
                                // TNO
                                $html.="<td class='num'>".round($total_no_imponible)."</td>";
                                // TH
                                $html.="<td class='num'>".round($total_haberes)."</td>";
                                // espacio xd
                                $html.="<td></td>";
                                // Nombre AFP
                                $html.="<td>".utf8_decode($dcto_afp['nombre'])."</td>";
                                // Porcentaje AFP
                                // $html.="<td>".moneda($dcto_afp['porcentaje_afp'])."</td>";
                                // Porcentaje adicional AFP
                                // $html.="<td>".moneda($dcto_afp['porcentaje_adicional'])."</td>";
                                // Porcentaje final AFP
                                $html.="<td class='num'>".round($dcto_afp['porcentaje'])."</td>";
                                // Tope máximo afp
                                // $html.="<td class='num'>".round($topes_maximos['afp)']."</td>";
                                // DCTO AFP
                                $html.="<td class='num'>".round($dcto_afp['monto'])."</td>";
                                // DCTO SALUD
                                $html.="<td class='num'>".round($dcto_fonasa['monto'])."</td>";
                                // DCTO SALUD
                                $html.="<td>".utf8_decode($dcto_isapre['isapre_nombre'])."</td>";
                                // DCTO ISAPRE
                                $html.="<td class='num'>".round($dcto_isapre['isapre_uf'])."</td>";
                                // DCTO ISAPRE
                                $html.="<td class='num'>".round($dcto_isapre['isapre_valor_uf'])."</td>";
                                // DCTO ISAPRE
                                $html.="<td class='num'>".round($dcto_isapre['isapre_monto'])."</td>";
                                // DCTO Seguro de Cesantía
                                $html.="<td class='num'>".round($seguro_cesantia)."</td>";
                                // IMPTO UNICO
                                $html.="<td class='num'>".round($impuesto_unico)."</td>";
                                // IMPTO
                                $html.="<td class='num'>".round($impuesto_renta)."</td>";
                                // AGUINALDO PAGADO
                                $html.="<td></td>";
                                // ANTICIPO
                                $html.="<td class='num'>".round($anticipo)."</td>";
                                // FULL AHORRO CAJA
                                $html.="<td class='num'>".round($full_ahorro_caja)."</td>";
                                // PPTMO CAJA
                                $html.="<td class='num'>".round($dcto_ccaf)."</td>";
                                // PPTMO EMPRESA
                                $html.="<td class='num'>".round($pptmo_empresa)."</td>";
                                // SEGURO ADICIONAL
                                $html.="<td class='num'>".round($seguro_adicional)."</td>";
                                // AHORRO AFP
                                $html.="<td></td>";
                                // APV
                                $html.="<td class='num'>".round($apv)."</td>";
                                // OTROS DCTOS
                                $html.="<td></td>";
                                // TOTAL
                                $html.="<td class='num'>".round($total_descuento)."</td>";
                                // LÍQ
                                $html.="<td class='num'>".round($liquido)."</td>";
                            }
                            $html.="</tr>";
                            // Actualización del progreso de reporte
                            $report->progress=round(($c*100)/count($building_workers));
                            // pr('Progreso: '.$report->progress.'%');
                            // Guardar xd
                            $this->RemunerationsReports->save($report);
                        }
                        $html.="</tbody></table></body>";
                    }

                    // se escribe la super variable $html al archivo "excel" (que es una tabla)
                    $fp = fopen($fullPath, 'w');
                    fwrite($fp, $html);
                    fclose($fp);
                    // Actualizar estado
                    $report->status=2;
                    // Guardar
                    $this->RemunerationsReports->save($report);
                }catch(\Exception $e){
                    // 08001 => error connection
                    // Se actualizan la cantidad de intentos
                    $report->tries=$report->tries+1;
                    // Se guarda el código de error
                    $report->code_errors.=($report->code_errors=="")?$e->getCode():", ".$e->getCode();
                    // Se actualizan el estado para que intente leerlo otra vez
                    $report->status=0;
                    // Guardar po
                    $this->RemunerationsReports->save($report);
                }
            }
        }
    }

    public function generateHeader($month, $year, $day_prev=31){
        $variables_reports = Configure::read('remuneraciones');

        $header_2_dias=[];
        $header_3_dias = [
            'N°', 'NOMBRE', 'RUT', 'CARGO', 'FECHA INGRESO', 'AREA',
        ];

        if($day_prev!==null){
            $days = $this->getHeaderDaysToReport($month, $year, 31, $day_prev);
            $header_2_dias = array_merge($header_2_dias, $days['numbers']);
            $header_3_dias = array_merge($header_3_dias, $days['initials']);
        }
        $header_2_dias[] = ' ';
        $header_3_dias[] = ' ';
        $days = $this->getHeaderDaysToReport($month, $year, 31);
        $header_2_dias = array_merge($header_2_dias, $days['numbers']);
        $header_3_dias = array_merge($header_3_dias, $days['initials']);


        // Imponibles
        $header_2_imponibles = ['', 'SUELDO', 'SUELDO', 'HRS.', 'VALOR', 'A PAGO POR', 'HRS.', 'VALOR', 'A DESCONTAR', 'BONOS', 'BONOS', '', '', '', 'TOTAL'];

        // No Imponibles
        $header_2_no_imponibles = ['ASIG.', 'ASIG.', 'ASIG.',  '', 'ASIG.',  '', '', '', '', '', '', '', 'TOTAL', 'TOTAL'];

        // DESCUENTOS / LIQUIDO
        $header_2_descuentos = ['AFP', '', '', '', 'ISAPRE', '', 'VALOR', '', 'SEGURO', 'IMP.', 'IMP. UNICO', 'AGUINALDO', 'ANTICIPO', 'FULL AHORRO', 'PPTMO', 'PPTMO', 'SEGURO', 'AHORRO', '', 'OTROS', 'TOTAL', 'LIQUIDO'
        ];

        // Imponibles
        $header_3_imponibles = ['DT', 'BASE', 'MES', 'EXTRAS', 'H. EXTRA', 'HRS. EXTRAS', 'ATRASO', 'H. ATRASO', 'H. ATRASO', 'ASISTENCIA', 'PRODUCCION', 'TRATOS', 'GRATIFICACION', 'AGUINALDO', 'IMPONIBLE'];

        // No Imponibles
        $header_3_no_imponibles = ['FAMILIAR', 'FAMILIAR R', 'MOVILIZACION',  'MOVILIZACION', 'COLACION',  'COLACION', 'VIATICOS', 'V', 'T', 'B', 'VM2', 'VM', 'NO IMPONIBLE', 'HABERES'];

        $header_3_descuentos = ['', '', 'AFP', 'SALUD', '', '', 'UF', 'ISAPRE', 'CESANTIA', 'UNICO', '', 'PAGADO', '', 'CAJA', 'CAJA', 'EMPRESA', 'ADICIONAL', 'AFP', 'APV', 'DESCUENTOS', ' DESCUENTOS', 'A PAGO'
        ];


        $return ="<thead>";

        /*Del formato enviado por Pedro Hartard (PH)*/
        $return.="<tr><th colspan='6'>CALCULO FINAL</th></tr>"; // Fila 1
        $return.="<tr>"; // Fila 3

        $titulo_imponibles = (count($header_3_dias)+2);
        $colspan_imponibles = count($header_3_imponibles)-2;
        $colspan_no_imponibles = count($header_3_no_imponibles)-1;
        $colspan_descuentos = count($header_3_descuentos)-1;

        for($i=1;$i<=$titulo_imponibles;$i++){
            $return.="<th></th>";
        }
        $return.="<th colspan='$colspan_imponibles' style='background-color: #99CC00'>IMPONIBLES</th>";

        $return.="<th colspan='$colspan_no_imponibles' style='background-color: #33CCCC'>NO IMPONIBLES</th>";
        $return.="<th></th>";
        $return.="<th></th>";

        $return.="<th colspan='$colspan_descuentos' style='background-color: #FF6600'>DESCUENTOS</th>";


        $return.="</tr>"; // Fila 3
        $return.="<tr>"; // Fila 4
        /*FIN Del formato enviado por Pedro Hartard (PH)*/

        /*FILA 3*/

        $return.='<th colspan="6">ASISTENCIA MES '.mb_strtoupper(convertMonthToSpanish(date('F', strtotime($year.'-'.$month)))).' '.$year.'</th>';

        $espacio = [''];
        // se escribe días en fila 2
        foreach(array_merge($header_2_dias, $header_2_imponibles, $header_2_no_imponibles, $espacio, $header_2_descuentos) AS $h){
            $return.="<th>".utf8_decode($h)."</th>";
        }

        /*FIN FILA 2 / INICIO FILA 3*/
        $return.="</tr><tr>";

        foreach(array_merge($header_3_dias, $header_3_imponibles, $header_3_no_imponibles, $espacio, $header_3_descuentos) AS $h){
            $return.="<th>".utf8_decode($h)."</th>";
        }

        /*FIN FILA 3*/
        $return.="</tr>";

        $return.="</thead>";

        return $return;
    }

    function getHeaderDaysToReport($month, $year, $total_days=30, $with_prev=null) {
        $return = ['numbers'=>[], 'initials'=>[]];
        $startFor = 1;
        if(!is_null($with_prev)){
            $month = ($month == 1)?12:$month-1;
            // $year = ($month == 1)?$year:$month-1;
            $startFor = $with_prev;
        }
        for($i=$startFor;$i<=31;$i++){
            $day = $i;
            $fullDate = $year; // Validar en caso que sea de Diciembre a Enero >.<
            $fullDate .= ($month<10)?"-0".$month:'-'.$month;
            $fullDate .= ($i<10)?'-0'.$i:'-'.$i;
            $endDate = mb_strtoupper(mb_substr(convertMonthToSpanish(date('l', strtotime($fullDate))), 0, 2));
            $printDate=$endDate;
            // Si es finde se pinta rojo, también se debe hacer con los feriados
            if(in_array($endDate, ['SÁ', 'DO']) || esFeriado($fullDate)){
                if($endDate=="SÁ") $endDate="SA";
                // $rows_styles_2[] = count($header_2);
                // $rows_styles_3[] = count($header_3);
                $printDate="<span style='color:RED;'>".$endDate.'</span>';
                $day="<span style='color:RED;'>".$day.'</span>';
            }
            $return['numbers'][]=$day;
            $return['initials'][]= $printDate;
        }
        return $return;
    }

    function getBodyDaysToReport($month, $year, $assists, $fechaIngreso, $total_days=30, $with_prev=null) {
        $return = "";
        $startFor = 1;
        if(!is_null($with_prev)){
            $month = ($month == 1)?12:$month-1;
            $startFor = $with_prev;
        }
        for($i=$startFor;$i<=$total_days;$i++){
            $keyPrevDay = $year;
            $keyPrevDay .= ($month<10)?"0".$month:$month;
            if($i<10){
                $keyPrevDay.="0";
            }
            $keyPrevDay.=$i;
            $asiste="1";
            $extraStyle=" style='text-align:center;";
            if(!empty($assists['faltas_dias'])){
                if(isset($assists['faltas_dias'][$keyPrevDay])){
                    $extraStyle.="background-color: ".$assists['faltas_dias'][$keyPrevDay]['background_color'].";";
                    $asiste=$assists['faltas_dias'][$keyPrevDay]['initials'];
                }
            }
            if(is_null($with_prev)){
                if(date('n', strtotime($fechaIngreso)).'-'.$year == $month.'-'.$year && $i<date('d', strtotime($fechaIngreso))){
                    $asiste="X";
                }
            }else{
                // Validar que fecha de ingreso este en el mes anterior del reporte
                if(date('n', strtotime($fechaIngreso)).'-'.$year == $month.'-'.$year){
                    if($i<date('d', strtotime($fechaIngreso))){
                        $asiste="X";
                    }
                }elseif(date('n', strtotime($fechaIngreso)).'-'.$year == ($month+1).'-'.$year){
                    $asiste="X";
                }
            }
            // Validar si es sábado o domingo o feriado
            if(
                in_array(mb_strtoupper(mb_substr(convertMonthToSpanish(date('l', strtotime($keyPrevDay))), 0, 2)), ['SÁ', 'DO']) ||
                esFeriado($keyPrevDay)
            ){
                $extraStyle .="color: RED;";
            }
            // Si el mes es de 30 días la columna 31 debe mostrarse vacía
            if($i==31 && cal_days_in_month(CAL_GREGORIAN, $month, $year)<31){
                $asiste="";
            }
            $extraStyle .= "'";
            $return.="<td$extraStyle>$asiste</td>";

        }
        return $return;
    }

    public function fillAssistsData(){
        // $sfWorkers = TableRegistry::get('SfWorkers');
        // pr('Inicio: '.date('d-m-Y H:i:s'));
        $this->loadModel('Buildings');
        $this->loadModel('AssistsData');
        // obtener buildings
        $buildings = $this->Buildings->find('all',[
            'conditions' => [
                'Buildings.active' => 1,
                // 'Buildings.id' => 40
            ]
        ]);
        if(!empty($buildings)){
            $this->AssistsData->updateAll(['mark' => 0], []);
            foreach($buildings AS $building){
                // pr('Obteniendo data de '.$building->softland_id);
                // cucho: esto ya no es necesario
                // $sfWorkerBuildings = TableRegistry::get('SfWorkerBuildings');

                // cucho: esto ya no es necesario
                // $sfWorkerCargosPersonal = TableRegistry::get('SfWorkerCargosPersonal');

                // cucho: esto ya no es necesario
                // $sfWorkerCargos = TableRegistry::get('SfWorkerCargos');

                // cucho: se deja esto, busca el id de la building de softland
                // $building = $this->Assists->Budgets->Buildings->find('all', ['conditions' => ['Buildings.id' => $building_id]])->first();

                // cucho: se crea una sola consulta que traiga todo lo necesario,
                // no me salio la custom query de cake, pero esto funciona y rápido
                // la consulta trae los trabajadores vigentes de la obra y su cargo
                $consulta = "
                    SELECT *
                    FROM softland.sw_personal
                    INNER JOIN (
                        softland.sw_cargoper
                        INNER JOIN softland.cwtcarg
                        ON
                            softland.sw_cargoper.carCod = softland.cwtcarg.CarCod
                    )
                    ON
                        softland.sw_personal.ficha = softland.sw_cargoper.ficha
                    WHERE softland.sw_personal.ficha IN (
                        SELECT ficha
                        FROM softland.sw_areanegper
                        WHERE codArn = '$building->softland_id'
                        AND vigHasta = {ts '9999-12-01 00:00:00.000'}
                    )
                    AND ( softland.sw_personal.FecTermContrato >= GETDATE() OR softland.sw_personal.FecTermContrato IS NULL)
                    ORDER BY nombres;
                ";
                // con una sola consulta los tiempos bajan de 60 a 6 seg

                // cucho: se ejecuta la consulta
                $array_workers = $this->SfWorkers->connection()->execute($consulta)->fetchAll('assoc');
                // se mantiene la estructura original para no modificar la vista
                $workers_data = [];
                // pr('Trabajadores de '.$building->id.': '.count($array_workers));
                foreach ($array_workers as $key => $worker) {
                    // Validar que el trabajador exista, en caso de que exista se actualiza la data, sino se crea no mas po
                    $workerExists = $this->AssistsData->find('all',[
                        'conditions' => [
                            'AssistsData.building_id' => $building->id,
                            'AssistsData.softland_id' => $worker['ficha'],
                            'AssistsData.cargo_codigo' => $worker['CarCod'],
                        ]
                    ])->first();
                    $new_worker = (!empty($workerExists))?$workerExists:$this->AssistsData->newEntity();
                    // $new_worker = $this->AssistsData->newEntity();

                    /*if($worker['ficha'] == "16130830"){
                        pr($worker);
                    }*/
                    $new_worker->building_id = $building->id;
                    $new_worker->softland_id = $worker['ficha'];
                    $new_worker->nombres = $worker['nombres'];
                    $new_worker->appaterno = $worker['appaterno'];
                    $new_worker->apmaterno = $worker['apmaterno'];
                    $new_worker->rut = ltrim($worker['rut'], '0');
                    $new_worker->email = $worker['Email'];
                    $new_worker->direccion = $worker['direccion'];
                    $new_worker->telefono1 = $worker['telefono1'];
                    $new_worker->fecha_nacimiento = $worker['fechaNacimient'];
                    $new_worker->fecha_ingreso = $worker['fechaIngreso'];
                    $new_worker->cargo_codigo = $worker['CarCod'];
                    $new_worker->cargo_nombre = $worker['CarNom'];
                    $new_worker->vig_desde = $worker['vigDesde'];
                    $new_worker->vig_hasta = $worker['vigHasta'];
                    $new_worker->mark = 1;
                    // Guardar
                    $this->AssistsData->save($new_worker);
                }
            }
            // Todos los que estén con mark 0 se deben eliminar
            $this->AssistsData->deleteAll(['AssistsData.mark' => 0]);
        }
        // pr('Fin: '.date('d-m-Y H:i:s'));
    }

}