<?
namespace PortalManager;

use MailManager\Mailer;
use MailManager\MailTemplates;
use PortalManager\Users;
use PortalManager\Documents;
use PortalManager\Template;
use PortalManager\OfferRequests;


/**
* class Projects
* @package PortalManager
* @version 1.0
*/
class Projects
{
	const DBPROJECTS = 'projects';

	private $db = null;

	function __construct( $arg = array() )
	{
		$this->db = $arg[db];
		return $this;
	}

	public function createByOffer( $request_id, $offer )
	{
		$requests = new OfferRequests( array('db' => $this->db) );
		$order_hash = md5(uniqid());

		// ajánlatkérés
		$request = $requests->getList(array(
			'ids' => array($request_id),
			'bindIDToList' => 1
		));
		$request = $request[$request_id];

		// létrehozás - megrendelő
		$megrendelo_hashkey = md5(uniqid());
		$this->db->insert(
			self::DBPROJECTS,
			array(
				'primary_user_id' => $request['user_id'],
				'hashkey' => $megrendelo_hashkey,
				'order_hashkey' => $order_hash,
				'admin_title' => ($offer['admin_title'] == '') ? NULL : addslashes($offer['admin_title']),
				'request_id' => $request_id,
				'offer_id' => $offer['ID'],
				'requester_id' => $request['user_id'],
				'servicer_id' => $offer['from_user_id'],
			)
		);
		$megrendelo_project_id = $this->db->lastInsertId();

		// létrehozás - megrendelő
		$szolgaltato_hashkey = md5(uniqid());
		$this->db->insert(
			self::DBPROJECTS,
			array(
				'primary_user_id' => $offer['from_user_id'],
				'hashkey' => $szolgaltato_hashkey,
				'order_hashkey' => $order_hash,
				'admin_title' => ($offer['admin_title'] == '') ? NULL : addslashes($offer['admin_title']),
				'request_id' => $request_id,
				'offer_id' => $offer['ID'],
				'requester_id' => $request['user_id'],
				'servicer_id' => $offer['from_user_id'],
			)
		);
		$szolgaltato_project_id = $this->db->lastInsertId();

		// project id-k mentése
		if ($megrendelo_project_id) {
			$this->db->update(
				'requests',
				array(
					'project_id' => $megrendelo_project_id
				),
				sprintf("ID = %d", (int)$request_id)
			);
			$this->db->update(
				'offers',
				array(
					'project_id' => $megrendelo_project_id
				),
				sprintf("ID = %d", (int)$request['admin_offer_id'])
			);
		}

		if ($szolgaltato_project_id) {
			$this->db->update(
				'offers',
				array(
					'accepted' => 1,
					'accepted_at' => NOW,
					'project_id' => $szolgaltato_project_id
				),
				sprintf("ID = %d", (int)$offer['ID'])
			);
		}

		// offerout project id
		$this->db->update(
			'requests_offerouts',
			array(
				'requester_accepted' => 1,
				'project_id' => $szolgaltato_project_id
			),
			sprintf("ID = %d", (int)$offer['offerout_id'])
		);

	}

