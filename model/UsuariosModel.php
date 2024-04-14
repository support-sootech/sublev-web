<?php
class UsuariosModel extends Connection {
    const TABLE = 'tb_usuarios';
    private $conn = false;
    private $newModel = array();

    function __construct() {
        $this->conn = new Connection();
    }

    //MODELAGEM DO BANCO
    private $fields = array(
        'id_usuarios'=>array('type'=>'integer', 'requered'=>true, 'max'=>10, 'key'=>true, 'description'=>'ID'),
        'senha'=>array('type'=>'string', 'requered'=>true, 'max'=>'32', 'key'=>false, 'description'=>'Senha'),
        'status'=>array('type'=>'string', 'requered'=>false, 'max'=>'1', 'default'=>'A', 'key'=>false, 'description'=>'status'),
        'id_perfil'=>array('type'=>'integer', 'requered'=>true, 'max'=>'10', 'key'=>false, 'description'=>'Perfil'),
        'id_pessoas'=>array('type'=>'integer', 'requered'=>true, 'max'=>'10', 'key'=>false, 'description'=>'Código da pessoa'),
    );
    
    private function setFields($arr) {
        if (count($arr) > 0) {
            /*
            foreach ($arr as $key => $value) {
                if (array_key_exists($key, $this->fields)) {
                    $this->newModel[$key] = $this->fields[$key];
                    $this->newModel[$key]['value'] = $value;
                }
            }
            */

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
                           ps.genero, e.nome as nm_empresa,
                           md5(concat(u.id_usuarios, now())) as hash_login
                      from ".self::TABLE." u
                      inner join tb_pessoas ps on ps.id_pessoas = u.id_pessoas
                      inner join tb_empresas e on e.id_empresas = ps.id_empresas
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
                                $endpoints[] = $v['link'];
                            }
                        }
                    }
                }
                
                $usuario['menu'] = $menu;
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
                           ps.genero, e.nome as nm_empresa
                      from ".self::TABLE." u
                      inner join tb_pessoas ps on ps.id_pessoas = u.id_pessoas
                      inner join tb_empresas e on e.id_empresas = ps.id_empresas
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

    public function loadAll($root=false) {
        try {
            $arr = array();
            $and = '';
            
            if (!$root) {
                $and.= " and u.status not in('D')";
            }
            
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
            if (isset($values[':ID_USUARIOS'])) {
                unset($values[':ID_USUARIOS']);
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
                $w[':'.$key.''] = $value;
            }

            if(isset($values[':ID_USUARIOS'])) {
                unset($values[':ID_USUARIOS']);
            }

            $save = $this->conn->update(self::TABLE, $values, $w);
            return $save;
        } catch (Exception $e) {
            return $e->getMessage();
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