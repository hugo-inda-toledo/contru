<?php
namespace App\Model\Table;

use App\Model\Entity\Budget;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Budgets Model
 */
class BudgetsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('budgets');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Buildings', [
            'foreignKey' => 'building_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_created_id'
        ]);

        $this->belongsTo('Currency', [
            'className' => 'Currencies',
            'foreignKey' => 'currency_id'
        ]);
        // $this->belongsTo('UserCreateds', [
        //     'className' => 'Users',
        //     'foreignKey' => 'user_created_id',
        //     'joinType' => 'INNER'
        // ]);
        $this->belongsTo('UserModifieds', [
            'className' => 'Users',
            'foreignKey' => 'user_modified_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Assists', [
            'foreignKey' => 'budget_id'
        ]);
        $this->hasMany('Bonuses', [
            'foreignKey' => 'budget_id'
        ]);
        $this->hasMany('Deals', [
            'foreignKey' => 'budget_id'
        ]);
        $this->hasMany('BudgetApprovals', [
            'foreignKey' => 'budget_id'
        ]);
        $this->hasMany('BudgetItems', [
            'foreignKey' => 'budget_id'
        ]);
        $this->hasMany('PaymentStatements', [
            'foreignKey' => 'budget_id'
        ]);
        $this->hasMany('Schedules', [
            'foreignKey' => 'budget_id'
        ]);
        $this->belongsToMany('Currencies', [
            'joinTable' => 'currencies_values',
            'through' => 'CurrenciesValues',
        ]);
        $this->hasMany('CurrenciesValues', [
            'foreignKey' => 'budget_id',
            'saveStrategy' => 'replace'
        ]);
        $this->hasMany('SalaryReports', [
            'foreignKey' => 'budget_id'
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

        $extra = 'Some additional value needed inside the closure';
        $validator
            ->add('duration', [
                'numeric' => [
                    'rule' => 'numeric',
                    'message' => 'value must be numeric'
                ],
                'positive' => [
                    'rule' => function ($value, $context) use ($extra) {
                        // Custom logic that returns true/false
                        return ($value < 0) ? false : true;
                    },
                    'message' => 'debe ser un valor mayor a cero'
                ],
            ])
            ->requirePresence('duration', 'create')
            ->notEmpty('duration');

        $validator
            ->add('general_costs', 'valid', ['rule' => 'numeric'])
            ->requirePresence('general_costs', 'create')
            ->notEmpty('utilities');

        $validator
            ->add('utilities', 'valid', ['rule' => 'numeric'])
            ->requirePresence('utilities', 'create')
            ->notEmpty('utilities');


        // $validator
        //     ->add('uf_value', 'valid', ['rule' => 'numeric'])
        //     ->requirePresence('uf_value', 'create')
        //     ->notEmpty('uf_value');

        // $validator
        //     ->add('total_cost_uf', 'valid', ['rule' => 'numeric'])
        //     ->requirePresence('total_cost_uf', 'create')
        //     ->notEmpty('total_cost_uf');

        $validator
            ->allowEmpty('comments');

        $validator
            ->allowEmpty('file');

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
        $rules->add($rules->existsIn(['building_id'], 'Buildings'));
        //$rules->add($rules->existsIn(['user_created_id'], 'Users'));
        //$rules->add($rules->existsIn(['user_modified_id'], 'Users'));
        return $rules;
    }

    /**
     * Manda el excel a un arreglo php que tiene forma de arbol:
     * - 1 Hoja de trabajo > presupuesto > item padre > sub items.
     * @param  array $arr arreglo plano para ordenar en arbol segun su primera columna (items 1 1.1 1.1.1 etc)
     * @return array multidimensional con arbol jerarquico.
     * @author Omar Sepúlveda <omar.sepulveda@ideauno.cl>
     */

    public function array_to_tree($arr)
    {
        $treeH = array();
        foreach($arr as $k =>$row) {
            $tests = explode(".", $row['A']);
            $this->assignArrayByPath($treeH,$row['A'], $row);
        }
        return $treeH;
    }

    /** magia para la funcion de arriba
    */
    public function assignArrayByPath(&$arr, $path, $value) {
        $keys = explode('.', $path);
        while ($key = array_shift($keys)) {
            $arr = &$arr[$key];
        }

        $arr = $value;
    }

    /**
     * formatea y valida el string recibido para buscar el item anterior, correspondiente.
     * @param  string $excel, excel completo para buscar el item anterior.
     * @param  string $node, string con el item validar.
     * @return string con el parent correspondiente o false si el nodo no tiene padre y es la raiz.
     * @author Omar Sepúlveda <omar.sepulveda@ideauno.cl>
     */
    public function findBeforeChild($excel, $node) {

        $showDebug = false;
        ($showDebug) ? debug('Node es: ' . $node) : false;
        $parentId = explode('.', $node);
        $lastItemChars = end($parentId);
        reset($parentId);
        //($showDebug) ? debug($parentId) : false;
        //($showDebug) ? debug($lastItemChars) : false;
        $first_item = reset($excel)['A'];
        //($showDebug) ? debug($first_item) : false;
        if($node == $first_item && $lastItemChars == $first_item && count($parentId) == 1) {
            ($showDebug) ? debug('nodo es 1, primer item no se busca padre ni hijos.') : false;
            $patronBefore = $node;
            return true;
        }
        elseif($lastItemChars == 1 && count($parentId) > 1) {
            ($showDebug) ? debug('primer item hijo, busco parent.') : false;
            $patronBefore = $this->findParent($excel, $node);

            //aca agregar if para q el primer item hijo de un padre sea valido, because why not
        }
        else {
            ($showDebug) ? debug('item hijo > 1 busco anterior') : false;
            $beforeItem = $lastItemChars - 1;
            $before = $parentId;
            array_splice($before, -1, 1, $beforeItem);
            $patronBefore = (!empty($before)) ? implode(".",$before) : false;
        }
        ($showDebug) ? debug($patronBefore) : false;
        //($showDebug) ? debug(array_column($excel, 'A')) : false;
        $keyNode = array_search($node, array_column($excel, 'A'),true);
        ($showDebug) ? debug('keyNode: ' . $keyNode) : false;
        $key = ($patronBefore) ? array_search($patronBefore, array_column($excel, 'A'),true) : false;
        ($showDebug) ? debug('key: ' . $key . ' existe, hijo anterior es: ' . $patronBefore .', nodo actual es: ' . $node) : false;
        ($showDebug) ? debug('patronBefore: ' . $patronBefore) : false;
        ($showDebug) ? debug('key: ' . $key . 'item anterior: ' . $patronBefore . 'keyNodo: ' . $keyNode . ' nodo:' . $node) : false;
        return ($key < $keyNode) ? true : false;
    }

    /**
     * formatea y valida el string recibido para buscar el item padre, correspondiente.
     * @param  string $node, string con el item para formatear y validar.
     * @return string con el parent correspondiente o false si el nodo no tiene padre y es la raiz.
     * @author Omar Sepúlveda <omar.sepulveda@ideauno.cl>
     */
    public function findParent($excel, $node) {

        $showDebug = false;
        //($showDebug) ? debug($node) : false;
        $parentId = explode('.', $node);
        //($showDebug) ? debug($parentId) : false;
        $lastItemChars = array_pop($parentId);
        //($showDebug) ? debug($lastItemChars) : false;
        $patron = (!empty($parentId)) ? implode(".",$parentId) : false;
        //($showDebug) ? debug('el parent es: ' . $patron) : false;
        //($showDebug) ? debug(array_column($excel, 'A')) : false;
        $key = ($patron) ? array_search($patron, array_column($excel, 'A')) : false;
        ($showDebug) ? debug('key: ' . $key . ' existe, parent es: ' . $patron .', nodo es: ' . $node) : false;
        return ($key !== false) ? $patron: false;
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
 * @param  boolean $before_on_db true si el item anterior correlativo existe en db, false por defecto.
 * @return array multidimensional con 3 arrays, 'errores' con una lista de errores encontrados en el excel, 'excel' el excel en un array plano, 'arbol' es el excel ordenado por jerarquia en forma de arbol.
 * @author Omar Sepúlveda <omar.sepulveda@ideauno.cl>
 */
    public function excel_req_a_array($ruta, $file = true, $before_on_db = false)
    {

        $unidades_en_sistema = ( $this->BudgetItems->Units->find('list')->toArray() );
        $showDebug = false;
        $parent = array();
        $salida['errores'] = array();

        if($file == true) {
            // creando el lector
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007', true, "ISO-8859-1");
            // cargamos el archivo
            $objPHPExcel = $objReader->load($ruta);
            // se coloca en la variable $excel la primera hoja del excel, se usa solo la primera
            $excel = $objPHPExcel->getActiveSheet();
            // se obtiene la ultima columna del excel
            $highColumn = $excel->getHighestColumn();
            $start = false;
            // validar que las columnas no sobrepasen hasta "objetivo" para evitar data gigante por fila
            if($highColumn == 'H')
            {
                //paso a un array plano todo el excel.
                $excelarray = $excel->toArray(null, true, false, true);
                //seteo el boolean a true para que inicie el ordenamiento de la data
                $start = true;
            }
            else
            {
                //seteo el boolean a true para que no inicie el ordenamiento de la data
                $start = false;
                //guardo la letra de la columna hasta donde lee el excel
                $excelarray = $highColumn;
            }

         }
         else {
            $start=true;
            $excelarray = $ruta;
        }

        //si esta seteado en true, inicia iteracion ordenamiento
        if($start == true)
        {
            //array para el calculo de la sumatoria de items padre.
            $tree = array();
            $finRow = 0;
            ($showDebug) ? debug($excelarray) : false;
            foreach($excelarray as $row=>$coll) {
                ($showDebug) ? debug($coll) : false;
                if(empty($coll)) {
                    ($showDebug) ? debug('if 1') : false;
                    unset($excelarray[$row]);
                    continue;
                }
                //temp fix
                //end test validacion
                if(!isset($coll['A']) && !isset($coll['B']) && !isset($coll['C']) && !isset($coll['D']) && !isset($coll['E']) && !isset($coll['F'])) {
                    // ($showDebug) ? debug('if 2') : false;
                    unset($excelarray[$row]);
                    continue;
                }
                if(!isset($coll['H']) || is_null($coll['H']) && !is_null($coll['F'])){
                    $excelarray[$row]['H'] = $coll['F'];
                }

                // temp fix
                //si no es el primer row, se intenta validar. (primer row son los titulos de columnas)
                if($row == 1 && $file == true) {
                    ($showDebug) ? debug('if 3') : false;
                    unset($excelarray[$row]);
                    continue;
                }
                //es fin tabla, no se guardan las rows siguientes ya que son informacion innecesaria.
                //if($coll['A'] == null && $coll['B'] == null && $coll['C'] == null && $coll['D'] == null && $coll['E'] == null && $coll['F'] == null && $coll['H'] == null) {
                // cucho fix
                if(is_null($coll['A']) && preg_match('/TOTAL/',$coll['B'])) {
                    unset($excelarray[$row]);
                    continue;
                }
                if(is_null($coll['A'])) {
                    ($showDebug) ? debug('if 4') : false;
                    //break;
                    $finRow = $row;
                    //$salida['errores'][] = 'fin tabla: ' . $row;
                }
                if($finRow <= $row && $finRow != 0) {
                    ($showDebug) ? debug('if 5') : false;
                    //debug('final row: ' . $finRow . 'intento borrar row: '. $row);
                    unset($excelarray[$row]);
                    continue;
                }
                 //test validacion
                if(is_null($coll['A']) || is_null($coll['B'])) {
                    //($showDebug) ? debug('no cumple el minimo de informacion para crear un item.') : false;
                    $salida['errores'][] = 'error fila: ' . $row . ' no cumple el minimo de informacion para crear un item.';

                }
            }
            //empizan las validaciones del excel
            // A item
            // B Descripcion
            // C Unidad
            // D Cantidad
            // E Precio Unitario
            // F Precio Total
            // G Observaciones/Comentario
            // H Valor Objetivo
            $temp_b = array();
            ($showDebug) ? debug($excelarray) : false;

            foreach($excelarray as $row=>$coll) {
                    /* validacion de descripcion unica
                    if(isset($coll['B']) && !empty($coll['B'])) {
                        if(in_array($coll['B'], $temp_b)) {
                            $salida['errores'][] = 'error en fila: ' . $row . ' item con descripcion ya existe en la lista, row numero: ' . array_search($coll['B'], $temp_b);
                        }
                        $temp_b[$row] = $coll['B'];
                    }
                    fin de validacion descripcion unica.
                    */
                    //regex checkea el formato de la columna items.
                    if (!preg_match("/^\d+(\.\d+)*$/", $coll['A'], $parent)) {
                        $salida['errores'][] = 'error fila: ' . $row . ' item con formato invalido';
                    }
                    //item es valido, es padre o hijo
                    //si es padre lo guardo en la raiz del arreglo
                    //($showDebug) ? debug($parent) : false;
                    if(count($parent) == 1) {
                        $tree[$coll['A']] = array($coll);
                    }
                    else
                    {
                        //es hijo lo guardo dentro del arreglo del padre correspondiente.
                        $tree[substr($coll['A'], 0, 1)][] = $coll;
                    }
                    //separo logica excel

                    if(!$this->findBeforeChild($excelarray, $coll['A']) && !$before_on_db) {
                        $rows = array_keys(array_column($excelarray, 'A'), $coll['A'],true);
                        if(count($rows) > 1) {
                            $msgRows = '';
                            foreach($rows as $r) {
                                $msgRows = (empty($msgRows)) ? (++$r) : $msgRows . ', ' . (++$r);
                            }
                            $msgRows .= '.';
                            $salida['errores'][] = 'error fila: ' . $msgRows . ' El item se encuentra repetido.';
                        } else {
                            $salida['errores'][] = 'error fila: ' . $row . ' El item anterior correlativo no corresponde.';
                        }
                    }
                    //reviso F
                     if(isset($coll['F']) && is_numeric($coll['F'])) {
                        //Si no viene cantidad ni precio unitario significa que no debiese tener total_price
                        if((is_null($coll['C']) || empty($coll['C'])) && (is_null($coll['D']) || empty($coll['D'])) && (is_null($coll['E']) || empty($coll['E']))) {
                            $excelarray[$row]['F'] = null;
                            $excelarray[$row]['H'] = null;
                        }
                        if(empty($coll['C']) && empty($coll['D']) && empty($coll['E'])) {
                            continue;
                        }
                        if(is_null($coll['C']) || empty($coll['C'])) {
                            $salida['errores'][] = sprintf("error fila: %s columna: %s ... %s", $row, "C", "el tipo de Unidad esta vacio");
                        }else{
                            $coll['C']=strtoupper($coll['C']);
                            if(!in_array($coll['C'], $unidades_en_sistema)){
                                $salida['info'][] = sprintf("información fila: %s columna: %s ... %s", $row, "C", "el tipo de Unidad '".$coll['C']."' no existe, por ende se creará automáticamente.");
                            }
                            $excelarray[$row]['C'] = $coll['C'];
                        }
                        if(!is_numeric($coll['D'])) {
                            $salida['errores'][] = sprintf("error fila: %s columna: %s ... %s", $row, "D", "la Cantidad no es un numero valido");
                        }
                        if(!is_numeric($coll['E'])) {
                            $salida['errores'][] = sprintf("error fila: %s columna: %s ... %s", $row, "E", "el Precio Unitario no es un numero valido");
                        }
                        if(isset($coll['H']) && !is_numeric($coll['H'])) {
                            $salida['errores'][] = sprintf("error fila: %s columna: %s ... %s", $row, "I", "el valor objetivo no es un numero valido");
                        }
                        //cucho fix
                        /*if(($coll['D'] * $coll['E']) != $coll['F']) {
                            $salida['errores'][] = 'error de calculo en fila: ' . $row;
                        }*/

                        if(($coll['D'] * $coll['E']) != $coll['F']) {
                            /*
                            ya no se requiere corregir automaticamente en base a otra columna
                             */
                            /*
                            $calc_temp_uf = $coll['F'] / $coll['G'];
                            if((($coll['D'] * $coll['E']) == $coll['G']) && (($coll['G'] * $calc_temp_uf) == $coll['F'])) {
                            $excelarray[$row]['E'] = $coll['E'] * $calc_temp_uf;
                            } else {
                            $salida['errores'][] = 'error de calculo en fila: ' . $row;
                            }
                            */
                           $salida['errores'][] = sprintf("error fila: %d Cantidad X P. Unitario no concuerda con P.Total (D x E ≠ F)", $row);
                        }
                        /*if(isset($coll['I']) && $coll['I']>$coll['F']){
                            $salida['errores'][] = 'La meta es mayor al : ' . $row;
                        }*/
                        if(isset($coll['D']) && is_null($coll['E'])) {
                            $salida['errores'][] = sprintf("error fila: %s columna: %s ... %s", $row, "E" , " falta Precio Unitario, solo existe Cantidad");
                        }
                        elseif(is_null($coll['D']) && isset($coll['E'])) {
                            $salida['errores'][] = sprintf("error fila: %s columna: %s ... %s", $row, "D" , " falta Cantidad, solo existe Precio Unitario");
                        }
                        else {
                            //continue;
                        }
                    }
            }

            /** validar sumatoria del valor de un item padre
            *   aca en $tree tengo un arreglo con orden jerarquico, cada padre esta separado en un arreglo multidimensional
            *   donde el primer objeto del arreglo siempre es el padre y el resto de objetos son sus hijos.
            *   cada leaf seria un padre y todos sus hijos.
            **/
            /*
            foreach($tree as &$leaf) {
                $childSum = 0;
                if(count($leaf) > 0) {
                    if(count($leaf) == 1) {
                        $childSum = $leaf[0]['F'];
                    }
                    $c = 0;
                    foreach($leaf as $c => $child) {
                        if($c > 0) {
                            if($child != null && $child['F'] != null) {
                                $childSum = $childSum + $child['F'];
                            }
                        }
                    }
                    //$leaf[0]['F'] = $childSum;

                    foreach($excelarray as &$row) {
                        if($leaf[0]['A'] == $row['A']) {
                            $row['F'] = $childSum;
                            break;
                        }
                    }
                    //$salida['errores'][] = 'error en sumatoria del item: ' . $leaf[0]['A'] . ' la suma de los sub items no cuadra.';
                }
            }
            */
            ($showDebug) ? debug($excelarray) : false;

            //transformo el array plano de excel a un arbol jerarquico con todos los niveles correspondientes.
            $treeH = $this->array_to_tree($excelarray);

            //faltan cosas por hacer, parece que ya no
            $salida['excel'] = $excelarray;
            $salida['arbol'] = $treeH;
            ($showDebug) ? debug($salida['errores']) : false;
            return $salida;
        }
        else
        {
            // si no devuelve a la funcion la letra de la columna excel que esta leyendo informacion
            return $excelarray;
        }
    }


     /**
     * Calcula la sumatoria de partidas padres en el presupuesto.
     * @param  int $id, id unico identificador del presupuesto.
     * @param  array $budget_items_guide_exits_total, informacion del total de guias de salida por partida.
     * @param  array $budget_items_completed_tasks_totals, informacion del total de los trabajos realizados por partida.
     * @return array con cada key corresponde al item y cada value corresponde a la sumatoria total.
     * @author Omar Sepúlveda <omar.sepulveda@ideauno.cl>
     */
    public function calc_parent_totals($id = null, $budget_items_guide_exits_total = '', $budget_items_completed_tasks_totals = '', $budget_items_subcontracts = '')
    {
        $salida = array();
        $budget = $this->get($id, [
                'contain' => ['BudgetItems']
            ]);
        foreach($budget->budget_items as &$bi)
        {
            if ($bi->disabled === 0 && $bi->total_price > 0) {
                $nodePatern = explode('.', $bi->item);
                $parents = array();
                while (count($nodePatern) > 1) {
                    $parentPatern = (count($parents) < 1) ? array_shift($nodePatern) : $parents[(count($parents) - 1)] . '.' . array_shift($nodePatern);
                    $parents[] = $parentPatern;
                    if (isset($salida[$parentPatern])) {
                        (empty($salida[$parentPatern]['budget_total'])) ? $salida[$parentPatern]['budget_total'] = 0 : '';
                        (empty($salida[$parentPatern]['guide_exits_total'])) ? $salida[$parentPatern]['guide_exits_total'] = 0 : '';
                        (empty($salida[$parentPatern]['subcontracts_total'])) ? $salida[$parentPatern]['subcontracts_total'] = 0 : '';
                        (empty($salida[$parentPatern]['completed_tasks_total'])) ? $salida[$parentPatern]['completed_tasks_total'] = 0 : '';
                        $salida[$parentPatern]['budget_total'] += $bi->total_price;
                        (!empty($budget_items_guide_exits_total[$bi->id])) ? $salida[$parentPatern]['guide_exits_total'] += $budget_items_guide_exits_total[$bi->id] : '';
                        (!empty($budget_items_subcontracts[$bi->id])) ? $salida[$parentPatern]['subcontracts_total'] += $budget_items_subcontracts[$bi->id] : '';
                        (!empty($budget_items_completed_tasks_totals[$bi->id])) ? $salida[$parentPatern]['completed_tasks_total'] += $budget_items_completed_tasks_totals[$bi->id] : '';
                    } else {
                        (empty($salida[$parentPatern]['budget_total'])) ? $salida[$parentPatern]['budget_total'] = 0 : '';
                        (empty($salida[$parentPatern]['guide_exits_total'])) ? $salida[$parentPatern]['guide_exits_total'] = 0 : '';
                        (empty($salida[$parentPatern]['subcontracts_total'])) ? $salida[$parentPatern]['subcontracts_total'] = 0 : '';
                        (empty($salida[$parentPatern]['completed_tasks_total'])) ? $salida[$parentPatern]['completed_tasks_total'] = 0 : '';
                        $salida[$parentPatern]['budget_total'] = $bi->total_price;
                        (!empty($budget_items_guide_exits_total[$bi->id])) ? $salida[$parentPatern]['guide_exits_total'] += $budget_items_guide_exits_total[$bi->id] : '';
                        (!empty($budget_items_subcontracts[$bi->id])) ? $salida[$parentPatern]['subcontracts_total'] += $budget_items_subcontracts[$bi->id] : '';
                        (!empty($budget_items_completed_tasks_totals[$bi->id])) ? $salida[$parentPatern]['completed_tasks_total'] += $budget_items_completed_tasks_totals[$bi->id] : '';
                    }
                }
            }
        }
        return $salida;
    }

     /**
     * Calcula la el costo total de un presupuesto.
     * @param  int $id, id unico identificador del presupuesto.
     * @param  int $filter, filtro para calcular items, 0 todos, 1 solo originales, 2 solo extras.
     * @param  int $state, estado de items, 0 todos, 1 habilitados, 2 deshabilitados.
     * @return int sumatoria total.
     * @author Omar Sepúlveda <omar.sepulveda@ideauno.cl>
     */
    public function calc_total($id = null, $filter = 0, $state = 0)
    {
        $sum_p = 0;
        $budget = $this->get($id, [
                'contain' => ['BudgetItems']
            ]);
        foreach($budget->budget_items as &$bi)
        {
            if($bi->parent_id == null) {
                if($filter == 1 && $bi->extra === 1) {
                    unset($bi);
                    continue;
                }
                elseif($filter == 2 && $bi->extra === 0) {
                    unset($bi);
                    continue;
                }
                if($state == 1 && $bi->disabled === 1) {
                    unset($bi);
                    continue;
                }
                elseif($state == 2 && $bi->disabled === 0) {
                    unset($bi);
                    continue;
                }
                if($filter == 0 && $bi->extra > 0){
                    unset($bi);
                    continue;
                }
                $sum_p = (float) $sum_p + (float) $bi->total_price;
            }
        }
        // var_dump($sum_p);
        // die();

        return $sum_p;
    }
     /**
     * Calcula la el costo total de un presupuesto.
     * @param  int $id, id unico identificador del presupuesto.
     * @param  int $filter, filtro para calcular items, 0 todos, 1 solo originales, 2 solo extras.
     * @param  int $state, estado de items, 0 todos, 1 habilitados, 2 deshabilitados.
     * @return int sumatoria total.
     * @author Omar Sepúlveda <omar.sepulveda@ideauno.cl>
     */
    public function calc_total_target($id = null, $filter = 0, $state = 0)
    {
        $sum_p = 0;
        $budget = $this->get($id, [
                'contain' => ['BudgetItems']
            ]);
        foreach($budget->budget_items as &$bi)
        {
            if($bi->parent_id == null) {
                if($filter == 1 && $bi->extra === 1) {
                    unset($bi);
                    continue;
                }
                elseif($filter == 2 && $bi->extra === 0) {
                    unset($bi);
                    continue;
                }
                if($state == 1 && $bi->disabled === 1) {
                    unset($bi);
                    continue;
                }
                elseif($state == 2 && $bi->disabled === 0) {
                    unset($bi);
                    continue;
                }
                if($filter == 0 && $bi->extra > 0){
                    unset($bi);
                    continue;
                }
                $sum_p = (float) $sum_p + (float) $bi->target_value;
            }
        }
        // var_dump($sum_p);
        // die();

        return $sum_p;
    }
    /**
     * Obtiene el estado actual del presupuesto
     * @param  int $id, id unico identificador del presupuesto.
     * @return id ultimo estado.
     * @author Omar Sepúlveda <omar.sepulveda@ideauno.cl>
     */
    public function current_state($id = null)
    {
        $state = null;
        $budget = $this->get($id, [
                'contain' => ['BudgetApprovals']
            ]);
        if(!empty($budget['budget_approvals'])) {
            $lastApprobal = end($budget['budget_approvals']);
            $state = $lastApprobal->budget_state_id;
        }

        return $state;
    }

    /**
     * Obtiene el estado actual del presupuesto sin budegt_items
     * @param  int $id, id unico identificador del presupuesto.
     * @return id ultimo estado.
     * @author Diego De la Cruz B <diego.delacruz@ideauno.cl>
     */
    public function current_budget_state($id = null)
    {
        $state = null;
        $budget = $this->get($id, [
            'contain' => ['Buildings', 'BudgetApprovals']
        ]);
        if ($budget->building['active']) {
            if(!empty($budget['budget_approvals'])) {
                $state = $budget['budget_approvals'][count($budget['budget_approvals']) - 1];
            }
            return $state['budget_state_id'];
        } else { //si la obra está bloqueada
            return -1;
        }
    }

    /**
     * calcula la lista de meses del presupuesto en base a la duración del presupuesto
     * @param  datetime $start_date      objeto fecha del inicio del presupuesto
     * @param  int $budget_duration meses de duración del presupuesto
     * @return array                  lista de meses del presupuesto
     * @author Diego De la Cruz B <diego.delacruz@ideauno.cl>
     */
    public function getListMonthsBudget($start_date = '', $budget_duration = '')
    {
        $start_date->modify('first day of this month');
        $months = null;
        for ($i=0; $i <= $budget_duration; $i++) {
            $months[$start_date->format('Y_m')] = convertMonthToSpanish($start_date->format('F-Y'));
            $start_date->modify('+1 month');
        }
        return $months;
    }

    /**
     * Calcula el total de días hábiles por mes para la duración completa de la obra
     * @param  string $start_date      fecha de inicio
     * @param  int $budget_duration duracion en meses de la obra
     * @return array                 lista meses con el total de días
     * @author Diego De la Cruz <diego.delacruz@ideauno.cl>
     */
    public function totalDaysBudgetMonths($start_date = '', $budget_duration = '')
    {
        $finish_date = new \DateTime($start_date->format('Y-m-d'));
        $finish_date->modify('+' . $budget_duration . ' month');
        $months_working_days = array();
        for ($i=0; $i <= $budget_duration; $i++) {
            $first_day_month = new \DateTime($start_date->format('Y-m-d'));
            $first_day_month->modify('first day of this month');
            $last_day_month = new \DateTime($start_date->format('Y-m-d'));
            $last_day_month->modify('last day of this month');
            if ($i == 0) {
                $months_working_days[$start_date->format('Y_m')] = $this->number_of_working_days($start_date, $last_day_month);
            } else if ($i == $budget_duration) {
                $months_working_days[$start_date->format('Y_m')] = $this->number_of_working_days($first_day_month, $finish_date);
            } else {
                $months_working_days[$start_date->format('Y_m')] = $this->number_of_working_days($first_day_month, $last_day_month);
            }
            $start_date->modify('+1 month');
        }
        return $months_working_days;
    }

    /**
     * Devuelve el total de días de trabajo de la duración de la obra
     * @param  string $start_date      fecha de inicio
     * @param  int $budget_duration duración en meses
     * @return int                  total de días de trabajo
     * @author Diego De la Cruz <diego.delacruz@ideauno.cl>
     */
    public function totalDaysBudget($start_date = '', $budget_duration = '')
    {
        $finish_date = new \DateTime($start_date->format('Y-m-d'));
        $finish_date->modify('+' . $budget_duration . ' month');
        $total_days = $this->number_of_working_days($start_date, $finish_date);
        return $total_days;
    }

    /**
     * Calcula el avance proyectado para la duración del proyecto en meses, además los valores de avance total por mes de las planificaciones
     * @param  string $budget_id               identificador presupuesto
     * @param  string $schedules_progress_info información avances planificaciones
     * @param  string $total_days              total dias habiles presupuesto
     * @param  string $total_days_months       total dias habiles por mes presupuesto
     * @param  string $total_contract_currency total contrato presupuesto en moneda configurada
     * @return array                           info de avances presupuesto, proyectados, planificados y reales por mes
     * @author Diego De la Cruz <diego.delacruz@ideauno.cl>
     */
    public function proyected_progress_budget($budget_id = '', $schedules_progress_info = '', $total_days = '', $total_days_months = '', $total_contract_currency = '')
    {
        $budget_progress_info = array(); //array con toda la info para la vista
        $proyected_progress_budget = array(); //array de valores proyectados
        $proyected_progress_schedules = array(); //array de valores progreso proyectado planificacion
        $overall_progress_schedules = array(); //array valores progreso real planificacion
        $total_proyected_progress_budget = 0; //variable calculo cada mes de proyección de avance del ppto total
        $total_proyected_progress_schedules = 0; //variable de sumatoria de la planificaciones por mes avance proyectado
        $total_overall_progress_schedules = 0; //variable de sumatoria de la planificaciones por mes avance real
        foreach ($schedules_progress_info as $schedule_progress_info) { // planificación
            if (count($schedule_progress_info) > 1) { // si tiene info para calcular
                $first_day_month = new \DateTime($schedule_progress_info['start_date']->format('Y-m-d'));
                $first_day_month->modify('first day of this month');
                $last_day_month = new \DateTime($schedule_progress_info['start_date']->format('Y-m-d'));
                $last_day_month->modify('last day of this month');
                // chequear si la semana de la planificación completa corresponde al mes de la fecha de inicio
                if ($schedule_progress_info['start_date'] >= $first_day_month && $schedule_progress_info['start_date'] <= $last_day_month &&
                    $schedule_progress_info['finish_date'] >= $first_day_month && $schedule_progress_info['finish_date'] <= $last_day_month) {
                    $total_proyected_progress_schedules += $schedule_progress_info['proyected_progress'];
                    $total_overall_progress_schedules += $schedule_progress_info['overall_progress'];
                    (empty($proyected_progress_schedules[$schedule_progress_info['start_date']->format('Y_m')])) ?
                     $proyected_progress_schedules[$schedule_progress_info['start_date']->format('Y_m')] = 0 : '';
                    $proyected_progress_schedules[$schedule_progress_info['start_date']->format('Y_m')] = $total_proyected_progress_schedules;
                    (empty($overall_progress_schedules[$schedule_progress_info['start_date']->format('Y_m')])) ?
                     $overall_progress_schedules[$schedule_progress_info['start_date']->format('Y_m')] = 0 : '';
                    $overall_progress_schedules[$schedule_progress_info['start_date']->format('Y_m')] = $total_overall_progress_schedules;
                } else {
                    // si la planificación termina en otro mes calcular el avance diario al mes que corresponde
                    $first_day_schedule = new \DateTime($schedule_progress_info['start_date']->format('Y-m-d'));
                    while ($first_day_schedule <= $schedule_progress_info['finish_date']) {
                        if ($first_day_schedule->format('Y_m') == $schedule_progress_info['start_date']->format('Y_m')) {
                            // si todavía estamos el en el mes en curso se suman los valores al acumulado y se reemplaza el valor del array
                            $total_proyected_progress_schedules += round($schedule_progress_info['proyected_progress'] / $schedule_progress_info['total_days'], 2);
                            $total_overall_progress_schedules += round($schedule_progress_info['overall_progress'] / $schedule_progress_info['total_days'], 2);
                            (empty($proyected_progress_schedules[$first_day_schedule->format('Y_m')])) ?
                             $proyected_progress_schedules[$first_day_schedule->format('Y_m')] = 0 : '';
                            $proyected_progress_schedules[$first_day_schedule->format('Y_m')] = $total_proyected_progress_schedules;
                            (empty($overall_progress_schedules[$first_day_schedule->format('Y_m')])) ?
                             $overall_progress_schedules[$first_day_schedule->format('Y_m')] = 0 : '';
                            $overall_progress_schedules[$first_day_schedule->format('Y_m')] = $total_overall_progress_schedules;
                        } else {
                            // nuevo mes de valores de planificacion se reemplaza el acumulado, y se suman los valores en el array del nuevo mes
                            $total_proyected_progress_schedules += round($schedule_progress_info['proyected_progress'] / $schedule_progress_info['total_days'], 2);
                            $total_overall_progress_schedules += round($schedule_progress_info['overall_progress'] / $schedule_progress_info['total_days'], 2);
                            (empty($proyected_progress_schedules[$first_day_schedule->format('Y_m')])) ?
                             $proyected_progress_schedules[$first_day_schedule->format('Y_m')] = 0 : '';
                            $proyected_progress_schedules[$first_day_schedule->format('Y_m')] = $total_proyected_progress_schedules;
                            (empty($overall_progress_schedules[$first_day_schedule->format('Y_m')])) ?
                             $overall_progress_schedules[$first_day_schedule->format('Y_m')] = 0 : '';
                            $overall_progress_schedules[$first_day_schedule->format('Y_m')] = $total_overall_progress_schedules;
                        }
                        $first_day_schedule->modify('+1 day');
                    }
                }
            }
        }
        $budget_day_cost = $total_contract_currency / $total_days;
        foreach ($total_days_months as $month => $days) {
            $total_proyected_progress_budget += round($days * $budget_day_cost);
            $proyected_progress_budget[$month] = $total_proyected_progress_budget;
        }
        $budget_progress_info['proyected_progress_budget'] = $proyected_progress_budget;
        $budget_progress_info['proyected_progress_schedules'] = $proyected_progress_schedules;
        $budget_progress_info['overall_progress_schedules'] = $overall_progress_schedules;
        // debug($budget_progress_info); die();
        return $budget_progress_info;
    }

    /**
     * Calcula el avance proyectado para la duración del proyecto en meses
     * @param  string $start_date [description]
     * @return [type]             [description]
     * @author Diego De la Cruz <diego.delacruz@ideauno.cl>
     */
    public function proyected_progress_budget_compare_months($budget_id = '', $schedules_progress_info = '', $total_days = '', $total_days_months = '', $total_contract_currency = '')
    {
        $budget_progress_compare_months_info = array(); //array con toda la info para la vista
        $proyected_progress_budget = array(); //array de valores proyectados
        $proyected_progress_schedules = array(); //array de valores progreso proyectado planificacion
        $overall_progress_schedules = array(); //array valores progreso real planificacion
        $total_proyected_progress_budget = 0; //variable calculo cada mes de proyección de avance del ppto total
        $total_proyected_progress_schedules = 0; //variable de sumatoria de la planificaciones por mes avance proyectado
        $total_overall_progress_schedules = 0; //variable de sumatoria de la planificaciones por mes avance real
        foreach ($schedules_progress_info as $schedule_progress_info) {
            if (count($schedule_progress_info) > 1) {
                $first_day_month = new \DateTime($schedule_progress_info['start_date']->format('Y-m-d'));
                $first_day_month->modify('first day of this month');
                $last_day_month = new \DateTime($schedule_progress_info['start_date']->format('Y-m-d'));
                $last_day_month->modify('last day of this month');
                // chequear si la semana de la planificación completa corresponde al mes de la fecha de inicio
                if ($schedule_progress_info['start_date'] >= $first_day_month && $schedule_progress_info['start_date'] <= $last_day_month &&
                    $schedule_progress_info['finish_date'] >= $first_day_month && $schedule_progress_info['finish_date'] <= $last_day_month) {
                    $total_proyected_progress_schedules += $schedule_progress_info['proyected_progress'];
                    $total_overall_progress_schedules += $schedule_progress_info['overall_progress'];
                    (empty($proyected_progress_schedules[$schedule_progress_info['start_date']->format('Y_m')])) ?
                     $proyected_progress_schedules[$schedule_progress_info['start_date']->format('Y_m')] = 0 : '';
                    $proyected_progress_schedules[$schedule_progress_info['start_date']->format('Y_m')] = $total_proyected_progress_schedules;
                    (empty($overall_progress_schedules[$schedule_progress_info['start_date']->format('Y_m')])) ?
                     $overall_progress_schedules[$schedule_progress_info['start_date']->format('Y_m')] = 0 : '';
                    $overall_progress_schedules[$schedule_progress_info['start_date']->format('Y_m')] = $total_overall_progress_schedules;
                } else {
                    // si la planificación termina en otro mes calcular el avance diario al mes que corresponde
                    $first_day_schedule = new \DateTime($schedule_progress_info['start_date']->format('Y-m-d'));
                    while ($first_day_schedule <= $schedule_progress_info['finish_date']) {
                        if ($first_day_schedule->format('Y_m') == $schedule_progress_info['start_date']->format('Y_m')) {
                            // si todavía estamos el en el mes en curso se suman los valores al acumulado y se reemplaza el valor del array
                            $total_proyected_progress_schedules += round($schedule_progress_info['proyected_progress'] / $schedule_progress_info['total_days'], 2);
                            $total_overall_progress_schedules += round($schedule_progress_info['overall_progress'] / $schedule_progress_info['total_days'], 2);
                            (empty($proyected_progress_schedules[$first_day_schedule->format('Y_m')])) ?
                             $proyected_progress_schedules[$first_day_schedule->format('Y_m')] = 0 : '';
                            $proyected_progress_schedules[$first_day_schedule->format('Y_m')] = $total_proyected_progress_schedules;
                            (empty($overall_progress_schedules[$first_day_schedule->format('Y_m')])) ?
                             $overall_progress_schedules[$first_day_schedule->format('Y_m')] = 0 : '';
                            $overall_progress_schedules[$first_day_schedule->format('Y_m')] = $total_overall_progress_schedules;
                        } else {
                            // nuevo mes de valores de planificacion se reemplaza el acumulado, y se suman los valores en el array del nuevo mes
                            $total_proyected_progress_schedules = round($schedule_progress_info['proyected_progress'] / $schedule_progress_info['total_days'], 2);
                            $total_overall_progress_schedules = round($schedule_progress_info['overall_progress'] / $schedule_progress_info['total_days'], 2);
                            (empty($proyected_progress_schedules[$first_day_schedule->format('Y_m')])) ?
                             $proyected_progress_schedules[$first_day_schedule->format('Y_m')] = 0 : '';
                            $proyected_progress_schedules[$first_day_schedule->format('Y_m')] += $total_proyected_progress_schedules;
                            (empty($overall_progress_schedules[$first_day_schedule->format('Y_m')])) ?
                             $overall_progress_schedules[$first_day_schedule->format('Y_m')] = 0 : '';
                            $overall_progress_schedules[$first_day_schedule->format('Y_m')] += $total_overall_progress_schedules;
                        }
                        $first_day_schedule->modify('+1 day');
                    }
                }
            }
        }
        $budget_day_cost = $total_contract_currency / $total_days;
        foreach ($total_days_months as $month => $days) {
            $proyected_progress_budget[$month] = round($days * $budget_day_cost);
        }
        $budget_progress_compare_months_info['proyected_progress_budget'] = $proyected_progress_budget;
        $budget_progress_compare_months_info['proyected_progress_schedules'] = $proyected_progress_schedules;
        $budget_progress_compare_months_info['overall_progress_schedules'] = $overall_progress_schedules;
        return $budget_progress_compare_months_info;
    }

    /**
     * Obtiene información de importaciones de Iconstruye de la obra
     * @param  int $budget_id identificador de presupuesto
     * @return array            Información de Iconstruye
     * @author Diego De la Cruz <diego.delacruz@ideauno.cl>
     */
    public function getIconstruyeStatsByBudgetId($buget_id = '', $months = '')
    {
        // Budget Items
        $budget_items_children_ids = $this->BudgetItems->find('list')->where(['BudgetItems.budget_id' => $buget_id, 'BudgetItems.parent_id IS NOT' => null])->select(['id'])->toArray();

        //IConstruye ITEMS
        $iconstruye_stats = array();
        $iconstruye_stats['total_imported_items'] = count($ic_items);
        $ic_total_items = 0;
        $ic_data = array_map(function() { return 0; }, $months);

        if( count($budget_items_children_ids) > 0 ){
            // $this->loadModel('IconstruyeImports');
            $guideExits = TableRegistry::get('GuideExits');
            $ic_items = $guideExits->find('all')
                ->where(['GuideExits.budget_item_id IN'=> array_keys($budget_items_children_ids)])
                ->select(['id','product_name','unit_price','amount','product_total','budget_item_id','date_system'])
                ->toArray();

            $ic_date_months = $months;
            foreach ($ic_items as $key => $ic_item) {
                $ic_total_items += $ic_item['product_total'];
                $fecha = $ic_item['date_system']->format('Y_m');
                // Sumo segun MES
                if (isset($ic_data[$fecha])) {
                    //sumo
                    $ic_data[$fecha] += $ic_item['product_total'];
                } else {
                    //creo si no existe el mes
                    $ic_data[$fecha] = $ic_item['product_total'];
                    $ic_date_months[$fecha] = $ic_item['date_system']->format('F - Y');
                }
            }

        }

        $iconstruye_stats['sum_product_total'] = $ic_total_items;
        $iconstruye_stats['iconstruye_data'] = $ic_data;
        return $iconstruye_stats;
    }

    /**
     * Obtiene los avances proyectados y reales de todas las planificaciones de la obra
     * @param  int $budget_id identificador de presupuesto
     * @return array            Lista de planificaciones con valores proyectados y reales de avance
     * @author Diego De la Cruz <diego.delacruz@ideauno.cl>
     */
    public function getSchedulesProgressInfo($budget_id = '')
    {
        $schedules = $this->Schedules->find('all', [
            'conditions' => ['Schedules.budget_id' => $budget_id],
            'contain' => ['Progress' => ['BudgetItems']]
        ]);
        //extract porcentajes y valores
        $schedules_progress_info = array();
        $schedules_progress_info['total_schedules'] = $schedules->count();
        foreach ($schedules->toArray() as $schedule) {
            $schedules_progress_info[$schedule['id']]['name'] = $schedule['name'];
            $schedules_progress_info[$schedule['id']]['start_date'] = $schedule['start_date'];
            $schedules_progress_info[$schedule['id']]['finish_date'] = $schedule['finish_date'];
            $schedules_progress_info[$schedule['id']]['total_days'] = $schedule['total_days'];
            $schedules_progress_info[$schedule['id']]['total_budget_items'] = count($schedule['progress']);
            foreach ($schedule['progress'] as $progress) {
                (empty($schedules_progress_info[$schedule['id']]['proyected_progress'])) ? $schedules_progress_info[$schedule['id']]['proyected_progress'] = 0 : '';
                $schedules_progress_info[$schedule['id']]['proyected_progress'] +=  round($progress['budget_item']['total_uf'] * ($progress['proyected_progress_percent'] / 100), 2);
                (empty($schedules_progress_info[$schedule['id']]['overall_progress'])) ? $schedules_progress_info[$schedule['id']]['overall_progress'] = 0 : '';
                $schedules_progress_info[$schedule['id']]['overall_progress'] += round($progress['budget_item']['total_uf'] * ($progress['overall_progress_percent'] / 100), 2);
            }
        }
        return $schedules_progress_info;
    }

    /**
     * Función que obtiene los materiales de iconstruye ordenados por partida
     */
    public function getGuideExitsByBudgetItems($id = '')
    {
        $budget_items_id = $this->BudgetItems->find('list', ['conditions' => ['BudgetItems.budget_id' => $id], 'keyField' => 'id', 'valueField' => 'parent_id'])->toArray();
        if( count($budget_items_id) == 0 ){
            return array();
        }

        $budget_items_guide_exits = $this->BudgetItems->GuideExits->find('all', [
            'conditions' => ['GuideExits.budget_item_id IN' => array_keys($budget_items_id)], 'order' => ['GuideExits.budget_item_id ASC']])->toArray();

        if( count($budget_items_guide_exits) == 0 ){
            return array();
        }

        $guide_exits_budget_item_total = array();
        foreach ($budget_items_guide_exits as $budget_items_guide_exit) {
            (empty($guide_exits_budget_item_total[$budget_items_guide_exit['budget_item_id']])) ? $guide_exits_budget_item_total[$budget_items_guide_exit['budget_item_id']] = 0 : '';
            $guide_exits_budget_item_total[$budget_items_guide_exit['budget_item_id']] += $budget_items_guide_exit['product_total'];
        }
        return ($guide_exits_budget_item_total);
    }

    /**
     * Función que obtiene los subcontratos de iconstruye ordenados por partida
     */
    public function getSubcontractsByBudgetItems($id = '')
    {
        $budget_items_id = $this->BudgetItems->find('list', ['conditions' => ['BudgetItems.budget_id' => $id], 'keyField' => 'id', 'valueField' => 'parent_id'])->toArray();

        if( count($budget_items_id) == 0 ){
            return array();
        }

        $budget_items_subcontracts = $this->BudgetItems->Subcontracts->find('all', [
            'conditions' => ['Subcontracts.budget_item_id IN' => array_keys($budget_items_id)], 'order' => ['Subcontracts.budget_item_id ASC']])->toArray();

        if( count($budget_items_subcontracts) == 0 ){
            return array();
        }

        $subcontracts_budget_item_total = array();
        foreach ($budget_items_subcontracts as $budget_items_subcontract) {
            (empty($subcontracts_budget_item_total[$budget_items_subcontract['budget_item_id']])) ? $subcontracts_budget_item_total[$budget_items_subcontract['budget_item_id']] = 0 : '';
            $subcontracts_budget_item_total[$budget_items_subcontract['budget_item_id']] += $budget_items_subcontract['payment_statement_total'];
        }
        return ($subcontracts_budget_item_total);
    }

    public function getAllCompletedTasksByWorkerAndSchedulesOrderbyMonth($budget_id = '')
    {
        $schedules = $this->Schedules->find('all', ['conditions' => ['Schedules.budget_id' => $budget_id]])->toArray();
        $task_hours = array();
        $allTaskHoursByBudgetItems = array();
        $schedules_tasks_workers = array();
        foreach ($schedules as $schedule) {
            if ($schedule->start_date->format('Y-m') == $schedule->finish_date->format('Y-m')) {
                $worker_ids = $this->Schedules->CompletedTasks->find('list', ['conditions'=> ['CompletedTasks.schedule_id' => $schedule->id],
                 'keyField' => 'worker_id', 'valueField' => 'worker_id'])->toArray();
                foreach ($worker_ids as $worker_id) {
                    (empty($schedules_tasks_workers[$schedule->start_date->format('Y-m')][$worker_id][$schedule->id])) ? $schedules_tasks_workers[$schedule->start_date->format('Y-m')][$worker_id][$schedule->id] = 0 : '';
                    $schedules_tasks_workers[$schedule->start_date->format('Y-m')][$worker_id][$schedule->id] = $this->Schedules->CompletedTasks->Workers->getTaskHoursByWorkerIdOrderByBudgetItem($worker_id, $schedule->id);
                    // debug($schedules_tasks_workers); die;
                }
            } else {
                //cal days
                $last_day_month = new \DateTime($schedule->start_date->format('Y-m-d'));
                $last_day_month->modify('last day of this month');
                $first_day_month = new \DateTime($schedule->finish_date->format('Y-m-d'));
                $first_day_month->modify('first day of this month');
                $current_month_diff = $schedule->start_date->diff($last_day_month);
                $next_month_diff = $first_day_month->diff($schedule->finish_date);
                $schedule_current_month_percent = ($current_month_diff->days + 1) / 5;
                $schedule_next_month_percent = ($next_month_diff->days + 1) / 5;
                $worker_ids = $this->Schedules->CompletedTasks->find('list', ['conditions'=> ['CompletedTasks.schedule_id' => $schedule->id],
                 'keyField' => 'worker_id', 'valueField' => 'worker_id'])->toArray();
                foreach ($worker_ids as $worker_id) {
                    (empty($schedules_tasks_workers[$schedule->start_date->format('Y-m')][$worker_id][$schedule->id])) ? $schedules_tasks_workers[$schedule->start_date->format('Y-m')][$worker_id][$schedule->id] = 0 : '';
                    $schedules_tasks_workers[$schedule->start_date->format('Y-m')][$worker_id][$schedule->id] = $this->Schedules->CompletedTasks->Workers->getTaskHoursByWorkerIdOrderByBudgetItem($worker_id, $schedule->id);
                    foreach ($schedules_tasks_workers[$schedule->start_date->format('Y-m')][$worker_id][$schedule->id] as &$hours) {
                        $hours = $hours * $schedule_current_month_percent;
                    }
                    (empty($schedules_tasks_workers[$schedule->finish_date->format('Y-m')][$worker_id][$schedule->id])) ? $schedules_tasks_workers[$schedule->finish_date->format('Y-m')][$worker_id][$schedule->id] = 0 : '';
                    $schedules_tasks_workers[$schedule->finish_date->format('Y-m')][$worker_id][$schedule->id] = $this->Schedules->CompletedTasks->Workers->getTaskHoursByWorkerIdOrderByBudgetItem($worker_id, $schedule->id);
                    foreach ($schedules_tasks_workers[$schedule->finish_date->format('Y-m')][$worker_id][$schedule->id] as &$hours) {
                        $hours = $hours * $schedule_next_month_percent;
                    }
                }
            }
        }
        return $schedules_tasks_workers;
    }

    public function getBudgetItemsCompletedTasksHoursAndCostByWorker($budget_id = '', $schedules_tasks_workers = '')
    {
        $budget = $this->get($budget_id);
        $workers_data = $this->Assists->Workers->getSoftlandWorkersAndRentaInfoByBuildingWithWorkerId($budget->building_id);
        $budget_items_completed_tasks_cost = array();
        foreach ($schedules_tasks_workers as $month => $worker) {
            $last_day_month = new \DateTime($month);
            $last_day_month->modify('last day of this month');
            $first_day_month = new \DateTime($month);
            $first_day_month->modify('first day of this month');
            $month_salaries = $this->Assists->getMonthSalariesdata($budget_id, $first_day_month, $last_day_month, $workers_data);
            foreach ($worker as $worker_id => $schedule_tasks) {
                if( isset($month_salaries[$worker_id]['Salary']) ){
                    $month_hours = $month_salaries[$worker_id]['Salary']['month_total_hours'] + $month_salaries[$worker_id]['Salary']['month_overtime_hours'];
                    $month_salary = $month_salaries[$worker_id]['Salary']['total_assets'];
                    foreach ($schedule_tasks as $schedule_id => $budget_items) {
                        foreach ($budget_items as $budget_item_id => $hours) {
                            (empty($budget_items_completed_tasks_cost[$month][$worker_id]['hours'][$budget_item_id])) ? $budget_items_completed_tasks_cost[$month][$worker_id]['hours'][$budget_item_id] = 0 : '';
                            (empty($budget_items_completed_tasks_cost[$month][$worker_id]['cost'][$budget_item_id])) ? $budget_items_completed_tasks_cost[$month][$worker_id]['cost'][$budget_item_id] = 0 : '';
                            $budget_items_completed_tasks_cost[$month][$worker_id]['hours'][$budget_item_id] += $hours;
                            $budget_items_completed_tasks_cost[$month][$worker_id]['cost'][$budget_item_id] += round(($hours * $month_salary) / $month_hours, 0);
                        }
                    }
                }else{
                    // printf("No puedo calcular nada para el trabajador %d", $worker_id);
                }
            }
        }
        return ($budget_items_completed_tasks_cost);
    }

    /**
     *
     */
    public function getBudgetItemsCompletedTasksTotals($budget_items_completed_tasks_cost = '')
    {
        $budget_items_tasks_cost_total = array();
        foreach ($budget_items_completed_tasks_cost as $month => $worker) {
            foreach ($worker as $worker_id => $totals) {
                foreach ($totals['cost'] as $budget_item_id => $cost) {
                    (empty($budget_items_tasks_cost_total[$budget_item_id])) ? $budget_items_tasks_cost_total[$budget_item_id] = 0 : '';
                    $budget_items_tasks_cost_total[$budget_item_id] += $cost;
                }
            }
        }
        return ($budget_items_tasks_cost_total);
    }

    /**
     * funcion que permite obtener la cantidad de días laborales
     * permite especificar fechas de feriados
     * @param  date $from fecha inicio
     * @param  date $to   fecha termine
     * @return int       días
     * @author Diego De la Cruz <diego.delacruz@ideauno.cl>
     */
    function number_of_working_days($from, $to) {
        $workingDays = [1, 2, 3, 4, 5]; # date format = N (1 = Monday, ...)
        // $holidayDays = ['*-12-25', '*-01-01', $from->format('Y-m-d')]; # variable and fixed holidays
        $holidayDays = ['*-12-25', '*-01-01']; # variable and fixed holidays

        // $from = new \DateTime($from);
        // $to = new \DateTime($to);
        $to->modify('+1 day');
        $interval = new \DateInterval('P1D');
        $periods = new \DatePeriod($from, $interval, $to);

        $days = 0;
        foreach ($periods as $period) {
            if (!in_array($period->format('N'), $workingDays)) continue;
            if (in_array($period->format('Y-m-d'), $holidayDays)) continue;
            if (in_array($period->format('*-m-d'), $holidayDays)) continue;
            $days++;
        }
        return $days;
    }

    /**
     * Actualiza los campos total_price y total_uf de los padres del item enviado
     * @param  Object $pbi         Objeto a buscar
     * @param  int $total_price Total a actualizar
     * @param  int $total_uf    Total a actualizar
     * @return bool              True
     * @author Gabriel Rebolledo <gabriel.rebolledo@ideauno.cl>
     */
    public function updateParentsPrice($pbi, $total_price, $total_uf, $target_value){
        $parentBudgetItem = $this->BudgetItems->find('all', [
            'conditions' => [
                'BudgetItems.id' => $pbi->parent_id
            ]
        ])->first();
        if($parentBudgetItem!=null){
            //Guiardar
            $price = $parentBudgetItem->total_price+$total_price;
            $uf = $parentBudgetItem->total_uf+$total_uf;
            $target = $parentBudgetItem->target_value+$target_value;
            $parentBudgetItem->total_price = $price;
            $parentBudgetItem->total_uf = $uf;
            $parentBudgetItem->target_value = $target;
            $this->BudgetItems->save($parentBudgetItem);
            if($parentBudgetItem->parent_id!=null){
                self::updateParentsPrice($parentBudgetItem, $total_price, $total_uf, $target_value);
            }
        }
        return true;
    }
    /**
     * Actualiza los gastos generales del presupuesto
     * @param  Object $pbi         Objeto a buscar
     * @return bool              True
     * @author Omar Sepulveda <omar.sepulveda@ideauno.cl>
     */
    public function updateGeneralCosts($id){
        $parentBudgetItem = $this->BudgetItems->find('all', [
            'conditions' => [
                'BudgetItems.budget_id' => $id,
                'BudgetItems.extra' => 3,
                'BudgetItems.parent_id IS' => null
            ]
        ])->first();
        if($parentBudgetItem!=null){
            //Guiardar
            $price = $parentBudgetItem->total_price;
            $tmp_budget = $this->find('all', [
                'conditions' => [
                    'Budgets.id' => $id,
                ]
            ])->first();
            $tmp_budget->general_costs = $price;
            if($this->save($tmp_budget)) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public function getPercentageBudget($budget_id){
        $budget = $this->find('all', [
            'conditions' => [
                'Budgets.id' => $budget_id
            ]
        ])->first();

        $items = $this->BudgetItems->find('all', [
            'conditions' => [
                'BudgetItems.budget_id' => $budget_id,
                'BudgetItems.parent_id IS NULL'
            ],
        ])->toArray();
        $total_cost = $budget->total_cost;
        $current_items = array('avance_real' => 0, 'avance_proyectado' => 0, 'avance_economico' => 0);
        if(!empty($items)){
            // pr($items);
            foreach($items AS $item){
                // $total_percentage += $item->percentage_overall_progress;
                if($item->percentage_overall_progress != null) $current_items['avance_real'] += ($item->total_price * $item->percentage_overall_progress)/100;
                if($item->percentage_proyected_progress != null) $current_items['avance_proyectado'] += ($item->total_price * $item->percentage_proyected_progress)/100;
                if($item->percentage_paid != null) $current_items['avance_economico'] += ($item->total_price * $item->percentage_paid)/100;
                // pr($item->percentage_overall_progress.' '.$item_percentage);
            }

        }

        $total_percentage = array('avance_real' => 0, 'avance_proyectado' => 0, 'avance_economico' => 0);

        if($total_cost != 0)
        {
            $total_percentage['avance_real'] = round(($current_items['avance_real'] * 100)/$total_cost, 2);
            $total_percentage['avance_proyectado'] = round(($current_items['avance_proyectado'] * 100)/$total_cost, 2);
            $total_percentage['avance_economico'] = round(($current_items['avance_economico'] * 100)/$total_cost, 2);
        }

        return $total_percentage;
    }
}
