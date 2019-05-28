<div class="page-desc">
  <?=__('Ezen az oldalon található beállítási lehetőségekkel paraméterezheti cége portfólióját. Az ajánlatkérők az Ön beállításai alapján lesznek cégéhez irányítva, amennyiben illeszkedik az ajánlatkérő igénye a cég portfóliójához.')?>
</div>
<?php
  $item_ids = array();
  $subservice_ids = array();
  $service_ids = array();
  $config_selected = array();

  if ($this->user_services) {
    foreach ((array)$this->user_services as $us) {
      if (!in_array($us['item_id'], $item_ids)) {
        $item_ids[] = (int)$us['item_id'];
      }
      if (!in_array($us['subservice_id'], $subservice_ids)) {
        $subservice_ids[] = (int)$us['subservice_id'];
      }
      if (!in_array($us['service_id'], $service_ids)) {
        $service_ids[] = (int)$us['service_id'];
      }
      if (!in_array($us['configval'], $config_selected)) {
        $config_selected[] = $us['configval'];
      }
    }
  }
?>
<?=$this->msg?>
<div class="row">
  <div class="col-md-8">
    <form class="" action="" method="post">
      <div class="wblock color-blue">
        <div class="data-header">
          <div class="d-flex align-items-center">
            <div class="col title">
              <i class="fas fa-file-invoice"></i> <?=__('Szolgáltatásaim beállítása')?>
            </div>
            <div class="col right"></div>
          </div>
        </div>
        <div class="data-container">
          <div class="dc-padding">
            <?php if (count($item_ids)): ?>
            <div class="selected-services-info">
              <?=sprintf(__('%d darab aktív szolgáltatás van kiválasztva!'), count($item_ids))?>
            </div>
            <?php endif; ?>
            <strong><?=__('Válassza ki azokat a szolgáltatásokat, melyeket cége teljesíteni tud')?>:</strong>
            <div class="services-config-settings">
              <?php foreach ( (array)$this->lists as $service ): ?>
              <div class="service <?php if (in_array($service['ID'], $service_ids)): ?>has-selected<?php endif; ?>">
                <div class="head">
                  <div class="d-flex align-items-center">
                    <div class="col">
                      <?=$service['neve']?> <?php if (in_array($service['ID'], $service_ids)): ?><span class="active-label"><?=__('kiválasztva')?></span><?php endif; ?>
                    </div>
                    <div class="col text-right listtoggler">
                      <a href="javascript:void(0);" onclick="$('#service_child<?=$service['ID']?>').slideToggle(300)">
                        <?php if ($service['child']): ?>
                            <?=sprintf(__('%d alszolgáltatás'), count($service['child']))?> <i class="fas fa-angle-down"></i>
                        <?php endif; ?>
                      </a>
                    </div>
                  </div>
                </div>
                <?php if ($service['child']): ?>
                <div class="servicesubs" id="service_child<?=$service['ID']?>">
                  <?php foreach ( (array)$service['child'] as $subservice ): ?>
                  <div class="subservice">
                    <div class="head"><?=$subservice['neve']?> <?php if (in_array($subservice['ID'], $subservice_ids)): ?><span class="active-label"><?=__('kiválasztva')?></span><?php endif; ?></div>
                    <?php if ($subservice['child']): ?>
                    <div class="subserviceitems">
                      <?php foreach ((array)$subservice['child'] as $item): ?>
                      <div class="serviceitem">
                        <input type="checkbox" class="ccb" id="servitem<?=$item['ID']?>" name="services[]" <?=(in_array($service['ID'].'_'.$subservice['ID'].'_'.$item['ID'], $config_selected))?'checked="checked"':''?> value="<?=$service['ID']?>_<?=$subservice['ID']?>_<?=$item['ID']?>"> <label for="servitem<?=$item['ID']?>"><?=$item['neve']?></label>
                      </div>
                      <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                  </div>
                  <?php endforeach; ?>
                </div>
                <?php endif; ?>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <div class="data-footer">
          <div class="d-flex align-items-center">
            <div class="col text-left title">
              <a href="javascript:void(0);" onclick="extractOwnedItems()"><?=__('Sajátjaim kibontása')?> <i class="fas fa-angle-down"></i></a> &nbsp;&nbsp;
              <a href="javascript:void(0);" onclick="extractItems()"><?=__('Összes kibontása')?> <i class="fas fa-angle-down"></i></a> &nbsp;&nbsp;
              <a href="javascript:void(0);" onclick="closeItems()"><?=__('Összes becsukása')?> <i class="fas fa-angle-up"></i></a>
            </div>
            <div class="col text-right">
              <button class="btn btn-success" name="changeCompanyServices"><?=__('Beállítás mentése')?> <i class="far fa-save"></i></button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript">
  function extractItems() {
    $.each($('.services-config-settings .servicesubs'), function(i,e){
      $(e).slideDown(100);
    });
  }
  function extractOwnedItems() {
    $.each($('.services-config-settings .service.has-selected .servicesubs'), function(i,e){
      $(e).slideDown(100);
    });
  }
  function closeItems() {
    $.each($('.services-config-settings .servicesubs'), function(i,e){
      $(e).slideUp(100);
    });
  }
</script>
