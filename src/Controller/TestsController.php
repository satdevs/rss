<?php
// Baked at 2022.03.09. 15:12:24
declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use Cake\Utility\Xml;
use Cake\I18n\FrozenTime;


class TestsController extends AppController
{

    public function initialize(): void
    {
        parent::initialize();

		if(!file_exists(WWW_ROOT . 'images')){
			mkdir(WWW_ROOT . 'images');
		}
		if(!file_exists(WWW_ROOT . 'images' . DS . 'weathers')){
			mkdir(WWW_ROOT . 'images' . DS . 'weathers');
		}
		if(!file_exists(WWW_ROOT . 'images' . DS . 'metamin')){
			mkdir(WWW_ROOT . 'images' . DS . 'metamin');
		}

	}

/*
	A "Tests"-et átírni: "rss" -re

	0. napi összefoglaló: 	http://192.168.254.215:8002/rss/newForecast/forecast/day-0.rss
	+1 napi összefoglaló: 	http://192.168.254.215:8002/rss/newForecast/forecast/day-1.rss
	+2 napi összefoglaló: 	http://192.168.254.215:8002/rss/newForecast/forecast/day-2.rss
	+3 napi összefoglaló: 	http://192.168.254.215:8002/rss/newForecast/forecast/day-3.rss
	+4 napi összefoglaló: 	http://192.168.254.215:8002/rss/newForecast/forecast/day-4.rss
	
	Napkelte, napnyugta és nap hossz: 
							http://192.168.254.215:8002/rss/newForecast/hourly/sun.rss
	Aktuális hőfok:			http://192.168.254.215:8002/rss/newForecast/hourly/degree.rss
	Szél, eső, pára:		http://192.168.254.215:8002/rss/newForecast/hourly/wind-rain-rhum.rss
	Időjárás szöveggel:		http://192.168.254.215:8002/rss/newForecast/hourly/weather.rss
	Óránkénti:				http://192.168.254.215:8002/rss/newForecast/hourly/hour-0.rss
							http://192.168.254.215:8002/rss/newForecast/hourly/hour-1.rss
							http://192.168.254.215:8002/rss/newForecast/hourly/hour-2.rss
							http://192.168.254.215:8002/rss/newForecast/hourly/hour-3.rss
							http://192.168.254.215:8002/rss/newForecast/hourly/hour-4.rss
							http://192.168.254.215:8002/rss/newForecast/hourly/hour-5.rss
							http://192.168.254.215:8002/rss/newForecast/hourly/hour-6.rss
							http://192.168.254.215:8002/rss/newForecast/hourly/hour-7.rss
							http://192.168.254.215:8002/rss/newForecast/hourly/hour-8.rss
							http://192.168.254.215:8002/rss/newForecast/hourly/hour-9.rss

	Az azott $mode-n belül a képecskék másolódnak és azokra fix url-lel lehet hivatkozni, ahol a szám az adott órát jelenti, azaz a 0 az aktuális órát vagy az aktuális napot.
	Az 1-es a +1 órával vagy +1 nappal távolabbira mutat. A 2-es a holnaputánra vagy a 2 órával későbbre.
	
							http://192.168.254.215:8002/images/weathers/day-0.png
							http://192.168.254.215:8002/images/weathers/wind-0.png
							http://192.168.254.215:8002/images/weathers/hour-0.png
							http://192.168.254.215:8002/images/weathers/hour-wind-0.png
	
*/

