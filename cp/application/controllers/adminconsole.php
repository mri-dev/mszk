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
      $this->addPagePagination(array(
				'link' => '/'.__CLASS__.'/'.__FUNCTION__,
				'title' => parent::$pageTitle
			));

			// Listák
			$lists = new Categories( array( 'db' => $this->db ) );
			$lists_groups = $lists->getGroups();


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


			// LOAD
			////////////////////////////////////////////////////////////////////////////////////////
			$list_tree 	= $lists->getTree();
			// Lista elemek
			$this->out( 'lists', $list_tree );
			// Lista csoportok
			$this->out( 'groups', $lists_groups );

    }

		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>
