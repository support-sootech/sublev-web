<?php
// routes/app.etiquetas.php
// Rotas do APP (mobile). Padrão Slim v2 + JSON { success, data, msg }.

if (!class_exists('UnidadesMedidasModel')) {
  require_once __DIR__ . '/../model/UnidadesMedidasModel.php';
}
if (!class_exists('MateriaisModel')) {
  require_once __DIR__ . '/../model/MateriaisModel.php';
}
if (!class_exists('EtiquetasModel')) {
  require_once __DIR__ . '/../model/EtiquetasModel.php';
}

/** Auth para App: aceita sessão OU header Token-User; resolve id_empresas via sessão/header/param */
function _authAppOrPanel($app, &$id_empresas_out) {
    $id_empresas_out = 0;
    $uid = null;

    // 1) Se tiver sessão do painel
    if (function_exists('valida_logado') && valida_logado()) {
        $uid = $_SESSION['usuario']['id_usuarios'] ?? null;
        $id_empresas_out = (int)($_SESSION['usuario']['id_empresas'] ?? 0);
    }

    // 2) Se não tiver sessão, tentar Token-User (hash do tb_usuarios)
    if (!$uid) {
        try {
            $token = $app->request->headers->get('Token-User');
            if ($token) {
                $pdo = $GLOBALS['pdo'];
                $st = $pdo->prepare("
                    SELECT u.id_usuarios
                    FROM tb_usuarios u
                    WHERE u.hash = :h AND u.status = 'A'
                    LIMIT 1
                ");
                $st->execute([':h' => $token]);
                if ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                    $uid = (int)$row['id_usuarios'];
                }
            }
        } catch (Exception $e) {}
    }

    // 3) Resolver empresa: sessão -> header -> query/post
    if ($id_empresas_out <= 0) {
        $hdr = (int)($app->request->headers->get('X-Company-Id') ?: 0);
        $id_empresas_out = $hdr ?: (int)($app->request->params('id_empresas') ?: 0);
    }
    return $uid ?: 0;
}

/** Util: envia resposta JSON padronizada */
function _json($app, $status, $arr) {
    $r = $app->response();
    $r['Access-Control-Allow-Origin']  = '*';
    $r['Access-Control-Allow-Methods'] = 'GET, POST, OPTIONS';
    $r['Content-Type'] = 'application/json';
    $r->status($status);
    $r->body(json_encode($arr));
}

if (!function_exists('_app_getUsuarioByToken')) {
  function _app_getUsuarioByToken($app) {
    try {
      $token = $app->request->headers->get('Token-User');
      if (!$token) return null;
      $pdo = $GLOBALS['pdo'];
      $st = $pdo->prepare("SELECT id_usuarios FROM tb_usuarios WHERE hash = :h AND status = 'A' LIMIT 1");
      $st->execute([':h' => $token]);
      $row = $st->fetch(PDO::FETCH_ASSOC);
      return $row ? (int)$row['id_usuarios'] : null;
    } catch (\Throwable $e) { return null; }
  }
}
if (!function_exists('_app_getEmpresaFromContext')) {
  function _app_getEmpresaFromContext($app) {
    if (function_exists('getIdEmpresasLogado')) {
      $id = getIdEmpresasLogado();
      if (!empty($id)) return (int)$id;
    }
    $hdr = $app->request->headers->get('X-Company-Id');
    if (!empty($hdr)) return (int)$hdr;
    $id = $app->request->params('id_empresas');
    return !empty($id) ? (int)$id : 0;
  }
}

// GET/POST /app-unidades-medidas (mantido)
$app->map('/app-unidades-medidas', function() use ($app) {
  $status = 200;
  $ret = ['success'=>false, 'data'=>[]];
  try {
    $logado = function_exists('valida_logado') ? valida_logado() : false;
    $id_usuario = $logado ? ($_SESSION['usuario']['id_usuarios'] ?? null) : null;
    if (!$id_usuario) $id_usuario = _app_getUsuarioByToken($app);

    if (!$id_usuario) {
      $status = 401; $ret = ['success'=>false,'msg'=>'Não autorizado'];
    } else {
      $id_empresas = _app_getEmpresaFromContext($app);
      if ($id_empresas <= 0) {
        $status = 400; $ret = ['success'=>false,'msg'=>'Empresa não informada'];
      } else {
        $statusParam = $app->request->params('status') ?: 'A';
        $class = new UnidadesMedidasModel();
        $arr   = $class->loadAll($id_empresas, $statusParam);
        $ret   = ['success'=>true, 'data'=>($arr ?: [])];
      }
    }
  } catch (\Throwable $e) {
    $status = 500; $ret = ['success'=>false,'msg'=>'Erro interno','detail'=>$e->getMessage()];
  }
  $resp = $app->response();
  $resp['Access-Control-Allow-Origin']  = '*';
  $resp['Access-Control-Allow-Methods'] = 'GET, POST';
  $resp['Content-Type'] = 'application/json';
  $resp->status($status);
  $resp->body(json_encode($ret));
})->via('GET','POST');

