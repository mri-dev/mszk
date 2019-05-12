<?php echo $this->bmsg; ?>
<div class="row">
  <div class="col-md-3">
    <div class="wblock">
      <div class="data-header">
        <div class="d-flex">
          <div class="col title">
            <i class="ico fas fa-ellipsis-v"></i> <?=__('Csoportok')?>
          </div>
        </div>
      </div>
      <div class="data-container">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th><?=__('Elnevezés')?></th>
                <th width="70" class="center"></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach( $this->groups as $item ): ?>
              <tr>
                <td><?php echo $item['neve']; ?></td>
                <td class="center actions">
                  <a href="#"><i class="fas fa-pencil-alt"></i></a>&nbsp;
                  <a href="#"><i class="fas fa-trash-alt"></i></a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col">
    <?php if (isset($_GET['creator']) && $_GET['creator'] == 'delete'): ?>
    <a name="creator"></a>
    <div class="wblock color-red">
      <div class="data-header">
        <div class="d-flex">
          <div class="col title">
            <i class="ico fas fa-trash-alt"></i> <?=__('elem törlése')?>
          </div>
          <div class="col right closer">
            <a href="/adminconsole/lists/"><i class="fas fa-times"></i></a>
          </div>
        </div>
      </div>
      <div class="data-container">
        <div class="dc-padding">
          <form class="" action="" method="post">
            <div class="row">
              <div class="col-md-12">
                <?=sprintf(__('Biztos benne, hogy a(z) <strong>%s</strong> (itt: <strong>%s</strong>) elemet véglegesen törli?'), $this->list_d->getName(), $this->list_d->getGroupName())?>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12 right">
                <button type="submit" class="btn btn-danger" name="deleteCategory"><?=__('Elem végleges törlése')?> <i class="fas fa-trash-alt"></i></button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <?php if (isset($_GET['creator']) && $_GET['creator'] != 'delete'): ?>
    <?php
      switch ($_GET['creator']) {
        case 'add':
          $mode = __('hozzáadás');
          $ico = 'fas fa-plus';
          $color = 'blue';
        break;
        case 'edit':
          $mode = __('szerkesztés');
          $ico = 'fas fa-edit';
          $color = 'green';
        break;
      }

    ?>
    <a name="creator"></a>
    <div class="wblock color-<?=$color?>">
      <div class="data-header">
        <div class="d-flex">
          <div class="col title">
            <i class="ico <?=$ico?>"></i> <?=__('Elem').' '.$mode?>
          </div>
          <div class="col right closer">
            <a href="/adminconsole/lists/"><i class="fas fa-times"></i></a>
          </div>
        </div>
      </div>
      <div class="data-container">
        <div class="dc-padding">
          <form class="" action="" method="post">
            <div class="row">
              <div class="col-md-3">
                <label for="creator_group_id"><?=__('Csoport')?> *</label>
                <select id="creator_group_id" class="form-control" name="group_id">
                  <option value="" selected="selected">&mdash; <?=__('válasszon')?> &mdash;</option>
                  <?php foreach ( (array)$this->groups as $g ): ?>
                  <option value="<?=$g['ID']?>" <?=($this->err && $_POST['group_id'] == $g['ID'] ? 'selected="selected"' : ($this->list && $this->list->getGroupID() == $g['ID'] ? 'selected="selected"' : '' ))?>><?=$g['neve']?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-6">
                <label for="creator_neve"><?=__('Elem elnevezése')?> *</label>
                <input id="creator_neve" type="text" class="form-control" name="neve" value="<?=(isset($_POST['neve']))?$_POST['neve']:(($this->list)?$this->list->getName():'')?>">
              </div>
              <div class="col-md-3">
                <label for="creator_szulo_id"><?=__('Szülő elem')?></label>
                <select id="creator_szulo_id" class="form-control" name="szulo_id">
                  <option value="" selected="selected">&mdash; ne legyen &mdash;</option>
  								<option value="" disabled="disabled"></option>
  								<?
  									while( $this->lists->walk() ):
  									$cat = $this->lists->the_cat();
  								?>
  								<option value="<?=$cat['ID']?>_<?=$cat['deep']?>" <?=($this->err && $_POST['szulo_id'] == $cat['ID'].'_'.$cat['deep'] ? 'selected="selected"' : ($this->list && $this->list->getParentKey() == $cat['ID'].'_'.$cat['deep'] ? 'selected="selected"' : '' ))?>><? for($s=$cat['deep']; $s>0; $s--){echo '&mdash;';}?><?=$cat['neve']?></option>
  								<? endwhile; ?>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <label for="creator_leiras"><?=__('Elem leírása')?> </label>
                <textarea id="creator_leiras" class="form-control" name="leiras"><?=(isset($_POST['leiras']))?$_POST['leiras']:(($this->list)?$this->list->getDesc():'')?></textarea>
              </div>
            </div>
            <div class="row">
              <div class="col-md-10">
                <label for="creator_kep"><?=__('Elem képe')?></label>
                <div class="input-group">
                  <input id="creator_kep" type="text" class="form-control" name="kep" value="<?=(isset($_POST['kep']))?$_POST['kep']:(($this->list)?$this->list->getImage():'')?>">
                  <div class="input-group-append">
                    <span class="input-group-text"><a title="Kép kiválasztása galériából" href="/src/js/tinymce/plugins/filemanager/dialog.php?type=1&amp;lang=hu_HU&amp;field_id=img" data-fancybox-type="iframe" class="iframe-btn"><i class="fas fa-images"></i></a></span>
                  </div>
                </div>
              </div>
              <div class="col-md-2">
                <label for="creator_sorrend"><?=__('Sorrend')?></label>
                <input id="creator_sorrend" type="number" class="form-control" name="sorrend" value="<?=(isset($_POST['sorrend']))?$_POST['sorrend']:(($this->list)?$this->list->getSortNumber():'')?>">
              </div>
            </div>
            <div class="row">
              <div class="col-md-12 right">
                <button type="submit" class="btn btn-<?=($_GET['creator']=='add')?'primary':'success'?>" name="<?=($_GET['creator']=='add')?'addCategory':'saveCategory'?>"><?=($_GET['creator']=='add')?__('Elem hozzádása').' <i class="fas fa-plus-circle"></i>':__('Változások mentése').' <i class="fas fa-save"></i>'?></button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <div class="wblock">
      <div class="data-header">
        <div class="d-flex align-items-center">
          <div class="col title">
            <i class="ico fas fa-ellipsis-h"></i> <?=__('Lista elemek')?>
          </div>
          <div class="col-md-5 right">
            <div class="d-flex align-items-center">
              <div class="col-md-8">
                <input type="text" class="form-control" data-list-searcher="list-items" value="<?=$_GET['src']?>" placeholder="<?=__('Gyorskeresés...')?>">
              </div>
              <div class="col-md-4">
                <a href="/adminconsole/lists/?creator=add#creator" class="btn btn-block btn-sm btn-primary"><?=__('Új elem')?> <i class="fas fa-plus"></i></a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="data-container">
        <div class="table-responsive">
          <table class="table" id="list-items">
            <thead>
              <tr>
                <th><?=__('Elem elnevezése')?></th>
                <th><?=__('Csoport')?></th>
                <th width="80" class="center"><?=__('Sorrend')?></th>
                <th width="70" class="center"></th>
              </tr>
            </thead>
            <tbody>
              <?php while( $this->lists->walk() ): $item = $this->lists->the_cat(); ?>
              <tr data-itemsrc="<?php echo $item['neve']; ?>">
                <td><?php echo str_repeat('&mdash;', $item['deep']); ?> <?php echo $item['neve']; ?></td>
                <td><?php echo $item['group_neve']; ?> <?=($item['group_desc'] != '')?'<i class="fas info fa-info-circle" title="'.$item['group_desc'].'"></i>':''?></td>
                <td class="center"><?php echo $item['sorrend']; ?></td>
                <td class="center actions">
                  <a href="/adminconsole/lists/?creator=edit&id=<?php echo $item['ID']; ?>#creator"><i class="fas fa-pencil-alt"></i></a>&nbsp;
                  <a href="/adminconsole/lists/?creator=delete&id=<?php echo $item['ID']; ?>#creator"><i class="fas fa-trash-alt"></i></a>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
