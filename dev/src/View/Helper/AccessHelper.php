<?php
namespace App\View\Helper;

use Cake\View\Helper;

class AccessHelper extends Helper
{
    public $helpers = ['Html'];

    public function verifyAction($controller = null, $action = null)
    {
        $access = false;

        if($controller != null && $action != null)
        {
            $words = explode('_', $controller);
            

            if(count($words) > 1)
            {
                $new_controller = '';
                foreach($words as $key => $value)
                {
                    $new_controller .= ucwords($value);
                }

                foreach($this->request->session()->read('groups') as $group)
                {
                    foreach($group->permissions as $perm)
                    {
                        if($perm->controller == $new_controller && $perm->action == $action)
                        {
                            $access = true;
                            break;
                        }
                    }

                    if($access == true)
                    {
                        break;
                    }
                }
            }
            else
            {
                $controller = ucwords($controller);
                
                foreach($this->request->session()->read('groups') as $group)
                {
                    foreach($group->permissions as $perm)
                    {
                        if($perm->controller == $controller && $perm->action == $action)
                        {
                            $access = true;
                            break;
                        }
                    }

                    if($access == true)
                    {
                        break;
                    }
                }
            }

            
        }

        return $access;
    }

    public function verifyLevel($level = null)
    {
        $access = false;

        if($level != null)
        {
            foreach($this->request->session()->read('groups') as $group)
            {
                if($group->level >= $level)
                {
                    $access = true;
                    break;
                }
            }
        }

        return $access;
    }

    public function verifyAccessByKeyword($keyword = null)
    {
        $access = false;

        if($keyword != null)
        {
            foreach($this->request->session()->read('groups') as $group)
            {
                if($group->group_keyword == $keyword)
                {
                    $access = true;
                    break;
                }
            }
        }

        return $access;
    }

    public function getGroupsForLayout()
    {
        if(count($this->request->session()->read('groups')) > 1)
        {
            $tooltip = '';
            foreach($this->request->session()->read('groups') as $group)
            {
                $tooltip .= '- '.$group->name.'<br>';
            }

            return $this->Html->tag('span', count($this->request->session()->read('groups')).' Perfiles', ['data-toggle' => 'tooltip', 'data-placement' => 'left', 'data-html' => 'true', 'title' => $tooltip]);
        }
        else
        {
            foreach($this->request->session()->read('groups') as $group)
            {
                $name = $group->name;
            }

            return $this->Html->tag('span', $name);
        }
    }
}