    public function newForecast($mode='forecast', $param='day-0')	// today: day-0, holnap: day-1, holnapután: day-2, azután: day-3, és azután: today: day-4 
	{
		$jsonForecast 	= '';
		$jsonHourly 	= '';
		$content 		= '-';
		$day 			= (int) substr($param, -1);

		if($mode == 'forecast'){
			if(file_exists(WWW_ROOT . "files" . DS . "forecast.json")){
				$jsonForecast = file_get_contents(WWW_ROOT . "files" . DS . "forecast.json");
			}
			$forecasts = json_decode($jsonForecast);
			$f = $forecasts[$day];
			$content = $f->month_short . " " . (int) $f->d . ".\n" . $f->day_name. "\n\n\n\n" . $f->max . "\n\n" . $f->min . "\n\n\n\n";
			$content .= $f->wind_speed . "\n\n\n\n" . $f->rain . "\n\n" . $f->weather;
			//debug($forecasts); 
			foreach($forecasts as $k => $f){
				$src = WWW_ROOT . "images" . DS . "weathers" . DS . "forecasts" . DS . $f->symb_name . ".png";
				$dest = WWW_ROOT . "images" . DS . "weathers" . DS . "day-" . $k . ".png";
				if(file_exists($src)){
					if(file_exists($dest)){
						unlink($dest);
					}
					copy($src, $dest);
				}

				$src = WWW_ROOT . "images" . DS . "weathers" . DS . "forecasts" . DS . $f->wind_degree . ".png";
				$dest = WWW_ROOT . "images" . DS . "weathers" . DS . "wind-" . $k . ".png";
				if(file_exists($src)){
					if(file_exists($dest)){
						unlink($dest);
					}
					copy($src, $dest);
				}
			}
			
		} // forecast

		if($mode == 'hourly'){
			// ------------------ JSON Beolvasása -----------------------
			if(file_exists(WWW_ROOT . "files" . DS . "hourly.json")){
				$jsonHourly = file_get_contents(WWW_ROOT . "files" . DS . "hourly.json");
			}

			$hourly = json_decode($jsonHourly);
			//debug($hourly);
			
			$f = $hourly[0];
			
			switch($param){
				case "sun": 
					$content = $f->sun_rise . "\n" . $f->sun_set . "\n" . $f->day_lengths;
					break;
				case "degree": 
					$content = $f->degree;
					break;
				case "wind-rain-rhum":
					$content = $f->wind_kmh . "\n\n" . $f->rain_mm . "\n" . $f->rhum;
					break;
				case "weather": 
					$content = $f->weather;
					break;
				case substr($param, 0,4) == "hour": 
					$hour = (int) substr($param, -1);
					$f = $hourly[$hour];
					$content = $f->hour . "           " . $f->degree . "   " . $f->feel . "        " . $f->wind_kmh . "   " . $f->rhum . "   " . $f->rain_mm . "   " . $f->weather;
					break;
			}

			foreach($hourly as $k => $f){
				
				$src = WWW_ROOT . "images" . DS . "weathers" . DS . "forecasts" . DS . $f->symbol . ".png";
				$dest = WWW_ROOT . "images" . DS . "weathers" . DS . "hour-" . $k . ".png";
				if(file_exists($src)){
					if(file_exists($dest)){
						unlink($dest);
					}
					copy($src, $dest);
				}

				$src = WWW_ROOT . "images" . DS . "weathers" . DS . "forecasts" . DS . $f->wind_symbol . ".png";
				$dest = WWW_ROOT . "images" . DS . "weathers" . DS . "hour-wind-" . $k . ".png";
				if(file_exists($src)){
					if(file_exists($dest)){
						unlink($dest);
					}
					copy($src, $dest);
				}

			}
			
		} // hourly

		$atomLink = ['controller' => 'Rss', 'action' => 'forecast', '_ext' => 'rss'];

		
		$forecast['channel']['title'] = "Időjárás előrejezlés";
		$forecast['channel']['description'] = $content;
		
		$rss = [
			'channel' => [
				'title' => $forecast['channel']['title'],
				'link' => 'http://192.168.254.215:8002/rss/newForecast/' . $mode . '.rss',
				'description' => $forecast['channel']['description'],
			],
			'items' => $forecast
		];

		$this->set(['rss' => $rss, '_serialize' => 'rss']);
	
	}

	
    public function metanim($param = null)
	{
		
		
		//$data['date'] 	= new FrozenTime($dt);
		//$now = FrozenTime::now();
		//$now = $now->modify('-170 minutes');
		//$time = getDate(strtotime($now->i18nFormat('yyyy-MM-dd HH:mm:ss')));
		
		$url = 'https://koponyeg.hu/terkep';
		$url = 'https://img.koponyeg.hu/maps/rainfall-intensity/20220404_0510.jpg';

		$url = 'https://img.koponyeg.hu/maps/rainfall-intensity/20220404_0510.jpg';

		
		
		$page = file_get_contents($url);

		$filename = $dir . '01.jpg';
		
		file_put_contents($filename, file_get_contents($url));
		
		
		
		die();
		
	}
	
	
	
	
    public function podcasts($param = null)
    {
		$i=1;
		$counter = 1;

		//https://devtales.shiwaforce.com/wp-content/uploads/2022/02/123.mp3

		$rss = 'https://devtales.shiwaforce.com/feed/podcast';

		$xmlString = file_get_contents($rss);
		$xmlArray = Xml::toArray(Xml::build($xmlString));
		$items = $xmlArray['rss']['channel']['item'];

		$dir = WWW_ROOT . 'files';
		if(!file_exists($dir)){
			mkdir($dir);
		}
		
		$from = 12;
		$to = 23;
		
		foreach($items as $key => $item){
			//echo "<pre>";
			debug($item);
			//echo($item['title']) . "<br>";
			//echo($item['link']) . "<br>";
			//echo($item['pubDate']) . "<br>";
			////echo($item['guid']) . "<br>";
			//echo($item['guid']['@']) . "<br>";
			////echo($item['enclosure']) . "<br>";
			//echo($item['enclosure']['@url']) . "<br>";
			//echo($item['dc:creator']) . "<br>";
			
			//$item['description'];
			//$item['content:encoded'];
			//$item['googleplay:description'];
			//echo "</pre>";
			die();
			
			if($counter >= $from){
				/*
				//$url = $item['link'];
				
				$url = $item['enclosure']['@url']
				//$page = file_get_contents($item['link']);
				//
				//$search = '"associatedMedia":{"contentUrl":"';
				//$pos = strpos($page, $search);
				//$content = substr($page, $pos + strlen($search));
				//
				//$search = '.mp3';
				//$pos = strpos($content, $search);
				//$mediaUrl = substr($content, 0, $pos + strlen($search));
				*/
				//$filename = $dir . DS . substr("0000" . $counter, -3) . " - " . $this->normalizeFilename($item['title']) . ".mp3";
				$filename = $dir . DS . $this->normalizeFilename($item['title']) . ".mp3";
				//echo $filename;
				$basename = basename($filename);
				echo $basename;
				echo "<br>";
				//file_put_contents($filename, file_get_contents($mediaUrl));				
			}
			
			if($counter >= $to){
				break;
			}
			
			$counter++;

		}
		die();

/*
		$urls = [
			'https://devtales.shiwaforce.com/wp-content/uploads/2022/02/124.mp3',
			
		];
	











/*
		$url = 'https://www.hirstart.hu/site/publicrss.php?pos=balrovat&pid=378';
		$urls = [
			//'',
			//'',
			//'',
			'https://www.hirstart.hu/site/publicrss.php?pos=fokoteg&pid=98&fid=5707',
			'https://www.hirstart.hu/site/publicrss.php?pos=fokoteg&pid=98&fid=9026',
			'https://www.hirstart.hu/site/publicrss.php?pos=fokoteg&pid=98&fid=5362',
			'https://www.hirstart.hu/site/publicrss.php?pos=fokoteg&pid=98&fid=9318',
			'https://www.hirstart.hu/site/publicrss.php?pos=fokoteg&pid=98&fid=6642',
		];
		
		foreach($urls as $url){
			$xmlString = file_get_contents($url);
			$xmlArray = Xml::toArray(Xml::build($xmlString));
			$items = $xmlArray['rss']['channel']['item'];

			//debug($xmlArray);
			
			
			echo "<h2>" . $xmlArray['rss']['channel']['title'] . "</h2>";
			echo $xmlArray['rss']['channel']['description'] . "<br>";
			echo "--------------------------------------<br>";
			
			foreach($items as $item){
				//debug($item);
				echo "<b>" . $item['title'] . "</b><br>";
				echo "" . $item['description'] . "<br>";
				echo "<i>" . $item['category'] . "</i><hr>";
			}
			
		}
*/	

		die();
		







/*

		$url = 'https://www.idokep.hu/idojaras/B%C3%B3ly';
		$cookies=array();
		foreach($http_response_header as $s){
			if(preg_match('|^Set-Cookie:\s*([^=]+)=([^;]+);(.+)$|',$s,$parts)){
				$cookies[$parts[1]]=$parts[2];
			}
		}
*/




		
/*file_get_contents($url);

		$cookies = array();
		foreach ($http_response_header as $hdr) {
			if (preg_match('/^Set-Cookie:\s*([^;]+)/', $hdr, $matches)) {
				parse_str($matches[1], $tmp);
				$cookies += $tmp;
			}
		}
		//print_r($cookies);
*/
/*
		file_get_contents($url);

		// https://tousu.in/qa/?qa=1144544/

		$cookies = array();
		debug($http_response_header);
		foreach ($http_response_header as $hdr) {
			if (preg_match('/^Set-Cookie:s*([^;]+)/', $hdr, $matches)) {
				parse_str($matches[1], $tmp);
				$cookies += $tmp;
			}
		}
		print_r($cookies);

		//debug($cookies);


		if (false !== ($f = fopen($url, 'r'))) {
				$meta = stream_get_meta_data($f);
				//debug($meta);
				$headers = $meta['wrapper_data'];
				//debug($headers);

				$contents = stream_get_contents($f);
				//debug($contents);
				fclose($f);
		}
		// $headers now contains the same array as $http_response_header

		die();


*/






	
		/*
			<div class="ik hosszutavu-elorejelzes m-sm-4 p-4 rounded-soft">
				<h2>Csapadékos idő, erős lehűlés jön</h2>

				<p>
					<strong>Pénteken</strong> kezdetben többfelé várható eső, zápor. Délután dél felől szakadozottabbá válik a felhőzet, főként keleten alakulhatnak ki záporok, helyenként zivatarok. Többfelé élénk, erős lesz a szél. A maximum-hőmérséklet az ország északi és nyugati felén 7 és 12, délkeletebbre 13 és 20 fok között alakulhat.
					<br>
					<br>
					<strong>Szombaton</strong> is sok lesz fölöttünk a felhő, dél felől ismét többfelé kialakulhat eső, zápor. A hegyekben már napközben, síkvidéken pedig estétől havas eső, havazás válthatja az esőt. Sokfelé marad a szeles idő. Délután 5-15 fok várható, éjjel viszont sokfelé fagypont közelébe hűl a levegő.                    
				</p>

				<p>
					<strong>Vasárnap</strong> kezdetben sokfelé várható havas eső, havazás, néhol eső, majd napközben csökken a csapadékhajlam, délután nyugat felől szakadozni kezd a felhőzet. Élénk, erős, a Dunántúlon viharos lesz az északnyugati szél. Napközben is csak 5-10 fokot mérhetünk. <strong>Hétfőn</strong> reggel többfelé fagyhat. Napközben szeles, északon felhősebb, délen naposabb időre, 7-14 fokra számíthatunk.
				</p>

				<p>
					<small>Kiadta:
						<a href="/impresszum" target="_blank">Időkép</a> (ma 08:58)
					</small>
				</p>
			</div>
		*/
/*
		$url = 'https://www.idokep.hu/30napos/B%C3%B3ly';
		$page = file_get_contents($url);

		$pos = (int) strpos('<div class="ik hosszutavu-elorejelzes m-sm-4 p-4 rounded-soft">', $page)
		$div = substr($page, $pos);
		
		$pos = (int) strpos('</div>', $div)
		$div = substr($div, 0, $pos);
		
		echo $div;
		die();
*/
		
		
		/*
			// Create a stream
			$opts = array(
			  'http'=>array(
				'method'=>"GET",
				'header'=>"Accept-language: en\r\n" .
						  "Cookie: foo=bar\r\n"
			  )
			);

			$context = stream_context_create($opts);

			// Open the file using the HTTP headers set above
			$file = file_get_contents('http://www.example.com/', false, $context);		
			
			// -----------------------------------------------------------------------
			
			$context = stream_create_context([
				"http" => [
					"method" => "GET",
					"header" => "Accept-languange: en\r\n" .
					"Cookie: foo=bar\r\n"
				]
			]);

			$file = file_get_contents('https://example.com', false, $context);
		*/
		
		
		//echo $page;
		//die();
/*
		$urls = [
			"felhokep2.webp"	=> "https://www.idokep.hu/terkep/hu600/felhokep2.webp",
			"idokep2.webp" 		=> "https://www.idokep.hu/terkep/hu600/idokep2.webp",
			"hoterkep3.webp"	=> "https://www.idokep.hu/terkep/hu970/hoterkep3.webp",
			"riaszt24.png"		=> "https://www.idokep.hu/kistersegi/riaszt_24_transp.png?1648707739",
			"riaszt48.png"		=> "https://www.idokep.hu/kistersegi/riaszt_48_transp.png?1648707739%22",
			"riaszt3.png"		=> "https://www.idokep.hu/kistersegi/riaszt_1-3_transp.png?1648707739"
		];

		foreach($urls as $key => $url){
			$tmp_filename = WWW_ROOT . 'images' . DS . 'weathers' . DS . 'tmp_' . $key;
			if(file_exists($tmp_filename)){
				unlink($tmp_filename);
			}
			
			file_put_contents($tmp_filename, file_get_contents($urls[$key]));

			$filename = WWW_ROOT . 'images' . DS . 'weathers' . DS . $key;
			if(file_exists($filename)){
				unlink($filename);
			}

			debug($filename);
			debug($tmp_filename);

		}

		die();

		// Load the WebP file
		$im = imagecreatefromwebp($tmp_filename);

		// Convert it to a jpeg file with 100% quality
		imagejpeg($im, $filename, 100);
		imagedestroy($im);
*/	
		die();
		
	}
	
	
	
