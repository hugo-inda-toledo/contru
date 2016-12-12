<?php
// elementos estandares de la vista
$this->assign('title_text', __('Directorio telefónico'));
$this->assign('title_icon', 'contacts-dialer');
$buttons = array();
$buttons[] = ['title' => __('Agregar contacto'), 'class' => 'primary', 'icon' => 'plus', 'link' => ''];
$this->set('buttons', $buttons);
?>

lista de procedimientos internos