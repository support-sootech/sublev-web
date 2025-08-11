<?php
class ProdutosModel extends Connection {
    const TABLE = 'tb_produtos';
    private $conn;
    private $newModel = array();

    function __construct() {
        $this->conn = new Connection();
    }

    //MODELAGEM DO BANCO
    private $fields = array(
        'id_produtos'=>array('type'=>'integer', 'requered'=>true, 'max'=>10, 'key'=>true, 'description'=>'ID'),
        'descricao'=>array('type'=>'string', 'requered'=>true, 'max'=>'100', 'default'=>'', 'key'=>false, 'description'=>'Descrição'),
        'codigo_barras'=>array('type'=>'string', 'requered'=>true, 'max'=>'50', 'default'=>'', 'key'=>false, 'description'=>'Código de Barras'),
        'dias_vencimento'=>array('type'=>'integer', 'requered'=>true, 'max'=>'2', 'default'=>'', 'key'=>false, 'description'=>'Qtd. de dias de vencimento'),
        'dias_vencimento_aberto'=>array('type'=>'integer', 'requered'=>true, 'max'=>'2', 'default'=>'', 'key'=>false, 'description'=>'Qtd. de dias de vencimento após aberto'),
        'status'=>array('type'=>'string', 'requered'=>false, 'max'=>'1', 'default'=>'A', 'key'=>false, 'description'=>'status'),
        'peso'=>array('type'=>'double', 'requered'=>true, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Peso'),
        'id_pessoas_fabricante'=>array('type'=>'integer', 'fk'=>true, 'requered'=>false, 'max'=>'10', 'default'=>null, 'key'=>false, 'description'=>'Informar o fabricante'),
        'id_materiais_marcas'=>array('type'=>'integer', 'fk'=>true, 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Selecionar uma marca'),
        'id_materiais_tipos'=>array('type'=>'integer', 'fk'=>true, 'requered'=>false, 'max'=>'10', 'default'=>'N', 'key'=>false, 'description'=>'Selecionar um tipo'),
        'id_unidades_medidas'=>array('type'=>'integer', 'fk'=>true, 'requered'=>false, 'max'=>'10', 'default'=>'N', 'key'=>false, 'description'=>'Selecionar uma unidade de medida'),
        'id_materiais_categorias'=>array('type'=>'integer', 'fk'=>true, 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Selecionar uma categoria'),
        'id_modo_conservacao'=>array('type'=>'integer', 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Modo de conservação'),
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

                if (isset($value['key'])==true && $fgRemoveKey==true) {
                    unset($this->newModel[$key]);
                }

                if ($value['type']=='double' && !empty($value['value'])) {
                    $value['value'] = numberFormatBanco($value['value']);
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
                     where p.id_produtos = :ID";
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

    public function loadCodigoBarras($codigo_barras) {
        try {
            $arr[':CODIGO_BARRAS'] = $codigo_barras;
            
            $sql = "select p.*
                      from ".self::TABLE." p
                     where p.codigo_barras = :CODIGO_BARRAS";
            $res = $this->conn->select($sql, $arr);
            
            return isset($res[0]) ? $res[0] : false;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function loadAll($status='',$start, $length,$order_by,$where) {
        try {
            $arr = array();
            $and = '';

            if (!empty($status)) {
                $arr[':STATUS'] = $status;
                $and .= " and status = :STATUS";
            } else {
                $arr[':STATUS'] = 'D';
                $and .= " and status != :STATUS";
            }
            
            if ($order_by != ''){
                $and .= $order_by;
            }

            if (($start != '') and ($length != '')) {
                $and .= " LIMIT ".$start.",".$length;
            }

            $sql = "select p.*
                      from ".self::TABLE." p
                     where 1 = 1 
                       ".$where." ".$and."";
            
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

    public function countAll($status='') {
        try {
            $arr = array();
            $and = '';

            if (!empty($status)) {
                $arr[':STATUS'] = $status;
                $and .= " and status = :STATUS";
            } else {
                $arr[':STATUS'] = 'D';
                $and .= " and status != :STATUS";
            }
            
            $sql = "select count(*) as total
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
            if (isset($values[':ID_PRODUTOS'])) {
                unset($values[':ID_PRODUTOS']);
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

            if(isset($values[':ID_PRODUTOS'])) {
                unset($values[':ID_PRODUTOS']);
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
                array(':STATUS'=>'D'), array(':ID_PRODUTOS'=>$id)
            );
            return $save;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
?>