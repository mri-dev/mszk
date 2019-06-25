<?php
use PortalManager\Documents;

class dokumentumok extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = __('Dokumentumok');

			$this->addPagePagination(array(
				'link' => '/'.__CLASS__,
				'title' => parent::$pageTitle
			));
			// Ha nincs belépve, akkor átirányít a bejelentkezésre
			if ( !$this->Users->user && $this->gets[0] != 'belepes' && $this->gets[0] != 'regisztracio') {
				Helper::reload('/belepes');
			}

      if ($this->gets[1] != '') {
        $doc_type = $this->gets[1];
        parent::$pageTitle = $doc_type . ' | ' . parent::$pageTitle;
      }

      if ($doc_type && $doc_type != 'folders' && $doc_type != 'hozzaad') {
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

			if ($this->view->folderinfo) {
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

		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>
