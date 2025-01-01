<?php
class UsuariosPerfilModel extends Connection {
    const TABLE = 'tb_usuarios_perfil';
    private $conn;
    private $newModel = array();

    function __construct() {
        $this->conn = new Connection();
    }

    //MODELAGEM DO BANCO
    private $fields = array(
        'id_perfil'=>array('type'=>'integer', 'requered'=>true, 'max'=>10, 'key'=>true, 'description'=>'ID_PERFIL'),
        'id_usuarios'=>array('type'=>'integer', 'requered'=>true, 'max'=>10, 'key'=>true, 'description'=>'ID_USUARIOS'),
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

                $arr[':'.toUpperCase($key).''] = $value['value'];
            }
        }

        return $arr;
    }    

    public function loadId($id_perfil, $id_usuarios) {
        try {
            $arr[':ID_PERFIL'] = $id_perfil;
            $arr[':ID_USUARIOS'] = $id_usuarios;
            
            $sql = "select p.*
                      from ".self::TABLE." p
                     where p.id_perfil = :ID_PERFIL
                       and p.id_usuarios = :ID_USUARIOS";
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

    public function loadGeralUsuarios($id_usuarios) {
        try {
            $arr = array(':ID_USUARIOS'=>$id_usuarios);
            
            $sql = "select p.*, pf.descricao as ds_perfil
                      from ".self::TABLE." p 
                      inner join tb_perfil pf on pf.id_perfil = p.id_perfil
                     where p.id_usuarios = :ID_USUARIOS";
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
    

    public function del($id_usuarios){
        try {
            $save = $this->conn->delete(self::TABLE, array(':ID_USUARIOS'=>$id_usuarios));
            return $save;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
?>