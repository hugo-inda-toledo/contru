<?php
namespace App\Model\Table;

use App\Model\Entity\PaymentStatement;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PaymentStatements Model
 */
class PaymentStatementsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('payment_statements');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Budgets', [
            'foreignKey' => 'budget_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_created_id'
        ]);
        $this->belongsTo('UserModifieds', [
            'className' => 'Users',
            'foreignKey' => 'user_modified_id'
        ]);
        $this->belongsTo('PaymentStatementStates', [
            'className' => 'PaymentStatementStates',
            'foreignKey' => 'payment_statement_state_id'
        ]);
        $this->hasMany('PaymentStatementApprovals', [
            'className' => 'PaymentStatementApprovals',
            'foreignKey' => 'payment_statement_id'
        ]);
        $this->hasMany('BudgetItemsPaymentStatements', [
            'className' => 'BudgetItemsPaymentStatements',
            'foreignKey' => 'payment_statement_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');

        // $validator
        //     ->requirePresence('presentation_date', 'create')
        //     ->notEmpty('presentation_date');

        // $validator
        //     ->requirePresence('billing_date', 'create')
        //     ->notEmpty('billing_date');

        // $validator
        //     ->requirePresence('estimation_pay_date', 'create')
        //     ->notEmpty('estimation_pay_date');


        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        // $rules->add($rules->existsIn(['budget_id'], 'Budgets'));
        // $rules->add($rules->existsIn(['user_created_id'], 'Users'));
        // $rules->add($rules->existsIn(['user_modified_id'], 'UserModifieds'));
        return $rules;
    }

    public function getLastPaymentStatementByBudgetItem($payment_statement ='')
    {
        # code...
    }

    /**
     *  Datos compartidos para Excel, PDF y View del Estado de Pago
     * @param  array $budget            Es el presupuesto asociado a un EDP
     * @param  object $paymentStatement  Es el Estado de Pago
     * @return array  $datos             Devuelve un array con datos
     */
    public function sharedData($budget, $paymentStatement){

        $datos = [];

        $datos['id'] = $paymentStatement->id;

        // Estado de pago anterior - junto a sus progress
        $ultimo_edp = $this->find()
                ->where(['PaymentStatements.budget_id' => $budget['id'], 'PaymentStatements.id <' => $paymentStatement->id])
                ->order(['PaymentStatements.created' => 'DESC'])
                /*->contain([
                        'Progress' => function($q) use ($datos){
                            return $q
                                ->select(['id', 'overall_progress_percent'])
                                ->order(['Progress.overall_progress_percent' => 'DESC']);
                            }
                    ])*/
                ->first();

        if(empty($ultimo_edp)){
            $ultimo_edp['total_direct_cost'] = 0;
            $ultimo_edp['overall_progress'] = 0;
            $ultimo_edp['progress_present_payment_statement'] = 0;
        }


        $datos['moneda']['nombre']= $budget['currencies_values'][0]['currency']['name'];
        $datos['moneda']['valor'] = $budget['currencies_values'][0]['value'];

        // $costo_directo = round($budget['total_cost']/$datos['moneda']['valor'],2);
        // $utilidad =  round( ($costo_directo +  $budget['general_costs']) * $budget['utilities']/100,2);
        // $total_contrato = $costo_directo + $budget['general_costs'] + $utilidad;
        $costo_directo = $budget['total_cost'];
        $utilidad = ($budget->total_cost + $budget->general_costs) * ($budget->utilities / 100);
        $total_contrato = $paymentStatement->contract_value;

        $datos['contrato']['total'] = $total_contrato;
        $datos['contrato']['anticipo'] = $budget['advances']/100 * $total_contrato;

        // Avance Presente EDP. %edp - $edp_anterior
        $datos['edp']['percent'] = (!empty($ultimo_edp['overall_progress']))? $paymentStatement->overall_progress - $ultimo_edp['overall_progress'] : $paymentStatement->overall_progress;

        $datos['costo_directo']['total_moneda'] = round($budget['total_cost']/$datos['moneda']['valor'],2);
        $datos['costo_directo']['a_la_fecha'] = round($ultimo_edp['total_direct_cost'] + $paymentStatement->total_direct_cost,2);

        $datos['gastos_generales']['total_moneda'] = $budget['general_costs'];
        $datos['gastos_generales']['a_la_fecha'] = round($datos['gastos_generales']['total_moneda'] * $paymentStatement->overall_progress/100,2);
        $datos['gastos_generales']['edp'] = round($datos['gastos_generales']['total_moneda'] * $datos['edp']['percent']/100,2);

        $datos['utilidad']['total_moneda'] = $utilidad;
        $datos['utilidad']['a_la_fecha'] = round($utilidad * $paymentStatement->overall_progress/100,2);
        $datos['utilidad']['edp'] = round($datos['utilidad']['total_moneda'] * $datos['edp']['percent']/100,2);

        $datos['gastos_generales']['anterior'] = round($datos['gastos_generales']['total_moneda'] * $ultimo_edp['overall_progress']/100,2);
        $datos['utilidad']['anterior'] = round($datos['utilidad']['total_moneda'] * $ultimo_edp['overall_progress']/100,2);

        $datos['ultimo_edp'] = $ultimo_edp;

        return $datos;

    }


    public function itemsEdpAdd($budget_id){


       // Items del Presupuesto
        $bi = $this->Budgets->BudgetItems->find('all',['conditions' => ['budget_id' => $budget_id, 'parent_id IS' => null]]);
        $budget_items = array();
        foreach ($bi as $value) {
            $children = $this->Budgets->BudgetItems
            ->find('children', ['for' => $value->id])
            ->find('threaded')
            ->contain([
                    'Units',
                    'Progress' => function ($q) {
                        // Avance Aprobados que no tengan asociados o no un Estado de Pago
                        // Ordenados por avance real descendente
                        return $q
                            //->contain(['PaymentStatements'])
                            // ->where(['Progress.approved' => true])
                            ->order(['Progress.overall_progress_percent' => 'DESC']);
                        }
            ])
            ->toArray();

            $budget_items[$value->id] = $value->toArray();
            $budget_items[$value->id]['children'] = $children;
        }

        return $budget_items;
    }



    /**
     * Obtiene los items de un ppto
     * @param  integer $budget_id   identificador de ppto.
     * @return array $budget_items  listado de items de un ppto.
     */
    public function itemsEdpView($paymentStatement){

       // array para items de un ppto
       $budget_items = array();

       $payment_statement_id = $paymentStatement->id;

       // todos los items de padres mayores
       $bi = $this->Budgets->BudgetItems->find('all',['conditions' => ['budget_id' => $paymentStatement->budget_id,'parent_id IS' => null]]);
       // por cada padre saco sus hijos
       foreach ($bi as $value) {
            $children = $this->Budgets->BudgetItems
            ->find('children', ['for' => $value->id])
            ->find('threaded')
            ->contain([
                    'Units',
                    'BudgetItemsPaymentStatements' => function ($q) use ($payment_statement_id) {
                        // Sacar EDP Actual y Anteriores.
                        return $q
                            ->contain(['PaymentStatements'])
                            ->where([
                                //'Progress.approved' => true,
                                'BudgetItemsPaymentStatements.payment_statement_id <=' => $payment_statement_id,
                                'BudgetItemsPaymentStatements.payment_statement_id IS NOT' => null
                            ])
                            ->order([
                                'BudgetItemsPaymentStatements.CREATED' => 'DESC',
                                'BudgetItemsPaymentStatements.payment_statement_id' => 'DESC'
                            ])
                            ->limit(1);
                        }
            ])
            ->toArray();
            $budget_items[$value->id] = $value->toArray();
            $budget_items[$value->id]['children'] = $children;
        }

        return $budget_items;

    }


    public function fecha_a_palabras($x) {
           $year = substr($x, -4);
           $mon = substr($x, 3, 2);
           switch($mon) {
              case "01":
                 $month = "Enero";
                 break;
              case "02":
                 $month = "Febrero";
                 break;
              case "03":
                 $month = "Marzo";
                 break;
              case "04":
                 $month = "Abril";
                 break;
              case "05":
                 $month = "Mayo";
                 break;
              case "06":
                 $month = "Junio";
                 break;
              case "07":
                 $month = "Julio";
                 break;
              case "08":
                 $month = "Agosto";
                 break;
              case "09":
                 $month = "Septiembre";
                 break;
              case "10":
                 $month = "Octubre";
                 break;
              case "11":
                 $month = "Noviembre";
                 break;
              case "12":
                 $month = "Diciembre";
                 break;
           }
           $day = substr($x, 0, 2);
           return $day." de ".$month." de ".$year;
    }


    public function generateItems(&$rows,$budget_items,$paymentStatement){

        /*echo '<pre>';
        print_r($budget_items);
        echo '</pre>';*/

        foreach ($budget_items as $key => $bi) {
            // has childs
            if(!empty($bi['children'])){
                // Big Boss
                if(is_null($bi['parent_id'])){
                     $rows[] = [
                                (string) $bi['item'],
                                $bi['description'],
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                'big_boss'
                    ];
                    $this->generateItems($rows,$bi['children'],$paymentStatement);
                }
                else{
                    // Only a Father
                     $rows[] = [
                                (string) $bi['item'],
                                $bi['description'],
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                'parent'

                    ];

                    $this->generateItems($rows,$bi['children'],$paymentStatement);
                }
            }
            else{
                //not childs
                if($bi['disabled']){
                    $rows[] = [
                                (string) $bi['item'],
                                $bi['description'],
                                $bi['unit']['name'],
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                'disabled'
                    ];
                }
                else{

                    // Tiene progress?
                    $item_on = false;
                    if(isset($bi['budget_items_payment_statements'])){
                        $bi['progress'] = $bi['budget_items_payment_statements'];
                        // var_dump($bi[0]['overall_progress_percent']);
                    }
                    if(isset($bi['progress']) && ! empty($bi['progress'])) {
                            if($bi['progress'][0]['payment_statement_id'] == $paymentStatement->id){
                              $item_on = true;
                              $completado = $bi['progress'][0]['overall_progress'];
                              // Edp Anterior?
                              if(isset($bi['progress'][1])){
                                  $edp_anterior = $bi['progress'][1]['overall_progress'];
                              }
                              else{
                                  $edp_anterior = 0;
                              }
                            }
                            else{
                                // Primer progress no pertenece a EDP Actual.
                                // Revisar si existe otro Progress es del EDP Anterior.
                                // Si es así, sumo cero
                                // Si no seteo cero pq no existe progress asociados.
                                if(isset($bi['progress'][1])){
                                  $completado = $bi['progress'][1]['overall_progress'];
                                  $edp_anterior = $bi['progress'][1]['overall_progress'];
                                }
                                else{
                                  // no existe progress asociado a ningin EDP
                                  // no se avanzo nada.
                                  $completado = 0;
                                  $edp_anterior = 0;
                                }

                            }

                             // Si progress pertenece a EDP
                            /*if($bi['progress'][0]['payment_statement_id'] == $paymentStatement->id){
                              $item_on = true;
                              $completado = $bi['progress'][0]['overall_progress_percent'];
                              // Edp Anterior?
                              if(isset($bi['progress'][1])){
                                  $edp_anterior = $bi['progress'][1]['overall_progress_percent'];
                              }
                              else{
                                  $edp_anterior = 0;
                              }
                            }
                            else{
                                // Primer progress no pertenece a EDP Actual.
                                // Revisar si existe otro Progress es del EDP Anterior.
                                // Si es así, sumo cero
                                // Si no seteo cero pq no existe progress asociados.
                                if(isset($bi['progress'][1])){
                                  $completado = $bi['progress'][1]['overall_progress_percent'];
                                  $edp_anterior = $bi['progress'][1]['overall_progress_percent'];
                                }
                                else{
                                  // no existe progress asociado a ningin EDP
                                  // no se avanzo nada.
                                  $completado = 0;
                                  $edp_anterior = 0;
                                }

                            }*/
                    }
                    else {
                          // No tiene progress entonces es cero el avance.
                          $completado = 0;
                          $edp_anterior = 0;
                    }

                   $edp_presente = $completado - $edp_anterior;
                   $moneda = $paymentStatement['contract_value'];
                   $monto_item_total_moneda = round($bi['total_price']/$moneda,2);
                   $monto_a_cobrar_edp_moneda = round($monto_item_total_moneda * $edp_presente/100,2);
                   $unit_name = (isset($bi['unit']))?$bi['unit']['name']:'';

                   /*$rows[] = [
                                (string) $bi['item'],
                                $bi['description'],
                                $unit_name,
                                $monto_item_total_moneda,
                                $completado/100,
                                round($completado/100 * $monto_item_total_moneda,2),
                                $edp_anterior/100,
                                round($edp_anterior/100 * $monto_item_total_moneda,2),
                                $edp_presente/100,
                                round($monto_a_cobrar_edp_moneda,2),
                                'child'
                    ];*/
                    if(isset($bi['budget_items_payment_statements']) && $bi['budget_items_payment_statements'] != null){
                      $rows[] = [
                                  (string) $bi['item'],
                                  $bi['description'],
                                  $unit_name,
                                  $bi['total_price'],
                                  round($bi['budget_items_payment_statements'][0]['progress'], 2),
                                  round($bi['budget_items_payment_statements'][0]['progress_value'], 2),
                                  round($bi['budget_items_payment_statements'][0]['previous_progress'], 2),
                                  round($bi['budget_items_payment_statements'][0]['previous_progress_value'], 2),
                                  round($bi['budget_items_payment_statements'][0]['overall_progress'], 2),
                                  round($bi['budget_items_payment_statements'][0]['overall_progress_value'], 2),
                                  'child'
                      ];
                    }
                    else
                    {
                      $rows[] = [
                                  (string) $bi['item'],
                                  $bi['description'],
                                  $unit_name,
                                  $bi['total_price'],
                                  round(0, 2),
                                  round(0, 2),
                                  round(0, 2),
                                  round(0, 2),
                                  round(0, 2),
                                  round(0, 2),
                                  'child'
                      ];
                    }
                }
            }
        }

        return $rows;
    }

    public function generateExcel($datos,$payments,$paymentStatement,$budget,$row_items){

        /*****
            Se Genera Archivo xlsx con valores Fijos.
            En caso que se requiera valores con formula calculada
            se deberian cambiar lineas de código xD

        *********/

        /*echo 'datos<pre>';
        print_r($datos);
        echo '</pre>';

        echo 'payments<pre>';
        print_r($payments);
        echo '</pre>';

        echo 'paymentStatement<pre>';
        print_r($paymentStatement);
        echo '</pre>';

        echo 'budget<pre>';
        print_r($budget);
        echo '</pre>';

        echo 'row_items<pre>';
        print_r($row_items);
        echo '</pre>';*/

        //die();

        $excel = new \PHPExcel();

        // Propiedades Hoja
        $excel->getProperties()
             ->setCreator("LDZ Constructora")
             ->setLastModifiedBy("LDZ Constructora")
             ->setTitle("Estado de Pago")
             ->setSubject("Estado de Pago")
             ->setDescription("LDZ Constructora EDP");

        // Hoja Activa
        $excel->setActiveSheetIndex(0);

        // Hoja activa en variable $resumen
        $excel->getActiveSheet()->setTitle('RESUMEN');
        $resumen = $excel->getActiveSheet();

        // Logo, no funciona en localhost
        $image_path = WWW_ROOT . 'img' . DS . 'logo.png';
        $objDrawing = new \PHPExcel_Worksheet_HeaderFooterDrawing();
        $objDrawing->setName('Logo');
        $objDrawing->setPath($image_path);
        $objDrawing->setHeight(50);
        $excel->getActiveSheet()->getHeaderFooter()->addImage($objDrawing, \PHPExcel_Worksheet_HeaderFooter::IMAGE_HEADER_LEFT);


        // Tipo de Papel para Hoja
        $resumen->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT)
                                ->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);

        // Esconder grilla
        $resumen->setShowGridlines(false);

        //Escalar Hoja y centrar
        $resumen->getPageSetup()//->setFitToPage(1)
                                ->setFitToWidth(1)
                                ->setFitToHeight(1)
                                ->setHorizontalCentered(true);
        // Margenes hoja
        $resumen->getPageMargins()->setBottom(0.7450980392)
                                  ->setTop(1.3333333333)
                                  ->setLeft(0.3137254902)
                                  ->setRight(0.3137254902);

        // Ancho columnas
        $resumen->getColumnDimension('A')->setWidth(2.57);
        $resumen->getColumnDimension('B')->setWidth(15.71);
        $resumen->getColumnDimension('C')->setWidth(19);
        $resumen->getColumnDimension('D')->setWidth(9.43);
        $resumen->getColumnDimension('E')->setWidth(15.14);
        $resumen->getColumnDimension('G')->setWidth(20.57);
        $resumen->getColumnDimension('H')->setWidth(10.43);
        $resumen->getColumnDimension('I')->setWidth(12);
        $resumen->getColumnDimension('J')->setWidth(15.71);
        $resumen->getColumnDimension('K')->setWidth(13.29);


        // Formato para miles y decimales
         \PHPExcel_Shared_String::setDecimalSeparator(',');
         \PHPExcel_Shared_String::setThousandsSeparator('.');
         $number_format = '#,##0.00';


         // Numero EDP
         $num = count($payments);

        // Titulos de Hoja
        $resumen->setCellValue('B2', 'ESTADO DE PAGO ['.$paymentStatement->gloss.']')
                ->setCellValue('B3', $datos['obra']['nombre'])
                ->setCellValue('B4', 'LDZ CONSTRUCTORA');


        // Formato para titulos de hoja
        $headerStyle = array(
            'font'  => array(
                'size'  => 14
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $excel->getActiveSheet()->getStyle('B2:B4')->applyFromArray($headerStyle);
        $excel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
        $excel->getActiveSheet()->mergeCells('B2:K2')
                                ->mergeCells('B3:K3')
                                ->mergeCells('B4:K4');


        // formato para texto despues de titulos
        $buildStyle = array(
            'font'  => array(
                'size'  => 9,
                'italic' => true
            )
        );
        $resumen->getStyle('B7:B8')->applyFromArray($buildStyle);
        $resumen->setCellValue('B7','Obra')
                ->setCellValue('C7',$datos['obra']['nombre'])
                ->setCellValue('B8','Ubicación')
                ->setCellValue('C8',$datos['obra']['direccion'])
                ->setCellValue('I7','Fecha')
                ->setCellValue('I8',$paymentStatement['created']->format('d/m/Y'));

        // tamaño textos
        $resumen->getStyle('C7:C8')->getFont()->setSize(10);
        $resumen->getStyle('I7:I8')->getFont()->setSize(9);

        // formato para encabezado de tabla
        $headerTableStyle = array(
            'font'  => array(
                'size'  => 9
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'rotation' => 90,
                'startcolor' => array(
                    'argb' => 'FBFBFBF',
                )
            ),
            'borders' => array(
                'allborders' => array(
                'style' => \PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => '000000'),
                ),
            ),
        );

        /** Tabla Avances **/

        // ITEM
        $resumen->setCellValue('B13','ITEM');
        $resumen->mergeCells('B13:B14');
        $resumen->getStyle('B13:B14')->applyFromArray($headerTableStyle);
        // designacion
        $resumen->setCellValue('C13','DESIGNACIÓN');
        $resumen->mergeCells('C13:D14');
        $resumen->getStyle('C13:D14')->applyFromArray($headerTableStyle);
        // total contrato
        $resumen->setCellValue('E13','TOTAL CONTRATO');
        $resumen->mergeCells('E13:E14');
        $resumen->getStyle('E13:E14')->applyFromArray($headerTableStyle);
        //Avance a la Fecha
        $resumen->setCellValue('F13','AVANCE A LA FECHA');
        $resumen->mergeCells('F13:G13');
        $resumen->getStyle('F13:G13')->applyFromArray($headerTableStyle);
        $resumen->setCellValue('F14','%');
        $resumen->setCellValue('G14','Monto '.$datos['moneda']['nombre']);
        $resumen->getStyle('F14:G14')->applyFromArray($headerTableStyle);
        //Avance E.P. Anterior
        $resumen->setCellValue('H13','AVANCE A LA FECHA');
        $resumen->mergeCells('H13:I13');
        $resumen->getStyle('H13:I13')->applyFromArray($headerTableStyle);
        $resumen->setCellValue('H14','%');
        $resumen->setCellValue('I14','Monto '.$datos['moneda']['nombre']);
        $resumen->getStyle('H14:K14')->applyFromArray($headerTableStyle);
        //Avance Presente EP
        $resumen->setCellValue('J13','AVANCE A LA FECHA');
        $resumen->mergeCells('J13:K13');
        $resumen->getStyle('J13:K13')->applyFromArray($headerTableStyle);
        $resumen->setCellValue('J14','%');
        $resumen->setCellValue('K14','Monto '.$datos['moneda']['nombre']);
        $resumen->getStyle('J14:K14')->applyFromArray($headerTableStyle);


        /**  TABLA EDPS **/

        $tableStyle = array(
            'font'  => array(
                'size'  => 9
            ),
            'borders' => array(
                'allborders' => array(
                'style' => \PHPExcel_Style_Border::BORDER_HAIR,
                'color' => array('argb' => '000000'),
                )
            )
        );

        $indice = 15;
        foreach ($payments as $key => $pay) {
            // Datos Anteriores
            $per_ant = 0;
            $uf_ant = "";
            $per_edp = $pay['overall_progress'];
            if(isset($payments[$key - 1])){
                $per_ant = $payments[$key - 1]['overall_progress'];
                $uf_ant = $payments[$key - 1]['progress_present_payment_statement'];
                $per_edp = $pay['overall_progress'] - $payments[$key - 1]['overall_progress'];
            }
            $indice += $key;
            // Set table
            $resumen->mergeCells('C'.$indice.':D'.$indice);
            $resumen->setCellValue('B' . $indice, 'EDP ['.$pay['gloss'].']');
            $resumen->setCellValue('C' . $indice, 'E. Pago ['.$pay['gloss'].'], según detalle adjunto');
            $resumen->setCellValue('E' . $indice, moneda($datos['contrato']['total']));
            $resumen->setCellValue('F' . $indice, moneda($pay['total_percent_to_date']).'%');
            $resumen->setCellValue('G' . $indice, moneda($pay['progress_present_payment_statement']));
            $resumen->setCellValue('H' . $indice, moneda($pay['total_percent_last']).'%');
            $resumen->setCellValue('I' . $indice, moneda($pay['paid_to_date']));
            $resumen->setCellValue('J' . $indice, moneda($pay['total_percent_present']).' %');
            $resumen->setCellValue('K' . $indice, moneda($pay['total_cost']));
        }
        //Set style to table
        $resumen->getStyle('B15:K'.$indice)->applyFromArray($tableStyle);
        // format
        $resumen->getStyle('E15'.':E'.$indice)->getNumberFormat()->setFormatCode($number_format);
        $resumen->getStyle('K15'.':K'.$indice)->getNumberFormat()->setFormatCode($number_format);


        /** Tabla Resumen 1 LEFT **/

        // Salto 2 lineas
        $left = $indice + 3;

        // data
        $resumen->setCellValue('B' . $left, 'Valor trabajos efectuados a la fecha');
        $resumen->setCellValue('E' . $left, $paymentStatement->progress_present_payment_statement);
        $resumen->mergeCells('B'.$left.':D'.$left);

        $resumen->setCellValue('B' . ($left + 1), 'Valor trabajos estado anterior');
        $resumen->setCellValue('E' . ($left + 1),  $paymentStatement->paid_to_date);
        $resumen->mergeCells('B'.($left + 1).':D'.($left + 1));

        $resumen->setCellValue('B' . ($left + 2), 'Valor presente Estado de Pago');
        $resumen->setCellValue('E' . ($left + 2), $paymentStatement->total_cost);
        $resumen->mergeCells('B'.($left + 2).':D'.($left + 2));

        $resumen->setCellValue('B' . ($left + 3), 'Descuento devolución de Anticipo '.$budget['advances'].'%');
        $resumen->setCellValue('E' . ($left + 3), $paymentStatement->discount_advances);
        $resumen->mergeCells('B'.($left + 3).':D'.($left + 3));

        $resumen->setCellValue('B' . ($left + 4), 'Descuento por retenciones '.$budget['retentions'].'%');
        $resumen->setCellValue('E' . ($left + 4), $paymentStatement->discount_retentions);
        $resumen->mergeCells('B'.($left + 4).':D'.($left + 4));

        //style
        $resumen->getStyle('B'.$left.':E'.($left + 4))->applyFromArray($tableStyle);
        // Format Number
        $resumen->getStyle('E'.$left.':E'.($left + 4))->getNumberFormat()->setFormatCode($number_format);


        /** Tabla Resumen 2 RIGHT **/

        $right = $indice + 3;

        // contrato
        $resumen->setCellValue('G' . $right, 'Valor del Contrato');
        $resumen->setCellValue('H' . $right,  $datos['moneda']['nombre']);
        $resumen->setCellValue('K' . $right,  $datos['contrato']['total']);
        //anticipo
        $resumen->setCellValue('G' . ($right + 1), 'Antcipo '.$budget['advances'].'%');
        $resumen->setCellValue('H' . ($right + 1),  $datos['moneda']['nombre']);
        $resumen->setCellValue('K' . ($right + 1),  $datos['contrato']['anticipo']);
        //pagado a la fecha
        $resumen->setCellValue('G' . ($right + 2), 'Pagado a la Fecha '.$budget['advances'].'%');
        $resumen->setCellValue('H' . ($right + 2),  $datos['moneda']['nombre']);
        $resumen->setCellValue('K' . ($right + 2),  $paymentStatement->paid_to_date);

        $resumen->setCellValue('G' . ($right + 3), 'Avance Pte. EP');
        $resumen->setCellValue('H' . ($right + 3),  $datos['moneda']['nombre']);
        $resumen->setCellValue('K' . ($right + 3),  $paymentStatement->total_cost);

        $resumen->setCellValue('G' . ($right + 4),  'Saldo por Pagar');
        $resumen->setCellValue('H' . ($right + 4),  $datos['moneda']['nombre']);
        $resumen->setCellValue('K' . ($right + 4),  $paymentStatement->balance_due);
        // Style
        $resumen->getStyle('G'.$right.':K'.($right + 4))->applyFromArray($tableStyle);
        // Format Number
        $resumen->getStyle('K'.$right.':K'.($right + 4))->getNumberFormat()->setFormatCode($number_format);


        /** Valor NETO EDP **/
        $i = $right + 7;
        $resumen->setCellValue('B' . $i, 'Valor Neto E.Pago');
        $resumen->setCellValue('E' . $i, $paymentStatement->liquid_pay);
        // Format
        $resumen->mergeCells('B'. $i.':D'.$i);
        $resumen->getStyle('B'.$i.':E'.$i)->applyFromArray($tableStyle);
        $resumen->getStyle('E'.$i)->getNumberFormat()->setFormatCode($number_format);


        /*** Totales **/
        // UF
        $resumen->setCellValue('B'.($i+2),'Valor '.$datos['moneda']['nombre'].' al día Estado de Pago' .$paymentStatement->created->format('d-m-Y'));
        $resumen->setCellValue('E'.($i+2),$paymentStatement->currency_value_to_date);
        $resumen->mergeCells('B'.($i+2).':D'.($i+2));
        // T. Neto
        $resumen->setCellValue('B'.($i+3),'TOTAL NETO $');
        $resumen->setCellValue('E'.($i+3),$paymentStatement->total_net);
        $resumen->mergeCells('B'.($i+3).':D'.($i+3));
        // IVA
        $resumen->setCellValue('B'.($i+4),'IVA');
        $resumen->setCellValue('E'.($i+4),$paymentStatement->tax);
        $resumen->mergeCells('B'.($i+4).':D'.($i+4));
        // TOTAL
        $resumen->setCellValue('B'.($i+5),'TOTAL');
        $resumen->setCellValue('E'.($i+5),$paymentStatement->total);
        $resumen->mergeCells('B'.($i+5).':D'.($i+5));
        // Format
        $resumen->getStyle('B'.($i+2).':E'.($i+5))->applyFromArray($tableStyle);
        $resumen->getStyle('E'.($i+2).':E'.($i+5))->getNumberFormat()->setFormatCode($number_format);


        /** Administrador, Visitador, Mandante **/
        //Admin
        $resumen->setCellValue('B'.($i+10),$datos['obra']['admin_obra']);
        $resumen->setCellValue('B'.($i+11),'Administrador');
        $resumen->setCellValue('B'.($i+12),'LDZ Constructora');
        $resumen->mergeCells('B'.($i+10).':C'.($i+10));
        $resumen->mergeCells('B'.($i+11).':C'.($i+11));
        $resumen->mergeCells('B'.($i+12).':C'.($i+12));
        //Visitador
        $resumen->setCellValue('F'.($i+10),$datos['obra']['visitador']);
        $resumen->setCellValue('F'.($i+11),'Administrador');
        $resumen->setCellValue('F'.($i+12),'LDZ Constructora');
        $resumen->mergeCells('F'.($i+10).':G'.($i+10));
        $resumen->mergeCells('F'.($i+11).':G'.($i+11));
        $resumen->mergeCells('F'.($i+12).':G'.($i+12));
        //Cliente
        $resumen->setCellValue('I'.($i+10),$datos['obra']['cliente']);
        $resumen->setCellValue('I'.($i+11),'Mandante');
        $resumen->mergeCells('I'.($i+10).':J'.($i+10));
        $resumen->mergeCells('I'.($i+11).':J'.($i+11));
        //Format
        $peopleStyle = array(
            'font'  => array(
                'size'  => 10,
                'bold'  => true,
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $peopleStyle2 = array(
            'font'  => array(
                'size'  => 10,
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $resumen->getStyle('B'.($i+10).':J'.($i+10))->applyFromArray($peopleStyle);
        $resumen->getStyle('B'.($i+11).':J'.($i+12))->applyFromArray($peopleStyle2);


        // ITEMS

        // nueva hoja
        $excel->createSheet();
        $detalles = $excel->getSheet(1);
        $detalles->setTitle('Detalle');

        // Tipo de Papel para Hoja
        $detalles->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
                                ->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);

        // Esconder grilla
        $detalles->setShowGridlines(false);

        //Escalar Hoja y centrar
        $detalles->getPageSetup()->setFitToWidth(1)
                                 ->setFitToHeight(0)
                                 ->setHorizontalCentered(true);

        // Margenes hoja
        $detalles->getPageMargins()->setBottom(0.7450980392)
                                  ->setTop(1.3333333333)
                                  ->setLeft(0.1176470588)
                                  ->setRight(0.1176470588)
                                  ->setHeader(0.32)
                                  ->setFooter(0.32);

        // Ancho columnas
        $detalles->getColumnDimension('A')->setWidth(2.29);
        $detalles->getColumnDimension('B')->setWidth(10.14);
        $detalles->getColumnDimension('C')->setWidth(82);

        // Logo, no funciona en localhost
        // objDrawing arriba definido en hoja resumen
        $detalles->getHeaderFooter()->addImage($objDrawing, \PHPExcel_Worksheet_HeaderFooter::IMAGE_HEADER_LEFT);

        // Footer num de paginas en footer
        $detalles->getHeaderFooter()->setOddFooter('&RPágina &P de &N');

        // TITULOS
        $detalles->setCellValue('B2', 'ESTADO DE PAGO ['.$num.']')
                 ->setCellValue('B3', $datos['obra']['nombre'])
                 ->setCellValue('B4', 'LDZ CONSTRUCTORA');


        // fecha en palabras
        $detalles->setCellValue('B6','Santiago, ' .$this->fecha_a_palabras((string)$paymentStatement['created']->format('d/m/Y')));
        $detalles->getStyle('B6')->getFont()->setSize(9);


        // FORMATO CELDAS TITULOS DE TABLA
        $headerStyle = array(
            'font'  => array(
                'size'  => 14
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $detalles->getStyle('B2:B4')->applyFromArray($headerStyle);
        $detalles->getStyle('B2')->getFont()->setBold(true);
        $detalles->mergeCells('B2:K2')
                 ->mergeCells('B3:K3')
                 ->mergeCells('B4:K4');


        // Titulos simples
        $detalles->setCellValue('B7','Código');
        $detalles->setCellValue('C7','RESUMEN');
        $detalles->setCellValue('D7','UNIDAD');
        $detalles->setCellValue('E7','TOTAL');
        // Titulos agrupados
        $detalles->setCellValue('F6','Avance a la Fecha');
        $detalles->setCellValue('F7','%');
        $detalles->setCellValue('G7','Monto $');
        $detalles->setCellValue('H6','Avance anterior');
        $detalles->setCellValue('H7','%');
        $detalles->setCellValue('I7','Monto $');
        $detalles->setCellValue('J6','Avance presente EP');
        $detalles->setCellValue('J7','%');
        $detalles->setCellValue('K7','Monto $');
        $detalles->mergeCells('F6:G6');
        $detalles->mergeCells('H6:I6');
        $detalles->mergeCells('J6:K6');
        // aplico estilo
        $detalles->getStyle('B7:E7')->applyFromArray($headerTableStyle);
        $detalles->getStyle('F6:K7')->applyFromArray($headerTableStyle);

        // Generate Array con Items
        if(!empty($row_items)){

                $limite_items = 7 + count($row_items);

                // formatos para los porcentajes
                /*$percent_format = array('code' => \PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $detalles->getStyle('F8:F'.$limite_items)->getNumberFormat()->applyFromArray($percent_format);
                $detalles->getStyle('H8:H'.$limite_items)->getNumberFormat()->applyFromArray($percent_format);
                $detalles->getStyle('J8:J'.$limite_items)->getNumberFormat()->applyFromArray($percent_format);*/

                // estilo para Titulo mayor item
                $big_boss_style = array(
                    'fill' => array(
                        'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                        'startcolor' => array(
                            'argb' => '0BFBFBF',
                        )
                    )
                );
                $parent_style = array(
                    'fill' => array(
                        'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                        'startcolor' => array(
                            'argb' => '0D8E4BC',
                        )
                    )
                );

                $disabled_style = array(
                    'font' => array(
                        'color' => array('rgb' => 'BFBFBF'),
                    )
                );

                $value_style = array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    )
                );

                // Seteo valores en tabla de items
                $column = 8;
                foreach ($row_items as $i => $row) {
                    //valores por columna
                    
                    if($row[10]=='child'){

                        $detalles->setCellValue('C'.$column,$row[1])
                                 ->setCellValue('D'.$column,$row[2])
                                 ->setCellValue('E'.$column,$row[3])
                                 ->setCellValue('F'.$column,$row[4].'%')
                                 ->setCellValue('G'.$column,$row[5])
                                 ->setCellValue('H'.$column,$row[6].'%')
                                 ->setCellValue('I'.$column,$row[7])
                                 ->setCellValue('J'.$column,$row[8].'%')
                                 ->setCellValue('K'.$column,$row[9]);

                    }
                    else
                    {
                      $detalles->setCellValue('C'.$column,$row[1])
                               ->setCellValue('D'.$column,$row[2])
                               ->setCellValue('E'.$column,$row[3])
                               ->setCellValue('F'.$column,$row[4])
                               ->setCellValue('G'.$column,$row[5])
                               ->setCellValue('H'.$column,$row[6])
                               ->setCellValue('I'.$column,$row[7])
                               ->setCellValue('J'.$column,$row[8])
                               ->setCellValue('K'.$column,$row[9]);
                    }

                    // estilo fila
                    if($row[10]=='big_boss'){
                        //big boss
                        $detalles->getStyle('B'.$column.':K'.$column)->applyFromArray($big_boss_style);

                    }
                    elseif($row[10]=='parent'){
                        $detalles->getStyle('B'.$column.':K'.$column)->applyFromArray($parent_style);
                    }
                    elseif($row[10]=='child'){
                        $detalles->getStyle('E'.$column.':K'.$column)->applyFromArray($value_style);
                    }
                    else{
                        if($row[10]=='disabled'){
                           $detalles->getStyle('B'.$column.':K'.$column)->applyFromArray($disabled_style);
                        }
                        else{
                            // 2 decimales en MONTO EP
                           $detalles->getStyle('K'.$column)->getNumberFormat()->setFormatCode('0.00');
                           // 2 decimales en TOTAL
                           $detalles->getStyle('E'.$column)->getNumberFormat()->setFormatCode('0.00');
                        }
                    }

                    //FIX . por , en ITEMS. Ej reemplaza 1,2 por 1.2
                    if (preg_match('/^\d+.\d+$/', $row[0])) {
                        $detalles->setCellValueExplicit('B'.$column, $row[0],\PHPExcel_Cell_DataType::TYPE_STRING);
                    }
                    else{
                        $detalles->setCellValue('B'.$column,$row[0]);
                    }

                    $column++;
                }
        }
        else{
            $limite_items = 10;
        }


       // RESUMEN DE MONTOS FINAL DE TABLA
       $final = $limite_items + 2;
       $detalles->setCellValue('D'.$final,'Total Costo Directo');
       $detalles->setCellValue('D'.($final + 2),'Gastos Generales');
       $detalles->setCellValue('D'.($final + 3),'Utilidades');
       $detalles->setCellValue('D'.($final + 5),'Total Neto');
       //  Alineo a la Derecha
       $detalles->getStyle('D'.$final.':D'.($final+5))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

       //  ******* TOTAL COSTO DIRECTO ******** /
       $detalles->setCellValue('E'.$final,$datos['costo_directo']['total_moneda'])
                ->setCellValue('F'.$final,$paymentStatement['overall_progress'])
                ->setCellValue('G'.$final,$datos['costo_directo']['a_la_fecha']);
       // edp anterior datos
       $detalles->setCellValue('H'.$final,$datos['ultimo_edp']['overall_progress'])
                ->setCellValue('I'.$final,round($datos['ultimo_edp']['total_direct_cost'] * $datos['ultimo_edp']['overall_progress']/100,2));
        $udp = $datos['ultimo_edp']['total_direct_cost'] * $datos['ultimo_edp']['overall_progress']/100;
       // edp actual datos
        $detalles->setCellValue('J'.$final,$datos['edp']['percent'])
                 ->setCellValue('K'.$final,$paymentStatement['total_direct_cost']);

        // ******** GASTOS GENERALES *********** /
        $detalles->setCellValue('E'.($final+2),$datos['gastos_generales']['total_moneda'])
                 ->setCellValue('F'.($final+2),$paymentStatement['overall_progress'])
                 ->setCellValue('G'.($final+2),$datos['gastos_generales']['a_la_fecha']);
        // edp anterior datos
        $detalles->setCellValue('H'.($final+2),$datos['ultimo_edp']['overall_progress'])
                 ->setCellValue('I'.($final+2),round($datos['gastos_generales']['total_moneda'] * $datos['ultimo_edp']['overall_progress']/100,2));
        $udp += $datos['gastos_generales']['total_moneda'] * $datos['ultimo_edp']['overall_progress']/100;
        // edp actual datos
        $detalles->setCellValue('J'.($final+2),$datos['edp']['percent'])
                 ->setCellValue('K'.($final+2),$datos['gastos_generales']['edp']);

        // ********** AVANCE PRESENTE EDP *********/
        $detalles->setCellValue('E'.($final+3),$datos['utilidad']['total_moneda'])
                 ->setCellValue('F'.($final+3),$paymentStatement['overall_progress'])
                 ->setCellValue('G'.($final+3),$datos['utilidad']['a_la_fecha']);
        // edp anterior datos
        $detalles->setCellValue('H'.($final+3),$datos['ultimo_edp']['overall_progress'])
                 ->setCellValue('I'.($final+3),round($datos['utilidad']['total_moneda'] * $datos['ultimo_edp']['overall_progress']/100,2));
        $udp += $datos['utilidad']['total_moneda'] * $datos['ultimo_edp']['overall_progress']/100;
        // edp actual datos
        $detalles->setCellValue('J'.($final+3),$datos['edp']['percent'])
                 ->setCellValue('K'.($final+3),$datos['utilidad']['edp']);


        $edp_total = $paymentStatement['total_direct_cost'] + $datos['gastos_generales']['edp'] + $datos['utilidad']['edp'];
        $suma_a_la_fecha = $datos['costo_directo']['a_la_fecha'] + $datos['gastos_generales']['a_la_fecha'] + $datos['utilidad']['a_la_fecha'];

        // ********** SUMA ***************
        $detalles->setCellValue('E'.($final+5), $datos['contrato']['total'])
                 ->setCellValue('G'.($final+5), $suma_a_la_fecha)
                 ->setCellValue('I'.($final+5), round($udp,2))
                 ->setCellValue('K'.($final+5), $edp_total);


        /******* Arreglos generales de estilo **********/

        // Alineo Izquierda numero items (codigo)
        $detalles->getStyle('B8:B'.$limite_items)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        // Ajuste automatico de Texto en descripcion del item (resumen)
        $detalles->getStyle('C8:C'.$limite_items)->getAlignment()->setWrapText(true);

        // Font size
        $detalles->getStyle('B8:K'.$limite_items)->getFont()->setSize(9);
        $detalles->getStyle('F6:K6')->applyFromArray($headerTableStyle);

        // borde para cada celda de la tabla
        $tableItemsStyle = array(
            'borders' => array(
                'allborders' => array(
                'style' => \PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => '000000'),
                )
            )
        );
        $detalles->getStyle('B8:K'.$limite_items)->applyFromArray($tableItemsStyle);
        $detalles->getStyle('F'.$final.':K'.$final)->applyFromArray($tableItemsStyle);
        $detalles->getStyle('F'.($final+2).':K'.($final+2))->applyFromArray($tableItemsStyle);
        $detalles->getStyle('F'.($final+3).':K'.($final+3))->applyFromArray($tableItemsStyle);
        $detalles->getStyle('F'.($final+5).':K'.($final+5))->applyFromArray($tableItemsStyle);
        $detalles->getStyle('D'.($final+5).':K'.($final+5))->getFont()->setBold(true);

        // set size para totales
        $detalles->getStyle('D'.$final.':K'.($final+5))->getFont()->setSize(9);

        // Formato Miles para Dinero
        $detalles->getStyle('E8:E'.($final+5))->getNumberFormat()->setFormatCode($number_format);
        $detalles->getStyle('G8:G'.($final+5))->getNumberFormat()->setFormatCode($number_format);
        $detalles->getStyle('I8:I'.($final+5))->getNumberFormat()->setFormatCode($number_format);
        $detalles->getStyle('K8:K'.($final+5))->getNumberFormat()->setFormatCode($number_format);


        // Hide grid
        $detalles->setShowGridlines(false);

        $resumen = $excel->getSheet(0);
        $resumen->setCellValue('E' . $left, $suma_a_la_fecha);
        $resumen->mergeCells('B'.$left.':D'.$left);

        // SET HORA 1 Cuando se abre archivo.
        $excel->setActiveSheetIndex(0);

        // SAVE EXCEL IN TMP FOLDER
        $valid = false;
        $edp_name = str_replace(' ','_',$datos['obra']['nombre']).'_EP_N'.$num.'.xlsx';
        $path = TMP . $edp_name;
        $objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->save($path);

        //exit;
        $reader = \PHPExcel_IOFactory::createReader('Excel2007');
        if ($reader->canRead($path)) {
            $valid = true;
        }

        return ['status'=>$valid,'path'=>$path,'fileName'=>$edp_name];

    }

    public function money($number,$decimal = 2){
        $num = $number;
        if(is_numeric($number)){
            $num = number_format($number,$decimal,',','.');
        }
        return $num;
    }

    public function percent($number){
        return number_format($number*100,2,',','').'%';
    }

    public function generatePdf($datos,$payments,$paymentStatement,$budget,$row_items){

        /*echo '<pre>';
        print_r($row_items);
        echo '</pre>';

        die();*/
        // Include lib pdf
        // Agregada a composer.
        // Está en vendor/tenickcom
        require_once(ROOT . DS . 'vendor' . DS  . 'tecnickcom' . DS . 'tcpdf' . DS . 'tcpdf.php');

        $pdf = new \TCPDF('L','mm','LETTER',true, 'UTF-8');

        // Numero EDP
         $num = count($payments);

        //remove header
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Pagina UNO
        $pdf->AddPage('P');

        //header
        $pdf->SetFont('dejavusans', '', 12);
        $pdf->Cell(0,5, 'ESTADO DE PAGO ['. $paymentStatement->gloss.']',0,1,'C');
        $pdf->SetFont('dejavusans', '', 11);
        $pdf->SetFontSize('12');
        $pdf->Cell(0,5, $datos['obra']['nombre'],0,1,'C');
        $pdf->Cell(0,5, 'LDZ CONSTRUCTORA',0,1,'C');


        //datos obra
        $pdf->Ln(12);
        $pdf->SetFont('dejavusans', 'I', 7);
        $pdf->Cell(30,5,'Obra',0,0,'L');
        $pdf->SetFont('dejavusans', '', 7);
        $pdf->MultiCell(200,5,$datos['obra']['nombre'],0,'L',false,1);

        // ubicacion
        $pdf->SetFont('dejavusans', 'I', 7);
        $pdf->Cell(30,5,'Ubicación',0,0,'L');

        //direccion
        $pdf->Cell(200,5,$datos['obra']['direccion'],0,0,'L');

        $x = $pdf->getX();
        $y = $pdf->getY();

        // fecha titulo
        //$pdf->Cell(80,5,"Fecha",0,1,'L');
        //$pdf->Cell(80,5,$paymentStatement['created']->format('d/m/Y'),0,0,'L');



        $pdf->SetFont('dejavusans', '', 7);


        // tabla resumen 1
        $pdf->SetFontSize(7);
        $pdf->Ln(10);

        $css = '<style type="text/css">
            .table{
                border-collapse: collapse;
            }
            .table th, .table td{
                border: 0.5px solid #333;
            }
            .table tr.none td{
              border: none;
            }
            .table tr.none td.border{
               border: 0.5px solid #333;
            }
            .table-center th{
                text-align:center;
                background-color: rgb(171,171,171);
            }
            .table-center-td{
                text-align:center;
            }
            .text-center{
                text-align: center;
            }
            .text-left{
                text-align: left;
            }
            .text-right{
                text-align: right;
            }
            tr.tachado td{
                text-decoration: line-through;
            }
            </style>';


        $tabla1 = '<table class="table table-center table-center-td" cellpadding="3px">
                    <tr>
                        <th rowspan="2">Item</th>
                        <th rowspan="2">Designación</th>
                        <th rowspan="2">Total Contrato</th>
                        <th colspan="2">Avance a la Fecha</th>
                        <th colspan="2">Avance EP. Anterior</th>
                        <th colspan="2">Avance presente EP</th>
                    </tr>
                    <tr>
                        <th>%</th>
                        <th>Monto '.$datos['moneda']['nombre'].'</th>
                        <th>%</th>
                        <th>Monto '.$datos['moneda']['nombre'].'</th>
                        <th>%</th>
                        <th>Monto '.$datos['moneda']['nombre'].'</th>
                    </tr>
                    ';


        foreach ($payments as $key => $p) {

                $per_ant = 0;
                $moneda_ant = 0;
                $per_edp = $p['overall_progress'];
                if(isset($payments[$key - 1])){
                    $per_ant = $payments[$key - 1]['overall_progress'];
                    $moneda_ant = $payments[$key - 1]['progress_present_payment_statement'];
                    $per_edp = $p['overall_progress'] - $payments[$key - 1]['overall_progress'];
                }

                // total EDP
                $edp_moneda = $p['progress_present_payment_statement'];
                // avance a la fecha es avance anterior + avance actual o porcentaje
                $avance_a_la_fecha_moneda = $p['progress_present_payment_statement'] + $moneda_ant;

                $tabla1 .= '
                        <tr>
                            <td>EP ['.($p['gloss']).']</td>
                            <td>E. Pago ['.($p['gloss']).'], según detalle adjunto</td>
                            <td>'.$this->money($datos['contrato']['total']).'</td>
                            <td>'.$this->percent($p['overall_progress']/100).'</td>
                            <td>'.$this->money($avance_a_la_fecha_moneda).'</td>
                            <td>'.$this->percent($per_ant/100).'</td>
                            <td>'.$this->money($moneda_ant).'</td>
                            <td>'.$this->percent($per_edp/100).'</td>
                            <td>'.$this->money($edp_moneda).'</td>
                        </tr>';

        }

        $tabla1 .= '</table>';

        // tabla 2: trabajos y descuentos

        $tabla2 = '<table class="table" cellpadding="3px">
                    <tr>
                        <td style="width: 75%;">Valor trabajos efectuados a la fecha</td>
                        <td style="width: 25%;">'. $this->money($paymentStatement->paid_to_date + $paymentStatement->progress_present_payment_statement).'</td>
                    </tr>
                    <tr>
                        <td>Valor trabajos estado anterior</td>
                        <td>'.$this->money($paymentStatement->paid_to_date).'</td>
                    </tr>
                    <tr>
                        <td>Valor presente Estado de Pago</td>
                        <td>'.$this->money($paymentStatement->progress_present_payment_statement).'</td>
                    </tr>
                    <tr>
                        <td>Descuento devolución de Anticipo '.$budget['advances'].'%</td>
                        <td>'.$this->money($paymentStatement->discount_advances).'</td>
                    </tr>
                    <tr>
                        <td>Descuento por retenciones '.$budget['retentions'].'%</td>
                        <td>'.$this->money($paymentStatement->discount_retentions).'</td>
                    </tr>
                    ';

        $tabla2 .= '</table>';


        $tabla3 = '<table class="table" cellpadding="3px">
                     <tr>
                        <td style="width:50%">Valor del Contrato</td>
                        <td style="width:20%">'.$datos['moneda']['nombre'].'</td>
                        <td>'.$this->money($datos['contrato']['total']).'</td>
                     </tr>
                      <tr>
                        <td>Antcipo '.$budget['advances'].'%</td>
                        <td>'.$datos['moneda']['nombre'].'</td>
                        <td>'.$this->money($datos['contrato']['anticipo']).'</td>
                     </tr>
                     <tr>
                        <td>Pagado a la Fecha</td>
                        <td>'.$datos['moneda']['nombre'].'</td>
                        <td>'.$this->money($paymentStatement->paid_to_date).'</td>
                     </tr>
                     <tr>
                        <td>Avance Pte EP</td>
                        <td>'.$datos['moneda']['nombre'].'</td>
                        <td>'.$this->money($paymentStatement->progress_present_payment_statement).'</td>
                     </tr>
                     <tr>
                        <td>Saldo por Pagar</td>
                        <td>'.$datos['moneda']['nombre'].'</td>
                        <td>'.$this->money($paymentStatement->balance_due).'</td>
                     </tr>
                  </table>';


        // DOC
        //writeHTML   ($html,$ln = true,$fill = false,$reseth = false, $cell = false,$align = '')
        // cell($w,$h = 0,$txt = '', $border = 0,$ln = 0,$align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T',$valign = 'M' )
        //writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)


        $html = $css . $tabla1;
        $html2 = $css . $tabla2;
        $html3 = $css . $tabla3;

        $pdf->writeHTML($html,1,0,false,false,'L');
        $pdf->Ln(7);
        $pdf->writeHTMLCell(95,'','',$pdf->getY(),$html2,0,0,0,true,'L',true);
        $pdf->writeHTMLCell(80,'',$pdf->getX() + 10,'',$html3,0,1,0,true,'L',true);



        //VALOR NETO
        $neto = '<table class="table" cellpadding="3px">
                    <tr>
                        <td style="width:75%;">Valor Neto presente E.Pago En '.$datos['moneda']['nombre'].'</td>
                        <td style="width:25%">'.$this->money($paymentStatement->liquid_pay).'</td>
                    </tr>
                </table>';
        $pdf->Ln(7);
        $pdf->writeHTMLCell(95,'','',$pdf->getY(),$css.$neto,0,1,0,true,'L',true);


        // VALORES TOTALES
        $tabla_totales = '<table class="table" cellpadding="3px">
                            <tr>
                                <td style="width:75%;">Valor '.$datos['moneda']['nombre'] .' al día Estado de Pago '. $paymentStatement->created->format('d-M-Y').'</td>
                                <td style="width:25%">'.$this->money($paymentStatement->currency_value_to_date).'</td>
                            </tr>
                            <tr>
                                <td>TOTAL NETO $</td>
                                <td>'. $this->money($paymentStatement->total_net,0).'</td>
                            </tr>
                            <tr>
                                <td>IVA</td>
                                <td>'.$this->money($paymentStatement->tax,0).'</td>
                            </tr>
                            <tr>
                                <td>TOTAL</td>
                                <td>'.$this->money($paymentStatement->total,0).'</td>
                            </tr>
                        </table>';

        $pdf->Ln(7);
        $pdf->writeHTMLCell(95,'','',$pdf->getY(),$css.$tabla_totales,0,0,0,true,'L',true);



        //admin, visitador, mandante
        $pdf->Ln(40);
        $pdf->cell(66,5,$datos['obra']['admin_obra'],0,0,'C');
        $pdf->cell(66,5,$datos['obra']['visitador'],0,0,'C');
        $pdf->cell(66,5,$datos['obra']['cliente'],0,1,'C');

        $pdf->cell(66,5,'Administrador de Obras',0,0,'C');
        $pdf->cell(66,5,'Visitador de Obras',0,0,'C');
        $pdf->cell(66,5,'Mandante',0,1,'C');

        $pdf->cell(66,5,'LDZ Constructora',0,0,'C');
        $pdf->cell(66,5,'LDZ Constructora',0,0,'C');


        // LISTA DE ITEMS
        $pdf->AddPage('L');


        // titulo EDP

        // cell($w,$h = 0,$txt = '', $border = 0,$ln = 0,$align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T',$valign = 'M' )
        $pdf->SetFontSize(12);
        $pdf->Cell('',6,'ESTADO DE PAGO N° '.$num,0,1,'C');
        $pdf->Cell('',6,$datos['obra']['nombre'],0,1,'C');
        $pdf->Cell('',6,'LDZ CONSTRUCTORA',0,1,'C');


        $pdf->Ln(10);
        $pdf->SetFontSize(7);
        $pdf->Cell('',4,$this->fecha_a_palabras($paymentStatement->created->format('d-m-Y')),0,1,'L');

        // ITEMS
        $items = '<table class="table table-center" cellpadding="3px">
                    <tr>
                        <th rowspan="2">Código</th>
                        <th rowspan="2" width="46%">Resumen</th>
                        <th rowspan="2">Unidad</th>
                        <th rowspan="2">Total ['.$datos['moneda']['nombre'].']</th>
                        <th colspan="2">Avance a la Fecha</th>
                        <th colspan="2">Avance EP. Anterior</th>
                        <th colspan="2">Avance presente EP</th>
                    </tr>
                    <tr>
                        <th>%</th>
                        <th>Monto UF</th>
                        <th>%</th>
                        <th>Monto UF</th>
                        <th>%</th>
                        <th>Monto UF</th>
                    </tr>

                    ';

        // Generate Array con Items
        if(!empty($row_items)){
            foreach ($row_items as $bi){
                                    //(string) $bi['item'],
                                    // $bi['description'],
                                    // $bi['unit']['name'],
                                    // $monto_item_total_moneda,
                                    // $completado/100,
                                    // round($completado/100 * $monto_item_total_moneda,2),
                                    // $edp_anterior/100,
                                    // round($edp_anterior/100 * $monto_item_total_moneda,2),
                                    // $edp_presente/100,
                                    // round($monto_a_cobrar_edp_moneda,2),
                                    // 'child'

                    if($bi[10]=='big_boss'){
                          $items .= '<tr class="big_boss parent">
                                    <td>'.$bi[0].'</td>
                                    <td>'.$bi[1].'</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>';

                    }
                    elseif($bi[10]=='parent'){
                        $items .= '<tr class="parent">
                                    <td>'.$bi[0].'</td>
                                    <td>'.$bi[1].'</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>';
                    }
                    else{
                        if($bi[10]=='disabled'){
                               $items .= '<tr class="tachado">
                                    <td>'.$bi[0].'</td>
                                    <td>'.$bi[1].'</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>';
                        }
                        else{
                           $items .= '<tr class="items child">
                                    <td>'.$bi[0].'</td>
                                    <td>'.$bi[1].'</td>
                                    <td>'.$bi[2].'</td>
                                    <td class="text-right">'.moneda($bi[3]).'</td>
                                    <td class="text-right">'.moneda($bi[4]).'%</td>
                                    <td class="text-right">'.moneda($bi[5]).'</td>
                                    <td class="text-right">'.moneda($bi[6]).'%</td>
                                    <td class="text-right">'.moneda($bi[7]).'</td>
                                    <td class="text-right">'.moneda($bi[8]).'%</td>
                                    <td class="text-right">'.moneda($bi[9]).'</td>
                                </tr>';
                        }
                    }
            }
        }

        $items .= '<tr class="none">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>';

         // total costo directo
        $items .= '<tr class="none">
                        <td colspan="3" class="text-right">Total Costo Directo</td>
                        <td>'.$this->money($datos['costo_directo']['total_moneda']).'</td>
                        <td class="border">'.$this->percent($paymentStatement['overall_progress']/100).'</td>
                        <td class="border">'.$this->money($datos['costo_directo']['a_la_fecha']).'</td>
                        <td class="border">'.$this->percent($datos['ultimo_edp']['overall_progress']/100).'</td>
                        <td class="border">'.$this->money($datos['ultimo_edp']['total_direct_cost']).'</td>
                        <td class="border">'.$this->percent($datos['edp']['percent']/100).'</td>
                        <td class="border">'.$this->money($paymentStatement['total_direct_cost']).'</td>
                    </tr>';

        // gastos generales
        $items .= '<tr class="none">
                        <td colspan="3" class="text-right">Gastos Generales</td>
                        <td class="">'.$this->money($datos['gastos_generales']['total_moneda']).'</td>
                        <td class="border">'.$this->percent($paymentStatement['overall_progress']/100).'</td>
                        <td class="border">'.$this->money($datos['gastos_generales']['a_la_fecha']).'</td>
                        <td class="border">'.$this->percent($datos['ultimo_edp']['overall_progress']/100).'</td>
                        <td class="border">'.$this->money($datos['gastos_generales']['anterior']).'</td>
                        <td class="border">'.$this->percent($datos['edp']['percent']/100).'</td>
                        <td class="border">'.$this->money($datos['gastos_generales']['edp']).'</td>
                    </tr>';

        // utilidad
        $items .= '<tr class="none">
                        <td colspan="3" class="text-right">Utilidades</td>
                        <td>'.$this->money($datos['utilidad']['total_moneda']).'</td>
                        <td class="border">'.$this->percent($paymentStatement['overall_progress']/100).'</td>
                        <td class="border">'.$this->money($datos['utilidad']['a_la_fecha']).'</td>
                        <td class="border">'.$this->percent($datos['ultimo_edp']['overall_progress']/100).'</td>
                        <td class="border">'.$this->money($datos['utilidad']['anterior']).'</td>
                        <td class="border">'.$this->percent($datos['edp']['percent']/100).'</td>
                        <td class="border">'.$this->money($datos['utilidad']['edp']).'</td>
                    </tr>';

        $edp_total = $paymentStatement['total_direct_cost'] + $datos['gastos_generales']['edp'] + $datos['utilidad']['edp'];
        //$suma_a_la_fecha = $datos['costo_directo']['a_la_fecha'] + $datos['gastos_generales']['a_la_fecha'] + $datos['utilidad']['a_la_fecha'];
        $suma_a_la_fecha = $paymentStatement['paid_to_date'] + $paymentStatement['progress_present_payment_statement'];

        // neto
        $items .= '<tr class="none">
                        <td colspan="3" class="text-right">Total Neto</td>
                        <td>'.$this->money($datos['contrato']['total']).'</td>
                        <td class="border"></td>
                        <td class="border">'.$this->money($suma_a_la_fecha).'</td>
                        <td class="border"></td>
                        <td class="border">'.$this->money($datos['ultimo_edp']['progress_present_payment_statement']).'</td>
                        <td class="border"></td>
                        <td class="border">'.$this->money($paymentStatement['progress_present_payment_statement']).'</td>
                    </tr>';

        // cierro tabla y escribo en pdf los items con resumen
        $items .= '</table>';
        $pdf->writeHTMLCell(190,'','',$pdf->getY(),$css.$items,0,0,0,true,'L',true);


        // SAVE EXCEL IN TMP FOLDER
        $valid = false;
        $edp_name = str_replace(' ','_',$datos['obra']['nombre']).'_EP_N'.$num.'.pdf';
        $path = TMP . $edp_name;
        $pdf->Output($path, 'F');

        if(file_exists($path)){
            $valid = true;
        }

        return ['status'=>$valid,'path'=>$path,'fileName'=>$edp_name];

    }


}