$app->post('/app-etiqueta-avulsa', function() use ($app) {
  $status = 400;
  $ret = ['success'=>false];

  try {
    if (!valida_logado()) {
      $status = 401; throw new Exception('Não autorizado');
    }

    $payload = json_decode($app->request->getBody(), true) ?: [];

    $descricao  = trim((string)($payload['descricao'] ?? $payload['produto'] ?? ''));
    $validade   = !empty($payload['validade']) ? (string)$payload['validade'] : null; // 'YYYY-MM-DD'
    $peso       = isset($payload['peso']) ? (float)$payload['peso'] : 0.0;
    $idUM       = isset($payload['idUnidadesMedidas']) ? (int)$payload['idUnidadesMedidas'] : null;
    $idMC       = isset($payload['idModoConservacao']) ? (int)$payload['idModoConservacao'] : null;
    $qtd        = isset($payload['quantidade']) ? (int)$payload['quantidade'] : 1;

    if ($descricao === '') throw new Exception('Descrição é obrigatória');
    if ($qtd <= 0) $qtd = 1;

    $id_empresas = getIdEmpresasLogado();
    $id_usuarios = getUsuario();
    $id_setor    = getIdSetorLogado();

    $pdo = $GLOBALS['pdo'];
    $pdo->beginTransaction();

    // 1) Cria material AVULSO com quantidade = tela
    $id_mat = MateriaisModel::createFromAvulsa(
      $descricao, $validade, $peso, $idUM, $idMC, $id_empresas, $id_usuarios, $qtd
    );

    // 2) Base do número sequencial (por empresa)
    $st = $pdo->prepare("SELECT COALESCE(MAX(num_etiqueta),0) AS mx FROM tb_etiquetas WHERE id_empresas = :e");
    $st->execute([':e' => $id_empresas]);
    $seqBase = (int)($st->fetch(PDO::FETCH_ASSOC)['mx'] ?? 0);

    $idsEtiquetas = [];

    // 3) Para cada unidade:
    for ($i = 1; $i <= $qtd; $i++) {
      // 3a) fracionado (qtd_fracionada SEMPRE 1)
      $sqlF = "INSERT INTO tb_materiais_fracionados
                 (id_materiais, qtd_fracionada, dt_vencimento, status, id_usuarios, id_setor, id_unidades_medidas)
               VALUES
                 (:m, 1, :v, 'A', :u, :s, :um)";
      $stF = $pdo->prepare($sqlF);
      $stF->execute([
        ':m' => $id_mat,
        ':v' => $validade ?: null,
        ':u' => $id_usuarios,
        ':s' => $id_setor,
        ':e' => $id_empresas,
        ':um'=> $idUM,
      ]);
      $id_mf = (int)$pdo->lastInsertId();

      // 3b) etiqueta (tipo A) com número sequencial
      $numero = $seqBase + $i;
      $id_etq = EtiquetasModel::criarAvulsaUnit(
        $id_mat, $id_mf, $descricao, $id_usuarios, $id_empresas, $numero
      );
      $idsEtiquetas[] = $id_etq;
    }

    $pdo->commit();
    $status = 200;
    $ret = ['success'=>true, 'data'=>['id_materiais'=>$id_mat, 'ids_etiquetas'=>$idsEtiquetas]];
  } catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    $ret = ['success'=>false, 'msg'=>$e->getMessage()];
    $status = $status === 400 ? 500 : $status;
  }

  $resp = $app->response();
  $resp['Access-Control-Allow-Origin']  = '*';
  $resp['Access-Control-Allow-Methods'] = 'POST';
  $resp['Content-Type'] = 'application/json';
  $resp->status($status);
  $resp->body(json_encode($ret));
});
