<?
namespace PortalManager;

use MailManager\Mailer;
use MailManager\MailTemplates;
use PortalManager\Users;
use PortalManager\Documents;
use PortalManager\Template;

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

	public function getProjectData( $key, $user_id = false  )
	{
		$arg = array();
		$where = '';

		$uid = 0;

		if ( $user_id ) {
			$uid = $user_id;
			$users = new Users( array('db' => $this->db ));
			$controll_user =  $users->get( array('user' => $uid, 'userby' => 'ID', 'alerts' => false) );
			$controll_user_admin = ($controll_user['data']['user_group'] == \PortalManager\Users::USERGROUP_SUPERADMIN || $controll_user['data']['user_group'] == \PortalManager\Users::USERGROUP_ADMIN) ? true : false;
		}

		if (is_numeric($key)) {
			$where = 'p.ID = :id';
			$arg['id'] = $key;
		} else {
			$where = 'p.hashkey = :hash';
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

			return $d;
		}
	}

	public function getList( $arg = array() )
	{
		$list = array();
		$qarg = array();

		$uid = (int)$arg['uid'];
		$users = new Users( array('db' => $this->db ));
		$controll_user =  $users->get( array('user' => $uid, 'userby' => 'ID', 'alerts' => false) );
		$controll_user_admin = ($controll_user['data']['user_group'] == \PortalManager\Users::USERGROUP_SUPERADMIN || $controll_user['data']['user_group'] == \PortalManager\Users::USERGROUP_ADMIN) ? true : false;

		$q = "SELECT
			p.*
		FROM projects as p
		WHERE 1=1 ";

		if (isset($arg['getproject'])) {
			$q .= " and p.hashkey  = :getproject";
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

		if (isset($arg['uid'])) {
			$q .= " and (p.requester_id = :uid or p.servicer_id = :uid)";
			$qarg['uid'] = (int)$arg['uid'];
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
			if ($controll_user_admin) {
				$d['my_relation'] = 'admin';
			} else {
				$d['my_relation'] = ($uid == $d['requester_id']) ? 'requester': 'servicer';
			}

			$d['title'] = $d[$d['my_relation'].'_title'];
			$d['created_dist'] = \Helper::distanceDate($d['created_at']);
			$d['user_requester'] = $users->get( array('user' => $d['requester_id'], 'userby' => 'ID', 'alerts' => false) );
			$d['user_servicer'] = $users->get( array('user' => $d['servicer_id'], 'userby' => 'ID', 'alerts' => false) );
			$d['partner'] = ($d['my_relation'] == 'requester') ? $d['user_servicer'] : $d['user_requester'];
			$d['offer'] = $this->getOffer( $d['offer_id'] );
			$d['paidamount'] = $this->getProjectPaidAmount( $d['ID'] );
			$d['status_percent'] = $this->getProjectProgress( $d );
			$d['status_percent_class'] = \Helper::progressBarColor($d['status_percent']);
			$d['paying_percent'] = $this->getProjectPaymentProgress( $d['ID'] );
			$d['paying_percent_class'] = \Helper::progressBarColor($d['paying_percent']);
			$d['messages'] = $this->getProjectMessagesInfo( $d['ID'], $uid, $d['my_relation'] );

			$list[] = $d;
		}

		if (isset($arg['getproject'])) {
			$list = $list[0];
		}

		return $list;
	}

	public function addDocument( $project_id, $doc_id, $adder_user_id )
	{
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
			$partner_relation = false;
			$project = $this->db->squery("SELECT p.requester_id, p.servicer_id, p.requester_title, p.servicer_title FROM ".self::DBPROJECTS." as p WHERE p.ID = :pid", array('pid' => $project_id));
			$projectdata = $project->fetch(\PDO::FETCH_ASSOC);

			if ( $projectdata['requester_id'] == $adder_user_id )
			{
				$partner_id = $projectdata['servicer_id'];
				$partner_relation = 'servicer';
			}
			else if( $projectdata['servicer_id'] == $adder_user_id )
			{
				$partner_id = $projectdata['requester_id'];
				$partner_relation = 'requester';
			}

			// update partner id on xref
			$this->db->update(
				\PortalManager\Documents::DBXREF_PROJECT,
				array(
					'partner_id' => $partner_id,
					'adder_relation' => ($partner_relation == 'servicer') ? 'requester' : 'servicer'
				),
				sprintf("ID = %d", (int)$xrefid)
			);

			// email alert other partner
			$users = new Users(array('db' => $this->db));
			$partner_user = $users->get( array('user' => $partner_id, 'userby' => 'ID') );

			$docs = new Documents(array('db' => $this->db));
			$arg = array();
			$arg['uid'] = $partner_id;
			$arg['getid'] = $doc_id;
			$doc = $docs->getList( $arg );

			if (true)
			{
				$mail = new Mailer( $this->db->settings['page_title'], SMTP_USER, $this->db->settings['mail_sender_mode'] );
				$mail->add( trim($partner_user['data']['email']) );
				$arg = array(
					'nev' => trim($partner_user['data']['nev']),
					'project_nev' => (($projectdata[$partner_relation.'_title'] != '') ? $projectdata[$partner_relation.'_title'] : __('- nincs név -')),
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
				$mail->setSubject( __('Új dokumentuma érkezett!') );
				$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'clearmail', $arg ) );
				$re = $mail->sendMail();
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
						'requester_alerted' => 1,
						'servicer_alerted' => 1
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
						'requester_alerted' => 1,
						'servicer_alerted' => 1
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
				sprintf("hashkey = '%s'", $project['hashkey'])
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
	public function getProjectMessagesInfo( $project_id, $uid, $relation )
	{
		$ret = array(
			'unreaded' => 0,
			'closed' => false
		);

		$projectdata = $this->getProjectData( $project_id, $uid );

		$message = $this->db->squery("SELECT m.closed, m.closed_at FROM ".\MessageManager\Messanger::DBTABLE." as m WHERE m.sessionid = :session", array('session' => $projectdata['hashkey']))->fetch(\PDO::FETCH_ASSOC);

		if ($message && $message['closed']) {
			$ret['closed'] = $message['closed_at'];
		}

		switch ($relation)
		{
			case 'admin':
			break;
			case 'requester':
				$qry = "SELECT COUNT(ms.ID) FROM ".\MessageManager\Messanger::DBTABLE_MESSAGES." as ms WHERE ms.sessionid = :session and (ms.user_from_id != 0 and ms.user_to_id) and ms.user_from_id != :uid and ms.requester_readed_at IS NULL";
			break;
			case 'servicer':
				$qry = "SELECT COUNT(ms.ID) FROM ".\MessageManager\Messanger::DBTABLE_MESSAGES." as ms WHERE ms.sessionid = :session and (ms.user_from_id != 0 and ms.user_to_id) and ms.user_from_id != :uid and ms.servicer_readed_at IS NULL";
			break;
		}

		if ($qry) {
			$data = $this->db->squery($qry, array('uid' => $uid, 'session' => $projectdata['hashkey']));

			if ($data->rowCount() == 0) {
				return $ret;
			}
			$unreaded = $data->fetchColumn();
			$ret['unreaded'] = $unreaded;
		}

		return $ret;
	}

	public function getProjectPaidAmount( $project_id )
	{
		$paid_amount = (float)$this->db->squery("SELECT
			SUM(d.ertek)
		FROM ".\PortalManager\Documents::DBXREF_PROJECT." as xp
		LEFT OUTER JOIN ".\PortalManager\Documents::DBTABLE." as d ON d.ID = xp.doc_id
		WHERE xp.project_id = :project and d.teljesites_at IS NOT NULL and 3 IN (SELECT folder_id FROM ".\PortalManager\Documents::DBXREF_FOLDER."  WHERE doc_id = xp.doc_id)",array('project' => $project_id))->fetchColumn();

		return $paid_amount;
	}

	public function getProjectProgress( $project_raw )
	{
		$p = (int)$project_raw['status_percent'];
		return ($p <= 100) ? $p : 100;
	}

	public function getProjectPaymentProgress( $project_id )
	{
		$paid_amount = $this->getProjectPaidAmount( $project_id );

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
			o.*,
			ro.item_id as service_item_id
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
		$row['szolgaltatas'] = $this->getServiceItemData( $row['service_item_id'] );

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
