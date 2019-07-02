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
              <div class="folder<?=(!$this->folderinfo)?' opened':''?>">
                <a href="/dokumentumok"><i class="fas fa-folder"></i> <?=__('Összes mappa')?></a>
              </div>
              <?php foreach ((array)$this->folders as $folder): $opened = (($this->folderinfo && $this->folderinfo['hashkey'] == $folder['hashkey']) || $_GET['topfolder'] == $folder['hashkey']) ? true : false; ?>
              <div class="folder<?=($opened)?' opened':''?>">
                <a href="/dokumentumok/?folder=<?=$folder['hashkey']?>"><i class="far <?=($_GET['folder'] == $folder['hashkey'])?'fa-folder-open':'fa-folder'?>"></i> <?=$folder['name']?><? if($folder['filecnt'] != 0):?><span class="pull-right badge badge-primary"><?=$folder['filecnt']?></span><? endif; ?></a>
                <?php if ( $opened && $folder['child']): ?>
                <?php foreach ((array)$folder['child'] as $cfolder): $copened = ($this->folderinfo && $this->folderinfo['hashkey'] == $cfolder['hashkey']) ? true : false; ?>
                <div class="folder<?=($copened)?' opened':''?> sub">
                  <a href="/dokumentumok/?folder=<?=$cfolder['hashkey']?>&topfolder=<?=$folder['hashkey']?>"><i class="far <?=($_GET['folder'] == $cfolder['hashkey'])?'fa-folder-open':'fa-folder'?>"></i> <?=$cfolder['name']?><? if($cfolder['filecnt'] != 0):?><span class="pull-right badge badge-primary"><?=$cfolder['filecnt']?></span><? endif; ?></a>
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
                <span class="docsnum"><strong><?=(int)$this->docs['total_num']?> <?=__('fájl')?></strong> &nbsp;&nbsp;&nbsp; <strong><?=(int)$this->docs['pages']['current']?></strong> / <?=(int)$this->docs['pages']['max']?> <?=__('oldal')?> </span>
              </div>
              <div class="adder">
                <a href="/dokumentumok/hozzaad"><?=__('új dokumentum')?> <i class="fas fa-plus"></i></a>
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
                  <th width="150" class="center"><?=__('Időpont')?></th>
                  <th width="50" class="center"><i class="fas fa-cog"></i></th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($this->docs['data'])): ?>
                <tr>
                  <td colspan="10">
                    <div class="no-data">
                      <i class="fas fa-folder-minus"></i>
                      <?=__('A keresési feltételek alapján nincsenek megjeleníthető dokumentumok.')?>
                    </div>
                  </td>
                </tr>
                <?php else: ?>
                  <?php foreach ((array)$this->docs['data'] as $doc): ?>
                  <tr>
                    <td>
                      <div class="doctitle">
                        <a href="/doc/<?=$doc['hashkey']?>"><strong><?=$doc['name']?></strong></a>
                      </div>
                      <div class="sub-datas">
                        <?php if (!empty($doc['avaiable_to'])): ?>
                          <span><i class="fas fa-history"></i> <?=__('Elérhető')?>: <strong><?=date('Y/m/d', strtotime($doc['avaiable_to']))?>-<?=__('ig')?></strong></span>
                        <?php endif; ?>
                        <?php if (!empty($doc['expire_at'])): ?>
                          <span><i class="far fa-calendar"></i> <?=__('Lejárat')?>: <strong><?=date('Y/m/d', strtotime($doc['expire_at']))?></strong></span>
                        <?php endif; ?>
                        <?php if (!empty($doc['teljesites_at'])): ?>
                          <span><i class="fas fa-calendar-check"></i> <?=__('Teljesítés')?>: <strong><?=date('Y/m/d', strtotime($doc['teljesites_at']))?></strong></span>
                        <?php endif; ?>
                        <?php if (!empty($doc['ertek']) && $doc['ertek'] > 0): ?>
                          <span><i class="fas fa-file-invoice-dollar"></i> <?=__('Érték')?>: <strong><?=\Helper::cashFormat($doc['ertek'])?> <?=__('Ft + ÁFA')?></strong></span>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td class="center">
                      <a class="folder-link" href="/dokumentumok/<?=$doc['folders'][0]['folder_slug']?>"><?=$doc['folders'][0]['folder_name']?></a>
                    </td>
                    <td class="center user-author">
                      <strong><?=$doc['user_nev']?></strong> <?=($doc['is_me'])?'(<span class="isme">'.__('Én').'</span>)':''?>
                      <?php if ($doc['user_company'] != ''): ?>
                      <div class="company">
                        <?=$doc['user_company']?>
                      </div>
                      <?php endif; ?>
                    </td>
                    <td class="center">
                      <?=$doc['created_at']?>
                    </td>
                    <td class="center actions">
                      <?php if ($doc['user_id'] == $this->_USERDATA['data']['ID'] || $this->is_admin_logged ): ?>
                      <a href="/dokumentumok/szerkeszt/<?=$doc['hashkey']?>" title="<?=__('Szerkesztés')?>"><i class="fas fa-pencil-alt"></i></a>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>

              </tbody>
            </table>
          </div>
          <?php echo $this->navigator; ?>
        </div>
      </div>
    </div>
  </div>
</div>
