<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/home">
            <div class="sidebar-brand-icon rotate-n-15">
                <i class="fas fa-tags"></i>
            </div>
            <div class="sidebar-brand-text mx-3">Controle de Etiquetas</div>
        </a>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <!-- Sidebar Toggler (Sidebar) -->
        <br>
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>

        <!-- Nav Item - Dashboard -->
        <li class="nav-item active">
            <a class="nav-link" href="/dashboard">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <?php if(isset($_SESSION['usuario']['menu']) && count($_SESSION['usuario']['menu'])>0):?>
            <?php foreach ($_SESSION['usuario']['menu'] as $menuKey => $menuValue): ?>
                <?php foreach ($menuValue as $key => $value): ?>
                    <li class="nav-item">
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menu<?=$key?>"
                            aria-expanded="true" aria-controls="menu<?=$key?>">
                            <i class="<?=$value['icone']?>"></i>
                            <span><?=$value['nome']?></span>
                        </a>
                        <div id="menu<?=$key?>" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                            <div class="bg-white py-2 collapse-inner rounded">
                                
                                <?php if(isset($value['descricao']) && !empty($value['descricao'])): ?>
                                    <h6 class="collapse-header"><?=$value['descricao']?></h6>
                                <?php endif; ?>

                                <?php if(isset($value['menu_sub']) && count($value['menu_sub'])>0): ?>
                                    <?php foreach ($value['menu_sub'] as $k => $v): ?>
                                        <a class="collapse-item" href="<?=$v['link']?>">
                                            <?php if(isset($v['icone']) && !empty($v['icone'])):?>
                                                <i class="<?=$v['icone']?>"></i>
                                            <?php endif;?>
                                            <?=$v['nome']?>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                            </div>
                        </div>
                        
                    </li>
                <?php endforeach;?>
            <?php endforeach;?>
        <?php endif;?>

    </ul>