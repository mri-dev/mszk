<?
namespace PortalManager;

use PortalManager\Formater;
use PortalManager\Template;
use Applications\Tabledata;

/**
* class Pages
* @package PortalManager
* @version v1.0
*/
class Pages
{
	private $db = null;
	public $tree = false;
	private $current_item = false;
	private $current_get_item = false;
	private $tree_steped_item = false;
	private $tree_items = 0;
	private $walk_step = 0;
	private $selected_page_id = false;
	private $is_admin = false;

	function __construct( $page_id = false, $arg = array() )
	{
		$this->db = $arg[db];

		if ( $page_id ) {
			$this->selected_page_id = $page_id;
		}
	}

	public function get( $page_id_or_slug )
	{
		$data = array();
		$qry = "
			SELECT *
			FROM oldalak
		";

		if (is_numeric($page_id_or_slug)) {
			$qry .= " WHERE ID = ".$page_id_or_slug;
		}else {
			$qry .= " WHERE eleres = '".$page_id_or_slug."'";
		}


		if ( !$this->is_admin ) {
			$qry .= " and lathato = 1 ";
		}

		$qry = $this->db->query($qry);

		$this->current_get_item = $qry->fetch(\PDO::FETCH_ASSOC);

		return $this;
	}

	public function add( $data )
	{
		$deep 		= 0;

		$cim 	= ($data['cim']) ?: false;
		$parent = ($data['parent']) ?: false;
		$eleres = ($data['eleres']) ?: false;
		$szoveg = ($data['szoveg']) ?: NULL;
		$boritokep = ($data['boritokep']) ?: NULL;
		$lathato= ($data['lathato'] == 'on') ? 1 : 0;
		$gyujto	= ($data['gyujto'] == 'on') ? 1 : 0;
		$hashkey= ($data['hashkey']) ?: NULL;
		$hashkey_kw= ($data['hashkey_keywords']) ?: NULL;

		$meta_title = ($data['meta_title']) ?: NULL;
		$meta_desc = ($data['meta_desc']) ?: NULL;
		$meta_image = ($data['meta_image']) ?: NULL;

		if ($parent) {
			$xparent = explode('_',$parent);
			$deep = $xparent[1]+1;
			$parent = $xparent[0];
		} else {
			$parent = NULL;
		}

		// Képek
		$kepek = '';

		if ( count($data['image_set']) > 0)
			foreach ($data['image_set'] as $kep ) {
				if ( $kep != '' ) {
					$kepek .= $kep.";;";
				}
			}
			$kepek = rtrim($kepek,";;");

		if ($kepek == '') {
			$kepek = NULL;
		}

		if (!$cim) { throw new \Exception("Kérjük, hogy adja meg az <strong>Oldal címét</strong>!"); }


		if (!$eleres) {
			$eleres = $this->checkEleres( $cim );
		} else {
			$eleres = Formater::makeSafeUrl($eleres,'');
		}

		$this->db->insert(
			"oldalak",
			array(
				'cim' => $cim,
				'szulo_id' => $parent,
				'eleres' => $eleres,
				'szoveg' => $szoveg,
				'deep' => $deep,
				'idopont' => NOW,
				'lathato' => $lathato,
				'boritokep' => $boritokep,
				'gyujto' => $gyujto,
				'kepek' => $kepek,
				'sorrend' => $data['sorrend'],
				'hashkey' => $hashkey,
				'hashkey_keywords' => $hashkey_kw,
				'meta_title' => $meta_title,
				'meta_desc' => $meta_desc,
				'meta_image' => $meta_image,
			)
		);
	}

