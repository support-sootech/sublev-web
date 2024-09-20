<?php
class SetorModel extends Connection {
    const TABLE = 'tb_setor';
    private $conn;
    private $newModel = array();

    function __construct() {
        $this->conn = new Connection();
    }

    //MODELAGEM DO BANCO
    private $fields = array(
        'id_setor'=>array('type'=>'integer', 'requered'=>true, 'max'=>10, 'key'=>true, 'description'=>'ID'),
        'nome'=>array('type'=>'string', 'requered'=>false, 'max'=>'100', 'default'=>'', 'key'=>false, 'description'=>'Nome do setor'),
        'status'=>array('type'=>'string', 'requered'=>false, 'max'=>'1', 'default'=>'A', 'key'=>false, 'description'=>'Status'),
        'id_empresas'=>array('type'=>'integer', 'requered'=>true, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Empresa'),
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

    public function loadId($id) {
        try {
            $arr[':ID'] = $id;
            
            $sql = "select x.*
                      from ".self::TABLE." x
                     where x.id_setor = :ID";
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

    public function loadAll($id_empresas='', $status='') {
        try {
            $arr = array();
            $and = '';

            if (empty($status)) {
                $and.= " and p.status != 'D'";
            } else {
                $and.= " and p.status = :STATUS";
                $arr[':STATUS'] = $status;
            }

            $and.= " and p.id_empresas = :ID_EMPRESAS";
            $arr[':ID_EMPRESAS'] = $id_empresas;
            
            $sql = "select p.*
                      from ".self::TABLE." p
                     where 1 = 1 
                       ".$and."";
            
            $res = $this->conn->select($sql, $arr);
            
            return isset($res[0]) ? $res : false;

        } catch (Exception $e) {
            throw $e->getMessage();
        }
    }

    public function add($arr) {
        try {
            $this->setFields($arr);
            $values = $this->getFields();
            if (isset($values[':ID_SETOR'])) {
                unset($values[':ID_SETOR']);
            }
            $save = $this->conn->insert(self::TABLE, $values);
            return $save;
        } catch (Exception $e) {
            throw $e->getMessage();
        }
    }

    public function edit(Array $arr, Array $where){
        
        $this->setFields($arr);
        $values = $this->getFields();
        
        $w = array();
        foreach ($where as $key => $value) {
            $w[':'.mb_strtoupper($key).''] = $value;
        }

        if(isset($values[':ID_SETOR'])) {
            unset($values[':ID_SETOR']);
        }

        $save = $this->conn->update(self::TABLE, $values, $w);
        return $save;
        
    }

    public function del($id){
        try {
            $save = $this->conn->update(self::TABLE, array(':STATUS'=>'D'), array(':ID_SETOR'=>$id));
            return $save;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
?>