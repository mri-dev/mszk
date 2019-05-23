</div>
</div>
</div>
</div>

<footer>
<div class="wb">
 <div class="width pad">
     <table width="100%" border="0">
         <tr>
             <tbody>
                 <tr>
                     <td style="text-align: center; font-size: 0.7rem; color: #333333;">
                        Ezt a levelet a(z) <?=$settings['page_author']?> küldte tájékoztató jelleggel. Az Ön adatait bizalmasan kezeljük.
                     </td>
                 </tr>
             </tbody>
         </tr>
     </table>
 </div>
</div>
<div class="cdiv"></div>
<div class="width pad footer">
 <div class="row" style="text-align: center;">
   <img src="<?=ADMROOT?><?=str_replace('logo','logo-white',$settings['logo'])?>" alt="<?=$settings['page_title']?>" style="width: 20%; margin: 0 auto;">
 </div>
</div>
<div class="row contacts" style="background: #1f4da3;">
  <div class="width pad">
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width:33.333%;"><?=$settings['page_author_address']?></td>
                <td style="width:33.333%;">Telefon: <?=$settings['page_author_phone']?></td>
                <td style="width:33.333%;">Email: <a href="mailto:<?=$settings['primary_email']?>"><?=$settings['primary_email']?></a></td>
            </tr>
        </tbody>
    </table>
  </div>
</div>
</footer>

</body>
</html>
