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
		$q = "SELECT
			p.*
		FROM projects as p
		WHERE 1=1 ";

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
			$list[] = $d;
		}

		return $list;
	}


	public function __destruct()
	{
		$this->db = null;
	}

}
?>
