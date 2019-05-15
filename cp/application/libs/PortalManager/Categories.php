<?
namespace PortalManager;

use PortalManager\Category;

/**
* class Categories
* @package ShopManager
* @version 1.0
*/
class Categories
{
	private $db = null;
	public $tree = false;
	private $current_category = false;
	private $tree_steped_item = false;
	private $tree_items = 0;
	private $walk_step = 0;
	private $parent_data = false;

	function __construct( $arg = array() )
	{
		$this->db = $arg[db];
  }

	public function getGroups()
	{
		$qry = "
			SELECT
				lg.*
			FROM lists_group as lg
			WHERE 1=1 ";

		$qry .= " ORDER BY lg.neve ASC;";

		$qry 	= $this->db->query($qry);

		if( $qry->rowCount() == 0 ) return array();


		$data = $qry->fetchAll(\PDO::FETCH_ASSOC);

		$back = array();

		foreach ((array)$data as $d) {
			$back[] = $d;
		}

		return $back;
	}

	public function getGroup( $id = 0)
	{
		$qry = "
			SELECT
				lg.*
			FROM lists_group as lg
			WHERE 1=1 and lg.ID = :id";

		$qry .= " ORDER BY lg.neve ASC;";
		$qry 	= $this->db->squery($qry, array('id' => $id));

		if( $qry->rowCount() == 0 ) return array();
		$data = $qry->fetch(\PDO::FETCH_ASSOC);
		return $data;
	}

	/**
	 * Kategória létrehzás
	 * @param array $data új kategória létrehozásához szükséges adatok
	 * @return void
	 */
	public function add( $data = array() )
	{
		$deep = 0;
		$name = ($data['neve']) ?: false;
		$name_en = ($data['neve_en']) ?: NULL;
		$sort = ($data['sorrend']) ?: 0;
		$parent = ($data['szulo_id']) ?: NULL;
		$hashkey = ($data['hashkey']) ?: NULL;
		$groupid = ($data['group_id']) ?: NULL;
		$kep = ($data['kep']) ?: NULL;
		$oldal_hashkeys = (count($new_data['oldal_hashkeys']) > 0) ? implode(",",$new_data['oldal_hashkeys']) : NULL;

		if ($parent) {
			$xparent = explode('_',$parent);
			$parent = (int)$xparent[0];
			$deep = (int)$xparent[1] + 1;
		}

		if ( !$groupid ) {
			throw new \Exception( __("Kérjük, hogy válassza ki a csoportot ahova szeretné rögzíteni az elemet!") );
		}

		if ( !$name ) {
			throw new \Exception( __("Kérjük, hogy adja meg az elem elnevezését!") );
		}

		$this->db->insert(
			"lists",
			array(
				'neve' 		=> $name,
				'neve_en' 		=> $name_en,
				'szulo_id' 	=> $parent,
				'group_id' => $groupid,
				'sorrend' 	=> $sort,
				'deep' 		=> $deep,
				'kep' 		=> $kep,
				'hashkey' 	=> $hashkey,
				'oldal_hashkeys' => $oldal_hashkeys
			)
		);
	}

	/**
	 * Kategória szerkesztése
	 * @param  Category $category ShopManager\Category class
	 * @param  array    $new_data
	 * @return void
	 */
	public function edit( Category $category, $new_data = array() )
	{
		$deep = 0;
		$name = ($new_data['neve']) ?: false;
		$name_en = ($new_data['neve_en']) ?: NULL;
		$sort = ($new_data['sorrend']) ?: 0;
		$parent = ($new_data['szulo_id']) ?: NULL;
		$hashkey = ($new_data['hashkey']) ?: NULL;
		$groupid = ($new_data['group_id']) ?: NULL;
		$oldal_hashkeys = (count($new_data['oldal_hashkeys']) > 0) ? implode(",",$new_data['oldal_hashkeys']) : NULL;
		$image = ( isset($new_data['kep']) ) ? $new_data['kep'] : NULL;

		if ($parent) {
			$xparent = explode('_',$parent);
			$parent = (int)$xparent[0];
			$deep = (int)$xparent[1] + 1;
		}

		if ( !$groupid ) {
			throw new \Exception( __("Kérjük, hogy válassza ki a csoportot ahova szeretné rögzíteni az elemet!") );
		}

		if ( !$name ) {
			throw new \Exception( __("Kérjük, hogy adja meg az elem elnevezését!") );
		}

		$category->edit(array(
			'neve' 		=> $name,
			'neve_en' 		=> $name_en,
			'szulo_id' 	=> $parent,
			'sorrend' 	=> $sort,
			'group_id' => $groupid,
			'deep' 		=> $deep,
			'hashkey' 	=> $hashkey,
			'oldal_hashkeys' => $oldal_hashkeys,
			'kep' 		=> $image
		));
	}

