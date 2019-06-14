<div class="wblock color-red">
  <div class="data-header">
    <div class="d-flex align-items-center">
      <div class="col title">
        <i class="fas fa-folder"></i> <?=__('Lezárt projektek')?>
      </div>
    </div>
  </div>
  <div class="data-container">
    <?php if (empty($this->projects)): ?>
    <div class="no-data-view">
      <div class="ico"><i class="far fa-check-circle"></i></div>
      <div class="text"><?=__('Nincs jelenleg lezárt projekt!')?></div>
    </div>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th><?=__('Projekt elnevezés')?></th>
            <th width="220" class="center"><?=__('Állapot')?></th>
            <th width="220" class="center"><?=__('Díjfizetés')?></th>
            <th width="240" class="center"><?=__('Partner')?></th>
            <th width="180" class="center"><?=__('Projekt kezdete')?></th>
            <th width="180" class="center"><?=__('Projekt vége')?></th>
            <th width="120" class="center"><?=__('Létrejött')?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ( (array)$this->projects as $p ): ?>
          <tr>
            <td><a href="/projektek/projekt/<?=$p['hashkey']?>"><?=($p['title'] != '')?$p['title']:'<span class="nosetdata">'.__('Projekt elnevezése hiányzik! &nbsp;&nbsp; Szerkesztés').' <i class="fas fa-pencil-alt"></i></span>'?></a>
            </td>
            <td>
              <div class="progress">
                <div class="progress-bar <?=\Helper::progressBarColor($p['status_percent'])?>" role="progressbar" style="width: <?=$p['status_percent']?>%;" aria-valuenow="<?=$p['status_percent']?>" aria-valuemin="0" aria-valuemax="100"><?=$p['status_percent']?>%</div>
              </div>
            </td>
            <td>
              <div class="progress">
                <div class="progress-bar <?=\Helper::progressBarColor($p['paying_percent'])?>"  role="progressbar" style="width: <?=$p['paying_percent']?>%;" aria-valuenow="<?=$p['paying_percent']?>" aria-valuemin="0" aria-valuemax="100"><?=$p['paying_percent']?>%</div>
            </td>
            <td>
              <strong><?=$p['partner']['data']['nev']?></strong> <? if($p['partner']['data']['company_name'] != ''): ?>(<?=$p['partner']['data']['company_name']?>)<? endif; ?>
            </td>
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
