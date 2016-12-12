<?php
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\Network\Session;

class AgregardatosBehavior extends Behavior
{
	/**
	 * [beforeSave description]
	 * @param  Event  $event [description]
	 * @return [type]        [description]
	 * @author Carlos Vásquez <carlos.vasquez@ideauno.cl>
	 */
	public function beforeSave(Event $event)
	{
		$session = new Session();
		if ($session->read('Auth.User.id')) {
			if ($event->data['entity']->isNew()) {
				if ($event->data['entity']->accessible('user_created_id')) {
					$event->data['entity']->user_creator_id = $session->read('Auth.User.id');
				}
				if ($event->data['entity']->accessible('user_modified_id')) {
					$event->data['entity']->user_modifier_id = $session->read('Auth.User.id');
				}
			} else {
				if ($event->data['entity']->accessible('user_modified_id')) {
					$event->data['entity']->user_modifier_id = $session->read('Auth.User.id');
				}
			}
		}
	}


}