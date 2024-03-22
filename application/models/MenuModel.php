<?php

class MenuModel extends CI_Model {
    public function __construct()
    {
        parent::__construct();
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
    }

    public function checkAuth($path, $id_user, $id_rol){
        /* $item = $this->db->query("SELECT * FROM menu WHERE path = '$path'")->row(); */

        $item = $this->ch->query("SELECT * FROM ". $this->schema_cm .".menu WHERE path = '$path'")->row();

        $auth = false;
        if($item){
            $roles_menu = explode(',', $item->rol);
            $users_can_menu = explode(',', $item->users_can);
            $users_not_menu = explode(',', $item->users_not);

            if(in_array($id_rol, $roles_menu)){
                if(!in_array($id_user, $users_not_menu)){
                    $auth = true;
                }
            }elseif(in_array($id_user, $users_can_menu)){
                $auth = true;
            }
        }

        return $auth;
    }

    public function getMenu($id_user, $id_rol)
    {
        $subheader = array();

        /* $items = $this->db->query("SELECT * FROM menu WHERE father IS NULL ORDER BY posOrder")->result_array(); */

        $items = $this->ch->query("SELECT * FROM ". $this->schema_cm .".menu WHERE father IS NULL ORDER BY posOrder")->result_array();

        $menus = array();
        foreach ($items as $key_i => $item) {
            $children = $this->ch->query("SELECT * FROM ". $this->schema_cm .".menu WHERE father=$item[id] ORDER BY posOrder")->result_array();

            $childs = array();
            foreach ($children as $key_c => $child) {

                $roles_child = explode(',', $child['rol']);
                $users_can_child = explode(',', $child['users_can']);
                $users_not_child = explode(',', $child['users_not']);

                if(in_array($id_rol, $roles_child)){
                    if(!in_array($id_user, $users_not_child)){
                        array_push($childs, $child);
                    }
                }elseif(in_array($id_user, $users_can_child)){
                    array_push($childs, $child);
                }
            }

            //IF
            if($childs){
                $item["children"] = $childs;
            }

            $roles_menu = explode(',', $item['rol']);
            $users_can_menu = explode(',', $item['users_can']);
            $users_not_menu = explode(',', $item['users_not']);

            if(in_array($id_rol, $roles_menu)){
                if(!in_array($id_user, $users_not_menu)){
                    array_push($menus, $item);
                }
            }elseif(in_array($id_user, $users_can_menu)){
                array_push($menus, $item);
            }
        }

        $subheader['items'] = $menus;

        return array($subheader);
    }

}

?>