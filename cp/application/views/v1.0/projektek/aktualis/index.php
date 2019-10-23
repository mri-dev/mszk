<div class="wblock color-green">
  <div class="data-header">
    <div class="d-flex align-items-center">
      <div class="col title">
        <i class="far fa-folder-open"></i> <?=__('Aktív projektek')?>
      </div>
    </div>
  </div>
  <div class="data-container">
    <?php if (empty($this->projects)): ?>
    <div class="no-data-view">
      <div class="ico"><i class="far fa-check-circle"></i></div>
      <div class="text"><?=__('Nincs jelenleg futó / aktív projekt!')?></div>
    </div>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th><?=__('Projekt elnevezés')?></th>
            <?php if (!$this->is_admin_logged): ?>
              <th width="220" class="center"><?=__('Díjfizetés')?></th>
            <?php endif; ?>
            <?php if ($this->is_admin_logged): ?>
              <th width="240" class="center"><?=__('Ajánlatkérő')?></th>
              <th width="240" class="center"><?=__('Szolgáltató')?></th>
            <?php endif; ?>
            <th width="180" class="center"><?=__('Projekt kezdete')?></th>
            <th width="180" class="center"><?=__('Projekt vége')?></th>
            <th width="120" class="center"><?=__('Létrejött')?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ( (array)$this->projects as $p ): ?>
          <tr>
            <?php if ($this->is_admin_logged): ?>
              <td>
                <a href="/projektek/projekt/<?=$p['order_hashkey']?>"><?=($p[$p['my_relation'].'_title'] != '')?'<strong>'.$p[$p['my_relation'].'_title'].'</strong>':'#'.$p['order_hashkey'].'<br><span class="nosetdata">'.__('Projekt elnevezése hiányzik! &nbsp;&nbsp; Szerkesztés').' <i class="fas fa-pencil-alt"></i></span>'?></a>
                <div class="progress status-percent">
                  <div class="progress-bar <?=\Helper::progressBarColor($p['status_percent'])?>" role="progressbar" style="width: <?=$p['status_percent']?>%;" aria-valuenow="<?=$p['status_percent']?>" aria-valuemin="0" aria-valuemax="100"><?=$p['status_percent']?>%</div>
                </div>
              </td>
            <?php else: ?>
              <td>
              <a href="/projektek/projekt/<?=$p['hashkey']?>">
                <strong><?php echo $p['admin_title']; ?></strong>
                <?php if ($p[$p['my_relation'].'_title'] != ''): ?>
                <div class="user-title"><?php echo $p[$p['my_relation'].'_title']; ?></div>
                <?php endif; ?>
              </a>
              <div class="progress status-percent">
                <div class="progress-bar <?=\Helper::progressBarColor($p['status_percent'])?>" role="progressbar" style="width: <?=$p['status_percent']?>%;" aria-valuenow="<?=$p['status_percent']?>" aria-valuemin="0" aria-valuemax="100"><?=$p['status_percent']?>%</div>
              </div>
              </td>
            <?php endif; ?>
            <?php if (!$this->is_admin_logged): ?>
            <td>
              <div class="progress">
                <div class="progress-bar <?=\Helper::progressBarColor($p[$p['my_relation'].'_paying_percent'])?>"  role="progressbar" style="width: <?=$p[$p['my_relation'].'_paying_percent']?>%;" aria-valuenow="<?=$p[$p['my_relation'].'_paying_percent']?>" aria-valuemin="0" aria-valuemax="100"><?=$p[$p['my_relation'].'_paying_percent']?>%</div>
              </div>
            </td>
            <?php endif; ?>
            <?php if ($this->is_admin_logged): ?>
              <td>
                <strong><a href="/account/?t=edit&ID=<?=$p['user_requester']['data']['ID']?>&ret=/projektek/aktualis" target="_blank"><?=$p['user_requester']['data']['nev']?></a></strong> <? if($p['user_requester']['data']['company_name'] != ''): ?><br>(<?=$p['user_requester']['data']['company_name']?>)<? endif; ?>
                <div class="progress" title="<?=__('Díjfizetés állapota')?>">
                  <div class="progress-bar <?=\Helper::progressBarColor($p['requester_paying_percent'])?>"  role="progressbar" style="width: <?=$p['requester_paying_percent']?>%;" aria-valuenow="<?=$p['requester_paying_percent']?>" aria-valuemin="0" aria-valuemax="100"><?=$p['requester_paying_percent']?>%</div>
                </div>
              </td>
              <td>
                <strong><a href="/account/?t=edit&ID=<?=$p['user_servicer']['data']['ID']?>&ret=/projektek/aktualis" target="_blank"><?=$p['user_servicer']['data']['nev']?></a></strong> <? if($p['user_servicer']['data']['company_name'] != ''): ?><br>(<?=$p['user_servicer']['data']['company_name']?>)<? endif; ?>
                <div class="progress" title="<?=__('Díjfizetés állapota')?>">
                  <div class="progress-bar <?=\Helper::progressBarColor($p['servicer_paying_percent'])?>"  role="progressbar" style="width: <?=$p['servicer_paying_percent']?>%;" aria-valuenow="<?=$p['servicer_paying_percent']?>" aria-valuemin="0" aria-valuemax="100"><?=$p['servicer_paying_percent']?>%</div>
                </div>
              </td>
            <?php endif; ?>
            <td class="center" ><?=($p['project_start'] != '')?$p['project_start']:'<span class="nosetdata">'.__('Még nincs meghatározva.').'</span>'?></td>
            <td class="center" ><?=($p['project_end'] != '')?$p['project_end']:'<span class="nosetdata">'.__('Még nincs meghatározva.').'</span>'?></td>
            <td class="center" title="<?=$p['created_at']?>"><?=$p['created_dist']?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
