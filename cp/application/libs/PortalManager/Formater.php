<?
namespace PortalManager;

/**
* Formázási fügvények gyűjteménye.
*
* class Formater
* @package PortalManager
* @version v1.0
*/
class Formater
{

	public function discountPrice( &$price, $percent = 0, $round = false )
	{
		if( $percent == 0 || !$percent || $percent < 0 ) return $price;
		$price = $price - ( $price / 100 * $percent );

		if( $round ) {
			$price = round( $price );
		}

		return $price;
	}

	public static function sourceImg( $img )
	{
		return UPLOADS.str_replace('/src/uploads/','',$img);
	}

	public static function makeUrlStrlist( $key, $list, $separator = '_' )
	{
		if ( empty($list) ) {
			return '';
		}

		return $key.'_'.implode($separator, $list);
	}


	public static function clockTimes( $half_hour_step = false, $last_item = false )
	{
		$times 		= array();

		$step_min 	= ($half_hour_step) ? 30 : 60;
		$ora 		= 0;
		$ora_pref 	= '0';
		$perc 		= 0;
		$perc_pref 	= ':';

		$times[] = '--:--';
		$times[] = '00:00';

		while( $ora < 24 )
		{
			$perc += $step_min;

			if($perc == 60)
			{
				$perc = 0;
				$perc_pref = ':0';

				$ora += 1;
			} else
			{
				$perc_pref = ':';
			}

			if( $ora >= 10 )
			{
				$ora_pref = '';
			}

			$times[] = $ora_pref.$ora.$perc_pref.$perc;
		}

		if( $last_item )
		{
			$times[] = $last_item;
		}

		return $times;
	}

	public static function cashFormat($cash){
		$cash = number_format($cash,0,""," ");
		return $cash;
	}

	public static function addValueToUrlStrlist2( $gets = array(), $get_pos = 1, $key, $value, $separator = '_' )
	{
		$url = '';
		$step = 0;

		$list = self::getUrlStrlist( $gets[$get_pos], $key, $separator );

		if ( in_array($value, $list)) {
			$inx = array_search($value,$list);
			unset($list[$inx]);
		} else {
			$list[] = $value;
		}

		$x_keys = explode("::", $gets[$get_pos] );

		if ( count( $x_keys ) > 1 ) {
			foreach ( $x_keys as $x_key ) {

				if ( strpos( $x_key, $key ) === 0 ) {
					$x_key = self::makeUrlStrlist( $key, $list, $separator );
				}

				$key_filter .= '::'.$x_key;
			}
			$key_filter = ltrim($key_filter, '::');
		} else {
			if ( $gets[$get_pos] == '' || is_numeric( $gets[$get_pos] ) ) {
				$key_filter = self::makeUrlStrlist( $key, $list, $separator );
			} else {
				if ( strpos( $gets[$get_pos], $key ) === 0 ) {
					$key_filter = self::makeUrlStrlist( $key, $list, $separator );
				} else {
					$key_filter = $gets[$get_pos];
					$key_filter .= "::".self::makeUrlStrlist( $key, $list, $separator );
				}
			}

		}

		while ( $step < $get_pos ) {
			$url .= '/'.$gets[$step];
			unset($gets[$step]);
			$step++;
		}

		if( !is_numeric($gets[$get_pos])){
			unset($gets[$get_pos]);
		}

		// Szűrő szöveg
		if ( $key_filter ) {
			$url .= '/'.$key_filter;
		}

		foreach ( $gets as $g ) {
			$url .= '/'.$g;
		}

		$url = trim($url, "::");

		return $url;
	}

	public static function addValueToUrlStrlist( $str, $key, $value, $separator = '_', $gets = array() )
	{
		$url = '';
		/*$hashkey = hash('crc32', microtime());
		$current_url = $_SERVER['REQUEST_URI'];

		$list = self::getUrlStrlist( $str, $key, $separator );

		if ( empty($list) ) {
			$list[] = $value;

			if($gets[2] != '' && !is_numeric($gets[2])){
				$replace_key = urlencode($gets[2]);
				$temp = str_replace( $replace_key, $hashkey, $current_url );
				$newurl = self::makeUrlStrlist( $key, $list ).'::'.$gets[2];
				$newurl = trim($newurl,'::');
				$url = str_replace($hashkey, $newurl , $temp);

			} else if(is_numeric($gets[2]) || $gets[2] == '') {

				$replace_key =  urlencode( $gets[0].'/'.$gets[1]);
				$temp = str_replace($replace_key, $hashkey, $current_url );
				$url = str_replace($hashkey, $replace_key.'/'.self::makeUrlStrlist( $key, $list ), $temp);
			}

			return $url;
		} else {
			$replace_key = self::makeUrlStrlist($key, $list, $separator);
		}

		$temp = str_replace( trim($replace_key,"::"), $hashkey, $current_url );

		if ( in_array($value, $list)) {
			$inx = array_search($value,$list);
			unset($list[$inx]);
		} else {
			$list[] = $value;
		}


		$new_keystr = self::makeUrlStrlist( $key, $list, $separator );
		$url = str_replace( $hashkey, trim($new_keystr,'::'), $temp);
		*/

		return $url;
	}

