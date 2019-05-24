<h1>Új fiók létrehozása</h1>
<?=$this->msg?>
<form action="" method="post" enctype="multipart/form-data">
	<div style="margin: 0 -10px;">
		<div class="row">
			<div class="col-sm-6">
				<div class="con">
					<h3 style="margin: 0 0 5px 0;">Fiók alapadatok</h3>
					<div class="divider" style="margin-bottom: 10px;"></div>
					<div class="row">
						<div class="col-sm-6">
							<label for="data_felhasznalok_nev">Név*</label>
							<input type="text" id="data_felhasznalok_nev" class="form-control" name="data[felhasznalok][nev]" value="<?=(isset($_POST[data])) ? $_POST[data][felhasznalok][nev] : ''?>" required>
						</div>
						<div class="col-sm-6">
							<label for="data_felhasznalok_email">E-mail cím*</label>
							<input type="text" id="data_felhasznalok_email" class="form-control" name="data[felhasznalok][email]" value="<?=(isset($_POST[data])) ? $_POST[data][felhasznalok][email] : ''?>" required>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-6">
							<label for="data_felhasznalok_jelszo">Jelszó*</label>
							<input type="text" id="data_felhasznalok_jelszo" class="form-control" name="data[felhasznalok][jelszo]" value="<?=strrev(uniqid())?>" required>
						</div>
						<div class="col-sm-6">
							<label for="data_felhasznalok_cash">Virtuális egyenleg</label>
							<input type="text" id="data_felhasznalok_cash" class="form-control" name="data[felhasznalok][cash]" value="<?=(isset($_POST[data])) ? $_POST[data][felhasznalo_adatok][szamlazas_nev] : 0?>" min="0">
						</div>
					</div>

					<br>
					<h3 style="margin: 0 0 5px 0;">Számlázási adatok</h3>
					<div class="divider" style="margin-bottom: 10px;"></div>
					<div class="row">
						<div class="col-sm-6">
							<label for="data_felhasznalo_adatok_szamlazas_nev">Számlázási név*</label>
							<input type="text" id="data_felhasznalok_nev" class="form-control" name="data[felhasznalo_adatok][szamlazas_nev]" value="<?=$_POST[data][felhasznalo_adatok][szamlazas_nev]?>" required>
						</div>
						<div class="col-sm-6">
							<label for="data_felhasznalo_adatok_szamlazas_state">Megye*</label>
							<select name="data[felhasznalo_adatok][szamlazas_state]" class="form-control" id="data_felhasznalo_adatok_szamlazas_state" required>
	                            <option value="" selected="selected">-- válasszon --</option>
	                            <option value="" disabled="disabled"></option>
	                            <? foreach( $this->states as $s ): ?>
	                                <option value="<?=$s?>" <?=($s==$_POST[data][felhasznalo_adatok][szamlazas_state])?'selected="selected"':''?>><?=$s?></option>
	                            <? endforeach; ?>
	                        </select>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-2">
							<label for="data_felhasznalo_adatok_szamlazas_irsz">Irányítószám*</label>
							<input type="text" id="data_felhasznalok_irsz" class="form-control" name="data[felhasznalo_adatok][szamlazas_irsz]" value="<?=$_POST[data][felhasznalo_adatok][szamlazas_irsz]?>" required>
						</div>
						<div class="col-sm-3">
							<label for="data_felhasznalo_adatok_szamlazas_city">Város*</label>
							<input type="text" id="data_felhasznalok_city" class="form-control" name="data[felhasznalo_adatok][szamlazas_city]" value="<?=$_POST[data][felhasznalo_adatok][szamlazas_city]?>" required>
						</div>
						<div class="col-sm-7">
							<label for="data_felhasznalo_adatok_szamlazas_uhsz">Utca, házszám, emelet, ajtó*</label>
							<input type="text" id="data_felhasznalok_uhsz" class="form-control" name="data[felhasznalo_adatok][szamlazas_uhsz]" value="<?=$_POST[data][felhasznalo_adatok][szamlazas_uhsz]?>" required>
						</div>
					</div>

					<br>
					<h3 style="margin: 0 0 5px 0;">Szállítási adatok</h3>
					<div class="divider" style="margin-bottom: 10px;"></div>
					<div class="row">
						<div class="col-sm-6">
							<label for="data_felhasznalo_adatok_szallitas_nev">Szállítási név*</label>
							<input type="text" id="data_felhasznalok_nev" class="form-control" name="data[felhasznalo_adatok][szallitas_nev]" value="<?=$_POST[data][felhasznalo_adatok][szallitas_nev]?>" required>
						</div>
						<div class="col-sm-6">
							<label for="data_felhasznalo_adatok_szallitas_state">Megye*</label>
							<select name="data[felhasznalo_adatok][szallitas_state]" class="form-control" id="data_felhasznalo_adatok_szallitas_state" required>
	                            <option value="" selected="selected">-- válasszon --</option>
	                            <option value="" disabled="disabled"></option>
	                            <? foreach( $this->states as $s ): ?>
	                                <option value="<?=$s?>" <?=($s==$_POST[data][felhasznalo_adatok][szallitas_state])?'selected="selected"':''?>><?=$s?></option>
	                            <? endforeach; ?>
	                        </select>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-2">
							<label for="data_felhasznalo_adatok_szallitas_irsz">Irányítószám*</label>
							<input type="text" id="data_felhasznalok_irsz" class="form-control" name="data[felhasznalo_adatok][szallitas_irsz]" value="<?=$_POST[data][felhasznalo_adatok][szallitas_irsz]?>" required>
						</div>
						<div class="col-sm-3">
							<label for="data_felhasznalo_adatok_szallitas_city">Város*</label>
							<input type="text" id="data_felhasznalok_city" class="form-control" name="data[felhasznalo_adatok][szallitas_city]" value="<?=$_POST[data][felhasznalo_adatok][szallitas_city]?>" required>
						</div>
						<div class="col-sm-7">
							<label for="data_felhasznalo_adatok_szallitas_uhsz">Utca, házszám, emelet, ajtó*</label>
							<input type="text" id="data_felhasznalok_uhsz" class="form-control" name="data[felhasznalo_adatok][szallitas_uhsz]" value="<?=$_POST[data][felhasznalo_adatok][szallitas_uhsz]?>" required>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-12">
							<label for="data_felhasznalo_adatok_szallitas_phone">Telefonszám</label>
							<input type="text" id="data_felhasznalok_phone" class="form-control" name="data[felhasznalo_adatok][szallitas_phone]" value="<?=$_POST[data][felhasznalo_adatok][szallitas_phone]?>">
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-12">
							<label for="data_felhasznalo_user_group">Felhasználó csoport</label>
							<select name="data[user_group]" class="form-control" id="data_felhasznalo_user_group" required>
	                            <option value="" selected="selected">-- válasszon --</option>
	                            <option value="" disabled="disabled"></option>
	                            <? foreach( $this->user_groupes as $key => $value ): ?>
	                                <option value="<?=$key?>" <?=($key==$data[user_group])?'selected="selected"':''?>><?=$value?></option>
	                            <? endforeach; ?>
	                        </select>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<?php if (false): ?>
				<div class="con">
					<div style="float:right;">
						<label>Igen <input type="checkbox" name="is_reseller" <?=($_GET[reseller]=='1')?'checked="checked"':''?> onclick="if($(this).is(':checked')){$('#reseller_v').slideDown(200);}else{$('#reseller_v').slideUp(200);}" value="1"></label>
					</div>
					<h3 style="margin:0;">Viszonteladó</h3>
					<div class="clr"></div>
					<div id="reseller_v" style="display:<?=($_GET[reseller]=='1')?'block':'none'?>;">
						<div class="divider" style="margin-bottom: 10px;"></div>
						<div class="row">
							<div class="col-sm-12">
								<label for="data_felhasznalo_adatok_company_name">Cég neve</label>
								<input type="text" id="data_felhasznalo_adatok_company_name" class="form-control" name="data[felhasznalo_adatok][company_name]" value="<?=$_POST[data][felhasznalo_adatok][company_name]?>">
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-sm-6">
								<label for="data_felhasznalo_adatok_company_address">Cég postázási címe</label>
								<input type="text" id="data_felhasznalo_adatok_company_address" class="form-control" name="data[felhasznalo_adatok][company_address]" value="<?=$_POST[data][felhasznalo_adatok][company_address]?>">
							</div>
							<div class="col-sm-6">
								<label for="data_felhasznalo_adatok_company_hq">Cég székhelye</label>
								<input type="text" id="data_felhasznalo_adatok_company_hq" class="form-control" name="data[felhasznalo_adatok][company_hq]" value="<?=$_POST[data][felhasznalo_adatok][company_hq]?>">
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-sm-6">
								<label for="data_felhasznalo_adatok_company_adoszam">Adószám</label>
								<input type="text" id="data_felhasznalo_adatok_company_adoszam" class="form-control" name="data[felhasznalo_adatok][company_adoszam]" value="<?=$_POST[data][felhasznalo_adatok][company_adoszam]?>">
							</div>
							<div class="col-sm-6">
								<label for="data_felhasznalo_adatok_company_bankszamlaszam">Bankszámlaszám</label>
								<input type="text" id="data_felhasznalo_adatok_company_bankszamlaszam" class="form-control" name="data[felhasznalo_adatok][company_bankszamlaszam]" value="<?=$_POST[data][felhasznalo_adatok][company_bankszamlaszam]?>">
							</div>
						</div>
					</div>
				</div>
				<div class="con">
					<div style="float:right;">
						<label>Igen <input type="checkbox" name="is_distributor" onclick="if($(this).is(':checked')){$('#tanacsado_v').slideDown(200);}else{$('#tanacsado_v').slideUp(200);}" value="1"></label>
					</div>
					<h3 style="margin:0;">Tanácsadó</h3>
					<div class="clr"></div>
					<div id="tanacsado_v" style="display:none;">
						<div class="divider" style="margin-bottom: 10px;"></div>
						<div class="row">
							<div class="col-sm-6">
								<label for="data_felhasznalo_adatok_casadapont_tanacsado_titulus">Titulus</label>
								<input type="text" id="data_felhasznalo_adatok_casadapont_tanacsado_titulus" class="form-control" name="data[felhasznalo_adatok][casadapont_tanacsado_titulus]" value="<?=$_POST[data][felhasznalo_adatok][casadapont_tanacsado_titulus]?>">
							</div>
							<div class="col-sm-6">
								<label for="data_felhasznalo_adatok_casadapont_tanacsado_profil">Mellkép (arckép)</label>
								<input type="file" id="data_felhasznalo_adatok_casadapont_tanacsado_profil" class="form-control" name="profil[]">
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>

				<div class="con">
					<div class="row np">
						<?php if (false): ?>
							<div class="col-sm-6">
								<label><input type="checkbox" name="flag[alert_user]"> felhasználó e-mail értesítése</label>
							</div>
						<?php endif; ?>
						
						<div class="col-sm-12 right">
							<button class="btn btn-success" name="createUserByAdmin">Létrehozás <i class="fa fa-plus-circle"></i></button>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</form>
