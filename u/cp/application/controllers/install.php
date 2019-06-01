<?php
use PortalManager\Installer;

class install extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = 'Modul telepítő';

			$this->view->adm = $this->AdminUser;
			$this->view->adm->logged = $this->AdminUser->isLogged();

      $module = $_GET['module'];

      // Check modules
      $module_support = $this->db->query("SHOW TABLES LIKE 'modules'")->fetchColumn();

      if ( $module_support === false ) {
        $this->out( 'unnable_install_module', true );
      } else {
        if ( !empty($module) && !class_exists($module, false) )
        {
          $x = explode("\\", $module);
          $inc = LIBS. $x[0].'/'.$x[1].'.php';
          $exists = file_exists($inc);

          if ( $exists )
          {
            include_once $inc;
            $installer = new $module( array( 'db' => $this->db ) );

            if (method_exists( $installer, 'installer')) {
              $installer_method_exists = true;

              // Check
              $cmodule = addslashes($module);
              $im = $this->db->query( $iq = "SELECT * FROM modules WHERE classname = '{$cmodule}'");
              if($im->rowCount() == 0){
                $module_installed = false;

                if (isset($_POST['installModul'])) {
                  $this->out( 'installing', true );
                  $state = $installer->installer( (new Installer(array( 'db' => $this->db ))) );

                  $state = ($state) ? 1 : 0;

                  \Helper::reload('/install?module=PortalManager\Vehicles&installed='.$state); exit;
                }
              } else {
                $module_installed = true;
                $module_data = $im->fetch(\PDO::FETCH_ASSOC);
                $module_active = ($module_data['active'] == 1) ? true : false;
              }
            } else {
              $installer_method_exists = false;
            }
          }

          $this->out( 'modultitle', $installer::MODULTITLE );
          $this->out( 'exists', $exists );
          $this->out( 'module_installed', $module_installed );
          $this->out( 'module_active', $module_active );
          $this->out( 'installer_exists', $installer_method_exists );
        } else {
          \Helper::reload('/'); exit;
        }
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

		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>
