<?php
class MateriaisFracionadosModel extends Connection {
    const TABLE = 'tb_materiais_fracionados';
    private $conn;
    private $newModel = array();

    function __construct() {
        $this->conn = new Connection();
    }

    //MODELAGEM DO BANCO
    private $fields = array(
        'id_materiais_fracionados'=>array('type'=>'integer', 'requered'=>true, 'max'=>10, 'key'=>true, 'description'=>'ID'),
        'qtd_fracionada'=>array('type'=>'double', 'requered'=>true, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Quantidade'),
        'dt_vencimento'=>array('type'=>'date', 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Data de Vencimento'),
        'dt_fracionamento'=>array('type'=>'date', 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Data de Manipulação'),
        'status'=>array('type'=>'string', 'requered'=>false, 'max'=>'1', 'default'=>'A', 'key'=>false, 'description'=>'status'),
        'motivo_descarte'=>array('type'=>'string', 'requered'=>false, 'max'=>'1000', 'default'=>'', 'key'=>false, 'description'=>'Motivo do descarte'),
        'id_materiais'=>array('type'=>'integer', 'requered'=>true, 'max'=>10, 'key'=>false, 'description'=>'ID MATERIAL'),
        'id_embalagens'=>array('type'=>'integer', 'requered'=>false, 'max'=>'10', 'default'=>'null', 'key'=>false, 'description'=>'Selecionar uma embalagem'),
        'id_unidades_medidas'=>array('type'=>'integer', 'requered'=>true, 'max'=>'10', 'default'=>'null', 'key'=>false, 'description'=>'Faltou a unidade de medida'),
        'id_usuarios'=>array('type'=>'integer', 'requered'=>false, 'max'=>'10', 'default'=>'N', 'key'=>false, 'description'=>'Usuário'),
        'id_setor'=>array('type'=>'integer', 'requered'=>false, 'max'=>'10', 'default'=>'null', 'key'=>false, 'description'=>'Setor do Usuário'),
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

                $arr[':'.toUpperCase($key).''] = !empty($value['value']) ? $value['value'] : null;
                
                

            }
        }

        return $arr;
    } 
    
    private function getFieldsView($data) {

        if (!empty($data['dt_vencimento'])) {
            $data['dt_vencimento'] = dt_br($data['dt_vencimento']);
        }

        if (!empty($data['dt_fracionamento'])) {
            $data['dt_fracionamento'] = dt_br($data['dt_fracionamento']);
        }

        if (!empty($data['qtd_fracionada'])) {
            $data['qtd_fracionada_formatado'] = numberformat($data['qtd_fracionada'], false);
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

    public function load($id_empresas, $status='', $id_setor='', $id_usuarios='') {
        try {
            $and = '';

            $arr[':ID_EMPRESAS'] = $id_empresas;

            if(!empty($status)) {
                $and.= " and x.status = :STATUS";
                $arr[':STATUS'] = $status;
            } /* else {
                $and.= " and p.status in('A','I')";
            } */

            if(!empty($id_setor)) {
                $and.= " and x.id_setor = :ID_SETOR";
                $arr[':ID_SETOR'] = $id_setor;
            }

            if(!empty($id_usuarios)) {
                $and.= " and x.id_usuarios = :ID_USUARIOS";
                $arr[':ID_USUARIOS'] = $id_usuarios;
            }
            
            $sql = "select x.*, 
                           m.descricao as ds_materiais,
                           (case when x.status = 'V' then 'UTILIZADO'
                                when x.status = 'C' then 'VENCIDO'
                                when x.status = 'D' then 'DESCARTADO'
                                else 'ATIVO'
                            end) as ds_status,
                            um.descricao as ds_unidade_medida,
                            p.nome as nm_usuario,
                            s.nome as nm_setor,
                            e.id_etiquetas as id_etiqueta,
                            e.num_etiqueta as num_etiqueta
                      from ".self::TABLE." x
                      inner join tb_materiais m on m.id_materiais = x.id_materiais
                      left join (select l.id_materiais_fracionados, l.id_usuarios 
                                   from tb_materiais_fracionados_log l 
                                  where l.acao = 'CADASTRO') as ul on ul.id_materiais_fracionados = x.id_materiais_fracionados
                      left join tb_usuarios u on u.id_usuarios = ifnull(ul.id_usuarios, x.id_usuarios)
                      left join tb_pessoas p on p.id_pessoas = u.id_pessoas
                      inner join tb_unidades_medidas um on um.id_unidades_medidas = x.id_unidades_medidas
                      inner join tb_setor s on s.id_setor = x.id_setor
                      inner join tb_etiquetas e on e.id_materiais_fracionados = x.id_materiais_fracionados
                     where m.id_empresas = :ID_EMPRESAS ".$and;
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
            throw $e->getMessage();
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
                $w[':'.toUpperCase($key).''] = $value;
            }
    
            if(isset($values[':ID_MATERIAIS_FRACIONADOS'])) {
                unset($values[':ID_MATERIAIS_FRACIONADOS']);
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
                array(':STATUS'=>'D'), array(':ID_MATERIAIS_FRACIONADOS'=>$id)
            );
            return $save;
        } catch (Exception $e) {
            throw $e->getMessage();
        }
    }

    public static function addUnit(
    int $id_materiais,
    string $dt_vencimento_iso, // 'YYYY-MM-DD'
    int $id_usuarios,
    int $id_setor,
    int $id_unidades_medidas
    ): int {
    $pdo = $GLOBALS['pdo'];
    $sql = "INSERT INTO tb_materiais_fracionados
                (id_materiais, qtd_fracionada, dt_vencimento, status, id_usuarios, id_setor, id_unidades_medidas)
            VALUES
                (:m, 1, :v, 'A', :u, :s, :um)";
    $st = $pdo->prepare($sql);
    $st->execute([
        ':m' => $id_materiais,
        ':v' => $dt_vencimento_iso ?: null,
        ':u' => $id_usuarios,
        ':s' => $id_setor,
        ':um'=> $id_unidades_medidas
    ]);
    return (int)$pdo->lastInsertId();
    }
}
?>