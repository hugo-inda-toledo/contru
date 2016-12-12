<?php
namespace App\Model\Table;

use App\Model\Entity\IconstruyeImport;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * IconstruyeImports Model
 */
class IconstruyeImportsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('iconstruye_imports');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        /*$this->belongsTo('Files', [
            'foreignKey' => 'file_id',
            'joinType' => 'INNER'
        ]);*/
        $this->belongsTo('UserUploaders', [
            'className' => 'Users',
            'foreignKey' => 'user_uploader_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('GuideEntries', [
            'foreignKey' => 'iconstruye_import_id'
        ]);
        $this->hasMany('GuideExits', [
            'foreignKey' => 'iconstruye_import_id'
        ]);
        $this->hasMany('Subcontracts', [
            'foreignKey' => 'iconstruye_import_id'
        ]);
        $this->hasMany('Invoices', [
            'foreignKey' => 'iconstruye_import_id'
        ]);
        $this->hasMany('PurchaseOrders', [
            'foreignKey' => 'iconstruye_import_id'
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

        $validator
            ->requirePresence('file_name', 'create')
            ->notEmpty('file_name');

        $validator
            ->add('transaction_lines', 'valid', ['rule' => 'numeric'])
            ->requirePresence('transaction_lines', 'create')
            ->notEmpty('transaction_lines');

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
        $rules->add($rules->existsIn(['file_id'], 'Files'));
        $rules->add($rules->existsIn(['user_uploader_id'], 'UserUploaders'));
        return $rules;
    }

    public function validateFileType($file_type = '')
    {
        $file_valid_type = false;
        $valid_excel = array('application/vnd.ms-excel',
                            'application/vnd.ms-excel.addin.macroEnabled.12',
                            'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
                            'application/vnd.ms-excel.sheet.macroEnabled.12',
                            'application/vnd.ms-excel.template.macroEnabled.12',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        if (in_array($file_type, $valid_excel)) {
            $file_valid_type = true;
        }
        return $file_valid_type;
    }

    public function validate_min_info($coll, $type) {
        $msg = 'Falta ';
        $hd = null;
        switch ($type) {
            case 'guide_exits':
                $hd = array(
                    'A' => 'Comprobante',
                    'B' => 'Fecha Sistema',
                    'C' => 'Fecha Movimiento',
                    'D' => 'Fecha Emisión Doc',
                    'E' => 'Tipo',
                    'F' => 'N Documento',
                    'G' => 'Código',
                    'H' => 'Descripción',
                    'I' => 'Glosa',
                    'J' => 'Unidad',
                    'K' => 'Salida',
                    'L' => 'Ingreso',
                    'M' => 'Stock',
                    'N' => 'Salida',
                    'O' => 'Ingreso',
                    'P' => 'Stock',
                    'Q' => 'PPP',
                    'R' => 'Cod. Destino',
                    'S' => 'Destino',
                    'T' => 'Cod. Partida',
                    'U' => 'Partida',
                    'V' => 'Cod.bodega',
                    'W' => 'Nombre bodega',
                    'X' => 'Cod. Centro de gestión',
                    'Y' => 'Nombre centro de gestión');
                break;
            case 'subcontracts':
                $hd = array(
                    'C' => 'Centro',
                    'D' => 'RUT',
                    'E' => 'Nombre',
                    'F' => 'Código',
                    'G' => 'Descripción',
                    'I' => 'Partida',
                    'J' => 'Unidad',
                    'K' => 'Moneda',
                    'L' => 'Tasa de Cambio',
                    'M' => 'Cantidad',
                    'P' => 'Precio',
                    'R' => 'Total',
                    'S' => 'Código',
                    'T' => 'Descripción',
                    'U' => 'Cantidad',
                    'V' => 'Total',
                    'W' => 'Saldo',
                    'X' => 'Monto EEPP',
                    'Y' => 'Fecha');
                break;
        }
        if ($type == 'subcontracts') {
            if (empty($coll['C'])) {
                ($msg != 'falta') ? ', ' : '';
                $msg .= $hd['C'];
            }
            if (empty($coll['D'])) {
                $msg .= ($msg != 'falta') ? ', ' : '';
                $msg .= $hd['D'];
            }
            if (empty($coll['F'])) {
                $msg .= ($msg != 'falta') ? ', ' : '';
                $msg .= $hd['F'];
            }
            if (empty($coll['I'])) {
                $msg .= ($msg != 'falta') ? ', ' : '';
                $msg .= $hd['I'];
            }
            if (empty($coll['K'])) {
                $msg .= ($msg != 'falta') ? ', ' : '';
                $msg .= $hd['K'];
            }
            if (empty($coll['L'])) {
                $msg .= ($msg != 'falta') ? ', ' : '';
                $msg .= $hd['L'];
            }
            if (empty($coll['M'])) {
                $msg .= ($msg != 'falta') ? ', ' : '';
                $msg .= $hd['M'];
            }
            if (empty($coll['P'])) {
                $msg .= ($msg != 'falta') ? ', ' : '';
                $msg .= $hd['P'];
            }
            if (empty($coll['R'])) {
                $msg .= ($msg != 'falta') ? ', ' : '';
                $msg .= $hd['R'];
            }
            if (empty($coll['S'])) {
                $msg .= ($msg != 'falta') ? ', ' : '';
                $msg .= $hd['S'];
            }
            if (empty($coll['U'])) {
                $msg .= ($msg != 'falta') ? ', ' : '';
                $msg .= $hd['U'];
            }
            if (empty($coll['V'])) {
                $msg .= ($msg != 'falta') ? ', ' : '';
                $msg .= $hd['V'];
            }
            if (empty($coll['W'])) {
                $msg .= ($msg != 'falta') ? ', ' : '';
                $msg .= $hd['W'];
            }
        } else {
            if (empty($coll['A'])) {
                ($msg != 'falta') ? ', ' : '';
                $msg .= $hd['A'];
            }
            if (empty($coll['B'])) {
                $msg .= ($msg != 'falta') ? ', ' : '';
                $msg .= $hd['B'];
            }
            if (empty($coll['H'])) {
                $msg .= ($msg != 'falta') ? ', ' : '';
                $msg .= $hd['H'];
            }
            if (!is_numeric($coll['N'])) {
                $msg .= ($msg != 'falta') ? ', ' : '';
                $msg .= $hd['N'];
            }
            if (!is_numeric($coll['Q'])) {
                $msg .= ($msg != 'falta') ? ', ' : '';
                $msg .= $hd['Q'];
            }
        }
        if (empty($coll['E'])) {
            $msg .= ($msg != 'falta') ? ', ' : '';
            $msg .= $hd['E'];
        }
        if (empty($coll['G'])) {
            $msg .= ($msg != 'falta') ? ', ' : '';
            $msg .= $hd['G'];
        }
        if (empty($coll['J'])) {
            $msg .= ($msg != 'falta') ? ', ' : '';
            $msg .= $hd['J'];
        }
        if (empty($coll['T'])) {
            $msg .= ($msg != 'falta') ? ', ' : '';
            $msg .= $hd['T'];
        }
        if (empty($coll['X'])) {
            $msg .= ($msg != 'falta') ? ', ' : '';
            $msg .= $hd['X'];
        }
        if (empty($coll['Y'])) {
            $msg .= ($msg != 'falta') ? ', ' : '';
            $msg .= $hd['Y'];
        }
        $msg .= ($msg != 'falta') ? '.' : '';
        return $msg;
    }
/**
 * Manda el excel a un arreglo php que tiene anidados:
 * - 1 Hoja de trabajo > presupuesto > item padre > sub items.
 * El algoritmo se detiene cuando:
 * - Formato de items invalido.
 * - El calculo de cantidad por precio unitario es invalido.
 * - Hay una celda vacía para algún valor en algún elemento
 * @param  string $ruta temporal del archivo excel
 * @param  boolean $file true si es la ruta del archivo, false si es el array directo.
 * @return array multidimensional con 3 arrays, 'errores' con una lista de errores encontrados en el excel, 'excel' el excel en un array plano, 'arbol' es el excel ordenado por jerarquia en forma de arbol.
 * @author Omar Sepúlveda <omar.sepulveda@ideauno.cl>
 */
    public function excel_guide_exits($ruta, $type)
    {
        $showDebug = false;
        $salida = array('registros' => array(), 'errores' => array());
        $headers = array(
            'A' => 'Comprobante',
            'B' => 'Fecha Sistema',
            'C' => 'Fecha Movimiento',
            'D' => 'Fecha Emisión Doc',
            'E' => 'Tipo',
            'F' => 'N Documento',
            'G' => 'Código',
            'H' => 'Descripción',
            'I' => 'Glosa',
            'J' => 'Unidad',
            'K' => 'Salida',
            'L' => 'Ingreso',
            'M' => 'Stock',
            'N' => 'Salida',
            'O' => 'Ingreso',
            'P' => 'Stock',
            'Q' => 'PPP',
            'R' => 'Cod. Destino',
            'S' => 'Destino',
            'T' => 'Cod. Partida',
            'U' => 'Partida',
            'V' => 'Cod.bodega',
            'W' => 'Nombre bodega',
            'X' => 'Cod. Centro de gestión',
            'Y' => 'Nombre centro de gestión');
        // cargamos el archivo
        $objPHPExcel = \PHPExcel_IOFactory::load($ruta);
        // se coloca en la variable $excel la primera hoja del excel, se usa solo la primera
        $excel = $objPHPExcel->getActiveSheet();
        //paso a un array plano todo el excel.
        $excelarray = $excel->toArray(null, true, true, true);
        $excelOriginal = $excelarray;
        $resumen = null;
        $tempNC = 0;
        foreach ($excelarray as $row=>&$coll) {
            $obra_id = null;
            if ($row == 1) {
                if ($coll == $headers) {
                    unset($excelarray[$row]);
                    $tempNC += 1;
                    continue;
                } else {
                    $salida['errores'][] = array('linea' => $row, 'error' => 'Las Columnas no corresponden al formato valido');
                    break;
                }
            } else {
                $coll['B'] = str_replace('/','-', $coll['B']);
                if($coll['E'] != 'Consumo') {
                    $tempNC += 1;
                    //unset($excelarray[$row]);
                    $salida['errores'][] = array('linea' => $row, 'error' => 'Registro no corresponde a consumo.');
                } else {
                    if (empty($coll['A']) || empty($coll['B']) || empty($coll['E']) || empty($coll['G']) || empty($coll['H']) || empty($coll['J']) ||
                     !is_numeric($coll['N']) || !is_numeric($coll['Q']) || empty($coll['T']) || empty($coll['X']) || empty($coll['Y'])) {
                        $err = $this->validate_min_info($coll, $type);
                        $salida['errores'][] = array('linea' => $row, 'error' => 'No se encuentra el minimo de informacion para ingresar el registro al sistema. (' . $err . ')');
                    }
                    if (!preg_match("/^\d+(\.\d+)*$/", $coll['T'], $output)) {
                        $salida['errores'][] = array('linea' => $row, 'error' => 'Registro con partida con el formato invalido');
                    }
                }
                 //linea valida
                //valido codigo de obra softland, el 'cod Centro de gestion' viene sin el 0 inicial por que excel lo toma como numerico, asi q le agrego un 0 al principio y lo valido
                //contra el resultado de un regex que saca el codigo de la columna siguiente, 'nombre centro de gestion'.
                if (preg_match("/^[\d]+\S/", $coll['Y'], $output)) {
                    if(('0' . $coll['X']) != $output[0]) {
                        $salida['errores'][] = array('linea' => $row, 'error' => 'Registro invalido, el codigo Centro de gestión no coincide con Nombre Centro de gestión');
                    } else {
                        $obra_id = (isset($output[0])) ? $output[0] : '';
                    }
                }
                $uid = $coll['A'] . ' ' . $coll['G'];
                if (!is_null($obra_id)) {
                    $obra = $this->GuideExits->BudgetItems->Budgets->Buildings->find('all', [
                            'conditions' => ['softland_id' => $obra_id],
                            'contain' => ['Budgets']
                            //'conditions' => ['softland_id' => $obra_id]
                        ])->First();
                    if(empty($obra)) {
                        $salida['errores'][] =  array('linea' => $row, 'error' => 'La obra no existe en el sistema.');
                    } else {
                        $budget_id = $obra->budget->id;
                        $registro = $this->GuideExits->find('all', [
                                'conditions' => ['uid' => $uid],
                            ])->First();
                        //no existe registro
                        if(empty($registro)) {
                            $item = $this->GuideExits->BudgetItems->find('all', [
                                'conditions' => ['budget_id' => $budget_id, 'item' => $coll['T']],
                            ])->First();
                            if(is_null($item)) {
                                $salida['errores'][] =  array('linea' => $row, 'error' => 'La partida no existe en el presupuesto.');
                            }
                        } else {
                            $salida['errores'][] =  array('linea' => $row, 'error' => 'El registro ya existe en el presupuesto.');
                        }
                    }
                }
                $our_budget_item_id = (isset($item->id)) ? $item->id : null;
                $salida['type'] = $type;
                $salida['registros'][$row] = array(
                    'uid' => $uid,
                    'building_id' => $obra_id,
                    'budget_item_id' => $our_budget_item_id,
                    'budget_item' => $coll['T'],
                    'voucher' => $coll['A'],
                    'date_system' => $coll['B'],
                    'product_code' => $coll['G'],
                    'product_name' => $coll['H'],
                    'amount' => $coll['K'],
                    'unit_price' => $coll['Q'],
                    'unit_type' => $coll['J'],
                    'product_total' => $coll['N'],
                    'json' => json_encode($coll));
            }
        }
    return $salida;
    }

/**
 * Manda el excel a un arreglo php que tiene anidados:
 * - 1 Hoja de trabajo > presupuesto > item padre > sub items.
 * El algoritmo se detiene cuando:
 * - Formato de items invalido.
 * - El calculo de cantidad por precio unitario es invalido.
 * - Hay una celda vacía para algún valor en algún elemento
 * @param  string $ruta temporal del archivo excel
 * @param  boolean $file true si es la ruta del archivo, false si es el array directo.
 * @return array multidimensional con 3 arrays, 'errores' con una lista de errores encontrados en el excel, 'excel' el excel en un array plano, 'arbol' es el excel ordenado por jerarquia en forma de arbol.
 * @author Omar Sepúlveda <omar.sepulveda@ideauno.cl>
 */
    public function excel_subcontracts($ruta, $type)
    {
        $showDebug = false;
        $salida = array('registros' => array(), 'errores' => array());
        $headers = array(
            'A' => 'Tipo',
            'B' => 'N°',
            'C' => 'Centro',
            'D' => 'RUT',
            'E' => 'Nombre',
            'F' => 'Código',
            'G' => 'Descripción',
            'H' => 'Glosa',
            'I' => 'Partida',
            'J' => 'Unidad',
            'K' => 'Moneda',
            'L' => 'Tasa de Cambio',
            'M' => 'Cantidad',
            'P' => 'Precio',
            'R' => 'Total',
            'S' => 'Código',
            'T' => 'Descripción',
            'U' => 'Cantidad',
            'V' => 'Total',
            'W' => 'Saldo',
            'X' => 'Monto EEPP',
            'Y' => 'Fecha');
        // cargamos el archivo
        $objPHPExcel = \PHPExcel_IOFactory::load($ruta);
        // se coloca en la variable $excel la primera hoja del excel, se usa solo la primera
        $excel = $objPHPExcel->getActiveSheet();
        //paso a un array plano todo el excel.
        $excelarray = $excel->toArray(null, true, true, true);
        $excelOriginal = $excelarray;
        $resumen = null;
        $tempNC = 0;
        foreach ($excelarray as $row=>&$coll) {
            $sofland_id = null;
            if ($row == 1) {
                // if ($coll == $headers) {
                    unset($excelarray[$row]);
                    $tempNC += 1;
                    continue;
                // } else {
                //     $salida['errores'][] = array('linea' => $row, 'error' => 'Las Columnas no corresponden al formato valido');
                //     break;
                // }
            } else {
                // $coll['B'] = str_replace('/','-', $coll['B']);
                if (empty($coll['C']) || empty($coll['D']) ||  empty($coll['E']) || empty($coll['G']) || empty($coll['J']) || empty($coll['K']) ||
                 empty($coll['L']) || empty($coll['M']) || empty($coll['P']) || empty($coll['R']) || empty($coll['S']) || empty($coll['T']) ||
                  empty($coll['U']) || empty($coll['V']) || empty($coll['W']) || empty($coll['X']) || empty($coll['Y'])) {
                    $err = $this->validate_min_info($coll, $type);
                    $salida['errores'][] = array('linea' => $row, 'error' => 'No se encuentra el minimo de informacion para ingresar el registro al sistema. (' . $err . ')');
                }
                if (!preg_match("/^\d+(\.\d+)*$/", $coll['S'], $output)) {
                    $salida['errores'][] = array('linea' => $row, 'error' => 'Registro con partida con el formato invalido');
                    debug($coll['S']);
                }
                if (!preg_match("/^\d+(\-\d+)*$/", $coll['D'], $output)) {
                    $salida['errores'][] = array('linea' => $row, 'error' => 'Registro con partida con el formato invalido');
                    debug($coll['D']);
                }
                if (!preg_match("/^[1-9]\d{0,2}(\.\d{3})*(,\d+)?$/", $coll['L'], $output)) {
                    $salida['errores'][] = array('linea' => $row, 'error' => 'Registro con partida con el formato invalido');
                    debug($coll['L']);
                }
                if (!preg_match("/^[1-9]\d{0,2}(\.\d{3})*(,\d+)?$/", $coll['M'], $output)) {
                    $salida['errores'][] = array('linea' => $row, 'error' => 'Registro con partida con el formato invalido');
                    debug($coll['M']);
                }
                if (!preg_match("/^[1-9]\d{0,2}(\.\d{3})*(,\d+)?$/", $coll['P'], $output)) {
                    $salida['errores'][] = array('linea' => $row, 'error' => 'Registro con partida con el formato invalido');
                    debug($coll['P']);
                }
                if (!preg_match("/^[1-9]\d{0,2}(\.\d{3})*(,\d+)?$/", $coll['R'], $output)) {
                    $salida['errores'][] = array('linea' => $row, 'error' => 'Registro con partida con el formato invalido');
                    debug($coll['R']);
                }
                if (!preg_match("/^[1-9]\d{0,2}(\.\d{3})*(,\d+)?$/", $coll['U'], $output)) {
                    $salida['errores'][] = array('linea' => $row, 'error' => 'Registro con partida con el formato invalido');
                    debug($coll['U']);
                }
                if (!preg_match("/^[1-9]\d{0,2}(\.\d{3})*(,\d+)?$/", $coll['V'], $output)) {
                    $salida['errores'][] = array('linea' => $row, 'error' => 'Registro con partida con el formato invalido');
                    debug($coll['V']);
                }
                if (!preg_match("/^[1-9]\d{0,2}(\.\d{3})*(,\d+)?$/", $coll['W'], $output)) {
                    $salida['errores'][] = array('linea' => $row, 'error' => 'Registro con partida con el formato invalido');
                    debug($coll['W']);
                }
                if (!preg_match("/^[1-9]\d{0,2}(\.\d{3})*(,\d+)?$/", $coll['X'], $output)) {
                    $salida['errores'][] = array('linea' => $row, 'error' => 'Registro con partida con el formato invalido');
                    debug($coll['X']);
                }
                 //linea valida
                //valido codigo de obra softland, el 'cod Centro de gestion' viene sin el 0 inicial por que excel lo toma como numerico, asi q le agrego un 0 al principio y lo valido
                //contra el resultado de un regex que saca el codigo de la columna siguiente, 'nombre centro de gestion'.
                if (!preg_match("/^\d+(\-\d+)*$/", $coll['B'], $output)) {
                    $salida['errores'][] = array('linea' => $row, 'error' => 'Registro con partida con el formato invalido');
                } else {
                    $subcontract_number = explode('-', $coll['B']);
                    if(!empty($subcontract_number[0])) {
                        $sofland_id = $subcontract_number[0];
                    } else {
                        $salida['errores'][] = array('linea' => $row, 'error' => 'Registro invalido, el codigo Centro de gestión no coincide con Nombre Centro de gestión');
                    }
                }

                $subcontract_import_json = json_encode($coll);
                if (!is_null($sofland_id)) {
                    $building = $this->Subcontracts->BudgetItems->Budgets->Buildings->find('all', [
                        'conditions' => ['softland_id' => $sofland_id],
                        'contain' => ['Budgets']
                        //'conditions' => ['softland_id' => $obra_id]
                    ]);
                    if ($building->isEmpty()) {
                        $salida['errores'][] =  array('linea' => $row, 'error' => 'La obra no existe en el sistema.');
                    } else {
                        $building = $building->first();
                        $budget_id = $building->budget->id;
                        $previous_subcontracts_imports = $this->Subcontracts->find('all', [
                            'conditions' => ['json' => $subcontract_import_json],
                        ]);
                        //no existe registro
                        if ($previous_subcontracts_imports->isEmpty()) {
                            $item = $this->GuideExits->BudgetItems->find('all', [
                                'conditions' => ['budget_id' => $budget_id, 'item' => $coll['S']],
                            ]);
                            if ($item->isEmpty()) {
                                $salida['errores'][] =  array('linea' => $row, 'error' => 'La partida no existe en el presupuesto.');
                            } else {
                                $item = $item->first();
                            }
                        } else {
                            $salida['errores'][] =  array('linea' => $row, 'error' => 'El registro ya existe en el presupuesto.');
                        }
                    }
                }
                $date = explode('-', $coll['Y']);
                (strlen($date[2]) == 2) ? $date = $date[0] . '-' . $date[1] . '-20' . $date[2] : $date = $coll['Y'];
                $our_budget_item_id = (isset($item->id)) ? $item->id : null;
                $salida['type'] = $type;
                $salida['registros'][$row] = array(
                    'building_id' => $sofland_id,
                    'budget_item_id' => $our_budget_item_id,
                    'budget_item' => $coll['S'],
                    'subcontract_work_number' => $coll['B'],
                    'building_name' => $coll['C'],
                    'rut' => $coll['D'],
                    'name' => $coll['E'],
                    'description' => $coll['G'],
                    'amount' => $coll['M'],
                    'unit_type' => $coll['J'],
                    'currency' => $coll['K'],
                    'currency_rate' => $coll['L'],
                    'price' => $coll['P'],
                    'total' => $coll['R'],
                    'partial_description' => $coll['T'],
                    'partial_amount' => $coll['U'],
                    'partial_total' => $coll['V'],
                    'balance_due' => $coll['W'],
                    'payment_statement_total' => $coll['X'],
                    'date' => $date,
                    'json' => json_encode($coll));
            }
        }
        return $salida;
    }
}
