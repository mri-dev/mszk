<div class="filter-block">
  <form class="" action="" method="post">
  <fieldset>
    <legend><i class="fas fa-filter"></i> <?=__('Szűrőfeltételek')?></legend>
    <div class="wrapper">
      <div class="row align-items-end">
        <div class="col-md-2">
          <label for="filter_nev"><?=__('Felhasználó neve')?></label>
          <input id="filter_nev" type="text" class="form-control form-control-sm<?=(isset($_COOKIE['filter_nev']))?' is-valid':''?>" name="nev" value="<?=$_COOKIE['filter_nev']?>">
        </div>
        <div class="col-md-2">
          <label for="filter_email"><?=__('E-mail cím')?></label>
          <input id="filter_email" type="text" class="form-control form-control-sm<?=(isset($_COOKIE['filter_email']))?' is-valid':''?>" name="email" value="<?=$_COOKIE['filter_email']?>">
        </div>
        <div class="col-md-2">
          <label for="filter_company_name"><?=__('Cég neve')?></label>
          <input id="filter_company_name" type="text" class="form-control form-control-sm<?=(isset($_COOKIE['filter_company_name']))?' is-valid':''?>" name="company_name" value="<?=$_COOKIE['filter_company_name']?>">
        </div>
        <div class="col-md-2">
          <label for="filter_company_adoszam"><?=__('Cég adószáma')?></label>
          <input id="filter_company_adoszam" type="text" class="form-control form-control-sm<?=(isset($_COOKIE['filter_company_adoszam']))?' is-valid':''?>" name="company_adoszam" value="<?=$_COOKIE['filter_company_adoszam']?>">
        </div>
        <div class="col-md-2">
          <label for="filter_user_group"><?=__('Fiók csoport')?></label>
          <select name="user_group" class="form-control form-control-sm<?=(isset($_COOKIE['filter_user_group']))?' is-valid':''?>" id="filter_user_group">
              <option value="" selected="selected">-- Mind --</option>
              <option value="" disabled="disabled"></option>
              <? foreach( $this->user_groupes as $key => $value ): ?>
                  <option value="<?=$key?>" <?=($key==$_COOKIE['filter_user_group'])?'selected="selected"':''?>><?=$value?></option>
              <? endforeach; ?>
          </select>
        </div>
        <div class="col-md-2 right">
          <button type="submit" class="btn btn-sm btn-primary" name="filterList"><?=__('Szűrés')?></button>
          <?php if (isset($_COOKIE['filtered'])): ?>
            <button type="submit" class="btn btn-sm btn-default" name="clearfilters" value="1" title="<?=__('Szűrőfeltételek eltávolítása')?>"><i class="far fa-times-circle"></i></button>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </fieldset>
  </form>
</div>
<div class="wblock">
  <div class="data-header">
    <div class="d-flex align-items-center">
      <div class="col title">
        <i class="ico fas fa-users"></i> <?=__('Felhasználók listája')?>
      </div>
      <div class="col-md-5 right">
        <div class="d-flex align-items-center">
          <div class="col">
            <div class="list-info">
              <div class="total-result">
                <?=$this->users['info']['total_num']?> <?=__('db')?> <?=__('felhasználó')?>
              </div>
              <div class="pager">
                <strong><?=$this->users['info']['pages']['current']?>. <?=__('oldal')?></strong> / <?=$this->users['info']['pages']['max']?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="data-container">
    <div class="table-responsive">
      <table class="table table-striped" id="users">
        <thead>
          <tr>
            <th class="<?=(isset($_COOKIE['filter_nev']) || isset($_COOKIE['filter_email']))?'filtered':''?>"><?=__('Név / E-mail')?></th>
            <th class="<?=(isset($_COOKIE['filter_company_adoszam']) || isset($_COOKIE['filter_company_name']))?'filtered':''?>"><?=__('Cég / adószám')?></th>
            <th class="<?=(isset($_COOKIE['filter_user_group']))?'filtered':''?>"><?=__('Fiók csoport')?></th>
            <th width="150" class="center"><?=__('Utoljára belépett')?></th>
            <th width="150" class="center"><?=__('Regisztrált')?></th>
            <th width="50" class="center"><?=__('Aktiválva')?></th>
            <th width="50" class="center"><?=__('Engedélyezve')?></th>
            <th width="70" class="center"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach( $this->users['data'] as $item ): ?>
          <tr>
            <td class="<?=(isset($_COOKIE['filter_nev']) || isset($_COOKIE['filter_email']))?'filtered':''?>">
              <strong><?php echo $item['nev']; ?></strong>
              <div class="">
                <span class="email"><?php echo $item['email']; ?></span>
              </div>
            </td>
            <td class="<?=(isset($_COOKIE['filter_company_adoszam']) || isset($_COOKIE['filter_company_name']))?'filtered':''?>">
              <strong><?php echo $item['total_data']['data']['company_name']; ?></strong>
              <div class="">
                <span class="adoszam"><?php echo $item['total_data']['data']['company_adoszam']; ?></span>
              </div>
            </td>
            <td class="<?=(isset($_COOKIE['filter_user_group']))?'filtered':''?>"><?php echo $item['total_data']['data']['user_group_name']; ?></td>
            <td class="center"><?php echo $item['utoljara_belepett']; ?></td>
            <td class="center"><?php echo $item['regisztralt']; ?></td>
            <td width="50" class="center"><?=($item['aktivalva'] != '')?'<i class="fas fa-check"></i>':'<i class="fas fa-times"></i>'?></td>
            <td width="50" class="center"><?=($item['engedelyezve'] == 1)?'<i class="fas fa-check"></i>':'<i class="fas fa-times"></i>'?></td>
            <td class="center actions">
              <a href="/account/?t=edit&ID=<?php echo $item['ID']; ?>&ret=/adminconsole/felhasznalok/"><i class="fas fa-pencil-alt"></i></a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php echo $this->navigator; ?>
