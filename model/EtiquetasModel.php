<?php
class EtiquetasModel extends Connection {
    const TABLE = 'tb_etiquetas';
    private $conn;
    private $newModel = array();

    function __construct() {
        $this->conn = new Connection();
    }

    //MODELAGEM DO BANCO
    private $fields = array(
        'id_etiquetas'=>array('type'=>'integer', 'requered'=>true, 'max'=>11, 'key'=>true, 'description'=>'ID'),
        'descricao'=>array('type'=>'string', 'requered'=>false, 'max'=>'50', 'default'=>'', 'key'=>false, 'description'=>'Descrição'),
        'codigo'=>array('type'=>'string', 'requered'=>false, 'max'=>'2000', 'default'=>'', 'key'=>false, 'description'=>'QR Code'),
        'id_materiais_fracionados'=>array('type'=>'integer', 'fk'=>true, 'requered'=>true, 'max'=>'11', 'default'=>'', 'key'=>false, 'description'=>'Material Fracionado'),
        'id_materiais'=>array('type'=>'integer', 'fk'=>true, 'requered'=>true, 'max'=>'11', 'default'=>'', 'key'=>false, 'description'=>'Material'),
        'status'=>array('type'=>'string', 'requered'=>false, 'max'=>'1', 'default'=>'A', 'key'=>false, 'description'=>'status'),
        'id_usuarios'=>array('type'=>'integer', 'fk'=>true, 'requered'=>true, 'max'=>'11', 'default'=>'', 'key'=>false, 'description'=>'Usuário'),
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

                $arr[':'.mb_strtoupper($key).''] = $value['value'];
            }
        }

        return $arr;
    }

    private function getFieldsView($data) {

        if (!empty($data['dt_fabricacao'])) {
            $data['dt_fabricacao_reduzido'] = dt_br($data['dt_fabricacao'], false);
            $data['dt_fabricacao'] = dt_br($data['dt_fabricacao']);
        }

        if (!empty($data['dt_vencimento'])) {
            $data['dt_vencimento_reduzido'] = dt_br($data['dt_vencimento'], false);
            $data['dt_vencimento'] = dt_br($data['dt_vencimento']);
        }

        if (!empty($data['dt_fracionamento'])) {
            $data['dt_fracionamento_reduzido'] = dt_br($data['dt_fracionamento'], false);
            $data['dt_fracionamento'] = dt_br($data['dt_fracionamento']);
        }

        if (!empty($data['qtd_fracionada'])) {
            $data['qtd_fracionada'] = numberformat($data['qtd_fracionada'], false);
        }

        return $data;
    }

    public function loadId($id) {
        try {
            $arr[':ID'] = $id;
            
            $sql = "select p.*
                      from ".self::TABLE." p
                     where p.id_etiquetas = :ID";
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

    public function loadIdEtiquetaInfo($id_etiquetas) {

        $arr[':ID_ETIQUETAS'] = $id_etiquetas;
        $sql = "select e.*, 
                    m.descricao as ds_material, m.lote, m.dt_fabricacao, m.cod_barras,
                    um.descricao as ds_unidades_medidas,
                    mc.descricao as ds_modo_conservacao,
                    mf.qtd_fracionada, mf.dt_fracionamento, mf.dt_vencimento,
                    p.nome as nm_pessoa,
                    s.nome as nm_setor
                from tb_etiquetas e 
                inner join tb_materiais_fracionados mf on mf.id_materiais_fracionados = e.id_materiais_fracionados
                inner join tb_materiais m on m.id_materiais = e.id_materiais
                inner join tb_unidades_medidas um on um.id_unidades_medidas = m.id_unidades_medidas
                inner join tb_modo_conservacao mc on mc.id = m.id_modo_conservacao
                inner join tb_usuarios u on u.id_usuarios = mf.id_usuarios
                inner join tb_pessoas p on p.id_pessoas = u.id_pessoas
                left join tb_setor s on s.id_setor = mf.id_setor
                where e.id_etiquetas = :ID_ETIQUETAS";
        $res = $this->conn->select($sql, $arr);

        if (isset($res[0])) {
            $data = $this->getFieldsView($res[0]);
            $data['nm_pessoa_abreviado'] = (!empty($data['nm_pessoa']) ? abreviarNome($data['nm_pessoa']) : '');
            return $data;
        } else {
            return false;
        }
        
        return isset($res[0]) ?  : false;

    }

    public function loadEtiquetaDetalhes($id) {
        try {
            $arr[':ID'] = $id;
            
            $sql = "select p.*,
                           m.descricao as desc_material,
                           m.lote,
                           mf.qtd_fracionada,
                           DATE_FORMAT(mf.dt_fracionamento,'%d/%m/%Y') as dt_fracionamento,
                           DATE_FORMAT(mf.dt_vencimento,'%d/%m/%Y') as dt_vencimento
                      from ".self::TABLE." p,
                           tb_materiais m,
                           tb_materiais_fracionados mf
                     where p.id_etiquetas = :ID
                           and p.id_materiais = mf.id_materiais
                           and p.id_materiais_fracionados = mf.id_materiais_fracionados
                           and mf.id_materiais = m.id_materiais";
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

    public function loadEtiquetasIdUsuarios($id_usuarios) {
        try {
            $arr[':ID_USUARIOS'] = $id_usuarios;
            
            $sql = "select e.*, 
                            m.descricao as ds_material, m.lote, m.dt_fabricacao, m.cod_barras,
                            um.descricao as ds_unidades_medidas,
                            mc.descricao as ds_modo_conservacao,
                            mf.qtd_fracionada, mf.dt_fracionamento, mf.dt_vencimento,
                            p.nome as nm_pessoa,
                            s.nome as nm_setor
                    from tb_etiquetas e 
                    inner join tb_materiais_fracionados mf on mf.id_materiais_fracionados = e.id_materiais_fracionados
                    inner join tb_materiais m on m.id_materiais = e.id_materiais
                    inner join tb_unidades_medidas um on um.id_unidades_medidas = m.id_unidades_medidas
                    inner join tb_modo_conservacao mc on mc.id = m.id_modo_conservacao
                    inner join tb_usuarios u on u.id_usuarios = mf.id_usuarios
                    inner join tb_pessoas p on p.id_pessoas = u.id_pessoas
                    left join tb_setor s on s.id_setor = mf.id_setor
                    where e.id_usuarios = :ID_USUARIOS
                    order by e.id_etiquetas desc";
            $res = $this->conn->select($sql, $arr);
            
            if (isset($res[0])) {
                $arr = array();
                foreach ($res as $key => $value) {
                    $value['nm_pessoa_abreviado'] = (!empty($value['nm_pessoa']) ? abreviarNome($value['nm_pessoa']) : '');
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

    public function loadCodigoBarras($codigo) {
        try {
            $arr[':CODIGO'] = $codigo;
            
            $sql = "select p.*
                      from ".self::TABLE." p
                     where p.codigo = :CODIGO";
            $res = $this->conn->select($sql, $arr);
            
            return isset($res[0]) ? $res[0] : false;
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
                $and .= " and status = :STATUS";
            } else {
                $arr[':STATUS'] = 'D';
                $and .= " and status != :STATUS";
            }
            
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
            if (isset($values[':ID_ETIQUETAS'])) {
                unset($values[':ID_ETIQUETAS']);
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
                $w[':'.mb_strtoupper($key).''] = $value;
            }

            if(isset($values[':ID_ETIQUETAS'])) {
                unset($values[':ID_ETIQUETAS']);
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
                array(':STATUS'=>'D'), array(':ID_ETIQUETAS'=>$id)
            );
            return $save;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
?>