	public function save( $data )
	{
		$deep 		= 0;

		$cim 	= ($data['cim']) ?: false;
		$parent = ($data['parent']) ?: false;
		$eleres = ($data['eleres']) ?: false;
		$szoveg = ($data['szoveg']) ?: NULL;
		$boritokep = ($data['boritokep']) ?: NULL;
		$lathato= ($data['lathato']) ? 1 : 0;
		$gyujto	= ($data['gyujto'] == 'on') ? 1 : 0;
		$hashkey= ($data['hashkey']) ?: NULL;
		$hashkey_kw= ($data['hashkey_keywords']) ?: NULL;

		$meta_title = ($data['meta_title']) ?: NULL;
		$meta_desc = ($data['meta_desc']) ?: NULL;
		$meta_image = ($data['meta_image']) ?: NULL;

			// Képek
		$kepek = '';

		if ( count($data['image_set']) > 0)
			foreach ($data['image_set'] as $kep ) {
				if ( $kep != '' ) {
					$kepek .= $kep.";;";
				}
			}
			$kepek = rtrim($kepek,";;");

		if ($kepek == '') {
			$kepek = NULL;
		}

		if ($parent) {
			$xparent = explode('_',$parent);
			$deep = $xparent[1]+1;
			$parent = $xparent[0];
		} else {
			$parent = NULL;
		}

		if (!$cim) { throw new \Exception("Kérjük, hogy adja meg az <strong>Oldal címét</strong>!"); }


		if (!$eleres) {
			$eleres = $this->checkEleres( $cim );
		} else {
			$eleres = Formater::makeSafeUrl($eleres,'');
		}

		$this->db->update(
			"oldalak",
			array(
				'cim' => $cim,
				'szulo_id' => $parent,
				'eleres' => $eleres,
				'szoveg' => $szoveg,
				'deep' => $deep,
				'idopont' => NOW,
				'lathato' => $lathato,
				'boritokep' => $boritokep,
				'kepek' => $kepek,
				'gyujto' => $gyujto,
				'sorrend' => $data['sorrend'],
				'hashkey' => $hashkey,
				'hashkey_keywords' => $hashkey_kw,
				'meta_title' => $meta_title,
				'meta_desc' => $meta_desc,
				'meta_image' => $meta_image,
			),
			sprintf("ID = %d", $this->selected_page_id)
		);
	}

