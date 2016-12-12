<?php
namespace App\Model\Table;

use App\Model\Entity\Worker;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Cache\Cache;
use Cake\I18n\Time;


/**
 * Workers Model
 */
class WorkersTable extends Table
{

    //  SOFTLAND: Otras tablas importantes: cwtcarg | sw_certifrta | sw_cajaper | sw_cajacomp | sw_afpper | sw_afp | sw_ccostoper | cwtccos | sw_certifhon ?
    //   | sw_certifrta | sw_isapre | sw_isapreper | sw_variable | sw_variablepersona | swequivaprevired |
    //
    //

    public static function defaultConnectionName()
    {
        return 'default';
    }

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('workers');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->hasMany('Assists', [
            'foreignKey' => 'worker_id'
        ]);
        $this->hasMany('Bonuses', [
            'foreignKey' => 'worker_id'
        ]);
        $this->hasMany('CompletedTasks', [
            'foreignKey' => 'worker_id'
        ]);
        $this->hasMany('Deals', [
            'foreignKey' => 'worker_id'
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

        return $validator;
    }

    public function checkBuildingsWithWorkersActive()
    {
        //Cargar siempre modelo de Obras de Softland
        $sfWorkers = TableRegistry::get('SfWorkers');
        $sfWorkerBuildings = TableRegistry::get('SfWorkerBuildings');
        $sf_workers = $sfWorkers->find('list', [
            'conditions' => ['SfWorkers.fechaFiniquito' => '99991231', 'SfWorkers.RolPrivado' => 'N'],
            'valueField' => 'ficha',
            'keyField' => 'ficha',
        ]);
        $building_workers = $sfWorkerBuildings->find('list', [
            'conditions' => ['SfWorkerBuildings.codArn IS NOT NULL', 'SfWorkerBuildings.codArn NOT IN' => ['001', '002', '004'],
             'SfWorkerBuildings.ficha IN' => $sf_workers, 'SfWorkerBuildings.vigHasta' => '99991201'],
            'valueField' => 'codArn',
            'keyField' => 'ficha',
            'groupField' => 'codArn'
        ]);
        return $building_workers->toArray();
    }

     /**
     * Obtiene la lista de trabajadores de Softland por area de negocio
     * @param  int $building_id identificador obra
     * @return [array] trabajadores de area de negocio sotfland
     */
    public function getSoftlandWorkers()
    {
        //Cargar siempre modelo de Obras de Softland
        $sfWorkers = TableRegistry::get('SfWorkers');
        $sfWorkerBuildings = TableRegistry::get('SfWorkerBuildings');
        $sfWorkerCargosPersonal = TableRegistry::get('SfWorkerCargosPersonal');
        $sfWorkerCargos = TableRegistry::get('SfWorkerCargos');
        $building_workers = $sfWorkerBuildings->find('list', [
            'conditions' => ['SfWorkerBuildings.codArn IS NOT NULL', 'SfWorkerBuildings.vigHasta' => '999101']
        ]);
        $sf_workers = $sfWorkers->find('all')
            ->where(['SfWorkers.ficha IN' => $building_workers, 'SfWorkers.fechaFiniquito' => '99991231', 'SfWorkers.RolPrivado' => 'N']);
        $array_fichas_building_clean = array();
        foreach ($sf_workers as $sf_worker) {
            $array_fichas_building_clean[] = $sf_worker->ficha;
        }
        $sf_workers_cargos_personal = $sfWorkerCargosPersonal->find('list')
            ->where(['SfWorkerCargosPersonal.ficha IN' => $array_fichas_building_clean, 'SfWorkerCargosPersonal.vigHasta' => '99991201']);
        $sf_workers_cargos = $sfWorkerCargos->find('list')
            ->where(['SfWorkerCargos.CarCod IN' => $sf_workers_cargos_personal->toArray()]);
        $sf_workers_cargos_personal_array = $sf_workers_cargos_personal->toArray();
        $sf_workers_cargos_array = $sf_workers_cargos->toArray();
        $workers_data = array();
        foreach ($sf_workers->toArray() as $key => $sf_worker) {
            $workers_data[$key]['ficha'] = $sf_worker->ficha;
            $workers_data[$key]['nombres'] = $sf_worker->nombres;
            $workers_data[$key]['appaterno'] = $sf_worker->appaterno;
            $workers_data[$key]['apmaterno'] = $sf_worker->apmaterno;
            $workers_data[$key]['rut'] = $sf_worker->rut;
            $workers_data[$key]['Email'] = $sf_worker->Email;
            $workers_data[$key]['direccion'] = $sf_worker->direccion;
            $workers_data[$key]['telefono1'] = $sf_worker->telefono1;
            $workers_data[$key]['fechaNacimient'] = $sf_worker->fechaNacimient;
            $workers_data[$key]['fechaIngreso'] = $sf_worker->fechaIngreso;
            $workers_data[$key]['Cargo']['cod_cargo'] = $sf_workers_cargos_personal_array[$sf_worker->ficha];
            $workers_data[$key]['Cargo']['nombre_cargo'] = $sf_workers_cargos_array[$workers_data[$key]['Cargo']['cod_cargo']];
        }
        return($workers_data);
    }


    public function getSoftlandWorkersByBuildingTest($building_id = ''){
        //Cache::clear(false, 'config_cache_sfworkers');
        if (!empty($building_id) && $building_id != null) {


            // cucho: si esta funcion no está cacheada, ejecuta
            if (($data_url = Cache::read('trabajadores_obra' . $building_id, 'config_cache_sfworkers')) === false) {
                $assistsDataTable = TableRegistry::get('AssistsData');
                $array_workers = $assistsDataTable->getWorkersByBuildingId($building_id);
                $workers_data = [];
                foreach ($array_workers as $key => $worker) {
                    $workers_data[] = [
                        'ficha' => $worker['softland_id'],
                        'nombres' => $worker['nombres'],
                        'appaterno' => $worker['appaterno'],
                        'apmaterno' => $worker['apmaterno'],
                        'rut' => ltrim($worker['rut'], '0'),
                        'Email' => $worker['email'],
                        'direccion' => $worker['direccion'],
                        'telefono1' => $worker['telefono1'],
                        'fechaNacimient' => $worker['fecha_nacimiento']->format('Y-m-d H:i:s'),
                        'fechaIngreso' => $worker['fecha_ingreso']->format('Y-m-d H:i:s'),
                        'Cargo' => [
                            'cod_cargo' => $worker['cargo_codigo'],
                            'nombre_cargo' => $worker['cargo_nombre'],
                        ],
                        'vigDesde' => $worker['vig_desde']->format('Y-m-d H:i:s'),
                        'vigHasta' => $worker['vig_hasta']->format('Y-m-d H:i:s'),
                    ];
                }
            } else {

                // si hay cache, lee desde el cache
                $workers_data = Cache::read('trabajadores_obra' . $building_id, 'config_cache_sfworkers');
                // con cache los tiempos bajan de 6 segundos a 0.
            }
            return $workers_data;
        } else {
            return null;
        }
    }

    /**
     * Obtiene la lista de trabajadores de Softland por area de negocio
     * @param  int $building_id identificador obra
     * @return [array] trabajadores de area de negocio sotfland
     * @author Alguien que no coloca el nombre <[<email address>]>
     * @author Refactoring by cucho
     * @author Refactoring 2 by cucho
     */
    public function getSoftlandWorkersByBuilding($building_id = '')
    {
        //Cache::clear(false, 'config_cache_sfworkers');
        if (!empty($building_id) && $building_id != null) {


            // cucho: si esta funcion no está cacheada, ejecuta
            if (($data_url = Cache::read('trabajadores_obra' . $building_id, 'config_cache_sfworkers')) === false) {

                //Cargar siempre modelo de Obras de Softland

                // cucho: ya no es necesario cargar todos los modelos
                // $sfBuildings = TableRegistry::get('SfBuildings');

                // cucho: esto se deja para hacer el enlace a la conexion de la bdd de softland
                $sfWorkers = TableRegistry::get('SfWorkers');

                // cucho: esto ya no es necesario
                // $sfWorkerBuildings = TableRegistry::get('SfWorkerBuildings');

                // cucho: esto ya no es necesario
                // $sfWorkerCargosPersonal = TableRegistry::get('SfWorkerCargosPersonal');

                // cucho: esto ya no es necesario
                // $sfWorkerCargos = TableRegistry::get('SfWorkerCargos');

                // cucho: se deja esto, busca el id de la building de softland
                $building = $this->Assists->Budgets->Buildings->find('all', ['conditions' => ['Buildings.id' => $building_id]])->first();

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
                $array_workers = $sfWorkers->connection()->execute($consulta)->fetchAll('assoc');
                // se mantiene la estructura original para no modificar la vista
                $workers_data = [];
                foreach ($array_workers as $key => $worker) {
                    $workers_data[] = [
                        'ficha' => $worker['ficha'],
                        'nombres' => $worker['nombres'],
                        'appaterno' => $worker['appaterno'],
                        'apmaterno' => $worker['apmaterno'],
                        'rut' => ltrim($worker['rut'], '0'),
                        'Email' => $worker['Email'],
                        'direccion' => $worker['direccion'],
                        'telefono1' => $worker['telefono1'],
                        'fechaNacimient' => $worker['fechaNacimient'],
                        'fechaIngreso' => $worker['fechaIngreso'],
                        'Cargo' => [
                            'cod_cargo' => $worker['CarCod'],
                            'nombre_cargo' => $worker['CarNom'],
                        ],
                        'vigDesde' => $worker['vigDesde'],
                        'vigHasta' => $worker['vigHasta'],
                    ];
                }

                // escribe en cache
                Cache::write('trabajadores_obra' . $building_id, $workers_data, 'config_cache_sfworkers');
            } else {

                // si hay cache, lee desde el cache
                $workers_data = Cache::read('trabajadores_obra' . $building_id, 'config_cache_sfworkers');
                // con cache los tiempos bajan de 6 segundos a 0.
            }


            /** Cucho: acá hay muchas consultas a softland, lo que hace lento ya que el servidor de
            softland está lejos, hay que darle una carga grande al servidor en vez de varias
            pequeñas, ya que la transmisión es lenta

            // cucho: lista de fichas de una obra
            $building_workers = $sfWorkerBuildings->find('list', [
                'conditions' => ['SfWorkerBuildings.codArn' => $building->softland_id, 'SfWorkerBuildings.vigHasta' => '99991201']
            ]);

            // cucho: busca los datos del trabajador de cada ficha
            $sf_workers = $sfWorkers->find('all')
                ->where(['SfWorkers.ficha IN' => $building_workers, 'SfWorkers.fechaFiniquito' => '99991231', 'SfWorkers.RolPrivado' => 'N']);

            // cucho: limpia la cosa
            $array_fichas_building_clean = array();
            foreach ($sf_workers as $sf_worker) {
                $array_fichas_building_clean[] = $sf_worker->ficha;
            }

            // cucho: busca los cargos de las fichas
            $sf_workers_cargos_personal = $sfWorkerCargosPersonal->find('list')
                ->where(['SfWorkerCargosPersonal.ficha IN' => $array_fichas_building_clean, 'SfWorkerCargosPersonal.vigHasta' => '99991201']);

            // cucho:
            $sf_workers_cargos = $sfWorkerCargos->find('list')
                ->where(['SfWorkerCargos.CarCod IN' => $sf_workers_cargos_personal->toArray()]);
            $sf_workers_cargos_personal_array = $sf_workers_cargos_personal->toArray();
            debug($sf_workers_cargos_personal_array);

            // cucho: manda a un array los cargos
            $sf_workers_cargos_array = $sf_workers_cargos->toArray();

            // cucho: crea un arreglo muy ordenado por cada trabajador

            // cucho: esto se reemplaza por otro array, se conservan los nombres
            // cucho: para asi no modificar la vista
            // $workers_data = array();
            foreach ($sf_workers->toArray() as $key => $sf_worker) {
                $workers_data[$key]['ficha'] = $sf_worker->ficha;
                $workers_data[$key]['nombres'] = $sf_worker->nombres;
                $workers_data[$key]['appaterno'] = $sf_worker->appaterno;
                $workers_data[$key]['apmaterno'] = $sf_worker->apmaterno;
                $workers_data[$key]['rut'] = $sf_worker->rut;
                $workers_data[$key]['Email'] = $sf_worker->Email;
                $workers_data[$key]['direccion'] = $sf_worker->direccion;
                $workers_data[$key]['telefono1'] = $sf_worker->telefono1;
                $workers_data[$key]['fechaNacimient'] = $sf_worker->fechaNacimient;
                $workers_data[$key]['fechaIngreso'] = $sf_worker->fechaIngreso;
                $workers_data[$key]['Cargo']['cod_cargo'] = $sf_workers_cargos_personal_array[$sf_worker->ficha];
                $workers_data[$key]['Cargo']['nombre_cargo'] = $sf_workers_cargos_array[$workers_data[$key]['Cargo']['cod_cargo']];
            }
            **/

            // cucho: retorna un arreglo muy ordenadito, eso esta muy bien
            return($workers_data);
        } else {
            return null;
        }
    }

    /**
     * Método que busca trabajadores con asistencia positiva en las fechas y obra requeridas
     * @param  string $building_id [description]
     * @param  string $start_date  [description]
     * @param  string $finish_date [description]
     * @return [type]              [description]
     * @author Diego De la Cruz <diego.delacruz@ideauno.cl>
     */
    public function getSoftlandWorkersWithAssistsByBuilding($building_id = '', $start_date = '', $finish_date = '')
    {
        if (!empty($building_id) && $building_id != null) {
            //Cargar siempre modelo de Obras de Softland
            $sfBuildings = TableRegistry::get('SfBuildings');
            $sfWorkers = TableRegistry::get('SfWorkers');
            $sfWorkerBuildings = TableRegistry::get('SfWorkerBuildings');
            $sfWorkerCargosPersonal = TableRegistry::get('SfWorkerCargosPersonal');
            $sfWorkerCargos = TableRegistry::get('SfWorkerCargos');
            $building = $this->Assists->Budgets->Buildings->find('all', ['conditions' => ['Buildings.id' => $building_id]])->first();
            $building_workers = $sfWorkerBuildings->find('list', [
                'conditions' => ['SfWorkerBuildings.codArn' => $building->softland_id, 'SfWorkerBuildings.vigHasta' => '99991201']
            ]);
            $sf_workers = $sfWorkers->find('all')
                ->where(['SfWorkers.ficha IN' => $building_workers, 'SfWorkers.fechaFiniquito' => '99991231', 'SfWorkers.RolPrivado' => 'N']);
            $array_fichas_building_clean = array();
            foreach ($sf_workers as $sf_worker) {
                $array_fichas_building_clean[] = $sf_worker->ficha;
            }
            $sf_workers_cargos_personal = $sfWorkerCargosPersonal->find('list')
                ->where(['SfWorkerCargosPersonal.ficha IN' => $array_fichas_building_clean, 'SfWorkerCargosPersonal.vigHasta' => '99991201']);
            $sf_workers_cargos = $sfWorkerCargos->find('list')
                ->where(['SfWorkerCargos.CarCod IN' => $sf_workers_cargos_personal->toArray()]);
            $sf_workers_cargos_personal_array = $sf_workers_cargos_personal->toArray();
            $sf_workers_cargos_array = $sf_workers_cargos->toArray();
            $workers_data = array();
            $workers_ids = $this->find('list', [
                'keyField' => 'softland_id',
                'valueField' => 'id',
                'conditions' => ['Workers.softland_id IN' => $array_fichas_building_clean]
                ]);
            foreach ($workers_ids as $softland_id => $worker_id) {
                $assists = $this->get($worker_id, [
                    'contain' => [
                        'Assists' => ['conditions' => ['Assists.assistance_date >=' => $start_date, 'Assists.assistance_date <=' => $finish_date],
                        'AssistTypes' => ['conditions' => ['AssistTypes.id' => 1]]]]]);
                foreach ($assists->assists as $assist) {
                    if (!empty($assist['assist_types'])) {
                        $workers_data[$softland_id] = $softland_id;
                    }
                }
            }
            foreach ($sf_workers as $key => $sf_worker) {
                if (!empty($workers_data[$sf_worker['ficha']])) {
                    if ($sf_worker['ficha'] == $workers_data[$sf_worker['ficha']]) {
                        $workers_data[$sf_worker['ficha']] = $sf_workers_cargos_array[$sf_workers_cargos_personal_array[$sf_worker['ficha']]] . ' - ' . $sf_worker['nombres'];
                    }
                }
            }
            return($workers_data);
        } else {
            return null;
        }
    }




    public function getSoftlandWorkersByBuildingWithWorkerId($building_id = '')
    {
        if (!empty($building_id) && $building_id != null) {
            //Cargar siempre modelo de Obras de Softland
            $sfBuildings = TableRegistry::get('SfBuildings');
            $sfWorkers = TableRegistry::get('SfWorkers');
            $sfWorkerBuildings = TableRegistry::get('SfWorkerBuildings');
            $sfWorkerCargosPersonal = TableRegistry::get('SfWorkerCargosPersonal');
            $sfWorkerCargos = TableRegistry::get('SfWorkerCargos');
            $building = $this->Assists->Budgets->Buildings->find('all', ['conditions' => ['Buildings.id' => $building_id]])->first();
            $building_workers = $sfWorkerBuildings->find('list', [
                'conditions' => ['SfWorkerBuildings.codArn' => $building->softland_id, 'SfWorkerBuildings.vigHasta' => '99991201']
            ]);
            $sf_workers = $sfWorkers->find('all')
                ->where(['SfWorkers.ficha IN' => $building_workers, 'SfWorkers.fechaFiniquito' => '99991231', 'SfWorkers.RolPrivado' => 'N']);
            $array_fichas_building_clean = array();
            foreach ($sf_workers as $sf_worker) {
                $array_fichas_building_clean[] = $sf_worker->ficha;
            }
            $sf_workers_cargos_personal = $sfWorkerCargosPersonal->find('list')
                ->where(['SfWorkerCargosPersonal.ficha IN' => $array_fichas_building_clean, 'SfWorkerCargosPersonal.vigHasta' => '99991201']);
            $sf_workers_cargos = $sfWorkerCargos->find('list')
                ->where(['SfWorkerCargos.CarCod IN' => $sf_workers_cargos_personal->toArray()]);
            $sf_workers_cargos_personal_array = $sf_workers_cargos_personal->toArray();
            $sf_workers_cargos_array = $sf_workers_cargos->toArray();
            $workers_data = array();
            foreach ($sf_workers->toArray() as $key => $sf_worker) {
                $worker_id = $this->find('all', ['conditions' => ['Workers.softland_id' => $sf_worker->ficha]])->first();
                if (is_null($worker_id)) {
                    $new_worker = $this->newEntity();
                    $new_worker->softland_id = $sf_worker->ficha;
                    if ($this->save($new_worker)) {
                        $workers_data[$new_worker->id]['ficha'] = $sf_worker->ficha;
                        $workers_data[$new_worker->id]['nombres'] = $sf_worker->nombres;
                        $workers_data[$new_worker->id]['appaterno'] = $sf_worker->appaterno;
                        $workers_data[$new_worker->id]['apmaterno'] = $sf_worker->apmaterno;
                        $workers_data[$new_worker->id]['rut'] = $sf_worker->rut;
                        $workers_data[$new_worker->id]['Email'] = $sf_worker->Email;
                        $workers_data[$new_worker->id]['direccion'] = $sf_worker->direccion;
                        $workers_data[$new_worker->id]['telefono1'] = $sf_worker->telefono1;
                        $workers_data[$new_worker->id]['fechaNacimient'] = $sf_worker->fechaNacimient;
                        $workers_data[$new_worker->id]['fechaIngreso'] = $sf_worker->fechaIngreso;
                        $workers_data[$new_worker->id]['Cargo']['cod_cargo'] = $sf_workers_cargos_personal_array[$sf_worker->ficha];
                        $workers_data[$new_worker->id]['Cargo']['nombre_cargo'] = $sf_workers_cargos_array[$workers_data[$new_worker->id]['Cargo']['cod_cargo']];
                    }
                } else {
                    $workers_data[$worker_id->id]['ficha'] = $sf_worker->ficha;
                    $workers_data[$worker_id->id]['nombres'] = $sf_worker->nombres;
                    $workers_data[$worker_id->id]['appaterno'] = $sf_worker->appaterno;
                    $workers_data[$worker_id->id]['apmaterno'] = $sf_worker->apmaterno;
                    $workers_data[$worker_id->id]['rut'] = $sf_worker->rut;
                    $workers_data[$worker_id->id]['Email'] = $sf_worker->Email;
                    $workers_data[$worker_id->id]['direccion'] = $sf_worker->direccion;
                    $workers_data[$worker_id->id]['telefono1'] = $sf_worker->telefono1;
                    $workers_data[$worker_id->id]['fechaNacimient'] = $sf_worker->fechaNacimient;
                    $workers_data[$worker_id->id]['fechaIngreso'] = $sf_worker->fechaIngreso;
                    $workers_data[$worker_id->id]['Cargo']['cod_cargo'] = $sf_workers_cargos_personal_array[$sf_worker->ficha];
                    $workers_data[$worker_id->id]['Cargo']['nombre_cargo'] = $sf_workers_cargos_array[$workers_data[$worker_id->id]['Cargo']['cod_cargo']];
                }
            }
            return($workers_data);
        } else {
            return null;
        }
    }

    /**
     * Obtiene el total de horas de trabajo por trabajo realizado en base a la asistencia por trabajador
     * @param  int $worker_id   identificador worker
     * @param  int $schedule_id identificador calendarizacion
     * @return float              total horas
     * @author Diego De la Cruz <diego.delacruz@ideauno.cl>
     */
    public function getTaskHoursByWorkerId($worker_id = '', $schedule_id = '')
    {
        if (!empty($worker_id) && $worker_id != null && !empty($schedule_id) && $schedule_id != null) {
            $completed_tasks = $this->CompletedTasks->find('all', ['contain' => ['Schedules'],
                'conditions' => ['CompletedTasks.worker_id' => $worker_id, 'CompletedTasks.schedule_id' => $schedule_id]
                ]);
            $task_hours = array();
            foreach ($completed_tasks as $completed_task) {
                if (empty($assists)) {
                    $assists = $this->Assists->find('all', ['contain' => ['AssistTypes'],
                        'conditions' => ['Assists.worker_id' => $worker_id, 'Assists.budget_id' => $completed_task->schedule->budget_id,
                        'Assists.assistance_date >=' => $completed_task->schedule->start_date->format('Y-m-d 00:00:01'),
                         'Assists.assistance_date <=' => $completed_task->schedule->finish_date->format('Y-m-d 23:59:59')]
                    ]);
                }
                $total_hours_week_assistance = 0;
                foreach ($assists as $assist) {
                    foreach ($assist['assist_types'] as $assist_type) {
                        $total_hours_week_assistance += $assist_type['_joinData']['hours'];
                    }
                    $total_hours_week_assistance += ($assist->overtime - $assist->delay);
                }
                $task_hours[$completed_task['budget_item_id']] = ($completed_task['budget_item_percentage'] / 100) * $total_hours_week_assistance;
            }
            return ($task_hours);
        } else {
            return 0;
        }
    }

    /**
     * Obtiene el total de horas de trabajo por trabajo realizado en base a la asistencia por trabajador
     * @param  int $worker_id   identificador worker
     * @param  int $schedule_id identificador calendarizacion
     * @return float              total horas
     * @author Diego De la Cruz <diego.delacruz@ideauno.cl>
     */
    public function getTaskHoursByWorkerIdOrderByBudgetItem($worker_id = '', $schedule_id = '')
    {
        if (!empty($worker_id) && $worker_id != null && !empty($schedule_id) && $schedule_id != null) {
            $completed_tasks = $this->CompletedTasks->find('all', ['contain' => ['Schedules'],
                'conditions' => ['CompletedTasks.worker_id' => $worker_id, 'CompletedTasks.schedule_id' => $schedule_id]
                ]);
            $task_hours = array();
            foreach ($completed_tasks as $completed_task) {
                if (empty($assists)) {
                    $assists = $this->Assists->find('all', ['contain' => ['AssistTypes'],
                        'conditions' => ['Assists.worker_id' => $worker_id, 'Assists.budget_id' => $completed_task->schedule->budget_id,
                        'Assists.assistance_date >=' => $completed_task->schedule->start_date->format('Y-m-d 00:00:01'),
                         'Assists.assistance_date <=' => $completed_task->schedule->finish_date->format('Y-m-d 23:59:59')]
                    ]);
                }
                $total_hours_week_assistance = 0;
                foreach ($assists as $assist) {
                    foreach ($assist['assist_types'] as $assist_type) {
                        $total_hours_week_assistance += $assist_type['_joinData']['hours'];
                    }
                    $total_hours_week_assistance += ($assist->overtime - $assist->delay);
                }
                $task_hours[$completed_task['budget_item_id']] = ($completed_task['budget_item_percentage'] / 100) * $total_hours_week_assistance;
            }
            return ($task_hours);
        } else {
            return 0;
        }
    }

    /**
     * Obtiene todos los datos necesarios de softland para las remuneraciones
     * @param  int $building_id   identificador de la obra
     * @return array              información trabajadores remuneraciones
     * @author Diego De la Cruz <diego.delacruz@ideauno.cl>
     */
    public function getSoftlandWorkersAndRentaInfoByBuildingWithWorkerId($building_id = '')
    {
        if (!empty($building_id) && $building_id != null) {
            //Cargar siempre modelo de Obras de Softland
            $sfBuildings = TableRegistry::get('SfBuildings');
            $sfWorkers = TableRegistry::get('SfWorkers');
            $sfWorkerBuildings = TableRegistry::get('SfWorkerBuildings');
            $sfWorkerCargosPersonal = TableRegistry::get('SfWorkerCargosPersonal');
            $sfWorkerCargos = TableRegistry::get('SfWorkerCargos');
            // $sfRentaCajasWorkers = TableRegistry::get('SfRentaCajasWorkers');
            // $sfRentaCajas = TableRegistry::get('SfRentaCajas');
            // $sfRentaAfpPreviReds = TableRegistry::get('SfRentaAfpPreviReds');
            $sfRentaIsapres = TableRegistry::get('SfRentaIsapres');
            $sfRentaIsapresWorkers = TableRegistry::get('SfRentaIsapresWorkers');
            $sfRentaAfps = TableRegistry::get('SfRentaAfps');
            $sfRentaAfpsWorkers = TableRegistry::get('SfRentaAfpsWorkers');
            $sfRentaVariables = TableRegistry::get('SfRentaVariables');
            $sfRentaVariablesWorkers = TableRegistry::get('SfRentaVariablesWorkers');
            // $sfWorkerCertificadoRenta = TableRegistry::get('SfWorkerCertificadoRenta');
            $building = $this->Assists->Budgets->Buildings->find('all', ['conditions' => ['Buildings.id' => $building_id]])->first();
            $building_workers = $sfWorkerBuildings->find('list', [
                'conditions' => ['SfWorkerBuildings.codArn' => $building->softland_id, 'SfWorkerBuildings.vigHasta' => '99991201']
            ])->toArray();
            // $building_workers = ['6228888'];
            $sf_workers = $sfWorkers->find('all')
                ->where(['SfWorkers.ficha IN' => $building_workers, 'SfWorkers.fechaFiniquito' => '99991231', 'SfWorkers.RolPrivado' => 'N'])->toArray();
             $workers_fichas = array();
            foreach ($sf_workers as $sf_worker) {
                $workers_fichas[] = $sf_worker['ficha'];
            }

            $sf_workers_cargos_personal = array();
            if( count($workers_fichas) > 0 ){
                $sf_workers_cargos_personal = $sfWorkerCargosPersonal->find('list')
                    ->where(['SfWorkerCargosPersonal.ficha IN' => $workers_fichas, 'SfWorkerCargosPersonal.vigHasta' => '99991201'])->toArray();

                $sf_workers_cargos = array();
                if( count($sf_workers_cargos_personal) > 0 ){
                    $sf_workers_cargos = $sfWorkerCargos->find('list')
                        ->where(['SfWorkerCargos.CarCod IN' => $sf_workers_cargos_personal])->toArray();
                }
            }
            // die('laraira');
            $sf_renta_afps = $sfRentaAfps->find('all')->toArray();
            $sf_renta_cod_afps = array();
            foreach ($sf_renta_afps as $renta_afp) {
                $sf_renta_cod_afps[$renta_afp['CodAFP']] = $renta_afp['CodPrevired'];
            }
            $sf_renta_isapres = $sfRentaIsapres->find('list', [
                'keyField' => 'CodIsapre',
                'valueField' => 'nombre',
                // 'groupField' => 'CodIsapre'
            ])->toArray();

            $sf_renta_isapres_workers = array();
            if( count($workers_fichas) > 0 ){
                $sf_renta_isapres_workers = $sfRentaIsapresWorkers->find('list')
                    // ->select(['ficha', 'codIsapre'])
                    ->where(['SfRentaIsapresWorkers.vigHasta' => '99991201', 'SfRentaIsapresWorkers.ficha IN' => $workers_fichas])->toArray();
            }
            // $sf_renta_afp_previreds = $sfRentaAfpPreviReds->find('list')
            //     ->select(['codPrevired', 'desPrevired'])->toArray();
            $sf_renta_afps_workers = array();
            if( count($workers_fichas) > 0 ){
                $sf_renta_afps_workers = $sfRentaAfpsWorkers->find('all')
                    ->select(['SfRentaAfpsWorkers.ficha', 'SfRentaAfpsWorkers.codAFP'])
                    ->where(['SfRentaAfpsWorkers.vigHasta' => '99991201', 'SfRentaAfpsWorkers.ficha IN' => $workers_fichas])->toArray();
            }

            $renta_afps_workers = array();
            foreach ($sf_renta_afps_workers as $sf_renta_afp_worker) {
                $renta_afps_workers[$sf_renta_afp_worker['ficha']] = $sf_renta_afp_worker['codAFP'];
            }
            $sf_renta_variables = $sfRentaVariables->find('list')
                ->select(['codVariable', 'descripcion'])
                ->where(['SfRentaVariables.fechaHasta' => '21001231'])->toArray();
            $sf_renta_variables_workers = $sfRentaVariablesWorkers->find('all')
                ->where(['SfRentaVariablesWorkers.ficha IN' => reset($workers_fichas),
                 'SfRentaVariablesWorkers.codVariable IN' => array_keys($sf_renta_variables)])->toArray();
            $variables_workers = array();
            foreach ($sf_renta_variables_workers as $renta_variable_worker) {
                $variables_workers[$renta_variable_worker['ficha']][$renta_variable_worker['codVariable']]['descripcion'] = $sf_renta_variables[$renta_variable_worker['codVariable']];
                $variables_workers[$renta_variable_worker['ficha']][$renta_variable_worker['codVariable']]['mes'] = $renta_variable_worker['mes'];
                $variables_workers[$renta_variable_worker['ficha']][$renta_variable_worker['codVariable']]['valor'] = $renta_variable_worker['valor'];
            }
            // debug(count($variables_workers));
            // debug($sf_renta_variables_workers);
            // debug($variables_workers);
            // debug($sf_renta_isapres);
            // debug($sf_renta_isapres_workers);
            // // debug($sf_renta_afp_previreds);
            // // debug($sf_renta_afps_workers);
            // debug($renta_afps_workers);
            // // debug($sf_renta_afps);
            // debug($sf_renta_cod_afps);
            // debug($sf_renta_isapres_workers); //die;
            // debug($sf_renta_variables); //die;
            // debug($variables_workers); //die;
            // debug($sf_renta_variables_workers); die;
            $workers_data = array();
            foreach ($sf_workers as $key => $sf_worker) {
                $worker_id = $this->find('all', ['conditions' => ['Workers.softland_id' => $sf_worker->ficha]])->first();
                if (is_null($worker_id)) {
                    $new_worker = $this->newEntity();
                    $new_worker->softland_id = $sf_worker->ficha;
                    if ($this->save($new_worker)) {
                        $workers_data[$new_worker->id]['ficha'] = $sf_worker->ficha;
                        $workers_data[$new_worker->id]['nombres'] = $sf_worker->nombres;
                        $workers_data[$new_worker->id]['appaterno'] = $sf_worker->appaterno;
                        $workers_data[$new_worker->id]['apmaterno'] = $sf_worker->apmaterno;
                        $workers_data[$new_worker->id]['rut'] = $sf_worker->rut;
                        $workers_data[$new_worker->id]['Email'] = $sf_worker->Email;
                        $workers_data[$new_worker->id]['direccion'] = $sf_worker->direccion;
                        $workers_data[$new_worker->id]['telefono1'] = $sf_worker->telefono1;
                        $workers_data[$new_worker->id]['fechaNacimient'] = $sf_worker->fechaNacimient;
                        $workers_data[$new_worker->id]['fechaIngreso'] = $sf_worker->fechaIngreso;
                        $workers_data[$new_worker->id]['Cargo']['cod_cargo'] = $sf_workers_cargos_personal[$sf_worker->ficha];
                        $workers_data[$new_worker->id]['Cargo']['nombre_cargo'] = $sf_workers_cargos[$workers_data[$new_worker->id]['Cargo']['cod_cargo']];
                        $workers_data[$new_worker->id]['VariablesRenta'] = $variables_workers[$sf_worker->ficha];
                    }
                } else {
                    $workers_data[$worker_id->id]['ficha'] = $sf_worker->ficha;
                    $workers_data[$worker_id->id]['nombres'] = $sf_worker->nombres;
                    $workers_data[$worker_id->id]['appaterno'] = $sf_worker->appaterno;
                    $workers_data[$worker_id->id]['apmaterno'] = $sf_worker->apmaterno;
                    $workers_data[$worker_id->id]['rut'] = $sf_worker->rut;
                    $workers_data[$worker_id->id]['Email'] = $sf_worker->Email;
                    $workers_data[$worker_id->id]['direccion'] = $sf_worker->direccion;
                    $workers_data[$worker_id->id]['telefono1'] = $sf_worker->telefono1;
                    $workers_data[$worker_id->id]['fechaNacimient'] = $sf_worker->fechaNacimient;
                    $workers_data[$worker_id->id]['fechaIngreso'] = $sf_worker->fechaIngreso;
                    $workers_data[$worker_id->id]['Cargo']['cod_cargo'] = $sf_workers_cargos_personal[$sf_worker->ficha];
                    $workers_data[$worker_id->id]['Cargo']['nombre_cargo'] = $sf_workers_cargos[$workers_data[$worker_id->id]['Cargo']['cod_cargo']];
                    $workers_data[$worker_id->id]['AFP'] = $renta_afps_workers[$sf_worker->ficha];
                    $workers_data[$worker_id->id]['VariablesRenta'] = (isset($variables_workers[$sf_worker->ficha]))?$variables_workers[$sf_worker->ficha]:[];
                    $workers_data[$worker_id->id]['Isapre'] = isset($sf_renta_isapres_workers[$sf_worker->ficha])?$sf_renta_isapres_workers[$sf_worker->ficha]: 'no';
                }
            }
            // $workers_data['VariablesRenta'] = $variables_workers;
            $workers_data['Afps'] = $sf_renta_afps;
            $workers_data['CodAfpsPrev'] = $sf_renta_cod_afps;
            $workers_data['Isapres'] = $sf_renta_isapres;
            return($workers_data);
        } else {
            return null;
        }
    }

    /**
     * Obtiene todos los datos necesarios de softland para las remuneraciones
     * @param  int $softland_id   identificador de la obra softland
     * @param  string $worker_ficha   identificador de trabajador softland
     * @return array              información trabajadores remuneraciones
     * @author Diego De la Cruz <diego.delacruz@ideauno.cl>
     */
    public function getSoftlandWorkerAndRentaInfoByWorkerId($worker_ficha)
    {
        if (!empty($worker_ficha) && $worker_ficha != null) {
            //Cargar siempre modelo de Obras de Softland
            $sfWorkers = TableRegistry::get('SfWorkers');
            $sfWorkerCargosPersonal = TableRegistry::get('SfWorkerCargosPersonal');
            $sfWorkerCargos = TableRegistry::get('SfWorkerCargos');
            $sfRentaIsapresWorkers = TableRegistry::get('SfRentaIsapresWorkers');
            $sfRentaAfpsWorkers = TableRegistry::get('SfRentaAfpsWorkers');
            $sfRentaVariables = TableRegistry::get('SfRentaVariables');
            $sfRentaVariablesWorkers = TableRegistry::get('SfRentaVariablesWorkers');

            $sf_workers = $sfWorkers->find('all')
                ->where(['SfWorkers.ficha' => $worker_ficha, 'SfWorkers.RolPrivado' => 'N'])
                ->cache(function ($q) {
                    $tmp = $q->where();
                    $tmp =array('sql' => $tmp->sql(), 'params' => $tmp->valueBinder()->bindings());
                    return $q->repository()->alias() . '-' . md5(serialize($tmp));
                }, 'config_cache_sfworkers');
            $workers_fichas = array();
            foreach ($sf_workers as $sf_worker) {
                $workers_fichas[] = $sf_worker->ficha;
            }
            $sf_workers_cargos_personal = $sfWorkerCargosPersonal->find('list')
                ->where(['SfWorkerCargosPersonal.ficha IN' => $workers_fichas, 'SfWorkerCargosPersonal.vigHasta' => '99991201'])
                ->cache(function ($q) {
                    $tmp = $q->where();
                    $tmp =array('sql' => $tmp->sql(), 'params' => $tmp->valueBinder()->bindings());
                    return $q->repository()->alias() . '-' . md5(serialize($tmp));
                }, 'config_cache_sfworkers')
                ->toArray();
            $sf_workers_cargos = $sfWorkerCargos->find('list')
                ->where(['SfWorkerCargos.CarCod IN' => $sf_workers_cargos_personal])
                ->cache(function ($q) {
                    $tmp = $q->where();
                    $tmp =array('sql' => $tmp->sql(), 'params' => $tmp->valueBinder()->bindings());
                    return $q->repository()->alias() . '-' . md5(serialize($tmp));
                }, 'config_cache_sfworkers')
                ->toArray();
            $workers_data = array();
            foreach ($sf_workers->toArray() as $key => $sf_worker) {
                $worker_id = $this->find('all', ['conditions' => ['Workers.softland_id' => $sf_worker->ficha]])->first();
                if (is_null($worker_id)) {
                    $new_worker = $this->newEntity();
                    $new_worker->softland_id = $sf_worker->ficha;
                    if ($this->save($new_worker)) {
                        $workers_data[$new_worker->id]['ficha'] = $sf_worker->ficha;
                        $workers_data[$new_worker->id]['nombres'] = $sf_worker->nombres;
                        $workers_data[$new_worker->id]['appaterno'] = $sf_worker->appaterno;
                        $workers_data[$new_worker->id]['apmaterno'] = $sf_worker->apmaterno;
                        $workers_data[$new_worker->id]['rut'] = $sf_worker->rut;
                        $workers_data[$new_worker->id]['Email'] = $sf_worker->Email;
                        $workers_data[$new_worker->id]['direccion'] = $sf_worker->direccion;
                        $workers_data[$new_worker->id]['telefono1'] = $sf_worker->telefono1;
                        $workers_data[$new_worker->id]['fechaNacimient'] = $sf_worker->fechaNacimient;
                        $workers_data[$new_worker->id]['fechaIngreso'] = $sf_worker->fechaIngreso;
                        $workers_data[$new_worker->id]['Cargo']['cod_cargo'] = $sf_workers_cargos_personal[$sf_worker->ficha];
                        $workers_data[$new_worker->id]['Cargo']['nombre_cargo'] = $sf_workers_cargos[$workers_data[$new_worker->id]['Cargo']['cod_cargo']];
                    }
                } else {
                    $workers_data[$worker_id->id]['ficha'] = $sf_worker->ficha;
                    $workers_data[$worker_id->id]['nombres'] = $sf_worker->nombres;
                    $workers_data[$worker_id->id]['appaterno'] = $sf_worker->appaterno;
                    $workers_data[$worker_id->id]['apmaterno'] = $sf_worker->apmaterno;
                    $workers_data[$worker_id->id]['rut'] = $sf_worker->rut;
                    $workers_data[$worker_id->id]['Email'] = $sf_worker->Email;
                    $workers_data[$worker_id->id]['direccion'] = $sf_worker->direccion;
                    $workers_data[$worker_id->id]['telefono1'] = $sf_worker->telefono1;
                    $workers_data[$worker_id->id]['fechaNacimient'] = $sf_worker->fechaNacimient;
                    $workers_data[$worker_id->id]['fechaIngreso'] = $sf_worker->fechaIngreso;
                    $workers_data[$worker_id->id]['Cargo']['cod_cargo'] = $sf_workers_cargos_personal[$sf_worker->ficha];
                    $workers_data[$worker_id->id]['Cargo']['nombre_cargo'] = $sf_workers_cargos[$workers_data[$worker_id->id]['Cargo']['cod_cargo']];
                }
            }
            return($workers_data);
        } else {
            return null;
        }
    }


    /**
     * Obtiene todos los datos de las ultimas tres liquidaciones de sueldo desde softland
     * @param  int $building_id   identificador del trabajador id softland
     * @return array              información ultimas 3 remuneraciones registradas en softland
     * @author Omar Sepúlveda <omar.sepulveda@ideauno.cl>
     */
    public function getLastRentaInfoByWorkerId($ficha, $codVariable="P090", $month=false)
    {
        if (!empty($ficha) && $ficha != null) {
            //Cargar siempre modelo de Obras de Softland
            $sfBuildings = TableRegistry::get('SfBuildings');
            $sfWorkers = TableRegistry::get('SfWorkers');
            $sfWorkerBuildings = TableRegistry::get('SfWorkerBuildings');
            $sfWorkerCargosPersonal = TableRegistry::get('SfWorkerCargosPersonal');
            $sfWorkerCargos = TableRegistry::get('SfWorkerCargos');
            $sfRentaCajasWorkers = TableRegistry::get('SfRentaCajasWorkers');
            $sfRentaCajas = TableRegistry::get('SfRentaCajas');
            $sfRentaAfpPreviReds = TableRegistry::get('SfRentaAfpPreviReds');
            $sfRentaIsapres = TableRegistry::get('SfRentaIsapres');
            $sfRentaIsapresWorkers = TableRegistry::get('SfRentaIsapresWorkers');
            $sfRentaAfps = TableRegistry::get('SfRentaAfps');
            $sfRentaAfpsWorkers = TableRegistry::get('SfRentaAfpsWorkers');
            $sfRentaVariables = TableRegistry::get('SfRentaVariables');
            $sfRentaVariablesWorkers = TableRegistry::get('SfRentaVariablesWorkers');
            $sfWorkerCertificadoRenta = TableRegistry::get('SfWorkerCertificadoRenta');
            $sf_workers = $sfWorkers->find('all')
                ->where(['SfWorkers.ficha' => $ficha, 'SfWorkers.RolPrivado' => 'N']);
                //->where(['SfWorkers.ficha' => $ficha, 'SfWorkers.fechaFiniquito' => '99991231', 'SfWorkers.RolPrivado' => 'N']);
            $workers_fichas = array();
            foreach ($sf_workers as $sf_worker) {
                $workers_fichas[] = $sf_worker->ficha;
            }
            $conditions1=['SfRentaVariablesWorkers.ficha IN' => $workers_fichas,
                 'SfRentaVariablesWorkers.codVariable' => $codVariable];
            if($month!=false){
                $conditions1['SfRentaVariablesWorkers.mes'] = $month;
            }
            //busco todos los pagos al trabajador, las fechas las estan guardando en distintos formatos asi q manualmente hay q armar el array para poder compararlas correctamente.
            $worker_payments = $sfRentaVariablesWorkers->find('all')
                ->where($conditions1)
                 ->cache(function ($q) {
                    $tmp = $q->where();
                    $tmp =array('sql' => $tmp->sql(), 'params' => $tmp->valueBinder()->bindings());
                    return $q->repository()->alias() . '-' . md5(serialize($tmp));
                }, 'config_cache_sfworkers')->toArray();
            if(empty($worker_payments)){
                return null;
            }
            //armo array con todas las fechas mismo formato
            foreach($worker_payments as $wp) {
                $tmp_valor = Time::parse(str_replace('/', '-', $wp->valor));
                $tmp_dates[$wp->mes] = $tmp_valor->format('d-m-Y');
            }
            uasort($tmp_dates, function($a1, $a2) {
               $value1 = strtotime($a1);
               $value2 = strtotime($a2);
               return $value1 - $value2;
            });
            // pr($tmp_dates);
            //array con key = mes, valor = fecha correspondiente al pago
            $last_3_payments = array_slice($tmp_dates,-3, 3, true);

            $worker_last_payments = $sfRentaVariablesWorkers->find('all')
                ->where(['SfRentaVariablesWorkers.ficha IN' => $workers_fichas,
                 'SfRentaVariablesWorkers.mes IN' => array_keys($last_3_payments)])->cache(function ($q) {
                    $tmp = $q->where();
                    $tmp =array('sql' => $tmp->sql(), 'params' => $tmp->valueBinder()->bindings());
                    return $q->repository()->alias() . '-' . md5(serialize($tmp));
                }, 'config_cache_sfworkers')->toArray();
            $sf_renta_variables = $sfRentaVariables->find('list')
                ->select(['codVariable', 'descripcion'])
                ->where(['SfRentaVariables.fechaHasta' => '21001231'])->cache(function ($q) {
                    $tmp = $q->where();
                    $tmp =array('sql' => $tmp->sql(), 'params' => $tmp->valueBinder()->bindings());
                    return $q->repository()->alias() . '-' . md5(serialize($tmp));
                }, 'config_cache_sfworkers')->toArray();
            $variables_workers = array();
            foreach ($worker_last_payments as $renta_variable_worker) {
                $variables_workers[$renta_variable_worker['mes']][$renta_variable_worker['codVariable']]['descripcion'] = $sf_renta_variables[$renta_variable_worker['codVariable']];
                $variables_workers[$renta_variable_worker['mes']][$renta_variable_worker['codVariable']]['mes'] = $renta_variable_worker['mes'];
                $variables_workers[$renta_variable_worker['mes']][$renta_variable_worker['codVariable']]['valor'] = $renta_variable_worker['valor'];
            }
            return($variables_workers);
        } else {
            return null;
        }
    }

    public function getTotalBonusAndDeals($worker_id, $month){
        $total['bonos'] = 0;
        $total['tratos'] = 0;
        $bonuses = $this->Bonuses->find('all', [
            'conditions' => [
                'Bonuses.state' => 'Aprobado',
                'Bonuses.worker_id' => $worker_id,
                'MONTH(Bonuses.start_date)' => $month,
            ]
        ]);
        $deals = $this->Deals->find('all', [
            'conditions' => [
                'Deals.state' => 'Aprobado',
                'Deals.worker_id' => $worker_id,
                'MONTH(Deals.start_date)' => $month,
            ]
        ]);
        if(!empty($bonuses)){
            foreach($bonuses AS $bonus){
                $total['bonos'] += $bonus->amount;
            }
        }
        if(!empty($deals)){
            foreach($deals AS $deal){
                $total['tratos'] += $deal->amount;
            }
        }
        return $total;
    }

    public function getAssistsApprovalByMonthAndWorkerId($month, $year, $worker_id, $day_cut, $fechaIngreso=null, $days_month=30, $isPrev=false){
        // si isPrev==true -> Se debe buscar los mayores al día hasta 30, sino los menores
        // pr('Obteniendo mes: '.$month);
        $from = 1;
        $to = $day_cut;
        if($isPrev==true){
            $from=$day_cut;
            $to=31;
        }
        $c=1;
        $return['asistencias']=0;
        $return['faltas']=0;
        $return['horas_extras']=0;
        $return['horas_atrasos']=0;
        $tableApprovals = TableRegistry::get('Approvals');
        // pr($fechaIngreso);
        $ndm=$days_month;
        for($i=1;$i<=$days_month;$i++){
            if(date('n', strtotime($fechaIngreso)).'-'.$year == $month.'-'.$year && $i<=date('d', strtotime($fechaIngreso))){
                $ndm--;
            }
        }
        if($isPrev==true){
            for($i=$day_cut;$i<=$to;$i++){
                if(date('n', strtotime($fechaIngreso)).'-'.$year == ($month-1).'-'.$year){
                    if($i<=date('d', strtotime($fechaIngreso))){
                        $ndm--;
                    }
                }elseif(date('n', strtotime($fechaIngreso)).'-'.$year == $month.'-'.$year){
                    $ndm--;
                }
            }
        }
        $days_month = $ndm;
        $assists = $this->Assists->find('all', [
            'conditions' => [
                'Assists.worker_id' => $worker_id,
                'MONTH(assistance_date)' => $month
            ],
            'contain' => [
                'AssistTypes'
            ]
        ])->toArray();
        if(!empty($assists)){
            foreach($assists AS $assist){
                $validaApprovals = $tableApprovals->find('all')
                    ->where([
                        'model' => 'Assists',
                        'model_id' => $assist->budget_id
                    ])
                    ->toArray();
                if(empty($validaApprovals)){
                    continue;
                }
                if(!empty($assist->assist_types) && $c >=$from && $c <= $to){
                    $return['horas_extras']+=$assist->overtime;
                    $return['horas_atrasos']+=$assist->delay;
                    // Se valida si el trabajador asistió todo el día (si tiene solo un assist_types quiere decir que asistió)
                    if(count($assist->assist_types)==1){
                        // Si el registro es "Asistencia" quiere decir que vino, sino quiere decir que no vino xd
                        if($assist->assist_types[0]->id ==1){
                            $return['asistencias']=$return['asistencias']+1;
                            $return['asistencia_dias'][$assist->assistance_date->format('Ymd')]=$assist->assistance_date->format('Y-m-d');
                        }else{
                            if(!esFeriado($assist->assistance_date->format('Y-m-d'))){
                                $return['faltas']=$return['faltas']+1;
                                $return['faltas_dias'][$assist->assistance_date->format('Ymd')]['date']=$assist->assistance_date->format('Y-m-d');
                                $return['faltas_dias'][$assist->assistance_date->format('Ymd')]['initials']=$assist->assist_types[0]->initials;
                                $return['faltas_dias'][$assist->assistance_date->format('Ymd')]['background_color']=$assist->assist_types[0]->background_color;
                            }
                        }
                    }else{ // En caso que hayan dos, quiere decir que el trabajador asistió x horas o no asistió
                        // Validar que el trabajador no tenga asistencia de 9 horas
                        if($assist->assist_types[0]->id !=1 || $assist->assist_types[1]->id != 1){
                            if(!esFeriado($assist->assistance_date->format('Y-m-d'))){
                                $return['faltas']=$return['faltas']+1;
                                $return['faltas_dias'][$assist->assistance_date->format('Ymd')]['date']=$assist->assistance_date->format('Y-m-d');
                                $return['faltas_dias'][$assist->assistance_date->format('Ymd')]['initials']=$assist->assist_types[1]->initials;
                                $return['faltas_dias'][$assist->assistance_date->format('Ymd')]['background_color']=$assist->assist_types[1]->background_color;
                            }
                        }
                    }
                    $c++;
                }
            }
        }
        $return['asistencias'] = $days_month-$return['faltas'];
        return $return;
    }

    public function getDctoAfp($worker_id){
        $tableAfpWorkers = TableRegistry::get('SfRentaAfpsWorkers');
        $tableAfp = TableRegistry::get('SfRentaAfps');
        $return['porcentaje'] = 0;
        $return['nombre'] = "";
        $return['porcentaje_afp'] = 0;
        $return['porcentaje_adicional'] = 0 ;
        //Buscar ficha del trabajador
        $afp_worker=$tableAfpWorkers->find('all', [
            'conditions' => [
                'ficha' => $worker_id
            ]
        ])->first();
        if(!empty($afp_worker)){
            $afp = $tableAfp->find('all', [
                'conditions' => [
                    'CodAFP' => $afp_worker->codAFP
                ]
            ])->first();
            $return['porcentaje'] = $afp->porAFP+$afp->porAdicSegSinSIS;
            $return['nombre'] = $afp->nombre;
            $return['porcentaje_afp'] = $afp->porAFP;
            $return['porcentaje_adicional'] = $afp->porAdicSegSinSIS;
        }
        return $return;
    }


    public function getDctoSalud($worker_id, $month){
        $tableIsapreWorkers = TableRegistry::get('SfRentaIsapresWorkers');
        $return['isapre_valor_uf']=0;
        $return['isapre_uf']=0;
        $return['isapre_monto']=0;
        $return['isapre_nombre']="FONASA";
        //Buscar ficha del trabajador
        $isapre_worker=$tableIsapreWorkers->find('all', [
            'conditions' => [
                'ficha' => $worker_id
            ]
        ])->first();
        if(!empty($isapre_worker)){
            $query_isapre_uf = "
                SELECT
                    campo$month AS isapre_uf,
                    (SELECT valor
                        FROM softland.sw_constvalor AS cv
                        WHERE cv.codConst LIKE 'c001'
                        AND cv.mes LIKE '$month'
                    ) AS isapre_valor_uf,
                    campo$month*(SELECT valor
                        FROM softland.sw_constvalor AS cv
                        WHERE cv.codConst LIKE 'c001'
                        AND cv.mes LIKE '$month'
                    ) AS isapre_monto,
                    sw_isapre.nombre AS isapre_nombre
                FROM softland.sw_isaper5 AS isap
                INNER JOIN softland.sw_isapreper ON softland.sw_isapreper.ficha = isap.ficha
                INNER JOIN softland.sw_isapre ON softland.sw_isapre.codIsapre = softland.sw_isapreper.CodIsapre
                WHERE isap.ficha LIKE '$worker_id'
                AND softland.sw_isapreper.vigHasta = {ts '9999-12-01 00:00:00.000'}
            ";
            $rowDcto = $tableIsapreWorkers->connection()->execute($query_isapre_uf)->fetchAll('assoc');
            if(!empty($rowDcto)){
                $return = array_shift($rowDcto);
            }
        }
        return $return;
    }

    public function getSueldoMinimo($month){
        $return = 0;
        if($month){
            // Se trae cualquier registro de tabla, para hacer la query, si me dió flojera crear un modelo nuevo xd
            $tableIsapreWorkers = TableRegistry::get('SfRentaIsapresWorkers');
            // obtener de la tabla sw_constvalor el c006 que corresponde al sueldo mínimo del mes
            $query = "
                SELECT valor
                FROM softland.sw_constvalor
                WHERE codConst = 'c006'
                AND mes = '$month'
            ";
            $result = $tableIsapreWorkers->connection()->execute($query)->fetchAll('assoc');
            if(!empty($result)){
                $return = array_shift($result)['valor'];
            }
        }
        return $return;
    }


    /*Función para generar los resultados de búsqueda para la remuneración de un trabajador*/
    public function generateSearchResult($data, $worker, $worker_sl, $assists, $ingresosExtra, $infoPago, $addAllFields=false){
        // 1. DÍAS TRABAJADOS (contador de días trabajados: ej: 30) - ok
        // 2. SUELDO BASE (se saca de softland) - ok
        // 3. (+) SUELDO MES (DÍAS TRABAJADOS * SUELDO BASE / 30) - ok
        // 4. (+) BONOS - ok
        // 5. (+) TRATOS - ok
        // 7. A = SUM(+)
        // 6. Gratificación. Si A > (98.958*4) -> Gratificacion = 98.958 sino Gratificacion = A*0,25
        // 7. (++) TOTAL IMPONIBLE = A + Gratificación
        // 8. Asignación Movilización (softland)
        // 9. (++) MOVILIZACIÓN = Asignación Movilización / 30 * DÍAS TRABAJADOS
        // 10. Asignación Colación (softland)
        // 11. (++) COLACIÓN = Asignación Colación / 30 * DÍAS TRABAJADOS
        // 12. TOTAL NO IMPONIBE (MOVILIZACIÓN + COLACIÓN)
        // 13. TOTAL HABERES = TOTAL NO IMPONIBLE + TOTAL IMPONIBLE
        // 14. (+++) DESCUENTO AFP
        // 15. (+++) DESCUENTO ISAPRE
        // 16. TOTAL DESCUENTO = SUM(+++)
        // 17. ALCANCE LÍQUIDO = TOTAL HABERES - TOTAL DESCUENTO
        $worker_id = $worker->id;
        $search_results=[];
        $month = $data['month'];
        $sb=0;
        $days_month=30;
        $gratificacion=98958;
        $he=0;

        // 1
        $search_results['pres']=['title' => 'DÍAS TRABAJADOS (DT)','description' => 'Días Trabajados (DT)', 'value' => $assists['asistencias']];
        // 2
        $search_results['sb']=['title' => 'SUELDO BASE','description' => 'Sueldo BASE (Obtenido de último pago en Sofland)','value' => $sb];
        // 3
        $search_results['m_pres']=['title' => 'MONTO TOTAL DT','description' => 'MONTO TOTAL DÍAS TRABAJADOS', 'value' => 0];
        // 4
        $search_results['bonos']=['title' => 'BONOS','description' => 'Bonos ingresados en CPO','value' => moneda($ingresosExtra['bonos'])];
        // 5
        $search_results['tratos']=['title' => 'TRATOS','description' => 'Tratos ingresados en CPO','value' => moneda($ingresosExtra['tratos'])];

        // En caso que solicite información anterior se deben buscar las faltas, obtener el valor diario y multiplicarlo por la cantidad de faltas
        if(isset($data['day_cut_prev']) && $data['day_cut_prev']!=""){
            if(isset($infoPago[$month-1])){
                $wp_prev =$infoPago[$month-1];
                if((isset($wp_prev['H058']['valor']))){
                    $assists_prev = $this->getAssistsApprovalByMonthAndWorkerId($month-1, $worker_id, $data['day_cut_prev'], $worker_sl['fechaIngreso']->format('Y-m-d'), 30, true);
                    $descuento = $assists_prev['faltas'] * $wp_prev['H058']['valor'];
                    $search_results['prev_faltas']=['title' => 'DÍAS AUSENCIAS MES ANTERIOR','description' => 'DÍAS AUSENCIAS MES ANTERIOR','value' => $assists_prev['faltas']];
                    $assists['asistencias'] -= $assists_prev['faltas'];
                    $search_results['pres']['value'] = $assists['asistencias'];
                    $search_results['prev_vd']=['title' => 'VALOR DIARIO MES ANTERIOR','description' => 'VALOR DIARIO MES ANTERIOR','value' => moneda($wp_prev['H058']['valor'])];
                    $search_results['prev_total_ausencia']=['title' => 'MONTO DÍAS AUSENCIAS MES ANTERIOR','description' => 'VALOR DIARIO MES ANTERIOR * DÍAS AUSENCIAS MES ANTERIOR','value' => moneda($descuento)];
                }
            }
        }
        //Parche peeeenca
        if(isset($addAllFields) && !isset($search_results['prev_total_ausencia'])){
            $search_results['prev_faltas']=['title' => 'DÍAS AUSENCIAS MES ANTERIOR','description' => 'DÍAS AUSENCIAS MES ANTERIOR','value' => 0];
            $search_results['prev_vd']=['title' => 'VALOR DIARIO MES ANTERIOR','description' => 'VALOR DIARIO MES ANTERIOR','value' => 0];
            $search_results['prev_total_ausencia']=['title' => 'MONTO DÍAS AUSENCIAS MES ANTERIOR','description' => 'VALOR DIARIO MES ANTERIOR * DÍAS AUSENCIAS MES ANTERIOR','value' => 0];
        }

        if(!empty($infoPago)){
            $wp = (isset($infoPago[$month]))?$infoPago[$month]:end($infoPago);
            $sb = $wp['H001']['valor'];
            $valor_dia=$sb/$days_month;
            $movilizacion=(isset($wp['P086']['valor']))?$wp['P086']['valor']:54400;
            $colacion=(isset($wp['P087']['valor']))?$wp['P087']['valor']:60000;
            $total_movilizacion=($movilizacion/30)*$assists['asistencias'];
            $total_colacion=($colacion/30)*$assists['asistencias'];
            $total_no_imponible=$total_colacion+$total_movilizacion;
            $search_results['sb']['value'] = moneda($sb);
            $search_results['m_pres']['value'] = moneda($valor_dia * $assists['asistencias']);

            $sb_imponible_sin_gratificacion = round($valor_dia * $assists['asistencias']) + $he + $ingresosExtra['bonos']+$ingresosExtra['tratos'];

            // 6
            // $search_results['sum1']=['title' => 'Suma 1','description' => 'Nose','value' => moneda($sb_imponible_sin_gratificacion)];

            // 7
            if($sb_imponible_sin_gratificacion < ($gratificacion*4)){
                $gratificacion = round($sb_imponible_sin_gratificacion*0.25);
            }
            // 8
            $search_results['gratificacion']=['title' => 'GRATIFICACIÓN LEGAL','description' => 'GRATIFICACIÓN LEGAL','value' => moneda($gratificacion)];
            // 9
            $sb_imponible = $sb_imponible_sin_gratificacion + $gratificacion;
            $search_results['hi']=['title' => 'TOTAL HABERES IMPONIBLES','description' => 'TOTAL HABERES IMPONIBLES (Valor Diario * Días Trabajados+Bonos+Tratos)','value' => moneda($sb_imponible)];
            // 10
            $search_results['mov']=['title' => 'MOVILIZACIÓN','description' => 'MOVILIZACIÓN', 'value' => moneda($total_movilizacion)];
            // 11
            $search_results['col']=['title' => 'COLACIÓN','description' => 'COLACIÓN', 'value' => moneda($total_colacion)];
            // 12
            $search_results['tni']=['title' => 'TOTAL NO IMPONIBLE','description' => 'TOTAL NO IMPONIBE (MOVILIZACIÓN + COLACIÓN)', 'value' => moneda($total_no_imponible)];
            // 13
            $total_haberes = $sb_imponible+$total_no_imponible;
            $search_results['total_haberes']=['title' => 'TOTAL HABERES','description' => 'TOTAL NO IMPONIBE + TOTAL IMPONIBLE', 'value' => moneda($total_haberes)];

            // Obtener porcentaje descuento AFP
            $afp = $this->getDctoAfp($data['worker']['ficha']);
            $dcto_afp['porcentaje'] = $afp['porcentaje'];
            $dcto_afp['monto'] = round(($sb_imponible*$dcto_afp['porcentaje'])/100);
            $search_results['afp']=['title' => 'DESCUENTO AFP','description' => 'DESCUENTO AFP ('.$dcto_afp['porcentaje'].'%)','value' => moneda($dcto_afp['monto'])];

            // Obtener porcentaje descuento Isapre. Se obtiene porcentaje isapre del último registro ((D004*100)/h030)
            $dcto_isapre['porcentaje'] = 7;
            $dcto_isapre['monto'] = round(($sb_imponible*$dcto_isapre['porcentaje'])/100);
            $search_results['salud']=['title' => 'DESCUENTO SALUD','description' => 'DESCUENTO SALUD ('.$dcto_isapre['porcentaje'].'%)','value' => moneda($dcto_isapre['monto'])];
            $total_descuento = $dcto_isapre['monto'] + $dcto_afp['monto'];

            $search_results['total_descuento']=['title' => 'TOTAL DESCUENTO','description' => 'TOTAL DESCUENTO (SALUD+AFP)', 'value' => moneda($total_descuento)];
            $liquido = $total_haberes-$total_descuento;
            $search_results['liquido']=['title' => 'ALCANCE LÍQUIDO','description' => 'ALCANCE LÍQUIDO','value' => moneda($liquido)];

        }


        return $search_results;
    }


    public function getImptoRentaByAmount($amount, $diario=false){
        $impto=0;
        if($amount){
            if(!$diario){
                $amount = $amount/30;
            }
            $sf_ir_obj = TableRegistry::get('SfImptoRentas');
            $tramos = $sf_ir_obj
                ->find('all')
                ->where([
                    'CotaInf <' => $amount,
                    'CotaSup >' => $amount,
                ])
                ->first()
                ->toArray();
            if(!empty($tramos)){
                $zi = $tramos['Zi']*30;
                $impto = (($amount*30)*$tramos['P17i'])-$zi;
            }
        }
        return $impto;
    }

/**
 * Obtiene los topes máximos en base al mes y a las variables de remuneración configuradas en bootstrap
 * @param  int $month                 Mes para obtención
 * @return array                        Arreglo topes maximos para salud, afp y afc
 * @author Piccolo Daimaku <piocoro.daimaku@ideauno.cl>
 */
    public function getTopesMaximosByMonth($month){
        $topes = [
            'salud'=>0,
            'afp'=>0,
            'afc'=>0,
        ];
        if($month){
            $variables_reports = \Cake\Core\Configure::read('remuneraciones');
            // Obtener valor UF, se trae el primer registro de tabla que pille por ahí xd es para hacer una consulta a softland
            $tableIsapreWorkers = TableRegistry::get('SfRentaIsapresWorkers');
            $query = "
                SELECT valor
                FROM softland.sw_constvalor AS cv
                WHERE cv.codConst LIKE 'c001'
                AND cv.mes LIKE '$month'
            ";
            $query_res = $tableIsapreWorkers->connection()->execute($query)->fetchAll('assoc');
            if(!empty($query_res)){
                $valor_uf = array_shift($query_res)['valor'];
                /* Obtener tope máximo de salud*/
                $topes['salud'] = (($variables_reports['tope_maximo_imponible'] * $variables_reports['porcentaje_salud'])/100)*$valor_uf;
                $topes['afp'] = (($variables_reports['tope_maximo_imponible'] * $variables_reports['porcentaje_afp'])/100)*$valor_uf;
                $topes['afc'] = (($variables_reports['tope_maximo_imponible_afc'] * ($variables_reports['seguro_cesantia']*100))/100)*$valor_uf;
            }
        }
        return $topes;
    }



    public function getWorkersByList($softland_id, $workers){
        $return = [];
        if(!empty($workers)){
            $sfWorkers = TableRegistry::get('SfWorkers');
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
                    WHERE softland.sw_personal.ficha IN ($workers)
                    ORDER BY nombres;
                ";
                // con una sola consulta los tiempos bajan de 60 a 6 seg

                // cucho: se ejecuta la consulta
                $array_workers = $sfWorkers->connection()->execute($consulta)->fetchAll('assoc');
                // se mantiene la estructura original para no modificar la vista
                foreach ($array_workers as $key => $worker) {
                    $return[] = [
                        'ficha' => $worker['ficha'],
                        'nombres' => $worker['nombres'],
                        'appaterno' => $worker['appaterno'],
                        'apmaterno' => $worker['apmaterno'],
                        'rut' => ltrim($worker['rut'], '0'),
                        'Email' => $worker['Email'],
                        'direccion' => $worker['direccion'],
                        'telefono1' => $worker['telefono1'],
                        'fechaNacimient' => $worker['fechaNacimient'],
                        'fechaIngreso' => $worker['fechaIngreso'],
                        'Cargo' => [
                            'cod_cargo' => $worker['CarCod'],
                            'nombre_cargo' => $worker['CarNom'],
                        ],
                        'vigDesde' => $worker['vigDesde'],
                        'vigHasta' => $worker['vigHasta'],
                    ];
                }
        }
        return $return;
    }

}
