<?php
$h = function_exists('getallheaders') ? getallheaders() : [];
echo json_encode([
  'HTTP_TOKEN_USER'   => $_SERVER['HTTP_TOKEN_USER']   ?? null,
  'HTTP_X_COMPANY_ID' => $_SERVER['HTTP_X_COMPANY_ID'] ?? null,
  'getallheaders'     => $h,
], JSON_PRETTY_PRINT), "\n";
