
<?=$this->msg?>
<? if($this->gets[1] == 'torles'): ?>
<form action="" method="post">
<input type="hidden" name="delId" value="<?=$this->gets[2]?>" />
<div class="row np">
	<div class="col-md-12">
    	<div class="con con-del">
            <h2>Oldal törlése</h2>
            Biztos, hogy törli a kiválasztott oldalt?
            <div class="row np">
                <div class="col-md-12 right">
                    <a href="/<?=$this->gets[0]?>/" class="btn btn-danger"><i class="fa fa-times"></i> NEM</a>
                    <button class="btn btn-success">IGEN <i class="fa fa-check"></i> </button>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
<? endif; ?>
<? if( true ): ?>
<? if($this->gets[1] != 'torles'): ?>
<div class="row" id="editor" style="<?=($this->gets[1] != 'szerkeszt' && !isset($_POST['cim']))?'display:none;':''?>">
	<div class="col-md-12">
    	<div class="con <?=($this->gets[1] == 'szerkeszt')?'con-edit':''?>">
        	<form action="" method="post" enctype="multipart/form-data">
        	<h2><? if($this->gets[1] == 'szerkeszt'): ?>Oldal szerkesztése<? else: ?>Új oldal hozzáadása<? endif; ?></h2>
            <br>
            <div class="row">
                <div class="col-md-5">
                	<label for="cim">Cím*</label>
                    <input type="text"class="form-control" name="cim" id="cim" value="<?=($this->page ? $this->page->getTitle() : '')?>">
                </div>
                <div class="col-md-3">
                    <label for="page_parent">Szülő oldal</label>
                    <select name="parent" id="page_parent" class="form-control">
                        <option value="" selected="selected">&mdash; ne legyen / legfelső oldalelem &mdash;</option>
                        <option value="" disabled="disabled"></option>
                        <option value="" disabled="disabled">Szülő oldal kiválasztása:</option>
                         <?
                            while( $this->pages->walk() ):
                            $page = $this->pages->the_page();
                        ?>
                        <option value="<?=$page['ID']?>_<?=$page['deep']?>" <?=($this->page && $this->page->getParentKey() == $page['ID'].'_'.$page['deep'] ? 'selected="selected"':'')?>><? for($s=$page['deep']; $s>0; $s--){echo '&mdash;';}?><?=$page['cim']?></option>
                        <? endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="eleres">Elérési kulcs:</label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-home" title="<?=HOMEDOMAIN?>p/"></i></span>
                      </div>
                    	<input type="text" class="form-control" placeholder="valami_szoveg" name="eleres" id="eleres" value="<?=($this->page ? $this->page->getUrl() : '')?>">
                    </div>
                </div>
                <div class="col-md-1">
                    <label for="lathato">Látható:</label>
                    <input type="checkbox" class="form-control" <?=($this->page && $this->page->getVisibility() ? 'checked="checked"' : '')?> id="lathato" name="lathato" />
                </div>
            </div>
            <br>
            <div class="row">
                 <div class="col-md-3">
                    <label for="sorrend">Sorrend</label>
                    <input type="number" id="sorrend" class="form-control" name="sorrend" value="<?=($this->page ? $this->page->getOrderIndex() : '')?>">
                </div>
                <div class="col-md-6">
                    <label for="cover">Borítókép</label>
                    <div class="input-group">
                        <input type="text" id="cover" class="form-control" name="boritokep" value="<?=($this->page ? $this->page->getCoverImg() : '')?>">
                        <div class="input-group-append">
                          <a title="Kép kiválasztása" href="<?=FILE_BROWSER_IMAGE?>&field_id=cover" data-fancybox-type="iframe" class="iframe-btn btn btn-outline-secondary" type="button"><i class="fa fa-search"></i></a>
                        </div>
                    </div>
                </div>
								<?php if (false): ?>
                <div class="col-md-3">
                    <label for="hashkey">Egyedi azonosító kulcs</label>
                    <input type="text" id="hashkey" class="form-control" name="hashkey" value="<?=($this->page ? $this->page->getHashkey() : '')?>">
                </div>
                <div class="col-md-3">
                    <label for="hashkey_keywords">Egyedi azonosító kulcsszavak</label>
                    <input type="text" id="hashkey_keywords" class="form-control" name="hashkey_keywords" value="<?=($this->page ? $this->page->getHashkeyKeywords() : '')?>">
                </div>
								<?php endif; ?>
            </div>
            <br />
            <div class="row">
            	<div class="col-md-12">
                	<label for="szoveg">Az oldal tartalma</label>
                	<div style="background:#fff;"><textarea name="szoveg" id="szoveg" class="form-control"><?=($this->page ? $this->page->getHtmlContent() : '')?></textarea></div>
                </div>
            </div>
            <br />

            <?php if (false): ?>
            <div class="row ">
                <div class="col-md-12 imageset">
                    <label for="">Csatolt képek</label>
                    <?  if( $this->page && count($this->page->getImageSet()) > 0 ):
                        $index = 0;
                        foreach ($this->page->getImageSet() as $img ) {
                        $index++;
                    ?>
                        <div class="row np" id="image_set_item_0<?=$index?>">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input type="text" id="imgset_0<?=$index?>" index="<?=$index?>" class="form-control" name="image_set[]" value="<?=$img?>">
                                    <div class="input-group-addon"><a title="Kép kiválasztása" href="<?=FILE_BROWSER_IMAGE?>&field_id=imgset_0<?=$index?>" data-fancybox-type="iframe" class="iframe-btn" type="button"><i class="fa fa-search"></i></a></div>
                                    <div class="input-group-addon"><a href="javascript:void(0);" onclick="$('#image_set_item_0<?=$index?>').remove();" style="color:red;" title="Kép törlése"><i class="fa fa-times"></i></a></div>
                                </div>
                            </div>
                        </div>
                    <? } else: ?>
                        <div class="row np" id="image_set_item_01">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input type="text" id="imgset_01" index="1" class="form-control" name="image_set[]" value="<?=$img?>">
                                    <div class="input-group-addon"><a title="Kép kiválasztása" href="<?=FILE_BROWSER_IMAGE?>&field_id=imgset_01" data-fancybox-type="iframe" class="iframe-btn" type="button"><i class="fa fa-search"></i></a></div>
                                    <div class="input-group-addon"><a href="javascript:void(0);" onclick="$('#image_set_item_01').remove();" style="color:red;" title="Kép törlése"><i class="fa fa-times"></i></a></div>
                                </div>
                            </div>
                        </div>
                    <? endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                   <a href="javascript:void(0);" onclick="$('.imageset').append(addImagesetRow());"><i class="fa fa-plus"></i> új kép hozzáadása</a>
                </div>
            </div>
            <br>
            <? endif; ?>

            <?php if (false): ?>
						<h3>Meta adatok</h3>
						<p>
							Az adatok hiányában az alapértelmezett, rendelkezésre álló adatok alapján lesznek összeállítva a meta adatok. Ezek a keresők és a social oldalak szempontjából fontosak.
						</p>
						<div class="row">
							<div class="col-md-6">
								<label for="meta_title">Cím</label>
								<input type="text" id="meta_title" class="form-control" name="meta_title" value="<?=($this->page ? $this->page->getMetaValue('title') : '')?>">
							</div>
							<div class="col-md-6">
								<label for="meta_image">Kép</label>
								<div class="input-group">
									<input type="text" id="meta_image" index="1" class="form-control" name="meta_image" value="<?=($this->page ? $this->page->getMetaValue('image') : '')?>">
									<div class="input-group-addon"><a title="Kép kiválasztása" href="<?=FILE_BROWSER_IMAGE?>&field_id=meta_image" data-fancybox-type="iframe" class="iframe-btn" type="button"><i class="fa fa-search"></i></a></div>
								</div>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-12">
								<label for="meta_desc">Leírás</label>
								<textarea name="meta_desc" class="no-editor form-control" id="meta_desc" maxlength="350"><?=($this->page ? $this->page->getMetaValue('desc') : '')?></textarea>
							</div>
						</div>
            <?php endif; ?>
						<br>
						<div class="row floating-buttons">
							<div class="col-md-12 right">
								<? if($this->gets[1] == 'szerkeszt'): ?>
									<input type="hidden" name="id" value="<?=$this->gets[2]?>" />
									<a href="/<?=$this->gets[0]?>"><button type="button" class="btn btn-danger btn-3x"><i class="fa fa-arrow-circle-left"></i> bezár</button></a>
									<button name="save" class="btn btn-success">Változások mentése <i class="fa fa-check-square"></i></button>
									<? else: ?>
									<button name="add" class="btn btn-primary">Hozzáadás <i class="fa fa-check-square"></i></button>
									<? endif; ?>
							</div>
						</div>
            </form>
        </div>
    </div>
