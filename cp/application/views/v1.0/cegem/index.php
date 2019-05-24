<div class="page-desc">
  <?=__('Ezen az oldalon található beállítási lehetőségekkel paraméterezheti cége portfólióját. Az ajánlatkérők az Ön beállításai alapján lesznek cégéhez irányítva, amennyiben illeszkedik az ajánlatkérő igénye a cég portfóliójához.')?>
</div>
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
            <strong><?=__('Válassza ki azokat a szolgáltatásokat, melyeket cége teljesíteni tud')?>:</strong>
            <div class="services-config-settings">
              <?php foreach ( $this->lists as $service ): ?>
              <div class="service">
                <div class="head">
                  <div class="d-flex align-items-center">
                    <div class="col">
                      <?=$service['neve']?>
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
                    <div class="head"><?=$subservice['neve']?></div>
                    <?php if ($subservice['child']): ?>
                    <div class="subserviceitems">
                      <?php foreach ((array)$subservice['child'] as $item): ?>
                      <div class="serviceitem">
                        <input type="checkbox" class="ccb" id="servitem<?=$item['ID']?>" name="services[]" value="<?=$service['ID']?>_<?=$service['ID']?>_<?=$item['ID']?>"> <label for="servitem<?=$item['ID']?>"><?=$item['neve']?></label>
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
              <a href="javascript:void(0);" onclick="extractItems()"><?=__('Összes kibontása')?> <i class="fas fa-angle-down"></i></a> &nbsp;&nbsp; <a href="javascript:void(0);" onclick="closeItems()"><?=__('Összes becsukása')?> <i class="fas fa-angle-up"></i></a>
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
  function closeItems() {
    $.each($('.services-config-settings .servicesubs'), function(i,e){
      $(e).slideUp(100);
    });
  }
</script>
