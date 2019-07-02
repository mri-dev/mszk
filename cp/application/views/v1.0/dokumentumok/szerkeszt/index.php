<?php echo $this->bmsg; ?>
<div class="wblock color-green">
  <div class="data-header">
    <div class="d-flex">
      <div class="col-md-8 title">
        <i class="fas fa-edit"></i> <?=__('Dokumentum szerkesztése')?>
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
          <div class="col-md-8">
            <label for="name"><?=__('Elnevezés')?> *</label>
            <input type="text" id="name" name="name" class="form-control" value="<?=$this->doc['name']?>">
          </div>
          <div class="col-md-4">
            <input type="hidden" name="prev_folder" value="<?=$this->doc['folders'][0]['folder_hashkey']?>">
            <label for="folder"><?=__('Mappa / Besorolás')?></label>
            <select class="form-control" id="folder" name="folder" onchange="filterFormInputs($(this))">
              <?php foreach ((array)$this->folders as $folder):?>
              <option value="<?=$folder['hashkey']?>" <?=($this->doc['folders'][0]['folder_hashkey'] == $folder['hashkey'])?'selected="selected"':''?>><?=$folder['name']?></option>
              <?php if ($folder['child']): ?>
                <?php foreach ($folder['child'] as $cfolder): ?>
                  <option value="<?=$cfolder['hashkey']?>" <?=($this->doc['folders'][0]['folder_hashkey'] == $cfolder['hashkey'])?'selected="selected"':''?>>&mdash; <?=$cfolder['name']?></option>
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
              <input type="text" id="docfile" readonly="readonly" name="docfile" class="form-control" value="<?=$this->doc['docfile']?>">
              <div class="input-group-append"><span class="input-group-text"><a title="<?=__('Fájlok feltöltése és kiválasztása a dokumentum hozzáadásához.')?>" href="<?=FILE_BROWSER_IMAGE?>&field_id=docfile" data-fancybox-type="iframe" class="iframe-btn" ><i class="fas fa-file-upload"></i></a></span></div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3">
            <label for="avaiable_to"><?=__('Fájl elérhető eddig')?></label>
            <input type="date" id="avaiable_to" name="avaiable_to" class="form-control" value="<?=(!empty($this->doc['avaiable_to']))?date('Y-m-d', strtotime($this->doc['avaiable_to'])):''?>">
          </div>
          <div class="col-md-3" style="display:none;" id="select_expire_at">
            <label for="expire_at"><?=__('Határidő / Fizetési határidő')?></label>
            <input type="date" id="expire_at" name="expire_at" class="form-control" value="<?=(!empty($this->doc['expire_at']))?date('Y-m-d', strtotime($this->doc['expire_at'])):''?>">
          </div>
          <div class="col-md-3" style="display:none;" id="select_teljesites_at">
            <label for="teljesites_at"><?=__('Teljesítés ideje')?></label>
            <input type="date" id="teljesites_at" name="teljesites_at" class="form-control" value="<?=(!empty($this->doc['teljesites_at']))?date('Y-m-d', strtotime($this->doc['teljesites_at'])):''?>">
          </div>
          <div class="col-md-3" style="display:none;" id="select_ertek">
            <label for="ertek"><?=__('Érték')?></label>
            <div class="input-group">
              <input type="number" id="ertek" name="ertek" min="0" step="0.01" class="form-control" value="<?=$this->doc['ertek']?>">
              <div class="input-group-append"><span class="input-group-text"><?=__('Ft + ÁFA')?></span></div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 left">
            <a href="/dokumentumok/delete/<?=$this->doc['hashkey']?>" class="btn btn-danger"><?=__('Dokumentum törlése')?> <i class="fas fa-trash"></i></a>
          </div>
          <div class="col-md-6 right">
            <button type="submit" class="btn btn-success" name="editFile" value="1"><?=__('Változások módosítása')?> <i class="fas fa-save"></i></button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(function(){
    $('#folder').trigger('change');
  });

  function filterFormInputs( e ) {
    var stext = e.find('option:selected').text();

    if( stext == 'Díjbekérő' || stext == 'Számla' ){
      $('#select_expire_at').show(0);
      $('#select_teljesites_at').show(0);
      $('#select_ertek').show(0);
    }
  }
</script>
