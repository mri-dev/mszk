<?
namespace PortalManager;

/**
* class Projects
* @package PortalManager
* @version 1.0
*/
class Projects
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
			$d['status_percent'] = $this->getProjectProgress( $d );
			$d['status_percent_class'] = \Helper::progressBarColor($d['status_percent']);
			$d['paying_percent'] = $this->getProjectPaymentProgress( $d['ID'] );
			$d['paying_percent_class'] = \Helper::progressBarColor($d['paying_percent']);
			$d['messages'] = $this->getProjectMessagesInfo( $d['ID']  );

			$list[] = $d;
		}

		if (isset($arg['getproject'])) {
			$list = $list[0];
		}

		return $list;
	}

	public function addDocument( $project_id, $doc_id, $adder_user_id )
	{
		// register
		$this->db->insert(
			\PortalManager\Documents::DBXREF_PROJECT,
			array(
				'doc_id' => $doc_id,
				'project_id' => $project_id,
				'adder_user_id' => $adder_user_id
			)
		);

		// get partner id from project

		// email alert other partner
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
			}
		} else {
				$updates['closed_by'] = NULL;
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
	public function getProjectMessagesInfo( $project_id )
	{
		$ret = array(
			'unreaded' => 0
		);

		return $ret;
	}

	public function getProjectProgress( $project_raw )
	{
		$p = (int)$project_raw['status_percent'];
		return ($p <= 100) ? $p : 100;
	}

	// TODO: Számlák alapján összevetni
	public function getProjectPaymentProgress( $project_id )
	{
		return 0;
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
