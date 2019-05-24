<?php

class profil extends Controller {
		function __construct(){
			parent::__construct();
			parent::$pageTitle = __('Profilom');

			$this->addPagePagination(array(
				'link' => '/'.__CLASS__,
				'title' => parent::$pageTitle
			));

      // Felhasználó adatok mentése
      if (isset($_POST['saveProfil'])) {
  			try {
  				$this->Users->saveProfil($this->view->_USERDATA['data']['ID'] ,$_POST );
  				Helper::reload('/'.__CLASS__.'?&msgkey=msg&msg='.__('Sikeresen mentette az adatait!'));
  			} catch (\Exception $e) {
  				$this->view->err 	= true;
  				$this->view->msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
  			}
  		}

      // Belépett saját felhasználó adatok
      $this->out('data', $this->view->_USERDATA['data']);

      // Jelszó csere
      if (isset($_POST['changePassword'])) {
        try {
          $this->Users->changePassword($this->view->_USERDATA['data']['ID'] ,$_POST['passchange'] );
          Helper::reload('/'.__CLASS__.'?&msgkey=msg&msg='.__('Sikeresen lecserélte a fiók jelszavát!'));
        } catch (\Exception $e) {
          $this->view->err 	= true;
          $this->view->msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
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
