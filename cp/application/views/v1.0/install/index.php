<h1>Modul telepítő platform</h1>
<?php if ($this->unnable_install_module): ?>
  <div class="alert alert-danger">
    <i class="fa fa-times"></i> A szoftver nem támogatja a modul telepítés funkciót. Vegye fel a kapcsolatot a fejlesztővel.
  </div>
<?php else: ?>
  <?php if ($this->exists): ?>
    <?php if ($this->installing): ?>
      <div class="alert alert-danger">
        <i class="fa fa-spin fa-spinner"></i> A(z) <strong><?=$this->modultitle?> (<?=$_GET['module']?>)</strong> modul telepítése folyamatban. Kis türelmét kérjük.
      </div>
    <?php else: ?>
      <?php if ($this->module_installed): ?>
        <div class="alert alert-success">
          <i class="fa fa-check-circle"></i> A(z) <strong><?=$this->modultitle?> (<?=$_GET['module']?>)</strong> modul telepítve. Használatra készen.
        </div>
        <?php if ($this->module_active): ?>
          <div class="alert alert-success">
            <i class="fa fa-check-circle"></i> A(z) <strong><?=$this->modultitle?> (<?=$_GET['module']?>)</strong> modul aktív.
          </div>
        <?php else: ?>
          <div class="alert alert-danger">
            <i class="fa fa-check-circle"></i> A(z) <strong><?=$this->modultitle?> (<?=$_GET['module']?>)</strong> modul inaktív.
          </div>
        <?php endif; ?>
      <?php else: ?>
        <?php if (isset($_GET['installed']) && $_GET['installed'] == '0'): ?>
        <div class="alert alert-danger">
          <i class="fa fa-exclamation-triangle"></i> A(z) <strong><?=$this->modultitle?> (<?=$_GET['module']?>)</strong> telepítése sikertelen. Vegye fel a kapcsolatot a fejlesztővel.
        </div>
        <?php endif; ?>
        <div class="alert alert-warning">
          <i class="fa fa-check-circle-o"></i> A(z) <strong><?=$this->modultitle?> (<?=$_GET['module']?>)</strong> modul elérhető. Telepítésre alkalmas.
        </div>
        <div class="">
          <form class="" action="" method="post">
            <button type="submit" class="btn btn-default" name="installModul" value="1">Modul telepítése</button>
          </form>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  <?php else: ?>
    <div class="alert alert-danger">
      <i class="fa fa-exclamation-triangle"></i> A(z) <strong><?=$_GET['module']?></strong> modul nem elérhető. Vegye fel a kapcsolatot a fejlesztővel.
    </div>
  <?php endif; ?>
<?php endif; ?>
