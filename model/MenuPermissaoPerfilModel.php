<?php
class MenuPermissaoPerfilModel extends Connection {
    const TABLE = 'tb_menu_permissao_perfil';
    private $conn = false;
    private $newModel = array();

    function __construct() {
        $this->conn = new Connection();
    }

    //MODELAGEM DO BANCO
    private $fields = array(
        'id_permissoes'=>array('type'=>'integer', 'requered'=>true, 'max'=>10, 'key'=>false, 'description'=>'ID Permissão'),
        'id_menu'=>array('type'=>'integer', 'requered'=>true, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'ID Menu'),
        'id_perfil'=>array('type'=>'integer', 'requered'=>true, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'ID Perfil'),
    );
    
    private function setFields($arr) {
        if (count($arr) > 0) {
            foreach ($this->fields as $key => $value) {
                $this->newModel[$key] = $value;
                $this->newModel[$key]['value'] = (isset($arr[$key]) ? $arr[$key] : '');
            }

        }
    }

    private function getFields($fgRemoveKey=true) {
        $arr = array();

        if (count($this->newModel) > 0) {
            foreach ($this->newModel as $key => $value) {

                $campo = (isset($value['description']) && !empty($value['description']) ? $value['description'] : $key);

                if ($value['requered']==true && empty($value['value']) && !$value['key']) {
                    throw new Exception('O campo '.$campo.' não pode ser vazio!');
                }

                if (isset($value['max']) && $value['max'] < strlen($value['value']) ) {
                    throw new Exception('O campo '.$campo.' deve conter no máximo '.$value['max'].' caracter(es)!');
                }

                /*
                if ($value['type']=='integer' && is_numeric($value['value'])) {
                    throw new Exception('O campo '.$campo.' deve ser do tipo numérico!');
                }
                */

                if (isset($value['key'])==true && $fgRemoveKey==true) {
                    unset($this->newModel[$key]);
                }

                $arr[':'.mb_strtoupper($key).''] = $value['value'];
            }
        }

        return $arr;
    }    

    public function loadIdMenuIdPermissoesIdPerfil($id_menu, $id_permissoes, $id_perfil) {
        try {
            $arr[':ID_MENU'] = $id_menu;
            $arr[':ID_PERMISSOES'] = $id_permissoes;
            $arr[':ID_PERFIL'] = $id_perfil;
            
            $sql = "select p.*
                      from ".self::TABLE." p
                     where p.id_permissoes = :ID_PERMISSOES
                       and p.id_menu = :ID_MENU
                       and p.id_perfil = :ID_PERFIL";
            $res = $this->conn->select($sql, $arr);
            
            if (isset($res[0])) {
                return $res[0];
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function loadAllIdMenu($id_menu) {
        try {
            $arr = array(':ID_MENU'=>$id_menu);
            $and = " and p.id_menu = :ID_MENU";
            
            $sql = "select p.*
                      from ".self::TABLE." p
                     where 1 = 1 
                       ".$and."";
            $res = $this->conn->select($sql, $arr);
            
            if (isset($res[0])) {
                return $res;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function loadAllIdPerfil($id_perfil) {
        try {
            $arr = array(':ID_PERFIL'=>$id_perfil);
            $and = " and p.id_perfil = :ID_PERFIL";
            
            $sql = "select p.*
                      from ".self::TABLE." p
                     where 1 = 1 
                       ".$and."";
            $res = $this->conn->select($sql, $arr);
            
            if (isset($res[0])) {
                return $res;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function add($arr) {
        try {
            $this->setFields($arr);
            $values = $this->getFields();
            $save = $this->conn->insert(self::TABLE, $values);
            return $save;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function edit(Array $arr, Array $where){
        
        try {
            $this->setFields($arr);
            $values = $this->getFields();
            
            $w = array();
            foreach ($where as $key => $value) {
                $w[':'.mb_strtoupper($key).''] = $value;
            }

            if(isset($values[':ID_PERMISSOES'])) {
                unset($values[':ID_PERMISSOES']);
            }

            $save = $this->conn->update(self::TABLE, $values, $w);
            return $save;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function del($id_perfil){
        try {
            $arr = $this->loadAllIdPerfil($id_perfil);
            if ($arr) {
                foreach ($arr as $key => $value) {
                    $save = $this->conn->delete(
                        self::TABLE, 
                        array(
                            ':ID_PERFIL'=>$id_perfil,
                            ':ID_MENU'=>$value['id_menu'],
                            ':ID_PERMISSOES'=>$value['id_permissoes']
                        ));
                }
            }

            return $save;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
?>