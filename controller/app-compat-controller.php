<?php
// Compat layer: roteia /app-* para endpoints legados *-json quando não existe um handler específico.
// Implementação simples: faz proxy HTTP interno para o endpoint legado correspondente.
// Isso evita duplicar lógica em vários controllers ao aplicar compatibilidade para o app mobile.

$__app_compat_map = array(
    '/app-categorias' => '/materiais-categorias-json',
    '/app-fornecedores' => '/fornecedores-fabricantes-json',
    '/app-fabricantes' => '/fornecedores-fabricantes-json',
    '/app-marcas' => '/materiais-marcas-json',
    // Rotas com handlers /app-* próprios (já tratam Token-User e X-Company-Id) removidas do compat:
    // '/app-embalagens-condicoes' => '/embalagem-condicoes-json',
    // '/app-unidades-medidas'    => '/unidades-medidas-json',
    // '/app-modo-conservacao'    => '/modo-conservacao-json',
    '/app-materiais-vencimento' => '/materiais-vencimento-json',
    '/app-quant-materiais-vencimento' => '/quant-materiais-vencimento-json',
    '/app-detalhes-etiqueta' => '/detalhes-etiqueta-json',
    '/app-tipos-pessoas' => '/tipos-pessoas-json',
    '/app-embalagens-tipos' => '/embalagens-tipos-json',
    '/app-fracionar-material' => '/fracionar-material-json',
    '/app-materiais' => '/materiais-json',
    '/app-materiais-da-categoria' => '/materiais-da-categoria-json',
    '/app-detalhes-materiais' => '/detalhes-materiais-json',
    '/app-empresas' => '/empresas-json',
    '/app-setor' => '/setor-json',
    '/app-permissoes' => '/permissoes-json',
    '/app-embalagens' => '/embalagens-json',
);

// Helper para proxy interno
function _proxy_to_legacy($legacyPath) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? '127.0.0.1');
    $port = $_SERVER['SERVER_PORT'] ?? '80';
    // Monta URL base. Se HOST já contém porta, não adiciona.
    $urlBase = $scheme . '://' . $host;

    $url = $urlBase . $legacyPath;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    // Headers a repassar
    $headers = array();
    if (!empty($_SERVER['HTTP_TOKEN_USER'])) {
        $headers[] = 'Token-User: ' . $_SERVER['HTTP_TOKEN_USER'];
    }
    if (!empty($_SERVER['HTTP_X_COMPANY_ID'])) {
        $headers[] = 'X-Company-Id: ' . $_SERVER['HTTP_X_COMPANY_ID'];
    }
    // Cookies: repassa cookie PHPSESSID se existir
    if (!empty($_COOKIE)) {
        $cookiePairs = array();
        foreach ($_COOKIE as $k => $v) {
            $cookiePairs[] = $k.'='.$v;
        }
        curl_setopt($ch, CURLOPT_COOKIE, implode('; ', $cookiePairs));
    }

    if (!empty($headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Método e body
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if (strtoupper($method) === 'POST') {
        $raw = file_get_contents('php://input');
        curl_setopt($ch, CURLOPT_POST, true);
        if (!empty($raw)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        }
    } else if (strtoupper($method) === 'PUT' || strtoupper($method) === 'DELETE') {
        $raw = file_get_contents('php://input');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if (!empty($raw)) curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    }

    $resp = curl_exec($ch);
    if ($resp === false) {
        $err = curl_error($ch);
        curl_close($ch);
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(array('success'=>false,'msg'=>'Erro no proxy interno','detail'=>$err));
        return;
    }

    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($resp, 0, $header_size);
    $body = substr($resp, $header_size);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Repassa headers selecionados (Content-Type e Access-Control)
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Content-Type: application/json');
    http_response_code($status);
    echo $body;
}

foreach ($__app_compat_map as $appRoute => $legacyRoute) {
    $app->map($appRoute, function() use ($app, $legacyRoute) {
        _proxy_to_legacy($legacyRoute);
    })->via('GET','POST','OPTIONS');
}

?>