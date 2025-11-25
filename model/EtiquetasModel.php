<?php

// garanta o include do fracionados (usa addUnit com qtd_fracionada = peso)
if (!class_exists('MateriaisFracionadosModel')) {
  require_once __DIR__.'/MateriaisFracionadosModel.php';
}

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
        'descricao'=>array('type'=>'string', 'requered'=>false, 'max'=>'100', 'default'=>'', 'key'=>false, 'description'=>'Descrição'),
        'codigo'=>array('type'=>'string', 'requered'=>false, 'max'=>'2000', 'default'=>'', 'key'=>false, 'description'=>'QR Code'),
        'id_materiais_fracionados'=>array('type'=>'integer', 'fk'=>true, 'requered'=>true, 'max'=>'11', 'default'=>'', 'key'=>false, 'description'=>'Material Fracionado'),
        'id_materiais'=>array('type'=>'integer', 'fk'=>true, 'requered'=>true, 'max'=>'11', 'default'=>'', 'key'=>false, 'description'=>'Material'),
        'status'=>array('type'=>'string', 'requered'=>false, 'max'=>'1', 'default'=>'A', 'key'=>false, 'description'=>'status'),
        // Padrão do banco continua "E" (etiqueta padrão). Para avulsas forçamos "A" durante o insert.
        'tipo_etiqueta'=>array('type'=>'string', 'requered'=>false, 'max'=>'1', 'default'=>'E', 'key'=>false, 'description'=>'Tipo (A/E)'),
        'id_usuarios'=>array('type'=>'integer', 'fk'=>true, 'requered'=>true, 'max'=>'11', 'default'=>'', 'key'=>false, 'description'=>'Usuário'),
        'id_empresas'=>array('type'=>'integer', 'fk'=>true, 'requered'=>true, 'max'=>'11', 'default'=>'', 'key'=>false, 'description'=>'ID EMPRESAS'),
        'num_etiqueta'=>array('type'=>'integer','requered'=>false,'max'=>20,'default'=>null,'key'=>false,'description'=>'Número Etiqueta')
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
                where e.id_etiquetas = :ID_ETIQUETAS and e.status = 'A'";
        $res = $this->conn->select($sql, $arr);

        if (isset($res[0])) {
            $data = $this->getFieldsView($res[0]);
            $data['nm_pessoa_abreviado'] = (!empty($data['nm_pessoa']) ? abreviarNome($data['nm_pessoa']) : '');
            return $data;
        } else {
            return false;
        }
        
    }

    public function loadNumEtiquetaInfo($num_etiqueta) {

        $arr[':NUM_ETIQUETA'] = $num_etiqueta;
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
                where e.num_etiqueta = :NUM_ETIQUETA and e.status = 'A'";
        $res = $this->conn->select($sql, $arr);

       if (isset($res[0])) {
            $data = $this->getFieldsView($res[0]);
            $data['nm_pessoa_abreviado'] = (!empty($data['nm_pessoa']) ? abreviarNome($data['nm_pessoa']) : '');
            return $data;
        } else {
            return false;
        }

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

    public function loadEtiquetasIdUsuarios($id_usuarios, $id_empresas) {
        try {
            $arr[':ID_USUARIOS'] = $id_usuarios;
            $arr[':ID_EMPRESAS'] = $id_empresas;
            
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
                      and e.id_empresas = :ID_EMPRESAS
                      and e.status = 'A'
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
            die('ERROR: '.$e->getMessage());
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

    /** (não usado neste fluxo, mantido p/ referência) */
    private static function nextNumEtiquetaLocked(\PDO $pdo, int $id_empresas): int {
        $st  = $pdo->prepare("
            SELECT num_etiqueta
            FROM tb_etiquetas
            WHERE id_empresas = :EMP
            ORDER BY num_etiqueta DESC
            LIMIT 1 FOR UPDATE");
        $st->execute([':EMP'=>$id_empresas]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        return isset($row['num_etiqueta']) ? ((int)$row['num_etiqueta'] + 1) : 1;
    }

    /**
     * Busca etiquetas por IDs (LEFT JOINs pois podem ser avulsas).
     */
    public static function buscarPorIds(array $ids): array {
        if (empty($ids)) return [];
        $pdo = $GLOBALS['pdo'];

        $in = implode(',', array_map('intval', $ids));
        $sql = "
        SELECT
            e.id_etiquetas,
            e.descricao,
            e.codigo,
            e.id_materiais_fracionados,
            e.id_materiais,
            e.status,
            e.id_usuarios,
            m.descricao AS ds_material,
            m.lote,
            DATE_FORMAT(m.dt_fabricacao, '%Y-%m-%d') AS dt_fabricacao,
            DATE_FORMAT(m.dt_fabricacao, '%d/%m/%Y') AS dt_fabricacao_reduzido,
            m.cod_barras,
            COALESCE(umf.descricao, umm.descricao) AS ds_unidades_medidas,
            COALESCE(mc.descricao, '') AS ds_modo_conservacao,
            mf.qtd_fracionada,
            DATE_FORMAT(mf.dt_fracionamento, '%Y-%m-%d') AS dt_fracionamento,
            DATE_FORMAT(mf.dt_fracionamento, '%d/%m/%Y') AS dt_fracionamento_reduzido,
            DATE_FORMAT(m.dt_vencimento, '%Y-%m-%d') AS dt_vencimento,
            DATE_FORMAT(m.dt_vencimento, '%d/%m/%Y') AS dt_vencimento_reduzido,
            p.nome AS nm_pessoa,
            s.nome AS nm_setor,
            e.num_etiqueta
        FROM tb_etiquetas e
        LEFT JOIN tb_materiais_fracionados mf ON mf.id_materiais_fracionados = e.id_materiais_fracionados
        LEFT JOIN tb_materiais m              ON m.id_materiais = e.id_materiais
        LEFT JOIN tb_unidades_medidas umm     ON umm.id_unidades_medidas = m.id_unidades_medidas
        LEFT JOIN tb_unidades_medidas umf     ON umf.id_unidades_medidas = mf.id_unidades_medidas
        LEFT JOIN tb_modo_conservacao mc      ON mc.id = m.id_modo_conservacao
        LEFT JOIN tb_usuarios u               ON u.id_usuarios = mf.id_usuarios
        LEFT JOIN tb_pessoas p                ON p.id_pessoas = u.id_pessoas
        LEFT JOIN tb_setor s                  ON s.id_setor = mf.id_setor
        WHERE e.id_etiquetas IN ($in)
        ORDER BY e.id_etiquetas DESC";
        $st = $pdo->query($sql);
        $rows = $st ? $st->fetchAll(\PDO::FETCH_ASSOC) : [];

        // Pós-processamento para compatibilidade com outros endpoints
        if ($rows) {
            foreach ($rows as &$r) {
                // Responsável abreviado
                if (!empty($r['nm_pessoa'])) {
                    $r['nm_pessoa_abreviado'] = abreviarNome($r['nm_pessoa']);
                } else {
                    $r['nm_pessoa_abreviado'] = '';
                }
                // Quantidade
                if (isset($r['qtd_fracionada']) && $r['qtd_fracionada'] !== null && $r['qtd_fracionada'] !== '') {
                    $r['qtd_fracionada'] = numberformat($r['qtd_fracionada'], false);
                }
                // Datas no padrão dd/mm/aaaa como na tela de fracionar
                if (!empty($r['dt_fabricacao'])) {
                    $r['dt_fabricacao_reduzido'] = dt_br($r['dt_fabricacao'], false);
                    $r['dt_fabricacao'] = dt_br($r['dt_fabricacao']);
                }
                if (!empty($r['dt_vencimento'])) {
                    $r['dt_vencimento_reduzido'] = dt_br($r['dt_vencimento'], false);
                    $r['dt_vencimento'] = dt_br($r['dt_vencimento']);
                }
                if (!empty($r['dt_fracionamento'])) {
                    $r['dt_fracionamento_reduzido'] = dt_br($r['dt_fracionamento'], false);
                    $r['dt_fracionamento'] = dt_br($r['dt_fracionamento']);
                }
            }
            unset($r);
        }
        return $rows ?: [];
    }

    /**
     * Próximo número sequencial por empresa.
     * IMPORTANTE: chamar dentro de uma transação aberta (beginTransaction/commit)
     * para garantir exclusão e evitar colisões em alta concorrência.
     */
    private static function nextNumEtiqueta(int $id_empresas): int {
        $pdo = $GLOBALS['pdo'];

        // trava as linhas desta empresa até o COMMIT
        $st = $pdo->prepare("
            SELECT COALESCE(MAX(num_etiqueta), 0) AS last
              FROM tb_etiquetas
             WHERE id_empresas = :EMP
             FOR UPDATE
        ");
        $st->execute([':EMP' => $id_empresas]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);

        return ((int)$row['last']) + 1;
    }

    /**
     * Gera N etiquetas avulsas para um material já criado.
     * Agora recebe $qtd_por_etiqueta (peso) e grava como qtd_fracionada.
     */
    public static function inserirAvulsasParaMaterial(
        int $id_materiais,
        int $quantidade,
        string $descricao,
        int $id_usuarios,
        int $id_empresas,
        ?int $id_setor = null,
        ?int $id_unidades_medidas = null,
        ?float $qtd_por_etiqueta = null  
    ): array {
        $pdo = $GLOBALS['pdo'];

        // Lê validade e unidade do material sob lock (requer transação aberta)
        $st = $pdo->prepare("
            SELECT 
                id_unidades_medidas,
                DATE_FORMAT(dt_vencimento, '%Y-%m-%d') AS dt_vencimento
            FROM tb_materiais
            WHERE id_materiais = :m
            FOR UPDATE
        ");
        $st->execute([':m' => $id_materiais]);
        $mat = $st->fetch(PDO::FETCH_ASSOC);
        if (!$mat) {
            throw new \RuntimeException('Material não encontrado para gerar etiquetas avulsas.');
        }

        $um_from_mat = $mat['id_unidades_medidas'] ? (int)$mat['id_unidades_medidas'] : null;
        $dt_v        = $mat['dt_vencimento'] ?: null;
        $um_final    = $id_unidades_medidas ?? $um_from_mat;

        $ids = [];
        for ($i = 0; $i < max(0, (int)$quantidade); $i++) {
            $num = self::nextNumEtiqueta($id_empresas);

            
            $id_mf = MateriaisFracionadosModel::addUnit(
                $id_materiais,
                $dt_v,
                $id_usuarios,
                $id_setor,             
                $um_final,             
                $qtd_por_etiqueta      
            );

            // etiqueta tipo 'A'
            $id_e = self::criarAvulsaUnit(
                $id_materiais,
                $id_mf,
                $descricao,
                $id_usuarios,
                $id_empresas,
                $num
            );

            $ids[] = $id_e;
        }
        return $ids;
    }

    /**
     * Wrapper compatível, agora com $qtd_por_etiqueta opcional
     */
    public static function inserirEtiquetasAvulsas(
        int $idMaterial,
        int $quantidade,
        ?string $ean,
        string $descricao,
        int $idUsuario,
        int $idEmpresa,
        ?int $id_setor = null,
        ?int $id_unidades_medidas = null,
        ?float $qtd_por_etiqueta = null   // <<--- NOVO
    ): array {
        return self::inserirAvulsasParaMaterial(
            id_materiais:        $idMaterial,
            quantidade:          $quantidade,
            descricao:           $descricao,
            id_usuarios:         $idUsuario,
            id_empresas:         $idEmpresa,
            id_setor:            $id_setor,
            id_unidades_medidas: $id_unidades_medidas,
            qtd_por_etiqueta:    $qtd_por_etiqueta
        );
    }

    public static function criarAvulsaUnit(
        int $id_materiais,
        int $id_materiais_fracionados,
        string $descricao,
        int $id_usuarios,
        int $id_empresas,
        ?int $num_etiqueta = null
    ): int {
        $pdo = $GLOBALS['pdo'];

        $cols = "id_materiais, id_materiais_fracionados, descricao, id_empresas, id_usuarios, status, tipo_etiqueta";
        $vals = ":m, :mf, :d, :e, :u, 'A', 'A'";

        if ($num_etiqueta !== null) {
            $cols .= ", num_etiqueta";
            $vals .= ", :n";
        }

        $sql = "INSERT INTO tb_etiquetas ($cols) VALUES ($vals)";
        $st = $pdo->prepare($sql);
        $params = [
            ':m'  => $id_materiais,
            ':mf' => $id_materiais_fracionados,
            ':d'  => $descricao,
            ':e'  => $id_empresas,
            ':u'  => $id_usuarios,
        ];
        if ($num_etiqueta !== null) {
            $params[':n'] = $num_etiqueta;
        }
        $st->execute($params);

        return (int)$pdo->lastInsertId();
    }
}
