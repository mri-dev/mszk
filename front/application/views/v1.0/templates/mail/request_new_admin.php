<? require "head.php"; ?>
<h2>Új ajánlatkérés érkezett!</h2>
<p>A feldolgozás érdekében jelentkezzen be az <a href="<?=ADMROOT?>belepes/?admhandler=ajanlatkeresek&request=<?=$request['hashkey']?>"><strong>adminisztrációs felületre</strong></a>. Ajánlatkérések között ki tudja ajánlani partnereinek az igényt.</p>
<div><h3>Ajánlatkérő adatai</h3></div>
<table class="if">
  <tbody>
    <tr>
      <th>Név:</th>
      <td><?=$nev?></td>
    </tr>
    <tr>
      <th>E-mail cím:</th>
      <td><?=$data['requester']['email']?></td>
    </tr>
    <tr>
      <th>Cég:</th>
      <td><?=$data['requester']['company']?></td>
    </tr>
    <tr>
      <th>Telefonszám:</th>
      <td><?=$data['requester']['phone']?></td>
    </tr>
    <tr>
      <th>Felhasználó ID:</th>
      <td><?=$user_id?></td>
    </tr>
    <tr>
      <th>Új regiszráció:</th>
      <td><?=($new_user_id != '')?'Igen':'Nem'?></td>
    </tr>
    <tr>
      <th>Üzenete:</th>
      <td><?=$data['requester']['message']?></td>
    </tr>
    <tr>
      <th>Ajánlatkérés azonosítók:</th>
      <td>
        <div class="">#ID: <?=$request['id']?></div>
        <div class="">Hashkey: <?=$request['hashkey']?></div>
      </td>
    </tr>
  </tbody>
</table>
<br>

<div><h3>Az ajánlatkérésének szövege</h3></div>
<div class="">
  <?php if ( $configuration['rawdb']['message'] != ''): ?>
    <?php echo nl2br($configuration['rawdb']['message']); ?>
  <?php else: ?>
    <em><?=__('nem lett megadva')?></em>
  <?php endif; ?>
</div>

<br>

<div><h3>Leadott konfigurációs paraméterek</h3></div>
<div class="selected-services">
  <?php foreach ((array)$configuration['services']['items'] as $service): ?>
  <div class="service">
    <div class="head"><?php echo $service['neve']; ?></div>
    <div class="service-describe">
      <div class="data">
        <div class="line">
          <div class="d-flex">
            <div class="h"><?=__('Kezdő időpont')?>:</div>
            <div class="v"><strong><?=($configuration['overall_service_details'][$service['ID']]['date_start'] != '')?date('Y. m. d.', strtotime($configuration['overall_service_details'][$service['ID']]['date_start'])):''?></strong><? if($configuration['overall_service_details'][$service['ID']]['date_start'] == ''): ?><em><?=__('nem lett meghatározva')?></em><? endif; ?></div>
          </div>
        </div>
        <div class="line">
          <div class="d-flex">
            <div class="h"><?=__('Időtartam')?>:</div>
            <div class="v"><strong><?=$configuration['overall_service_details'][$service['ID']]['date_duration']?></strong><? if($configuration['overall_service_details'][$service['ID']]['date_start'] == ''): ?><em><?=__('nem lett meghatározva')?></em><? endif; ?></div>
          </div>
        </div>
        <div class="line">
          <div class="d-flex">
            <div class="h"><?=__('Teljes költségkeret')?>:</div>
            <div class="v"><strong><?=\Helper::cashFormat($configuration['overall_service_details'][$service['ID']]['cash_total'])?><?=($configuration['overall_service_details'][$service['ID']]['cash_total'] != '')?' '.__('Ft + ÁFA'):''?></strong><? if($configuration['overall_service_details'][$service['ID']]['cash_total'] == ''): ?><em><?=__('nem lett meghatározva')?></em><? endif; ?></div>
          </div>
        </div>
        <div class="line mdesc">
          <div class="h"><?=__('Megjegyzés / Részletek')?>:</div>
          <div class="v"><strong><?=$configuration['overall_service_details'][$service['ID']]['description']?></strong><? if($configuration['overall_service_details'][$service['ID']]['description'] == ''): ?><em><?=__('nem lett meghatározva')?></em><? endif; ?></div>
        </div>
      </div>
    </div>
    <?php foreach ((array)$configuration['subservices']['items'] as $subservice): if($subservice['szulo_id'] != $service['ID']) continue; ?>
    <div class="subservice">
      <div class="head"><?php echo $subservice['neve']; ?></div>
      <?php foreach ((array)$configuration['subservicesitems']['items'] as $subserviceitem): if($subserviceitem['szulo_id'] != $subservice['ID']) continue; ?>
      <div class="subserviceitem">
        &mdash; <?php echo $subserviceitem['neve']; ?>
        <?php if (!empty($configuration['cash']['subservicesitems'][$subservice['ID']][$subserviceitem['ID']])): ?>
          <span class="cash"><?php echo number_format($configuration['cash']['subservicesitems'][$subservice['ID']][$subserviceitem['ID']], 0, '.', ' '); ?> Ft</span>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
      <?php if (!empty($configuration['cash']['subservices_overall'][$subservice['ID']])): ?>
        <div class="cashall">
          <div class="cashall-header">Költségkeret:</div>
          <?php echo number_format($configuration['cash']['subservices_overall'][$subservice['ID']], 0, '.', ' '); ?> <?=__('Ft + ÁFA')?>
        </div>
      <?php endif; ?>
      <?php if (!empty($configuration['subservices_descriptions'][$subservice['ID']])): ?>
        <div class="comment">
          <div class="comment-header">Megjegyzés / Igények:</div>
          <?php echo nl2br($configuration['subservices_descriptions'][$subservice['ID']]); ?>
        </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endforeach; ?>
  <div class=serv-footer>
    Teljes költségvetés összege: <strong><?php echo number_format($configuration['cash']['total'], 0, '.', ' '); ?> <?=__('Ft + ÁFA')?></strong>
  </div>