	public function addGroup( $data = array() )
	{
		$name = ($data['neve']) ?: false;
		$slug = ($data['slug']) ?: NULL;
		$desc = ($data['leiras']) ?: NULL;

		if ( !$name ) {
			throw new \Exception( __("Kérjük, hogy adja meg a csoport elnevezését!") );
		}

		if (!$slug) {
			$slug = $this->checkEleres( 'lists_group', $name );
		} else {
			$slug = Formater::makeSafeUrl($slug,'');
		}

		$this->db->insert(
			"lists_group",
			array(
				'neve' => addslashes($name),
				'slug' => $slug,
				'description'	=> addslashes($desc),
			)
		);
	}

	public function editGroup( $group, $new_data = array() )
	{

		$name = ($new_data['neve']) ?: false;
		$slug = ($new_data['slug']) ?: NULL;
		$desc = ($new_data['leiras']) ?: NULL;

		if ( !$name ) {
			throw new \Exception( __("Kérjük, hogy adja meg a csoport elnevezését!") );
		}

		if (!$slug) {
			$slug = $this->checkEleres( 'lists_group', $name );
		} else {
			$slug = Formater::makeSafeUrl($slug,'');
		}

		$this->db->update(
			'lists_group',
			array(
				'neve' => addslashes($name),
				'slug' => $slug,
				'description'	=> addslashes($desc),
			),
			sprintf("ID = %d", $group['ID'])
		);
	}

	public function delete( Category $category )
	{
		$category->delete();
	}

	public function deleteGroup( $group = 0 )
	{
		$this->db->squery("DELETE FROM lists_group WHERE ID = :id", array('id' => $group['ID']));
	}

	/**
	 * Kategória fa kilistázása
	 * @param int $top_category_id Felső kategória ID meghatározása, nem kötelező. Ha nincs megadva, akkor
	 * a teljes kategória fa listázódik.
	 * @return array Kategóriák
	 */
	public function getTree( $top_category_id = false, $arg = array() )
	{
		$tree 		= array();

		if ( $top_category_id ) {
			$this->parent_data = $this->db->query( sprintf("SELECT * FROM lists WHERE ID = %d", $top_category_id) )->fetch(\PDO::FETCH_ASSOC);
		}

		// Legfelső színtű kategóriák
		$qry = "
			SELECT
				l.*,
				lg.neve as group_neve,
				lg.description as group_desc
			FROM lists as l
			LEFT OUTER JOIN lists_group as lg ON lg.ID = l.group_id
			WHERE 1=1 ";

		if ( !$top_category_id ) {
			$qry .= " and l.szulo_id IS NULL ";
		} else {
			$qry .= " and l.szulo_id = ".$top_category_id;
		}

		// Group ID
		if( isset($arg['group_id']) )
		{
			$qry .= " and l.group_id IN (".implode(",",(array)$arg['group_id']).") ";
		}

		// Group slug
		if( isset($arg['group_slug']) )
		{
			$qry .= " and lg.slug = '".$arg['group_slug']."' ";
		}

		// ID SET
		if( isset($arg['id_set']) && count($arg['id_set']) )
		{
			$qry .= " and l.ID IN (".implode(",",$arg['id_set']).") ";
		}

		$qry .= " ORDER BY l.sorrend ASC, l.ID ASC;";
		error_log($qry);

		$top_cat_qry 	= $this->db->query($qry);
		$top_cat_data 	= $top_cat_qry->fetchAll(\PDO::FETCH_ASSOC);

		if( $top_cat_qry->rowCount() == 0 ) return $this;

		foreach ( $top_cat_data as $top_cat ) {
			$this->tree_items++;

			$top_cat['link'] = DOMAIN.'termekek/'.\PortalManager\Formater::makeSafeUrl($top_cat['neve'],'_-'.$top_cat['ID']);

			if( !$arg['admin'] ){
				$lang = \Lang::getLang();
				$top_cat['neve'] = ($lang != DLANG) ? ( ($top_cat['neve_'.$lang]) == '' && $top_cat['neve'] != '' ) ? $top_cat['neve'] : $top_cat['neve_'.$lang] : $top_cat['neve'];
			}

			$this->tree_steped_item[] = $top_cat;

			// Alkategóriák betöltése
			$top_cat['child'] = $this->getChildCategories($top_cat['ID']);
			$tree[] = $top_cat;
		}

		$this->tree = $tree;

		return $this;
	}

