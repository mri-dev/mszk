<? $data = $this->data; ?>
<?=$this->msg?>
<form action="" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-6">
			<div class="wblock">
				<div class="data-header">
					<div class="d-flex align-items-center">
			      <div class="col title">
			        <i class="ico fas fa-users"></i> <?=__('Fiók alapadatok')?>
			      </div>
			      <div class="col right"></div>
			    </div>
				</div>
				<div class="data-container">
					<div class="dc-padding">
						<div class="row">
							<div class="col-sm-6">
								<label for="data_felhasznalok_nev"><?=__('Név')?> *</label>
								<input type="text" id="data_felhasznalok_nev" class="form-control" name="data[felhasznalok][nev]" value="<?=$data[nev]?>" required>
							</div>
							<div class="col-sm-6">
								<label for="data_felhasznalok_email"><?=__('E-mail cím')?> *</label>
								<input type="text" id="data_felhasznalok_email" class="form-control" name="data[felhasznalok][email]" value="<?=$data[email]?>" required>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label for="data_felhasznalo_user_group"><?=__('Fiók csoport')?></label>
								<select name="data[felhasznalok][user_group]" class="form-control" id="data_felhasznalo_user_group" required>
	                  <option value="" selected="selected">-- válasszon --</option>
	                  <option value="" disabled="disabled"></option>
	                  <? foreach( $this->user_groupes as $key => $value ): ?>
	                      <option value="<?=$key?>" <?=($key==$data[user_group])?'selected="selected"':''?>><?=$value?></option>
	                  <? endforeach; ?>
	              </select>
							</div>
							<div class="col-sm-6">
								<label for="data_felhasznalok_jelszo"><?=__('Új jelszó beállítás')?></label>
								<input type="text" id="data_felhasznalok_jelszo" class="form-control" name="data[felhasznalok][jelszo]">
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="wblock">
				<div class="data-header">
					<div class="d-flex align-items-center">
			      <div class="col title">
			        <i class="fas fa-file-invoice"></i> <?=__('Számlázási adatok')?>
			      </div>
			      <div class="col right"></div>
			    </div>
				</div>
				<div class="data-container">
					<div class="dc-padding">
						<div class="dc-padding">
							<div class="row">
								<div class="col-sm-5">
									<label for="data_felhasznalo_adatok_szamlazas_nev"><?=__('Számlázási név')?></label>
									<input type="text" id="data_felhasznalok_nev" class="form-control" name="data[felhasznalo_adatok][szamlazas_nev]" value="<?=$data[szamlazas_nev]?>" >
								</div>
								<div class="col-sm-2">
									<label for="data_felhasznalo_adatok_szamlazas_irsz"><?=__('Irányítószám')?></label>
									<input type="text" id="data_felhasznalo_adatok_szamlazas_irsz" class="form-control" name="data[felhasznalo_adatok][szamlazas_irsz]" value="<?=$data[szamlazas_irsz]?>" >
								</div>
								<div class="col-sm-2">
									<label for="data_felhasznalo_adatok_szamlazas_kerulet"><?=__('Kerület')?></label>
									<input type="text" id="data_felhasznalo_adatok_szamlazas_kerulet" class="form-control" name="data[felhasznalo_adatok][szamlazas_kerulet]" value="<?=$data[szamlazas_kerulet]?>">
								</div>
								<div class="col-sm-3">
									<label for="data_felhasznalo_adatok_szamlazas_city"><?=__('Város')?></label>
									<input type="text" id="data_felhasznalo_adatok_szamlazas_city" class="form-control" name="data[felhasznalo_adatok][szamlazas_city]" value="<?=$data[szamlazas_city]?>" >
								</div>
							</div>

							<div class="row">
								<div class="col-sm-5">
									<label for="data_felhasznalo_adatok_szamlazas_kozterulet_nev"><?=__('Közterület neve')?></label>
									<input type="text" id="data_felhasznalo_adatok_szamlazas_kozterulet_nev" class="form-control" name="data[felhasznalo_adatok][szamlazas_kozterulet_nev]" value="<?=$data[szamlazas_kozterulet_nev]?>" >
								</div>
								<div class="col-sm-4">
									<label for="data_felhasznalo_adatok_szamlazas_kozterulet_jelleg"><?=__('Közterület jelleg')?></label>
									<select name="data[felhasznalo_adatok][szamlazas_kozterulet_jelleg]" class="form-control" id="data_felhasznalo_adatok_szamlazas_kozterulet_jelleg">
											<option value="">-- <?=__('válasszon')?> --</option>
											<option value="" disabled="disabled"></option>
											<? foreach( $this->kozterulet_jellege as $s ): ?>
											<option value="<?=$s?>" <?=( $data[szamlazas_kozterulet_jelleg] == $s ) ? 'selected="selected"' : ''?>><?=$s?></option>
											<? endforeach; ?>
										</select>
								</div>
								<div class="col-sm-3">
									<label for="data_felhasznalo_adatok_szamlazas_hazszam"><?=__('Házszám')?></label>
									<input type="text" id="data_felhasznalo_adatok_szamlazas_hazszam" class="form-control" name="data[felhasznalo_adatok][szamlazas_hazszam]" value="<?=$data[szamlazas_hazszam]?>">
								</div>
							</div>

							<div class="row">
								<div class="col-sm-3">
									<label for="data_felhasznalo_adatok_szamlazas_epulet"><?=__('Épület')?></label>
									<input type="text" id="data_felhasznalo_adatok_szamlazas_epulet" class="form-control" name="data[felhasznalo_adatok][szamlazas_epulet]" value="<?=$data[szamlazas_epulet]?>">
								</div>
								<div class="col-sm-3">
									<label for="data_felhasznalo_adatok_szamlazas_lepcsohaz"><?=__('Lépcsőház')?></label>
									<input type="text" id="data_felhasznalo_adatok_szamlazas_lepcsohaz" class="form-control" name="data[felhasznalo_adatok][szamlazas_lepcsohaz]" value="<?=$data[szamlazas_lepcsohaz]?>" >
								</div>
								<div class="col-sm-3">
									<label for="data_felhasznalo_adatok_szamlazas_szint"><?=__('Szint')?></label>
									<input type="text" id="data_felhasznalo_adatok_szamlazas_szint" class="form-control" name="data[felhasznalo_adatok][szamlazas_szint]" value="<?=$data[szamlazas_szint]?>" >
								</div>
								<div class="col-sm-3">
									<label for="data_felhasznalo_adatok_szamlazas_ajto"><?=__('Ajtó')?></label>
									<input type="text" id="data_felhasznalo_adatok_szamlazas_ajto" class="form-control" name="data[felhasznalo_adatok][szamlazas_ajto]" value="<?=$data[szamlazas_ajto]?>" >
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="wblock">
				<div class="data-header">
					<div class="d-flex align-items-center">
			      <div class="col title">
			        <i class="fas fa-dolly"></i> <?=__('Szállítási adatok')?>
			      </div>
			      <div class="col right"></div>
			    </div>
				</div>
				<div class="data-container">
					<div class="dc-padding">
						<div class="row">
							<div class="col-sm-5">
								<label for="data_felhasznalo_adatok_szallitas_nev"><?=__('Szállítási név')?></label>
								<input type="text" id="data_felhasznalok_nev" class="form-control" name="data[felhasznalo_adatok][szallitas_nev]" value="<?=$data[szallitas_nev]?>" >
							</div>
							<div class="col-sm-2">
								<label for="data_felhasznalo_adatok_szallitas_irsz"><?=__('Irányítószám')?></label>
								<input type="text" id="data_felhasznalo_adatok_szallitas_irsz" class="form-control" name="data[felhasznalo_adatok][szallitas_irsz]" value="<?=$data[szallitas_irsz]?>" >
							</div>
							<div class="col-sm-2">
								<label for="data_felhasznalo_adatok_szallitas_kerulet"><?=__('Kerület')?></label>
								<input type="text" id="data_felhasznalo_adatok_szallitas_kerulet" class="form-control" name="data[felhasznalo_adatok][szallitas_kerulet]" value="<?=$data[szallitas_kerulet]?>">
							</div>
							<div class="col-sm-3">
								<label for="data_felhasznalo_adatok_szallitas_city"><?=__('Város')?></label>
								<input type="text" id="data_felhasznalo_adatok_szallitas_city" class="form-control" name="data[felhasznalo_adatok][szallitas_city]" value="<?=$data[szallitas_city]?>" >
							</div>
						</div>

						<div class="row">
							<div class="col-sm-5">
								<label for="data_felhasznalo_adatok_szallitas_kozterulet_nev"><?=__('Közterület neve')?></label>
								<input type="text" id="data_felhasznalo_adatok_szallitas_kozterulet_nev" class="form-control" name="data[felhasznalo_adatok][szallitas_kozterulet_nev]" value="<?=$data[szallitas_kozterulet_nev]?>" >
							</div>
							<div class="col-sm-4">
								<label for="data_felhasznalo_adatok_szallitas_kozterulet_jelleg"><?=__('Közterület jelleg')?></label>
								<select name="data[felhasznalo_adatok][szallitas_kozterulet_jelleg]" class="form-control" id="data_felhasznalo_adatok_szallitas_kozterulet_jelleg">
										<option value="">-- <?=__('válasszon')?> --</option>
										<option value="" disabled="disabled"></option>
										<? foreach( $this->kozterulet_jellege as $s ): ?>
										<option value="<?=$s?>" <?=( $data[szallitas_kozterulet_jelleg] == $s ) ? 'selected="selected"' : ''?>><?=$s?></option>
										<? endforeach; ?>
									</select>
							</div>
							<div class="col-sm-3">
								<label for="data_felhasznalo_adatok_szallitas_hazszam"><?=__('Házszám')?></label>
								<input type="text" id="data_felhasznalo_adatok_szallitas_hazszam" class="form-control" name="data[felhasznalo_adatok][szallitas_hazszam]" value="<?=$data[szallitas_hazszam]?>">
							</div>
						</div>

						<div class="row">
							<div class="col-sm-3">
								<label for="data_felhasznalo_adatok_szallitas_epulet"><?=__('Épület')?></label>
								<input type="text" id="data_felhasznalo_adatok_szallitas_epulet" class="form-control" name="data[felhasznalo_adatok][szallitas_epulet]" value="<?=$data[szallitas_epulet]?>">
							</div>
							<div class="col-sm-3">
								<label for="data_felhasznalo_adatok_szallitas_lepcsohaz"><?=__('Lépcsőház')?></label>
								<input type="text" id="data_felhasznalo_adatok_szallitas_lepcsohaz" class="form-control" name="data[felhasznalo_adatok][szallitas_lepcsohaz]" value="<?=$data[szallitas_lepcsohaz]?>" >
							</div>
							<div class="col-sm-3">
								<label for="data_felhasznalo_adatok_szallitas_szint"><?=__('Szint')?></label>
								<input type="text" id="data_felhasznalo_adatok_szallitas_szint" class="form-control" name="data[felhasznalo_adatok][szallitas_szint]" value="<?=$data[szallitas_szint]?>" >
							</div>
							<div class="col-sm-3">
								<label for="data_felhasznalo_adatok_szallitas_ajto"><?=__('Ajtó')?></label>
								<input type="text" id="data_felhasznalo_adatok_szallitas_ajto" class="form-control" name="data[felhasznalo_adatok][szallitas_ajto]" value="<?=$data[szallitas_ajto]?>" >
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<label for="data_felhasznalo_adatok_szallitas_phone"><?=__('Telefonszám')?></label>
								<input type="text" id="data_felhasznalok_phone" class="form-control" name="data[felhasznalo_adatok][szallitas_phone]" value="<?=$data[szallitas_phone]?>">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col">
			<? if($data[user_group] == \PortalManager\Users::USERGROUP_USER || $data[user_group] == \PortalManager\Users::USERGROUP_SERVICES): ?>
			<div class="wblock">
				<div class="data-header">
					<div class="d-flex align-items-center">
						<div class="col title">
							<i class="far fa-building"></i> <?=__('Cég adatok megadása')?>
						</div>
						<div class="col right"></div>
					</div>
				</div>
				<div class="data-container">
					<div class="dc-padding">
						<div class="row">
							<div class="col-sm-8">
								<label for="data_felhasznalo_adatok_company_name">Cég neve</label>
								<input type="text" id="data_felhasznalo_adatok_company_name" class="form-control" name="data[felhasznalo_adatok][company_name]" value="<?=$data[company_name]?>">
							</div>
							<div class="col-sm-4">
								<label for="data_felhasznalo_adatok_company_adoszam">Adószám</label>
								<input type="text" id="data_felhasznalo_adatok_company_adoszam" class="form-control" name="data[felhasznalo_adatok][company_adoszam]" value="<?=$data[company_adoszam]?>">
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-sm-6">
								<label for="data_felhasznalo_adatok_company_address">Cég postázási címe</label>
								<input type="text" id="data_felhasznalo_adatok_company_address" class="form-control" name="data[felhasznalo_adatok][company_address]" value="<?=$data[company_address]?>">
							</div>
							<div class="col-sm-6">
								<label for="data_felhasznalo_adatok_company_hq">Cég székhelye</label>
								<input type="text" id="data_felhasznalo_adatok_company_hq" class="form-control" name="data[felhasznalo_adatok][company_hq]" value="<?=$data[company_hq]?>">
							</div>
						</div>
					</div>
				</div>
			</div>
			<? endif; ?>
			<div class="row">
				<div class="col right">
					<button class="btn btn-success" name="saveUserByAdmin">Változások mentése <i class="fa fa-save"></i></button>
				</div>
			</div>
		</div>
	</div>
</form>
