<?php if ($this->notoffered_requests && $this->notoffered_requests > 0): ?>
  <div class="alert alert-warning">
    <?php if ($this->is_admin_logged): ?>
      <?php echo sprintf(__('Összesen <strong>%d darab</strong> feldolgozatlan ajnálat kérés vár feldolgozásra!'), $this->notoffered_requests); ?>
    <?php else: ?>
      <?php echo sprintf(__('Önnek <strong>%d darab</strong> feldolgozatlan ajnálat kérés igénye van rögzítve! Az ajánlatkérések között akkor tekintheti meg, ha ki lett ajánlva partnereink felé!'), $this->notoffered_requests); ?>
    <?php endif; ?>
  </div>
<?php endif; ?>

<div class="wblock color-red">
  <div class="data-container">
    <div class="no-data-view">
      <div class="ico"><i class="far fa-check-circle"></i></div>
      <div class="text"><?=__('Minden rendben!')?></div>
    </div>
  </div>
  <div class="data-footer">
    <div class="d-flex align-items-center">
      <div class="title">
        <h3><?=__('Feldolgozatlan ajánlat kérések')?></h3>
        <?php if ($this->is_admin_logged): ?>
          <a href="/ajanlatkeresek/feldolgozatlan"><?=__('Tovább az igények feldolgozásához')?></a>
        <?php endif; ?>
      </div>
      <div class="count">
        <div class="count-wrapper"><div class="num"><?=$this->notoffered_requests?></div></div>
      </div>
    </div>
  </div>
</div>
