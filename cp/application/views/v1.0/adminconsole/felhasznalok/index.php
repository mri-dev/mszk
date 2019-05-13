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
            <th><?=__('Név')?></th>
            <th><?=__('E-mail')?></th>
            <th><?=__('Fiók csoport')?></th>
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
            <td><?php echo $item['nev']; ?></td>
            <td><?php echo $item['email']; ?></td>
            <td><?php echo $item['total_data']['data']['user_group_name']; ?></td>
            <td class="center"><?php echo $item['utoljara_belepett']; ?></td>
            <td class="center"><?php echo $item['regisztralt']; ?></td>
            <td width="50" class="center"><?=($item['aktivalva'] != '')?'<i class="fas fa-check"></i>':'<i class="fas fa-times"></i>'?></td>
            <td width="50" class="center"><?=($item['engedelyezve'] == 1)?'<i class="fas fa-check"></i>':'<i class="fas fa-times"></i>'?></td>
            <td class="center actions">
              <a href="/adminconsole/felhasznalok/edit/<?php echo $item['ID']; ?>"><i class="fas fa-pencil-alt"></i></a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php echo $this->navigator; ?>
