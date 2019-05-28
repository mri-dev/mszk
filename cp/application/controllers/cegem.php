<?php
use PortalManager\Categories;

class cegem extends Controller {
		function __construct(){
			parent::__construct();
			parent::$pageTitle = __('Cégem beállításai');

			$this->addPagePagination(array(
				'link' => '/'.__CLASS__,
				'title' => parent::$pageTitle
			));

      // Ha nem szolgáltató, akkor átirányítás - nincs joga megtekinteni az oldal
      if ( $this->view->_USERDATA && $this->view->_USERDATA['data']['user_group'] != \PortalManager\Users::USERGROUP_SERVICES ) {
        \Helper::reload('/');
      }

      // Szolgáltató beállítások mentése
      if (isset($_POST['changeCompanyServices'])) {
        try {
          $this->Users->saveProfilServices($this->view->_USERDATA['data'], $_POST['services'] );
          Helper::reload('/'.__CLASS__.'?&msgkey=msg&msg='.__('Sikeresen mentette a szolgáltatás beállításait!'));
        } catch (\Exception $e) {
          $this->view->err 	= true;
          $this->view->msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
        }
      }

      // Listák
			$lists = new Categories( array( 'db' => $this->db ) );
      $arg = array();
      $arg['group_id'] = 1;
			$list_tree 	= $lists->getTree( false, $arg );
      // Lista elemek
  		$this->out( 'lists', $list_tree->tree );
			// Felhasználó cég szolgáltatások
			$this->out( 'user_services', $this->Users->getUserServices($this->view->_USERDATA['data']['ID'], $this->view->_USERDATA['data']['user_group']));

		}

		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>
