<?php
use PortalManager\OfferRequests;

class ajanlatkeresek extends Controller {
		function __construct(){
			parent::__construct();
			parent::$pageTitle = __('Ajánlat kérések');

			$this->addPagePagination(array(
				'link' => '/'.__CLASS__,
				'title' => parent::$pageTitle
			));
			if ( !$this->view->_USERDATA ) {
        \Helper::reload('/belepes/?return='.$_SERVER['REQUEST_URI']);
      }

			$uid = $this->view->_USERDATA['data']['ID'];

			// még nem feldolgozott kéréseim
			$notoffered_requests_arg = array();
			$notoffered_requests_qry = "SELECT ID FROM requests WHERE elutasitva = 0 and offerout = 0";
			if ( !$this->view->is_admin_logged ) {
				$notoffered_requests_qry .= " and user_id = :uid";
				$notoffered_requests_arg['uid'] = $uid;
			}
			$notoffered_requests = $this->db->squery($notoffered_requests_qry, $notoffered_requests_arg)->rowCount();
			$this->out('notoffered_requests', $notoffered_requests);

			$offerrequests = new OfferRequests(array('db' => $this->db));

			if ($notoffered_requests != 0)
			{
				$arg = array('offerout' => 0, 'elutasitva' => 0, 'servicetree' => true, 'shortlist' => true);
				if ( !$this->view->is_admin_logged ) {
					$arg['user'] = $uid;
				}
				$requests = $offerrequests->getList( $arg );
				$this->out('requests', $requests);
			}

		}

		public function bejovo()
		{
			parent::$pageTitle = __('Bejövő ajánlatkérések');
			$this->addPagePagination(array(
				'link' => '/'.__CLASS__.'/'.__FUNCTION__,
				'title' => parent::$pageTitle
			));
		}

		public function kimeno()
		{
			parent::$pageTitle = __('Kimenő ajánlatkérések');
			$this->addPagePagination(array(
				'link' => '/'.__CLASS__.'/'.__FUNCTION__,
				'title' => parent::$pageTitle
			));
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

		public function letrehozhato()
    {
      // Jogkör vizsgálat - Csak admin szintűek
      if (
        $this->view->_USERDATA &&
        ( $this->view->_USERDATA['data']['user_group'] != \PortalManager\Users::USERGROUP_ADMIN && $this->view->_USERDATA['data']['user_group'] != \PortalManager\Users::USERGROUP_SUPERADMIN)
      ) {
          Helper::reload('/'.__CLASS__);
      }

			parent::$pageTitle = __('Létrehozható kérések');

      $this->addPagePagination(array(
        'link' => '/'.__CLASS__.'/'.__FUNCTION__,
				'title' => parent::$pageTitle
      ));
    }

		public function letrejott()
    {
      // Jogkör vizsgálat - Csak admin szintűek
      if (
        $this->view->_USERDATA &&
        ( $this->view->_USERDATA['data']['user_group'] != \PortalManager\Users::USERGROUP_ADMIN && $this->view->_USERDATA['data']['user_group'] != \PortalManager\Users::USERGROUP_SUPERADMIN)
      ) {
          Helper::reload('/'.__CLASS__);
      }

			parent::$pageTitle = __('Létrejött kérések');

      $this->addPagePagination(array(
        'link' => '/'.__CLASS__.'/'.__FUNCTION__,
				'title' => parent::$pageTitle
      ));
    }

		public function ajanlat_elkuldve()
		{
			parent::$pageTitle = __('Feldolgozott ajánlat kérések');
			$this->addPagePagination(array(
				'link' => '/'.__CLASS__.'/'.__FUNCTION__,
				'title' => parent::$pageTitle
			));
		}

		public function fuggoben()
		{
			parent::$pageTitle = __('Függőben lévő ajánlat kérések');
			$this->addPagePagination(array(
				'link' => '/'.__CLASS__.'/'.__FUNCTION__,
				'title' => parent::$pageTitle
			));
		}

		public function elfogadott()
		{
			parent::$pageTitle = __('Elfogadott ajánlat kérések');
			$this->addPagePagination(array(
				'link' => '/'.__CLASS__.'/'.__FUNCTION__,
				'title' => parent::$pageTitle
			));
		}

		public function osszes()
		{
			parent::$pageTitle = __('Összes ajánlat kérések');
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
