<div class="dashboard">
<? if($this->_USER): ?>
  <div class="wblock overview-services p-2">
    <div class="row">
      <div class="col ct-info color-blue">
        <div class="d-flex">
          <div class="ico">
            <div class="ico-wrapper"><i class="far fa-lightbulb"></i></div>
          </div>
          <div class="title"><?=__('Projektek')?><div class="line"></div></div>
          <div class="count"><?=$this->badges['projects']['all']?></div>
        </div>
      </div>
      <div class="col ct-info color-green">
        <div class="d-flex">
          <div class="ico">
            <div class="ico-wrapper"><i class="fas fa-file-import"></i></div>
          </div>
          <div class="title"><?=__('Ajánlat kérések')?><div class="line"></div></div>
          <div class="count"><?=$this->badges['offers']['all']['total']?></div>
        </div>
      </div>
      <div class="col ct-info color-blue-light">
        <div class="d-flex">
          <div class="ico">
            <div class="ico-wrapper"><i class="far fa-file-alt"></i></div>
          </div>
          <div class="title"><?=__('Díjbekérők')?><div class="line"></div></div>
          <div class="count"><?=(int)$this->dashboard['dijbekero']['all']['total_num']?></div>
        </div>
      </div>
      <div class="col ct-info color-red">
        <div class="d-flex">
          <div class="ico">
            <div class="ico-wrapper"><i class="far fa-calendar-times"></i></div>
          </div>
          <div class="title"><?=__('Lejárt díjbekérők')?><div class="line"></div></div>
          <div class="count"><?=(int)$this->dashboard['dijbekero']['expired']['total_num']?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4">
      <div class="wblock color-red">
        <div class="data-container">
          <?php $doc = $this->dashboard['dijbekero']['expired']; ?>
          <?php if ((int)$doc['total_num'] == 0): ?>
          <div class="no-data-view">
            <div class="ico"><i class="far fa-check-circle"></i></div>
            <div class="text"><?=__('Minden rendben!')?></div>
          </div>
          <?php else: ?>
            <div class="data-list">
              <div class="wrapper">
                <div class="header">
                  <div class="holder">
                    <div class="data"><?=__('Adatok')?></div>
                    <div class="relation"><?=__('Hozzáadta')?></div>
                    <div class="add-at"><?=__('Határidő')?></div>
                  </div>
                </div>
                <?php foreach ((array)$doc['data'] as $d): ?>
                <div class="list-item">
                  <div class="holder">
                    <div class="data">
                      <div class="title">
                        <a href="/doc/<?=$d['hashkey']?>" target="_blank"><strong><?=$d['name']?></strong></a>
                      </div>
                      <div class="subtitle">
                        <?php if ($d['ertek'] != 0): ?>
                        <span class="doc-ertek"><strong><?=\Helper::cashFormat($d['ertek'])?></strong> <?=__('Ft + ÁFA')?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="relation">
                      <?php if ($this->is_admin_logged): ?>
                        <strong><a href="/account/?t=edit&ID=<?=$d['user_id']?>&ret=/"><?=$d['user_nev']?></a></strong>
                      <?php else: ?>
                        <?=($d['is_me'])?__('Én'):__('Partner')?>
                      <?php endif; ?>
                    </div>
                    <div class="add-at">
                      <?=($d['expire_at'] != '') ? date('Y/m/d', strtotime($d['expire_at'])) : '<em>N/A</em>'?>
                    </div>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
        <div class="data-footer">
          <div class="d-flex align-items-center">
            <div class="title">
              <h3><?=__('Lejárt díjbekérők')?></h3>
              <a href="/dokumentumok/dijbekero"><?=__('Tovább az összes díjbekérőhöz')?></a>
            </div>
            <div class="count">
              <div class="count-wrapper"><div class="num"><?=(int)$doc['total_num']?></div></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="wblock color-green">
        <div class="data-container">
          <?php $doc = $this->dashboard['requests']; ?>
          <?php if ( $doc && count($doc['data']) != 0): ?>
            <div class="data-list requests-dashboard">
              <div class="wrapper">
                <div class="header">
                  <div class="holder">
                    <div class="status"><?=__('Irány')?></div>
                    <div class="data"><?=__('Szolgáltatások')?></div>
                    <div class="cash"><?=__('Keretösszeg (nettó)')?></div>
                    <div class="add-at"><?=__('Időpont')?></div>
                  </div>
                </div>
                <?php foreach ((array)$doc['data'] as $hash => $d): ?>
                <div class="list-item">
                  <div class="holder">
                    <div class="status">
                      <?=($d['my_relation'] == 'to')?__('Beérkező'):__('Kimenő')?>
                    </div>
                    <div class="data">
                      <div class="req-services">
                        <strong><?=sprintf(__('%d igényelt szolgáltatás'), count($d['services']))?></strong>
                        <?php if (count($d['services']) != 0): ?>
                          <div class="serv-tooltip">
                            <?php foreach ((array)$d['services'] as $config => $serv): ?>
                            <div class="serv"><?=$serv?></div>
                            <?php endforeach; ?>
                          </div>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="cash"><?=Helper::cashFormat($d['total_cash'])?></div>
                    <div class="add-at"><?=date('Y/m/d H:i', strtotime($d['offerout_at']))?></div>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php else: ?>
            <div class="no-data-view">
              <div class="ico"><i class="far fa-circle"></i></div>
              <div class="text"><?=__('Nincsenek ajánlatkérései.')?></div>
            </div>
          <?php endif; ?>
        </div>
        <div class="data-footer">
          <div class="d-flex align-items-center">
            <div class="title">
              <h3><?=__('Ajánlat kérések')?></h3>
              <a href="/ajanlatkeresek/osszes"><?=__('Tovább az összes ajánlatkéréshez')?></a>
            </div>
            <div class="count">
              <div class="count-wrapper"><div class="num"><?=(int)count($doc['data'])?></div></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4">
    <?php if (!$this->is_admin_logged): ?>
      <div class="wblock color-blue">
        <div class="data-container">
          <?php $doc = $this->dashboard['messanger']; ?>
          <?php if (count($doc['sessions']) == 0): ?>
            <div class="no-data-view">
              <div class="ico"><i class="far fa-check-circle"></i></div>
              <div class="text"><?=__('Nincs folyamatban lévő beszélgetés.')?></div>
            </div>
          <?php else: ?>
            <div class="data-list">
              <div class="wrapper">
                <div class="header">
                  <div class="holder">
                    <div class="data"><?=__('Projekt / Partner')?></div>
                    <div class="unreaded-msg"><?=__('Üzenetek')?></div>
                  </div>
                </div>
                <?php foreach ((array)$doc['sessions'] as $d): ?>
                <div class="list-item">
                  <div class="holder">
                    <div class="data">
                      <div class="title">
                        <a href="/uzenetek/session/<?=$d['sessionid']?>" target="_blank"><strong><?=$d['project_title']?></strong></a>
                      </div>
                      <div class="subtitle">
                        <span><?=$d['partner_nev']?></span>
                      </div>
                    </div>
                    <div class="unreaded-msg center">
                      <?=$d['message_total']?><? if((int)$d['message_unreaded'] != 0):?> / <strong style="color:red;"><?=sprintf(__('%d új'), (int)$d['message_unreaded'])?></strong><? endif; ?>
                    </div>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
        <div class="data-footer">
          <div class="d-flex align-items-center">
            <div class="title">
              <h3><?=__('Beszélgetések')?></h3>
              <a href="/uzenetek"><?=__('Tovább az összes üzenethez')?></a>
            </div>
            <div class="count">
              <div class="count-wrapper"><div class="num"><?=(int)$doc['unreaded']?></div></div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <?php if ($this->is_admin_logged): ?>
      <div class="wblock color-blue">
        <div class="data-container">
          <?php $doc = $this->dashboard['szamla']['all']; ?>
          <?php if ((int)$doc['total_num'] == 0): ?>
          <div class="no-data-view">
            <div class="ico"><i class="far fa-check-circle"></i></div>
            <div class="text"><?=__('Jelenleg nincs számla.')?></div>
          </div>
          <?php else: ?>
            <div class="data-list">
              <div class="wrapper">
                <div class="header">
                  <div class="holder">
                    <div class="data"><?=__('Adatok')?></div>
                    <div class="relation"><?=__('Hozzáadta')?></div>
                    <div class="add-at"><?=__('Határidő')?></div>
                  </div>
                </div>
                <?php foreach ((array)$doc['data'] as $d): ?>
                <div class="list-item">
                  <div class="holder">
                    <div class="data">
                      <div class="title">
                        <a href="/doc/<?=$d['hashkey']?>" target="_blank"><strong><?=$d['name']?></strong></a>
                      </div>
                      <div class="subtitle">
                        <?php if ($d['ertek'] != 0): ?>
                        <span class="doc-ertek"><strong><?=\Helper::cashFormat($d['ertek'])?></strong> <?=__('Ft + ÁFA')?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="relation">
                      <?php if ($this->is_admin_logged): ?>
                        <strong><a href="/account/?t=edit&ID=<?=$d['user_id']?>&ret=/"><?=$d['user_nev']?></a></strong>
                      <?php else: ?>
                        <?=($d['is_me'])?__('Én'):__('Partner')?>
                      <?php endif; ?>
                    </div>
                    <div class="add-at">
                      <?=($d['expire_at'] != '') ? date('Y/m/d', strtotime($d['expire_at'])) : '<em>N/A</em>'?>
                    </div>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
        <div class="data-footer">
          <div class="d-flex align-items-center">
            <div class="title">
              <h3><?=__('Számlák')?></h3>
              <a href="/dokumentumok/szamla"><?=__('Tovább az összes számlára')?></a>
            </div>
            <div class="count">
              <div class="count-wrapper"><div class="num"><?=(int)$doc['total_num']?></div></div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
    </div>
    <div class="col-md-8">
      <div class="wblock color-blue">
        <div class="data-container">
          <?php $doc = $this->dashboard['projects']; ?>
          <?php if (empty($doc)): ?>
            <div class="no-data-view">
              <div class="ico"><i class="fab fa-buffer"></i></div>
              <div class="text"><?=__('Nincsenek folyamatban lévő projektek.')?></div>
            </div>
          <?php else: ?>
            <div class="data-list">
              <div class="wrapper">
                <div class="header">
                  <div class="holder">
                    <div class="data"><?=__('Projekt')?></div>
                    <div class="progress-status"><?=__('Állapot')?></div>
                    <div class="progress-status"><?=__('Díjfizetés')?></div>
                    <div class="add-at"><?=__('Létrejött')?></div>
                  </div>
                </div>
                <?php foreach ((array)$doc as $d): ?>
                <div class="list-item">
                  <div class="holder">
                    <div class="data">
                      <div class="title">
                        <a href="/projektek/projekt/<?=$d['order_hashkey']?>" target="_blank"><strong><?=$d['title']?></strong></a>
                      </div>
                      <div class="subtitle">
                        <?php if ($this->is_admin_logged): ?>
                          <span><strong title="<?=__('Ajánlatkérő')?>"><?=$d['user_requester']['data']['nev']?></strong> <-> <strong title="<?=__('Szolgáltató')?>"><?=$d['user_servicer']['data']['nev']?></strong></span>
                        <?php endif; ?>
                        <span><?=__('Fizetve (nettó)')?>: <strong><span class="allprice"><?=\Helper::cashFormat($d['offer']['price'])?></span> /<span class="paidprice"><?=\Helper::cashFormat($d['paidamount'])?></span></strong></span>
                      </div>
                    </div>
                    <div class="progress-status">
                      <div class="progress">
                        <div class="progress-bar <?=\Helper::progressBarColor($d['status_percent'])?>" role="progressbar" style="width: <?=$d['status_percent']?>%;" aria-valuenow="<?=$d['status_percent']?>" aria-valuemin="0" aria-valuemax="100"><?=$d['status_percent']?>%</div>
                      </div>
                    </div>
                    <div class="progress-status">
                      <div class="progress">
                        <div class="progress-bar <?=\Helper::progressBarColor($d['paying_percent'])?>" role="progressbar" style="width: <?=$d['paying_percent']?>%;" aria-valuenow="<?=$d['paying_percent']?>" aria-valuemin="0" aria-valuemax="100"><?=$d['paying_percent']?>%</div>
                      </div>
                    </div>
                    <div class="add-at center">
                      <?=date('Y/m/d', strtotime($d['created_at']))?>
                    </div>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
        <div class="data-footer">
          <div class="d-flex align-items-center">
            <div class="title">
              <h3><?=__('Aktuális projektek')?></h3>
              <a href="/projektek"><?=__('Tovább az összes projekthez')?></a>
            </div>
            <div class="count">
              <div class="count-wrapper"><div class="num"><?=$this->badges['projects']['inprogress']?></div></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<? endif;?>
</div>

<pre><?php //print_r($this->dashboard['requests']); ?></pre>