	// http://192.168.254.215:8002/rss/forecast/forecatDay1.rss   // forecatDay1-forecatDay10
	
	// https://www.foreca.hu/Hungary/Baranya/B%C3%B3ly/10-day-forecast
	
    public function forecast($param = null)
    {
		$contents = '';
		$page = 1;

		if($param !== null){
			$page = (int) $param;
		}
		$page = (int) substr($param, 10);

		$atomLink = ['controller' => 'Rss', 'action' => 'forecast', '_ext' => 'rss']; // Example controller and action
		
		$this->loadModel('Forecasts');

		$f = $this->Forecasts->find('all', [
			'order' => [
				'date' => 'desc'
			],
			'limit' => 1,
			'page' => $page
		])->first();
		
		$content = $f->day . ".\n" . $f->d . "\n\n" . $f->tmax . "\n\n" . $f->tmin . "\n\n" . $f->wx . "\n\n" ;
		$content .= $f->sr . "\n\n" . $f->ss;
		
		$forecast['channel']['title'] = "Időjárás előrejezlés";
		$forecast['channel']['description'] = $content;
		
		$rss = [
			'channel' => [
				'title' => $forecast['channel']['title'],
				'link' => 'http://192.168.254.215:8002/rss/forecast/' . $param . '.rss',
				'description' => $forecast['channel']['description'],
			],
			'items' => $forecast
		];

		$this->set(['rss' => $rss, '_serialize' => 'rss']);

    }









	// https://www.met.hu/methu/rss/rss.php
	
	// https://www.met.hu/methu/rss/rss.php?categoryid=1
	// https://www.met.hu/methu/rss/rss.php?categoryid=2
	// https://www.met.hu/methu/rss/rss.php?categoryid=7
	// https://www.met.hu/methu/rss/rss.php?categoryid=12	
	// https://www.met.hu/methu/rss/rss.php?categoryid=16
	// https://www.met.hu/methu/rss/rss.php?categoryid=25
	
	// https://www.idokep.hu/elorejelzes/B%C3%B3ly
	// Hő Grafikon: https://www.idokep.hu/automata/boly1_hom.png?3eba7
	// Pára Grafikon: https://www.idokep.hu/automata/boly1_rh.png?3eba7
	// Légnyomás Grafikon: https://www.idokep.hu/automata/boly1_p.png?3eba7
	// Csapadék Grafikon: https://www.idokep.hu/automata/boly1_csap24.png?3eba7
	// Csapadék intenzitás Grafikon: https://www.idokep.hu/automata/boly1_csap1.png?3eba7
	
	// WEB: https://www.idokep.hu/automata/boly1


	// WEB: https://www.foreca.hu/Hungary/Baranya/B%C3%B3ly/10-day-forecast
	

    public function fixingBadJson($param = null)
	{
		$s = str_replace("'", '"', $param);
		
		$s = preg_replace('/(\w+):/i', '"\1":', $s);

		$s = str_replace('""', '"', $s);
		$s = str_replace('":0', ':0', $s);
		$s = str_replace('":1', ':1', $s);
		$s = str_replace('":2', ':2', $s);
		$s = str_replace('":3', ':3', $s);
		$s = str_replace('":4', ':4', $s);
		$s = str_replace('":5', ':5', $s);
		$s = str_replace('":6', ':6', $s);
		$s = str_replace('":7', ':7', $s);
		$s = str_replace('":8', ':8', $s);
		$s = str_replace('":9', ':9', $s);
		$s = str_replace('10d', 'd10', $s);
		
		return $s;
	}

