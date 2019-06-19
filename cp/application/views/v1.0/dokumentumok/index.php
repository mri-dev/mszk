<div class="documents-controll">
  <div class="wblock">
    <div class="data-container">
      <div class="d-flex">
        <div class="sidebar">
          <div class="head">
            <i class="fas fa-folder"></i> <?=__('Dokumentum mappák')?>
          </div>
          <div class="folders">
            <?php if (empty($this->folders)): ?>
            <div class="no-data">
              <?=__('Nincsenek mappák létrehozva.')?>
            </div>
            <?php else: ?>
              <?php foreach ((array)$this->folders as $folder): $opened = ($_GET['folder'] == $folder['hashkey'] || $_GET['topfolder'] == $folder['hashkey']) ? true : false; ?>
              <div class="folder<?=($opened)?' opened':''?>">
                <a href="/dokumentumok/?folder=<?=$folder['hashkey']?>"><i class="far <?=($_GET['folder'] == $folder['hashkey'])?'fa-folder-open':'fa-folder'?>"></i> <?=$folder['name']?> <span class="pull-right badge badge-primary">0</span></a>
                <?php if ( $opened && $folder['child']): ?>
                <?php foreach ((array)$folder['child'] as $cfolder): $copened = ($_GET['folder'] == $cfolder['hashkey']) ? true : false; ?>
                <div class="folder<?=($copened)?' opened':''?> sub">
                  <a href="/dokumentumok/?folder=<?=$cfolder['hashkey']?>&topfolder=<?=$folder['hashkey']?>"><i class="far <?=($_GET['folder'] == $cfolder['hashkey'])?'fa-folder-open':'fa-folder'?>"></i> <?=$cfolder['name']?> <span class="pull-right badge badge-primary">0</span></a>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
              </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
        <div class="docs">
          <div class="head">
            <div class="d-flex justify-content-between">
              <div class="title">
                <?=__('Összes dokumentum')?>
              </div>
              <div class="info">
                0 <?=__('db')?>
              </div>
            </div>
          </div>
          <div class="filters">
            <form class="" action="" method="get">
              <div class="d-flex">
                <div class="ftext"><div class="col-form-label-sm"><i class="fas fa-filter"></i> <?=__('Szűrés')?></div> </div>
                <div class="name">
                  <input type="text" class="form-control form-control-sm<?=(!empty($_GET['name'])?' is-valid':'')?>" name="name" value="<?=$_GET['name']?>" placeholder="<?=__('Keresés: címben...')?>">
                </div>
                <div class="folder">
                  <select class="form-control form-control-sm<?=(!empty($_GET['folder'])?' is-valid':'')?>" name="folder">
                    <option value="" selected="selected"><?=__('Mappa: összes')?></option>
                    <option value="" disabled="disabled"></option>
                    <?php foreach ((array)$this->folders as $folder): ?>
                    <option value="<?=$folder['hashkey']?>" <?=($folder['hashkey'] == $_GET['folder']||$folder['hashkey'] == $_GET['topfolder'])?'selected="selected"':''?>><?=$folder['name']?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="button">
                  <button type="submit" class="btn btn-default btn-sm"><i class="fas fa-search"></i></button>
                </div>
              </div>
            </form>
          </div>
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th><?=__('Cím')?></th>
                  <th width="220"class="center"><?=__('Mappa')?></th>
                  <th width="200" class="center"><?=__('Létrehozta')?></th>
                  <th width="120" class="center"><?=__('Időpont')?></th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
