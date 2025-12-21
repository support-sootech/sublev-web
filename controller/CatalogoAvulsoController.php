<?php
if (!class_exists('CatalogoAvulsoModel')) {
    require_once __DIR__ . '/../model/CatalogoAvulsoModel.php';
}

class CatalogoAvulsoController
{
    public function loadAll($filtro = '', $id_empresas)
    {
        $class = new CatalogoAvulsoModel();
        return $class->loadAll($filtro, $id_empresas);
    }

    public function loadId($id)
    {
        $class = new CatalogoAvulsoModel();
        return $class->loadById($id);
    }

    public function save($data)
    {
        $class = new CatalogoAvulsoModel();
        return $class->add($data);
    }

    public function edit($data, $where)
    {
        $class = new CatalogoAvulsoModel();
        return $class->edit($data, $where);
    }

    public function del($id)
    {
        $class = new CatalogoAvulsoModel();
        return $class->del($id);
    }

    public function toggleFavorito($id, $isFavorito)
    {
        $class = new CatalogoAvulsoModel();
        return $class->toggleFavorito($id, $isFavorito);
    }
}
