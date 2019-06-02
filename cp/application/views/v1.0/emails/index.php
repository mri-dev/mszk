<?=$this->msg?>

<div class="wblock">
	<div class="data-header">
    <div class="d-flex align-items-center">
      <div class="col title">
        <i class="ico fas fa-users"></i> <?=__('Elérhető sablonok')?>
      </div>
    </div>
  </div>
	<div class="data-container">
		<table class="table termeklista table-bordered">
			<thead>
		    	<tr>
					<th width="200">Azonosító</th>
					<th>Elnevezés</th>
					<th><i class="fa fa-gear"></i></th>
		        </tr>
			</thead>
		    <tbody>
			<?
				foreach( $this->mails as $mail ):
			?>
		    	<tr>
			    	<td class="center"><?=$mail['elnevezes']?></td>
			    	<td><strong><?=$mail['cim']?></strong></td>
			    	<td align="center">
	            <a role="menuitem" tabindex="-1" href="/emails/edit/<?=$mail['elnevezes']?>" title="Szerkesztés"><i class="fas fa-pencil-alt"></i></a>
            </td>
		        </tr>
		    <? 	endforeach; ?>
		    </tbody>
		</table>
	</div>
</div>
