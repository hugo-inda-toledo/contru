<?php

namespace App\View\Cell;

use Cake\View\Cell;

class UsuarioCell extends Cell
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
 * Ficha usuario chica
 * @return [type] [description]
 * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
 */
public function user_small_info($user_id)
{
	$this->loadModel('Users');
	$user = $this->Users->get($user_id);
	$this->set(compact('user'));
}


/**
 * Retorna al cumpleanero del dia de hoy
 * @return [type] [description]
 * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
 * @todo
 */
public function cumpleanero()
{
	$this->loadModel('Users');
	$users = $this->Users->find('all', [
		'conditions' => [],
		'order' => ['id' => 'asc'],
		'limit' => 2,
		'recursive' => -1
		])->toArray();

	$this->set(compact('users'));
}

/**
 * Retorna proximos usuarios cumpleaneros
 * @return [type] [description]
 * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
 * @todo
 */
public function proximos_cumpleanos()
{
	$this->loadModel('Users');
	$users = $this->Users->find('all', [
		'conditions' => [],
		'order' => ['id' => 'desc'],
		'limit' => 5,
		'recursive' => -1
		])->toArray();

	$this->set(compact('users'));
}





}