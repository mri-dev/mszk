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
              <h3><?=__('Lejárt díjbekérők')?></h3>
              <a href="/dokumentumok/dijbekero"><?=__('Tovább az összes díjbekérőhöz')?></a>
            </div>
            <div class="count">
              <div class="count-wrapper"><div class="num">2</div></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="wblock">
        <div class="wblock color-green">
          <div class="data-container">
            <div class="no-data-view">
              <div class="ico"><i class="far fa-circle"></i></div>
              <div class="text"><?=__('Nincsenek ajánlatkérései.')?></div>
            </div>
          </div>
          <div class="data-footer">
            <div class="d-flex align-items-center">
              <div class="title">
                <h3><?=__('Ajánlat kérések')?></h3>
                <a href="/ajanlatkeresek/"><?=__('Tovább az összes ajánlatkéréshez')?></a>
              </div>
              <div class="count">
                <div class="count-wrapper"><div class="num">4</div></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4">
      <div class="wblock color-blue">
        <div class="data-container">
          <div class="no-data-view">
            <div class="ico"><i class="far fa-check-circle"></i></div>
            <div class="text"><?=__('Nincs folyamatban lévő beszélgetés.')?></div>
          </div>
        </div>
        <div class="data-footer">
          <div class="d-flex align-items-center">
            <div class="title">
              <h3><?=__('Beszélgetések')?></h3>
              <a href="/uzenetek"><?=__('Tovább az összes üzenethez')?></a>
            </div>
            <div class="count">
              <div class="count-wrapper"><div class="num">2</div></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="wblock">
        <div class="wblock color-blue">
          <div class="data-container">
            <div class="no-data-view">
              <div class="ico"><i class="fab fa-buffer"></i></div>
              <div class="text"><?=__('Nincsenek folyamatban lévő projektek.')?></div>
            </div>
          </div>
          <div class="data-footer">
            <div class="d-flex align-items-center">
              <div class="title">
                <h3><?=__('Aktuális projektek')?></h3>
                <a href="/projektek"><?=__('Tovább az összes projekthez')?></a>
              </div>
              <div class="count">
                <div class="count-wrapper"><div class="num">12</div></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<? endif;?>
</div>
