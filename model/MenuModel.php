<?php
class MenuModel extends Connection {
    const TABLE = 'tb_menu';
    private $conn = false;
    private $newModel = array();

    function __construct() {
        $this->conn = new Connection();
    }

    //MODELAGEM DO BANCO
    private $fields = array(
        'id_menu'=>array('type'=>'integer', 'requered'=>true, 'max'=>10, 'key'=>true, 'description'=>'ID'),
        'nome'=>array('type'=>'string', 'requered'=>true, 'max'=>'100', 'key'=>false, 'description'=>'Nome do menu'),
        'descricao'=>array('type'=>'string', 'requered'=>false, 'max'=>'100', 'default'=>'A', 'key'=>false, 'description'=>'Descrição'),
        'link'=>array('type'=>'string', 'requered'=>false, 'max'=>'200', 'key'=>false, 'description'=>'Link da página'),
        'icone'=>array('type'=>'string', 'requered'=>false, 'max'=>'100', 'key'=>false, 'description'=>'Ícone do menu'),
        'tipo'=>array('type'=>'string', 'requered'=>true, 'max'=>'1', 'key'=>false, 'description'=>'Tipo do menu'),
        'status'=>array('type'=>'string', 'requered'=>true, 'max'=>'1', 'key'=>false, 'description'=>'Status'),
        'id_menu_principal'=>array('type'=>'integer', 'requered'=>false, 'max'=>'10', 'key'=>false, 'description'=>'Código do menu principal'),
        'ordem'=>array('type'=>'integer', 'requered'=>true, 'max'=>'2', 'key'=>false, 'description'=>'Ordem do menu na tela'),
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

    public function menuSistema($id_perfil) {
        try {

            $menu = array();

            $and = ' and pf.id_perfil = :ID_PERFIL';
            $arr[':ID_PERFIL'] = $id_perfil;

            if($id_perfil != 1) {
                $and.= " and m.status not in('I','D')";
            }

            //MENUS PRINCIPAL
            $sql = "select m.id_menu, 
                           m.nome, 
                           m.descricao, 
                           m.link, 
                           m.icone, 
                           m.status, 
                           mpp.id_permissoes, 
                           p.descricao as ds_permissoes,
                           mpp.id_perfil, 
                           pf.descricao as ds_perfil
                      from ".self::TABLE." m
                     inner join tb_menu_permissao_perfil mpp on mpp.id_menu = m.id_menu
                     inner join tb_permissoes p on p.id_permissoes = mpp.id_permissoes
                     inner join tb_perfil pf on pf.id_perfil = mpp.id_perfil  
                     where m.tipo = 'P'
                       and m.status = 'A'
                       and mpp.id_permissoes = 1
                       ".$and."
                     order by m.ordem";            
            
            $res = $this->conn->select($sql, $arr);
            if (isset($res[0])) {
                foreach ($res as $key => $value) {

                    $arr_menu_sub = array(
                        ':ID_PERFIL'=>$id_perfil, 
                        ':ID_MENU_PRINCIPAL'=>$value['id_menu']
                    );

                    //MENU SUB
                    $sql = "select m.id_menu, 
                                   m.nome,
                                   m.link,
                                   m.status, 
                                   m.icone,
                                   mpp.id_permissoes,
                                   p.descricao as ds_permissoes
                              from ".self::TABLE." m
                             inner join tb_menu_permissao_perfil mpp on mpp.id_menu = m.id_menu
                             inner join tb_permissoes p on p.id_permissoes = mpp.id_permissoes
                             inner join tb_perfil pf on pf.id_perfil = mpp.id_perfil  
                             where m.tipo = 'S'
                               and m.status = 'A'
                               and mpp.id_perfil = :ID_PERFIL
                               and m.id_menu_principal = :ID_MENU_PRINCIPAL
                             order by m.ordem";

                    $res_menu_sub = $this->conn->select($sql, $arr_menu_sub);
                    $menu_sub = array();
                    if (isset($res_menu_sub[0])) {
                        foreach ($res_menu_sub as $k => $v) {
                            $menu_sub[$v['id_menu']]['nome'] = $v['nome'];
                            $menu_sub[$v['id_menu']]['link'] = $v['link'];
                            $menu_sub[$v['id_menu']]['icone'] = $v['icone'];
                            $menu_sub[$v['id_menu']]['permissoes'][$k][] = $v['ds_permissoes'];
                        }
                    }

                    $value['menu_sub'] = $menu_sub;

                    $menu[] = $value;
                }
            }

            return $menu;

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function loadId($id) {
        try {
            $arr[':ID'] = $id;
            
            $sql = "select u.*, 
                           p.descricao as ds_perfil, 
                           ps.nome as nm_pessoa, 
                           ps.email, 
                           ps.dt_nascimento, 
                           ps.fg_pessoa, 
                           (case when ps.fg_pessoa = 'J' then lpad(ps.cpf_cnpj, 14, 0)
                                else lpad(ps.cpf_cnpj, 11, 0)
                           end) as cpf_cnpj,
                           #ps.cpf_cnpj,
                           ps.genero, e.nome as nm_empresa
                      from ".self::TABLE." u
                      inner join tb_perfil p on p.id_perfil = u.id_perfil
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
                $and.= " and m.status not in('D')";
            }
            
            $sql = "select m.*,
                           m1.nome as nm_menu_principal
                      from ".self::TABLE." m
                      left join ".self::TABLE." m1 on m1.id_menu = m.id_menu_principal                      
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

    public function loadEndPointAcesso($id_perfil='') {
        try {
            $arr = array(':ID_PERFIL'=>(empty($id_perfil)?0:$id_perfil));
            
            $sql = "select distinct m.nome, m.link
                      from tb_menu_permissao_perfil mpp
                     inner join tb_menu m on m.id_menu = mpp.id_menu
                     where mpp.id_perfil = :ID_PERFIL
                       and m.tipo = 'S'";
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