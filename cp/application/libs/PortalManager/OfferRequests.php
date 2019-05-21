<?
namespace PortalManager;

/**
* class OfferRequests
* @package PortalManager
* @version 1.0
*/
class OfferRequests
{
	private $db = null;

	function __construct( $arg = array() )
	{
		$this->db = $arg[db];
		return $this;
	}

  public function sendRequest( $requester, $config )
  {
    // Ellenőrzés
    if (empty($requester['name'])) {
      throw new \Exception(__('Kérjük, hogy adja meg a saját nevét!'));
    }

    if (empty($requester['email'])) {
      throw new \Exception(__('Kérjük, hogy adja meg a saját e-mail címét!'));
    }

    if (empty($requester['phone'])) {
      throw new \Exception(__('Kérjük, hogy adja meg a telefonszámát!'));
    }

    if (empty($requester['aszf'])) {
      throw new \Exception(__('Az ajánlatkérés elküldéséhez kötelezően el kell fogadni az Általános Szerződési Feltételeket.'));
    }

    if (empty($requester['adatvedelem'])) {
      throw new \Exception(__('Az ajánlatkérés elküldéséhez kötelezően hozzá kell járulni az adatai kezeléséhez, melyről az Adatvédelmi Tájékoztatótban tájékozódhat.'));
    }


    // Gyors felh. fiók regisztráció


    // Igény regisztrálása

    // E-mail - Igénylő értesítő
    // E-mail - Admin értesítő
  }

	public function __destruct()
	{
		$this->db = null;
	}

}
?>