	public function getProjectData( $key, $user_id = false  )
	{
		$arg = array();
		$where = '';

		$uid = 0;
		$users = new Users( array('db' => $this->db ));

		if ( $user_id ) {
			$uid = $user_id;
			$controll_user =  $users->get( array('user' => $uid, 'userby' => 'ID', 'alerts' => false) );
			$controll_user_admin = ($controll_user['data']['user_group'] == \PortalManager\Users::USERGROUP_SUPERADMIN || $controll_user['data']['user_group'] == \PortalManager\Users::USERGROUP_ADMIN) ? true : false;
		}

		if (is_numeric($key)) {
			$where = 'p.ID = :id';
			$arg['id'] = $key;
		} else {
			$where = ' (p.hashkey = :hash or p.order_hashkey = :hash)';
			$arg['hash'] = $key;
		}

		$db = $this->db->squery($iq = "SELECT p.* FROM ".self::DBPROJECTS." as p WHERE ".$where, $arg);

		if ($db->rowCount() == 0) {
			return false;
		} else {
			$d = $db->fetch(\PDO::FETCH_ASSOC);

			if ($controll_user_admin) {
				$d['my_relation'] = 'admin';
			} else {
				$d['my_relation'] = ($uid == $d['requester_id']) ? 'requester': 'servicer';
			}

			$d['title'] = $d[$d['my_relation'].'_title'];

			// Admin esetében teendők
			if ($d['my_relation'] == 'admin')
			{
				$d['user_requester'] = $users->get( array('user' => $d['requester_id'], 'userby' => 'ID', 'alerts' => false) );
				$d['user_servicer'] = $users->get( array('user' => $d['servicer_id'], 'userby' => 'ID', 'alerts' => false) );

				if (!empty($d['offer_id'])) {
					$d['offer_data'] =  $this->getOffer( $d['offer_id'] );
				}

				$title = '';
				$title .= __('Project hash').': '.$d['hashkey']."<br>";
				$title .= __('Ajánlatkérő')." (".$d['user_requester']['data']['nev'].")".': ';

				if ($d['requester_title'] != '') {
					$title .= $d['requester_title'];
				} else {
					$title .= '<u><em>'.__('a projektet nem nevezte el').'</em></u>';
				}
				$title .= '<br>'.__('Szolgáltató')." (".$d['user_servicer']['data']['nev'].")".': ';

				if ($d['servicer_title'] != '') {
					$title .= $d['servicer_title'];
				} else {
					$title .= '<u><em>'.__('a projektet nem nevezte el').'</em></u>';
				}

				$order_project_hashkeys = $this->getOrderProjectHashkeys( $d['order_hashkey'], 'ID' );
				$d['order_project_hashkeys'] = $order_project_hashkeys;

				$d['title'] = $title;
			} else {
				$order_project_hashkeys = $this->getOrderProjectHashkeys( $d['order_hashkey'], 'hashkey' );
				$d['order_project_hashkeys'] = $order_project_hashkeys;
				$order_project_hashkeys = $this->getOrderProjectHashkeys( $d['order_hashkey'], 'ID' );
				$d['order_project_ids'] = $order_project_hashkeys;
			}

			return $d;
		}
	}

