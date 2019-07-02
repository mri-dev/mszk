<?php
use PortalManager\Documents;
use PortalManager\Pagination;

class dokumentumok extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = __('Dokumentumok');

			$this->addPagePagination(array(
				'link' => '/'.__CLASS__,
				'title' => parent::$pageTitle
			));

			// Ha nincs belépve, akkor átirányít a bejelentkezésre
			if ( !$this->Users->user && $this->gets[0] != 'belepes' && $this->gets[0] != 'regisztracio'  && $this->gets[0] != 'delete') {
				Helper::reload('/belepes');
			}

      if ($this->gets[1] != '' && !is_numeric($this->gets[1])) {
        $doc_type = $this->gets[1];
        parent::$pageTitle = $doc_type . ' | ' . parent::$pageTitle;
      }

      if ($doc_type && $doc_type != 'folders' && $doc_type != 'hozzaad' && $doc_type != 'szerkeszt' && $doc_type != 'delete') {
        $this->addPagePagination(array(
  				'link' => '/'.__CLASS__.'/'.$doc_type,
  				'title' => $doc_type
  			));
				$doc_type = false;
      }

			$uid = $this->view->_USERDATA['data']['ID'];

			$this->docs = new Documents(array('db' => $this->db));
			$folderhash = $this->docs->findFolderHashkey($this->gets[1], $uid);
			$docs_folders = $this->docs->getAvaiableFolders( $uid );
			$this->out('folders', $docs_folders);
			$this->out('folderinfo', $this->docs->getFolderData($folderhash));

			// Dokumentum lista
			$arg = array();
			$arg['uid'] = $uid;
			$arg['limit'] = 25;
			$arg['page'] = (is_numeric(\Helper::getLastParam())) ? (int)\Helper::getLastParam() : 1;

			if ($this->view->folderinfo)
			{
				$arg['folder'] = (int)$this->view->folderinfo['ID'];
			}

			if (isset($_GET['name']) && !empty($_GET['name']))
			{
				$arg['search']['name'] = $_GET['name'];
			}

			$docs = $this->docs->getList( $arg );
			$this->out('docs', $docs);

			// Pagination
			$get = $_GET;
			$root = '/'.__CLASS__;
			unset($get['tag']);
			$get = http_build_query($get);
			$this->out( 'cget', $get );
			$this->out( 'navigator', (new Pagination(array(
				'class' => 'pagination pagination-sm center',
				'current' => $docs['pages']['current'],
				'max' => $docs['pages']['max'],
				'root' => $root,
				'after' => ( $get ) ? '?'.$get : '',
				'item_limit' => 12
			)))->render() );

			if ($this->view->folderinfo)
			{
				parent::$pageTitle = $this->view->folderinfo['name'];
				$this->addPagePagination(array(
					'link' => false,
					'title' => parent::$pageTitle
				));
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

		public function folders()
		{
			$uid = $this->view->_USERDATA['data']['ID'];

			// Létrehozás
			if ( $_GET['mode'] == 'create' )
			{
				parent::$pageTitle = __('Új mappa létrehozása');
				$this->addPagePagination(array(
					'link' => '/'.__CLASS__,
					'title' => parent::$pageTitle
				));

				// Új csoport
				if( isset($_POST['addFolder']) )
				{
					try {
						$this->docs->addFolder( $uid, $_POST );
						Helper::reload('/dokumentumok');
					} catch ( Exception $e ) {
						$this->view->err	= true;
						$this->view->bmsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
					}
				}
			}

			// Módosítás
			if ( $_GET['mode'] == 'edit' )
			{
				$folder = $this->docs->getFolderData($_GET['folder']);
				$permission = $this->docs->checkFolderEditPermission( $folder['hashkey'], $uid );
				if ( !$permission ) {
					Helper::reload('/dokumentumok');
				}
				parent::$pageTitle = '"'.$folder['name'].'" '.__('mappa szerkesztése');
				$this->addPagePagination(array(
					'link' => false,
					'title' => parent::$pageTitle
				));
				$this->out('folder', $folder);

				// Csoport szerkesztés
				if( isset($_POST['saveFolder']) )
				{
					try {
						$this->docs->saveFolder( $folder['hashkey'], $_POST, $uid );
						Helper::reload('/dokumentumok');
					} catch ( Exception $e ) {
						$this->view->err = true;
						$this->view->bmsg = Helper::makeAlertMsg('pError', $e->getMessage());
					}
				}
			}

			// Törlés
			if ( $_GET['mode'] == 'delete' )
			{
				$folder = $this->docs->getFolderData($_GET['folder']);
				$permission = $this->docs->checkFolderEditPermission( $folder['hashkey'], $uid );
				if ( !$permission ) {
					Helper::reload('/dokumentumok');
				}
				parent::$pageTitle = '"'.$folder['name'].'" '.__('mappa törlése');
				$this->addPagePagination(array(
					'link' => false,
					'title' => parent::$pageTitle
				));
				$this->out('folder', $folder);

				// Csoport szerkesztés
				if( isset($_POST['deleteFolder']) )
				{
					try {
						$this->docs->deleteFolder( $folder['hashkey'] );
						Helper::reload('/dokumentumok');
					} catch ( Exception $e ) {
						$this->view->err = true;
						$this->view->bmsg = Helper::makeAlertMsg('pError', $e->getMessage());
					}
				}
			}
		}

		public function hozzaad()
		{
			$uid = $this->view->_USERDATA['data']['ID'];

			parent::$pageTitle = __('Új dokumentum hozzáadása');
			$this->addPagePagination(array(
				'link' => false,
				'title' => parent::$pageTitle
			));

			if( isset($_POST['addFile']) )
			{
				try {
					$this->docs->addFile( $uid, $_POST );
					Helper::reload('/dokumentumok');
				} catch ( Exception $e ) {
					$this->view->err = true;
					$this->view->bmsg = Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

		}

		public function szerkeszt()
		{
			$uid = $this->view->_USERDATA['data']['ID'];

			$arg = array();
			$arg['uid'] = $uid;
			$arg['get'] = $this->gets[2];

			$docs = $this->docs->getList( $arg );
			$this->out('doc', $docs);

			if ( !$this->view->is_admin_logged && $docs['user_id'] != $uid ) {
				Helper::reload('/dokumentumok');
			}

			parent::$pageTitle =$docs['name']. ' | '. __('Dokumentum szerkesztés');
			$this->addPagePagination(array(
				'link' => false,
				'title' =>__('Szerkesztés')
			));

			$this->addPagePagination(array(
				'link' => false,
				'title' => $docs['name']
			));

			if( isset($_POST['editFile']) )
			{
				try {
					$this->docs->editFile( $uid, $_POST );
					Helper::reload('/dokumentumok');
				} catch ( Exception $e ) {
					$this->view->err = true;
					$this->view->bmsg = Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

		}

		public function delete()
		{
			$uid = $this->view->_USERDATA['data']['ID'];

			$arg = array();
			$arg['uid'] = $uid;
			$arg['get'] = $this->gets[2];

			$docs = $this->docs->getList( $arg );
			$this->out('doc', $docs);

			if ( !$this->view->is_admin_logged && $docs['user_id'] != $uid ) {
				Helper::reload('/dokumentumok');
			}

			parent::$pageTitle =$docs['name']. ' | '. __('Dokumentum törlése');
			$this->addPagePagination(array(
				'link' => false,
				'title' =>__('Törlés')
			));

			$this->addPagePagination(array(
				'link' => false,
				'title' => $docs['name']
			));

			if( isset($_POST['deleteFile']) )
			{
				try {
					$this->docs->deleteFile( $uid, $_POST );
					Helper::reload('/dokumentumok');
				} catch ( Exception $e ) {
					$this->view->err = true;
					$this->view->bmsg = Helper::makeAlertMsg('pError', $e->getMessage());
				}
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
