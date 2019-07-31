<script type="text/javascript">
	$(function(){
		$('.settings-change input[type=checkbox]').click( function(){

            var by  = $(this).attr('key');
            var v   = $(this).is(':checked');
            v = (v) ? 1 : 0;

            $('#'+by+'_response').stop().html('<strong style="color:red;">folyamatban...</strong>');

            $.post('<?=AJAX_POST?>',{
                type: 'changeSettings',
                key : by,
                val : v
            }, function(d){
                $('#'+by+'_response').stop().html('<strong style="color:green;">Mentve!</strong>');
                setTimeout(function(){
                   $('#'+by+'_response').stop().html('');
                }, 4500);
            }, "html");
        });

	})
     $(function(){

    })

    function responsive_filemanager_callback(field_id){
        var imgurl = $('#'+field_id).val();
        $('#logo_preview').attr('src',imgurl);
    }
</script>


<div class="settings-change">
		<? if( $this->err && $this->bmsg['admin'] ): ?>
				<?=$this->bmsg['admin']?>
		<? endif; ?>

		<a name="basics"></a>
		<div class="row">
			<div class="col-md-12">
				<? if( $this->err && $this->bmsg['basics'] ): ?>
		        <?=$this->bmsg['basics']?>
		    <? endif; ?>
			</div>
		</div>

		<form action="#basics" method="post">
			<div class="row mb-3">
				<div class="col-md-12">
					<div class="card">
						<div class="card-header">
							Változók
						</div>
						<div class="card-body">
							<div class="row np">
									<div class="col-md-12">
											<label for="basics_page_title">Weboldal főcíme</label>
											<input type="text" id="basics_page_title" name="page_title" class="form-control" value="<?=$this->settings['page_title']?>">
									</div>
							</div>
							<br>
							<div class="row np">
									<div class="col-md-12">
											<label for="basics_page_description">Weboldal alcíme</label>
											<input type="text" id="basics_page_description" name="page_description" class="form-control" value="<?=$this->settings['page_description']?>">
									</div>
							</div>
							<br>
							<div class="row np">
									<div class="col-md-12">
											<label for="basics_about_us">Weboldal bemutatkozás szövege</label>
											<textarea name="about_us" id="basics_about_us" class="form-control no-editor" style="max-width: 100%;"><?=$this->settings['about_us']?></textarea>
									</div>
							</div>
							<br>
							<div class="row np">
									<div class="col-md-5">
											<label for="basics_logo">Alapértelmezz logó</label>
											<div class="input-group">
													<input type="text" id="basics_logo" name="logo" class="form-control" value="<?=$this->settings['logo']?>">
													<div class="input-group-addon"><a title="Kép kiválasztása a galériából" href="<?=FILE_BROWSER_IMAGE?>&field_id=basics_logo" data-fancybox-type="iframe" class="iframe-btn" ><i class="fa fa-link"></i></a></div>
											</div>
											<div style="margin-top: 5px;
	background: #c5171e;
	padding: 10px;
	float: left;">
													<img src="<?=$this->settings['logo']?>" id="logo_preview" alt="" style="max-width:180px;">
											</div>
									</div>
							</div>
							<br>
							<div class="row np">
									<div class="col-md-12">
											<label for="basics_date_format">Dátum formátum</label>
											<input type="text" id="basics_date_format" name="date_format" class="form-control" value="<?=$this->settings['date_format']?>">
											<div class="info"><em><a href="http://php.net/manual/en/function.date.php" target="_blank">Dátum formátum struktúra (php.net)</a></em></div>
									</div>
							</div>
							<br>
							<div class="row np">
									<div class="col-md-12">
											<label for="basics_google_analitics"><i class="fa fa-pie-chart"></i> Google Analitics követőkód</label>
											<textarea type="text" id="basics_google_analitics" name="google_analitics" class="form-control no-editor"><?=$this->settings['google_analitics']?></textarea>
									</div>
							</div>
							<br>
							<div class="row np">
									<div class="col-md-12">
											<label for="basics_recaptcha_private_key"><a target="_blank" href="https://www.google.com/recaptcha/intro/index.html">reCaptcha</a> PRIVATE kulcs</label>
											<input type="text" id="basics_recaptcha_private_key" name="recaptcha_private_key" class="form-control" value="<?=$this->settings['recaptcha_private_key']?>">
									</div>
							</div>
							<br>
							<div class="row np">
									<div class="col-md-12">
											<label for="basics_recaptcha_public_key"><a target="_blank" href="https://www.google.com/recaptcha/intro/index.html">reCaptcha</a> PUBLIC kulcs</label>
											<input type="text" id="basics_recaptcha_public_key" name="recaptcha_public_key" class="form-control" value="<?=$this->settings['recaptcha_public_key']?>">
									</div>
							</div>
							<div class="divider"></div>
							<br>
							<h3>Tulajdon adatok</h3>
							<div class="row np">
									<div class="col-md-12">
											<label for="basics_page_author">Weboldal tulajdonos</label>
											<input type="text" id="basics_page_author" name="page_author" class="form-control" value="<?=$this->settings['page_author']?>">
									</div>
							</div>
							<br>
							<div class="row np">
									<div class="col-md-12">
											<label for="basics_page_url">Weboldal elérhetősége</label>
											<input type="text" id="basics_page_url" name="page_url" class="form-control" value="<?=$this->settings['page_url']?>">
									</div>
							</div>
							<br>
							<div class="row np">
									<div class="col-md-12">
											<label for="basics_page_author_address">Telephely</label>
											<input type="text" id="basics_page_author_address" name="page_author_address" class="form-control" value="<?=$this->settings['page_author_address']?>">
									</div>
							</div>
							<br>
							<div class="row np">
									<div class="col-md-12">
											<label for="basics_primary_email">Elsődleges e-mail cím</label>
											<input type="text" id="basics_primary_email" name="primary_email" class="form-control" value="<?=$this->settings['primary_email']?>">
									</div>
							</div>
							<br>
							<div class="row np">
									<div class="col-md-12">
											<label for="basics_alert_email">Adminisztratív e-mail cím, értesítő leveleknek <?=\PortalManager\Formater::tooltip('A rendszer erre az e-mail címre fogja kiküldeni a fontosabb értesítő e-mail üzeneteket. Pl.: új megrendelés, új üzenet, stb...')?></label>
											<input type="text" id="basics_alert_email" name="alert_email" class="form-control" value="<?=(is_array($this->settings['alert_email'])) ? implode($this->settings['alert_email'],",") : $this->settings['alert_email']?>">
									</div>
							</div>
							<br>
							<div class="row np">
									<div class="col-md-12">
											<label for="basics_reply_email">Válasz e-mail cím</label>
											<input type="text" id="basics_reply_email" name="reply_email" class="form-control" value="<?=$this->settings['reply_email']?>">
									</div>
							</div>
							<br>
							<div class="row np">
									<div class="col-md-12">
											<label for="basics_email_noreply_address">Inaktív (no-reply) e-mail cím, értesítő levelek válaszcímének</label>
											<input type="text" id="basics_email_noreply_address" name="email_noreply_address" class="form-control" value="<?=$this->settings['email_noreply_address']?>">
									</div>
							</div>
							<br>
							<h3>Linkek</h3>
							<div class="row np">
									<div class="col-md-12">
											<label for="basics_ASZF_URL">ÁSZF elérhetősége</label>
											<input type="text" id="basics_ASZF_URL" name="ASZF_URL" class="form-control" value="<?=$this->settings['ASZF_URL']?>">
									</div>
							</div>
							<br>
							<h3>Banki adatok</h3>

							<div class="row np">
									<div class="col-md-12">
											<label for="basics_banktransfer_author">Banki adat - Tulajdonos</label>
											<input type="text" id="basics_banktransfer_author" name="banktransfer_author" class="form-control" value="<?=$this->settings['banktransfer_author']?>">
									</div>
							</div>
							<br>
							<div class="row np">
									<div class="col-md-12">
											<label for="basics_banktransfer_number">Banki adat - Számlaszám</label>
											<input type="text" id="basics_banktransfer_number" name="banktransfer_number" class="form-control" value="<?=$this->settings['banktransfer_number']?>">
									</div>
							</div>
							<br>
							<div class="row np">
									<div class="col-md-12">
											<label for="basics_banktransfer_bank">Banki adat - Bank neve</label>
											<input type="text" id="basics_banktransfer_bank" name="banktransfer_bank" class="form-control" value="<?=$this->settings['banktransfer_bank']?>">
									</div>
							</div>
							<br>
							<div class="row np">
									<div class="col-md-12 right">
											<button name="saveBasics" value="1" class="btn btn-success">Változások mentése <i class="fa fa-save"></i></button>
									</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
</div>