	/**
	 * Végigjárja az összes kategóriát, amit betöltöttünk a getFree() függvény segítségével. while php függvénnyel
	 * járjuk végig. A while függvényen belül használjuk a the_cat() objektum függvényt, ami az aktuális kategória
	 * adataiat tartalmazza tömbbe sorolva.
	 * @return boolean
	 */
	public function walk()
	{
		if( !$this->tree_steped_item ) return false;

		$this->current_category = $this->tree_steped_item[$this->walk_step];

		$this->walk_step++;

		if ( $this->walk_step > $this->tree_items ) {
			// Reset Walk
			$this->walk_step = 0;
			$this->current_category = false;

			return false;
		}

		return true;
	}

	/**
	 * A walk() fgv-en belül visszakaphatjuk az aktuális kategória elem adatait tömbbe tárolva.
	 * @return array
	 */
	public function the_cat()
	{
		return $this->current_category;
	}

	public function getParentData( $field = false )
	{
		if ( $field ) {
			return $this->parent_data[$field];
		} else
		return $this->parent_data;
	}

	/**
	 * Kategória alkategóriáinak listázása
	 * @param  int $parent_id 	Szülő kategória ID
	 * @return array 			Szülő kategória alkategóriái
	 */
	public function getChildCategories( $parent_id )
	{
		$tree = array();

		// Gyerek kategóriák
		$child_cat_qry 	= $this->db->query( sprintf("
			SELECT 		l.*,
								lg.neve as group_neve,
								lg.description as group_desc
			FROM 			lists as l
			LEFT OUTER JOIN lists_group as lg ON lg.ID = l.group_id
			WHERE 		l.szulo_id = %d
			ORDER BY 	l.sorrend ASC, l.ID ASC;", $parent_id));
		$child_cat_data	= $child_cat_qry->fetchAll(\PDO::FETCH_ASSOC);

		if( $child_cat_qry->rowCount() == 0 ) return false;
		foreach ( $child_cat_data as $child_cat ) {
			$this->tree_items++;
			$child_cat['link'] 	= DOMAIN.'termekek/'.\PortalManager\Formater::makeSafeUrl($child_cat['neve'],'_-'.$child_cat['ID']);
			$child_cat['kep'] 	= ($child_cat['kep'] == '') ? '/src/images/no-image.png' : $child_cat['kep'];
			$this->tree_steped_item[] = $child_cat;

			$child_cat['child'] = $this->getChildCategories($child_cat['ID']);
			$tree[] = $child_cat;
		}

		return $tree;

	}


	/**
	 * Kategória szülő listázása
	 * @param  int $child_id 	Szülő kategória ID
	 * @return array 			Szülő szülő kategóriái
	 */
	public function getCategoryParentRow( $id, $return_row = 'ID', $deep_allow_under = 0 )
	{
		$row = array();

		$has_parent = true;

		$limit = 10;

		$sid = $id;

		while( $has_parent && $limit > 0 ) {

			$q 		= "SELECT ".( ($return_row) ? $return_row.', szulo_id, deep' : '*' )." FROM lists WHERE ID = ".$sid.";";
			$qry 	= $this->db->query($q);
			$data 	= $qry->fetch(\PDO::FETCH_ASSOC);

			$sid = $data['szulo_id'];

			if( is_null( $data['szulo_id'] ) ) {
				$has_parent = false;
			}

			if( (int)$data['deep'] >= $deep_allow_under ) {
				if (!$return_row) {
					$row[] = $data;
				} else {
					$row[] = $data[$return_row];
				}
			}

			$limit--;
		}

		return $row;
	}

	private function checkEleres( $where, $text )
	{
		$text = Formater::makeSafeUrl($text,'');

		$qry = $this->db->query(sprintf("
			SELECT 		slug
			FROM 		".$where."
			WHERE 		slug = '%s' or
						slug like '%s-_' or
						slug like '%s-__'
			ORDER BY 	slug DESC
			LIMIT 		0,1", trim($text), trim($text), trim($text) ));
		$last_text = $qry->fetch(\PDO::FETCH_COLUMN);

		if( $qry->rowCount() > 0 ) {

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

	public function killDB()
	{
		$this->db = null;
	}

	public function __destruct()
	{
		//echo ' -DEST- ';
		$this->tree = false;
		$this->current_category = false;
		$this->tree_steped_item = false;
		$this->tree_items = 0;
		$this->walk_step = 0;
		$this->parent_data = false;
	}
}
?>
