<?php
use PortalManager\Projects;
use PortalManager\Documents;

class projektek extends Controller
{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = __('Projektek');

			$this->addPagePagination(array(
				'link' => false,
				'title' => parent::$pageTitle
			));

			if ( !$this->view->_USERDATA ) {
        \Helper::reload('/');
      }

      if ($this->gets[1] == '') {
        \Helper::reload('/'.__CLASS__.'/aktualis');
      }

			$uid = $this->view->_USERDATA['data']['ID'];
		}

    public function projekt()
    {
      parent::$pageTitle = __('Projekt adatlap');

      $this->addPagePagination(array(
        'link' => '/'.__CLASS__.'/'.__FUNCTION__,
        'title' => parent::$pageTitle
      ));

      $hashkey = $this->gets[2];
			$uid = $this->view->_USERDATA['data']['ID'];

			// Hozzáfárás ellenőrzése
			$projects = new Projects(array('db' => $this->db));
			$acceptpermission = $projects->validateProjectPermission( $hashkey, $this->view->_USERDATA['data']['ID'] );

			if ( $acceptpermission !== true )
			{
				\Helper::reload('/'.__CLASS__.'/aktualis');
			}

			$projectdata = $projects->getProjectData( $hashkey, $uid );

			$outputdocs = array();
			$docs = new Documents(array('db' => $this->db));

			// Díjbekérők
			$folderhash = $docs->findFolderHashkey('dijbekero', $uid);
			$folderinfo = $docs->getFolderData($folderhash);

			$outputdocs['requester']['dijbekero'] = $docs->getList(array(
				'limit' => 9999,
				'in_project' => $projectdata['order_project_hashkeys']['requester'],
				'order' => 'xp.added_at DESC',
				'folder' => $folderinfo['ID'],
				'expire_qry' => '<= now()',
				'teljesites_qry' => 'IS NULL'
			));
			$outputdocs['servicer']['dijbekero'] = $docs->getList(array(
				'limit' => 9999,
				'in_project' => $projectdata['order_project_hashkeys']['servicer'],
				'order' => 'xp.added_at DESC',
				'folder' => $folderinfo['ID'],
				'expire_qry' => '<= now()',
				'teljesites_qry' => 'IS NULL'
			));

			// Számlák
			$folderhash = $docs->findFolderHashkey('szamla', $uid);
			$folderinfo = $docs->getFolderData($folderhash);

			$outputdocs['requester']['szamla'] = $docs->getList(array(
				'limit' => 9999,
				'in_project' => $projectdata['order_project_hashkeys']['requester'],
				'order' => 'xp.added_at DESC',
				'folder' => $folderinfo['ID']
			));
			$outputdocs['servicer']['szamla'] = $docs->getList(array(
				'limit' => 9999,
				'in_project' => $projectdata['order_project_hashkeys']['servicer'],
				'order' => 'xp.added_at DESC',
				'folder' => $folderinfo['ID']
			));

			// Dokumentumok
			$outputdocs['requester']['all'] = $docs->getList(array(
				'limit' => 10,
				'in_project' => $projectdata['order_project_hashkeys']['requester'],
				'order' => 'xp.added_at DESC'
			));
			$outputdocs['servicer']['all'] = $docs->getList(array(
				'limit' => 10,
				'in_project' => $projectdata['order_project_hashkeys']['servicer'],
				'order' => 'xp.added_at DESC'
			));

			/*
			echo '<pre>';
			print_r($projectdata);
			echo '</pre>';
			*/

			$this->out('doc', $outputdocs);
    }

    public function aktualis()
    {
    	parent::$pageTitle = __('Aktív projektek');

      $this->addPagePagination(array(
        'link' => '/'.__CLASS__.'/'.__FUNCTION__,
				'title' => parent::$pageTitle
      ));

      $projects = new Projects(array('db' => $this->db));
			$listarg['uid'] = $this->view->_USERDATA['data']['ID'];

			$listarg['closed'] = 0;
      $this->out( 'projects', $projects->getList( $listarg ));
    }

    public function lezart()
    {
    	parent::$pageTitle = __('Lezárt projektek');

      $this->addPagePagination(array(
        'link' => '/'.__CLASS__.'/'.__FUNCTION__,
				'title' => parent::$pageTitle
      ));

			$projects = new Projects(array('db' => $this->db));
			$listarg = array();
			$listarg['uid'] = $this->view->_USERDATA['data']['ID'];
			$listarg['closed'] = 1;
			$this->out( 'projects', $projects->getList( $listarg ));
    }

		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>
