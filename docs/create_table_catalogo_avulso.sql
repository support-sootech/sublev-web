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
    status CHAR(1) DEFAULT 'A',
    FOREIGN KEY (id_empresas) REFERENCES tb_empresas(id_empresas),
    FOREIGN KEY (id_usuarios) REFERENCES tb_usuarios(id_usuarios),
    FOREIGN KEY (id_unidades_medidas) REFERENCES tb_unidades_medidas(id_unidades_medidas),
    FOREIGN KEY (id_modo_conservacao) REFERENCES tb_modo_conservacao(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
