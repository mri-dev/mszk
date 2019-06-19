<?
namespace PortalManager;

/**
* class Documents
* @package PortalManager
* @version 1.0
*/
class Documents
{
  const DBTABLE = 'documents';
  const DBFOLDERS = 'docs_folders';
  const DBXREF_FOLDER = 'documents_x_folder';
  const DBXREF_PROJECT = 'documents_x_project';

	private $db = null;

	function __construct( $arg = array() )
	{
		$this->db = $arg[db];
		return $this;
	}

  public function getAvaiableFolders( $uid = false, $szulo_id = false )
  {
    $list = array();
    $qarg = array();

		$q = "SELECT
			d.*
		FROM ".self::DBFOLDERS." as d
		WHERE 1=1 ";

    if ($szulo_id) {
      $q .= " and d.szulo_id = :szid";
      $qarg['szid'] = (int)$szulo_id;
    }

    if (!$uid) {
      $q .= " and d.isdefault = 1";
    } else {
      if (!$szulo_id) {
        $q .= " and d.szulo_id IS NULL";
      }
      $q .= " and (d.isdefault = 1 || d.user_id = :uid)";
      $qarg['uid'] = (int)$uid;
    }

		$q .= " ORDER BY d.isdefault DESC";

		$data = $this->db->squery($q, $qarg);
		if ($data->rowCount() == 0) {
			return $list;
		}

		$data = $data->fetchAll(\PDO::FETCH_ASSOC);

    foreach ((array)$data as $d)
    {
      $child = $this->getAvaiableFolders( $uid, (int)$d['ID']);
      $d['child'] = $child;
      $list[] = $d;
    }

    return $list;
  }

	public function getList( $arg = array() )
	{
		$list = array();
		$qarg = array();

		$uid = (int)$arg['uid'];
		$users = new Users( array('db' => $this->db ));

    if ($uid) {
      $controll_user =  $users->get( array('user' => $uid, 'userby' => 'ID', 'alerts' => false) );
      $controll_user_admin = ($controll_user['data']['user_group'] == \PortalManager\Users::USERGROUP_SUPERADMIN || $controll_user['data']['user_group'] == \PortalManager\Users::USERGROUP_ADMIN) ? true : false;
    }

		$q = "SELECT
			d.*
		FROM ".self::DBTABLE." as d
		WHERE 1=1 ";

		if (isset($arg['get'])) {
			$q .= " and d.hashkey  = :get";
			$qarg['get'] =$arg['get'];
		}

    if (isset($arg['hashkey'])) {
			$q .= " and d.hashkey  = :hashkey";
			$qarg['hashkey'] =$arg['hashkey'];
		}

		if ( isset($arg['ids']) && !empty($arg['ids']) ) {
			$q .= " and FIND_IN_SET(d.ID, :idslist)";
			$qarg['idslist'] = implode(",", (array)$arg['ids']);
		}

		$q .= " ORDER BY d.created_at DESC";

		$data = $this->db->squery($q, $qarg);
		if ($data->rowCount() == 0) {
			return $list;
		}

		$data = $data->fetchAll(\PDO::FETCH_ASSOC);

		foreach ((array)$data as $d)
		{

			$list[] = $d;
		}

		if (isset($arg['get'])) {
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
