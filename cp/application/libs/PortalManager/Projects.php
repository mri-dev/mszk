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

		$q = "SELECT
			p.*
		FROM projects as p
		WHERE 1=1 ";

		if (isset($arg['getproject'])) {
			$q .= " and p.hashkey  = :getproject";
			$qarg['getproject'] =$arg['getproject'];
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

		if (isset($arg['uservalid'])) {
			$q .= " and (p.requester_id = :uid or p.servicer_id = :uid)";
			$qarg['uid'] = (int)$arg['uservalid'];
		}

		$q .= " ORDER BY p.created_at DESC";

		$data = $this->db->squery($q, $qarg);
		if ($data->rowCount() == 0) {
			return $list;
		}

		$data = $data->fetchAll(\PDO::FETCH_ASSOC);

		foreach ((array)$data as $d)
		{
			$d['my_relation'] = ($uid == $d['requester_id']) ? 'requester': 'servicer';
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
		return 75;
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
