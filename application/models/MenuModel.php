<?php

class MenuModel extends CI_Model {
    public function __construct()
    {
        parent::__construct();
    }

    public function getMenu($id_user, $id_rol)
    {
        $headers = $this->db->query("SELECT * FROM menu_headers")->result_array();
        
        $sections = array();
        foreach ($headers as $key_h => $header) {
            $items = $this->db->query("SELECT * FROM menu WHERE header=$header[id] AND father IS NULL")->result_array();

            $menus = array();
            foreach ($items as $key_i => $item) {
                $children = $this->db->query("SELECT * FROM menu WHERE father=$item[id]")->result_array();

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

            if($menus){
                $header["items"] = $menus;

                array_push($sections, $header);
            }
        }

        return $sections;
    }

}

?>