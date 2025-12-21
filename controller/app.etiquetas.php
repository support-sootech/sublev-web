<?php

if (!class_exists('UnidadesMedidasModel')) {
  require_once __DIR__ . '/../model/UnidadesMedidasModel.php';
}
if (!class_exists('MateriaisModel')) {
  require_once __DIR__ . '/../model/MateriaisModel.php';
}
if (!class_exists('EtiquetasModel')) {
  require_once __DIR__ . '/../model/EtiquetasModel.php';
}
if (!class_exists('MateriaisFracionadosModel')) {
  require_once __DIR__ . '/../model/MateriaisFracionadosModel.php';
}
if (!class_exists('EtiquetasController')) {
  require_once __DIR__ . '/../controller/EtiquetasController.php';
}

// helpers
function _parseDateYmd($s)
{
  $s = trim((string) $s);
  if ($s === '')
    return null;
  if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $s))
    return $s;
  if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $s, $m)) {
    return "{$m[3]}-{$m[2]}-{$m[1]}";
  }
  return null;
}
function _authAppOrPanel($app, &$id_empresas_out)
{
  $id_empresas_out = 0;
  $uid = null;
  if (function_exists('valida_logado') && valida_logado()) {
    $uid = $_SESSION['usuario']['id_usuarios'] ?? null;
    $id_empresas_out = (int) ($_SESSION['usuario']['id_empresas'] ?? 0);
  }
  if (!$uid) {
    try {
      $token = $app->request->headers->get('Token-User');
      if ($token) {
        $pdo = $GLOBALS['pdo'];
        $st = $pdo->prepare("SELECT u.id_usuarios FROM tb_usuarios u WHERE u.hash = :h AND u.status = 'A' LIMIT 1");
        $st->execute([':h' => $token]);
        if ($row = $st->fetch(PDO::FETCH_ASSOC))
          $uid = (int) $row['id_usuarios'];
      }
    } catch (Exception $e) {
    }
  }
  if ($id_empresas_out <= 0) {
    $hdr = (int) ($app->request->headers->get('X-Company-Id') ?: 0);
    $id_empresas_out = $hdr ?: (int) ($app->request->params('id_empresas') ?: 0);
  }
  return $uid ?: 0;
}
function _json($app, $status, $arr)
{
  $r = $app->response();
  $r['Access-Control-Allow-Origin'] = '*';
  $r['Access-Control-Allow-Methods'] = 'GET, POST, OPTIONS';
  $r['Content-Type'] = 'application/json';
  $r->status($status);
  $r->body(json_encode($arr));
}
if (!function_exists('_app_getUsuarioByToken')) {
  function _app_getUsuarioByToken($app)
  {
    try {
      $token = $app->request->headers->get('Token-User');
      if (!$token)
        return null;
      $pdo = $GLOBALS['pdo'];
      $st = $pdo->prepare("SELECT id_usuarios FROM tb_usuarios WHERE hash = :h AND status = 'A' LIMIT 1");
      $st->execute([':h' => $token]);
      $row = $st->fetch(PDO::FETCH_ASSOC);
      return $row ? (int) $row['id_usuarios'] : null;
    } catch (\Throwable $e) {
      return null;
    }
  }
}
if (!function_exists('_app_getEmpresaFromContext')) {
  function _app_getEmpresaFromContext($app)
  {
    if (function_exists('getIdEmpresasLogado')) {
      $id = getIdEmpresasLogado();
      if (!empty($id))
        return (int) $id;
    }
    $hdr = $app->request->headers->get('X-Company-Id');
    if (!empty($hdr))
      return (int) $hdr;
    $id = $app->request->params('id_empresas');
    return !empty($id) ? (int) $id : 0;
  }
}

// GET/POST /app-unidades-medidas
$app->map('/app-unidades-medidas', function () use ($app) {
  $status = 200;
  $ret = ['success' => false, 'data' => []];
  try {
    $logado = function_exists('valida_logado') ? valida_logado() : false;
    $id_usuario = $logado ? ($_SESSION['usuario']['id_usuarios'] ?? null) : null;
    if (!$id_usuario)
      $id_usuario = _app_getUsuarioByToken($app);

    if (!$id_usuario) {
      $status = 401;
      $ret = ['success' => false, 'msg' => 'Não autorizado'];
    } else {
      $id_empresas = _app_getEmpresaFromContext($app);
      if ($id_empresas <= 0) {
        $status = 400;
        $ret = ['success' => false, 'msg' => 'Empresa não informada'];
      } else {
        $statusParam = $app->request->params('status') ?: 'A';
        $class = new UnidadesMedidasModel();
        $arr = $class->loadAll($id_empresas, $statusParam);
        $ret = ['success' => true, 'data' => ($arr ?: [])];
      }
    }
  } catch (\Throwable $e) {
    $status = 500;
    $ret = ['success' => false, 'msg' => 'Erro interno', 'detail' => $e->getMessage()];
  }
  $resp = $app->response();
  $resp['Access-Control-Allow-Origin'] = '*';
  $resp['Access-Control-Allow-Methods'] = 'GET, POST';
  $resp['Content-Type'] = 'application/json';
  $resp->status($status);
  $resp->body(json_encode($ret));
})->via('GET', 'POST');

