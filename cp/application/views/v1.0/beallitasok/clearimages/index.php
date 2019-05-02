<h1>Beállítások / Képek eltávolítása</h1>
<em>Felesleges, használaton kívüli termékképek eltávolítása a szerverről.</em>
<br><br>
<div class="con">
	<form action="" method="post">
	<div style="float:right;">
		<button class="btn btn-danger">Képek törlése <i class="fa fa-trash"></i></button>
	</div>
	<h3>Használaton kívüli termékképek listája (<?=count($this->unused_images)?>)</h3>
	<div style="color:red;">A listában szereplő képek nem csatlakoznak termékekhez! A "Képek törlése" gombra nyomással az összes nem használt képet törölheti a tárhelyről, ezzel helyet spórolva.</div>
	<br>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Kép</th>
					<th>Mappa</th>
					<th>Direkt URL</th>
				</tr>
			</thead>
			<tbody>
				<? foreach( $this->unused_images as $img ): ?>
				<tr>
					<td width="100"><a title="Nagyítás" href="/<?=$img?>" class="zoom"><img src="<?=HOMEDOMAIN?>render/thumbnail/?i=admin/<?=$img?>&w=100" style="max-width:100px;" alt=""></a></td>
					<td style="vertical-align:middle;">
						<input type="hidden" name="del_img[]" value="<?=$img?>">
						<?=$img?>
					</td>
					<td style="vertical-align:middle;">
						<a href="<?=\PortalManager\Formater::productImage($img)?>" target="_blank"><?=\PortalManager\Formater::productImage($img)?></a>
					</td>
				</tr>
				<? endforeach; ?>
			</tbody>
		</table>
	</form>
</div>