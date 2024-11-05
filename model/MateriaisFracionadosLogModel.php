<?php
class MateriaisFracionadosLogModel extends Connection {
    const TABLE = 'tb_materiais_fracionados_log';
    private $conn;
    private $newModel = array();

    function __construct() {
        $this->conn = new Connection();
    }

    private function getFieldsView($data) {

        if (!empty($data['dt_log'])) {
            $data['dt_log'] = dh_br($data['dt_log']);
        }

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

    public function loadIdMateriaisFrancionados($id_materiais_fracionados) {
        try {

            $arr[':ID_MATERIAIS_FRACIONADOS'] = $id_materiais_fracionados;

            $sql = "SELECT x.*,
                            (case when x.status = 'V' then 'VENDIDO'
                                when x.status = 'C' then 'VENCIDO'
                                when x.status = 'D' then 'DESCARTADO'
                                else 'ATIVO'
                            end) as ds_status,
                            (case when x.status = 'V' then 'warning'
                                when x.status = 'C' then 'danger'
                                when x.status = 'D' then 'secondary'
                                else 'success'
                            end) as label_status,
                            m.descricao as ds_materiais,
                            um.descricao as ds_unidade_medida,
                            p.nome as nm_usuario
                    FROM ".self::TABLE." x
                    inner join tb_materiais m on m.id_materiais = x.id_materiais
                    inner join tb_unidades_medidas um on um.id_unidades_medidas = x.id_unidades_medidas
                    inner join tb_usuarios u on u.id_usuarios = x.id_usuarios
                    inner join tb_pessoas p on p.id_pessoas = u.id_pessoas
                    where x.id_materiais_fracionados = :ID_MATERIAIS_FRACIONADOS
                    order by x.id_log desc";
            
            $res = $this->conn->select($sql, $arr);
            
            if (isset($res[0])) {
                $data = array();
                foreach ($res as $key => $value) {
                    $data[] = $this->getFieldsView($value);
                }
                return $data;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw $e->getMessage();
        }
    }

    
}
?>