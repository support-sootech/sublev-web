<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
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

        <?php if (menuSistema() && count(menuSistema())>0): ?>
            <?php foreach (menuSistema() as $key => $value): ?>                
                
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menu<?=$key?>"
                        aria-expanded="true" aria-controls="menu<?=$key?>">
                        <i class="<?=$value['icone']?>"></i>
                        <span><?=$value['descricao']?></span>
                    </a>
                    <div id="menu<?=$key?>" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            
                            <?php if(isset($value['subtitulo']) && !empty($value['subtitulo'])): ?>
                                <h6 class="collapse-header"><?=$value['subtitulo']?></h6>
                            <?php endif; ?>

                            <?php if(isset($value['submenus']) && count($value['submenus'])>0): ?>
                                <?php foreach ($value['submenus'] as $k => $v): ?>
                                    <a class="collapse-item" href="<?=$v['link']?>"><?=$v['descricao']?></a>
                                <?php endforeach; ?>
                            <?php endif; ?>

                        </div>
                    </div>
                </li>
                <!-- <hr class="sidebar-divider d-none d-md-block"> -->

            <?php endforeach; ?>
        <?php endif;?>
        

    </ul>