    /**
	 * http://192.168.254.215:8002/tests/forecast/forecastToDay.rss
	 // $daily_data =  "{mode: 'full',rows: 2,'10d': { '20220325120000': {dt: '25/03', dm: 'Március 25', d: 'Péntek', ds: 'P', tmax: '+18', txfg: '000', txbg: 'ffb340', tmin: '+8', symb: 'd000', wx: 'Tiszta', winds: 11, windd: '225', sr: '05:40', ss: '18:04', dlh: 12, dlm: 24, dayidx: 0},'20220326120000': {dt: '26/03', dm: 'Március 26', d: 'Szombat', ds: 'Szo', tmax: '+20', txfg: '000', txbg: 'ffb340', tmin: '+7', symb: 'd000', wx: 'Tiszta', winds: 14, windd: '270', sr: '05:38', ss: '18:05', dlh: 12, dlm: 27, dayidx: 1},'20220327120000': {dt: '27/03', dm: 'Március 27', d: 'Vasárnap', ds: 'V', tmax: '+20', txfg: '000', txbg: 'ffb340', tmin: '+6', symb: 'd300', wx: 'Enyhén borús', winds: 11, windd: '045', sr: '06:36', ss: '19:07', dlh: 12, dlm: 31, dayidx: 2},'20220328120000': {dt: '28/03', dm: 'Március 28', d: 'Hétfő', ds: 'H', tmax: '+21', txfg: '000', txbg: 'ffb340', tmin: '+7', symb: 'd000', wx: 'Tiszta', winds: 11, windd: '180', sr: '06:34', ss: '19:08', dlh: 12, dlm: 34, dayidx: 3},'20220329120000': {dt: '29/03', dm: 'Március 29', d: 'Kedd', ds: 'K', tmax: '+20', txfg: '000', txbg: 'ffb340', tmin: '+9', symb: 'd100', wx: 'Enyhén borús', winds: 18, windd: '270', sr: '06:32', ss: '19:09', dlh: 12, dlm: 37, dayidx: 4},'20220330120000': {dt: '30/03', dm: 'Március 30', d: 'Szerda', ds: 'Sze', tmax: '+19', txfg: '000', txbg: 'ffb340', tmin: '+9', symb: 'd300', wx: 'Felhős', winds: 22, windd: '225', sr: '06:30', ss: '19:11', dlh: 12, dlm: 41, dayidx: 5},'20220331120000': {dt: '31/03', dm: 'Március 31', d: 'Csütörtök', ds: 'Cs', tmax: '+17', txfg: '000', txbg: 'ffd940', tmin: '+9', symb: 'd410', wx: 'Borús és záporok ', winds: 11, windd: '180', sr: '06:28', ss: '19:12', dlh: 12, dlm: 44, dayidx: 6},'20220401120000': {dt: '01/04', dm: 'Április 1', d: 'Péntek', ds: 'P', tmax: '+19', txfg: '000', txbg: 'ffb340', tmin: '+9', symb: 'd410', wx: 'Borús és gyenge eső', winds: 18, windd: '225', sr: '06:26', ss: '19:13', dlh: 12, dlm: 47, dayidx: 7},'20220402120000': {dt: '02/04', dm: 'Április 2', d: 'Szombat', ds: 'Szo', tmax: '+12', txfg: '000', txbg: 'ffff40', tmin: '+6', symb: 'd410', wx: 'Borús és gyenge eső', winds: 11, windd: '000', sr: '06:24', ss: '19:15', dlh: 12, dlm: 51, dayidx: 8},'20220403120000': {dt: '03/04', dm: 'Április 3', d: 'Vasárnap', ds: 'V', tmax: '+10', txfg: '000', txbg: 'ffff40', tmin: '+2', symb: 'd400', wx: 'Felhős', winds: 29, windd: '315', sr: '06:23', ss: '19:16', dlh: 12, dlm: 53, dayidx: 9}},days: ['20220325120000','20220326120000','20220327120000','20220328120000','20220329120000','20220330120000','20220331120000','20220401120000','20220402120000','20220403120000'],hour: {'20220325110000': {time: '11:00', temp: '+15', winds: 10, windd: '135', windt: 'SO', flike: '+15', rain: 0.0, rainp: 2, rhum: 35, symb: 'd000', wx: 'Tiszta'},'20220325120000': {time: '12:00', temp: '+16', winds: 10, windd: '180', windt: 'S', flike: '+16', rain: 0.0, rainp: 2, rhum: 35, symb: 'd000', wx: 'Tiszta'},'20220325130000': {time: '13:00', temp: '+17', winds: 9, windd: '270', windt: 'V', flike: '+17', rain: 0.0, rainp: 2, rhum: 34, symb: 'd000', wx: 'Tiszta'},'20220325140000': {time: '14:00', temp: '+17', winds: 10, windd: '270', windt: 'V', flike: '+17', rain: 0.0, rainp: 2, rhum: 34, symb: 'd000', wx: 'Tiszta'},'20220325150000': {time: '15:00', temp: '+18', winds: 11, windd: '270', windt: 'V', flike: '+18', rain: 0.0, rainp: 2, rhum: 33, symb: 'd000', wx: 'Tiszta'},'20220325160000': {time: '16:00', temp: '+18', winds: 12, windd: '225', windt: 'SV', flike: '+18', rain: 0.0, rainp: 2, rhum: 34, symb: 'd000', wx: 'Tiszta'},'20220325170000': {time: '17:00', temp: '+16', winds: 9, windd: '225', windt: 'SV', flike: '+16', rain: 0.0, rainp: 2, rhum: 59, symb: 'd000', wx: 'Tiszta'},'20220325180000': {time: '18:00', temp: '+15', winds: 5, windd: '270', windt: 'V', flike: '+15', rain: 0.0, rainp: 2, rhum: 51, symb: 'd000', wx: 'Tiszta'},'20220325190000': {time: '19:00', temp: '+13', winds: 2, windd: '270', windt: 'V', flike: '+13', rain: 0.0, rainp: 2, rhum: 55, symb: 'n000', wx: 'Tiszta'},'20220325200000': {time: '20:00', temp: '+13', winds: 4, windd: '270', windt: 'V', flike: '+13', rain: 0.0, rainp: 2, rhum: 60, symb: 'n000', wx: 'Tiszta'},'20220325210000': {time: '21:00', temp: '+12', winds: 5, windd: '270', windt: 'V', flike: '+12', rain: 0.0, rainp: 2, rhum: 63, symb: 'n000', wx: 'Tiszta'},'20220325220000': {time: '22:00', temp: '+10', winds: 6, windd: '270', windt: 'V', flike: '+10', rain: 0.0, rainp: 2, rhum: 65, symb: 'n000', wx: 'Tiszta'},'20220325230000': {time: '23:00', temp: '+8', winds: 5, windd: '270', windt: 'V', flike: '+7', rain: 0.0, rainp: 2, rhum: 66, symb: 'n000', wx: 'Tiszta'},'20220326000000': {time: '00:00', temp: '+9', winds: 5, windd: '270', windt: 'V', flike: '+9', rain: 0.0, rainp: 2, rhum: 68, symb: 'n000', wx: 'Tiszta'},'20220326010000': {time: '01:00', temp: '+10', winds: 5, windd: '315', windt: 'NV', flike: '+10', rain: 0.0, rainp: 2, rhum: 69, symb: 'n000', wx: 'Tiszta'},'20220326020000': {time: '02:00', temp: '+9', winds: 6, windd: '315', windt: 'NV', flike: '+8', rain: 0.0, rainp: 2, rhum: 69, symb: 'n000', wx: 'Tiszta'},'20220326030000': {time: '03:00', temp: '+9', winds: 7, windd: '000', windt: 'N', flike: '+8', rain: 0.0, rainp: 2, rhum: 71, symb: 'n000', wx: 'Tiszta'},'20220326040000': {time: '04:00', temp: '+8', winds: 8, windd: '000', windt: 'N', flike: '+7', rain: 0.0, rainp: 2, rhum: 77, symb: 'n000', wx: 'Tiszta'},'20220326050000': {time: '05:00', temp: '+7', winds: 6, windd: '315', windt: 'NV', flike: '+6', rain: 0.0, rainp: 2, rhum: 79, symb: 'n000', wx: 'Tiszta'},'20220326060000': {time: '06:00', temp: '+7', winds: 5, windd: '315', windt: 'NV', flike: '+7', rain: 0.0, rainp: 2, rhum: 76, symb: 'd000', wx: 'Tiszta'},'20220326070000': {time: '07:00', temp: '+9', winds: 3, windd: '270', windt: 'V', flike: '+9', rain: 0.0, rainp: 2, rhum: 70, symb: 'd000', wx: 'Tiszta'},'20220326080000': {time: '08:00', temp: '+12', winds: 6, windd: '270', windt: 'V', flike: '+12', rain: 0.0, rainp: 2, rhum: 64, symb: 'd000', wx: 'Tiszta'},'20220326090000': {time: '09:00', temp: '+14', winds: 8, windd: '270', windt: 'V', flike: '+14', rain: 0.0, rainp: 2, rhum: 53, symb: 'd000', wx: 'Tiszta'},'20220326100000': {time: '10:00', temp: '+16', winds: 10, windd: '270', windt: 'V', flike: '+16', rain: 0.0, rainp: 2, rhum: 44, symb: 'd000', wx: 'Tiszta'},'20220326110000': {time: '11:00', temp: '+17', winds: 12, windd: '270', windt: 'V', flike: '+17', rain: 0.0, rainp: 2, rhum: 35, symb: 'd000', wx: 'Tiszta'},'20220326120000': {time: '12:00', temp: '+18', winds: 12, windd: '270', windt: 'V', flike: '+18', rain: 0.0, rainp: 2, rhum: 32, symb: 'd000', wx: 'Tiszta'},'20220326130000': {time: '13:00', temp: '+19', winds: 13, windd: '270', windt: 'V', flike: '+19', rain: 0.0, rainp: 2, rhum: 30, symb: 'd000', wx: 'Tiszta'},'20220326140000': {time: '14:00', temp: '+19', winds: 14, windd: '270', windt: 'V', flike: '+19', rain: 0.0, rainp: 2, rhum: 31, symb: 'd000', wx: 'Tiszta'},'20220326150000': {time: '15:00', temp: '+20', winds: 14, windd: '270', windt: 'V', flike: '+20', rain: 0.0, rainp: 2, rhum: 30, symb: 'd000', wx: 'Tiszta'},'20220326160000': {time: '16:00', temp: '+20', winds: 15, windd: '270', windt: 'V', flike: '+20', rain: 0.0, rainp: 2, rhum: 30, symb: 'd000', wx: 'Tiszta'},'20220326170000': {time: '17:00', temp: '+18', winds: 12, windd: '315', windt: 'NV', flike: '+18', rain: 0.0, rainp: 2, rhum: 39, symb: 'd000', wx: 'Tiszta'},'20220326180000': {time: '18:00', temp: '+15', winds: 8, windd: '315', windt: 'NV', flike: '+15', rain: 0.0, rainp: 2, rhum: 49, symb: 'd000', wx: 'Tiszta'},'20220326190000': {time: '19:00', temp: '+14', winds: 5, windd: '315', windt: 'NV', flike: '+14', rain: 0.0, rainp: 2, rhum: 54, symb: 'n000', wx: 'Tiszta'},'20220326200000': {time: '20:00', temp: '+14', winds: 6, windd: '315', windt: 'NV', flike: '+14', rain: 0.0, rainp: 2, rhum: 60, symb: 'n000', wx: 'Tiszta'},'20220326210000': {time: '21:00', temp: '+13', winds: 6, windd: '315', windt: 'NV', flike: '+13', rain: 0.0, rainp: 2, rhum: 64, symb: 'n000', wx: 'Tiszta'},'20220326220000': {time: '22:00', temp: '+12', winds: 7, windd: '315', windt: 'NV', flike: '+12', rain: 0.0, rainp: 2, rhum: 67, symb: 'n000', wx: 'Tiszta'},'20220326230000': {time: '23:00', temp: '+12', winds: 7, windd: '315', windt: 'NV', flike: '+12', rain: 0.0, rainp: 2, rhum: 68, symb: 'n000', wx: 'Tiszta'},'20220327000000': {time: '00:00', temp: '+12', winds: 7, windd: '315', windt: 'NV', flike: '+12', rain: 0.0, rainp: 2, rhum: 71, symb: 'n000', wx: 'Tiszta'},'20220327010000': {time: '01:00', temp: '+11', winds: 7, windd: '315', windt: 'NV', flike: '+11', rain: 0.0, rainp: 2, rhum: 70, symb: 'n000', wx: 'Tiszta'},'20220327030000': {time: '03:00', temp: '+11', winds: 8, windd: '315', windt: 'NV', flike: '+11', rain: 0.0, rainp: 2, rhum: 70, symb: 'n100', wx: 'Többnyire tiszta'},'20220327040000': {time: '04:00', temp: '+9', winds: 8, windd: '315', windt: 'NV', flike: '+8', rain: 0.0, rainp: 2, rhum: 69, symb: 'n100', wx: 'Többnyire tiszta'},'20220327050000': {time: '05:00', temp: '+9', winds: 9, windd: '315', windt: 'NV', flike: '+8', rain: 0.0, rainp: 2, rhum: 69, symb: 'n100', wx: 'Többnyire tiszta'},'20220327060000': {time: '06:00', temp: '+7', winds: 8, windd: '315', windt: 'NV', flike: '+6', rain: 0.0, rainp: 2, rhum: 69, symb: 'n000', wx: 'Tiszta'},'20220327070000': {time: '07:00', temp: '+6', winds: 7, windd: '315', windt: 'NV', flike: '+5', rain: 0.0, rainp: 2, rhum: 71, symb: 'd000', wx: 'Tiszta'},'20220327080000': {time: '08:00', temp: '+9', winds: 6, windd: '315', windt: 'NV', flike: '+8', rain: 0.0, rainp: 2, rhum: 64, symb: 'd000', wx: 'Tiszta'},'20220327090000': {time: '09:00', temp: '+13', winds: 8, windd: '000', windt: 'N', flike: '+13', rain: 0.0, rainp: 2, rhum: 58, symb: 'd000', wx: 'Tiszta'},'20220327100000': {time: '10:00', temp: '+15', winds: 9, windd: '000', windt: 'N', flike: '+15', rain: 0.0, rainp: 2, rhum: 53, symb: 'd000', wx: 'Tiszta'},'20220327110000': {time: '11:00', temp: '+16', winds: 11, windd: '045', windt: 'NO', flike: '+16', rain: 0.0, rainp: 2, rhum: 51, symb: 'd000', wx: 'Tiszta'},'20220327120000': {time: '12:00', temp: '+17', winds: 10, windd: '045', windt: 'NO', flike: '+17', rain: 0.0, rainp: 2, rhum: 49, symb: 'd300', wx: 'Felhős'},'20220327130000': {time: '13:00', temp: '+18', winds: 9, windd: '045', windt: 'NO', flike: '+18', rain: 0.0, rainp: 2, rhum: 47, symb: 'd300', wx: 'Felhős'},'20220327140000': {time: '14:00', temp: '+19', winds: 8, windd: '045', windt: 'NO', flike: '+19', rain: 0.0, rainp: 2, rhum: 45, symb: 'd300', wx: 'Felhős'},'20220327150000': {time: '15:00', temp: '+20', winds: 9, windd: '045', windt: 'NO', flike: '+20', rain: 0.0, rainp: 2, rhum: 45, symb: 'd300', wx: 'Felhős'},'20220327160000': {time: '16:00', temp: '+20', winds: 9, windd: '045', windt: 'NO', flike: '+20', rain: 0.0, rainp: 2, rhum: 45, symb: 'd300', wx: 'Felhős'},'20220327170000': {time: '17:00', temp: '+20', winds: 9, windd: '090', windt: 'O', flike: '+20', rain: 0.0, rainp: 2, rhum: 46, symb: 'd300', wx: 'Felhős'},'20220327200000': {time: '20:00', temp: '+15', winds: 4, windd: '045', windt: 'NO', flike: '+15', rain: 0.0, rainp: 2, rhum: 63, symb: 'n000', wx: 'Tiszta'},'20220327230000': {time: '23:00', temp: '+12', winds: 10, windd: '090', windt: 'O', flike: '+12', rain: 0.0, rainp: 2, rhum: 50, symb: 'n000', wx: 'Tiszta'},'20220328020000': {time: '02:00', temp: '+11', winds: 8, windd: '135', windt: 'SO', flike: '+11', rain: 0.0, rainp: 2, rhum: 63, symb: 'n100', wx: 'Többnyire tiszta'},'20220328050000': {time: '05:00', temp: '+8', winds: 8, windd: '180', windt: 'S', flike: '+7', rain: 0.0, rainp: 2, rhum: 73, symb: 'n000', wx: 'Tiszta'},'20220328080000': {time: '08:00', temp: '+8', winds: 5, windd: '135', windt: 'SO', flike: '+8', rain: 0.0, rainp: 2, rhum: 66, symb: 'd000', wx: 'Tiszta'},'20220328110000': {time: '11:00', temp: '+16', winds: 9, windd: '180', windt: 'S', flike: '+16', rain: 0.0, rainp: 2, rhum: 52, symb: 'd000', wx: 'Tiszta'},'20220328140000': {time: '14:00', temp: '+20', winds: 11, windd: '225', windt: 'SV', flike: '+20', rain: 0.0, rainp: 2, rhum: 41, symb: 'd000', wx: 'Tiszta'},'20220328170000': {time: '17:00', temp: '+21', winds: 12, windd: '180', windt: 'S', flike: '+21', rain: 0.0, rainp: 2, rhum: 40, symb: 'd000', wx: 'Tiszta'},'20220328200000': {time: '20:00', temp: '+16', winds: 6, windd: '180', windt: 'S', flike: '+16', rain: 0.0, rainp: 2, rhum: 67, symb: 'n000', wx: 'Tiszta'},'20220328230000': {time: '23:00', temp: '+12', winds: 10, windd: '225', windt: 'SV', flike: '+12', rain: 0.0, rainp: 2, rhum: 77, symb: 'n000', wx: 'Tiszta'},'20220329020000': {time: '02:00', temp: '+11', winds: 11, windd: '270', windt: 'V', flike: '+11', rain: 0.0, rainp: 2, rhum: 75, symb: 'n000', wx: 'Tiszta'},'20220329050000': {time: '05:00', temp: '+9', winds: 12, windd: '270', windt: 'V', flike: '+7', rain: 0.0, rainp: 2, rhum: 74, symb: 'n300', wx: 'Felhős'},'20220329080000': {time: '08:00', temp: '+10', winds: 12, windd: '270', windt: 'V', flike: '+8', rain: 0.0, rainp: 2, rhum: 68, symb: 'd000', wx: 'Tiszta'},'20220329110000': {time: '11:00', temp: '+16', winds: 20, windd: '270', windt: 'V', flike: '+16', rain: 0.0, rainp: 2, rhum: 45, symb: 'd000', wx: 'Tiszta'},'20220329140000': {time: '14:00', temp: '+19', winds: 17, windd: '270', windt: 'V', flike: '+19', rain: 0.0, rainp: 2, rhum: 36, symb: 'd100', wx: 'Többnyire tiszta'},'20220329170000': {time: '17:00', temp: '+20', winds: 18, windd: '270', windt: 'V', flike: '+20', rain: 0.0, rainp: 4, rhum: 34, symb: 'd300', wx: 'Felhős'},'20220329200000': {time: '20:00', temp: '+15', winds: 5, windd: '270', windt: 'V', flike: '+15', rain: 0.0, rainp: 2, rhum: 48, symb: 'n300', wx: 'Felhős'},'20220329230000': {time: '23:00', temp: '+12', winds: 13, windd: '270', windt: 'V', flike: '+12', rain: 0.0, rainp: 2, rhum: 48, symb: 'n200', wx: 'Enyhén borús'},'20220330020000': {time: '02:00', temp: '+11', winds: 11, windd: '270', windt: 'V', flike: '+11', rain: 0.0, rainp: 2, rhum: 70, symb: 'n300', wx: 'Felhős'},'20220330050000': {time: '05:00', temp: '+9', winds: 12, windd: '225', windt: 'SV', flike: '+7', rain: 0.0, rainp: 2, rhum: 75, symb: 'n300', wx: 'Felhős'},'20220330080000': {time: '08:00', temp: '+10', winds: 8, windd: '225', windt: 'SV', flike: '+9', rain: 0.0, rainp: 6, rhum: 69, symb: 'd300', wx: 'Felhős'},'20220330110000': {time: '11:00', temp: '+16', winds: 16, windd: '225', windt: 'SV', flike: '+16', rain: 0.0, rainp: 7, rhum: 50, symb: 'd400', wx: 'Borús'},'20220330140000': {time: '14:00', temp: '+19', winds: 21, windd: '225', windt: 'SV', flike: '+19', rain: 0.0, rainp: 11, rhum: 41, symb: 'd300', wx: 'Felhős'},'20220330170000': {time: '17:00', temp: '+16', winds: 14, windd: '225', windt: 'SV', flike: '+16', rain: 0.0, rainp: 25, rhum: 44, symb: 'd400', wx: 'Borús'},'20220330200000': {time: '20:00', temp: '+14', winds: 3, windd: '315', windt: 'NV', flike: '+14', rain: 0.0, rainp: 40, rhum: 69, symb: 'n400', wx: 'Borús'},'20220330230000': {time: '23:00', temp: '+13', winds: 10, windd: '180', windt: 'S', flike: '+13', rain: 0.0, rainp: 41, rhum: 66, symb: 'n400', wx: 'Borús'},'20220331020000': {time: '02:00', temp: '+12', winds: 8, windd: '180', windt: 'S', flike: '+12', rain: 0.2, rainp: 54, rhum: 48, symb: 'n410', wx: 'Borús és gyenge eső'},'20220331050000': {time: '05:00', temp: '+9', winds: 8, windd: '180', windt: 'S', flike: '+8', rain: 0.3, rainp: 67, rhum: 65, symb: 'n410', wx: 'Borús és gyenge eső'},'20220331080000': {time: '08:00', temp: '+10', winds: 9, windd: '135', windt: 'SO', flike: '+9', rain: 0.3, rainp: 80, rhum: 82, symb: 'd410', wx: 'Borús és gyenge eső'},'20220331110000': {time: '11:00', temp: '+15', winds: 10, windd: '180', windt: 'S', flike: '+15', rain: 0.6, rainp: 80, rhum: 63, symb: 'd410', wx: 'Borús és gyenge eső'},'20220331140000': {time: '14:00', temp: '+16', winds: 12, windd: '180', windt: 'S', flike: '+16', rain: 0.6, rainp: 80, rhum: 44, symb: 'd410', wx: 'Borús és gyenge eső'},'20220331170000': {time: '17:00', temp: '+17', winds: 8, windd: '090', windt: 'O', flike: '+17', rain: 1.1, rainp: 82, rhum: 58, symb: 'd420', wx: 'Borús és záporok '},'20220331200000': {time: '20:00', temp: '+12', winds: 3, windd: '045', windt: 'NO', flike: '+12', rain: 1.1, rainp: 84, rhum: 72, symb: 'n420', wx: 'Borús és záporok '},'20220331230000': {time: '23:00', temp: '+11', winds: 5, windd: '090', windt: 'O', flike: '+11', rain: 0.1, rainp: 71, rhum: 71, symb: 'n410', wx: 'Borús és gyenge eső'},'20220401020000': {time: '02:00', temp: '+11', winds: 7, windd: '180', windt: 'S', flike: '+11', rain: 0.1, rainp: 57, rhum: 69, symb: 'n410', wx: 'Borús és gyenge eső'},'20220401050000': {time: '05:00', temp: '+9', winds: 9, windd: '225', windt: 'SV', flike: '+8', rain: 0.2, rainp: 56, rhum: 77, symb: 'n410', wx: 'Borús és gyenge eső'},'20220401080000': {time: '08:00', temp: '+11', winds: 12, windd: '225', windt: 'SV', flike: '+11', rain: 0.2, rainp: 55, rhum: 85, symb: 'd410', wx: 'Borús és gyenge eső'},'20220401110000': {time: '11:00', temp: '+17', winds: 15, windd: '315', windt: 'NV', flike: '+17', rain: 0.1, rainp: 61, rhum: 70, symb: 'd410', wx: 'Borús és gyenge eső'},'20220401140000': {time: '14:00', temp: '+17', winds: 17, windd: '225', windt: 'SV', flike: '+17', rain: 0.1, rainp: 68, rhum: 55, symb: 'd410', wx: 'Borús és gyenge eső'},'20220401170000': {time: '17:00', temp: '+19', winds: 14, windd: '315', windt: 'NV', flike: '+19', rain: 0.4, rainp: 71, rhum: 88, symb: 'd410', wx: 'Borús és gyenge eső'},'20220401200000': {time: '20:00', temp: '+14', winds: 11, windd: '225', windt: 'SV', flike: '+14', rain: 0.4, rainp: 74, rhum: 64, symb: 'n410', wx: 'Borús és gyenge eső'},'20220401230000': {time: '23:00', temp: '+12', winds: 9, windd: '270', windt: 'V', flike: '+12', rain: 0.2, rainp: 68, rhum: 79, symb: 'n410', wx: 'Borús és gyenge eső'},'20220402020000': {time: '02:00', temp: '+11', winds: 8, windd: '225', windt: 'SV', flike: '+11', rain: 0.2, rainp: 61, rhum: 53, symb: 'n410', wx: 'Borús és gyenge eső'},'20220402050000': {time: '05:00', temp: '+8', winds: 6, windd: '315', windt: 'NV', flike: '+7', rain: 0.5, rainp: 68, rhum: 87, symb: 'n410', wx: 'Borús és gyenge eső'},'20220402080000': {time: '08:00', temp: '+8', winds: 4, windd: '000', windt: 'N', flike: '+8', rain: 0.5, rainp: 74, rhum: 78, symb: 'd410', wx: 'Borús és gyenge eső'},'20220402110000': {time: '11:00', temp: '+12', winds: 7, windd: '315', windt: 'NV', flike: '+12', rain: 0.4, rainp: 75, rhum: 47, symb: 'd410', wx: 'Borús és gyenge eső'},'20220402140000': {time: '14:00', temp: '+10', winds: 10, windd: '000', windt: 'N', flike: '+9', rain: 0.4, rainp: 75, rhum: 69, symb: 'd410', wx: 'Borús és gyenge eső'},'20220402170000': {time: '17:00', temp: '+12', winds: 7, windd: '315', windt: 'NV', flike: '+12', rain: 0.2, rainp: 70, rhum: 86, symb: 'd410', wx: 'Borús és gyenge eső'},'20220402200000': {time: '20:00', temp: '+8', winds: 4, windd: '000', windt: 'N', flike: '+8', rain: 0.2, rainp: 66, rhum: 72, symb: 'n410', wx: 'Borús és gyenge eső'},'20220402230000': {time: '23:00', temp: '+6', winds: 5, windd: '315', windt: 'NV', flike: '+5', rain: 0.0, rainp: 51, rhum: 84, symb: 'n310', wx: 'Felhős és gyenge eső'},'20220403020000': {time: '02:00', temp: '+5', winds: 6, windd: '000', windt: 'N', flike: '+4', rain: 0.0, rainp: 36, rhum: 63, symb: 'n300', wx: 'Felhős'},'20220403050000': {time: '05:00', temp: '+2', winds: 11, windd: '315', windt: 'NV', flike: '-1', rain: 0.0, rainp: 31, rhum: 77, symb: 'n400', wx: 'Borús'},'20220403080000': {time: '08:00', temp: '+3', winds: 16, windd: '315', windt: 'NV', flike: '-1', rain: 0.0, rainp: 26, rhum: 66, symb: 'd400', wx: 'Borús'},'20220403110000': {time: '11:00', temp: '+8', winds: 22, windd: '315', windt: 'NV', flike: '+5', rain: 0.0, rainp: 32, rhum: 51, symb: 'd400', wx: 'Borús'},'20220403140000': {time: '14:00', temp: '+8', winds: 27, windd: '315', windt: 'NV', flike: '+4', rain: 0.0, rainp: 38, rhum: 36, symb: 'd400', wx: 'Borús'},'20220403170000': {time: '17:00', temp: '+10', winds: 17, windd: '315', windt: 'NV', flike: '+8', rain: 0.0, rainp: 33, rhum: 53, symb: 'd300', wx: 'Felhős'},'20220403200000': {time: '20:00', temp: '+6', winds: 6, windd: '315', windt: 'NV', flike: '+5', rain: 0.0, rainp: 27, rhum: 36, symb: 'n300', wx: 'Felhős'},'20220403230000': {time: '23:00', temp: '+5', winds: 8, windd: '315', windt: 'NV', flike: '+3', rain: 0.0, rainp: 18, rhum: 75, symb: 'n400', wx: 'Borús'},'20220404020000': {time: '02:00', temp: '+4', winds: 9, windd: '315', windt: 'NV', flike: '+2', rain: 0.0, rainp: 10, rhum: 43, symb: 'n400', wx: 'Borús'},'20220404050000': {time: '05:00', temp: '+3', winds: 10, windd: '270', windt: 'V', flike: '+0', rain: 0.0, rainp: 8, rhum: 80, symb: 'n400', wx: 'Borús'},'20220404080000': {time: '08:00', temp: '+5', winds: 10, windd: '270', windt: 'V', flike: '+3', rain: 0.0, rainp: 6, rhum: 54, symb: 'd400', wx: 'Borús'},'20220404110000': {time: '11:00', temp: '+17', winds: 13, windd: '270', windt: 'V', flike: '+17', rain: 0.0, rainp: 18, rhum: 55, symb: 'd400', wx: 'Borús'},'20220404140000': {time: '14:00', temp: '+24', winds: 15, windd: '270', windt: 'V', flike: '+24', rain: 0.0, rainp: 29, rhum: 43, symb: 'd400', wx: 'Borús'},'20220404170000': {time: '17:00', temp: '+22', winds: 12, windd: '225', windt: 'SV', flike: '+22', rain: 0.0, rainp: 25, rhum: 40, symb: 'd400', wx: 'Borús'},'20220404200000': {time: '20:00', temp: '+14', winds: 9, windd: '225', windt: 'SV', flike: '+14', rain: 0.0, rainp: 21, rhum: 39, symb: 'n400', wx: 'Borús'},'20220404230000': {time: '23:00', temp: '+14', winds: 9, windd: '225', windt: 'SV', flike: '+14', rain: 0.0, rainp: 23, rhum: 57, symb: 'n400', wx: 'Borús'},'20220405020000': {time: '02:00', temp: '+16', winds: 9, windd: '225', windt: 'SV', flike: '+16', rain: 0.0, rainp: 25, rhum: 63, symb: 'n400', wx: 'Borús'},'20220405050000': {time: '05:00', temp: '+11', winds: 10, windd: '225', windt: 'SV', flike: '+11', rain: 0.0, rainp: 24, rhum: 71, symb: 'n400', wx: 'Borús'},'20220405080000': {time: '08:00', temp: '+10', winds: 11, windd: '225', windt: 'SV', flike: '+8', rain: 0.0, rainp: 24, rhum: 53, symb: 'd400', wx: 'Borús'}}}";
     */
    public function getforecast($param = null)
	{
		$forecast10days = [];
		
		$url = 'https://www.foreca.hu/Hungary/Baranya/B%C3%B3ly/10-day-forecast';

		$page = file_get_contents($url);

		if($param == 'forecastToDay'){
			$pos = strpos($page, 'var daily_data =');
			$content = substr($page, $pos + 17);
			$pos = strpos($content, '}}};</script>');
			$content = trim(substr($content, 0, $pos + 3));
			$content = $this->fixingBadJson($content);
		}

		$json = json_decode($content);

		//debug($json);

		// ------------------------------- OK ----------------------------------
		foreach( $json->d10 as $key => $day){
			$forecast10days[$key] = [
				'dm' => $day->dm,
				'd' => $day->d,
				'tmin' => $day->tmin,
				'tmax' => $day->tmax,
				'wx' => $day->wx,
				'sr' => $day->sr,
				'ss' => $day->ss,
			];
			//echo $day->dm . "; " . $day->d . "; " . $day->tmin . "; " . $day->tmax . "; " . $day->wx . "; " . $day->sr . "; " . $day->ss . "<br>";
			//debug($day);
		}

		//debug($forecast10days);
		
		$data = [];
		
		$this->loadModel("Forecasts");
		
		foreach( $forecast10days as $k => $day){
			$key = (string) $k;
			$dt = substr($key,0,4) . "-" . substr($key,4,2) . "-" . substr($key,6,2) . " " . substr($key,8,2) . ":" . substr($key,10,2) . ":" . substr($key,12,2);
			$data["name"] 	= date("Y") . " " . $day["dm"] . ". " . $day["d"];
			$data['date'] 	= new FrozenTime($dt);
			$data["year"] 	= date("Y");
			$data["day"] 	= $day["dm"];
			$data["d"] 		= $day["d"];
			$data["tmin"] 	= $day["tmin"];
			$data["tmax"] 	= $day["tmax"];
			$data["wx"] 	= $day["wx"];
			$data["sr"] 	= $day["sr"];
			$data["ss"] 	= $day["ss"];
			$forecast 		= $this->Forecasts->newEmptyEntity();
			$forecast 		= $this->Forecasts->patchEntity($forecast, $data);
			//debug($forecast->getErrors());
			$this->Forecasts->save($forecast);
		}
		die();

/*
		$days10 = json_encode($forecast10days);
*/


		foreach( $json->hour as $key =>$hour){
			//echo $key;
			
			$dt = substr($key,0,4) . "-" . substr($key,4,2) . "-" . substr($key,6,2) . " " . substr($key,8,2) . ":" . substr($key,10,2) . ":" . substr($key,12,2);
			
			echo date("Y.m.d. H:i:s", strtotime($dt));
			
			echo "<br>";
			//debug($key);
			
			debug($hour);
			
		}

		//debug($json);
		
		die();
	}


/*
<a href="/Hungary/Baranya/Bóly"> <p>Bóly, HU</p> <img src="//img-d.foreca.net/f/s/28x28v2/d000.png" alt="Tiszta" title="Tiszta" width="20" height="20"> <span class="warm"><strong>+18</strong>°</span> </a>
*/




