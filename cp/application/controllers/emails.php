<?
use MailManager\MailTemplates;

class emails extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = __('E-mail sablonok');

      $this->addPagePagination(array(
        'link' => '/'.__CLASS__,
        'title' => parent::$pageTitle
      ));

      if ( !$this->view->_USERDATA ) {
        \Helper::reload('/');
      }

      // Jogkör vizsgálat - Csak admin szintűek
      if (
        $this->view->_USERDATA &&
        ( $this->view->_USERDATA['data']['user_group'] != \PortalManager\Users::USERGROUP_ADMIN && $this->view->_USERDATA['data']['user_group'] != \PortalManager\Users::USERGROUP_SUPERADMIN)
      ) {
          Helper::reload('/'.__CLASS__);
      }

			$mailtemplates = new MailTemplates(array('db'=>$this->db));
			$this->out('mails', $mailtemplates->getList());
		}

		public function edit()
		{
			$mailtemplate = (new MailTemplates(array('db'=>$this->db)))->load($this->gets[2]);
			$this->out( 'mail', $mailtemplate->getData() );

      parent::$pageTitle = __($this->view->mail['cim']);

			if (isset($_POST['saveEmail']) )
			{
				try {
					$mailtemplate->save( $this->gets[2], $_POST['data'] );
					Helper::reload( );
				} catch (\Exception $e) {
					$this->view->msg = Helper::makeAlertMsg( 'pError', $e->getMessage() );
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
