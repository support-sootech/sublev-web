<?php

class PessoasModel extends Connection {
    const TABLE = 'tb_pessoas';
    private $conn;
    private $newModel = array();

    function __construct() {
        $this->conn = new Connection();
    }

    public function getConnection() {
        return new Connection();
    }

    //MODELAGEM DO BANCO
    private $fields = array(
        'id_pessoas'=>array('type'=>'integer', 'requered'=>true, 'max'=>10, 'key'=>true, 'description'=>'ID'),
        'nome'=>array('type'=>'string', 'requered'=>true, 'max'=>100, 'key'=>false, 'description'=>'Nome'),
        'dt_nascimento'=>array('type'=>'date', 'requered'=>true, 'max'=>10, 'key'=>false, 'description'=>'Data de Nascimento'),
        'cpf_cnpj'=>array('type'=>'integer', 'requered'=>true, 'max'=>14, 'key'=>false, 'description'=>'CPF / CNPJ'),
        'tp_juridico'=>array('type'=>'string', 'requered'=>true, 'max'=>1, 'key'=>false, 'description'=>'Tipo Jurídico'),
        'genero'=>array('type'=>'string', 'requered'=>true, 'max'=>'1', 'key'=>false, 'description'=>'Gênero'),
        'telefone'=>array('type'=>'integer', 'requered'=>false, 'max'=>'11', 'key'=>false, 'description'=>'Telefone'),
        'email'=>array('type'=>'string', 'requered'=>true, 'max'=>'100', 'key'=>false, 'description'=>'E-mail'),
        //'dh_cadastro'=>array('type'=>'date', 'requered'=>false, 'max'=>'8', 'key'=>false, 'description'=>'Data e Hora de Cadastro'),
        'id_empresas'=>array('type'=>'integer', 'requered'=>false, 'max'=>'10', 'key'=>false, 'description'=>'Código da empresa'),
        'id_tipos_pessoas'=>array('type'=>'integer', 'requered'=>true, 'max'=>'10', 'key'=>false, 'description'=>'Código tipo de pessoa'),
        'cep'=>array('type'=>'integer', 'requered'=>false, 'max'=>'8', 'key'=>false, 'description'=>'Endereço: CEP'),
        'logradouro'=>array('type'=>'string', 'requered'=>false, 'max'=>'200', 'key'=>false, 'description'=>'Endereço: Logradouro'),
        'numero'=>array('type'=>'string', 'requered'=>false, 'max'=>'10', 'key'=>false, 'description'=>'Endereço: Número'),
        'complemento'=>array('type'=>'string', 'requered'=>false, 'max'=>'200', 'key'=>false, 'description'=>'Endereço: Complemento'),
        'bairro'=>array('type'=>'string', 'requered'=>false, 'max'=>'200', 'key'=>false, 'description'=>'Endereço: Bairro'),
        'cidade'=>array('type'=>'string', 'requered'=>false, 'max'=>'200', 'key'=>false, 'description'=>'Endereço: Cidade'),
        'estado'=>array('type'=>'string', 'requered'=>false, 'max'=>'100', 'key'=>false, 'description'=>'Endereço: Estado'),
        'cod_ibge'=>array('type'=>'integer', 'requered'=>false, 'max'=>'10', 'key'=>false, 'description'=>'Endereço: Código IBGE'),
        'status'=>array('type'=>'string', 'requered'=>false, 'max'=>'1', 'default'=>'A', 'key'=>false, 'description'=>'status'),
    );
    
