<form method="post" action>
	<div class="row">
		<div class="col-md-6">
			<div class="wblock color-green">
				<div class="data-header">
			    <div class="d-flex align-items-center">
			      <div class="col title">
			        <i class="ico fas fa-pencil-alt"></i> <?=__('Sablon szerkesztése')?>
			      </div>
			    </div>
			  </div>
				<div class="data-container">
					<textarea class="editor" name="data[content]"><?=$this->mail['content']?></textarea>
				</div>
				<div class="data-footer">
					<div class="text-right">
						<button class="btn btn-success" name="saveEmail"><?=__('Változások mentése')?> <i class="fa fa-save"></i></button>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="wblock">
				<div class="data-header">
			    <div class="d-flex align-items-center">
			      <div class="col title">
			       <?=__('Globális paraméterek')?>
			      </div>
			    </div>
			  </div>
				<div class="data-container">
					<div class="dc-padding">
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
		</div>
	</div>
</form>