// POST /app-etiqueta-avulsa
$app->post('/app-etiqueta-avulsa', function () use ($app) {
  // 1) Usuário
  $id_usuario = null;
  if (function_exists('valida_logado') && valida_logado()) {
    $id_usuario = $_SESSION['usuario']['id_usuarios'] ?? null;
  }
  if (!$id_usuario) {
    try {
      $token = $app->request->headers->get('Token-User');
      if ($token) {
        $pdo = $GLOBALS['pdo'];
        $st = $pdo->prepare("SELECT id_usuarios FROM tb_usuarios WHERE hash = :h AND status = 'A' LIMIT 1");
        $st->execute([':h' => $token]);
        if ($row = $st->fetch(PDO::FETCH_ASSOC))
          $id_usuario = (int) $row['id_usuarios'];
      }
    } catch (Exception $e) {
    }
  }
  if (!$id_usuario)
    return _json($app, 401, ['success' => false, 'msg' => 'Não autorizado (token/usuário)']);

  // 2) Empresa
  $id_empresas = 0;
  if (isset($_SESSION['usuario']['id_empresas'])) {
    $id_empresas = (int) $_SESSION['usuario']['id_empresas'];
  }
  if ($id_empresas <= 0) {
    $hdr = $app->request->headers->get('X-Company-Id');
    if (!empty($hdr))
      $id_empresas = (int) $hdr;
  }
  if ($id_empresas <= 0) {
    $param = $app->request->params('id_empresas');
    if (!empty($param))
      $id_empresas = (int) $param;
  }
  if ($id_empresas <= 0)
    $id_empresas = 1; // default local

  // 3) Body
  $raw = $app->request->getBody();
  $payload = json_decode($raw, true);
  if (!is_array($payload) || empty($payload)) {
    $payload = $app->request->post();
  }

  // Normaliza peso: aceita "0,8" ou "0.8"
  $peso = null;
  if (isset($payload['peso'])) {
    $peso = (float) str_replace(',', '.', (string) $payload['peso']);
  }

  // Normaliza chaves
  $data = [
    'descricao' => trim($payload['descricao'] ?? ''),
    'quantidade' => (int) ($payload['quantidade'] ?? 0),
    'validade' => $payload['validade'] ?? null,
    'peso' => $peso,
    'id_unidades_medidas' => isset($payload['idUnidadesMedidas']) ? (int) $payload['idUnidadesMedidas'] : (isset($payload['id_unidades_medidas']) ? (int) $payload['id_unidades_medidas'] : null),
    'id_modo_conservacao' => isset($payload['idModoConservacao']) ? (int) $payload['idModoConservacao'] : (isset($payload['id_modo_conservacao']) ? (int) $payload['id_modo_conservacao'] : null),
    'salvar_catalogo' => !empty($payload['salvar_catalogo']),
  ];

  if ($data['descricao'] === '' || $data['quantidade'] <= 0 || empty($data['id_unidades_medidas']) || empty($data['id_modo_conservacao']) || ($data['peso'] ?? 0) <= 0) {
    return _json($app, 400, ['success' => false, 'msg' => 'Campos obrigatórios faltando (descrição, quantidade, UM, modo, peso)']);
  }

  // 4) Controller
  $controller = new EtiquetasController();
  $ret = $controller->criarAvulsa($data, $id_usuario, $id_empresas);

  if (!($ret['ok'] ?? false)) {
    return _json($app, 500, ['success' => false, 'msg' => 'Erro ao gerar etiquetas', 'detail' => $ret['detail'] ?? $ret['message'] ?? '']);
  }

  return _json($app, 200, ['success' => true, 'ids' => $ret['ids'], 'data' => $ret['data']]);
});

// Debug
$app->post('/app-etiqueta-avulsa-debug', function () use ($app) {
  $headers = [];
  foreach ($app->request->headers as $k => $v) {
    $headers[$k] = $v;
  }
  $raw = $app->request->getBody();
  $params = $app->request->params();
  _json($app, 200, ['headers' => $headers, 'params' => $params, 'raw' => $raw]);
});
