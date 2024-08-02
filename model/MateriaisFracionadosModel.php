<?php
class MateriaisFracionadosModel extends Connection {
    const TABLE = 'tb_materiais_fracionados';
    private $conn = false;
    private $newModel = array();

    function __construct() {
        $this->conn = new Connection();
    }

    //MODELAGEM DO BANCO
    private $fields = array(
        'id_materiais_fracionados'=>array('type'=>'integer', 'requered'=>true, 'max'=>10, 'key'=>true, 'description'=>'ID'),
        'qtd_fracionada'=>array('type'=>'double', 'requered'=>true, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Quantidade'),
        'dt_vencimento'=>array('type'=>'date', 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Data de Vencimento'),
        'status'=>array('type'=>'string', 'requered'=>false, 'max'=>'1', 'default'=>'A', 'key'=>false, 'description'=>'status'),
        'motivo_descarte'=>array('type'=>'string', 'requered'=>false, 'max'=>'1000', 'default'=>'', 'key'=>false, 'description'=>'Motivo do descarte'),
        'id_materiais'=>array('type'=>'integer', 'requered'=>true, 'max'=>10, 'key'=>false, 'description'=>'ID MATERIAL'),
        'id_embalagens'=>array('type'=>'integer', 'requered'=>false, 'max'=>'10', 'default'=>'N', 'key'=>false, 'description'=>'Selecionar uma embalagem'),
        'id_usuarios'=>array('type'=>'integer', 'requered'=>false, 'max'=>'10', 'default'=>'N', 'key'=>false, 'description'=>'Usuário'),
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

                if ($value['type']=='date' && !empty($value['value'])) {
                    $value['value'] = dt_banco($value['value']);
                }

                if ($value['type']=='double' && !empty($value['value'])) {
                    $value['value'] = numberFormatBanco($value['value']);
                }

                if (isset($value['key'])==true && $fgRemoveKey==true) {
                    unset($this->newModel[$key]);
                }

                $arr[':'.mb_strtoupper($key).''] = !empty($value['value']) ? $value['value'] : null;
                

            }
        }

        return $arr;
    } 
    
    private function getFieldsView($data) {

        if (!empty($data['dt_vencimento'])) {
            $data['dt_vencimento'] = dt_br($data['dt_vencimento']);
        }

        return $data;
    }

    public function loadId($id) {
        try {
            $arr[':ID'] = $id;
            
            $sql = "select p.*
                      from ".self::TABLE." p
                     where p.id_materiais_fracionados = :ID";
            $res = $this->conn->select($sql, $arr);
            
            if (isset($res[0])) {
                return $this->getFieldsView($res[0]);
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw $e->getMessage();
        }
    }

    public function loadIdMateriais($id_materiais) {
        try {
            $arr[':ID_MATERIAIS'] = $id_materiais;
            
            $sql = "select p.*
                      from ".self::TABLE." p
                     where p.id_materiais = :ID_MATERIAIS";
            $res = $this->conn->select($sql, $arr);
            
            if (isset($res[0])) {
                $arr = array();
                foreach ($res as $key => $value) {
                    $arr[] = $this->getFieldsView($value);
                }
                return $arr;
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
        if (isset($values[':ID_MATERIAIS_FRACIONADOS'])) {
            unset($values[':ID_MATERIAIS_FRACIONADOS']);
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
                $w[':'.mb_strtoupper($key).''] = $value;
            }

            if(isset($values[':ID_MATERIAIS_FRACIONADOS'])) {
                unset($values[':ID_MATERIAIS_FRACIONADOS']);
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
                array(':STATUS'=>'D'), array(':ID_MATERIAIS_FRACIONADOS'=>$id)
            );
            return $save;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
?>