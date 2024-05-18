<?php
class FornecedoresFabricantesModel extends Connection {   
    
    const TABLE = 'tb_pessoas';
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
            
            $sql = "select ps.id_pessoas,
                           ps.nome as nm_pessoa, 
                           ps.email, 
                           ps.dt_nascimento, 
                           ps.tp_juridico, 
                           (case when ps.tp_juridico = 'J' then lpad(ps.cpf_cnpj, 14, 0)
                                else lpad(ps.cpf_cnpj, 11, 0)
                           end) as cpf_cnpj,
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
                           ps.status,
                           e.id_empresas,
                           e.nome as nm_empresa
                      from ".self::TABLE." ps
                      inner join tb_empresas e on e.id_empresas = ps.id_empresas
                      inner join tb_tipos_pessoas tp on tp.id_tipos_pessoas = ps.id_tipos_pessoas
                     where ps.id_pessoas = :ID
                       and tp.id_tipos_pessoas in(2,3)";
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

    
    public function loadAll($status='') {
        try {
            $arr = array();
            $and = '';
            
            if (!empty($status)) {
                $and.= " and u.status = :STATUS";
                $arr[':STATUS'] = $status;
            } else {
                $and.= " and ps.status not in('D')";
            }
                        
            $sql = "select ps.id_pessoas,
                           ps.nome as nm_pessoa, 
                           ps.email, 
                           ps.dt_nascimento, 
                           ps.tp_juridico, 
                           (case when ps.tp_juridico = 'J' then lpad(ps.cpf_cnpj, 14, 0)
                                else lpad(ps.cpf_cnpj, 11, 0)
                           end) as cpf_cnpj,
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
                           ps.status,
                           tp.id_tipos_pessoas,
                           tp.descricao as ds_tipos_pessoas
                      from ".self::TABLE." ps
                      inner join tb_empresas e on e.id_empresas = ps.id_empresas
                      inner join tb_tipos_pessoas tp on tp.id_tipos_pessoas = ps.id_tipos_pessoas
                     where tp.id_tipos_pessoas in(2,3) 
                       ".$and."";
            $res = $this->conn->select($sql, $arr);
            
            if (isset($res[0])) {
                $arr = array();

                foreach ($res as $key => $value) {
                    $arr[] = $this->class_pessoa_model->getModelView($value);
                }

                return $arr;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function addFornecedoresFabricantes($arr) {
        $arr['genero'] = 'M';
        try {
            $this->class_pessoa_model->setFields($arr);
            $values = $this->class_pessoa_model->getFields();
            if (isset($values[':ID_PESSOAS'])) {
                unset($values[':ID_PESSOAS']);
            }
            //$save = $this->conn->insert(self::TABLE, $values);
            $save = $this->class_pessoa_model->add($arr);
            return $save;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 1);
        }
    }

    public function edit(Array $arr, Array $where){
        $arr['genero'] = 'M';
        try {
            $save = $this->class_pessoa_model->edit($arr, array('id_pessoas'=>$arr['id_pessoas']));
            return $save;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 1);
            
        }
    }

    public function del($id){
        try {
            $save = $this->class_pessoa_model->del($id);
            return $save;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 1);
        }
    }
}
?>