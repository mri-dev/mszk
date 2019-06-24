<?php echo $this->bmsg; ?>
<?php if (isset($_GET['mode']) && $_GET['mode'] != 'delete'): ?>
<?php
  switch ($_GET['mode']) {
    case 'create':
      $mode = __('létrehozás');
      $ico = 'fas fa-folder-plus';
      $color = 'blue';
    break;
    case 'edit':
      $mode = __('szerkesztés');
      $ico = 'fas fa-edit';
      $color = 'green';
    break;
  }
?>
<div class="wblock color-<?=$color?>">
  <div class="data-header">
    <div class="d-flex">
      <div class="col-md-8 title">
        <i class="ico <?=$ico?>"></i> <?=__('Dokumentum mappa').' '.$mode?>
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
            <label for="name"><?=__('Mappa elnevezése')?> *</label>
            <input id="name" type="text" class="form-control" name="name" value="<?=(isset($_POST['name']))?$_POST['name']:(($this->folder)?$this->folder['name']:'')?>">
          </div>
          <div class="col-md-4">
            <label for="szulo_id"><?=__('Szülő mappa')?> *</label>
            <select class="form-control" id="szulo_id" name="szulo_id">
              <option value="" selected="selected"><?=__('ne legyen')?></option>
              <option value="" disabled="disabled"></option>
              <?php foreach ((array)$this->folders as $folder): ?>
              <option value="<?=$folder['hashkey']?>" <?=((isset($_POST['szulo_id']) && $_POST['szulo_id'] == $folder['ID']))?'selected="selected"':(($this->folder && $this->folder['szulo_id'] == $folder['ID'])?'selected="selected"':'')?>><?=$folder['name']?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4 left">
            <a href="/dokumentumok/folders?mode=delete&folder=<?=$this->folder['hashkey']?>" class="btn btn-sm btn-danger"><?=__('Törölni szeretném a mappát')?></a>
          </div>
          <div class="col-md-8 right">
            <button type="submit" class="btn btn-<?=($_GET['mode']=='create')?'primary':'success'?>" name="<?=($_GET['mode']=='create')?'addFolder':'saveFolder'?>"><?=($_GET['mode']=='create')?__('Mappa hozzádása').' <i class="fas fa-plus-circle"></i>':__('Változások mentése').' <i class="fas fa-save"></i>'?></button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; /* END: Group Creator */ ?>

<?php if (isset($_GET['mode']) && $_GET['mode'] == 'delete'): ?>
  <div class="wblock color-red">
    <div class="data-header">
      <div class="d-flex">
        <div class="col-md-8 title">
          <i class="ico <?=$ico?>"></i> <?=__('Dokumentum mappa törlése')?>
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
            <div class="col-md-12">
              <?=sprintf(__('Biztos benne, hogy törli a(z) <strong>%s</strong> dokumentum mappát? A művelet nem visszavonható!'), $this->folder['name'])?>
              <br><br>
              <strong><?=__('A törlés során a dokumentumok átkerülnek a Kategorizálatlan mappába!')?></strong>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 right">
              <button type="submit" class="btn btn-danger" name="deleteFolder"><?=__('Végleges törlés')?> <i class="fas fa-trash-alt"></i></button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endif; ?>
