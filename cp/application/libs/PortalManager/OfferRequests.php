<?
namespace PortalManager;

use PortalManager\Users;
use PortalManager\Template;
use MailManager\Mailer;
use PortalManager\Categories;

/**
* class OfferRequests
* @package PortalManager
* @version 1.0
*/
class OfferRequests
{
	private $db = null;

	function __construct( $arg = array() )
	{
		$this->db = $arg[db];
		return $this;
	}

	public function getList( $arg = array() )
	{
		$list = array();
		$qarg = array();
		$q = "SELECT
			r.*
		FROM requests as r
		WHERE 1=1 ";

		if (isset($arg['offerout'])) {
			$q .= " and r.offerout  = :offerout";
			$qarg['offerout'] = (int)$arg['offerout'];
		}

		$data = $this->db->squery($q, $qarg);

		if ($data->rowCount() == 0) {
			return $list;
		}

		$data = $data->fetchAll(\PDO::FETCH_ASSOC);

		$users = new Users( array('db' => $this->db ));

		foreach ((array)$data as $d) {
			$d['cash'] = json_decode($d['cash'], true);
			$d['cash_config'] = json_decode($d['cash_config'], true);
			$d['services'] = $this->findServicesItems(json_decode($d['services'], true));
			$d['subservices'] = $this->findServicesItems(json_decode($d['subservices'], true));
			$d['subservices_items'] = $this->findServicesItems(json_decode($d['subservices_items'], true));
			$d['service_description'] = json_decode($d['service_description'], true);
			$d['user'] = $users->get( array('user' => $d['user_id'], 'userby' => 'ID') );
			$d['requested_at'] = \Helper::distanceDate($d['requested']);

			// Lehetséges szolgáltatók betöltése
			if (isset($arg['loadpossibleservices']) && $arg['loadpossibleservices'] == 1)
			{
				$d['services_hints'] = $this->possibleRequestServices( $d['services'], $d['subservices'], $d['subservices_items'] );
			}

			$list[] = $d;
		}

		return $list;
	}

	public function possibleRequestServices( $services, $subservices, $items )
	{
		$list = array();
		$itemids = array();
		$qarg = array();

		foreach ((array)$items as $i) {
			$itemids[] = (int)$i['ID'];
		}

		if (empty($itemids)) {
			return $list;
		}

		$q = "SELECT
			s.user_id,
			s.item_id,
			s.service_id,
			s.subservice_id,
			f.nev as user_neve,
			f.email as user_email,
			f.regisztralt,
			f.utoljara_belepett,
			fa.ertek as user_company,
			l1.neve as item_neve,
			l1.leiras as item_desc,
			l1.szulo_id as item_parent,
			l2.neve as service_neve,
			l2.leiras as service_desc,
			l2.szulo_id as service_parent,
			l3.neve as subservice_neve,
			l3.leiras as subservice_desc,
			l3.szulo_id as subservice_parent
		FROM felhasznalo_services as s
		LEFT OUTER JOIN felhasznalok as f ON f.ID = s.user_id
		LEFT OUTER JOIN felhasznalo_adatok as fa ON fa.fiok_id = s.user_id and fa.nev = 'company_name'
		LEFT OUTER JOIN lists as l1 ON l1.ID = s.item_id
		LEFT OUTER JOIN lists as l2 ON l2.ID = s.service_id
		LEFT OUTER JOIN lists as l3 ON l3.ID = s.subservice_id
		WHERE 1=1 and f.user_group = '".\PortalManager\Users::USERGROUP_SERVICES."' and  f.engedelyezve = 1 and f.aktivalva IS NOT NULL and f.mukodik = 1 ";
		$q .= " and s.item_id IN (".implode(",", $itemids).")";

		$qry = $this->db->squery($q, $qarg);

		if ($qry->rowCount() != 0) {
			$data = $qry->fetchAll(\PDO::FETCH_ASSOC);

			$in = array();
			foreach ((array)$data as $d)
			{
				if (!array_key_exists((int)$d['user_id'],	$in['users']))
				{
					$in['users'][(int)$d['user_id']] = array(
						'ID' => (int)$d['user_id'],
						'nev' => $d['user_neve'],
						'email' => $d['user_email'],
						'company' => $d['user_company'],
						'regisztralt' => $d['regisztralt'],
						'regisztralt_dist' => \Helper::distanceDate($d['regisztralt']),
						'utoljara_belepett' => $d['utoljara_belepett'],
						'utoljara_belepett_dist' => \Helper::distanceDate($d['utoljara_belepett'])
					);
				}

				$in['item'] = array(
					'ID' => (int)$d['item_id'],
					'szulo_id' => (int)$d['item_parent'],
					'nev' => $d['item_neve'],
					'desc' => $d['item_desc']
				);

				$in['service'] = array(
					'ID' => (int)$d['service_id'],
					'szulo_id' => (int)$d['service_parent'],
					'nev' => $d['service_neve'],
					'desc' => $d['service_desc']
				);

				$in['subservice'] = array(
					'ID' => (int)$d['subservice_id'],
					'szulo_id' => (int)$d['subservice_parent'],
					'nev' => $d['subservice_neve'],
					'desc' => $d['subservice_desc']
				);

				$list[(int)$d['item_id']] = $in;
			}
		}

		return $list;
	}

