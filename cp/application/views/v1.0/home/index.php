<div class="dashboard">
<? if($this->_USER): ?>
  <div class="wblock overview-services p-2">
    <div class="row">
      <div class="col ct-info color-blue">
        <div class="d-flex">
          <div class="ico">
            <div class="ico-wrapper"><i class="far fa-lightbulb"></i></div>
          </div>
          <div class="title"><?=__('Projektek')?><div class="line"></div></div>
          <div class="count">10</div>
        </div>
      </div>
      <div class="col ct-info color-green">
        <div class="d-flex">
          <div class="ico">
            <div class="ico-wrapper"><i class="fas fa-file-import"></i></div>
          </div>
          <div class="title"><?=__('Ajánlat kérések')?><div class="line"></div></div>
          <div class="count">4</div>
        </div>
      </div>
      <div class="col ct-info color-blue-light">
        <div class="d-flex">
          <div class="ico">
            <div class="ico-wrapper"><i class="far fa-file-alt"></i></div>
          </div>
          <div class="title"><?=__('Díjbekérők')?><div class="line"></div></div>
          <div class="count">12</div>
        </div>
      </div>
      <div class="col ct-info color-red">
        <div class="d-flex">
          <div class="ico">
            <div class="ico-wrapper"><i class="far fa-calendar-times"></i></div>
          </div>
          <div class="title"><?=__('Lejárt díjbekérők')?><div class="line"></div></div>
          <div class="count">2</div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4">
      <div class="wblock">
        <div class="data-container">
          asd
        </div>
        <div class="data-footer">
          <div class="d-flex align-items-center">
            <div class="title">
              <h3><?=__('Lejárt díjbekérők')?></h3>
              <a href="/dokumentumok/dijbekero"><?=__('Tovább az összes díjbekérőhöz')?></a>
            </div>
            <div class="count">
              <div class="count-wrapper"><div class="num">10</div></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="wblock">
        111
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4">
      <div class="wblock">
        111
      </div>
    </div>
    <div class="col-md-8">
      <div class="wblock">
        111
      </div>
    </div>
  </div>
<? endif;?>
</div>