	public function getList( $arg = array() )
	{
		$list = array();
		$qarg = array();

		$uid = (int)$arg['uid'];

		if ($uid != 0) {
			$users = new Users( array('db' => $this->db ));
			$controll_user =  $users->get( array('user' => $uid, 'userby' => 'ID', 'alerts' => false) );
			$controll_user_admin = ($controll_user['data']['user_group'] == \PortalManager\Users::USERGROUP_SUPERADMIN || $controll_user['data']['user_group'] == \PortalManager\Users::USERGROUP_ADMIN) ? true : false;
		}

		$q = "SELECT
			p.*,
			r.requested as requested_at,
			r.visited_at as request_admin_visited_at,
			(SELECT ro.offerout_at FROM requests_offerouts as ro WHERE ro.request_id GROUP BY ro.request_id LIMIT 0,1) as admin_offerout_at
		FROM projects as p
		LEFT OUTER JOIN requests as r ON r.ID = p.request_id
		WHERE 1=1 ";

		if (isset($arg['getproject'])) {
			$q .= " and (p.hashkey = :getproject or p.order_hashkey = :getproject)";
			$qarg['getproject'] =$arg['getproject'];
		}

		if (isset($arg['getprojectbyid'])) {
			$q .= " and p.ID  = :getprojectbyid";
			$qarg['getprojectbyid'] =$arg['getprojectbyid'];
		}

    if (isset($arg['hashkey'])) {
			$q .= " and p.hashkey  = :hashkey";
			$qarg['hashkey'] =$arg['hashkey'];
		}

		if ( isset($arg['ids']) && !empty($arg['ids']) ) {
			$q .= " and FIND_IN_SET(p.ID, :idslist)";
			$qarg['idslist'] = implode(",", (array)$arg['ids']);
		}

		if (isset($arg['closed'])) {
			$q .= " and p.closed  = :closed";
			$qarg['closed'] = (int)$arg['closed'];
		}

		if (isset($arg['uid']) && !$controll_user_admin) {
			$q .= " and (p.requester_id = :uid or p.servicer_id = :uid) and p.primary_user_id = :uid";
			$qarg['uid'] = (int)$arg['uid'];
		}

		if ( $controll_user_admin ) {
			$q .= " GROUP BY p.order_hashkey ";
		}

		$q .= " ORDER BY p.created_at DESC";

		$data = $this->db->squery($q, $qarg);
		if ($data->rowCount() == 0) {
			return $list;
		}

		$data = $data->fetchAll(\PDO::FETCH_ASSOC);

		foreach ((array)$data as $d)
		{
			$d['closed'] = (int)$d['closed'];
			$timeline = array();

			if ($controll_user_admin) {
				$d['my_relation'] = 'admin';
				$d['title'] = $d['admin_title'];
			} else {
				$d['my_relation'] = ($uid == $d['requester_id']) ? 'requester': 'servicer';
				$d['title'] = $d[$d['my_relation'].'_title'];
			}

			$d['title'] = $d[$d['my_relation'].'_title'];
			$d['created_dist'] = \Helper::distanceDate($d['created_at']);
			$d['user_requester'] = $users->get( array('user' => $d['requester_id'], 'userby' => 'ID', 'alerts' => false) );
			$d['user_servicer'] = $users->get( array('user' => $d['servicer_id'], 'userby' => 'ID', 'alerts' => false) );
			$d['partner'] = ($d['my_relation'] == 'requester') ? $d['user_servicer'] : $d['user_requester'];
			$d['offer'] = $this->getOffer( $d['offer_id'] );
			$d['status_percent'] = $this->getProjectProgress( $d );
			$d['status_percent_class'] = \Helper::progressBarColor($d['status_percent']);

			if (isset($arg['show_messages'])) {
				$d['messages'] = $this->getProjectMessagesInfo( $d['order_hashkey'], $uid, $d['my_relation'] );
			}

			if (isset($arg['show_request_data'])) {
				$d['request_data'] = $this->getRequestData( $d['request_id'] );
			}

			if ($d['my_relation'] == 'admin') {
				if ($d['admin_title'] != '') {
					$title = $d['admin_title'] . "<br>";
				} else {
					$title = 'Nincs elnevezve &mdash; #'.$d['order_hashkey'] . "<br>";
				}
				$d['title'] = $title;
			} else {
				if ($d['title'] == '') {
					$title = 'Nincs elnevezve &mdash; #'.$d['order_hashkey'] ."<br>";
					$d['title'] = $title;
				}
			}

			$order_project_hashkeys = $this->getOrderProjectHashkeys( $d['order_hashkey'] );
			$d['order_project_hashkeys'] = $order_project_hashkeys;

			if ($order_project_hashkeys) {
				foreach ((array)$order_project_hashkeys as $rel => $hash) {
					$d[$rel.'_project_data'] = $this->getProjectData( $hash, $uid, $d['my_relation'] );
				}
			}
			$d['requester_paying_percent'] = $this->getProjectPaymentProgress( $d['requester_project_data']['ID'], 'requester' );
			$d['requester_paying_percent_class'] = \Helper::progressBarColor($d['requester_paying_percent']);
			$d['servicer_paying_percent'] = $this->getProjectPaymentProgress( $d['servicer_project_data']['ID'], 'servicer' );
			$d['servicer_paying_percent_class'] = \Helper::progressBarColor($d['servicer_paying_percent']);

			$d['requester_paidamount'] = $this->getProjectPaidAmount( $d['requester_project_data']['ID'], 'requester' );
			$d['servicer_paidamount'] = $this->getProjectPaidAmount( $d['servicer_project_data']['ID'], 'servicer' );

			// Timeline
			if ($controll_user_admin)
			{
				$timeline[] = array('title' => __('Ajánlatkérő ajánlatkérésének ideje'), 'time' => $d['requested_at'] );
				$timeline[] = array('title' => __('Admin megtekintette az ajánlatkérést'), 'time' => $d['request_admin_visited_at'] );
				$timeline[] = array('title' => __('Az ajánlatkérés kiajánlva a szolgáltatók felé'), 'time' => $d['admin_offerout_at'] );
				$timeline[] = array('title' => __('Szolgáltatói ajánlat beérkezésének ideje'), 'time' => $d['servicer_project_data']['offer_data']['sended_at'] );
				$timeline[] = array('title' => __('Közvetítői ajánlatkiküldés az ajánlatkérő felé'), 'time' => $d['requester_project_data']['offer_data']['sended_at'] );
				$timeline[] = array('title' => __('Ajánlatkérő (igénylő) elfogadta a közvetítői ajánlatot'), 'time' => $d['requester_project_data']['offer_data']['accepted_at'] );
				$timeline[] = array('title' => __('Közvetítés létrejött: a projektek elindultak!'), 'time' => $d['created_at'] );


				$d['timeline'] = $timeline;
			}

			$list[] = $d;
		}

		if (isset($arg['getproject'])) {
			$list = $list[0];
		}

		return $list;
	}

