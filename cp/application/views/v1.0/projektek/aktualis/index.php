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
            <th width="220" class="center"><?=__('Állapot')?></th>
            <th width="220" class="center"><?=__('Díjfizetés')?></th>
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
            <?php if (!$this->is_admin_logged): ?>
              <td><a href="/projektek/projekt/<?=$p['hashkey']?>"><?=($p[$p['my_relation'].'_title'] != '')?$p[$p['my_relation'].'_title']:'#'.$p['order_hashkey'].'<br><span class="nosetdata">'.__('Projekt elnevezése hiányzik! &nbsp;&nbsp; Szerkesztés').' <i class="fas fa-pencil-alt"></i></span>'?></a>
              </td>
            <?php else: ?>
              <td>
                <div class=""><?=__('Ajánlatkérő')?>: <a href="/projektek/projekt/<?=$p['hashkey']?>"><?=($p['requester_title'] != '')?$p['requester_title']:'<span class="nosetdata">'.__('Projekt elnevezése hiányzik!').' <i class="fas fa-pencil-alt"></i></span>'?></a></div>
                <div class=""><?=__('Szolgáltató')?>: <a href="/projektek/projekt/<?=$p['hashkey']?>"><?=($p['servicer_title'] != '')?$p['servicer_title']:'<span class="nosetdata">'.__('Projekt elnevezése hiányzik!').' <i class="fas fa-pencil-alt"></i></span>'?></a></div>
              </td>
            <?php endif; ?>
            <td>
              <div class="progress">
                <div class="progress-bar <?=\Helper::progressBarColor($p['status_percent'])?>" role="progressbar" style="width: <?=$p['status_percent']?>%;" aria-valuenow="<?=$p['status_percent']?>" aria-valuemin="0" aria-valuemax="100"><?=$p['status_percent']?>%</div>
              </div>
            </td>
            <td>
              <div class="progress">
                <div class="progress-bar <?=\Helper::progressBarColor($p['paying_percent'])?>"  role="progressbar" style="width: <?=$p['paying_percent']?>%;" aria-valuenow="<?=$p['paying_percent']?>" aria-valuemin="0" aria-valuemax="100"><?=$p['paying_percent']?>%</div>
            </td>
            <?php if ($this->is_admin_logged): ?>
              <td>
                <strong><a href="/account/?t=edit&ID=<?=$p['user_requester']['data']['ID']?>&ret=/projektek/aktualis" target="_blank"><?=$p['user_requester']['data']['nev']?></a></strong> <? if($p['user_requester']['data']['company_name'] != ''): ?><br>(<?=$p['user_requester']['data']['company_name']?>)<? endif; ?>
              </td>
              <td>
                <strong><a href="/account/?t=edit&ID=<?=$p['user_servicer']['data']['ID']?>&ret=/projektek/aktualis" target="_blank"><?=$p['user_servicer']['data']['nev']?></a></strong> <? if($p['user_servicer']['data']['company_name'] != ''): ?><br>(<?=$p['user_servicer']['data']['company_name']?>)<? endif; ?>
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
</div>
<pre><?php //print_r($this->projects); ?></pre>
