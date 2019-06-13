<?php
use PortalManager\Projects;

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
      parent::$pageTitle = __('Projekt');

      $this->addPagePagination(array(
        'link' => '/'.__CLASS__.'/'.__FUNCTION__,
        'title' => parent::$pageTitle
      ));

      $hashkey = $this->gets[2];
    }

    public function aktualis()
    {
    	parent::$pageTitle = __('Aktív projektek');

      $this->addPagePagination(array(
        'link' => '/'.__CLASS__.'/'.__FUNCTION__,
				'title' => parent::$pageTitle
      ));

      $projects = new Projects(array('db' => $this->db));
      $listarg = array();
      $this->out( 'projects', $projects->getList( $listarg ));
    }

    public function lezart()
    {
    	parent::$pageTitle = __('Lezárt projektek');

      $this->addPagePagination(array(
        'link' => '/'.__CLASS__.'/'.__FUNCTION__,
				'title' => parent::$pageTitle
      ));
    }



		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>