	public static function getUrlStrlist( $str, $key, $separator = '_' )
	{
		$row = explode('::', $str);
		$items = array();


		if( $row[0] ) {
			foreach ( $row as $r ) {
				$i = explode($separator, $r);
				if (reset($i) == $key) {
					foreach ($i as $value) {
						if ($value != reset($i)) {
							if( $value != '' )
							$items[] = $value;
						}
					}
				}
			}
		} else {
			$i = explode($separator, $str);
			if (reset($i) == $key) {
				foreach ($i as $value) {
					if ($value != reset($i)) {
						if( $value != '' )
						$items[] = $value;
					}
				}
			}
		}

		return $items;
	}

	public static function productImage( $source, $size = false, $noimgtag = '' )
	{
		if ( !$source ) {
			return IMG.$noimgtag;
		} else {
			if ($size) {
				$ct 	= explode("/",$source);
				$max 	= count($ct);
				$im 	= $ct[$max-1];
				$root 	= str_replace($im,"",$source);

				if($im == 'noimg.png'){
					$thmb 	= '/'.$root.$im;
				}else{
					$thmb 	= '/'.$root.'thb'.$size.'_'.$im;
				}

				$thmb = str_replace( 'images/','', IMG).str_replace('src/','',ltrim($thmb,'/'));
				return $thmb;
			} else {
				return str_replace( 'images/','', IMG).str_replace('src/','',$source);
			}

		}
	}

	public static function tooltip($msg)
	{
		$tooltip = '<span class="tooltip-view"><i class="fa fa-question-circle"></i> ';

		$tooltip .= '<span class="tip-msg">'.$msg.'</span>';
		$tooltip .= '</span>';

		return $tooltip;
	}

	public static function dateFormat( $date = false, $format_patern = false )
	{
		if( !$date ) return '<span title="nincs adat">n.a.</span>';

		$used_date = NOW;

		if ( $date ) {
			$used_date = $date;
		}

		if ( $format_patern ) {
			$fdate = new \DateTime( $used_date );
			$fdate = $fdate->format( $format_patern );
		} else {
			$fdate = $used_date;
		}

		$fdate = str_replace(
			array('Jan','Feb','Mar', 'Apr', 'May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'),
			array('január','február','március','április','május','június','julius','augusztus','szeptember','október','november','december'),
			$fdate
		);

		return $fdate;
	}

	/**
	 * Eltelt idő kimutatása
	 * @param  string $date viszonyítási időpont
	 * @return string       formázva az eltelt idő
	 */
	public static function distanceDate($date = NOW){

		if($date == '0000-00-00 00:00:00' || !$date ){ return 'sose'; }
		$now 		= strtotime(NOW);
		$date 		= strtotime($date);
		$mode 		= 'past';
		if($date < $now){
			$dif_sec =  $now - $date ;
		}else{
			$mode = 'future';
			$dif_sec =  $date - $now ;
		}

		$ret 		= '';
		///////////////////////////////
		$perc 	= 60;
		$ora 	= $perc * 60;
		$nap 	= $ora * 24;
		$honap 	= $nap * 30;
		$ev 	= $honap * 12;
		///////////////////////////////
			switch($mode){
				case 'past':
					if($dif_sec <= $perc){ // Másodperc
						$ret = $dif_sec.' '. __('másodperce');
					}else if($dif_sec > $perc && $dif_sec <= $ora){ // Perc
						$ret = floor($dif_sec / $perc).' '.__('perce');
					}else if($dif_sec > $ora && $dif_sec <= $nap){ // Óra
						$ret = floor($dif_sec / $ora).' '.__('órája');
					}else if($dif_sec > $nap && $dif_sec <= $honap){ // Nap
						$np = floor($dif_sec / $nap);
						if($np == 1){
							$ret = __('tegnap');
						}else
							$ret = $np.' '.__('napja');
					}else if($dif_sec > $honap && $dif_sec <= $ev){ // Hónap
						$ret = floor($dif_sec / $honap).' '.__('hónapja');
					}else{ // Év
						$ret = floor($dif_sec / $ev).' '.__('éve');
					}
				break;
				case 'future':
					if($dif_sec <= $perc){ // Másodperc
						$ret = $dif_sec.' '. __('másodperc');
					}else if($dif_sec > $perc && $dif_sec <= $ora){ // Perc
						$ret = floor($dif_sec / $perc).' '.__('perc');
					}else if($dif_sec > $ora && $dif_sec <= $nap){ // Óra
						$ret = floor($dif_sec / $ora).' '.__('óra');
					}else if($dif_sec > $nap && $dif_sec <= $honap){ // Nap
						$np = floor($dif_sec / $nap);
						$ret = $np.' '.__('nap');
					}else if($dif_sec > $honap && $dif_sec <= $ev){ // Hónap
						$ret = floor($dif_sec / $honap).' '.__('hónap');
					}else{ // Év
						$ret = floor($dif_sec / $ev).' '.__('év');
					}
				break;
			}


		return $ret;
	}

	public static function makeSafeUrl($str,$after = ''){
		$f 		= array(' ',',','á','Á','é','É','í','Í','ú','Ú','ü','Ü','ű','Ű','ö','Ö','ő','Ő','ó','Ó','(',')','\'','"',"=","/","\\","?","&","!");
		$t 		= array('-','','a','a','e','e','i','i','u','u','u','u','u','u','o','o','o','o','o','o','','','','','','','','','','');
		$str 	= str_replace($f,$t,$str);
		$str 	= strtolower($str);

		$ret = $str . $after;
		return $ret;
	}
}
?>
