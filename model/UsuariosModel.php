<?php
class UsuariosModel extends Connection {
    const TABLE = 'tb_usuarios';
    private $conn;
    private $newModel = array();

    function __construct() {
        $this->conn = new Connection();
    }

    //MODELAGEM DO BANCO
    private $fields = array(
        'id_usuarios'=>array('type'=>'integer', 'requered'=>true, 'max'=>10, 'key'=>true, 'description'=>'ID'),
        'senha'=>array('type'=>'string', 'requered'=>false, 'max'=>'50', 'key'=>false, 'description'=>'Senha'),
        'status'=>array('type'=>'string', 'requered'=>false, 'max'=>'1', 'default'=>'A', 'key'=>false, 'description'=>'status'),
        'id_pessoas'=>array('type'=>'integer', 'requered'=>true, 'max'=>'10', 'key'=>false, 'description'=>'Código da pessoa'),
        'id_setor'=>array('type'=>'integer', 'requered'=>true, 'max'=>'10', 'key'=>false, 'description'=>'Setor'),
    );
    
    private function setFields($arr) {
        if (count($arr) > 0) {

            //VALIDA A SENHA
            if (isset($arr['id_usuarios']) && !empty($arr['id_usuarios'])) {
                $usuario = $this->loadId($arr['id_usuarios']);
                if ($usuario) {
                    $arr['senha'] = $arr['senha'] != $usuario['senha'] ? md5($arr['senha']) : $usuario['senha'];
                } else {
                    $arr['senha'] = md5($arr['senha']);
                }
            } else {
                if (isset($arr['senha']) && !empty($arr['senha'])) {
                    $arr['senha'] = md5($arr['senha']);
                } else {
                    $arr['senha'] = '';
                }
            }

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

                if (isset($value['key'])==true && $fgRemoveKey==true) {
                    unset($this->newModel[$key]);
                }

                $arr[':'.mb_strtoupper($key).''] = $value['value'];
            }
        }

