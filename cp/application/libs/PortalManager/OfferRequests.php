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
		LEFT OUTER JOIN offers as o ON o.ID = r.admin_offer_id
		WHERE 1=1 ";

		if ( isset($arg['ids']) && !empty($arg['ids']) ) {
			$q .= " and FIND_IN_SET(r.ID, :idslist)";
			$qarg['idslist'] = implode(",", (array)$arg['ids']);
		}

		if (isset($arg['offerout'])) {
			$q .= " and r.offerout  = :offerout";
			$qarg['offerout'] = (int)$arg['offerout'];
		}

		if (isset($arg['elutasitva'])) {
			$q .= " and r.elutasitva  = :elutasitva";
			$qarg['elutasitva'] = (int)$arg['elutasitva'];
		}

		if (isset($arg['user'])) {
			$q .= " and r.user_id  = :uid";
			$qarg['uid'] = (int)$arg['user'];
		}

		if (isset($arg['allpositive']) && $arg['allpositive'] == '1') {
			$q .= " and o.accepted  = 1 and o.project_id IS NULL";
			//$qarg['offerout'] = (int)$arg['offerout'];
		}

		if (isset($arg['letrejott']) && $arg['letrejott'] == '1') {
			$q .= " and r.project_id IS NOT NULL";
			//$qarg['offerout'] = (int)$arg['offerout'];
		}

		$q .= " ORDER BY r.project_id ASC, r.visited ASC, r.requested ASC";

		$data = $this->db->squery($q, $qarg);
		if ($data->rowCount() == 0) {
			return $list;
		}

		$data = $data->fetchAll(\PDO::FETCH_ASSOC);

		$users = new Users( array('db' => $this->db ));

		foreach ((array)$data as $d)
		{
			if (!isset($arg['shortlist']))
			{
				$d['cash'] = json_decode($d['cash'], true);
				$d['cash_config'] = json_decode($d['cash_config'], true);
				$d['services'] = $this->findServicesItems(json_decode($d['services'], true));
				$d['subservices'] = $this->findServicesItems(json_decode($d['subservices'], true));
				$d['service_description'] = json_decode($d['service_description'], true);
				$d['user'] = $users->get( array('user' => $d['user_id'], 'userby' => 'ID') );
			}

			$d['subservices_items'] = $this->findServicesItems(json_decode($d['subservices_items'], true));
			$d['requested_at'] = \Helper::distanceDate($d['requested']);

			if (isset($arg['servicetree']))
			{
				$d['services_list'] = $this->getServicesList( $d['subservices_items'] );
			}

			// Lehetséges szolgáltatók betöltése
			/*	Kikapcsolva
			if ( isset($arg['loadpossibleservices']) && $arg['loadpossibleservices'] == 1 )
			{
				$d['services_hints'] = $this->possibleRequestServices( $d['services'], $d['subservices'], $d['subservices_items'], $d['user_id'] );
				$d['offerouts'] = $this->getRequestOfferouts( (int)$d[ID] );
			}
			*/

			$d['offerouts'] = $this->getRequestOfferouts( (int)$d[ID] );
			$offers = $this->getOfferDatas( (int)$d[ID] );
			$d['offers'] = $offers;
			$unwatched_offers = 0;

			if ($offers) {
				foreach ( (array)$offers as $offer ) {
					if ( empty($offer['admin_visited']) ) {
						$unwatched_offers++;
					}
				}
			}
			unset($offers);

			$admin_offer = false;
			if (	$d['admin_offer_id'] != '' ) {
				$admin_offer = $this->getOfferData($d['admin_offer_id']);
			}
			$d['admin_offer'] = $admin_offer;

			$project_data = false;
			if ( $d['project_id'] != '' ) {
				$project_data = $this->db->squery("SELECT p.* FROM projects as p WHERE p.ID = :project", array('project' => (int)$d['project_id']))->fetch(\PDO::FETCH_ASSOC);
			}
			$d['project_data'] = $project_data;

			$d['unwatched_offers'] = $unwatched_offers;

			if ( isset($arg['bindIDToList']) && $arg['bindIDToList'] == 1 ) {
				$list[$d['ID']] = $d;
			} else {
				$list[] = $d;
			}
		}

		return $list;
	}

	public function getServicesList( $items )
	{
		$back = array();

		if (empty($items))
		{
			return $back;
		}

		foreach ((array)$items as $i )
		{
			$parent = $i['szulo_id'];
			$parents = array();
			$fullname = '';

			while( $parent ) {
				$p = $this->getCatData( $parent );
				$parents[] = array(
					'ID' => $p['ID'],
					'neve' => $p['neve'],
					'szulo_id' => $p['szulo_id']
				);

				if ($p['szulo_id'] == '0') {
					$parent = false;
				} else {
					$parent = $p['szulo_id'];
				}
			}

			$parents = array_reverse($parents);

			foreach ((array)$parents as $pa) {
				$fullname .= $pa['neve'] . ' / ';
			}

			$fullname .= $i['neve'];

			$dat = array(
				'ID' => $i['ID'],
				'neve' => $i['neve'],
				'fullneve' => $fullname,
				'szulo_id' => $i['szulo_id'],
				'parents' => $parents
			);

			$back[] = $dat;
		}


		return $back;
	}

	public function getCatData( $id )
	{
		$qarg = array();
		$q = "SELECT
			l.*
		FROM lists as l
		WHERE 1=1 ";

		$q .= " and l.group_id = 1";

		$q .= " and l.ID = :id";
		$qarg['id'] = (int)$id;

		$data = $this->db->squery($q, $qarg);
		if ($data->rowCount() == 0) {
			return false;
		}

		$data = $data->fetch(\PDO::FETCH_ASSOC);

		return $data;
	}

	public function setAdminVisitedOffer( $offerid = 0 )
	{
		$this->db->update(
			"offers",
			array(
				'admin_visited' => NOW
			),
			sprintf("ID = %d and admin_visited IS NULL", $offerid)
		);
	}

	public function registerAdminOffer($user_id, $request_id, $offer )
	{
		// Register offer
		$this->db->insert(
			"offers",
			array(
				'from_admin' => 1,
				'original_id' => (int)$offer['ID'],
				'from_user_id' => $user_id,
				'offerout_id' => (int)$offer['offerout_id'],
				'message' => $offer['message'],
				'project_start_at' => $offer['project_start_at'],
				'offer_project_idotartam' => $offer['project_idotartam'],
				'price' => (float)$offer['price']
			)
		);

		$offer_id = $this->db->lastInsertId();

		// update request admin_offer_id
		$this->db->update(
			"requests",
			array(
				'admin_offer_id' => $offer_id
			),
			sprintf("ID = %d", (int)$request_id)
		);

		// update original offer accepting datas
		$this->db->update(
			"offers",
			array(
				'admin_offered_out' => $offer_id
			),
			sprintf("ID = %d", (int)$offer['ID'])
		);

		// update admin visit on all request offers
		$this->db->update(
			"offers",
			array(
				'admin_visited' => NOW
			),
			sprintf("offerout_id = %d and admin_visited IS NULL", $request_id)
		);

		// TODO: E-mail értesítő az értintettnek

		return (int)$offer_id;

	}

	public function registerOffer( $user_id, $request, $offer )
	{
		// Register offer
		$this->db->insert(
			"offers",
			array(
				'from_admin' => 0,
				'from_user_id' => $user_id,
				'offerout_id' => $request['ID'],
				'message' => $offer['message'],
				'project_start_at' => $offer['project_start_at'],
				'offer_project_idotartam' => $offer['project_idotartam'],
				'price' => (float)$offer['price']
			)
		);

		$offer_id = $this->db->lastInsertId();

		// Offerout update offer id
		$this->db->update(
			"requests_offerouts",
			array(
				'user_offer_id' => $offer_id,
				'recepient_accepted' => 1,
				'recepient_visited_at' => NOW
			),
			sprintf("ID = %d", (int)$request['ID'])
		);

		// TODO: E-mail értesítő az értintettnek

		return (int)$offer_id;

	}

	public function acceptOffer( $request_id, $offer_id, $user_id, $projectdata )
	{
		// jelszó ellenőrzés
		$pass = \Hash::jelszo( trim($projectdata['password']) );
		$uv = $this->db->squery("SELECT ID FROM felhasznalok WHERE ID = :id and jelszo = :pw", array('id' => $user_id, 'pw' => $pass));
		if ( $uv->rowCount() == 0 ) {
			throw new \Exception(__('A megadott jelszó hibás! Kérjük, hogy adja meg a fiók jelszavát az ajánlat elfogadásához.'));
		}

		// update offer
		$this->db->update(
			"offers",
			array(
				'accepted' => 1,
				'accepted_at' => NOW
			),
			sprintf("ID = %d", (int)$offer_id)
		);

		// update request
		$this->db->update(
			"requests",
			array(
				'closed' => 1,
				'user_requester_title' => addslashes($projectdata['project'])
			),
			sprintf("ID = %d", (int)$request_id)
		);
		// TODO: értesítés az adminnak az elfogadásról
	}

	// // TODO: Admin offer accepter
	public function acceptOfferByAdmin( $user_id, $requester_id, $servicer_id, $request_id, $offer_id, $projectdata, $relation )
	{
		// jelszó ellenőrzés
		$pass = \Hash::jelszo( trim($projectdata['password']) );

		$uv = $this->db->squery("SELECT ID FROM felhasznalok WHERE ID = :id and jelszo = :pw", array('id' => $user_id, 'pw' => $pass));

		if ( $uv->rowCount() == 0 ) {
			throw new \Exception(__('A megadott jelszó hibás! Kérjük, hogy adja meg a fiók jelszavát a projekt létrehozásához.'));
		}

		$hashkey = md5(uniqid());
		$this->db->insert(
			"projects",
			array(
				'hashkey' => $hashkey,
				'requester_id' => $requester_id,
				'servicer_id' => $servicer_id,
				'request_id' => $request_id,
				'offer_id' => $offer_id,
				'requester_title' => ($relation == 'from') ? $projectdata['project'] : NULL,
				'servicer_title' => ($relation == 'to') ? $projectdata['project'] : NULL
			)
		);

		$inserted_project_id = $this->db->lastInsertId();

		$req_id = (int)$this->db->squery("SELECT request_id FROM requests_offerouts WHERE ID = :id", array('id' => $request_id))->fetchColumn();

		// update requests_offerouts
		$this->db->update(
			"requests_offerouts",
			array(
				'project_id' => $inserted_project_id,
				'requester_accepted' => 1
			),
			sprintf("ID = %d", (int)$request_id)
		);

		// update offer
		$this->db->update(
			"offers",
			array(
				'accepted' => 1,
				'project_id' => $inserted_project_id,
				'accepted_at' => NOW
			),
			sprintf("ID = %d", (int)$offer_id)
		);

		$offer_data = $this->getOfferData( $offer_id );

		// e-mail értesítő az igénylőnek (requester)
		$requester_data = $this->db->squery("SELECT ID, nev, email FROM felhasznalok WHERE ID = :id", array('id' => $requester_id))->fetch(\PDO::FETCH_ASSOC);

		if (true)
		{
			$mail = new Mailer( $this->db->settings['page_title'], SMTP_USER, $this->db->settings['mail_sender_mode'] );
			$mail->add( trim($requester_data['email']) );
			$arg = array(
				'nev' => trim($requester_data['nev']),
				'projekt_hashkey' => $hashkey,
				'projekt_szolgaltatas' => $offer_data['szolgaltatas']['fullneve'],
				'projekt_price' => $offer_data['price'],
				'projekt_idotartam' => $offer_data['offer_project_idotartam'],
				'projekt_start' => $offer_data['project_start_at'],
				'projekt_elfogadva' => $offer_data['accepted_at'],
				'settings' => $this->db->settings,
				'infoMsg' => 'Ezt az üzenetet a rendszer küldte. Kérjük, hogy ne válaszoljon rá!'
			);
			$mail->setSubject( __('Elfogadott egy ajánlatot. Új projekt létrehozva!') );
			$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'offers_accept_to_requester', $arg ) );
			$re = $mail->sendMail();
		}

		// e-mail értesítő a szolgáltatónak (servicer)
		$servicer_data = $this->db->squery("SELECT ID, nev, email FROM felhasznalok WHERE ID = :id", array('id' => $servicer_id))->fetch(\PDO::FETCH_ASSOC);

		if (true)
		{
			$mail = new Mailer( $this->db->settings['page_title'], SMTP_USER, $this->db->settings['mail_sender_mode'] );
			$mail->add( trim($servicer_data['email']) );
			$arg = array(
				'nev' => trim($servicer_data['nev']),
				'projekt_hashkey' => $hashkey,
				'projekt_szolgaltatas' => $offer_data['szolgaltatas']['fullneve'],
				'projekt_price' => $offer_data['price'],
				'projekt_idotartam' => $offer_data['offer_project_idotartam'],
				'projekt_start' => $offer_data['project_start_at'],
				'projekt_elfogadva' => $offer_data['accepted_at'],
				'settings' => $this->db->settings,
				'infoMsg' => 'Ezt az üzenetet a rendszer küldte. Kérjük, hogy ne válaszoljon rá!'
			);
			$mail->setSubject( __('Elfogadták az egyik ajánlatát. Új projekt létrehozva!') );
			$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'offers_accept_to_servicer', $arg ) );
			$re = $mail->sendMail();
		}

		// Messanger create
		$this->db->insert(
			\MessageManager\Messanger::DBTABLE,
			array(
				'sessionid' => $hashkey,
				'project_id' => $inserted_project_id,
				'requester_id' => $requester_data['ID'],
				'servicer_id' => $servicer_data['ID']
			)
		);

		// Messanger system message
		$this->db->insert(
			\MessageManager\Messanger::DBTABLE_MESSAGES,
			array(
				'sessionid' => $hashkey,
				'message' => __('Üzenetváltás automatikusan létrejött a projekt létrejöttével. Mostantól ezen a felületen tarthatja partnerével a kapcsolatot!'),
				'user_from_id' => 0,
				'user_to_id' => 0,
				'requester_alerted' => 1,
				'servicer_alerted' => 1
			)
		);

		return $hashkey;
	}

	public function getUserOfferRequests($uid, $user_group, $relation, $arg = array())
	{
		$re = array();

		// Beérkező ajánlatok
		if ($relation == 'to') {
			$re = $this->getINBOXOffers($uid, $user_group, $arg);
		}

		// Kimenő ajánlatok
		if ($relation == 'from') {
			$re = $this->getOUTBOXOffers($uid, $user_group, $arg);
		}

		return $re;
	}

	public function getINBOXOffers( $uid, $user_group, $arg = array())
	{
		$re = array();
		$qarg = array();

		$q = "SELECT
			r.hashkey,
			r.services,
			r.subservices,
			r.subservices_items,
			r.cash_total,
			r.cash,
			r.message,
			r.service_description,
			r.closed as request_closed,
			ro.ID,
			ro.offerout_at,
			ro.user_offer_id,
			ro.recepient_visited_at,
			ro.recepient_declined,
			ro.recepient_accepted
		FROM requests_offerouts as ro
		LEFT OUTER JOIN requests as r ON r.ID = ro.request_id
		WHERE 1=1";
		$q .= " and ro.user_id = :uid";
		$qarg['uid'] = $uid;

		$q .= " ORDER BY ro.user_offer_id DESC, ro.recepient_declined ASC, ro.recepient_visited_at ASC, ro.offerout_at DESC";

		if (isset($arg['limit'])) {
			$limit = (!empty($arg['limit'])) ? (int)$arg['limit'] : 10;
			$q .= " LIMIT 0,".$limit;
		}
		
		$qry = $this->db->squery($q, $qarg);

		if ($qry->rowCount() == 0) {
			return $re;
		}

		$data = $qry->fetchAll(\PDO::FETCH_ASSOC);

		foreach ( (array)$data as $d ) {
			$d['services'] =  $this->findServicesItems((array)json_decode($d['services'], true));
			$d['subservices'] =  $this->findServicesItems((array)json_decode($d['subservices'], true));
			$d['subservices_items'] =  $this->findServicesItems((array)json_decode($d['subservices_items'], true));
			$d['service_description'] =  (array)json_decode($d['service_description'], true);
			$d['cash'] =  (array)json_decode($d['cash'], true);
			$d['offerout_dist'] = \Helper::distanceDate($d['offerout_at']);
			$d['offer'] = $this->getOfferData($d['user_offer_id']);
			$d['status'] = $this->getOfferStatus($d, 'to');
			$re[] = $d;
		}

		return $re;
	}

	public function getOUTBOXOffers( $uid, $user_group, $arg = array())
	{
		$re = array();
		$qarg = array();

		$q = "SELECT
			r.ID,
			r.user_id,
			r.hashkey,
			r.user_requester_title,
			r.admin_offer_id,
			r.services,
			r.subservices,
			r.subservices_items,
			r.cash_total,
			r.cash,
			r.visited,
			r.visited_at as recepient_visited_at,
			r.message,
			r.service_description,
			r.offerout,
			r.elutasitva,
			r.project_id,
			r.closed as request_closed,
			r.requested as offerout_at
		FROM requests as r
		WHERE 1=1";
		$q .= " and r.user_id = :uid";
		$qarg['uid'] = $uid;

		$q .= " ORDER BY r.elutasitva ASC, r.closed ASC, r.visited ASC, r.admin_offer_id DESC, r.requested DESC";

		if (isset($arg['limit'])) {
			$limit = (!empty($arg['limit'])) ? (int)$arg['limit'] : 10;
			$q .= " LIMIT 0,".$limit;
		}

		$qry = $this->db->squery($q, $qarg);

		if ($qry->rowCount() == 0) {
			return $re;
		}

		$data = $qry->fetchAll(\PDO::FETCH_ASSOC);

		foreach ( (array)$data as $d ) {
			$d['services'] =  $this->findServicesItems((array)json_decode($d['services'], true));
			$d['subservices'] =  $this->findServicesItems((array)json_decode($d['subservices'], true));
			$d['subservices_items'] =  $this->findServicesItems((array)json_decode($d['subservices_items'], true));
			$d['service_description'] =  (array)json_decode($d['service_description'], true);
			$d['cash'] =  (array)json_decode($d['cash'], true);
			$d['offerout_dist'] = \Helper::distanceDate($d['offerout_at']);
			//$d['offer'] = $this->getOfferData($d['user_offer_id']);*/

			$admin_offer = false;
			if (	$d['admin_offer_id'] != '' ) {
				$admin_offer = $this->getOfferData($d['admin_offer_id']);
			}
			$d['admin_offer'] = $admin_offer;
			$d['status'] = $this->getOfferStatus($d, 'from');

			$re[] = $d;
		}

		return $re;
	}

	public function getOfferStatus( $data, $by )
	{
		$status = array();

		switch ($by)
		{
			case 'from':
				$status = array(
					'text' => __('Feldolgozatlan'),
					'title' => __('A kérést még nem dolgoztuk fel.'),
					'color' => 'orange'
				);

				// Olvasott - Admin által olvasva
				if ($data['visited'] == 1)
				{
					$status = array(
						'text' => __('Olvasott'),
						'title' => sprintf(__('A közvetítő adminisztrátorai elolvasták a kérést: %s'), $data['recepient_visited_at']),
						'color' => '#efc23a'
					);
				}

				// Feldolgozva - Admin által kiajáltva
				if ($data['offerout'] == 1)
				{
					$status = array(
						'text' => __('Feldolgozott'),
						'title' => __('A kérése feldolgozás alatt áll, ajánlat készítés folyamatban.'),
						'color' => '#9cce8e'
					);
				}

				// Lezárva - user által elutasítva
				if (!empty($data['admin_offer_id']) && $data['project_id'] == 0)
				{
					$status = array(
						'text' => __('Elfogadásra vár'),
						'title' => __('A közvetítő elküldte ajánlatát. Az ajánlat elfogadásra várakozik!'),
						'color' => '#007bff'
					);
				}

				// Elutasítva - Admin által elutasítva
				if ($data['elutasitva'] == 1)
				{
					$status = array(
						'text' => __('Elutasítva'),
						'title' => __('A kérése elutasításra került az adminisztrátorok által.'),
						'color' => '#c20707'
					);
				}

				// Ajánlat elfogadva - user által elfogadva
				if (!empty($data['admin_offer_id']) && $data['request_closed'] == 1 && empty($data['project_id']) && $data['admin_offer'] && $data['admin_offer']['accepted'] == 1)
				{
					$status = array(
						'text' => __('Ajánlat elfogadva'),
						'title' => __('Ajánlat elfogadva. Projekt létrehozására várakozik.'),
						'color' => '#21a9f1'
					);
				}

				// Projekt létrejött
				if (!empty($data['admin_offer_id']) && $data['request_closed'] == 1 && !empty($data['project_id']) && $data['admin_offer'] && $data['admin_offer']['accepted'] == 1)
				{
					$status = array(
						'text' => __('Projekt létrejött'),
						'title' => __('A projekt sikeresen létrejött!'),
						'color' => '#28a745'
					);
				}
			break;
			case 'to':
				$status = array(
					'text' => __('Feldolgozatlan'),
					'title' => __('Új beérkezett ajánlatkérés.'),
					'color' => 'orange'
				);

				// Olvasott - Admin által olvasva
				if ( !empty($data['recepient_visited_at']) )
				{
					$status = array(
						'text' => __('Megtekintett'),
						'title' => sprintf(__('Az ajánlatkérést megtekintette: %s'), $data['recepient_visited_at']),
						'color' => '#efc23a'
					);
				}

				// Elutasítva
				if ($data['recepient_declined'] == 1)
				{
					$status = array(
						'text' => __('Elutasítva'),
						'title' => __('Az ajánlatkérést elutasította.'),
						'color' => '#c20707'
					);
				}

				// Feldolgozva - Ajnálat elküldve
				if ( !empty($data['user_offer_id']) )
				{
					$status = array(
						'text' => __('Ajánlat elküldve'),
						'title' => __('A kérést elfogadta és elküldte a személyes ajánlatát.'),
						'color' => '#007bff'
					);
				}

				// Projekt létrejött
				if (!empty($data['user_offer_id']) && !empty($data['offer']) && $data['offer']['accepted'] == 1)
				{
					$status = array(
						'text' => __('Ajánlat elfogadva'),
						'title' => __('A projekt sikeresen létrejött!'),
						'color' => '#28a745'
					);
				}
			break;

			default: break;
		}

		return $status;
	}

	// TODO: Törölni majd, ha kész az új
	public function old_getUserOfferRequests( $uid, $user_group, $arg = array() )
	{
		$re = array();
		$qarg = array();
		$format = (isset($arg['format'])) ? $arg['format'] : 'grouped';

		$q = "SELECT
			ro.ID,
			ro.offerout_at,
			ro.request_id,
			ro.user_id as user_to_id,
			ro.recepient_visited_at,
			ro.recepient_accepted,
			ro.recepient_declined,
			ro.requester_accepted,
			ro.user_offer_id,
			r.hashkey as request_hashkey,
			r.cash,
			r.cash_config,
			r.user_id as user_from_id,
			r.name as requester_form_name,
			r.company as requester_form_company,
			r.phone as requester_form_phone,
			r.email as requester_form_email,
			r.message as requester_form_message,
			r.service_description,
			r.requested,
			r.offerout,
			r.closed as request_closed,
			IF(ro.user_id = :uid, 'to', 'from') as my_relation
		FROM `requests_offerouts` as ro
		LEFT OUTER JOIN requests as r ON r.ID = ro.request_id
		WHERE 1=1 ";

		$q .= " and (ro.user_id = :uid or r.user_id = :uid)";
		$qarg['uid'] = (int)$uid;

		if (isset($arg['inprogress']))
		{
			if ( $arg['inprogress'] == 1 )
			{
				$q .= " and r.closed = 0 and (ro.recepient_accepted = 0 and ro.recepient_declined = 0)";
			}
			else if( $arg['inprogress'] == 0 )
			{
				$q .= " and (ro.recepient_accepted = 1 or ro.recepient_declined = 1)";
			}
		}

		if (isset($arg['accepted']))
		{
			if ( $arg['accepted'] == 1 )
			{
				$q .= " and ro.recepient_accepted = 1";
			}
			else if( $arg['accepted'] == 0 )
			{
				$q .= " and ro.recepient_accepted = 0";
			}
		}

		if (isset($arg['offeraccepted']))
		{
			if ( $arg['offeraccepted'] == 1 )
			{
				$q .= " and ro.requester_accepted = 1";
			}
			else if( $arg['offeraccepted'] == 0 )
			{
				$q .= " and ro.requester_accepted = 0";
			}
		}

		if (isset($arg['progressed']))
		{
			if ( $arg['progressed'] == 1 )
			{
				$q .= " and ((ro.recepient_accepted = 1 and ro.requester_accepted IS NULL) or ro.recepient_declined = 1)";
			}
		}

		if ($format == 'list') {
			//$q .= " GROUP BY r.hashkey ";
		}

		$q .= " ORDER BY r.closed ASC, r.requested DESC, ro.recepient_declined ASC, ro.recepient_visited_at ASC";

		$qry = $this->db->squery($q, $qarg);

		if ($qry->rowCount() == 0) {
			return $re;
		}

		$users = new Users( array('db' => $this->db ));

		$data = $qry->fetchAll(\PDO::FETCH_ASSOC);
		$from_num = 0;
		$from_users_num = 0;
		$to_num = 0;
		$to_users_num = 0;
		$from_hashes = array();
		$to_hashes = array();

		foreach ( (array)$data as $d )
		{
			$xserv = explode("_",$d['configval']);
			$servicegroup = $xserv[0].'_'.$xserv[1];

			if ($d['my_relation'] == 'from') {
				$from_users_num += 1;
				if (!in_array($d['request_hashkey'], $from_hashes)) {
					$from_hashes[] = $d['request_hashkey'];
				}
			}
			if ($d['my_relation'] == 'to') {
				$to_users_num += 1;
				if (!in_array($d['request_hashkey'], $to_hashes)) {
					$to_hashes[] = $d['request_hashkey'];
				}
			}

			if ($format == 'grouped') {
				$d['servicegroup'] = $servicegroup;
				$d['service'] = $this->findServicesItems((array)$xserv[0])[0];
				$d['subservice'] = $this->findServicesItems((array)$xserv[1])[0];
				$d['item'] = $this->findServicesItems((array)$d['item_id'])[0];
				$d['servicegroup_name'] = $d['service']['neve']. ' / '.$d['subservice']['neve'];
				$d['cash'] = json_decode($d['cash'], true);
				$d['cash_config'] = json_decode($d['cash_config'], true);
				$d['service_description'] = json_decode($d['service_description'], true);
				$d['offer'] = $this->getOfferData($d['user_offer_id']);
				$d['offers'] = $this->getOfferDatas($d['ID']);

				$d['user_to'] = $users->get( array('user' => $d['user_to_id'], 'userby' => 'ID') );
				$d['user_from'] = $users->get( array('user' => $d['user_from_id'], 'userby' => 'ID') );
				$d['requested_dist'] = \Helper::distanceDate($d['requested']);

				$re[$d['my_relation']][$d['request_hashkey']]['services'][$d['servicegroup']]['serviceID'] = (int)$xserv[0];
				$re[$d['my_relation']][$d['request_hashkey']]['services'][$d['servicegroup']]['subserviceID'] = (int)$xserv[1];
				$re[$d['my_relation']][$d['request_hashkey']]['services'][$d['servicegroup']]['name'] = $d['servicegroup_name'];
				$re[$d['my_relation']][$d['request_hashkey']]['services'][$d['servicegroup']]['items'][$d['item_id']]['name'] = $d['item']['neve'];
				$re[$d['my_relation']][$d['request_hashkey']]['services'][$d['servicegroup']]['items'][$d['item_id']]['ID'] = (int)$d['item_id'];
				$re[$d['my_relation']][$d['request_hashkey']]['services'][$d['servicegroup']]['items'][$d['item_id']]['users'][] = $d;
				$re[$d['my_relation']][$d['request_hashkey']]['idopont'] = $d['requested'];
				$re[$d['my_relation']][$d['request_hashkey']]['hashkey'] = $d['request_hashkey'];
				$re[$d['my_relation']][$d['request_hashkey']]['user_name'] = $d['requester_form_name'];
			} else
			if( $format == 'list' )
			{

				$d['service'] = $this->findServicesItems((array)$xserv[0])[0];
				$d['subservice'] = $this->findServicesItems((array)$xserv[1])[0];
				$d['item'] = $this->findServicesItems((array)$d['item_id'])[0];
				$d['services_name'] = $d['service']['neve']. ' / '.$d['subservice']['neve']. ' / '.$d['item']['neve'];
				$d['cash'] = json_decode($d['cash'], true);
				$d['cash_config'] = json_decode($d['cash_config'], true);
				$d['user_to'] = $users->get( array('user' => $d['user_to_id'], 'userby' => 'ID', 'alerts' => false) );
				$d['user_from'] = $users->get( array('user' => $d['user_from_id'], 'userby' => 'ID', 'alerts' => false) );
				$re['data'][] = $d;
			}

		}

		if ($format == 'grouped') {
			// code...
		}

		$re['from_users_num'] = $from_users_num;
		$re['to_users_num'] = $to_users_num;
		$re['from_num'] = count($from_hashes);
		$re['to_num'] = count($to_hashes);
		$re['from_hashes'] = $from_hashes;
		$re['to_hashes'] = $to_hashes;

		return $re;
	}

	private function offerDatarowPreparer( &$row )
	{
		$users = new Users( array('db' => $this->db ));

		$row['ID'] = (int)$row['ID'];
		$row['from_user_id'] = (int)$row['from_user_id'];
		$row['from_user'] = $users->get( array('user' => (int)$row['from_user_id'], 'userby' => 'ID', 'alerts' => false) );
		$row['sended_at_dist'] = \Helper::distanceDate($row['sended_at']);

		return $row;
	}

	public function getOfferData( $id )
	{
		$list = array();
		$qarg = array();
		$q = "SELECT
			o.*
		FROM offers as o
		LEFT OUTER JOIN requests_offerouts as ro ON ro.ID = o.offerout_id
		WHERE 1=1 and o.ID = :id";

		$qarg['id'] = (int)$id;

		$data = $this->db->squery($q, $qarg);

		if ($data->rowCount() == 0) {
			return false;
		}

		$data = $data->fetch(\PDO::FETCH_ASSOC);

		$this->offerDatarowPreparer($data);

		return $data;
	}

	public function getServiceItemData( $id )
	{
		$top = $this->getCatData( $id );
		$parent = $top['szulo_id'];
		$parents = array();
		$fullname = '';

		while( $parent ) {
			$p = $this->getCatData( $parent );
			$parents[] = array(
				'ID' => $p['ID'],
				'neve' => $p['neve'],
				'szulo_id' => $p['szulo_id']
			);

			if ($p['szulo_id'] == '0') {
				$parent = false;
			} else {
				$parent = $p['szulo_id'];
			}
		}

		$parents = array_reverse($parents);

		foreach ((array)$parents as $pa) {
			$fullname .= $pa['neve'] . ' / ';
		}

		$fullname .= $top['neve'];

		$dat = array(
			'ID' => $top['ID'],
			'neve' => $top['neve'],
			'fullneve' => $fullname,
			'szulo_id' => $top['szulo_id'],
			'parents' => $parents
		);

		return $dat;
	}

	public function getOfferDatas( $request_id )
	{
		$list = array();
		$qarg = array();
		$q = "SELECT
			o.*
		FROM offers as o
		LEFT OUTER JOIN requests_offerouts as ro ON ro.ID = o.offerout_id
		WHERE
		1=1 and
		o.from_admin = 0 and
		ro.request_id = :oid";

		$q .= " ORDER BY o.accepted DESC, o.admin_offered_out DESC, o.admin_visited ASC, o.sended_at DESC";

		$qarg['oid'] = (int)$request_id;

		$data = $this->db->squery($q, $qarg);

		if ($data->rowCount() == 0) {
			return false;
		}

		$data = $data->fetchAll(\PDO::FETCH_ASSOC);

		foreach ((array)$data as $d) {
			$this->offerDatarowPreparer($d);
			$list[] = $d;
		}

		return $list;
	}

	public function getRequestOfferouts( $request_id )
	{
		$list = array();
		$qarg = array();
		$q = "SELECT
			ro.ID,
			ro.user_id,
			ro.offerout_at,
			ro.requester_accepted
		FROM requests_offerouts as ro
		WHERE 1=1 and ro.request_id = :rid";

		$qarg['rid'] = $request_id;

		$q .= " ORDER BY ro.offerout_at ASC";

		$data = $this->db->squery($q, $qarg);

		if ($data->rowCount() == 0) {
			return $list;
		}

		$data = $data->fetchAll(\PDO::FETCH_ASSOC);

		foreach ( (array)$data as $d )
		{
			$uid = (int)$d['user_id'];
			$d['offerout_at_dist'] = \Helper::distanceDate($d['offerout_at']);
			if (!in_array($uid, (array)$list['user_ids'])) {
				$list['user_ids'][] = $uid;
				$list['users'][$d['user_id']] = $d;
			}
			$list['data'][$d['ID']] = $d;
		}

		return $list;
	}

	public function pickOfferoutEmailStack( $limit = 20 )
	{
		$list = array();
		$qarg = array();
		$q = "SELECT
			e.ID,
			e.user_id,
			e.to_email,
			e.parameters,
			e.request_id,
			f.nev as cimzett_neve,
			fa.ertek as company_nev
		FROM requests_outgo_emails as e
		LEFT OUTER JOIN felhasznalok as f ON f.ID = e.user_id
		LEFT OUTER JOIN felhasznalo_adatok as fa ON fa.fiok_id = e.user_id and fa.nev = 'company_name'
		WHERE 1=1 and e.sended = 0 and e.cannot_send = 0 and f.mukodik = 1";

		$q .= " ORDER BY e.added_at ASC";
		$q .= " LIMIT 0,".$limit;

		$qry = $this->db->squery($q, $qarg);

		if ($qry->rowCount() == 0) {
			return $list;
		}

		$data = $qry->fetchAll(\PDO::FETCH_ASSOC);

		foreach ( (array)$data as $d ) {
			$d['parameters'] = json_decode($d['parameters'], true);
			$list[] = $d;
		}

		return $list;
	}

	public function possibleRequestServices( $services, $subservices, $items, $exclude_user = false )
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

		if ($exclude_user && !empty($exclude_user) ) {
			$q .= " and s.user_id != :uid";
			$qarg['uid'] = $exclude_user;
		}

		$qry = $this->db->squery($q, $qarg);

		if ($qry->rowCount() != 0) {
			$data = $qry->fetchAll(\PDO::FETCH_ASSOC);

			$in = array();
			foreach ((array)$data as $d)
			{
				if (!array_key_exists((int)$d['user_id'],	(array)$in['users']))
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
				$in['configval'] = $d['service_id'].'_'.$d['subservice_id'].'_'.$d['item_id'];

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

	public function sendServiceRequest( $request_hashkey = false, $tousers )
	{
		$to_servicers = array();
		$request_id = 0;

		/**
		* Ellenőrzés
		**/
		// hashkey check
		if ( empty($request_hashkey) || !$request_hashkey )
		{
			throw new \Exception(__("Az ajánlatkérés azonosítója nem lett megadva!"));
		}

		$ch = $this->db->squery("SELECT s.ID FROM requests as s WHERE hashkey = :hash", array('hash' => $request_hashkey ));
		if ( $ch->rowCount() == 0 )
		{
			throw new \Exception(__("Hibás ajánlatkérés azonosító! Nem létezik ilyen igénylés!"));
		}
		$request_id = (int)$ch->fetchColumn();

		// prepare users
		if ($tousers)
		{
			foreach ( (array)$tousers as $u )
			{
				// user check
				$uch = $this->db->squery("SELECT ro.ID FROM requests_offerouts as ro WHERE ro.user_id = :uid and ro.request_id = :rid", array('uid' => $u, 'rid' => $request_id));

				if ($uch->rowCount() == 0) {
					$to_servicers[] = (int)$u;
				}
			}
		}

		/**
		* Kiajánlások rögzítése @ requests_offerouts
		**/
		if ( $to_servicers )
		{
			$outgo_emails = array();
			foreach ( (array)$to_servicers as $u )
			{
				$user_email = $this->db->squery("SELECT email FROM felhasznalok WHERE ID = :id", array('id' => $u))->fetchColumn();
				$this->db->insert(
					"requests_offerouts",
					array(
						'user_id' => (int)$u,
						'request_id' => $request_id
					)
				);

				$offerout_id = $this->db->lastInsertId();
				$outgo_emails[$u]['email'] = $user_email;
				$outgo_emails[$u]['ID'] = $u;
				$outgo_emails[$u]['stack'][] = array(
					'ID' => $offerout_id
				);
			}

			/**
			* Kiajánló e-mailek várólistára helyezése
			**/
			$r = array();
			if ($outgo_emails)
			{
				foreach ( (array)$outgo_emails as $uid => $out )
				{
					$r['users'][] = $uid;

					$check = $this->db->squery("SELECT ro.ID FROM requests_outgo_emails as ro WHERE ro.user_id = :uid and ro.request_id = :rid", array('uid' => $out['ID'], 'rid' => $request_id));
					if ($check->rowCount() == 0)
					{
						$paramters = array();
						$paramters['offers'] = $out['stack'];
						$rout = array(
							'user_id' => (int)$uid,
							'request_id' => $request_id,
							'parameters' => json_encode($paramters, \JSON_UNESCAPED_UNICODE),
							'to_email' => $out['email']
						);
						$this->db->insert(
							"requests_outgo_emails",
							$rout
						);
					}
				}
			}
		}

		/**
		* Kiküldés logolása a requestnél
		**/
		$upd = array();
		$upd['offerout'] = 1;

		$visited = $this->db->squery("SELECT ID FROM requests WHERE hashkey = :hash and visited = 1", array('hash' => $request_hashkey))->rowCount();

		if ($visited == 0) {
			$upd['visited'] = 1;
			$upd['visited_at'] = NOW;
		}

		$this->db->update(
			"requests",
			$upd,
			sprintf("hashkey = '%s'", $request_hashkey)
		);

		return $r;
	}

	public function setRequestOfferData( $request_id = 0, $field, $value )
	{
		$accepted_fields = array('recepient_visited_at','recepient_declined','recepient_accepted');
		if (empty($request_id) || $request_id == 0)
		{
			throw new \Exception(__('Hiányzó ajánlat kérés azonosító!'));
		}

		if ( !in_array($field, (array)$accepted_fields) )
		{
				throw new \Exception(sprintf(__('Hibás vagy nem engedélyezett műveletet próbál végrehajtani az ajánlat kérésen: %s (field)'), $field));
		}

		$update[$field] = $value;

		$this->db->update(
			"requests_offerouts",
			$update,
			sprintf("ID = %d", $request_id)
		);

		return true;
	}

	public function setRequestData( $request_id = 0, $field, $value )
	{
		$accepted_fields = array('visited', 'visited_at', 'elutasitva', 'offerout');
		if (empty($request_id) || $request_id == 0)
		{
			throw new \Exception(__('Hiányzó ajánlat kérés azonosító!'));
		}

		if ( !in_array($field, (array)$accepted_fields) )
		{
				throw new \Exception(sprintf(__('Hibás vagy nem engedélyezett műveletet próbál végrehajtani az ajánlat kérésen: %s (field)'), $field));
		}

		$update[$field] = $value;

		$this->db->update(
			"requests",
			$update,
			sprintf("ID = %d", $request_id)
		);

		return true;
	}

	public function getConfigvalUserByRequest( $item_id, $uid )
	{
		$q = $this->db->squery("SELECT fs.configval FROM felhasznalo_services as fs WHERE fs.user_id = :uid and fs.item_id = :item", array('uid' => $uid, 'item' => $item_id));
		if ($q->rowCount() == 0) {
			return false;
		} else {
			return $q->fetchColumn();
		}
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

	public function findServicesItems( $ids = array() )
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
					'user_requester_title' => (empty($requester['requester_title'])) ? NULL : addslashes(trim($requester['requester_title'])),
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