</div>
<script>
    function addImagesetRow () {
        var lastitem = $('.imageset .row:last-child');
        var next_index = parseInt(lastitem.find('input[type=text]').attr('index'))+ 1;
        console.log(next_index);
        var newitem = '<div class="row np" id="image_set_item_0'+next_index+'">'+
                        '<div class="col-md-12">'+
                            '<div class="input-group">'+
                                '<input type="text" id="imgset_0'+next_index+'" index="'+next_index+'" class="form-control" name="image_set[]" value="">'+
                                '<div class="input-group-addon"><a title="Kép kiválasztása" href="<?=FILE_BROWSER_IMAGE?>&field_id=imgset_0'+next_index+'" data-fancybox-type="iframe" class="iframe-btn" type="button"><i class="fa fa-search"></i></a></div>'+
                                '<div class="input-group-addon"><a href="javascript:void(0);" onclick="$(\'#image_set_item_0'+next_index+'\').remove();" style="color:red;" title="Kép törlése"><i class="fa fa-times"></i></a></div>'+
                            '</div>'+
                        '</div>'+
                    '</div>';
        return newitem;
    }
</script>
<? endif; ?>
<div class="row">
	<div class="col-md-12">
      <div class="wblock">
        <div class="data-header">
          <div class="d-flex align-items-center">
            <div class="col title"><i class="ico fas fa-users"></i> Létrehozott oldalak</div>
            <div class="col col-md-5 right">
              <?php if ($this->gets[1] != 'szerkeszt'): ?>
            	<a href="javascript:void(0);" class="btn btn-primary" onclick="$('#editor').slideDown(400);"><i class="fa fa-plus"></i> új oldal</a>
            	<?php endif; ?>
            </div>
          </div>
        </div>
        <div class="data-container">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th><em>Cím</em></th>
                  <th><em>Utoljára frissítve</em></th>
                  <th><em>Gyűjtő</em></th>
                  <th><em>Látható</em></th>
                  <th><em>Sorrend</em></th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?
                  if( $this->pages->has_page() ):
                  while( $this->pages->walk() ):
                      $page = $this->pages->the_page();
                  ?>
                  <tr>
                    <td>
                      <strong><?=$page[cim]?></strong> <? if($page[hashkey]): ?><span class="hashkey">(<?=$page[hashkey]?>)</span><? endif; ?>
                        <div><a target="_blank" href="<?=HOMEDOMAIN?>p/<?=$page['eleres']?>" class="page-url"><?=HOMEDOMAIN?>p/<strong><?=$page[eleres]?></strong></a></div>
                    </td>
                    <td><?=\PortalManager\Formater::dateFormat($page['idopont'], $this->settings['date_format'])?></td>
                    <td><?=($page['gyujto'] == '1')?'<i class="fa fa-check" style="color:green;"></i>':'<i class="fa fa-minus" style="color:lightgrey;"></i>'?></td>
                    <td><? if($page[lathato] == '1'): ?><i style="color:green;" class="fa fa-check"></i><? else: ?><i style="color:red;" class="fa fa-times"></i><? endif; ?></td>
                    <td><?=$page['sorrend']?></td>
                    <td class="actions center">
                      <a href="/<?=$this->gets[0]?>/szerkeszt/<?=$page[ID]?>" title="Szerkesztés"><i class="fas fa-pencil-alt"></i></a>&nbsp;&nbsp;
                      <a href="/<?=$this->gets[0]?>/torles/<?=$page[ID]?>" title="Törlés"><i class="fas fa-trash-alt"></i></a>
                    </td>
                  </tr>
                <? endwhile; else:?>
                  <tr>
                    <td class="center">Nincs létrehozott oldal!</td>
                  </tr>
                <? endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
</div>
<? endif; ?>
<script>
    $(function(){
        $('#cim').bind( 'keyup', function(){
            $('#eleres').val( $(this).val() );
        });
    })
</script>
