<?
use PortalManager\Redirector;

class PageNotFound extends Controller
{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = 'Az oldal nem található!';

			$this->redirector  = new Redirector('shop', $_GET["tag"], array( 'db' => $this->db ));
			$this->redirector->start();

			if ( $this->redirector->hasRedirect() )
			{
				header( "HTTP/1.1 301 Moved Permanently" );
				header( "Location: ".$this->redirector->redirect() );
				exit;

			}
			header("HTTP/1.0 404 Not Found");
		}

		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}
?>