	private function checkEleres( $text )
	{
		$text = Formater::makeSafeUrl($text,'');

		$qry = $this->db->query(sprintf("
			SELECT 		eleres
			FROM 		oldalak
			WHERE 		eleres = '%s' or
						eleres like '%s-_' or
						eleres like '%s-__'
			ORDER BY 	eleres DESC
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

	public function delete( $id = false )
	{
		$del_id = ($id) ?: $this->selected_page_id;

		if ( !$del_id ) return false;

		$this->db->query(sprintf("DELETE FROM oldalak WHERE ID = %d", $del_id));
	}

	/**
	 * Oldal fa kilistázása
	 * @param int $top_page_id Felső oldal ID meghatározása, nem kötelező. Ha nincs megadva, akkor
	 * az összes oldal fa listázódik.
	 * @return array Oldalak
	 */
	public function getTree( $top_page_id = false, $arg = array() )
	{
		$tree 		= array();

		// Legfelső színtű oldalak
		$qry = "
			SELECT 			*
			FROM 			oldalak
			WHERE 			ID IS NOT NULL ";

		if ( !$this->is_admin ) {
			$qry .= " and lathato = 1 ";
		}


		if ( !$top_page_id ) {
			$qry .= " and szulo_id IS NULL ";
		} else {
			$qry .= " and szulo_id = ".$top_page_id;
		}

		$qry .= "
			ORDER BY 		sorrend ASC;";

		$top_page_qry 	= $this->db->query($qry);
		$top_page_data 	= $top_page_qry->fetchAll(\PDO::FETCH_ASSOC);

		if( $top_page_qry->rowCount() == 0 ) return $this;

		foreach ( $top_page_data as $top_page ) {
			$this->tree_items++;
			$this->tree_steped_item[] = $top_page;

			// Aloldalak betöltése
			$top_page['child'] = $this->getChildItems($top_page['ID']);

			$tree[] = $top_page;
		}

		$this->tree = $tree;

		return $this;
	}

	public function has_page()
	{
		return ($this->tree_items === 0) ? false : true;
	}

	/**
	 * Végigjárja az összes oldalt, amit betöltöttünk a getTree() függvény segítségével. while php függvénnyel
	 * járjuk végig. A while függvényen belül használjuk a the_page() objektum függvényt, ami az aktuális oldal
	 * adataiat tartalmazza tömbbe sorolva.
	 * @return boolean
	 */
	public function walk()
	{
		if( !$this->tree_steped_item ) return false;

		$this->current_item = $this->tree_steped_item[$this->walk_step];

		$this->walk_step++;

		if ( $this->walk_step > $this->tree_items ) {
			// Reset Walk
			$this->walk_step = 0;
			$this->current_item = false;

			return false;
		}

		return true;
	}

	public function getWalkInfo()
	{
		return array(
			'walk_step' => $this->walk_step,
			'tree_steped_item' => $this->tree_steped_item,
			'tree_items' => $this->tree_items,
			'current_item' => $this->current_item,
		);
	}

	/**
	 * A walk() fgv-en belül visszakaphatjuk az aktuális oldal elem adatait tömbbe tárolva.
	 * @return array
	 */
	public function the_page()
	{
		return $this->current_item;
	}

	/**
	 * Oldal al-elemeinek listázása
	 * @param  int $parent_id 	Szülő oldal ID
	 * @return array 			Szülő oldal al-elemei
	 */
	private function getChildItems( $parent_id )
	{
		$tree = array();

		// Gyerek oldalak
		$child_page_qry 	= $this->db->query( sprintf("
			SELECT 			*
			FROM 			oldalak
			WHERE 			szulo_id = %d
			ORDER BY 		sorrend ASC;", $parent_id));
		$child_page_data	= $child_page_qry->fetchAll(\PDO::FETCH_ASSOC);

		if( $child_page_qry->rowCount() == 0 ) return false;
		foreach ( $child_page_data as $child_page ) {
			$this->tree_items++;

			$this->tree_steped_item[] = $child_page;

			$child_page['child'] = $this->getChildItems($child_page['ID']);

			$tree[] = $child_page;
		}

		return $tree;
	}

	public function textRewrites( $text )
	{
		$template 	= new Template ( VIEW . 'templates/' );
		$Tabledata 	= new Tabledata;

		// Kép
		$text = str_replace( '../../src/uploads/', UPLOADS, $text );

		// Includes
		$text = preg_replace_callback( "/==(.*)==/i", function ( $m ) use ( $template ) {
			return $template->get( $m[1]);
		} , $text );

		// Méret táblázat
		$text = preg_replace_callback("/##table-data:(.*)##/i", function( $m ) use ( $template, $Tabledata ) {
			$sizedata = array();
			$sizedata['key'] = $m[1];
			$sizedata['data'] = $Tabledata->getTable( $m[1] );

			return $template->get('size_data', $sizedata);
		}, $text);

		// Aloldal beszúrása
		if ( isset($_GET['page']) ) {
				$sub = $this->db->query(sprintf("SELECT szoveg FROM oldalak WHERE eleres = '%s';", $_GET['page']));

				if( $sub->rowCount() == 0 ) {
					$sub = false;
				} else {
					$sub = $sub->fetch(\PDO::FETCH_COLUMN);
					$sub = str_replace( '../../src/uploads/', UPLOADS, $sub );
				}

				$text = preg_replace_callback("/##aloldal##/i", function( $m ) use ( $sub ) {
					return $sub;
				}, $text);
		}else {
			$text = str_replace( "##aloldal##", "", $text);
		}

		return $text;
	}

	public function setAdmin( $flag )
	{
		$this->is_admin = $flag;
	}

	public function getTopParentId( $id )
	{
		$got_top = false;
		$top_id = false;

		if ( !$id ) {
			return false;
		}

		$step_id = $id;

		while ( !$got_top ) {
			$q = $this->db->query("SELECT szulo_id FROM oldalak WHERE ID = ".$step_id );
			$qq = $q->fetch(\PDO::FETCH_ASSOC);

			if ( is_null($qq['szulo_id']) ) {
				$got_top = true;
				$top_id = $step_id;
			} else {
				$step_id = $qq['szulo_id'];
			}
		}

		return $top_id;
	}

	/*===============================
	=            GETTERS            =
	===============================*/
	public function getParentId()
	{
		return $this->current_get_item['szulo_id'];
	}
	public function getImageSet()
	{
		$set = array();

		$kep =$this->current_get_item['kepek'];

		if ( !$kep ) {
			return $set;
		}

		$kep = rtrim($kep,";;");
		$kepek = explode(";;", $kep);

		foreach ( $kepek as $k ) {
			$i = $k;
			$set[] = $i;
		}

		return $set;
	}
	public function getMetaValue( $key )
	{
		return $this->current_get_item['meta_'.$key];
	}
	public function getMeta( $key = false )
	{
		$back = array();

		$set = array(
			'title' => array(
				'A' => 'meta_title',
				'B' => 'cim'
			),
			'desc' => array(
				'A' => 'meta_desc',
				'B' => 'szoveg'
			),
			'image' => array(
				'A' => 'meta_image',
				'B' => false
			));

		if ( !$key ) {
			return false;
		} else {
			if ( isset( $set[$key]['A'] ) ) {
				$a = $set[$key]['A'];
				$b = $set[$key]['B'];

				$qry = "SELECT " . $a . " FROM oldalak WHERE ID = ".(int)$this->getId();
				$qry = $this->db->query($qry);
				$qry = $qry->fetch(\PDO::FETCH_ASSOC);

				if ( !empty($qry[$a]) && !is_null($qry[$a]) ) {
					if ($key == 'image') {
						return str_replace('/src/','',SOURCE) . $qry[$a];
					} else {
						return $qry[$a];
					}
				} else {
					switch ($key) {
						case 'image':
							$back = IMG . 'no-image-meta.jpg';
						break;

						case 'desc':
							$qry = "SELECT " . $b . " FROM oldalak WHERE ID = ".(int)$this->getId();
							$qry = $this->db->query($qry);
							$qry = $qry->fetch(\PDO::FETCH_ASSOC);
							if (!empty($qry[$b])) {
								$desc = substr(strip_tags($qry[$b]),0,300).'...';
								//$desc = preg_replace('/\s+/', '', $desc);
								return $desc;
							} else return false;
						break;

						default:
							$qry = "SELECT " . $b . " FROM oldalak WHERE ID = ".(int)$this->getId();
							$qry = $this->db->query($qry);
							$qry = $qry->fetch(\PDO::FETCH_ASSOC);
							if (!empty($qry[$b])) {
								return $qry[$b];
							} else return false;
						break;
					}
				}
			} else {
				$back = false;
			}
		}

		return $back;
	}
	public function getParentKey()
	{
		return $this->current_get_item['szulo_id'].'_'.($this->current_get_item['deep']-1);
	}
	public function getOrderIndex()
	{
		return $this->current_get_item['sorrend'];
	}
	public function getDeepIndex()
	{
		return $this->current_get_item['deep'];
	}
	public function getId()
	{
		return $this->current_get_item['ID'];
	}
	public function getHashkey()
	{
		return $this->current_get_item['hashkey'];
	}
	public function getHashkeyKeywords()
	{
		return $this->current_get_item['hashkey_keywords'];
	}
	public function getTitle()
	{
		return $this->current_get_item['cim'];
	}
	public function getUrl()
	{
		return $this->current_get_item['eleres'];
	}
	public function getHtmlContent()
	{
		return $this->current_get_item['szoveg'];
	}
	public function getVisibility()
	{
		return ($this->current_get_item['lathato'] == 1 ? true : false);
	}

	public function getCoverImg()
	{
		return ($this->current_get_item['boritokep'] != '' ? $this->current_get_item['boritokep'] : false);
	}

	public function isContainer()
	{
		return ($this->current_get_item['gyujto'] == 1 ? true : false);
	}
	/*-----  End of GETTERS  ------*/
}
?>
