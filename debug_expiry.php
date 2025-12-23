<?php
// Simula o ambiente da API
require_once 'model/Connection.php';
require_once 'model/MateriaisModel.php';
require_once 'model/EtiquetasModel.php';
require_once 'model/MateriaisFracionadosModel.php';

// Funções de data usadas no projeto
date_default_timezone_set('America/Sao_Paulo');

function dt_banco($d)
{
    return $d;
} // Mock simples
function toUpperCase($s)
{
    return strtoupper($s);
}

// Configuração manual do PDO (usando credenciais do config.php se possível, ou injetadas)
// ATENÇÃO: O código abaixo assume que config.php define as constantes ou $GLOBALS['pdo']
// Se não, precisaremos incluir o config.php real.
require_once 'config.php';
require_once '_pdo_boot.php'; // Se existir, ou conectar manualmente

echo "--- Debug Expiry Logic ---\n";
echo "Hoje (PHP): " . date('Y-m-d H:i:s') . "\n";
echo "Hoje (DB - CURDATE): ";

// Check DB date
try {
    $stm = $GLOBALS['pdo']->query("SELECT CURDATE(), NOW()");
    $row = $stm->fetch(PDO::FETCH_ASSOC);
    echo $row['CURDATE()'] . " / " . $row['NOW()'] . "\n";
} catch (Exception $e) {
    echo "Erro DB: " . $e->getMessage() . "\n";
}

echo "\n--- Simulando filtro 'btn_vencem_semana' ---\n";
// Lógica copiada de MateriaisModel
$dt_ini = date('Y-m-d', strtotime('+2 days'));
$dt_fim = date('Y-m-d', strtotime('+7 days'));

echo "Intervalo PHP (+2 a +7): $dt_ini ate $dt_fim\n";

// Vamos buscar a etiqueta 837 especificamente para ver o estado dela
echo "\n--- Dados da Etiqueta 837 ---\n";
$sql = "
SELECT 
    e.num_etiqueta, e.status as status_etiqueta, e.tipo_etiqueta,
    m.descricao, m.dt_vencimento as dt_venc_material,
    mf.dt_vencimento as dt_venc_fracionado,
    mf.dt_fracionamento
FROM tb_etiquetas e
LEFT JOIN tb_materiais m ON m.id_materiais = e.id_materiais
LEFT JOIN tb_materiais_fracionados mf ON mf.id_materiais_fracionados = e.id_materiais_fracionados
WHERE e.num_etiqueta = :num OR e.id_etiquetas = :id
";

try {
    $stm = $GLOBALS['pdo']->prepare($sql);
    // Tenta buscar por num_etiqueta ou ID (caso 837 seja ID)
    $stm->execute([':num' => '837', ':id' => 837]);
    $res = $stm->fetchAll(PDO::FETCH_ASSOC);

    if (empty($res)) {
        echo "NENHUM REGISTRO ENCONTRADO PARA 837.\n";
    } else {
        foreach ($res as $r) {
            print_r($r);

            $venc = $r['dt_venc_fracionado'] ?: $r['dt_venc_material'];
            echo "Vencimento considerado: $venc\n";

            if ($venc >= $dt_ini && $venc <= $dt_fim) {
                echo "RESULTADO: DEVERIA APARECER! Está dentro do intervalo.\n";
            } else {
                echo "RESULTADO: NÃO APARECE. Fora do intervalo (Entre $dt_ini e $dt_fim).\n";
            }

            if ($r['status_etiqueta'] != 'A') {
                echo "ALERTA: Status da etiqueta não é 'A' (Ativo), é '{$r['status_etiqueta']}'. Isso esconde ela.\n";
            }
        }
    }

} catch (Exception $e) {
    echo "Erro Query: " . $e->getMessage() . "\n";
}
?>