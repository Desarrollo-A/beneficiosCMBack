<?php

class MenuModel extends CI_Model {
    public function __construct()
    {
        $this->schema_cm = $this->config->item('schema_cm');
        $this->schema_ch = $this->config->item('schema_ch');
        $this->ch = $this->load->database('ch', TRUE);
        parent::__construct();
    }

    public function checkAuth($path, $id_user, $id_rol){

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

    public function getMenu($id_user, $id_rol, $permisos)
    {
    $subheader = array();

    $items = $this->ch->query("SELECT * FROM ". $this->schema_cm .".menu WHERE father IS NULL AND estatus = 1 ORDER BY posOrder")->result_array();

    $menus = array();
    foreach ($items as $key_i => $item) {
        // Obtiene el segundo nivel de elementos (submenú)
        $children = $this->ch->query("SELECT * FROM ". $this->schema_cm .".menu WHERE father=$item[id] AND estatus = 1 ORDER BY posOrder")->result_array();

        $childs = array();
        foreach ($children as $key_c => $child) {
            // Obtiene el tercer nivel de elementos
            $subchildren = $this->ch->query("SELECT * FROM ". $this->schema_cm .".menu WHERE subFather=$child[id] AND estatus = 1 ORDER BY posOrder")->result_array();

            $subchilds = array();
            foreach ($subchildren as $key_d => $subchild) {
                $roles_subchild = explode(',', $subchild['rol']);
                $users_can_subchild = explode(',', $subchild['users_can']);
                $users_not_subchild = explode(',', $subchild['users_not']);

                if (in_array($id_rol, $roles_subchild) || in_array($permisos, $roles_subchild)) {
                    if (!in_array($id_user, $users_not_subchild)) {
                        array_push($subchilds, $subchild);
                    }
                } elseif (in_array($id_user, $users_can_subchild)) {
                    array_push($subchilds, $subchild);
                }
            }

            // Asigna los submenús al elemento del segundo nivel
            if ($subchilds) {
                $child["children"] = $subchilds;
            }

            $roles_child = explode(',', $child['rol']);
            $users_can_child = explode(',', $child['users_can']);
            $users_not_child = explode(',', $child['users_not']);

            if (in_array($id_rol, $roles_child) || in_array($permisos, $roles_child)) {
                if (!in_array($id_user, $users_not_child)) {
                    array_push($childs, $child);
                }
            } elseif (in_array($id_user, $users_can_child)) {
                array_push($childs, $child);
            }
        }

        // Asigna los elementos del segundo nivel al elemento del primer nivel
        if ($childs) {
            $item["children"] = $childs;
        }

        $roles_menu = explode(',', $item['rol']);
        $users_can_menu = explode(',', $item['users_can']);
        $users_not_menu = explode(',', $item['users_not']);

        if (in_array($id_rol, $roles_menu) || in_array($permisos, $roles_menu)) {
            if (!in_array($id_user, $users_not_menu)) {
                array_push($menus, $item);
            }
        } elseif (in_array($id_user, $users_can_menu)) {
            array_push($menus, $item);
        }
    }

    $subheader['items'] = $menus;

    return array($subheader);

    }

}

?>