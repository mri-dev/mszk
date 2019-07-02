<?php echo $this->bmsg; ?>
<div class="wblock color-red">
  <div class="data-header">
    <div class="d-flex">
      <div class="col-md-8 title">
        <i class="fas fa-trash"></i> <?=__('Dokumentum végleges törlése')?>
      </div>
      <div class="col right closer">
        <a href="/dokumentumok/"><i class="fas fa-times"></i></a>
      </div>
    </div>
  </div>
  <div class="data-container">
    <div class="dc-padding">
      <form class="" action="" method="post">
        <input type="hidden" name="hashkey" value="<?=$this->doc['hashkey']?>">
        <div class="row">
          <div class="col-md-12">
            <?=__('Biztos benne, hogy véglegesen törli a dokumentumot? A művelet nem visszavonható!')?>
            <p><small><?=__('A tényleges fájl <strong>nem lesz törölve</strong>, csak a dokumentum bejegyzése. A fájlt továbbra is elérheti a feltöltött fájlok/dokumentumok között.')?></small></p>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 right">
            <button type="submit" class="btn btn-danger" name="deleteFile" value="1"><?=__('Véglegesen törlöm a dokumentum bejegyzést')?> <i class="fas fa-trash"></i></button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
