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
			$d['status_percent'] = 15;
			$d['paying_percent'] = 50;
			$d['user_requester'] = $users->get( array('user' => $d['requester_id'], 'userby' => 'ID', 'alerts' => false) );
			$d['user_servicer'] = $users->get( array('user' => $d['servicer_id'], 'userby' => 'ID', 'alerts' => false) );
			$d['partner'] = ($d['my_relation'] == 'requester') ? $d['user_servicer'] : $d['user_requester'];
			$list[] = $d;
		}

		if (isset($arg['getproject'])) {
			$list = $list[0];
		}

		return $list;
	}


	public function __destruct()
	{
		$this->db = null;
	}

}
?>
