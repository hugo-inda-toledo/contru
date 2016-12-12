<?php

namespace App\View\Cell;

use Cake\View\Cell;
use Cake\Cache\Cache;

class PostsCell extends Cell
{

	/**
 * Retorna al cumpleanero del dia de hoy
 * @return [type] [description]
 * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
 * @todo
 */
public function avisos_clasificados()
{
	$this->loadModel('Posts');
	$posts = $this->Posts->find('all', [
		'conditions' => [],
		'order' => ['id' => 'asc'],
		'limit' => 2,
		'recursive' => -1
		])->toArray();

	$this->set(compact('posts'));
}


}
