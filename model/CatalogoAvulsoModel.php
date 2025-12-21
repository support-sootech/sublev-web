<?php
class CatalogoAvulsoModel extends Connection
{
    const TABLE = 'tb_catalogo_avulso';
    private $conn;
    private $newModel = array();

    function __construct()
    {
        $this->conn = new Connection();
    }

    // MODELAGEM DO BANCO
    private $fields = array(
        'id' => array('type' => 'integer', 'requered' => true, 'max' => 10, 'key' => true, 'description' => 'ID'),
        'descricao' => array('type' => 'string', 'requered' => true, 'max' => '100', 'default' => '', 'key' => false, 'description' => 'Descrição'),
        'qtde_dias_vencimento' => array('type' => 'integer', 'requered' => true, 'max' => '5', 'default' => '0', 'key' => false, 'description' => 'Dias Vencimento'),
        'peso' => array('type' => 'double', 'requered' => false, 'max' => '10', 'default' => '0.000', 'key' => false, 'description' => 'Peso'),
        'id_unidades_medidas' => array('type' => 'integer', 'fk' => true, 'requered' => true, 'max' => '10', 'default' => null, 'key' => false, 'description' => 'Unidade de Medida'),
        'id_modo_conservacao' => array('type' => 'integer', 'fk' => true, 'requered' => true, 'max' => '10', 'default' => null, 'key' => false, 'description' => 'Modo Conservação'),
        'favorito' => array('type' => 'integer', 'requered' => false, 'max' => '1', 'default' => '1', 'key' => false, 'description' => 'Favorito'),

        'id_empresas' => array('type' => 'integer', 'fk' => true, 'requered' => true, 'max' => '10', 'default' => null, 'key' => false, 'description' => 'Empresa'),
        'id_usuarios' => array('type' => 'integer', 'fk' => true, 'requered' => true, 'max' => '10', 'default' => null, 'key' => false, 'description' => 'Usuário'),

        'status' => array('type' => 'string', 'requered' => false, 'max' => '1', 'default' => 'A', 'key' => false, 'description' => 'Status')
    );

    private function setFields($arr)
    {
        foreach ($this->fields as $key => $value) {
            $this->newModel[$key] = $value;
            // Verifica se a chave existe no array de entrada
            if (array_key_exists($key, $arr)) {
                $this->newModel[$key]['value'] = $arr[$key];
            } elseif (isset($value['default'])) {
                // Se nao veio, usa default
                $this->newModel[$key]['value'] = $value['default'];
            } else {
                $this->newModel[$key]['value'] = null;
            }
        }
    }

    private function getFields($fgRemoveKey = true)
    {
        $arr = array();

        if (count($this->newModel) > 0) {
            foreach ($this->newModel as $key => $value) {

                $campo = (isset($value['description']) && !empty($value['description']) ? $value['description'] : $key);

                if ($value['requered'] == true && ($value['value'] === null || $value['value'] === '') && !$value['key']) {
                    throw new Exception('O campo ' . $campo . ' não pode ser vazio!');
                }

                if (isset($value['max']) && $value['value'] !== null && $value['max'] < strlen((string) $value['value'])) {
                    throw new Exception('O campo ' . $campo . ' deve conter no máximo ' . $value['max'] . ' caracter(es)!');
                }

                if ($value['type'] == 'integer' && !$value['key']) {
                    if ($value['value'] !== null) {
                        $value['value'] = intval($value['value']);
                        if ((isset($value['fk']) && $value['fk']) && empty($value['value']) && $value['requered']) {
                            throw new Exception('O campo ' . $value['description'] . ' deve ser selecionado.');
                        }
                    }
                }

                if ($value['type'] == 'double' && $value['value'] !== null) {
                    // Assume-se que ja venha formatado ou conversivel para float
                    // numberFormatBanco geralmente converte virgula pra ponto
                    $value['value'] = numberFormatBanco($value['value']);
                }

                if (isset($value['key']) == true && $fgRemoveKey == true) {
                    unset($this->newModel[$key]);
                } else {
                    $arr[':' . toUpperCase($key) . ''] = $value['value'];
                }
            }
        }
        return $arr;
    }

