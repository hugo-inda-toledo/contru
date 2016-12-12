<?php
// elementos estandares de la vista
$this->assign('title_text', __('Mi Cuenta'));
$this->assign('title_icon', 'user');
$buttons = array();
$buttons[] = ['title' => __('Cambiar mi contraseña'), 'class' => 'primary', 'icon' => 'key', 'link' => '/users/updatePassword'];
$buttons[] = ['title' => __('Editar mis datos'), 'class' => 'primary', 'icon' => 'pencil', 'link' => '/users/editUser'];
$buttons[] = ['title' => __('Salir'), 'class' => 'danger', 'icon' => 'exit', 'link' => '/users/logout'];
$this->set('buttons', $buttons);
?>