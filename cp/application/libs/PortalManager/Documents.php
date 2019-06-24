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

		$q .= " ORDER BY d.isdefault DESC, d.ID ASC";

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

  public function findFolderHashkey( $slug, $uid )
  {
    if ($slug == 'folders') {
      return false;
    }

    if ($uid == '') {
      return false;
    }

    $hash = $this->db->squery($iq = "SELECT hashkey FROM ".self::DBFOLDERS." WHERE (isdefault = 0 and user_id = :uid and slug = :slug) or (isdefault = 1 and slug = :slug)", array('slug' => trim($slug), 'uid' => $uid))->fetchColumn();

    if ( !$hash && isset($_GET['folder']) && !empty($_GET['folder']) ) {
      $hash = $_GET['folder'];
    }

    return $hash;
  }

  public function getFolderData( $folderhash )
  {
    $qarg = array();

    $q = "SELECT
      d.*
    FROM ".self::DBFOLDERS." as d
    WHERE 1=1 and d.hashkey = :hash";
    $qarg['hash'] = $folderhash;


    $data = $this->db->squery($q, $qarg);

    if ($data->rowCount() == 0) {
      return false;
    }

    $data = $data->fetch(\PDO::FETCH_ASSOC);

    return $data;
  }

  public function addFolder( $uid, $post )
  {
    extract($post);

    if (empty($name))
    {
      throw new \Exception(__('A mappa elnevezése kötelező! Kérjük, hogy adja meg.'));
    }

    if (empty($uid))
    {
      throw new \Exception(__('Hiányzó felhasználó ID. A mappa létrehozásához szükséges egy felhasználó ID.'));
    }

    $name = ($name) ?: NULL;
    $szulo_id = ($szulo_id) ?: NULL;

    if (!$slug) {
			$slug = $this->checkEleres( $name, $uid );
		} else {
			$slug = Formater::makeSafeUrl($slug, '');
		}

    $hash = md5(uniqid());

    $szulo_id = $this->db->squery("SELECT ID FROM ".self::DBFOLDERS." WHERE hashkey = :hash",array('hash' => $szulo_id))->fetchColumn();

    $this->db->insert(
      self::DBFOLDERS,
      array(
        'name' => $name,
        'slug' => $slug,
        'szulo_id' => $szulo_id,
        'hashkey' => $hash,
        'user_id' => $uid
      )
    );
  }

  public function checkFolderEditPermission( $hashkey, $uid )
  {
    $folder = $this->db->squery("SELECT isdefault, user_id FROM ".self::DBFOLDERS." WHERE hashkey = :hash",array('hash' => $hashkey))->fetch(\PDO::FETCH_ASSOC);

    if ($folder['isdefault'] == 1) {
      return false;
    }

    if ( $uid == $folder['user_id'] ) {
      return true;
    }

    return false;
  }

  public function saveFolder( $hashkey, $post, $uid )
  {
    $update = array();

    $update['name'] = $post['name'];
    $update['szulo_id'] = ($post['szulo_id'] != '') ? (int)$post['szulo_id'] : NULL;

    $check_slug = $this->db->squery("SELECT slug FROM ".self::DBFOLDERS." WHERE hashkey = :hashkey", array('hashkey' => $hashkey))->fetchColumn();

    if ( $check_slug != Formater::makeSafeUrl($post['name'],'') )
    {
      $slug = $this->checkEleres( $post['name'], $uid );
      $update['slug'] = $slug;
    }

    $this->db->update(
      self::DBFOLDERS,
      $update,
      sprintf("hashkey = '%s'", $hashkey)
    );
  }

  private function checkEleres( $text, $uid )
	{
		$text = Formater::makeSafeUrl($text,'');

		$qry = $this->db->query(sprintf("
			SELECT slug
			FROM ".self::DBFOLDERS."
			WHERE user_id = %d and (slug = '%s' or
						slug like '%s-_' or
						slug like '%s-__')
			ORDER BY 	slug DESC
			LIMIT 0,1", (int)$uid, trim($text), trim($text), trim($text) ));
		$last_text = $qry->fetch(\PDO::FETCH_COLUMN);

		if( $qry->rowCount() > 0 )
    {
			$last_int = (int)end(explode("-",$last_text));

			if( $last_int != 0 ){
				$last_text = str_replace('-'.$last_int, '-'.($last_int+1) , $last_text);
			} else {
				$last_text .= '-1';
			}
		} else {
			$last_text = $text;
		}

		return $last_text;
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