    // Lista para o APP / Tela de consulta
    // Ordena por data atualizacao desc (ultimos usados primeiro)
    public function loadAll($filtro = '', $id_empresas)
    {
        try {
            $arr = array();
            $arr[':ID_EMPRESAS'] = $id_empresas;

            $whereFiltro = "";
            if (!empty($filtro)) {
                $buscaLower = function_exists('mb_strtolower') ? mb_strtolower($filtro, 'UTF-8') : strtolower($filtro);
                $arr[':BUSCA'] = '%' . $buscaLower . '%';
                // Filtro Expandido: Descricao OR (Peso + Unidade) OR Modo
                // Ex: "5kg", "Congelado", "Picanha"
                $whereFiltro = " AND (
                    lower(c.descricao) LIKE :BUSCA 
                    OR lower(IFNULL(mc.descricao, '')) LIKE :BUSCA
                    OR lower(CONCAT(REPLACE(CAST(c.peso AS CHAR), '.000', ''), IFNULL(um.descricao, ''))) LIKE :BUSCA
                    OR lower(CONCAT(REPLACE(CAST(c.peso AS CHAR), '.000', ''), ' ', IFNULL(um.descricao, ''))) LIKE :BUSCA
                ) ";
            }

            $sql = "SELECT c.*,
                           um.descricao as ds_unidade_medida,
                           mc.descricao as ds_modo_conservacao
                      FROM " . self::TABLE . " c
                      LEFT JOIN tb_unidades_medidas um ON um.id_unidades_medidas = c.id_unidades_medidas
                      LEFT JOIN tb_modo_conservacao mc ON mc.id = c.id_modo_conservacao
                     WHERE c.id_empresas = :ID_EMPRESAS
                       AND c.status = 'A'
                       " . $whereFiltro . "
                     ORDER BY c.favorito DESC, c.dt_atualizacao DESC";

            $res = $this->conn->select($sql, $arr);
            return isset($res[0]) ? $res : array();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function loadById($id)
    {
        try {
            $arr = array(':ID' => $id);
            $sql = "SELECT * FROM " . self::TABLE . " WHERE id = :ID";
            $res = $this->conn->select($sql, $arr);
            return isset($res[0]) ? $res[0] : false;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function add($arr)
    {
        $this->setFields($arr);
        $values = $this->getFields();
        // Remove :ID se existir pois é auto increment
        if (isset($values[':ID']))
            unset($values[':ID']);

        $save = $this->conn->insert(self::TABLE, $values);
        return $save;
    }

    public function edit(array $arr, array $where)
    {
        $this->setFields($arr);
        $values = $this->getFields(false); // false pra manter key se precisar ou nao? Edit geralmente nao manda key no set

        // Ajuste: remove key do values de set
        $keyName = ':ID';
        if (isset($values[$keyName]))
            unset($values[$keyName]);

        $w = array();
        foreach ($where as $key => $value) {
            $w[':' . toUpperCase($key) . ''] = $value;
        }

        $save = $this->conn->update(self::TABLE, $values, $w);
        return $save;
    }

    public function del($id)
    {
        try {
            $save = $this->conn->update(
                self::TABLE,
                array(':STATUS' => 'D'),
                array(':ID' => $id)
            );
            return $save;
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Metodo helper para toggle favorito
    public function toggleFavorito($id, $isFavorito)
    {
        // se era favorito (1), vira nao favorito (0).
        // mas aqui vamos receber o ESTADO DESEJADO ou apenas inverter?
        // O usuario pediu pra clicar e favoritar/desfavoritar. O app manda o estado novo.
        $val = $isFavorito ? 1 : 0;
        return $this->conn->update(
            self::TABLE,
            array(':FAVORITO' => $val),
            array(':ID' => $id)
        );
    }
}