	public function getRequestData( $id )
	{
		$data = array();

		$qry = "SELECT
			r.*
		FROM requests as r
		WHERE 1=1 and r.ID = :id";

		$qry = $this->db->squery( $qry, array('id' => (int)$id));

		if ($qry->rowCount() == 0) {
			return $data;
		}

		$d = $qry->fetch( \PDO::FETCH_ASSOC );

		$d['cash'] = json_decode($d['cash'], true);
		$d['cash_config'] = json_decode($d['cash_config'], true);
		$d['services'] = $this->findServicesItems(json_decode($d['services'], true));
		$d['subservices'] = $this->findServicesItems(json_decode($d['subservices'], true));
		$d['subservices_items'] = $this->findServicesItems(json_decode($d['subservices_items'], true));
		$d['service_description'] = json_decode($d['service_description'], true);

		$data = $d;

		return $data;
	}

	private function findServicesItems( $ids = array() )
	{
		$or = new OfferRequests(array('db' => $this->db));
		$services = $or->findServicesItems( $ids );
		unset($or);
		return $services;
	}

	public function getOrderProjectHashkeys( $hashkey = '', $return_val = 'hashkey' )
	{
		$arr = array();
		$hashes = $this->db->squery("SELECT ID, hashkey, IF(primary_user_id = requester_id, 'requester', 'servicer') as relations FROM projects WHERE order_hashkey = :hash", array('hash' => $hashkey))->fetchAll(\PDO::FETCH_ASSOC);

		foreach ((array)$hashes as $h) {
			$arr[$h['relations']] = $h[$return_val];
		}

		return $arr;
	}

