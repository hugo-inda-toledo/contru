<?php

namespace App\View\Cell;

use Cake\View\Cell;
use Cake\Core\Configure;

class WelcomeCell extends Cell
{
/**
* Función a ejecutar al mostrar la cel
* @author Matías Pardo <matias.pardo@ideauno.cl>
*/
public function display()
{
	// $this->loadModel('Users');
	// $user = $this->Users->get($this->request->session()->read('Auth.User.id'));
	// $configurations = $this->Users->Configurations->find()->where(['name' => 'bienvenida'])->first();

	// if (! $user->welcome) {
	// 	$this->set('welcome_mensaje', $configurations->value);
	// 	$user->welcome = '1';
	// 	$this->Users->save($user);
	// } else {
	// 	$this->set('welcome_mensaje', false);
	// }
}




}