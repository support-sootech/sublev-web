<?php
class EmbalagensModel extends Connection {
    const TABLE = 'tb_embalagens';
    private $conn;
    private $newModel = array();

    function __construct() {
        $this->conn = new Connection();
    }

    //MODELAGEM DO BANCO
    private $fields = array(
        'id_embalagens'=>array('type'=>'integer', 'requered'=>true, 'max'=>10, 'key'=>true, 'description'=>'ID'),
        'descricao'=>array('type'=>'string', 'requered'=>false, 'max'=>'100', 'default'=>'', 'key'=>false, 'description'=>'Descrição'),
        'qtd_maxima'=>array('type'=>'interger', 'requered'=>true, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Qtd. máxima'),
        'qtd_minima'=>array('type'=>'interger', 'requered'=>true, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Qtd. mínima'),
        'id_embalagens_tipos'=>array('type'=>'integer', 'requered'=>true, 'max'=>10, 'key'=>true, 'description'=>'Tipo da embalagens'),
        'status'=>array('type'=>'string', 'requered'=>false, 'max'=>'1', 'default'=>'A', 'key'=>false, 'description'=>'status'),
        'id_empresas'=>array('type'=>'interger', 'requered'=>true, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Empresa'),
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

                $arr[':'.toUpperCase($key).''] = $value['value'];
            }
        }

        return $arr;
    }    

    public function loadId($id) {
        try {
            $arr[':ID'] = $id;
            
            $sql = "select p.*, et.descricao as ds_embalagens_tipos
                      from ".self::TABLE." p
                      inner join tb_embalagens_tipos et on et.id_embalagens_tipos = p.id_embalagens_tipos
                     where p.id_embalagens = :ID";
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

    public function loadAll($id_empresas) {
        try {
            $arr = array();
            $and = '';

            $arr[':STATUS'] = 'D';
            $and .= " and p.status != :STATUS";

            $arr[':ID_EMPRESAS'] = $id_empresas;
            $and .= " and p.id_empresas = :ID_EMPRESAS";
            
            $sql = "select p.*, et.descricao as ds_embalagens_tipos
                      from ".self::TABLE." p
                      inner join tb_embalagens_tipos et on et.id_embalagens_tipos = p.id_embalagens_tipos
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
        $this->setFields($arr);
            $values = $this->getFields();
            if (isset($values[':ID_EMBALAGENS'])) {
                unset($values[':ID_EMBALAGENS']);
            }
            $save = $this->conn->insert(self::TABLE, $values);
            return $save;
        /*try {
            
        } catch (Exception $e) {
            throw $e->getMessage();
        }
        */
    }

    public function edit(Array $arr, Array $where){
        
        try {
            $this->setFields($arr);
            $values = $this->getFields();
            
            $w = array();
            foreach ($where as $key => $value) {
                $w[':'.toUpperCase($key).''] = $value;
            }

            if(isset($values[':ID_EMBALAGENS'])) {
                unset($values[':ID_EMBALAGENS']);
            }

            $save = $this->conn->update(self::TABLE, $values, $w);
            return $save;
        } catch (Exception $e) {
            throw $e->getMessage();
        }
    }

    public function del($id){
        try {
            $save = $this->conn->update(
                self::TABLE, 
                array(':STATUS'=>'D'), array(':ID_EMBALAGENS'=>$id)
            );
            return $save;
        } catch (Exception $e) {
            throw $e->getMessage();
        }
    }
}
?>