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
        // Embalagem deve aceitar NULL quando não selecionada
        'id_embalagens'=>array('type'=>'integer', 'fk'=>true, 'requered'=>false, 'max'=>'10', 'default'=>null, 'key'=>false, 'description'=>'Selecionar uma embalagem'),
        'id_materiais_marcas'=>array('type'=>'integer', 'fk'=>true, 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Selecionar uma marca'),
        // Tipo pode ficar vazio; enviaremos NULL quando não houver seleção
        'id_materiais_tipos'=>array('type'=>'integer', 'fk'=>true, 'requered'=>false, 'max'=>'10', 'default'=>null, 'key'=>false, 'description'=>'Selecionar um tipo'),
        'id_unidades_medidas'=>array('type'=>'integer', 'fk'=>true, 'requered'=>false, 'max'=>'10', 'default'=>'N', 'key'=>false, 'description'=>'Selecionar uma unidade de medida'),
        'id_materiais_categorias'=>array('type'=>'integer', 'fk'=>true, 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Selecionar uma categoria'),
        'id_empresas'=>array('type'=>'integer', 'fk'=>true, 'requered'=>false, 'max'=>'10', 'default'=>'N', 'key'=>false, 'description'=>'Selecionar uma empresa'),
        'cod_barras'=>array('type'=>'string', 'requered'=>false, 'max'=>'50', 'default'=>null, 'key'=>false, 'description'=>'Código barras'),
        'dias_vencimento'=>array('type'=>'integer', 'requered'=>false, 'max'=>'2', 'default'=>'', 'key'=>false, 'description'=>'Qtd dias vencimento'),
        'dias_vencimento_aberto'=>array('type'=>'integer', 'requered'=>false, 'max'=>'2', 'default'=>'', 'key'=>false, 'description'=>'Qtd dias vencimento aberto'),
        'temperatura'=>array('type'=>'integer', 'requered'=>false, 'max'=>'3', 'default'=>'', 'key'=>false, 'description'=>'Temperatura'),
        'sif'=>array('type'=>'integer', 'requered'=>false, 'max'=>'3', 'default'=>'', 'key'=>false, 'description'=>'SIF'),
        'nro_nota'=>array('type'=>'string', 'requered'=>false, 'max'=>'100', 'default'=>'', 'key'=>false, 'description'=>'Número da nota fiscal'),
        'id_embalagem_condicoes'=>array('type'=>'integer', 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Embalagem condições'),
        'id_modo_conservacao'=>array('type'=>'integer', 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Modo de conservação'),
        'fg_avulsa'=>array('type'=>'string', 'requered'=>false, 'max'=>'1', 'default'=>'N', 'key'=>false, 'description'=>'Origem etiqueta avulsa'),
        'id_usuarios'=>array('type'=>'integer', 'requered'=>false, 'max'=>'10', 'default'=>'', 'key'=>false, 'description'=>'Usuário'),

    );
    
    private function setFields($arr) {
        foreach ($this->fields as $key => $value) {
            $this->newModel[$key] = $value;
            $hasInput = array_key_exists($key, $arr);
            $inputVal = $hasInput ? $arr[$key] : null;
            $isZeroLike = $inputVal === 0 || $inputVal === '0' || $inputVal === 0.0;
            if ($hasInput && ($isZeroLike || !empty($inputVal))) {
                $this->newModel[$key]['value'] = $inputVal;
            } elseif (isset($value['default'])) {
                $this->newModel[$key]['value'] = $value['default'];
            } else {
                $this->newModel[$key]['value'] = null;
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

        if (!empty($data['dh_cadastro'])) {
            $data['dh_cadastro'] = dt_br($data['dh_cadastro']);
        }

        if (!empty($data['dt_vencimento'])) {
            $data['dt_vencimento'] = dt_br($data['dt_vencimento']);
        }

        if (!empty($data['dt_vencimento_aberto'])) {
            $data['dt_vencimento_aberto'] = dt_br($data['dt_vencimento_aberto']);
        }

        if (!empty($data['preco'])) {
            $data['preco_formatado'] = numberformat($data['preco']);
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
            
            $sql = "select p.*, coalesce(p1.nome, e1.nome) as nm_fabricante, p2.nome as nm_fornecedor
                      from ".self::TABLE." p
                      left join tb_pessoas p1 on p1.id_pessoas = p.id_pessoas_fabricante
                      left join tb_empresas e1 on e1.id_empresas = p1.id_empresas
                      left join tb_pessoas p2 on p2.id_pessoas = p.id_pessoas_fornecedor
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

    public function loadIdMaterialCategoria($status='', $id, $id_empresas) {
        
        try {
            $arr= array();

            $arr[':ID'] = $id;           

            $and = ' and p.id_empresas = :ID_EMPRESAS';
            $arr[':ID_EMPRESAS'] = $id_empresas;

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

    public function loadIdMaterialDetalhes($status,$id, $id_empresas) {
        
        try {
            $arr= array();

            $arr[':ID'] = $id;
            $arr[':STATUS'] = $status;

            $and = ' and p.id_empresas = :ID_EMPRESAS';
            $arr[':ID_EMPRESAS'] = $id_empresas;

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
                           ifnull(um.descricao, '') as ds_unidade_medida,
                           coalesce(p1.nome, e1.nome) as nm_fabricante,
                           p2.nome as nm_fornecedor
                      from ".self::TABLE." p
                      left join tb_etiquetas et on et.id_materiais = p.id_materiais and et.tipo_etiqueta = 'E' and et.status = 'A'
                      left join tb_materiais_marcas mm on mm.id_materiais_marcas = p.id_materiais_marcas
                      left join tb_unidades_medidas um on um.id_unidades_medidas = p.id_unidades_medidas
                      left join tb_materiais_fracionados mf on mf.id_materiais = p.id_materiais
                      left join tb_pessoas p1 on p1.id_pessoas = p.id_pessoas_fabricante
                      left join tb_empresas e1 on e1.id_empresas = p1.id_empresas
                      left join tb_pessoas p2 on p2.id_pessoas = p.id_pessoas_fornecedor
                     where p.id_materiais = :ID
                       and (
                            et.id_etiquetas is not null
                            or not exists (
                                select 1 from tb_etiquetas te
                                 where te.id_materiais = p.id_materiais
                                   and te.status = 'A'
                            )
                       )
                       and not exists (
                           select 1 from tb_etiquetas ta
                            where ta.id_materiais = p.id_materiais
                              and ta.tipo_etiqueta = 'A'
                              and ta.status = 'A'
                       )
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

            $sql = "select distinct p.*, 
                           ifnull(mm.descricao, '') as marca,
                           ifnull(um.descricao, '') as ds_unidade_medida,
                           coalesce(p1.nome, e1.nome) as nm_fabricante,
                           p2.nome as nm_fornecedor,
                           (case when datediff(p.dt_vencimento, current_date()) <= 1 then 'danger'
                                when datediff(p.dt_vencimento, current_date()) > 1 and datediff(p.dt_vencimento, current_date()) < 5 then 'primary'
                                else 'success'
                            end) as color_dt_vencimento
                      from ".self::TABLE." p
                      left join tb_etiquetas et on et.id_materiais = p.id_materiais and et.tipo_etiqueta = 'E'
                      left join tb_materiais_marcas mm on mm.id_materiais_marcas = p.id_materiais_marcas
                      left join tb_unidades_medidas um on um.id_unidades_medidas = p.id_unidades_medidas
                      left join tb_pessoas p1 on p1.id_pessoas = p.id_pessoas_fabricante
                      left join tb_empresas e1 on e1.id_empresas = p1.id_empresas
                      left join tb_pessoas p2 on p2.id_pessoas = p.id_pessoas_fornecedor
                     where p.cod_barras = :COD_BARRAS
                       and p.quantidade >= 1
                       and (
                            et.id_etiquetas is not null
                            or not exists (select 1 from tb_etiquetas te where te.id_materiais = p.id_materiais)
                       )
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

    public function loadMaterialCodBarrasNomeDetalhes($status,$filtro, $id_empresas='') {
        
        try {
            $arr= array();

            $arr[':FILTRO'] = $filtro;
            $fil_descricao = str_replace(' ','%',$filtro);
            $arr[':STATUS'] = $status;

            $and = '';

            if (!empty($id_empresas)) {
                $and.= " and p.id_empresas = :ID_EMPRESAS";
                $arr[':ID_EMPRESAS'] = $id_empresas;
            }

            if (!empty($status)) {
                $arr[':STATUS'] = $status;
                $and .= " and p.status = :STATUS";
            } else {
                $arr[':STATUS'] = 'D';
                $and .= " and p.status != :STATUS";
            }

                        // Regra ajustada: incluir materiais com pelo menos uma etiqueta tipo 'E'
                        // OU sem qualquer etiqueta (apenas em estoque). Excluir materiais que possuem
                        // somente etiquetas do tipo 'A'. Implementação usando LEFT JOIN restrito a 'E'
                        // e subconsulta NOT EXISTS para detectar ausência total de etiquetas.
                        $sql = "select distinct p.*, 
                                                     ifnull(mm.descricao, '') as marca,
                                                     ifnull(um.descricao, '') as ds_unidade_medida,
                                                     coalesce(p1.nome, e1.nome) as nm_fabricante,
                                                     p2.nome as nm_fornecedor,
                                                     (case when datediff(p.dt_vencimento, current_date()) <= 1 then 'danger'
                                                                when datediff(p.dt_vencimento, current_date()) > 1 and datediff(p.dt_vencimento, current_date()) < 5 then 'primary'
                                                                else 'success'
                                                        end) as color_dt_vencimento
                                            from ".self::TABLE." p
                                            left join tb_etiquetas e on e.id_materiais = p.id_materiais and e.tipo_etiqueta = 'E' and e.status = 'A'
                                            left join tb_materiais_marcas mm on mm.id_materiais_marcas = p.id_materiais_marcas
                                            left join tb_unidades_medidas um on um.id_unidades_medidas = p.id_unidades_medidas
                                            left join tb_pessoas p1 on p1.id_pessoas = p.id_pessoas_fabricante
                                            left join tb_empresas e1 on e1.id_empresas = p1.id_empresas
                                            left join tb_pessoas p2 on p2.id_pessoas = p.id_pessoas_fornecedor
                                         where (p.cod_barras = :FILTRO or p.descricao like '%".$fil_descricao."%')
                                             and p.quantidade >= 1
                                             and ifnull(p.fg_avulsa,'N') <> 'S'
                                             and ( e.id_etiquetas is not null 
                                                   or not exists (select 1 from tb_etiquetas t where t.id_materiais = p.id_materiais and t.status = 'A') )
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
    
    public function loadAll($status='', $id_empresas) {
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

            $arr[':ID_EMPRESAS'] = $id_empresas;
            $and .= " and p.id_empresas = :ID_EMPRESAS";
            
                        // Regra de exibição:
                        // Mostrar materiais que:
                        //   - Possuem pelo menos uma etiqueta tipo 'E' (fracionada) OU
                        //   - Não possuem nenhuma etiqueta (baixa / sem cadastro),
                        // E ocultar materiais que tenham somente etiquetas do tipo 'A' (avulsas).
                        // Implementação: excluir materiais onde existem etiquetas 'A' e não existem etiquetas 'E'.
                        $sql = "select distinct p.*, 
                                                         coalesce(p1.nome, e1.nome) as nm_fabricante, 
                                                         p2.nome as nm_fornecedor, 
                                                         mm.descricao as marca,
                                                         pr.nome as nm_responsavel
                                            from ".self::TABLE." p
                                            left join tb_etiquetas et on et.id_materiais = p.id_materiais and et.tipo_etiqueta = 'E' and et.status = 'A'
                                            left join tb_pessoas p1 on p1.id_pessoas = p.id_pessoas_fabricante
                                            left join tb_empresas e1 on e1.id_empresas = p1.id_empresas
                                            left join tb_pessoas p2 on p2.id_pessoas = p.id_pessoas_fornecedor
                                            left join tb_materiais_marcas mm on mm.id_materiais_marcas = p.id_materiais_marcas
                                            left join tb_usuarios u on u.id_usuarios = p.id_usuarios
                                            left join tb_pessoas pr on pr.id_pessoas = u.id_pessoas
                     where p.quantidade > 0
                       and (
                           et.id_etiquetas is not null
                           or not exists (
                               select 1 from tb_etiquetas te
                                where te.id_materiais = p.id_materiais
                                  and te.status = 'A'
                           )
                       )
                       and not exists (
                           select 1
                             from tb_etiquetas ta
                            where ta.id_materiais = p.id_materiais
                              and ta.tipo_etiqueta = 'A'
                              and ta.status = 'A'
                       )
                       and p.fg_avulsa <> 'S'
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

    /**
     * Lista materiais que possuem ao menos uma etiqueta do tipo 'E'.
     * Mantém mesma estrutura de loadAll adicionando o join com tb_etiquetas.
     */
    public function loadAllComEtiquetaTipoE($status='', $id_empresas) {
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

            $arr[':ID_EMPRESAS'] = $id_empresas;
            $and .= " and p.id_empresas = :ID_EMPRESAS";

                        $sql = "select distinct p.*, 
                                                         coalesce(p1.nome, e1.nome) as nm_fabricante, 
                                                         p2.nome as nm_fornecedor, 
                                                         mm.descricao as marca,
                                                         pr.nome as nm_responsavel
                                            from ".self::TABLE." p
                                            inner join tb_etiquetas e on e.id_materiais = p.id_materiais and e.tipo_etiqueta = 'E' and e.status = 'A'
                                            left join tb_pessoas p1 on p1.id_pessoas = p.id_pessoas_fabricante
                                            left join tb_empresas e1 on e1.id_empresas = p1.id_empresas
                                            left join tb_pessoas p2 on p2.id_pessoas = p.id_pessoas_fornecedor
                                            left join tb_materiais_marcas mm on mm.id_materiais_marcas = p.id_materiais_marcas
                                            left join tb_usuarios u on u.id_usuarios = p.id_usuarios
                                            left join tb_pessoas pr on pr.id_pessoas = u.id_pessoas
                    where p.quantidade > 0
                      and not exists (
                          select 1
                            from tb_etiquetas ta
                           where ta.id_materiais = p.id_materiais
                             and ta.tipo_etiqueta = 'A'
                             and ta.status = 'A'
                      )
                      and p.fg_avulsa <> 'S'
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

    public function loadMateriaisVencimento($status='',$id_acao, $id_empresas='') {
        try {
            $arr = array();
            $and = '';

            if (!empty($status)) {
                $arr[':STATUS'] = $status;
                $and .= " and p.status = :STATUS";
                $and .= " and mf.status = :STATUS";
                $and .= " and e.status = :STATUS";
            } else {
                $arr[':STATUS'] = 'D';
                $and .= " and p.status != :STATUS";
                $and .= " and mf.status != :STATUS";
                $and .= " and e.status != :STATUS";
            }

            if (!empty($id_empresas)) {
                $and.= " and p.id_empresas = :ID_EMPRESAS";
                $arr[':ID_EMPRESAS'] = $id_empresas;
            }
            
            if ($id_acao == 'btn_vencem_hoje')
                $and .= " and mf.dt_vencimento = DATE_FORMAT(CURDATE(), '%Y-%m-%d')";
            if ($id_acao == 'btn_vencem_amanha')
                $and .= " and mf.dt_vencimento = DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
            if ($id_acao == 'btn_vencem_semana')
                $and .= " and (mf.dt_vencimento BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-%d') AND DATE_ADD(CURDATE(), INTERVAL 7 DAY))";
            if ($id_acao == 'btn_vencem_mais_1_semana')
                $and .= " and mf.dt_vencimento > DATE_ADD(CURDATE(), INTERVAL 7 DAY)";

            $sql = "select p.*,
                           mc.descricao as ds_modo_consevacao,
                           e.id_etiquetas as id_etiquetas,
                           mf.id_materiais_fracionados as id_materiais_fracionados,
                           mf.qtd_fracionada,
                           mf.id_setor,
                           s.nome as nm_setor,
                           DATE_FORMAT(mf.dt_fracionamento,'%d/%m/%Y') as dt_fracionamento,
                           DATE_FORMAT(mf.dt_vencimento,'%d/%m/%Y') as dt_vencimento,
                           ifnull(mm.descricao, '') as marca,
                           ifnull(um.descricao, '') as ds_unidade_medida
                      from ".self::TABLE." p
                      inner join tb_materiais_marcas mm on mm.id_materiais_marcas = p.id_materiais_marcas
                      inner join tb_unidades_medidas um on um.id_unidades_medidas = p.id_unidades_medidas
                      inner join tb_materiais_fracionados mf on mf.id_materiais = p.id_materiais
                      inner join tb_etiquetas e on ((e.id_materiais = p.id_materiais) and (e.id_materiais_fracionados = mf.id_materiais_fracionados))
                      left join tb_setor s on s.id_setor = mf.id_setor
                      left join tb_modo_conservacao mc on mc.id = p.id_modo_conservacao
                     where 1=1
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

    public function loadQuantMateriaisVencimento($status='',$id_acao, $id_empresas) {
        try {
            $arr = array();
            $and = '';

            if (!empty($status)) {
                $arr[':STATUS'] = $status;
                $and .= " and p.status = :STATUS";
                $and .= " and mf.status = :STATUS";
                $and .= " and e.status = :STATUS";
            } else {
                $arr[':STATUS'] = 'D';
                $and .= " and p.status != :STATUS";
                $and .= " and mf.status != :STATUS";
                $and .= " and e.status != :STATUS";
            }

            $arr[':ID_EMPRESAS'] = $id_empresas;
            $and .= " and p.id_empresas = :ID_EMPRESAS";
            
            if ($id_acao == 'texto_vencem_hoje')
                $and .= " and mf.dt_vencimento = DATE_FORMAT(CURDATE(), '%Y-%m-%d')";
            if ($id_acao == 'texto_vencem_amanha')
                $and .= " and mf.dt_vencimento = DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
            if ($id_acao == 'texto_vencem_semana')
                $and .= " and (mf.dt_vencimento BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-%d') AND DATE_ADD(CURDATE(), INTERVAL 7 DAY))";
            if ($id_acao == 'texto_vencem_mais_1_semana')
                $and .= " and mf.dt_vencimento > DATE_ADD(CURDATE(), INTERVAL 7 DAY)";

            $sql = "select count(*) as quantidade
                      from ".self::TABLE." p
                      inner join tb_materiais_marcas mm on mm.id_materiais_marcas = p.id_materiais_marcas
                      inner join tb_unidades_medidas um on um.id_unidades_medidas = p.id_unidades_medidas
                      inner join tb_materiais_fracionados mf on mf.id_materiais = p.id_materiais
                      inner join tb_etiquetas e on ((e.id_materiais = p.id_materiais) and (e.id_materiais_fracionados = mf.id_materiais_fracionados))
                     where 1=1
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

    public function loadRelatorioMateriaisRecebimento($id_empresas="", $dt_ini, $dt_fim, $status='', $busca='') {
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

            if (!empty($busca)) {
                $buscaLower = function_exists('mb_strtolower') ? mb_strtolower($busca, 'UTF-8') : strtolower($busca);
                $like = '%'.$buscaLower.'%';
                $arr[':BUSCA_DESC'] = $like;
                $arr[':BUSCA_FORN'] = $like;
                $arr[':BUSCA_LOTE'] = $like;
                $arr[':BUSCA_NOTA'] = $like;
                $arr[':BUSCA_COND'] = $like;
                $arr[':BUSCA_RESP'] = $like;
                $and .= " and (
                            lower(m.descricao) like :BUSCA_DESC
                            or lower(p.nome) like :BUSCA_FORN
                            or lower(m.lote) like :BUSCA_LOTE
                            or lower(m.nro_nota) like :BUSCA_NOTA
                            or lower(ec.descricao) like :BUSCA_COND
                            or lower(p1.nome) like :BUSCA_RESP
                        )";
            }

            $sql = "select m.dh_cadastro, m.descricao, m.dt_vencimento, m.quantidade, m.temperatura, m.sif, m.lote, m.nro_nota,
                            p.nome as nm_fornecedor,
                            ec.descricao as ds_embalagem_condicoes,
                            p1.nome as nm_responsavel
                    from tb_materiais m
                    left join tb_etiquetas et on et.id_materiais = m.id_materiais and et.tipo_etiqueta = 'E' and et.status = 'A'
                    left join tb_pessoas p on p.id_pessoas = m.id_pessoas_fornecedor and p.id_tipos_pessoas = 3
                    left join tb_embalagem_condicoes ec on ec.id = m.id_embalagem_condicoes
                    left join tb_usuarios u on u.id_usuarios = m.id_usuarios
                    left join tb_pessoas p1 on p1.id_pessoas = u.id_pessoas
                    where 1 = 1
                       and (
                            et.id_etiquetas is not null
                            or not exists (
                                select 1 from tb_etiquetas te
                                 where te.id_materiais = m.id_materiais
                                   and te.status = 'A'
                            )
                       )
                       and not exists (
                            select 1 from tb_etiquetas ta
                             where ta.id_materiais = m.id_materiais
                               and ta.tipo_etiqueta = 'A'
                               and ta.status = 'A'
                       )
                       and m.fg_avulsa <> 'S'
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

   // === INÍCIO: métodos para etiqueta avulsa ===


   /**
     * AVULSA: SEM procurar existente, SEM incrementar.
     * Sempre cria um novo material com a QUANTIDADE informada.
     * Se existir fg_avulsa em tb_materiais, marca 'S'.
     */
    public static function createFromAvulsa(
    string $descricao,
    ?string $validadeIso,
    float $peso,
    ?int $id_unidades_medidas,
    ?int $id_modo_conservacao,
    int $id_empresas,
    int $id_usuarios,
    int $quantidade
    ): int {
    $pdo = $GLOBALS['pdo'];

    $cols = "descricao, peso, quantidade, status, dt_vencimento, id_unidades_medidas, id_modo_conservacao, id_empresas, id_usuarios, fg_avulsa";
    $vals = ":d, :p, :q, 'A', :v, :um, :mc, :e, :u, 'S'";
    $params = [
        ':d'  => $descricao,
        ':p'  => $peso,
        ':q'  => max(0, $quantidade),
        ':v'  => $validadeIso ?: null,
        ':um' => $id_unidades_medidas ?: null,
        ':mc' => $id_modo_conservacao ?: null,
        ':e'  => $id_empresas,
        ':u'  => $id_usuarios,
    ];


    $sqlI = "INSERT INTO tb_materiais ($cols) VALUES ($vals)";
    $stI = $pdo->prepare($sqlI);
    $stI->execute($params);

    return (int)$pdo->lastInsertId();
    }
}
?>