</div>

<style media="all">
/* line 854, sass/media.scss */
.selected-services {
  border: 0.5px solid #d7d7d7;
  padding: 0.5px;
  overflow: hidden;
  border-radius: 8px;
}
/* line 860, sass/media.scss */
.selected-services .service:first-child {
  border-top: none;
}
/* line 863, sass/media.scss */
.selected-services .service > .head {
  background: #2c62c7;
  padding: 8px 15px;
  font-weight: bold;
  text-transform: uppercase;
  border-bottom: 0.5px solid #4e7ed8;
  color: white;
  font-size: 1.2rem;
}
/* line 872, sass/media.scss */
.selected-services .service .subservice {
  border-bottom: 0.5px dashed #eaeaea;
  padding: 10px 15px;
}
/* line 875, sass/media.scss */
.selected-services .service .subservice > .head {
  color: #ff7979;
  padding: 5px;
  font-size: 1.1rem;
  font-weight: bold;
  background: #eeeeee;
}
/* line 882, sass/media.scss */
.selected-services .service .subservice .subserviceitem {
  font-size: 0.9rem;
  margin: 4px 0;
}
/* line 886, sass/media.scss */
.selected-services .service .subservice .subserviceitem .cash {
  background: #757575;
  color: white;
  border-radius: 3px;
  margin: 0 4px;
  font-size: 0.7rem;
  padding: 2px 5px;
}
/* line 899, sass/media.scss */
.selected-services .comment,
.selected-services .cashall {
  font-size: 0.8rem;
  color: #555555;
  line-height: 1.3;
  margin: 15px 0 10px 0;
}
/* line 904, sass/media.scss */
.selected-services .comment .comment-header, .selected-services .comment .cashall-header,
.selected-services .cashall .comment-header,
.selected-services .cashall .cashall-header {
  color: black;
  margin: 0 0 4px 0;
  font-weight: bold;
}
/* line 910, sass/media.scss */
.selected-services .serv-footer {
  padding: 25px;
  font-size: 1.1rem;
  background: #f1f1f1;
  border-top: 0.5px solid #d7d7d7;
}

/* line 991, sass/media.scss */
.selected-services .service-describe > .data {
  padding: 10px 12px;
}
/* line 993, sass/media.scss */
.selected-services .service-describe > .data .line + .line {
  margin: 10px 0 0 0;
}
/* line 998, sass/media.scss */
.selected-services .service-describe > .data .line .h {
  color: #aaaaaa;
}
/* line 1001, sass/media.scss */
.selected-services .service-describe > .data .line .d-flex {
  align-items: center;
}
/* line 1003, sass/media.scss */
.selected-services .service-describe > .data .line .d-flex > div {
  padding: 5px;
}
/* line 1006, sass/media.scss */
.selected-services .service-describe > .data .line .d-flex .h {
  flex-basis: 180px;
}
/* line 1009, sass/media.scss */
.selected-services .service-describe > .data .line .d-flex .v {
  flex: 1;
}
/* line 1014, sass/media.scss */
.selected-services .service-describe > .data .line.mdesc .h, .selected-services .service-describe > .data .line.mdesc .v {
  padding: 5px;
}
/* line 1018, sass/media.scss */
.selected-services .service-describe > .data .line.mdesc .v {
  white-space: pre-wrap;
}
</style>

<? require "footer.php"; ?>
