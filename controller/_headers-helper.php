<?php
/**
 * Helper para ler headers de forma robusta e case-insensitive:
 * 1) Tenta Slim ($app->request->headers->get)
 * 2) Cai para $_SERVER[HTTP_XYZ]
 * 3) Cai para getallheaders()
 */
if (!function_exists('_getHeaderValue')) {
    function _getHeaderValue(string $name): ?string {
        // 1) Slim (se $GLOBALS['app'] estiver setado)
        if (isset($GLOBALS['app']) && is_object($GLOBALS['app'])) {
            try {
                $v = $GLOBALS['app']->request()->headers->get($name);
                if (!empty($v)) return $v;
            } catch (Throwable $e) {}
        }

        // 2) $_SERVER (HTTP_X_COMPANY_ID, etc.)
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        if (!empty($_SERVER[$key])) return $_SERVER[$key];

        // 3) getallheaders() (case-insensitive)
        if (function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strcasecmp($k, $name) === 0 && $v !== '') return $v;
            }
        }
        return null;
    }
}
