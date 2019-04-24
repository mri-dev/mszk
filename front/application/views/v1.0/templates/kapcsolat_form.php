<form action="" method="post">
	<div class="contact-form">	
		<div class="row np">
			<div class="col-md-3 form-text-md">
				<strong>Név</strong> (kötelező)
			</div>
			<div class="col-md-9">
				<input type="text" class="form-control" name="contact_name">
			</div>
		</div>
		<div class="row np">
			<div class="col-md-3 form-text-md">
				<strong>E-mail cím</strong> (kötelező)
			</div>
			<div class="col-md-9">
				<input type="text" class="form-control" name="contact_email">
			</div>
		</div>
		<div class="row" style="margin: 0 -15px;">
			<div class="col-sm-5">
				<div>
					<strong>Tárgy</strong>
				</div>
				<textarea name="contact_subject" class="form-control"></textarea>
			</div>
			<div class="col-sm-7">
				<div>
					<strong>Üzenet</strong>
				</div>
				<textarea name="contact_msg" class="form-control msg"></textarea>
			</div>
		</div>
		<div class="row np">
			<div class="col-md-12 left">
				<div class="divider"></div>
				<? \Applications\Captcha::show(); ?>
				<div style="float:right; text-align:right;">
					<input type="checkbox" checked="checked" name="contact_subscribe" id="contact_subscribe"><label for="contact_subscribe"><strong>Feliratkozás hírlevélre</strong></label>
					<br>
					<button class="btn btn-info" name="contact_form" value="1">KÜLDÉS</button>
				</div>	
			</div>
		</div>
	</div>	
</form>