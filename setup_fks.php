<?php
require_once 'config.php';
require_once 'model/Connection.php';

$conn = new Connection();

$fks = [
    "ALTER TABLE tb_catalogo_avulso ADD CONSTRAINT fk_cat_empresas FOREIGN KEY (id_empresas) REFERENCES tb_empresas(id_empresas)",
    "ALTER TABLE tb_catalogo_avulso ADD CONSTRAINT fk_cat_usuarios FOREIGN KEY (id_usuarios) REFERENCES tb_usuarios(id_usuarios)",
    "ALTER TABLE tb_catalogo_avulso ADD CONSTRAINT fk_cat_unidades FOREIGN KEY (id_unidades_medidas) REFERENCES tb_unidades_medidas(id_unidades_medidas)",
    "ALTER TABLE tb_catalogo_avulso ADD CONSTRAINT fk_cat_modo_cons FOREIGN KEY (id_modo_conservacao) REFERENCES tb_modo_conservacao(id)"
];

foreach ($fks as $sql) {
    echo "Executando FK...\n";
    $res = $conn->queryExecute($sql);
    if ($res !== TRUE) {
        // Se der erro, pode ser que ja exista. Imprimir e continuar.
        echo "Aviso (pode ser ignorado se FK ja existir): " . strip_tags($res) . "\n";
    } else {
        echo "FK criada com sucesso.\n";
    }
}
