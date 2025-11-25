<?php
class UnidadesMedidasModel extends Connection {
    const TABLE = 'tb_unidades_medidas';
    private $conn;
    private $newModel = array();

    function __construct() {
        $this->conn = new Connection();
    }

    //MODELAGEM DO BANCO
    private $fields = array(
        'id_unidades_medidas'=>array('type'=>'integer', 'requered'=>true, 'max'=>10, 'key'=>true, 'description'=>'ID'),
        'descricao'=>array('type'=>'string', 'requered'=>false, 'max'=>'100', 'default'=>'', 'key'=>false, 'description'=>'Descrição'),
        'status'=>array('type'=>'string', 'requered'=>false, 'max'=>'1', 'default'=>'A', 'key'=>false, 'description'=>'status'),
        'id_empresas'=>array('type'=>'interger', 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Empresa'),
    );
    
    private function setFields($arr) {
        if (count($arr) > 0) {
            foreach ($this->fields as $key => $value) {
                $this->newModel[$key] = $value;
                $this->newModel[$key]['value'] = (isset($arr[$key]) && !empty($arr[$key]) ? $arr[$key] : null);
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
            
            $sql = "select p.*
                      from ".self::TABLE." p
                     where p.id_unidades_medidas = :ID";
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

    public function loadAll(int $id_empresas, string $status = 'A') {
        try {
            $arr = [':EMP' => $id_empresas];
            $where = " WHERE id_empresas = :EMP ";

            if ($status !== '') {
                $where .= " AND status = :STATUS ";
                $arr[':STATUS'] = $status;
            }

            $sql = "
                SELECT
                    id_unidades_medidas,
                    descricao,
                    status,
                    id_empresas
                FROM tb_unidades_medidas
                $where
                ORDER BY descricao
            ";
            $res = $this->conn->select($sql, $arr);
            return isset($res[0]) ? $res : [];
        } catch (\Throwable $e) {
            return [];
        }
    }   


    public function add($arr) {
        try {
            $this->setFields($arr);
            $values = $this->getFields();
            if (isset($values[':ID_UNIDADES_MEDIDAS'])) {
                unset($values[':ID_UNIDADES_MEDIDAS']);
            }
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
                $w[':'.toUpperCase($key).''] = $value;
            }

            if(isset($values[':ID_UNIDADES_MEDIDAS'])) {
                unset($values[':ID_UNIDADES_MEDIDAS']);
            }

            $save = $this->conn->update(self::TABLE, $values, $w);
            return $save;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function del($id){
        try {
            $save = $this->conn->update(
                self::TABLE, 
                array(':STATUS'=>'D'), array(':ID_UNIDADES_MEDIDAS'=>$id)
            );
            return $save;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
?>