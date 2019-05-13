<?php
use PortalManager\Categories;
use PortalManager\Category;

class adminconsole extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = __('Adminisztráció');

			$this->addPagePagination(array(
				'link' => false,
				'title' => parent::$pageTitle
			));

			// Ha nincs belépve, akkor átirányít a bejelentkezésre
			if ( !$this->Users->user && $this->gets[0] != 'belepes' && $this->gets[0] != 'regisztracio') {
				Helper::reload('/belepes');
			}

			// Jogkör vizsgálat
			if (
				$this->view->_USERDATA &&
				( $this->view->_USERDATA['data']['user_group'] != \PortalManager\Users::USERGROUP_ADMIN && $this->view->_USERDATA['data']['user_group'] != \PortalManager\Users::USERGROUP_SUPERADMIN)
			) {
					Helper::reload('/');
			}

			// SEO Információk
			$SEO = null;
			// Site info
			$SEO .= $this->view->addMeta('description','');
			$SEO .= $this->view->addMeta('keywords','');
			$SEO .= $this->view->addMeta('revisit-after','3 days');

			// FB info
			$SEO .= $this->view->addOG('type','website');
			$SEO .= $this->view->addOG('url',DOMAIN);
			$SEO .= $this->view->addOG('image',DOMAIN.substr(IMG,1).'noimg.jpg');
			$SEO .= $this->view->addOG('site_name',TITLE);

			$this->view->SEOSERVICE = $SEO;
		}

    /**
    * /adminconsole/felhasznalok - Felhasználók Controller
    **/
    public function felhasznalok()
    {
      parent::$pageTitle = __('Felhasználók');
      $this->addPagePagination(array(
				'link' => '/'.__CLASS__.'/'.__FUNCTION__,
				'title' => parent::$pageTitle
			));

    }
    /**
    * /adminconsole/list - Dinamikus listák Controller
    **/
    public function lists()
    {
      parent::$pageTitle = __('Dinamikus listák');

			// Group szűrő a listánál
			if (isset($_GET['filtergroup'])) {
				setcookie('filtergroup', $_GET['filtergroup'], time() + 3600 * 24, '/adminconsole');
				Helper::reload('/adminconsole/lists/');
			}
			// Group szűrő eltávolítás
			if (isset($_GET['clearfiltergroup'])) {
				setcookie('filtergroup', null, time() - 3600, '/adminconsole');
				Helper::reload('/adminconsole/lists/');
			}

			// Listák
			$lists = new Categories( array( 'db' => $this->db ) );
			$lists_groups = $lists->getGroups();

			// Group adat
			if (isset($_GET['groupcreator'])) {
				$group = $lists->getGroup( $_GET['id'] );
			}

			// Új lista
			if( isset($_POST['addCategory']) )
			{
				try {
					$lists->add( $_POST );
					Helper::reload();
				} catch ( Exception $e ) {
					$this->view->err	= true;
					$this->view->bmsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Új csoport
			if( isset($_POST['addGroup']) )
			{
				try {
					$lists->addGroup( $_POST );
					Helper::reload();
				} catch ( Exception $e ) {
					$this->view->err	= true;
					$this->view->bmsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Szerkesztés
			if ( $_GET['creator'] == 'edit') {
				// Kategória adatok
				$list_data = new Category( $_GET['id'],  array( 'db' => $this->db )  );
				$this->out( 'list', $list_data );

				// Változások mentése
				if(isset($_POST['saveCategory']) )
				{
					try {
						$lists->edit( $list_data, $_POST );
						Helper::reload();
					} catch ( Exception $e ) {
						$this->view->err	= true;
						$this->view->bmsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
					}
				}
			}

			// Csoport szerkesztés
			if ( $_GET['groupcreator'] == 'edit') {
				// Változások mentése
				if(isset($_POST['saveGroup']) )
				{
					try {
						$lists->editGroup( $group, $_POST );
						Helper::reload();
					} catch ( Exception $e ) {
						$this->view->err	= true;
						$this->view->bmsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
					}
				}
			}

			// Törlés
			if ( $_GET['creator'] == 'delete') {
				// Lista adatok
				$list_data = new Category( $_GET['id'], array( 'db' => $this->db )  );
				$this->out( 'list_d', $list_data );

				// Kategória törlése
				if( isset($_POST['deleteCategory']) )
				{
					try {
						$lists->delete( $list_data );
						Helper::reload( '/adminconsole/lists/' );
					} catch ( Exception $e ) {
						$this->view->err	= true;
						$this->view->bmsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
					}
				}
			}

			// Csoport törlés
			if ( $_GET['groupcreator'] == 'delete') {
				if( isset($_POST['deleteGroup']) )
				{
					try {
						$lists->deleteGroup( $group );
						Helper::reload( '/adminconsole/lists/' );
					} catch ( Exception $e ) {
						$this->view->err	= true;
						$this->view->bmsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
					}
				}
			}

			// LOAD
			////////////////////////////////////////////////////////////////////////////////////////
			$arg = array();
			$filtergroup = (isset($_COOKIE['filtergroup'])) ? (int)$_COOKIE['filtergroup'] : false;
			if ($filtergroup) {
				$arg['group_id'] = $filtergroup;
				$selected_group = $lists->getGroup( $filtergroup );
			}
			$list_tree 	= $lists->getTree( false, $arg );
			// Lista elemek
			$this->out( 'lists', $list_tree );
			// Lista csoportok
			$this->out( 'groups', $lists_groups );
			// Csoport adat
			$this->out( 'group', $group );
			// Szűrt csoport
			$this->out( 'selected_group', $selected_group );

			if ($filtergroup) {
				parent::$pageTitle = $selected_group['neve'].' - lista elemek';
				$this->addPagePagination(array(
				 'link' => '/'.__CLASS__.'/'.__FUNCTION__.'/?clearfiltergroup=1',
				 'title' => parent::$pageTitle
			 ));
			 $this->addPagePagination(array(
				'link' => '/'.__CLASS__.'/'.__FUNCTION__,
				'title' => '<i class="fas fa-filter"></i> '.$selected_group['neve'].' elemek'
			));
			} else {
	      $this->addPagePagination(array(
					'link' => '/'.__CLASS__.'/'.__FUNCTION__,
					'title' => parent::$pageTitle
				));
			}
    }

		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>
