<?php if ($this->notoffered_requests && $this->notoffered_requests > 0): ?>
  <div class="alert alert-warning">
    <?php if ($this->is_admin_logged): ?>
      <?php echo sprintf(__('Összesen <strong>%d darab</strong> feldolgozatlan ajánlat kérés vár feldolgozásra!'), $this->notoffered_requests); ?>
    <?php else: ?>
      <?php echo sprintf(__('Önnek <strong>%d darab</strong> feldolgozatlan ajánlat kérés igénye van rögzítve! Az ajánlatkérések között akkor tekintheti meg, ha ki lett ajánlva partnereink felé!'), $this->notoffered_requests); ?>
    <?php endif; ?>
  </div>
<?php endif; ?>

<div class="wblock color-red">
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
              <pre><?php //print_r($request['services_list']); ?></pre>
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
        <h3><?=__('Feldolgozatlan ajánlat kérések')?></h3>
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
