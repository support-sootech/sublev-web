<?php
// controller/EtiquetasController.php

class EtiquetasController {
  public function criarAvulsa(array $data, int $idUsuario, int $idEmpresa): array {
    $pdo = $GLOBALS['pdo'];
    try {
      $pdo->beginTransaction();

      $idUn = isset($data['id_unidades_medidas']) ? (int)$data['id_unidades_medidas'] : null;

      $idMat = MateriaisModel::findOrCreateFromAvulsa(
        descricao:   $data['produto'],
        ean:         $data['codigo'],
        lote:        $data['lote'] ?? null,
        validadeIso: $data['validade'] ?? null,
        preco:       isset($data['preco']) ? (float)$data['preco'] : null,
        idUnidadeMedida: $idUn,
        idEmpresa:   $idEmpresa,
        idUsuario:   $idUsuario
      );

      $ids = EtiquetasModel::inserirEtiquetasAvulsas(
        idMaterial: $idMat,
        quantidade: (int)$data['quantidade'],
        ean:        $data['codigo'],
        descricao:  $data['produto'],
        idUsuario:  $idUsuario,
        idEmpresa:  $idEmpresa
      );

      $rows = EtiquetasModel::buscarPorIds($ids);

      $pdo->commit();
      return ['ok'=>true, 'ids'=>$ids, 'data'=>$rows, 'message'=>'Etiquetas criadas'];
    } catch (Throwable $e) {
      if ($pdo->inTransaction()) $pdo->rollBack();
      return ['ok'=>false, 'message'=>'Erro ao criar etiqueta', 'detail'=>$e->getMessage()];
    }
  }
}