    public function setFields($arr) {
        if (count($arr) > 0) {
            
            if (isset($arr['dt_nascimento']) && !empty($arr['dt_nascimento'])) {
                $arr['dt_nascimento'] = dt_banco($arr['dt_nascimento']);
            }
            
            if (isset($arr['cpf_cnpj']) && !empty($arr['cpf_cnpj'])) {
                $arr['cpf_cnpj'] = limpa_numero($arr['cpf_cnpj']);
            }

            if (isset($arr['telefone']) && !empty($arr['telefone'])) {
                $arr['telefone'] = limpa_numero($arr['telefone']);
            }
            
            if (isset($arr['dh_cadastro']) && !empty($arr['dh_cadastro'])) {
                $arr['dh_cadastro'] = dh_banco($arr['dh_cadastro']);
            }            

            if (isset($arr['cep']) && !empty($arr['cep'])) {
                $arr['cep'] = limpa_numero($arr['cep']);
            }

            if (isset($arr['numero']) && !empty($arr['numero'])) {
                $arr['numero'] = limpa_numero($arr['numero']);
            }
            
            foreach ($this->fields as $key => $value) {
                $this->newModel[$key] = $value;
                $this->newModel[$key]['value'] = (isset($arr[$key]) && !empty($arr[$key]) ? $arr[$key] : null);
            }
        }
    }

    public function getFields($fgRemoveKey=true) {
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

    public function getModelView($arr) {
        
        if (isset($arr['cpf_cnpj']) && !empty($arr['cpf_cnpj'])) {
            $arr['cpf_cnpj'] = mascaraCpfCnpj($arr['tp_juridico'], $arr['cpf_cnpj']);
        }

        if (isset($arr['dt_nascimento']) && !empty($arr['dt_nascimento'])) {
            $arr['dt_nascimento'] = dt_br($arr['dt_nascimento']);
        }

        if (isset($arr['dh_cadastro']) && !empty($arr['dh_cadastro'])) {
            $arr['dh_cadastro'] = dh_br($arr['dh_cadastro']);
        }

        if (isset($arr['cep']) && !empty($arr['cep'])) {
            $arr['cep'] = mascaraCep($arr['cep']);
        }
        
        if (isset($arr['telefone']) && !empty($arr['telefone'])) {
            $arr['telefone'] = mascaraTelefone($arr['telefone']);
        }

        return $arr;
    }

    public function loadId($id) {
        try {
            $arr[':ID'] = $id;
            
            $sql = "select u.*,
                           e.id_empresas as id_empresa,
                           e.nome as nm_empresa,
                           tp.descricao as ds_tipos_pessoas
                      from ".self::TABLE." u                      
                      inner join tb_empresas e on e.id_empresas = u.id_empresas
                      inner join tb_tipos_pessoas tp on tp.id_tipos_pessoas = u.id_tipos_pessoas
                     where u.id_usuarios = :ID";
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

    public function loadAll() {
        try {
            $arr = array();
            $and = '';
            
            $sql = "select u.*,
                           ps.nome as nm_pessoa, 
                           ps.email, 
                           ps.dt_nascimento, 
                           ps.tp_juridico, 
                           (case when ps.tp_juridico = 'J' then lpad(ps.cpf_cnpj, 14, 0)
                                else lpad(ps.cpf_cnpj, 11, 0)
                           end) as cpf_cnpj,
                           #ps.cpf_cnpj,
                           ps.genero, e.nome as nm_empresa
                      from ".self::TABLE." u
                      inner join tb_pessoas ps on ps.id_pessoas = u.id_pessoas
                      inner join tb_empresas e on e.id_empresas = ps.id_empresas
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
            if (isset($values[':ID_PESSOAS'])) {
                unset($values[':ID_PESSOAS']);
            }
            $save = $this->conn->insert(self::TABLE, $values);
            return $save;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function edit(Array $arr, Array $where){
        
        try {
            $this->setFields($arr);
            $values = $this->getFields();
            
            $w = array();
            foreach ($where as $key => $value) {
                $w[':'.$key.''] = $value;
            }

            if(isset($values[':ID_PESSOAS'])) {
                unset($values[':ID_PESSOAS']);
            }   
            
            $save = $this->conn->update(self::TABLE, $values, $w);
            return $save;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 1);
        }
    }

    public function del($id){
        try {
            //$save = $this->conn->delete(self::TABLE, array(':ID_USUARIOS'=>$id));
            $save = $this->conn->update(self::TABLE, array(':STATUS'=>'D'), array(':ID_PESSOAS'=>$id));
            return $save;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
?>