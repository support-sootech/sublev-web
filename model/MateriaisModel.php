<?php
class MateriaisModel extends Connection {
    const TABLE = 'tb_materiais';
    private $conn;
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
        'id_pessoas_fornecedor'=>array('type'=>'integer', 'fk'=>true, 'requered'=>false, 'max'=>'10', 'default'=>null, 'key'=>false, 'description'=>'Informar o fornecedor'),
        'id_pessoas_fabricante'=>array('type'=>'integer', 'fk'=>true, 'requered'=>false, 'max'=>'10', 'default'=>null, 'key'=>false, 'description'=>'Informar o fabricante'),
        'id_embalagens'=>array('type'=>'integer', 'fk'=>true, 'requered'=>false, 'max'=>'10', 'default'=>'N', 'key'=>false, 'description'=>'Selecionar uma embalagem'),
        'id_materiais_marcas'=>array('type'=>'integer', 'fk'=>true, 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Selecionar uma marca'),
        'id_materiais_tipos'=>array('type'=>'integer', 'fk'=>true, 'requered'=>false, 'max'=>'10', 'default'=>'N', 'key'=>false, 'description'=>'Selecionar um tipo'),
        'id_unidades_medidas'=>array('type'=>'integer', 'fk'=>true, 'requered'=>false, 'max'=>'10', 'default'=>'N', 'key'=>false, 'description'=>'Selecionar uma unidade de medida'),
        'id_materiais_categorias'=>array('type'=>'integer', 'fk'=>true, 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Selecionar uma categoria'),
        'id_empresas'=>array('type'=>'integer', 'fk'=>true, 'requered'=>false, 'max'=>'10', 'default'=>'N', 'key'=>false, 'description'=>'Selecionar uma empresa'),
        'cod_barras'=>array('type'=>'string', 'requered'=>false, 'max'=>'50', 'default'=>'N', 'key'=>false, 'description'=>'Código barras'),
        'dias_vencimento'=>array('type'=>'integer', 'requered'=>false, 'max'=>'2', 'default'=>'', 'key'=>false, 'description'=>'Qtd dias vencimento'),
        'dias_vencimento_aberto'=>array('type'=>'integer', 'requered'=>false, 'max'=>'2', 'default'=>'', 'key'=>false, 'description'=>'Qtd dias vencimento aberto'),
        'temperatura'=>array('type'=>'integer', 'requered'=>false, 'max'=>'3', 'default'=>'', 'key'=>false, 'description'=>'Temperatura'),
        'sif'=>array('type'=>'integer', 'requered'=>false, 'max'=>'3', 'default'=>'', 'key'=>false, 'description'=>'SIF'),
        'nro_nota'=>array('type'=>'string', 'requered'=>false, 'max'=>'100', 'default'=>'', 'key'=>false, 'description'=>'Número da nota fiscal'),
        'id_embalagem_condicoes'=>array('type'=>'integer', 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Embalagem condições'),
        'id_modo_conservacao'=>array('type'=>'integer', 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Modo de conservação'),
        'id_usuarios'=>array('type'=>'integer', 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Usuário'),

    );
    
    private function setFields($arr) {
        if (count($arr) > 0) {
            foreach ($this->fields as $key => $value) {
                $this->newModel[$key] = $value;
                $this->newModel[$key]['value'] = (isset($arr[$key]) && (!empty($arr[$key]) || $arr[$key]==0) ? $arr[$key] : null);
            }
        }
    }

    private function getFields($fgRemoveKey=true) {
        $arr = array();

        if (count($this->newModel) > 0) {
            foreach ($this->newModel as $key => $value) {

                $campo = (isset($value['description']) && !empty($value['description']) ? $value['description'] : $key);

                if ($value['requered']==true && (!isset($value['value']) && empty($value['value'])) && !$value['key']) {
                    throw new Exception('O campo '.$campo.' não pode ser vazio!');
                }

                if (isset($value['max']) && $value['max'] < strlen($value['value']) ) {
                    throw new Exception('O campo '.$campo.' deve conter no máximo '.$value['max'].' caracter(es)!');
                }

                if ($value['type']=='integer' && !$value['key']) {

                    $value['value'] = $value['value']!=null ? intval($value['value']) : null;

                    if ((isset($value['fk']) && $value['fk']) && (empty($value['value']) && $value['requered'])) {
                        throw new Exception('O campo '.$value['description'].' não pode ser vazio.');
                    } else if(!is_int($value['value']) && (isset($value['fk']) && !$value['fk'])) {
                        throw new Exception('O campo '.$campo.' deve ser do tipo numérico!');
                    }
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

                if ($value['type']=='integer') {
                    $arr[':'.toUpperCase($key).''] = is_int($value['value']) ? $value['value'] : null;
                } else {
                    $arr[':'.toUpperCase($key).''] = !empty($value['value']) ? $value['value'] : null;
                }
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
            $data['peso_banco'] = $data['peso'];
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

    public function loadIdMaterialDtVencimento($id , $dt_vencimento) {
        try {
            $arr[':ID'] = $id;
            $arr[':DT_VENCIMENTO'] = dt_banco($dt_vencimento);
            
            $sql = "select p.*
                      from ".self::TABLE." p
                     where p.id_materiais = :ID
                           and p.dt_vencimento = :DT_VENCIMENTO";
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

    public function loadIdMaterialCategoria($status,$id) {
        
        try {
            $arr= array();

            $arr[':ID'] = $id;
            $arr[':STATUS'] = $status;

            $and = '';

            if (!empty($status)) {
                $arr[':STATUS'] = $status;
                $and .= " and status = :STATUS";
            } else {
                $arr[':STATUS'] = 'D';
                $and .= " and status != :STATUS";
            }

            $sql = "select p.*
                      from ".self::TABLE." p
                     where p.id_materiais_categorias = :ID
                     ".$and."
                     order by p.descricao";
            
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

    public function loadIdMaterialDetalhes($status,$id) {
        
        try {
            $arr= array();

            $arr[':ID'] = $id;
            $arr[':STATUS'] = $status;

            $and = '';

            if (!empty($status)) {
                $arr[':STATUS'] = $status;
                $and .= " and p.status = :STATUS";
            } else {
                $arr[':STATUS'] = 'D';
                $and .= " and p.status != :STATUS";
            }

            $sql = "select p.*,
                           mf.id_materiais_fracionados as id_materiais_fracionados,
                           DATE_FORMAT(mf.dt_fracionamento,'%d/%m/%Y') as dt_fracionamento,
                           DATE_FORMAT(mf.dt_vencimento,'%d/%m/%Y') as dt_vencimento,
                           ifnull(mm.descricao, '') as marca,
                           ifnull(um.descricao, '') as ds_unidade_medida
                      from ".self::TABLE." p
                      left join tb_materiais_marcas mm on mm.id_materiais_marcas = p.id_materiais_marcas
                      left join tb_unidades_medidas um on um.id_unidades_medidas = p.id_unidades_medidas
                      left join tb_materiais_fracionados mf on mf.id_materiais = p.id_materiais
                     where p.id_materiais = :ID
                     ".$and."";
            
            $res = $this->conn->select($sql, $arr);
            
            return isset($res[0]) ? $res[0] : false;
            
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
    public function loadCodBarrasMaterialDetalhes($status,$cod_barras) {
        
        try {
            $arr= array();

            $arr[':COD_BARRAS'] = $cod_barras;
            $arr[':STATUS'] = $status;

            $and = '';

            if (!empty($status)) {
                $arr[':STATUS'] = $status;
                $and .= " and p.status = :STATUS";
            } else {
                $arr[':STATUS'] = 'D';
                $and .= " and p.status != :STATUS";
            }

            $sql = "select p.*, 
                           ifnull(mm.descricao, '') as marca,
                           ifnull(um.descricao, '') as ds_unidade_medida,
                           (case when datediff(p.dt_vencimento, current_date()) <= 1 then 'danger'
                                when datediff(p.dt_vencimento, current_date()) > 1 and datediff(p.dt_vencimento, current_date()) < 5 then 'primary'
                                else 'success'
                            end) as color_dt_vencimento
                      from ".self::TABLE." p
                      left join tb_materiais_marcas mm on mm.id_materiais_marcas = p.id_materiais_marcas
                      left join tb_unidades_medidas um on um.id_unidades_medidas = p.id_unidades_medidas
                     where p.cod_barras = :COD_BARRAS
                       and p.quantidade >= 1
                     ".$and."
                     order by p.dt_vencimento";
            
            $res = $this->conn->select($sql, $arr);
            
            $arr = false;
            if (isset($res[0])) {
                foreach ($res as $key => $value) {
                    $arr[] = $this->getFieldsView($value);
                }
            }

            return $arr;
            
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
            
            $sql = "select p.*, p1.nome as nm_fabricante, p2.nome as nm_fornecedor, mm.descricao as marca
                      from ".self::TABLE." p
                      left join tb_pessoas p1 on p1.id_pessoas = p.id_pessoas_fabricante
                      left join tb_pessoas p2 on p2.id_pessoas = p.id_pessoas_fornecedor
                      left join tb_materiais_marcas mm on mm.id_materiais_marcas = p.id_materiais_marcas
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

    public function loadRelatorioMateriaisRecebimento($id_empresas="", $dt_ini, $dt_fim, $status='') {
        try {
            $arr = array();
            $and = '';

            if (!empty($status)) {
                $arr[':STATUS'] = $status;
                $and .= " and m.status = :STATUS";
            } else {
                $arr[':STATUS'] = 'D';
                $and .= " and m.status != :STATUS";
            }

            if (!empty($id_empresas)) {
                $and.= " and m.id_empresas = :ID_EMPRESAS";
                $arr[':ID_EMPRESAS'] = $id_empresas;
            }

            $and.= " and cast(m.dh_cadastro as date) between :DT_INI and :DT_FIM";
            $arr[':DT_INI'] = dt_banco($dt_ini);
            $arr[':DT_FIM'] = dt_banco($dt_fim);

            
            $sql = "select m.dh_cadastro, m.descricao, m.dt_vencimento, m.quantidade, m.temperatura, m.sif, m.lote, m.nro_nota,
                            p.nome as nm_fornecedor,
                            ec.descricao as ds_embalagem_condicoes,
                            p1.nome as nm_responsavel
                    from tb_materiais m
                    left join tb_pessoas p on p.id_pessoas = m.id_pessoas_fornecedor and p.id_tipos_pessoas = 3
                    left join tb_embalagem_condicoes ec on ec.id = m.id_embalagem_condicoes
                    left join tb_usuarios u on u.id_usuarios = m.id_usuarios
                    left join tb_pessoas p1 on p1.id_pessoas = u.id_pessoas
                    where 1 = 1                        
                       ".$and."
                    order by m.dh_cadastro";
            $res = $this->conn->select($sql, $arr);
            
            return isset($res[0]) ? $res : false;

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
    }

    public function edit(Array $arr, Array $where){

        $this->setFields($arr);
        $values = $this->getFields();
        
        $w = array();
        foreach ($where as $key => $value) {
            $w[':'.toUpperCase($key).''] = $value;
        }

        if(isset($values[':ID_MATERIAIS'])) {
            unset($values[':ID_MATERIAIS']);
        }
        
        $save = $this->conn->update(self::TABLE, $values, $w);
        return $save;
    }

    public function del($id){
        try {
            $save = $this->conn->update(
                self::TABLE, 
                array(':STATUS'=>'D'), array(':ID_MATERIAIS'=>$id)
            );
            return $save;
        } catch (Exception $e) {
            throw $e->getMessage();
        }
    }
}
?>