	public function collectOfferData( $value = '', $findby = 'hashkey' )
	{
		if (!in_array($findby, array('ID', 'hashkey', 'email', 'phone', 'name', 'company')))
		{
			$findby = 'hashkey';
		}

		$q = "SELECT r.* FROM requests as r WHERE r.".$findby." = :value";
		$qry = $this->db->squery($q, array('value' => trim($value) ));

		$rc = $qry->rowCount();

		if ( $rc == 0) {
			return array();
		} else {
			if ($rc == 1) {
				$data = $qry->fetch(\PDO::FETCH_ASSOC);
				$this->prepareRAWOfferData( $data );
			} else if($rc > 1){
				$datas = $qry->fetchAll(\PDO::FETCH_ASSOC);
				$data = array();
				foreach ((array)$datas as $d) {
					$this->prepareRAWOfferData( $d );
					$data[] = $d;
				}
			}
		}

		return $data;
	}

	private function prepareRAWOfferData( &$data )
	{
		$temp = $data;
		$data = array();

		$data['cash']['total'] = $temp['cash_total'];
		$data['cash']['subservices_overall'] = json_decode($temp['cash'], true);
		$data['cash']['subservicesitems'] = json_decode($temp['cash_config'], true);

		$data['services']['ids'] = json_decode($temp['services'], true);
		$data['services']['items'] = $this->findServicesItems( $data['services']['ids'] );
		$data['subservices']['ids'] = json_decode($temp['subservices'], true);
		$data['subservices']['items'] = $this->findServicesItems( $data['subservices']['ids'] );
		$data['subservicesitems']['ids'] = json_decode($temp['subservices_items'], true);
		$data['subservicesitems']['items'] = $this->findServicesItems( $data['subservicesitems']['ids'] );
		$data['subservices_descriptions'] = json_decode($temp['service_description'], true);

		$data['rawdb'] = $temp;
		unset($temp);

		return $data;
	}

	private function findServicesItems( $ids = array() )
	{
		$list = array();

		$handler = new Categories(array('db' => $this->db));
		$arg = array();
		$arg['group_id'] = 1;
		$arg['id_set'] = (array)$ids;
		$arg['parenting_off'] = true;
		$arg['unload_child'] = true;
		$lists 	= $handler->getTree( false, $arg );

		while( $lists->walk() ){
			$item = $lists->the_cat();
			$list[] = $item;
		}

		return $list;
	}

