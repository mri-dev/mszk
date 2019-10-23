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
      <?php if (!$this->is_admin_logged): ?>
      <div class="col ct-info color-green">
        <div class="d-flex">
          <div class="ico">
            <div class="ico-wrapper"><i class="fas fa-file-export"></i></div>
          </div>
          <div class="title"><?=__('Ajánlatkéréseim')?><div class="line"></div></div>
          <div class="count"><?=(int)$this->badges['offers']['outbox']?></div>
        </div>
      </div>
      <?php if ($this->_USERDATA['data']['user_group'] == 'szolgaltato'): ?>
        <div class="col ct-info color-green">
          <div class="d-flex">
            <div class="ico">
              <div class="ico-wrapper"><i class="fas fa-file-import"></i></div>
            </div>
            <div class="title"><?=__('Bejövő ajánlatkérések')?><div class="line"></div></div>
            <div class="count"><?=(int)$this->badges['offers']['inbox']?></div>
          </div>
        </div>
      <?php endif; ?>
      <?php else: ?>
        <div class="col ct-info color-green">
          <div class="d-flex">
            <div class="ico">
              <div class="ico-wrapper"><i class="fas fa-file-import"></i></div>
            </div>
            <div class="title"><?=__('Bejövő ajánlatkérések')?><div class="line"></div></div>
            <div class="count"><?=(int)$this->notoffered_requests?></div>
          </div>
        </div>
      <?php endif; ?>
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
                <h3><?=__('Olvasatlan üzenetek')?></h3>
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
      <?php if ($this->is_admin_logged): ?>
      <div class="wblock color-green">
        <div class="data-container">
          <?php if (empty($this->requests)): ?>
          <div class="no-data-view">
            <div class="ico"><i class="far fa-check-circle"></i></div>
            <div class="text"><?=__('Minden rendben! Nincs feldolgozatlan ajánlat kérés!')?></div>
          </div>
          <?php else: ?>
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th><?=__('Név')?></th>
                  <th><?=__('E-mail')?></th>
                  <th><?=__('Szolgáltatások')?></th>
                  <th class="center"><?=__('Teljes költségvetés')?></th>
                  <th class="center"><?=__('Igényelte')?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ( (array)$this->requests as $request ): ?>
                <tr>
                  <td><a href="/ajanlatkeresek/feldolgozatlan/<?=$request['hashkey']?>"><?=$request['name']?></a></td>
                  <td><?=$request['email']?></td>
                  <td>
                    <div class="request-services-tree">
                      <?php foreach ((array)$request['services_list'] as $serv): ?>
                      <div class="service">
                        <?=$serv['neve']?>
                        <?php if ($serv['child'] && !empty($serv['child'])): ?>
                          <?php foreach ($serv['child'] as $subserv): ?>
                          <div class="subservice">
                            &mdash; <?=$subserv['neve']?>
                            <?php if ($subserv['child'] && !empty($subserv['child'])): ?>
                              <?php foreach ($subserv['child'] as $subservitem): ?>
                              <div class="subserviceitem">
                                &mdash;&mdash; <?=$subservitem['neve']?>
                              </div>
                              <?php endforeach; ?>
                            <?php endif; ?>
                          </div>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </div>
                      <?php endforeach; ?>
                    </div>
                  </td>
                  <td class="center"><?=\Helper::cashFormat($request['cash_total'])?> <?=__('Ft + ÁFA')?></td>
                  <td class="center"><?=$request['requested_at']?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>
        <div class="data-footer">
          <div class="d-flex align-items-center">
            <div class="title">
              <h3><?=__('Feldolgozatlan ajánlatkérések')?></h3>
              <?php if ($this->is_admin_logged): ?>
                <a href="/ajanlatkeresek/feldolgozatlan"><?=__('Tovább az igények feldolgozásához')?></a>
              <?php endif; ?>
            </div>
            <div class="count">
              <div class="count-wrapper"><div class="num"><?=$this->notoffered_requests?></div></div>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>

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
                    <div class="paying-status"><?=__('Díjfizetés')?></div>
                    <div class="add-at"><?=__('Létrejött')?></div>
                  </div>
                </div>
                <?php foreach ((array)$doc as $d): ?>
                <div class="list-item">
                  <div class="holder">
                    <div class="data">
                      <div class="title">
                        <a href="/projektek/projekt/<?=$d['order_hashkey']?>" target="_blank">
                          <strong><?=$d['admin_title']?></strong>
                          <?php if ($d['title'] != '' && !$this->is_admin_logged): ?>
                          <div class="user-title"><?=$d['title']?></div>
                          <?php endif; ?>
                        </a>
                      </div>
                      <div class="subtitle">
                        <?php if ($this->is_admin_logged): ?>
                          <span><strong class="txt-requester" title="<?=__('Ajánlatkérő')?>"><?=$d['user_requester']['data']['nev']?></strong> <-> <strong class="txt-servicer" title="<?=__('Szolgáltató')?>"><?=$d['user_servicer']['data']['nev']?></strong></span>
                        <?php else: ?>
                          <span><?=__('Státuszom')?>: <strong class="txt-<?=$d['my_relation']?>"><?=($d['my_relation'] == 'requester')?__('Ajánlatkérő'):__('Szolgáltató')?></strong> </span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="progress-status">
                      <div class="progress">
                        <div class="progress-bar <?=\Helper::progressBarColor($d['status_percent'])?>" role="progressbar" style="width: <?=$d['status_percent']?>%;" aria-valuenow="<?=$d['status_percent']?>" aria-valuemin="0" aria-valuemax="100"><?=$d['status_percent']?>%</div>
                      </div>
                    </div>
                    <div class="paying-status">
                      <?php if ($this->is_admin_logged || $d['my_relation'] == 'requester' ): ?>
                      <div class="d-flex align-items-center">
                        <div class="ptext txt-requester"><?=__('Ajánlatkérő')?></div>
                        <div class="pprogress">
                          <div class="progress">
                            <div class="progress-bar <?=\Helper::progressBarColor($d['requester_paying_percent'])?>" role="progressbar" style="width: <?=$d['requester_paying_percent']?>%;" title="<?=($d['requester_paidamount'] > 0)?__('Befizetve: ').\Helper::cashFormat($d['requester_paidamount']).' Ft':''?>" aria-valuenow="<?=$d['requester_paying_percent']?>" aria-valuemin="0" aria-valuemax="100"><?=$d['requester_paying_percent']?>%</div>
                          </div>
                        </div>
                      </div>
                      <?php endif; ?>
                      <?php if ($this->is_admin_logged || $d['my_relation'] == 'servicer' ): ?>
                      <div class="d-flex align-items-center">
                        <div class="ptext txt-servicer"><?=__('Szolgáltató')?></div>
                        <div class="pprogress">
                          <div class="progress">
                            <div class="progress-bar <?=\Helper::progressBarColor($d['servicer_paying_percent'])?>" role="progressbar" style="width: <?=$d['servicer_paying_percent']?>%;" title="<?=($d['servicer_paidamount'] > 0)?__('Kifizetve: ').\Helper::cashFormat($d['servicer_paidamount']).' Ft':''?>" aria-valuenow="<?=$d['servicer_paying_percent']?>" aria-valuemin="0" aria-valuemax="100"><?=$d['servicer_paying_percent']?>%</div>
                          </div>
                        </div>
                      </div>
                      <?php endif; ?>
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

      <?php if (!$this->is_admin_logged): ?>
      <div class="wblock color-green">
        <div class="data-container">
          <?php $doc = $this->dashboard['requests_out']; ?>
          <?php if ( $doc && count($doc) != 0): ?>
            <div class="data-list requests-dashboard">
              <div class="wrapper">
                <div class="header">
                  <div class="holder">
                    <div class="data"><?=__('Tárgy')?></div>
                    <div class="ostat"><?=__('Státusz')?></div>
                    <div class="cash"><?=__('Keretösszeg (nettó)')?></div>
                    <div class="add-at"><?=__('Időpont')?></div>
                  </div>
                </div>
                <?php foreach ((array)$doc as $hash => $d): ?>
                <div class="list-item">
                  <div class="holder">
                    <div class="data">
                      <div class="req-services">
                        <div class="title">
                          <strong><a href="/ajanlatkeresek/kimeno/<?=$d['hashkey']?>"><?=$d['user_requester_title']?></a></strong>
                        </div>
                        <?=sprintf(__('%d igényelt szolgáltatás'), count($d['services']))?>
                        <?php if (count($d['services']) != 0): ?>
                          <div class="serv-tooltip">
                            <?php foreach ((array)$d['services'] as $serv): ?>
                            <div class="serv"><?=$serv['neve']?></div>
                            <?php endforeach; ?>
                          </div>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="ostat"><strong title="<?=$d['status']['title']?>" style="color:<?=$d['status']['color']?>;"><?=$d['status']['text']?></strong></div>
                    <div class="cash"><?=Helper::cashFormat($d['cash_total'])?></div>
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
              <h3><?=__('Ajánlatkéréseim')?></h3>
              <a href="/ajanlatkeresek/kimeno"><?=__('Tovább az összes ajánlatkérémhez')?></a>
            </div>
            <div class="count">
              <div class="count-wrapper"><div class="num"><?=(int)$this->badges['offers']['outbox']?></div></div>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>
      <?php if (!$this->is_admin_logged && $this->_USERDATA['data']['user_group'] == 'szolgaltato'): ?>
      <div class="wblock color-green">
        <div class="data-container">
          <?php $doc = $this->dashboard['requests_in']; ?>
          <?php if ( $doc && count($doc) != 0): ?>
            <div class="data-list requests-dashboard">
              <div class="wrapper">
                <div class="header">
                  <div class="holder">
                    <div class="data"><?=__('Tárgy')?></div>
                    <div class="ostat"><?=__('Státusz')?></div>
                    <div class="cash"><?=__('Keretösszeg (nettó)')?></div>
                    <div class="add-at"><?=__('Időpont')?></div>
                  </div>
                </div>
                <?php foreach ((array)$doc as $hash => $d): ?>
                <div class="list-item">
                  <div class="holder">
                    <div class="data">
                      <div class="req-services">
                        <div class="title">
                          <strong><a href="/ajanlatkeresek/bejovo/<?=$d['hashkey']?>"><?=sprintf(__('%d igényelt szolgáltatás'), count($d['services']))?></a></strong>
                        </div>
                        <?php if (count($d['services']) != 0): ?>
                          <div class="serv-tooltip">
                            <?php foreach ((array)$d['services'] as $serv): ?>
                            <div class="serv"><?=$serv['neve']?></div>
                            <?php endforeach; ?>
                          </div>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="ostat"><strong title="<?=$d['status']['title']?>" style="color:<?=$d['status']['color']?>;"><?=$d['status']['text']?></strong></div>
                    <div class="cash"><?=Helper::cashFormat($d['cash_total'])?></div>
                    <div class="add-at"><?=date('Y/m/d H:i', strtotime($d['offerout_at']))?></div>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php else: ?>
            <div class="no-data-view">
              <div class="ico"><i class="far fa-circle"></i></div>
              <div class="text"><?=__('Nincsenek bejövő ajánlatkérései.')?></div>
            </div>
          <?php endif; ?>
        </div>
        <div class="data-footer">
          <div class="d-flex align-items-center">
            <div class="title">
              <h3><?=__('Bejövő ajánlatkérések')?></h3>
              <a href="/ajanlatkeresek/bejovo"><?=__('Tovább az összes bejövő ajánlatkéréshez')?></a>
            </div>
            <div class="count">
              <div class="count-wrapper"><div class="num"><?=(int)$this->badges['offers']['inbox']?></div></div>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </div>
<? endif;?>
</div>

<pre><?php //print_r($this->dashboard['requests_in']); ?></pre>
