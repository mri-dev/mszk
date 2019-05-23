<div class="pw">
  <br><br>

  <div class="selected-services">
    <?php foreach ((array)$this->configuration['services']['items'] as $service): ?>
    <div class="service">
      <div class="head"><?php echo $service['neve']; ?></div>
      <?php foreach ((array)$this->configuration['subservices']['items'] as $subservice): if($subservice['szulo_id'] != $service['ID']) continue; ?>
      <div class="subservice">
        <div class="head"><?php echo $subservice['neve']; ?></div>
        <?php foreach ((array)$this->configuration['subservicesitems']['items'] as $subserviceitem): if($subserviceitem['szulo_id'] != $subservice['ID']) continue; ?>
        <div class="subserviceitem">
          &mdash; <?php echo $subserviceitem['neve']; ?>
          <?php if (!empty($this->configuration['cash']['subservicesitems'][$subservice['ID']][$subserviceitem['ID']])): ?>
            <span class="cash"><?php echo number_format($this->configuration['cash']['subservicesitems'][$subservice['ID']][$subserviceitem['ID']], 0, '.', ' '); ?> Ft</span>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php if (!empty($this->configuration['cash']['subservices_overall'][$subservice['ID']])): ?>
          <div class="cashall">
            <div class="cashall-header">Költségkeret:</div>
            <?php echo number_format($this->configuration['cash']['subservices_overall'][$subservice['ID']], 0, '.', ' '); ?> Ft
          </div>
        <?php endif; ?>
        <?php if (!empty($this->configuration['subservices_descriptions'][$subservice['ID']])): ?>
          <div class="comment">
            <div class="comment-header">Megjegyzés / Igények:</div>
            <?php echo nl2br($this->configuration['subservices_descriptions'][$subservice['ID']]); ?>
          </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
    <div class=serv-footer>
      Teljes költségvetés összege: <strong><?php echo number_format($this->configuration['cash']['total'], 0, '.', ' '); ?> Ft</strong>
    </div>
  </div>

  <pre><?php print_r($this->configuration); ?></pre>

</div>