  public function sendRequest( $requester, $config )
  {
		$ret = array();
		$users = new Users(array('db' => $this->db));

    // Ellenőrzés
    if (empty($requester['name'])) {
      throw new \Exception(__('Kérjük, hogy adja meg a saját nevét!'));
    }

    if (empty($requester['email'])) {
      throw new \Exception(__('Kérjük, hogy adja meg a saját e-mail címét!'));
    }

    if (empty($requester['phone'])) {
      throw new \Exception(__('Kérjük, hogy adja meg a telefonszámát!'));
    }

    if (empty($requester['aszf'])) {
      throw new \Exception(__('Az ajánlatkérés elküldéséhez kötelezően el kell fogadni az Általános Szerződési Feltételeket.'));
    }

    if (empty($requester['adatvedelem'])) {
      throw new \Exception(__('Az ajánlatkérés elküldéséhez kötelezően hozzá kell járulni az adatai kezeléséhez, melyről az Adatvédelmi Tájékoztatótban tájékozódhat.'));
    }

		// Adat előkészítés
		// subservice leírások
		$service_desc = array();
		foreach ((array)$config['service_desc'] as $id => $desc)
		{
			if (empty($desc)) continue;
			$service_desc[$id] = $desc;
		}

		$selected_cashall = array();
		$total_cash = 0;
		foreach ((array)$config['selected_cashall'] as $id => $v)
		{
			if (empty($v)) continue;
			$total_cash += (float)$v;
			$selected_cashall[$id] = $v;
		}

		$selected_cashrow = array();
		foreach ((array)$config['selected_cashrow'] as $id => $v)
		{
			if (empty($v)) continue;
			$selected_cashrow[$id] = $v;
		}

    // Gyors felh. fiók regisztráció

		$user_id = 0;
		// check email usage
		$exists_user = $users->userExists( 'email', trim($requester['email']) );
		if ($exists_user)
		{
			$user_data = $users->getData(  trim($requester['email']), 'email' );
			$user_id = (int)$user_data['ID'];
		}

		// Ha nem létező felhasználó, akkor létrehozás
		if ( !$exists_user || $user_id == 0 )
		{
			$rand_password = substr(uniqid(), -10);
			$user_id = $users->createByAdmin(array(
				'data' => array(
					'felhasznalok' => array(
						'nev' => addslashes(trim($requester['name'])),
						'email' => addslashes(trim($requester['email'])),
						'jelszo' => $rand_password
					)
				)
			));
			if ($user_id) {
				$ret['created_user_id'] = $user_id;
			}
		}

		// Igény regisztrálása
		$hashkey = md5(uniqid());
		$this->db->insert(
				"requests",
				array(
					'hashkey' => $hashkey,
					'user_id' => $user_id,
					'email' => addslashes(trim($requester['email'])),
					'name' => addslashes(trim($requester['name'])),
					'company' => addslashes(trim($requester['company'])),
					'phone' => addslashes(trim($requester['phone'])),
					'message' => addslashes(trim($requester['message'])),
					'services' => json_encode($config['selected_services'], \JSON_UNESCAPED_UNICODE),
					'subservices' => json_encode($config['selected_subservices'], \JSON_UNESCAPED_UNICODE),
					'subservices_items' => json_encode($config['selected_subservices_items'], \JSON_UNESCAPED_UNICODE),
					'cash_config' => json_encode($selected_cashrow, \JSON_UNESCAPED_UNICODE),
					'cash' => json_encode($selected_cashall, \JSON_UNESCAPED_UNICODE),
					'cash_total' => $total_cash,
					'service_description' => json_encode($service_desc, \JSON_UNESCAPED_UNICODE)
				)
		);
		$request_id = $this->db->lastInsertId();

		$ret['request_hashkey'] = $hashkey;
		$ret['request_id'] = (int)$request_id;
		$ret['email'] = trim($requester['email']);

		$configuration = $this->collectOfferData( $hashkey );

		// Ha van user_id vagy létre lett hozva
		if ( $user_id != 0 )
		{
			$ret['user_id'] = $user_id;

			// E-mail - Igénylő értesítő
			if (true)
			{
				$mail = new Mailer( $this->db->settings['page_title'], SMTP_USER, $this->db->settings['mail_sender_mode'] );
				$mail->add( trim($requester['email']) );
				$arg = array(
					'nev' => trim($requester['name']),
					'jelszo' => $rand_password,
					'settings' => $this->db->settings,
					'data' => array(
						'requester' => $requester,
						'config' => $config
					),
					'request' => array(
						'hashkey' => $hashkey,
						'id' => (int)$request_id,
					),
					'configuration' => $configuration,
					'new_user_id' => $ret['created_user_id'],
					'user_id' => $user_id,
					'infoMsg' => 'Ezt az üzenetet a rendszer küldte. Kérjük, hogy ne válaszoljon rá!'
				);
				$mail->setSubject( __('Ajánlatkérését fogadtuk.') );
				$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'request_new_user', $arg ) );
				$re = $mail->sendMail();
			}

	    // E-mail - Admin értesítő
			if (true)
			{
				$mail = new Mailer( $this->db->settings['page_title'], SMTP_USER, $this->db->settings['mail_sender_mode'] );
				$mail->add( $this->db->settings['alert_email'] );
				$arg = array(
					'nev' => trim($requester['name']),
					'jelszo' => $rand_password,
					'settings' => $this->db->settings,
					'data' => array(
						'requester' => $requester,
						'config' => $config
					),
					'request' => array(
						'hashkey' => $hashkey,
						'id' => (int)$request_id,
					),
					'configuration' => $configuration,
					'new_user_id' => $ret['created_user_id'],
					'user_id' => $user_id,
					'infoMsg' => 'Ezt az üzenetet a rendszer küldte. Kérjük, hogy ne válaszoljon rá!'
				);
				$mail->setSubject( __('Értesítés: új ajánlatkérés érkezett.') );
				$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'request_new_admin', $arg ) );
				$re = $mail->sendMail();
			}
		}

		return $ret;
  }

	public function __destruct()
	{
		$this->db = null;
	}

}
?>
