<div style="float: right;">
	<a href="/emails" class="btn btn-default"><i class="fa fa-arrow-left"></i> vissza</a>
</div>
<h1><?=$this->mail['cim']?></h1>
<form method="post" action>
	<div class="row">
		<div class="col-sm-6">
			<div class="con">
				<h3>Tartalom</h3>
				<textarea class="editor" name="data[content]"><?=$this->mail['content']?></textarea>
				<br>
				<div class="right">
					<button class="btn btn-success" name="saveEmail">Változások mentése <i class="fa fa-save"></i></button>
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="con">
				<h3>Website paraméterek</h3>
				<? foreach( $this->settings as $key => $value ): ?>
				<div class="row np">
					<div class="col-sm-5">
						<strong>{<?=$key?>}</strong>
					</div>
					<div class="col-sm-7">
						<?=$value?>
					</div>
				</div>
				<? endforeach; ?>
			</div>
		</div>
	</div>

</form>
