<?php echo $this->bmsg; ?>
<div class="wblock color-green">
  <div class="data-header">
    <div class="d-flex">
      <div class="col-md-8 title">
        <i class="fas fa-plus"></i> <?=__('Új dokumentum hozzáadása')?>
      </div>
      <div class="col right closer">
        <a href="/dokumentumok/"><i class="fas fa-times"></i></a>
      </div>
    </div>
  </div>
  <div class="data-container">
    <div class="dc-padding">
      <form class="" action="" method="post">
        <div class="row">
          <div class="col-md-8">
            <label for="name"><?=__('Elnevezés')?> *</label>
            <input type="text" id="name" name="name" class="form-control" value="">
          </div>
          <div class="col-md-4">
            <label for="folder"><?=__('Mappa / Besorolás')?></label>
            <select class="form-control" id="folder" name="folder" onchange="filterFormInputs($(this))">
              <?php foreach ((array)$this->folders as $folder):?>
              <option value="<?=$folder['hashkey']?>" <?=((isset($_POST['szulo_id']) && $_POST['szulo_id'] == $folder['ID']))?'selected="selected"':(($this->folder && $this->folder['szulo_id'] == $folder['ID'])?'selected="selected"':'')?>><?=$folder['name']?></option>
              <?php if ($folder['child']): ?>
                <?php foreach ($folder['child'] as $cfolder): ?>
                  <option value="<?=$cfolder['hashkey']?>">&mdash; <?=$cfolder['name']?></option>
                <?php endforeach; ?>

              <?php endif; ?>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <label for="docfile"><?=__('File / Dokumentum kiválasztása')?></label>
            <div class="input-group">
              <input type="text" id="docfile" readonly="readonly" name="docfile" class="form-control" value="">
              <div class="input-group-append"><span class="input-group-text"><a title="<?=__('Fájlok feltöltése és kiválasztása a dokumentum hozzáadásához.')?>" href="<?=FILE_BROWSER_IMAGE?>&field_id=docfile" data-fancybox-type="iframe" class="iframe-btn" ><i class="fas fa-file-upload"></i></a></span></div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3">
            <label for="avaiable_to"><?=__('Fájl elérhető eddig')?></label>
            <input type="date" id="avaiable_to" name="avaiable_to" class="form-control" value="">
          </div>
          <div class="col-md-3" style="display:none;" id="select_expire_at">
            <label for="expire_at"><?=__('Határidő / Fizetési határidő')?></label>
            <input type="date" id="expire_at" name="expire_at" class="form-control" value="">
          </div>
          <div class="col-md-3" style="display:none;" id="select_teljesites_at">
            <label for="teljesites_at"><?=__('Teljesítés ideje')?></label>
            <input type="date" id="teljesites_at" name="teljesites_at" class="form-control" value="">
          </div>
          <div class="col-md-3" style="display:none;" id="select_ertek">
            <label for="ertek"><?=__('Érték')?></label>
            <div class="input-group">
              <input type="number" id="ertek" name="ertek" min="0" step="0.01" class="form-control" value="">
              <div class="input-group-append"><span class="input-group-text"><?=__('Ft + ÁFA')?></span></div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 right">
            <button type="submit" class="btn btn-success" name="addFile" value="1"><?=__('Hozzáadás')?> <i class="fas fa-plus"></i></button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
  function filterFormInputs( e ) {
    var stext = e.find('option:selected').text();

    $('#select_expire_at').hide(0).find('input').val('');
    $('#select_teljesites_at').hide(0).find('input').val('');
    $('#select_ertek').hide(0).find('input').val('');

    if( stext == 'Díjbekérő' || stext == 'Számla' ){
      $('#select_expire_at').show(0);
      $('#select_teljesites_at').show(0);
      $('#select_ertek').show(0);
    }
  }
</script>
