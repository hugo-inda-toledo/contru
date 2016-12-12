<?php

namespace App\View\Cell;

use Cake\View\Cell;
use Cake\Cache\Cache;

class IndicatorsCell extends Cell
{

/**
 * [display description]
 * @return [type] [description]
 * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
 */
public function display()
{

}

/**
 * Santoral
 * @return [type] [description]
 * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
 */
public function santoral()
{
	// obtine los indicadores diarios y devuelve el santoral
	$this->set('santoral', $this->get_dialy_indicators()->santoral);
}

/**
 * Indicadores economicos, cambio + indicadores economicos
 * @return [type] [description]
 * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
 */
public function economicos()
{
	// obtine los indicadores diarios y devuelve el santoral
	$this->set('economicos', $this->get_dialy_indicators());
}


/**
 * Indicadores
 * @return [type] [description]
 * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
 */
public function indicadores()
{
	// obtine los indicadores diarios y devuelve el santoral
	$this->set('indicador', $this->get_dialy_indicators()->indicador);
}


/**
 * Moneda de cambio
 * @return [type] [description]
 * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
 */
public function cambio()
{
	// obtine los indicadores diarios y devuelve el santoral
	if (isset($this->get_dialy_indicators()->moneda))
	$this->set('cambio', $this->get_dialy_indicators()->moneda);
}


/**
 * Restriccion
 * @return [type] [description]
 * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
 */
public function restriccion()
{
	// obtine los indicadores diarios y devuelve el santoral
	$this->set('restriccion', $this->get_dialy_indicators()->restriccion);
}


/**
 * Clima
 * @return [type] [description]
 * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
 */
public function clima()
{
	// widget del tiempo de accuweather :)
}

/**
 * [get_dialy_indicators description]
 * @return [type] [description]
 * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
 */
private function get_dialy_indicators()
{
	// si no hay cache de inidicadores diarios
	if (($indicators = Cache::read('indicadores', 'config_cache_diario')) === false) {

		// obtiene los datos de los indicadores diarios
		$url = "http://indicadoresdeldia.cl/webservice/indicadores.xml";
		//$url = "http://indicadoresdeldia.cl/webservice/indicadores.json"; // a veces el json viene incompleto
		$indicators = file_get_contents($url);

		// escribe en el cache el resultado de la ulr
		Cache::write('indicadores', $indicators, 'config_cache_diario');
	} else {

		// hay cache, los indicadores son el cache
		$indicators = Cache::read('indicadores', 'config_cache_diario');
	}

	// retorna el objeto
	return simplexml_load_string($indicators);
	// return json_decode($indicators); // a veces el json viene incompleto
}


}