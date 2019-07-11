<div class="alerts-controller">
  <div class="alert-list">
    <?php while ($this->alerts->walk()) { $button = $this->alerts->getNavButton();?>
      <div class="alert <?=(!$this->alerts->isWatched())?'unwatched':'watched'?>">
        <div class="wrapper">
          <div class="ico">
            <?php if (!$this->alerts->isWatched()): ?>
              <div class="unreaded">&nbsp;</div>
            <?php endif; ?>
            <i class="<?=$this->alerts->getIcon()?>"></i>
          </div>
          <div class="message">
            <div class="msg"><?=$this->alerts->getMessage()?></div>
            <div class="submsg">
              <span class="time"><?=$this->alerts->getAlertDate()?></span>
            </div>
          </div>
          <div class="action">
            <?php if ($button): ?>
              <a href="<?=$button['url']?>" title="<?=$button['msg']?>" class="btn btn-sm btn-primary"><?=$button['text']?></a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <? } ?>
  </div>
</div>
