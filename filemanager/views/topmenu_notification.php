<?php if($count):
        if( $theme == 'purple'): ?>
          <li class="nav-item dropdown">
              <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
                <i class="mdi mdi-bell-outline"></i>
                <span class="count-symbol bg-danger"></span>
              </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
                <h6 class="p-3 mb-0"><?= cclang('notifications');?></h6>
                <div class="dropdown-divider"></div>

                <?php foreach ($get_notifications as $notice):?>
                <a class="dropdown-item preview-item" href="<?= base_url('notifications/view/'.$notice->id);?>">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-success">
                      <i class="mdi mdi-calendar"></i>
                    </div>
                  </div>
                  <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                    <h6 class="preview-subject font-weight-normal mb-1"><?= substr($notice->title,0,50);?></h6>
                    <p class="text-gray ellipsis mb-0"> <?= substr($notice->content,0,100);?> </p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
              <?php endforeach ?>
                
                <h6 class="p-3 mb-0 text-center"><a href="<?= base_url('notifications');?>"><?= cclang('see_all_notifications');?></h6>
              </div>
            </li>
        <?php endif ?>
      <?php else: ?>
        <li class="nav-item dropdown">
              <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="<?= base_url('notifications/view/'.$notice->id);?>" data-toggle="dropdown">
                <i class="mdi mdi-bell-outline"></i>
                
              </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
                <h6 class="p-3 mb-0"><?= cclang('notifications');?></h6>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-success">
                      Hi
                    </div>
                  </div>
                  <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                    
                    <p class="text-gray ellipsis mb-0"> <?= cclang('you_have_no_new_notification');?> </p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <h6 class="p-3 mb-0 text-center"><a href="<?= base_url('notifications');?>"><?= cclang('see_all_notifications');?></h6>
              </div>
            </li>
      <?php endif ?>