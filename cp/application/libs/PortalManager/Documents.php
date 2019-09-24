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
  const DBLOG_VIEWS = 'documents_view';

	private $db = null;

	function __construct( $arg = array() )
	{
		$this->db = $arg[db];
		return $this;
	}

  public function getAvaiableFolders( $uid = false, $szulo_id = false, $projecthash = false )
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
      $d['filecnt'] = $this->getFolderFileCount( $uid, (int)$d['ID'], $projecthash );
      $d['child'] = $child;
      $list[] = $d;
    }

    return $list;
  }

  public function getFolderFileCount( $uid, $folder_id, $projecthash = false )
  {
    $qarg = array('folder' => $folder_id);

    if ($uid) {
      $user = $this->db->squery("SELECT user_group FROM felhasznalok WHERE ID = :id", array('id' => $uid))->fetch(\PDO::FETCH_ASSOC);
      $controll_user_admin = ($user['user_group'] == \PortalManager\Users::USERGROUP_SUPERADMIN || $user['user_group'] == \PortalManager\Users::USERGROUP_ADMIN) ? true : false;
    }

    if ( $projecthash ) {
      $project_id = (int)$this->db->squery("SELECT ID FROM ".\PortalManager\Projects::DBPROJECTS." WHERE hashkey = :hash", array('hash' => $projecthash))->fetchColumn();
    }

    $qry = "SELECT d.ID FROM ".self::DBTABLE." as d WHERE 1=1 ";

    if (!$controll_user_admin) {
      $qry .= "and (d.avaiable_to IS NULL or (d.avaiable_to >= now() or :auid = d.user_id))";
      $qarg['auid'] = $uid;
    }

    if ($user && !in_array($user['user_group'], array(\PortalManager\Users::USERGROUP_ADMIN, \PortalManager\Users::USERGROUP_SUPERADMIN)) ) {
      $qry .= " and (d.user_id = :uid or :uid IN (SELECT partner_id FROM ".self::DBXREF_PROJECT." WHERE doc_id = d.ID))";
      $qarg['uid'] = $uid;
    }
    $qry .= " and FIND_IN_SET(:folder, (SELECT folder_id FROM ".self::DBXREF_FOLDER." WHERE doc_id = d.ID))";

    if ( $projecthash && $project_id != 0 ) {
      $qry .= " and FIND_IN_SET(:pid, (SELECT project_id FROM ".self::DBXREF_PROJECT." WHERE doc_id = d.ID))";
      $qarg['pid'] = $project_id;
    }
    $cnt = 0;
    $qry = $this->db->squery( $qry, $qarg );

    $cnt = $qry->rowCount();

    return $cnt;
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
        'szulo_id' => ($szulo_id) ? $szulo_id : NULL,
        'hashkey' => $hash,
        'user_id' => $uid
      )
    );
    $folder_id = $this->db->lastInsertId();

    return array(
      'id' => $folder_id,
      'hashkey' => $hash,
      'name' => $name
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

  public function reAddFileToFolder( $doc_id, $folder_id )
  {
    // remove prev folder
    $this->db->squery("DELETE FROM ".self::DBXREF_FOLDER." WHERE doc_id = :did", array('did' => $doc_id));

    // add to new
    $this->db->insert(
      self::DBXREF_FOLDER,
      array(
        'doc_id' => $doc_id,
        'folder_id' => $folder_id
      )
    );
  }

  // TODO: Fájlok átcsatolása a ketegorizálatlan mappába
  public function deleteFolder( $hashkey )
  {
    // fájlok átcsatolása
    $folderinfo = $this->getFolderData( $hashkey );
    $docs = $this->getList(array(
      'folder' => $folderinfo['ID']
    ));
    if ($docs['data']) {
      foreach ( (array)$docs['data'] as $doc  ) {
        $this->reAddFileToFolder( $doc['ID'], 1 );
      }
    }

    // törlés
    //$this->db->squery("DELETE FROM ".self::DBFOLDERS." WHERE hashkey = :hash", array('hash' => $hashkey));
  }

  public function addFile( $uid, $post )
  {
    $hash = md5(uniqid());

    if (empty($post['name'])) {
      throw new \Exception(__('A dokumentum elnevezése kötelező!'));
    }

    $ertek = (!empty($post['ertek'])) ? (float)$post['ertek'] : 0;
    $expipre_at = (!empty($post['expipre_at'])) ? date('Y-m-d', strtotime($post['expipre_at'])) : NULL;
    $teljesites_at = (!empty($post['teljesites_at'])) ? date('Y-m-d', strtotime($post['teljesites_at'])) : NULL;
    $avaiable_to = (!empty($post['avaiable_to'])) ? date('Y-m-d', strtotime($post['avaiable_to'])) : NULL;

    $folder = (!empty($post['folder'])) ? $post['folder'] : false;

    $this->db->insert(
      self::DBTABLE,
      array(
        'user_id' => $uid,
        'hashkey' => $hash,
        'name' => $post['name'],
        'docfile' => $post['docfile'],
        'expire_at' => $expipre_at,
        'teljesites_at' => $teljesites_at,
        'avaiable_to' => $avaiable_to,
        'ertek' => $ertek
      )
    );

    $doc_id = $this->db->lastInsertId();

    if ( $folder && $doc_id )
    {
      $folderdata = $this->getFolderData( $folder );
      $folder_id = $folderdata['ID'];

      $this->db->insert(
        self::DBXREF_FOLDER,
        array(
          'doc_id' => $doc_id,
          'folder_id' => $folder_id
        )
      );
    }

    return array(
      'id' => $doc_id,
      'name' => $post['name'],
      'hashkey' => $hash
    );
  }

  public function deleteFile( $uid, $post)
  {
    $hash = $post['hashkey'];

    if (empty($hash)) {
      throw new \Exception(__('A dokumentum azonosítója (hashkey) hiányzik!'));
    }

    $users = new Users( array('db' => $this->db ));

    if ($uid) {
      $controll_user =  $users->get( array('user' => $uid, 'userby' => 'ID', 'alerts' => false) );
      $controll_user_admin = ($controll_user['data']['user_group'] == \PortalManager\Users::USERGROUP_SUPERADMIN || $controll_user['data']['user_group'] == \PortalManager\Users::USERGROUP_ADMIN) ? true : false;
    }

    if ( !$controll_user_admin && $controll_user['data']['ID'] != $uid ) {
      throw new \Exception(__('Önnek nincs jogosultsága törölni ezt a dokumentumot.'));
    }

    $this->db->squery("DELETE FROM ".self::DBTABLE." WHERE hashkey = :hash", array('hash'  => $hash));
  }

  public function editFile( $uid, $post )
  {
    $hash = $post['hashkey'];

    if (empty($post['name'])) {
      throw new \Exception(__('A dokumentum elnevezése kötelező!'));
    }

    $doc = $this->db->squery("SELECT * FROM ".self::DBTABLE." WHERE hashkey = :hash", array('hash' => $hash))->fetch(\PDO::FETCH_ASSOC);

    $ertek = (!empty($post['ertek'])) ? (float)$post['ertek'] : 0;
    $expire_at = (!empty($post['expire_at'])) ? date('Y-m-d', strtotime($post['expire_at'])) : NULL;
    $teljesites_at = (!empty($post['teljesites_at'])) ? date('Y-m-d', strtotime($post['teljesites_at'])) : NULL;
    $avaiable_to = (!empty($post['avaiable_to'])) ? date('Y-m-d', strtotime($post['avaiable_to'])) : NULL;

    $folder = (!empty($post['folder'])) ? $post['folder'] : false;

    $this->db->update(
      self::DBTABLE,
      array(
        'name' => $post['name'],
        'docfile' => $post['docfile'],
        'expire_at' => $expire_at,
        'teljesites_at' => $teljesites_at,
        'avaiable_to' => $avaiable_to,
        'ertek' => $ertek
      ),
      sprintf("hashkey = '%s'", $hash)
    );

    $doc_id = (int)$doc['ID'];

    if ( $folder && $doc_id && $doc_id != 0 )
    {
      if ($post['prev_folder'] != $folder)
      {
        // reset
        $this->db->squery("DELETE FROM ".self::DBXREF_FOLDER." WHERE doc_id = :did", array('did' => $doc_id));

        $folderdata = $this->getFolderData( $folder );
        $folder_id = $folderdata['ID'];

        $this->db->insert(
          self::DBXREF_FOLDER,
          array(
            'doc_id' => $doc_id,
            'folder_id' => $folder_id
          )
        );
      }
    }
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

  public function getDocFolders( $id )
  {
    $folders = array();

    if (empty($id)) {
      return false;
    }

    $qry = $this->db->squery("SELECT
      f.name as folder_name,
      f.hashkey as folder_hashkey,
      f.szulo_id as folder_szulo_id,
      f.user_id as folder_author_user,
      f.slug as folder_slug,
      f.isdefault
    FROM ".self::DBXREF_FOLDER." as df
    LEFT OUTER JOIN ".self::DBFOLDERS." as f ON f.ID = df.folder_id
    WHERE df.doc_id = :id
    ", array('id' => $id));

    if ($qry->rowCount() == 0) {
      return false;
    }

    $data = $qry->fetchAll(\PDO::FETCH_ASSOC);

    foreach ((array)$data as $d) {
      $folders[] = $d;
    }

    return $folders;
  }

	public function getList( $arg = array() )
	{
		$list = array();
		$qarg = array();
    $pages = array();
		$total_num	= 0;
    $limit = 50;
    $current_page = ($arg['page']) ? $arg['page'] : \Helper::getLastParam();
		$pages[current] = (is_numeric($current_page) && $current_page > 0) ? $current_page : 1;

		$uid = (int)$arg['uid'];
		$users = new Users( array('db' => $this->db ));

    if ($uid) {
      $controll_user =  $users->get( array('user' => $uid, 'userby' => 'ID', 'alerts' => false) );
      $controll_user_admin = ($controll_user['data']['user_group'] == \PortalManager\Users::USERGROUP_SUPERADMIN || $controll_user['data']['user_group'] == \PortalManager\Users::USERGROUP_ADMIN) ? true : false;
    }

		$q = "SELECT SQL_CALC_FOUND_ROWS
		d.*,
    f.nev as user_nev,
    fa.ertek as user_company";

    if ( isset($arg['in_project']) && !empty($arg['in_project']) ) {
      $q .= ",xp.added_at as xproject_added_at";
    }

		$q .= " FROM ".self::DBTABLE." as d
    LEFT OUTER JOIN felhasznalok as f ON f.ID = d.user_id
    LEFT OUTER JOIN felhasznalo_adatok as fa ON fa.fiok_id = d.user_id and fa.nev = 'company_name' ";

    if ( isset($arg['in_project']) && !empty($arg['in_project']) ) {
      $q .= " LEFT OUTER JOIN ".self::DBXREF_PROJECT." as xp ON xp.doc_id = d.ID and xp.project_id = ".$arg['in_project'];
    }

    $q .= " WHERE 1=1 ";

    if ( isset($arg['uid']) && !$controll_user_admin ) {
			$q .= " and (d.user_id = :uid or :uid IN (SELECT partner_id FROM ".self::DBXREF_PROJECT." WHERE adder_user_id != :uid and doc_id = d.ID))";
      $qarg['uid'] = $uid;
		}

    if (!isset($arg['exclude_unavaiable'])) {
      if (!$controll_user_admin) {
        $q .= " and (d.avaiable_to IS NULL or (d.avaiable_to >= now() or :auid = d.user_id))";
        $qarg['auid'] = $uid;
      }
		}

		if (isset($arg['get'])) {
			$q .= " and d.hashkey  = :get";
			$qarg['get'] =$arg['get'];
		}

    if (isset($arg['getid'])) {
			$q .= " and d.ID  = :id";
			$qarg['id'] = $arg['getid'];
		}

    if (isset($arg['hashkey'])) {
			$q .= " and d.hashkey  = :hashkey";
			$qarg['hashkey'] =$arg['hashkey'];
		}

    if ( isset($arg['from_user']) && !empty($arg['from_user']) ) {
      $q .= " and d.user_id = :fuserid";
      $qarg['fuserid'] = (int)$arg['from_user'];
    }

		if ( isset($arg['ids']) && !empty($arg['ids']) )
    {
			$q .= " and FIND_IN_SET(d.ID, :idslist)";
			$qarg['idslist'] = implode(",", (array)$arg['ids']);
		}

    if ( isset($arg['not_in_project']) && !empty($arg['not_in_project']) )
    {
      $q .= " and :not_in_project NOT IN (SELECT project_id FROM ".self::DBXREF_PROJECT." WHERE doc_id = d.ID)";

      if ( is_numeric($arg['not_in_project']) ) {
        $qarg['not_in_project'] = $arg['not_in_project'];
      } else {
        $hash = $arg['not_in_project'];
        $projekt_id = $this->db->squery("SELECT ID FROM projects WHERE hashkey = :hash", array('hash' => $hash))->fetchColumn();
        $qarg['not_in_project'] = $projekt_id;
      }
		}

    if ( isset($arg['in_project']) && !empty($arg['in_project']) ) {
      $q .= " and :in_project IN (SELECT project_id FROM ".self::DBXREF_PROJECT." WHERE doc_id = d.ID)";
      $qarg['in_project'] = $arg['in_project'];
    }

    if ( isset($arg['not_ids']) && !empty($arg['not_ids']) ) {
			$q .= " and !FIND_IN_SET(d.ID, :not_ids)";
			$qarg['not_ids'] = implode(",", (array)$arg['not_ids']);
		}

    if ( isset($arg['folder']) && !empty($arg['folder']) ) {
			$q .= " and FIND_IN_SET(:folder, (SELECT folder_id FROM ".self::DBXREF_FOLDER." WHERE doc_id = d.ID ))";
      $qarg['folder'] = (int)$arg['folder'];
		}

    if (isset($arg['expire_qry']) && !empty($arg['expire_qry']) ) {
      $q .= " and d.expire_at ".$arg['expire_qry'];
    }

    if (isset($arg['teljesites_qry']) && !empty($arg['teljesites_qry']) ) {
      $q .= " and d.teljesites_at ".$arg['teljesites_qry'];
    }

    // Searches
    if (isset($arg['search'])) {
      foreach ((array)$arg['search'] as $sk => $s ) {
        if ($sk == 'name') {
          $q .= " and d.name LIKE :src_name";
          $qarg['src_name'] = "%".trim($s)."%";
        }
        if ($sk == 'own') {
          $q .= " and d.user_id = :ownuserid";
          $qarg['ownuserid'] = $uid;
        }
      }
    }

    if (!isset($arg['order'])) {
      $q .= " ORDER BY d.created_at DESC";
    } else {
      $q .= " ORDER BY ".$arg['order'];
    }

    if($arg[limit]){
			$q = rtrim($q,";");
			$limit = (is_numeric($arg[limit]) && $arg[limit] > 0 && $arg[limit] != '') ? $arg[limit] : $limit;
			$l_min = 0;
			$l_min = $pages[current] * $limit - $limit;
			$q .= " LIMIT $l_min, $limit";
			$q .= ";";
		}

		$data = $this->db->squery($q, $qarg);
    $total_num 	=  $this->db->query("SELECT FOUND_ROWS();")->fetchColumn();
		if ($data->rowCount() == 0) {
			return $list;
		}

    $return_num = $data->rowCount();
    ///
    $pages[max] = ($total_num == 0) ? 0 : ceil($total_num / $limit);
    $pages[limit]	= ($arg[limit]) ? $limit : false;

		$data = $data->fetchAll(\PDO::FETCH_ASSOC);

		foreach ((array)$data as $d)
		{
      $d['folders'] = $this->getDocFolders( $d['ID'] );
      $d['is_me'] = ($uid && $uid == $d['user_id']) ? true : false;

      // Ha projekt kapcsán lett szűrve
      if ( isset($arg['in_project']) && !empty($arg['in_project']) )
      {
        $d['xrefproject'] = $this->db->squery("SELECT xp.ID, xp.adder_user_id, xp.partner_id, xp.adder_relation, xp.added_at FROM ".self::DBXREF_PROJECT." as xp WHERE xp.project_id = :pid and xp.doc_id = :did", array('pid' => $arg['in_project'], 'did' => $d['ID']))->fetch(\PDO::FETCH_ASSOC);
      }
			$list[] = $d;
		}

		if (isset($arg['get']) || isset($arg['getid'])) {
			$list = $list[0];
      return $list;
		} else {
      return array(
        'pages' => $pages,
        'return_num' => (int)$return_num,
        'total_num' => (int)$total_num,
        'data' => $list
      );
    }
	}

  public function logDocumentView( $doc_hashkey )
  {
    $date = date('Y-m-d');
    $check = $this->db->squery("SELECT ID, visited FROM ".self::DBLOG_VIEWS." WHERE visit_date = :date and hashkey = :hash", array('date' => $date, 'hash' => $doc_hashkey));

    if ($check->rowCount() == 0) {
      $this->db->insert(
        self::DBLOG_VIEWS,
        array(
          'hashkey' => $doc_hashkey,
          'visit_date' => $date,
          'visited' => 1
        )
      );
    } else {
      $checkd = $check->fetch(\PDO::FETCH_ASSOC);
      $visited = (int)$checkd['visited'];
      $this->db->update(
        self::DBLOG_VIEWS,
        array(
          'visited' => $visited+1
        ),
        sprintf("hashkey = '%s' and visit_date = '%s'", $doc_hashkey, $date)
      );
    }
  }

	public function __destruct()
	{
		$this->db = null;
	}

}
?>
