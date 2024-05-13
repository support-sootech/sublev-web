<?php
class FornecedoresModel extends Connection {   
    
    //const TABLE = 'tb_pessoas';
    private $conn = '';
    private $class_pessoa_model = '';

    function __construct()
    {
        //$this->conn = $this->getConnection();
        $this->conn = new Connection();
        $this->class_pessoa_model = new PessoasModel();

    }

    public function loadId($id) {
        try {
            $arr[':ID'] = $id;
            
            $sql = "select u.*, 
                           ps.nome as nm_pessoa, 
                           ps.email, 
                           ps.dt_nascimento, 
                           ps.tp_juridico, 
                           (case when ps.tp_juridico = 'J' then lpad(ps.cpf_cnpj, 14, 0)
                                else lpad(ps.cpf_cnpj, 11, 0)
                           end) as cpf_cnpj,
                           #ps.cpf_cnpj,
                           tp.id_tipos_pessoas,
                           tp.descricao as ds_tipos_pessoas,
                           ps.genero, 
                           ps.cep,
                           ps.logradouro,
                           ps.numero,
                           ps.complemento,
                           ps.bairro,
                           ps.cidade,
                           ps.estado,
                           ps.cod_ibge,
                           ps.telefone,
                           e.id_empresas,
                           e.nome as nm_empresa
                      from tb_pessoas u
                      inner join tb_pessoas ps on ps.id_pessoas = u.id_pessoas
                      inner join tb_empresas e on e.id_empresas = ps.id_empresas
                      inner join tb_tipos_pessoas tp on tp.id_tipos_pessoas = ps.id_tipos_pessoas
                     where u.id_pessoas = :ID";
            $res = $this->conn->select($sql, $arr);
            
            if (isset($res[0])) {
                return $this->class_pessoa_model->getModelView($res[0]);
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function add($arr) {
        try {
            $arr['id_tipos_pessoas'] = '3';
            $arr['cpf_cnpj'] = '12911671023';
            return $this->class_pessoa_model->add($arr);
        } catch (Exception $e) {
            $msg = messagesDefault($e->getCode());
            if (empty($msg)) {
                $msg = $e->getMessage();
            }
            return throw new Exception($msg);
        }
        
    }

    /*
    public function loadAll($id_tipos_perfil='', $status='') {
        try {
            $arr = array();
            $and = " and u.status not in('D')";
                        
            $sql = "select u.*,
                           ps.nome as nm_pessoa, 
                           ps.email, 
                           ps.dt_nascimento, 
                           ps.tp_juridico, 
                           (case when ps.tp_juridico = 'J' then lpad(ps.cpf_cnpj, 14, 0)
                                else lpad(ps.cpf_cnpj, 11, 0)
                           end) as cpf_cnpj,
                           #ps.cpf_cnpj,
                           ps.genero, e.nome as nm_empresa,
                           ps.cep,
                           ps.logradouro,
                           ps.numero,
                           ps.complemento,
                           ps.bairro,
                           ps.cidade,
                           ps.estado,
                           ps.cod_ibge,
                           ps.telefone,
                           tp.id_tipos_pessoas,
                           tp.descricao as ds_tipos_pessoas
                      from ".self::TABLE." u
                      inner join tb_pessoas ps on ps.id_pessoas = u.id_pessoas
                      inner join tb_empresas e on e.id_empresas = ps.id_empresas
                      inner join tb_tipos_pessoas tp on tp.id_tipos_pessoas = ps.id_tipos_pessoas
                     where 1 = 1 
                       ".$and."";
            $res = $this->conn->select($sql, $arr);
            
            if (isset($res[0])) {
                $arr = array();

                foreach ($res as $key => $value) {
                    $arr[] = $this->getModelView($value);
                }

                return $arr;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function loadHash($hash) {
        try {
            $arr[':HASH'] = $hash;
            
            $sql = "select u.*, 
                           ps.nome as nm_pessoa, 
                           ps.email, 
                           ps.dt_nascimento, 
                           ps.tp_juridico, 
                           (case when ps.tp_juridico = 'J' then lpad(ps.cpf_cnpj, 14, 0)
                                else lpad(ps.cpf_cnpj, 11, 0)
                           end) as cpf_cnpj,
                           #ps.cpf_cnpj,
                           tp.id_tipos_pessoas,
                           tp.descricao as ds_tipos_pessoas,
                           ps.genero, 
                           ps.cep,
                           ps.logradouro,
                           ps.numero,
                           ps.complemento,
                           ps.bairro,
                           ps.cidade,
                           ps.estado,
                           ps.cod_ibge,
                           ps.telefone,
                           e.id_empresas,
                           e.nome as nm_empresa
                      from ".self::TABLE." u
                      inner join tb_pessoas ps on ps.id_pessoas = u.id_pessoas
                      inner join tb_empresas e on e.id_empresas = ps.id_empresas
                      inner join tb_tipos_pessoas tp on tp.id_tipos_pessoas = ps.id_tipos_pessoas
                     where md5(concat(u.id_usuarios,ps.email)) = :HASH";
            $res = $this->conn->select($sql, $arr);
            
            if (isset($res[0])) {
                return $this->getModelView($res[0]);
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function loadEmail($email) {
        try {
            $arr[':EMAIL'] = mb_strtolower($email);
            
            $sql = "select u.*, 
                           ps.nome as nm_pessoa, 
                           ps.email, 
                           ps.dt_nascimento, 
                           ps.tp_juridico, 
                           (case when ps.tp_juridico = 'J' then lpad(ps.cpf_cnpj, 14, 0)
                                else lpad(ps.cpf_cnpj, 11, 0)
                           end) as cpf_cnpj,
                           #ps.cpf_cnpj,
                           tp.id_tipos_pessoas,
                           tp.descricao as ds_tipos_pessoas,
                           ps.genero, 
                           ps.cep,
                           ps.logradouro,
                           ps.numero,
                           ps.complemento,
                           ps.bairro,
                           ps.cidade,
                           ps.estado,
                           ps.cod_ibge,
                           ps.telefone,
                           e.id_empresas,
                           e.nome as nm_empresa
                      from ".self::TABLE." u
                      inner join tb_pessoas ps on ps.id_pessoas = u.id_pessoas
                      inner join tb_empresas e on e.id_empresas = ps.id_empresas
                      inner join tb_tipos_pessoas tp on tp.id_tipos_pessoas = ps.id_tipos_pessoas
                     where ps.email = :EMAIL";
            $res = $this->conn->select($sql, $arr);
            
            if (isset($res[0])) {
                return $this->getModelView($res[0]);
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
            if (isset($values[':ID_USUARIOS'])) {
                unset($values[':ID_USUARIOS']);
            }
            $save = $this->conn->insert(self::TABLE, $values);
            return $save;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 1);
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

            if(isset($values[':ID_USUARIOS'])) {
                unset($values[':ID_USUARIOS']);
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
            $save = $this->conn->update(self::TABLE, array(':STATUS'=>'D'), array(':ID_USUARIOS'=>$id));
            return $save;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }*/
}
?>