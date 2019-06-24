<div class="documents-controll">
  <div class="wblock">
    <div class="data-container">
      <div class="d-flex">
        <div class="sidebar">
          <div class="head">
            <div class="d-flex justify-content-between">
              <div class="">
                <i class="fas fa-folder"></i> <?=__('Mappák')?>
              </div>
              <div class="adderbtn">
                <a href="/dokumentumok/folders?mode=create"><?=__('Új mappa')?> <i class="fas fa-folder-plus"></i></a>
              </div>
            </div>
          </div>
          <div class="folders">
            <?php if (empty($this->folders)): ?>
            <div class="no-data">
              <?=__('Nincsenek mappák létrehozva.')?>
            </div>
            <?php else: ?>
              <?php foreach ((array)$this->folders as $folder): $opened = (($this->folderinfo && $this->folderinfo['hashkey'] == $folder['hashkey']) || $_GET['topfolder'] == $folder['hashkey']) ? true : false; ?>
              <div class="folder<?=($opened)?' opened':''?>">
                <a href="/dokumentumok/?folder=<?=$folder['hashkey']?>"><i class="far <?=($_GET['folder'] == $folder['hashkey'])?'fa-folder-open':'fa-folder'?>"></i> <?=$folder['name']?><span class="pull-right badge badge-primary">0</span></a>
                <?php if ( $opened && $folder['child']): ?>
                <?php foreach ((array)$folder['child'] as $cfolder): $copened = ($this->folderinfo && $this->folderinfo['hashkey'] == $cfolder['hashkey']) ? true : false; ?>
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
                <?php if ($this->folderinfo): ?>
                  <?=$this->folderinfo['name']?> &mdash; <?=__('Dokumentumok')?>
                <?php else: ?>
                  <?=__('Összes dokumentum')?>
                <?php endif; ?>
              </div>
              <div class="info">
                <?php if ($this->folderinfo && $this->folderinfo['isdefault'] == 0): ?>
                <a href="/dokumentumok/folders?mode=edit&folder=<?=$this->folderinfo['hashkey']?>" class="btn btn-default btn-sm edit-folder"><?=__('mappa szerkesztése')?></a>
                <?php endif; ?>
                <span class="docsnum">0 <?=__('db')?></span>
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