	// http://192.168.254.215:8002/rss/forecast
    public function horoscope($param = null)
    {

		$horoscope = [];
		$contents = '';
		$date = '';
		$page = 1;
		
		if($param == 'page1'){
			$page = 1;
		}
		if($param == 'page2'){
			$page = 2;
		}
		if($param == 'page3'){
			$page = 3;
		}

		$atomLink = ['controller' => 'Rss', 'action' => 'horoscopes', '_ext' => 'rss']; // Example controller and action
		
		$this->loadModel('NewHoroscopes');

		$horoscopes = $this->NewHoroscopes->find('all', [
			'conditions' => [
				'year' => date("Y"),
				'month' => date("m"),
				'day' => date("d"),
			],
			'order' => [
				'id' => 'asc'
			],
			'limit' => 4,
			'page' => $page
		]);
		
		foreach($horoscopes as $h){
			$date = $h->date;
			$contents .= $h->name . ":\n" . $h->content . "\n\n";
		}
		
		$contents = trim($date . "\n\n" . $contents);

		$horoscope['channel']['title'] = "Napi horoszkóp";
		$horoscope['channel']['description'] = $contents;
		
		$rss = [
			'channel' => [
				'title' => $horoscope['channel']['title'],
				'link' => 'http://192.168.254.215:8002/rss/horoscopes/' . $param . '.rss',
				'description' => $horoscope['channel']['description'],
			],
			'items' => $horoscope
		];

		$this->set(['rss' => $rss, '_serialize' => 'rss']);

    }



	
}