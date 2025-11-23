<?php
class EtiquetasController {
  public function criarAvulsa(array $data, int $idUsuario, int $idEmpresa): array {
    $pdo = $GLOBALS['pdo'];
    try {
      $pdo->beginTransaction();

      $descricao  = trim((string)($data['descricao'] ?? ''));
      $quant      = (int)($data['quantidade'] ?? 0);
      $validade   = $data['validade'] ?? null; // 'YYYY-MM-DD' ou null
      $peso       = isset($data['peso']) ? (float)$data['peso'] : 0.0;
      $idUM       = isset($data['id_unidades_medidas']) ? (int)$data['id_unidades_medidas'] : null;
      $idMC       = isset($data['id_modo_conservacao']) ? (int)$data['id_modo_conservacao'] : null;

      if ($descricao === '' || $quant <= 0 || $peso <= 0.0 || !$idUM || !$idMC) {
        throw new \InvalidArgumentException('Campos obrigatórios inválidos (descrição, quantidade, peso, UM, modo).');
      }

    
      $idMat = MateriaisModel::createFromAvulsa(
        descricao:           $descricao,
        validadeIso:         $validade,
        peso:                $peso,
        id_unidades_medidas: $idUM,
        id_modo_conservacao: $idMC,
        id_empresas:         $idEmpresa,
        id_usuarios:         $idUsuario,
        quantidade:          $quant
      );

      // Descobre setor padrão do usuário (fallback para preencher nm_setor na etiqueta)
      $idSetor = null;
      if (isset($_SESSION['usuario']['id_setor']) && !empty($_SESSION['usuario']['id_setor'])) {
        $idSetor = (int)$_SESSION['usuario']['id_setor'];
      } else {
        try {
          $stS = $pdo->prepare("SELECT id_setor FROM tb_usuarios WHERE id_usuarios = :u LIMIT 1");
          $stS->execute([':u' => $idUsuario]);
          $rowS = $stS->fetch(PDO::FETCH_ASSOC);
          if ($rowS && !empty($rowS['id_setor'])) $idSetor = (int)$rowS['id_setor'];
        } catch (\Throwable $e) { /* ignora fallback */ }
      }

      $ids = EtiquetasModel::inserirAvulsasParaMaterial(
          id_materiais:        $idMat,
          quantidade:          $quant,
          descricao:           $descricao,
          id_usuarios:         $idUsuario,
          id_empresas:         $idEmpresa,
          id_setor:            $idSetor,
          id_unidades_medidas: $idUM,
          qtd_por_etiqueta:    $peso        
      );

      $rows = EtiquetasModel::buscarPorIds($ids);

      $pdo->commit();
      return ['ok'=>true, 'ids'=>$ids, 'data'=>$rows, 'message'=>'Etiquetas criadas'];
    } catch (\Throwable $e) {
      if ($pdo->inTransaction()) $pdo->rollBack();
      return ['ok'=>false, 'message'=>'Erro ao criar etiqueta', 'detail'=>$e->getMessage()];
    }
  }
}
