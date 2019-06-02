<?php

class ajanlatkeresek extends Controller {
		function __construct(){
			parent::__construct();
			parent::$pageTitle = __('Ajánlat kérések');

			$this->addPagePagination(array(
				'link' => '/'.__CLASS__,
				'title' => parent::$pageTitle
			));
			if ( !$this->view->_USERDATA ) {
        \Helper::reload('/');
      }
		}

    public function feldolgozatlan()
    {
      // Jogkör vizsgálat - Csak admin szintűek
      if (
        $this->view->_USERDATA &&
        ( $this->view->_USERDATA['data']['user_group'] != \PortalManager\Users::USERGROUP_ADMIN && $this->view->_USERDATA['data']['user_group'] != \PortalManager\Users::USERGROUP_SUPERADMIN)
      ) {
          Helper::reload('/'.__CLASS__);
      }

			parent::$pageTitle = __('Feldolgozatlan ajánlat kérések');

      $this->addPagePagination(array(
        'link' => '/'.__CLASS__.'/'.__FUNCTION__,
				'title' => parent::$pageTitle
      ));
    }

		public function feldolgozott()
    {
      // Jogkör vizsgálat - Csak admin szintűek
      if (
        $this->view->_USERDATA &&
        ( $this->view->_USERDATA['data']['user_group'] != \PortalManager\Users::USERGROUP_ADMIN && $this->view->_USERDATA['data']['user_group'] != \PortalManager\Users::USERGROUP_SUPERADMIN)
      ) {
          Helper::reload('/'.__CLASS__);
      }

			parent::$pageTitle = __('Feldolgozott, kiküldött ajánlatok');

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