        return $arr;
    }

    private function getModelView($arr) {
        
        if (isset($arr['cpf_cnpj']) && !empty($arr['cpf_cnpj'])) {
            $arr['cpf_cnpj'] = mascaraCpfCnpj($arr['tp_juridico'], $arr['cpf_cnpj']);
        }

        if (isset($arr['dt_nascimento']) && !empty($arr['dt_nascimento'])) {
            $arr['dt_nascimento'] = dt_br($arr['dt_nascimento']);
        }

        if (isset($arr['cep']) && !empty($arr['cep'])) {
            $arr['cep'] = mascaraCep($arr['cep']);
        }
        
        if (isset($arr['telefone']) && !empty($arr['telefone'])) {
            $arr['telefone'] = mascaraTelefone($arr['telefone']);
        }

        return $arr;
    }

    public function login($cpf_cnpj, $senha) {
        try {
            $arr[':CPF_CNPJ'] = limpa_numero($cpf_cnpj);
            $arr[':SENHA'] = $senha;
            
            $sql = "select u.*, 
                           ps.nome as nm_pessoa, 
                           ps.email, 
                           ps.dt_nascimento, 
                           ps.tp_juridico, 
                           (case when ps.tp_juridico = 'J' then lpad(ps.cpf_cnpj, 14, 0)
                                else lpad(ps.cpf_cnpj, 11, 0)
                           end) as cpf_cnpj,
                           #ps.cpf_cnpj,
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
                           e.nome as nm_empresa,
                           tp.id_tipos_pessoas,
                           tp.descricao as ds_tipos_pessoas,
                           md5(concat(u.id_usuarios, now())) as hash_login,
                           s.id_setor,
                           s.nome as nm_setor
                      from ".self::TABLE." u
                      inner join tb_pessoas ps on ps.id_pessoas = u.id_pessoas
                      inner join tb_empresas e on e.id_empresas = ps.id_empresas
                      inner join tb_tipos_pessoas tp on tp.id_tipos_pessoas = ps.id_tipos_pessoas
                      left join tb_setor s on s.id_setor = u.id_setor
                     where u.senha = :SENHA
                       and ps.cpf_cnpj = :CPF_CNPJ";
            $res = $this->conn->select($sql, $arr);
            
            if (isset($res[0])) {

                $usuario = $res[0];
                $class_usuarios_perfil = new UsuariosPerfilModel();
                $usuarios_perfil = $class_usuarios_perfil->loadGeralUsuarios($usuario['id_usuarios']);
                if ($usuarios_perfil) {
                    foreach ($usuarios_perfil as $key => $value) {
                        $usuario['perfil'][] = array('id_perfil'=>$value['id_perfil'], 'ds_perfil'=>$value['ds_perfil']);
                    }
                }

                $class_menu = new MenuModel();
                $menu = array();
                $arr_menu = array();
                $endpoints = array();
                if (isset($usuario['perfil']) && count($usuario['perfil'])>0) {
                    foreach ($usuario['perfil'] as $key => $value) {                        
                        //MENU
                        $m = $class_menu->menuSistema($value['id_perfil']);
                        if ($m) {
                            $menu[] = $m;
                        }

                        //ENDPOINTS
                        $arr_endpoints = $class_menu->loadEndPointAcesso($value['id_perfil']);
                        if ($arr_endpoints) {
                            foreach ($arr_endpoints as $k => $v) {
                                //$endpoints[$v['link']] = $v;
                                $endpoints[$k] = $v;
                            }
                        }
                    }
                }

                //REMOVE OS MENUS REPETIDOS POR CAUSA DO PERFIL
                $arr_menu = array();
                foreach ($menu as $key => $value) {
                    foreach ($value as $k => $v) {
                        $arr_menu[$v['id_menu']] = $v;
                    }
                }

                $usuario['menu'] = $arr_menu;
                $usuario['endpoints'] = $endpoints;

                return $usuario;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
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
                           e.nome as nm_empresa,
                           s.id_setor,
                           s.nome as nm_setor
                      from ".self::TABLE." u
                      inner join tb_pessoas ps on ps.id_pessoas = u.id_pessoas
                      inner join tb_empresas e on e.id_empresas = ps.id_empresas
                      inner join tb_tipos_pessoas tp on tp.id_tipos_pessoas = ps.id_tipos_pessoas
                      left join tb_setor s on s.id_setor = u.id_setor
                     where u.id_usuarios = :ID";
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
                           tp.descricao as ds_tipos_pessoas,
                           s.id_setor,
                           s.nome as nm_setor
                      from ".self::TABLE." u
                      inner join tb_pessoas ps on ps.id_pessoas = u.id_pessoas
                      inner join tb_empresas e on e.id_empresas = ps.id_empresas
                      inner join tb_tipos_pessoas tp on tp.id_tipos_pessoas = ps.id_tipos_pessoas
                      left join tb_setor s on s.id_setor = u.id_setor
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
                           e.nome as nm_empresa,
                           s.id_setor,
                           s.nome as nm_setor
                      from ".self::TABLE." u
                      inner join tb_pessoas ps on ps.id_pessoas = u.id_pessoas
                      inner join tb_empresas e on e.id_empresas = ps.id_empresas
                      inner join tb_tipos_pessoas tp on tp.id_tipos_pessoas = ps.id_tipos_pessoas
                      left join tb_setor s on s.id_setor = u.id_setor
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
                           e.nome as nm_empresa,
                           s.id_setor,
                           s.nome as nm_setor
                      from ".self::TABLE." u
                      inner join tb_pessoas ps on ps.id_pessoas = u.id_pessoas
                      inner join tb_empresas e on e.id_empresas = ps.id_empresas
                      inner join tb_tipos_pessoas tp on tp.id_tipos_pessoas = ps.id_tipos_pessoas
                      left join tb_setor s on s.id_setor = u.id_setor
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
    }
}
?>