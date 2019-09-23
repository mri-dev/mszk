<div class="project-controller" ng-controller="ProjectControl" ng-init="init({hashkey: '<?=$this->gets[2]?>', user: <?=$this->_USERDATA['data']['ID']?>})">
  <?php if ($this->is_admin_logged): ?>
    <?=$this->render('projektek/admincontrol')?>
  <?php else: ?>
    <?=$this->render('projektek/usercontrol')?>
  <?php endif; ?>
</div>
