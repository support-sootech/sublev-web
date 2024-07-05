<?php
class MateriaisModel extends Connection {
    const TABLE = 'tb_materiais';
    private $conn = false;
    private $newModel = array();

    function __construct() {
        $this->conn = new Connection();
    }

    //MODELAGEM DO BANCO
    private $fields = array(
        'id_materiais'=>array('type'=>'integer', 'requered'=>true, 'max'=>10, 'key'=>true, 'description'=>'ID'),
        'descricao'=>array('type'=>'string', 'requered'=>true, 'max'=>'100', 'default'=>'', 'key'=>false, 'description'=>'Descrição'),
        'peso'=>array('type'=>'double', 'requered'=>true, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Peso'),
        'quantidade'=>array('type'=>'integer', 'requered'=>true, 'max'=>'4', 'default'=>'', 'key'=>false, 'description'=>'Quantidade'),
        'status'=>array('type'=>'string', 'requered'=>false, 'max'=>'1', 'default'=>'A', 'key'=>false, 'description'=>'status'),
        'motivo_descarte'=>array('type'=>'string', 'requered'=>false, 'max'=>'1000', 'default'=>'', 'key'=>false, 'description'=>'Motivo do descarte'),
        'lote'=>array('type'=>'string', 'requered'=>false, 'max'=>'100', 'default'=>'', 'key'=>false, 'description'=>'Lote'),
        'preco'=>array('type'=>'double', 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Preço'),
        'dt_fabricacao'=>array('type'=>'date', 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Data de Fabricação'),
        'dt_vencimento'=>array('type'=>'date', 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Data de Vencimento'),
        'dt_vencimento_aberto'=>array('type'=>'date', 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Data de Vencimento Aberto'),
        'qtd_restante'=>array('type'=>'double', 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Qtd. Restante'),
        'fg_embalagem'=>array('type'=>'string', 'requered'=>false, 'max'=>'1', 'default'=>'N', 'key'=>false, 'description'=>'Flag da embalagem'),
        'id_pessoas_fornecedor'=>array('type'=>'integer', 'requered'=>true, 'max'=>'10', 'default'=>'N', 'key'=>false, 'description'=>'Informar o fornecedor'),
        'id_pessoas_fabricante'=>array('type'=>'integer', 'requered'=>true, 'max'=>'10', 'default'=>'N', 'key'=>false, 'description'=>'Informar o fabricante'),
        'id_embalagens'=>array('type'=>'integer', 'requered'=>false, 'max'=>'10', 'default'=>'N', 'key'=>false, 'description'=>'Selecionar uma embalagem'),
        'id_materiais_marcas'=>array('type'=>'integer', 'requered'=>false, 'max'=>'10', 'default'=>'N', 'key'=>false, 'description'=>'Selecionar uma marca'),
        'id_materiais_tipos'=>array('type'=>'integer', 'requered'=>false, 'max'=>'10', 'default'=>'N', 'key'=>false, 'description'=>'Selecionar um tipo'),
        'id_unidades_medidas'=>array('type'=>'integer', 'requered'=>false, 'max'=>'10', 'default'=>'N', 'key'=>false, 'description'=>'Selecionar uma unidade de medida'),
        'id_materiais_categorias'=>array('type'=>'integer', 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Selecionar uma categoria'),
        'id_empresas'=>array('type'=>'integer', 'requered'=>false, 'max'=>'10', 'default'=>'N', 'key'=>false, 'description'=>'Selecionar uma empresa'),
        'cod_barras'=>array('type'=>'string', 'requered'=>false, 'max'=>'50', 'default'=>'N', 'key'=>false, 'description'=>'Código barras'),
        'dias_vencimento'=>array('type'=>'integer', 'requered'=>false, 'max'=>'2', 'default'=>'', 'key'=>false, 'description'=>'Qtd dias vencimento'),
        'dias_vencimento_aberto'=>array('type'=>'integer', 'requered'=>false, 'max'=>'2', 'default'=>'', 'key'=>false, 'description'=>'Qtd dias vencimento aberto'),
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

        if (!empty($data['dt_fabricacao'])) {
            $data['dt_fabricacao'] = dt_br($data['dt_fabricacao']);
        }

        if (!empty($data['dt_vencimento'])) {
            $data['dt_vencimento'] = dt_br($data['dt_vencimento']);
        }

        if (!empty($data['dt_vencimento_aberto'])) {
            $data['dt_vencimento_aberto'] = dt_br($data['dt_vencimento_aberto']);
        }

        if (!empty($data['preco'])) {
            $data['preco'] = numberformat($data['preco']);
        }

        if (!empty($data['peso'])) {
            $data['peso'] = numberformat($data['peso'], false);
        }

        return $data;
    }

    public function loadId($id) {
        try {
            $arr[':ID'] = $id;
            
            $sql = "select p.*
                      from ".self::TABLE." p
                     where p.id_materiais = :ID";
            $res = $this->conn->select($sql, $arr);
            
            if (isset($res[0])) {
                return $this->getFieldsView($res[0]);
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    

    public function loadAll($status='') {
        try {
            $arr = array();
            $and = '';

            if (!empty($status)) {
                $arr[':STATUS'] = $status;
                $and .= " and p.status = :STATUS";
            } else {
                $arr[':STATUS'] = 'D';
                $and .= " and p.status != :STATUS";
            }
            
            $sql = "select p.*, p1.nome as nm_fabricante, p2.nome as nm_fornecedor 
                      from ".self::TABLE." p
                      inner join tb_pessoas p1 on p1.id_pessoas = p.id_pessoas_fabricante
                      inner join tb_pessoas p2 on p2.id_pessoas = p.id_pessoas_fornecedor
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
        if (isset($values[':ID_MATERIAIS'])) {
            unset($values[':ID_MATERIAIS']);
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

            if(isset($values[':ID_MATERIAIS'])) {
                unset($values[':ID_MATERIAIS']);
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
                array(':STATUS'=>'D'), array(':ID_MATERIAIS'=>$id)
            );
            return $save;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
?>