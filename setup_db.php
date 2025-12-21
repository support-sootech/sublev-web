<?php
require_once 'config.php';
require_once 'model/Connection.php';

try {
    $conn = new Connection();
    
    $sql = "
    CREATE TABLE IF NOT EXISTS tb_catalogo_avulso (
        id INT AUTO_INCREMENT PRIMARY KEY,
        descricao VARCHAR(100) NOT NULL,
        qtde_dias_vencimento INT NOT NULL,
        peso DECIMAL(10,3),
        id_unidades_medidas INT,
        id_modo_conservacao INT,
        favorito TINYINT(1) DEFAULT 1,
        id_empresas INT NOT NULL,
        id_usuarios INT NOT NULL,
        dt_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
        dt_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        status CHAR(1) DEFAULT 'A'
        -- chaves estrangeiras serao adicionadas via alter table para evitar erros se ja existirem ou ordem de criacao
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    echo "Tentando criar tabela tb_catalogo_avulso...\n";
    $res = $conn->queryExecute($sql);
    if ($res !== TRUE) {
        die("Erro ao criar tabela: $res\n");
    }
    echo "Tabela criada (ou ja existente).\n";

    // Adicionar FKs separadamente para garantir (ignorando erro se ja existir)
    $fks = [
        "ALTER TABLE tb_catalogo_avulso ADD CONSTRAINT fk_cat_empresas FOREIGN KEY (id_empresas) REFERENCES tb_empresas(id_empresas)",
        "ALTER TABLE tb_catalogo_avulso ADD CONSTRAINT fk_cat_usuarios FOREIGN KEY (id_usuarios) REFERENCES tb_usuarios(id_usuarios)",
        "ALTER TABLE tb_catalogo_avulso ADD CONSTRAINT fk_cat_unidades FOREIGN KEY (id_unidades_medidas) REFERENCES tb_unidades_medidas(id_unidades_medidas)",
        "ALTER TABLE tb_catalogo_avulso ADD CONSTRAINT fk_cat_modo_cons FOREIGN KEY (id_modo_conservacao) REFERENCES tb_modo_conservacao(id)"
    ];

    foreach ($fks as $fkSql) {
        // try catch silencioso para FKs duplicadas
        $conn->queryExecute($fkSql); 
    }
    
    echo "Estrutura de banco atualizada com sucesso.\n";

} catch (Exception $e) {
    echo "Exceção: " . $e->getMessage() . "\n";
}