	public function addDocument( $hashkey, $doc_id, $adder_user_id )
	{
		$project = $this->db->squery("SELECT ID, IF(primary_user_id = requester_id, 'requester', 'servicer') as relations FROM projects WHERE hashkey = :hash", array('hash' => $hashkey))->fetch(\PDO::FETCH_ASSOC);

		$project_id = (int)$project['ID'];
		$relation = $project['relations'];

		$check = $this->db->squery("SELECT ID FROM ".\PortalManager\Documents::DBXREF_PROJECT." WHERE project_id = :pid and doc_id = :did", array('pid' => $project_id, 'did' => $doc_id));

		if ($check->rowCount() == 0) {
			// register
			$this->db->insert(
				\PortalManager\Documents::DBXREF_PROJECT,
				array(
					'doc_id' => $doc_id,
					'project_id' => $project_id,
					'adder_user_id' => $adder_user_id
				)
			);

			$xrefid = $this->db->lastInsertId();

			// get partner id from project
			$partner_id = false;
			$partner_relation = 'admin';
			$project = $this->db->squery("SELECT
				p.order_hashkey,
				p.requester_id,
				p.primary_user_id,
				p.servicer_id,
				p.requester_title,
				p.servicer_title,
				p.admin_title
			FROM ".self::DBPROJECTS." as p
			WHERE p.ID = :pid", array('pid' => $project_id));
			$projectdata = $project->fetch(\PDO::FETCH_ASSOC);
			$partner_id = $projectdata['primary_user_id'];

			if ( $projectdata['requester_id'] == $adder_user_id )
			{
				$partner_id = $projectdata['primary_user_id'];
				$partner_relation = 'requester';
			}
			else if( $projectdata['servicer_id'] == $adder_user_id )
			{
				$partner_id = $projectdata['primary_user_id'];
				$partner_relation = 'servicer';
			}

			// Ha felhasználó, akkor nincs partner id, mivel az admint kell értesíteni
			if ( $partner_relation != 'admin' ) {
				$partner_id = 0;
			}

			// update partner id on xref
			$this->db->update(
				\PortalManager\Documents::DBXREF_PROJECT,
				array(
					'partner_id' => $partner_id,
					'adder_relation' => $partner_relation
				),
				sprintf("ID = %d", (int)$xrefid)
			);

			$docs = new Documents(array('db' => $this->db));
			$arg = array();
			$arg['uid'] = $partner_id;
			$arg['getid'] = $doc_id;
			$doc = $docs->getList( $arg );

			// email alert
			if ($partner_relation == 'admin')
			{
				// Ha felhasználót kell értesíteni
				$users = new Users(array('db' => $this->db));
				$partner_user = $users->get( array('user' => $partner_id, 'userby' => 'ID') );

				if (true)
				{
					$partner_relation = ($partner_id == $projectdata['requester_id']) ? 'requester' : 'servicer';
					$mail = new Mailer( $this->db->settings['page_title'], SMTP_USER, $this->db->settings['mail_sender_mode'] );
					$mail->add( trim($partner_user['data']['email']) );
					$projekt_name = (($projectdata[$partner_relation.'_title'] != '') ? $projectdata[$partner_relation.'_title'] : __('- nincs név -'));
					$arg = array(
						'nev' => trim($partner_user['data']['nev']),
						'project_nev' => $projekt_name,
						'doc_hashkey' => $doc['hashkey'],
						'doc_nev' => $doc['name'],
						'doc_tipus' => $doc['folders'][0]['folder_name'],
						'doc_ertek' => (($doc['ertek'] != '0') ? __('Érték / Összeg').': <strong>'.\Helper::cashFormat($doc['ertek']).' '.__('Ft + ÁFA').'</strong>' : '') ,
						'doc_hatarido' => ((!empty($doc['expire_at'])) ? __('Határidő').': <strong>'.$doc['expire_at'].'</strong>' : '') ,
						'doc_teljesitve' => ((!empty($doc['teljesites_at'])) ? __('Teljesítés idelye').': <strong>'.$doc['teljesites_at'].'</strong>' : '') ,
						'settings' => $this->db->settings,
						'infoMsg' => 'Ezt az üzenetet a rendszer küldte. Kérjük, hogy ne válaszoljon rá!'
					);
					$arg['mailtemplate'] = (new MailTemplates(array('db'=>$this->db)))->get('projects_documents_add_'.$partner_relation, $arg);
					$mail->setSubject( __('Új dokumentuma érkezett: ').$projekt_name );
					$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'clearmail', $arg ) );
					$re = $mail->sendMail();
				}
			} else {
				if (true)
				{
					$mail = new Mailer( $this->db->settings['page_title'], SMTP_USER, $this->db->settings['mail_sender_mode'] );
					$mail->add( $this->db->settings['alert_email'] );
					$arg = array(
						'order_hashkey' => $projectdata['order_hashkey'],
						'project_nev' => (($projectdata['admin_title'] != '') ? $projectdata['admin_title'] : __('- nincs név -')),
						'doc_hashkey' => $doc['hashkey'],
						'doc_nev' => $doc['name'],
						'doc_tipus' => $doc['folders'][0]['folder_name'],
						'doc_ertek' => (($doc['ertek'] != '0') ? __('Érték / Összeg').': <strong>'.\Helper::cashFormat($doc['ertek']).' '.__('Ft + ÁFA').'</strong>' : '') ,
						'doc_hatarido' => ((!empty($doc['expire_at'])) ? __('Határidő').': <strong>'.$doc['expire_at'].'</strong>' : '') ,
						'doc_teljesitve' => ((!empty($doc['teljesites_at'])) ? __('Teljesítés idelye').': <strong>'.$doc['teljesites_at'].'</strong>' : '') ,
						'settings' => $this->db->settings,
						'infoMsg' => 'Ezt az üzenetet a rendszer küldte. Kérjük, hogy ne válaszoljon rá!'
					);
					$arg['mailtemplate'] = (new MailTemplates(array('db'=>$this->db)))->get('projects_documents_add_admin', $arg);
					$ufrom = ($partner_relation == 'requester') ? __('ajánlatkérőtől') : __('szolgáltatótól');
					$mail->setSubject( __('Új dokumentuma érkezett a(z) '.$ufrom.': '.$projectdata['admin_title']) );
					$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'clearmail', $arg ) );
					$re = $mail->sendMail();
					error_log(print_r($re, true));
				}
			}

		} else{
			throw new \Exception(__("A dokumentum korábban már be lett csatolva."));
		}
	}

	public function userModifyProject( $project, $uid = false )
	{
		$updates = array();
		$relation = $project['my_relation'];

		if ($relation == 'admin') {
			$requester_title = trim($project['requester_title']);
			$updates['requester_title'] = ($requester_title == '') ? NULL : $requester_title;

			$servicer_title = trim($project['servicer_title']);
			$updates['servicer_title'] = ($servicer_title == '') ? NULL : $servicer_title;
		}

		$status_percent = (float)$project['status_percent'];
		$updates['status_percent'] = $status_percent;

		$closed = (int)$project['closed'];
		$updates['closed'] = $closed;

		$project_start = $project['project_start'];
		$updates['project_start'] = $project_start;

		$project_end = $project['project_end'];
		$updates['project_end'] = $project_end;

		if ($closed == 1) {
			$check_close = (int)$this->db->squery("SELECT closed FROM projects WHERE hashkey = :hash", array('hash' => $project['hashkey']))->fetchColumn();

			if ($check_close == 0) {
				$updates['closed_by'] = $uid;

				// Messanger close
				$this->db->update(
					\MessageManager\Messanger::DBTABLE,
					array(
						'closed' => 1,
						'closed_at' => NOW
					),
					sprintf("sessionid = '%s' and closed = 0", $project['hashkey'])
				);

				// Push system message
				$this->db->insert(
					\MessageManager\Messanger::DBTABLE_MESSAGES,
					array(
						'sessionid' => $project['hashkey'],
						'message' => __('Az üzenetváltás lezárásra került a projekt inaktív állapot változása végett.'),
						'user_from_id' => 0,
						'user_to_id' => 0,
						'admin_alerted' => 1,
						'partner_alerted' => 1
					)
				);
			}
		} else {
			$check_close = (int)$this->db->squery("SELECT closed FROM projects WHERE hashkey = :hash", array('hash' => $project['hashkey']))->fetchColumn();
			$updates['closed_by'] = NULL;
			if ($check_close == 1) {
				// Messanger close
				$this->db->update(
					\MessageManager\Messanger::DBTABLE,
					array(
						'closed' => 0,
						'closed_at' => NULL
					),
					sprintf("sessionid = '%s' and closed = 1", $project['hashkey'])
				);

				// Push system message
				$this->db->insert(
					\MessageManager\Messanger::DBTABLE_MESSAGES,
					array(
						'sessionid' => $project['hashkey'],
						'message' => __('Az üzenetváltás megnyitásra került a projekt aktív állapot változása végett.'),
						'user_from_id' => 0,
						'user_to_id' => 0,
						'admin_alerted' => 1,
						'partner_alerted' => 1
					)
				);
			}
		}

		if ($relation != 'admin') {
			$title = trim($project['title']);
			$updates[$relation.'_title'] = ($title == '') ? NULL : $title;
		}

		if (!empty($updates)) {
			$this->db->update(
				'projects',
				$updates,
				sprintf("order_hashkey = '%s'", $project['order_hashkey'])
			);

			return true;
		}

		return false;
	}

	public function validateProjectPermission( $hashkey, $user_id )
	{
		// user check
		$user = $this->db->squery("SELECT user_group, engedelyezve FROM felhasznalok WHERE ID = :uid", array('uid' => $user_id));

		if ($user->rowCount() == 0) {
			return false;
		}

		$user_data = $user->fetch(\PDO::FETCH_ASSOC);


		// Ha admin jog, akkor eléri
		if ( $user_data['user_group'] == \PortalManager\Users::USERGROUP_ADMIN || $user_data['user_group'] == \PortalManager\Users::USERGROUP_SUPERADMIN )
		{
			return true;
		}
		// Ha nem admin jog, akkor ellenőrzés
		else
		{
			// user restrict - nem engedélyezett / letiltott felhasználó nem érheti el
			if ($user_data && $user_data['engedelyezve'] == 0) {
				return false;
			}
			// project check
			$qry = $this->db->squery("SELECT ID FROM projects WHERE hashkey = :hash and (requester_id = :uid or servicer_id = :uid)", array('hash' => $hashkey, 'uid' => $user_id));
			if ($qry->rowCount() == 0) {
				return false;
			} else {
				return true;
			}
		}
	}

	// TODO: Megcsinálni
	public function getProjectMessagesInfo( $order_hashkey, $uid, $relation )
	{
		$ret = array(
			'servicer' => array(
				'unreaded' => 0,
				'closed' => false
			),
			'requester' => array(
				'unreaded' => 0,
				'closed' => false
			)
		);

		$row = array('servicer', 'requester');

		$sessions = $this->getOrderProjectHashkeys( $order_hashkey );
		$projectdata = $this->getProjectData( $order_hashkey, $uid);

		foreach ( (array)$row as $r )
		{
			$session = $sessions[$r];
			$message = $this->db->squery("SELECT m.closed, m.closed_at FROM ".\MessageManager\Messanger::DBTABLE." as m WHERE m.sessionid = :session", array('session' => $session))->fetch(\PDO::FETCH_ASSOC);
			if ($message && $message['closed']) {
				$ret[$r]['closed'] = $message['closed_at'];
			}
			if ($relation == 'admin') {
				$qry = "SELECT COUNT(ms.ID) FROM ".\MessageManager\Messanger::DBTABLE_MESSAGES." as ms WHERE ms.sessionid = :session and (ms.user_to_id = 0 and ms.user_from_id != 0) and ms.admin_readed_at IS NULL";
				$qry = $this->db->squery( $qry, array('session' => $session));
				$num = 0;
				if ($qry->rowCount() != 0) {
					$num = (int)$qry->fetchColumn();
				}
				$ret[$r]['unreaded']= $num;
			} else {
				if ($relation == $r) {
					$qry = "SELECT COUNT(ms.ID) FROM ".\MessageManager\Messanger::DBTABLE_MESSAGES." as ms WHERE ms.sessionid = :session and (ms.user_from_id != 0 and ms.user_to_id = :uid) and ms.user_from_id != :uid and ms.user_readed_at IS NULL";
					$qry = $this->db->squery( $qry, array('session' => $session, 'uid' => $uid));
					$num = 0;
					if ($qry->rowCount() != 0) {
						$num = (int)$qry->fetchColumn();
					}
					$ret[$r]['unreaded']= $num;
				}
			}
		}

		return $ret;
	}

	public function getProjectPaidAmount( $project_id, $relation )
	{
		$qry = "SELECT
			SUM(d.ertek)
		FROM ".\PortalManager\Documents::DBXREF_PROJECT." as xp
		LEFT OUTER JOIN ".\PortalManager\Documents::DBTABLE." as d ON d.ID = xp.doc_id
		WHERE
			xp.project_id = :project and
			d.teljesites_at IS NOT NULL and
			3 IN (SELECT folder_id FROM ".\PortalManager\Documents::DBXREF_FOLDER."  WHERE doc_id = xp.doc_id)";

		if ($relation == 'servicer') {
			$qry .= " and xp.adder_relation = 'servicer'";
		} else {
			$qry .= " and xp.adder_relation = 'admin'";
		}
		$paid_amount = (float)$this->db->squery( $qry ,
		array('project' => $project_id))->fetchColumn();

		return $paid_amount;
	}

	public function getProjectProgress( $project_raw )
	{
		$p = (int)$project_raw['status_percent'];
		return ($p <= 100) ? $p : 100;
	}

	public function getProjectPaymentProgress( $project_id, $relation = false )
	{
		$paid_amount = $this->getProjectPaidAmount( $project_id, $relation );

		$project = $this->getProjectData( $project_id );
		$offer_id = $project['offer_id'];
		$offer = $this->getOffer( $offer_id );

		$offer_price  = (float)$offer['price'];

		$amount = $paid_amount / $offer_price * 100;

		$amount = number_format($amount, 2, ".", "");

		return $amount;
	}

	public function getOffer( $id)
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

	private function offerDatarowPreparer( &$row )
	{
		$row['ID'] = (int)$row['ID'];
		$row['from_user_id'] = (int)$row['from_user_id'];
		$row['message'] = nl2br($row['message']);
		$row['accepted_at_dist'] = \Helper::distanceDate($row['accepted_at']);
		$row['sended_at_dist'] = \Helper::distanceDate($row['sended_at']);

		return $row;
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


	public function __destruct()
	{
		$this->db = null;
	}

}
?>
