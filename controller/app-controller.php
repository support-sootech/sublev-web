<?php
$app->get('/app-download', function () use ($app) {
    $filePath = './app/sublev.apk';
    //verMatriz($filePath);die();
    if (!file_exists($filePath)) {
        $app->response->setStatus(404);
        $app->response->headers->set('Content-Type', 'text/plain');
        echo 'Arquivo não encontrado.';
        return;
    }

    $fileName = basename($filePath);
    $fileSize = filesize($filePath);

    $app->response->headers->set('Content-Type', 'application/vnd.android.package-archive');
    $app->response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    $app->response->headers->set('Content-Length', $fileSize);
    $app->response->headers->set('Pragma', 'public');
    $app->response->headers->set('Expires', '0');
    $app->response->headers->set('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
    $app->response->headers->set('Content-Transfer-Encoding', 'binary');

    readfile($filePath);
});
?>