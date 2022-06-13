<?php
// Baked at 2022.03.09. 15:12:24

/*
CRON-ba betenni ezeket:

	--- Valuta árfolyamok ---
	Naponta óránként lekérni:
	http://192.168.254.215:8002/collectrss/currencies
	
	--- Lottószámok ---
	Naponta délután 8-kor, 9-kor, 10 órakor és este 11 éséjfélkor is kb:
	http://192.168.254.215:8002/collectrss/lotteries

	--- Időjárás szövegesen ---
	Naponta 2 óránként:
	http://192.168.254.215:8002/Collectrss/weathers/data

	--- Satelit képek az időjárásról ---
	Naponta, 1 óránként:
	http://192.168.254.215:8002/Collectrss/weathers/mapCurrentDegree
	http://192.168.254.215:8002/Collectrss/weathers/mapSatelit

	--- Áramszünetek ---
	Áramszünetek XLSX lekérése az EON oldaláról powerbreaks táblába: Talán naponta valamikor? Mi a szokás?
	http://192.168.254.215:8002/Collectrss/powerbreaks.rss

	--- Horoszkópok ---
	Horoszkópok begyűjtése. Gondolom reggel 5, 6, 7, 8 óra körül már megvannak. Érdemes lenne ekkor ránézni az alábbi linkkel vagy utána is talán.
	De az is lehet, hogy éjfél után már megvannak és akkor reggel 8-ig óránként megnézni. (?)
	http://192.168.254.215:8002/Collectrss/horoscopes

	--- 10 napot időjárás előrejelzés ---
	10 napos időjárás előrejelzés: Minden nap 12 óra után, pl: 13, 14, 15 órakor, de inkább 2 óránként naponta
	http://192.168.254.215:8002/Collectrss/forecast

*/

declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use Cake\Utility\Xml;
use Cake\I18n\FrozenTime;
use Shuchkin\SimpleXLSX;

/**
 * HirstartWeathers Controller
 *
 * @property \App\Model\Table\HirstartWeathersTable $HirstartWeathers
 * @method \App\Model\Entity\HirstartWeather[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */

class CollectrssController extends AppController
{


	public $cities = ["Babarc","Belvárdgyula","Berkesd","Bezedek","Birján","Borjád","Bár","Dunaszekcső","Erdősmárok","Erzsébet","Felsőszentiván","Hásságy","Ivándárda","Kisbudmér","Kiskassa","Kislippó","Kisnyárád","Kátoly","Kölked","Lippó","Liptód","Magyarsarlós","Majs","Monyoród","Máriakéménd","Márok","Nagybudmér","Nagynyárád","Olasz","Pécsdevecser","Pócsa","Szajk","Szederkény","Szellő","Sárok","Sátorhely","Töttös","Versend","Újpetre"];
	public $zodiacs = ["kos" => "Kos", "bika" => "Bika", "ikrek" => "Ikrek", "rak" => "Rák", "oroszlan" => "Oroszlán", "szuz" => "Szűz", "merleg" => "Mérleg", "skorpio" => "Skorpió", "nyilas" => "Nyilas", "bak" => "Bak", "vizonto" => "Vízöntő", "halak" => "Halak"];

    /**
     * Initialize controller
     *
     * @return void
     */
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

		if(!file_exists(WWW_ROOT . 'files')){
			mkdir(WWW_ROOT . 'files');
		}
		
	}

/*
	Az információ begyűjtése CRON-nal megy minden óra 10. percében:

	http://192.168.254.215:8002/Collectrss/newForecast

*/
							
    public function newForecast()
	{
		$month 		= ['Január', 'Február', 'Március', 'Április', 'Május', 'Június', 'Július', 'Augusztus', 'Szeptember', 'Október', 'November', 'December'];
		$m 			= ['Jan.', 'Febr.', 'Márc.', 'Ápr.', 'Máj.', 'Jún.', 'Júl.', 'Aug.', 'Szept.', 'Okt.', 'Nov.', 'Dec.'];

		$url = 'https://www.foreca.hu/Hungary/Baranya/B%C3%B3ly/10-day-forecast';

		$data = [];
		$forecast10days = [];

		$page = file_get_contents($url);

		//https://www.foreca.hu/public/images/symbols/d300.svg

		$days 		= [];
		$weathers 	= [];
		$image 		= [];

		//for($i=0; $i<5; $i++){
		for($i=0; $i<10; $i++){
			$start='<div id="day_' . date("Y-m-d", strtotime("+" . $i . " days")) . '"';
			$end = 'mm</span></em></div></div></div>';
			$startPos = (int) strpos($page, $start);
			$content = substr($page, $startPos);
			$endPos = (int) strpos($content, $end);
			$content = substr($content, 0, $endPos + strlen($end));
			$days[$i] = $content;

			$weathers[$i]['id'] = $i;

/*
			//$start='<div id="day_' . date("Y-m-d", strtotime("+" . $i . " days")) . '"';
			//$end = 'mm</span></em></div></div></div>';
			$startPos = (int) strpos($page, $start);
			$content = substr($page, $startPos);
			$endPos = (int) strpos($content, $end);
			$content = substr($content, 0, $endPos + strlen($end));
			$days[$i] = $content;

			$weathers[$i]['id'] = $i;
*/



			// NAP
			$start 	= '<span class="date_long">';
			$end	= '</span>';
			$startPos = (int) strpos($days[$i], $start);
			$content = substr($days[$i], $startPos);
			$endPos = (int) strpos($content, $end);
			$content = strip_tags(substr($content, 0, $endPos + strlen($end)));
			$weathers[$i]['date'] 			= date("Y-m-d", strtotime("+" . $i . " days"));
			$weathers[$i]['y'] 				= date("Y", strtotime("+" . $i . " days"));
			$weathers[$i]['m'] 				= date("m", strtotime("+" . $i . " days"));
			$weathers[$i]['d'] 				= date("d", strtotime("+" . $i . " days"));
			$weathers[$i]['month'] 			= $month[(int) date("m", strtotime("+" . $i . " days"))-1];
			$weathers[$i]['month_short'] 	= $m[(int) date("m", strtotime("+" . $i . " days"))-1];
			$weathers[$i]['day_name'] 		= $content;
			// /NAP

			// Szimbólum
			$start 	= '<p class="symb"><img src="/';
			$end	= '" ';
			$startPos = (int) strpos($days[$i], $start);
			$content = substr($days[$i], $startPos);
			$endPos = (int) strpos($content, $end);
			$content = substr($content, strlen($start)-1);
			$endPos = (int) strpos($content, $end);
			$content = substr($content, 0, $endPos);
			//$content = strip_tags(substr($content, 0, $endPos + strlen($end)));
			//debug($content);
			//$weathers[$i]['symb'] = $content;
			//'symb' => '/public/images/symbols/d210.svg',
			$content = substr($content, strlen('/public/images/symbols/'), 4);
			$weathers[$i]['symb_name'] = $content;
			$weathers[$i]['symb_jpg'] = '/images/weathers/forecasts/' . $content . '.jpg';
			$weathers[$i]['symb_png'] = '/images/weathers/forecasts/' . $content . '.png';

			//$weathers[$i]['symb_url'] = '<img src="https://www.foreca.hu' . $content .'" /> ';
			// /Szimbólum


			// Időjárás szöveggel
			$start 	= '<p class="symb"><img src="/';
			$end	= '" ';
			$startPos = (int) strpos($days[$i], $start);
			$content = substr($days[$i], $startPos);
			$endPos = (int) strpos($content, $end);
			$content = substr($content, strlen($start)-1);
			$wcontent = $content;
			$endPos = (int) strpos($content, $end);
			$content = substr($content, 0, $endPos);
			//debug($content);
			$start = 'alt="';
			$end = '" ';
			$startPos = (int) strpos($wcontent, $start);
			$wcontent = substr($wcontent, $startPos);
			$wcontent = substr($wcontent, strlen($start));
			$endPos = (int) strpos($wcontent, $end);
			$wcontent = substr($wcontent, 0, $endPos);
			//debug($wcontent);
			$weathers[$i]['weather'] = $wcontent;
			// /Időjárás szöveggel



			// Szélirány ikonok
			//-------------- szélirány: fokban ----------------
			$start 	= '<div class="windContainer"><div><img src="//img-d.foreca.net/f/s/w-21x21/';
			$end	= '.png"';
			$startPos = (int) strpos($days[$i], $start);
			$content = substr($days[$i], $startPos);
			$endPos = (int) strpos($content, $end);
			$content = substr($content, strlen($start));
			$endPos = (int) strpos($content, $end);
			$content = substr($content, 0, $endPos);
			$weathers[$i]['wind_degree'] = $content;
			$weathers[$i]['wind_jpg'] = '/images/weathers/forecasts/' . $content . '.jpg';
			$weathers[$i]['wind_png'] = '/images/weathers/forecasts/' . $content . '.png';

			//-------------- szélsebesség: km/h -------------
			$start 	= '<span class="value wind wind_kmh">';
			$end	= '</span>';
			$startPos = (int) strpos($days[$i], $start);
			$content = substr($days[$i], $startPos);
			$endPos = (int) strpos($content, $end);
			$content = substr($content, strlen($start));
			$endPos = (int) strpos($content, $end);
			$content = substr($content, 0, $endPos);
			$content = str_replace(".", ",", $content);
			$content = str_replace("km/h", "km/ó", $content);
			$weathers[$i]['wind_speed'] = $content;
			// /Szélirány ikonok


			// Eső mennyisége
			//<p class="rain">
			//	<span class="value rain rain_mm"></span>
			//	<span class="value rain rain_in"></span>
			//</p>

			
			//-------------- Eső menyisége: mm -------------
			$start 	= '<span class="value rain rain_mm clear">';
			$end	= '</span>';
			$startPos = (int) strpos($days[$i], $start);
			//debug($startPos);
			if($startPos == 0){
				$start 	= '<span class="value rain rain_mm">';			
				$startPos = (int) strpos($days[$i], $start);
				//debug($startPos);
			}
			//$weathers[$i]['rain'] = '0 mm.';
			$weathers[$i]['rain'] = '-';	// Ha alább nem lenne érték adva, akkor ez a defaultja.
			if($startPos > 0){
				$content = substr($days[$i], $startPos);
				$endPos = (int) strpos($content, $end);
				$content = substr($content, strlen($start));
				//debug($content); die();
				$endPos = (int) strpos($content, $end);
				$content = substr($content, 0, $endPos);
				$content = str_replace(".", ",", $content);
				$weathers[$i]['rain'] = $content;
			}
			// /Eső mennyisége




			// MIN
			$content = $days[$i];
			//$start = '<p class="temp';
			$start = 'title="Min">';
			$end = '</p>';
			$startPos = strpos($content, $start);
			$content = substr($content, $startPos);
			$endPos = strpos($content, $end);
			$content = substr($content, 0, $endPos);
			$content = str_replace("&deg;", "°C", $content);

			$content = substr($content, strpos($content, 'temp_c'));
			$content = substr($content, strpos($content, '">')+2);
			$content = substr($content, 0, strpos($content, '</span>'));
			$content = str_replace(".", ",", $content);
			$weathers[$i]['min'] = $content;
			// /MIN


			// MIN
			$content = $days[$i];
			//$start = '<p class="temp';
			$start = 'title="Max">';
			$end = '</p>';
			$startPos = strpos($content, $start);
			$content = substr($content, $startPos);
			$endPos = strpos($content, $end);
			$content = substr($content, 0, $endPos);
			$content = str_replace("&deg;", "°C", $content);

			$content = substr($content, strpos($content, 'temp_c'));
			$content = substr($content, strpos($content, '">')+2);
			$content = substr($content, 0, strpos($content, '</span>'));
			$content = str_replace(".", ",", $content);
			$weathers[$i]['max'] = $content;
			// /MIN

			$weathers[$i]['updated'] = date("Y.m.d. H:i:s");

		}

		$hourly 	= [];
		$sunRise 	= '';
		$sunSet 	= '';
		$dayLength 	= '';

		// ---------------- Óránkénti TARTALOM ----------------
		$start = '<section class="hourly">';
		$end = '</section>';
		$startPos = (int) strpos($page, $start);
		$content = substr($page, $startPos);
		$endPos = (int) strpos($content, $end);
		$hourlyFullContent = substr($content, 0, $endPos + strlen($end));

		// --------------- Napkelte ---------------
		$start 		= '<div class="suntimes">';
		$startPos 	= (int) strpos($hourlyFullContent, $start);
		$content 	= substr($hourlyFullContent, $startPos);
		$start 		= '<span class="value time time_24h">';
		$end 		= '</span>';
		$startPos 	= (int) strpos($content, $start);
		$content 	= substr($content, $startPos + strlen($start));
		$endPos 	= (int) strpos($content, $end);
		$content 	= substr($content, 0, $endPos);
		//debug($content); die();
		$sunRise = $content;



		// --------------- Napnyugta ---------------
		$start 		= '<div class="suntimes">';
		$startPos 	= (int) strpos($hourlyFullContent, $start);
		$content 	= substr($hourlyFullContent, $startPos);
		$start 		= '<span class="value time time_24h">';
		$end 		= '</span>';
		$startPos 	= (int) strpos($content, $start, strlen('<div class="suntimes">' . '<div>' . '<p class="time">' . '<span class="value time time_24h">' . '<p class="time">' . '<span class="value time time_24h">'));
		$content 	= substr($content, $startPos + strlen($start));
		$endPos 	= (int) strpos($content, $end);
		$content 	= substr($content, 0, $endPos);
		//debug($content); die();

		$sunSet = $content;


		$start = '<p class="time">';
		$end = '</p>';
		$startPos 	= (int) strpos($hourlyFullContent, $start);
		$content 	= substr($hourlyFullContent, $startPos);

		$startPos 	= (int) strpos($hourlyFullContent, $start, $startPos + 5);
		$content 	= substr($hourlyFullContent, $startPos);

		$startPos 	= (int) strpos($hourlyFullContent, $start, $startPos + 5);
		$content 	= substr($hourlyFullContent, $startPos);

		$endPos 	= (int) strpos($content, $end);
		$content 	= strip_tags(substr($content, 0, $endPos));

		$content = str_replace("h","ó",$content);
		$content = str_replace("min","perc",$content);

		$dayLength = $content;

		$start = '<div id="hour_' . date("Y-m-d") . '"';
		$end = '<div id="hour_' . date("Y-m-d", strtotime("+1 day")) . '"';

		$startPos = (int) strpos($hourlyFullContent, $start);
		$endPos = (int) strpos($hourlyFullContent, $end);

		$hourlyFullContent = substr($hourlyFullContent, $startPos, $endPos - strlen($end) + 1);
		
		//debug($hourlyFullContent); die();
		// ---------- a mai napi tartalom kiszedve: OK ---------

		// ---------------- Óránkénti TARTALOM tömbbe helyezve ----------------
		$hourly = [];
		$tmp = [];
		$endPos = 0;
		for($i=0; $i<=10; $i++){
			$start = '<div class="row">';
			$end = '</div>';
			$startPos = (int) strpos($hourlyFullContent, $start);
			$content = substr($hourlyFullContent, $startPos);
			$endPos = (int) strpos($content, $end);
			$content = substr($content, 0, $endPos + strlen($end));
			$tmp[$i] = $content;
			$hourlyFullContent = substr($hourlyFullContent, $endPos + strlen($end));
		}
		unset($tmp[1]);	// Ezt az elemet el kell távolítani...
		foreach($tmp as $hour){	// A törlés miatt újraírva
			$hourly[] = $hour;
		}
		unset($tmp);

		//debug($hourly); die();

		// Óránkénti adatok a tömbelemekben: OK
		// ---------------- Óránkénti ----------------
		for($i=0; $i<10; $i++){

			$hour = (string) $hourly[$i];
			//debug($hour);

			$forecastHourly[$i]['sun_rise']    = $sunRise;
			$forecastHourly[$i]['sun_set'] 	   = $sunSet;
			$forecastHourly[$i]['day_lengths'] = $dayLength;

			// ------------------------- Hour -------------------------
			$start = '<span class="value time time_24h">';
			$end = '</span>';
			$startPos 	= (int) strpos($hour, $start);
			$content 	= substr($hour, $startPos + strlen($start));
			$endPos 	= (int) strpos($content, $end);
			$content 	= substr($content, 0, $endPos);
			$forecastHourly[$i]['hour'] = $content;

			// ------------------------- Symbol -------------------------
			$start 		= '<img src="/public/images/symbols/';
			$end 		= '.svg"';
			$startPos 	= (int) strpos($hour, $start);
			$content 	= substr($hour, $startPos + strlen($start));
			$endPos 	= (int) strpos($content, $end);
			$content 	= substr($content, 0, $endPos);
			$forecastHourly[$i]['symbol'] = $content;
			$forecastHourly[$i]['symb_jpg'] = '/images/weathers/forecasts/' . $content . '.jpg';
			$forecastHourly[$i]['symb_png'] = '/images/weathers/forecasts/' . $content . '.png';
			
			

			// ------------------------- Weather -------------------------
			$start = 'alt="';
			$end = '"';
			$startPos 	= (int) strpos($hour, $start);
			$content 	= substr($hour, $startPos + strlen($start));
			$endPos 	= (int) strpos($content, $end);
			$content 	= substr($content, 0, $endPos);
			$forecastHourly[$i]['weather'] = $content;

			// ------------------------- DEGREE -------------------------
			$start = '<p class="temp">';
			$end = '</span>';
			$startPos 	= (int) strpos($hour, $start);
			$content 	= substr($hour, $startPos + strlen($start));
			$endPos 	= (int) strpos($content, $end);
			$content 	= substr($content, 0, $endPos);
			$forecastHourly[$i]['degree'] = strip_tags(str_replace("&deg;","°C",$content));

			// ------------------------- FEEL -------------------------
			$start = '<p class="flike">';
			$end = '</span>';
			$startPos 	= (int) strpos($hour, $start);
			$content 	= substr($hour, $startPos + strlen($start));
			$endPos 	= (int) strpos($content, $end);
			$content 	= substr($content, 0, $endPos);
			$forecastHourly[$i]['feel'] = strip_tags(str_replace("&deg;","°C",$content));

			// ------------------------- WIND SYMBOL -------------------------
			$start = '<img src="//img-d.foreca.net/f/s/w-21x21/';
			$end = '.png"';
			$startPos 	= (int) strpos($hour, $start);
			$content 	= substr($hour, $startPos + strlen($start));
			$endPos 	= (int) strpos($content, $end);
			$content 	= substr($content, 0, $endPos);
			$forecastHourly[$i]['wind_symbol'] = $content;
			$forecastHourly[$i]['wind_jpg'] = '/images/weathers/forecasts/' . $content . '.jpg';
			$forecastHourly[$i]['wind_png'] = '/images/weathers/forecasts/' . $content . '.png';


			// ------------------------- WIND MS -------------------------
			$start = '<span class="value wind wind_ms">';
			$end = '</span>';
			$startPos 	= (int) strpos($hour, $start);
			$content 	= substr($hour, $startPos + strlen($start));
			$endPos 	= (int) strpos($content, $end);
			//$content	= str_replace("m/s", "m/mp", $content);
			$content 	= substr($content, 0, $endPos);
			$forecastHourly[$i]['wind_ms'] = $content;

			// ------------------------- WIND KMH -------------------------
			$start = '<span class="value wind wind_kmh">';
			$end = '</span>';
			$startPos 	= (int) strpos($hour, $start);
			$content 	= substr($hour, $startPos + strlen($start));
			$endPos 	= (int) strpos($content, $end);
			$content 	= substr($content, 0, $endPos);
			$content	= str_replace("km/h", "km/ó", $content);
			$forecastHourly[$i]['wind_kmh'] = $content;

			// ------------------------- HUM -------------------------
			$start = '<p class="rhum">';
			$end = '</p>';
			$startPos 	= (int) strpos($hour, $start);
			$content 	= substr($hour, $startPos + strlen($start));
			$endPos 	= (int) strpos($content, $end);
			$content 	= substr($content, 0, $endPos);
			$forecastHourly[$i]['rhum'] = $content;

			// ------------------------- RAIN_mm -------------------------
			$content = '';
			$start = '<span class="value rain rain_mm">';
			$end = '</span>';
			$startPos 	= (int) strpos($hour, $start);
			$content 	= substr($hour, $startPos + strlen($start));
			$endPos 	= (int) strpos($content, $end);
			$content 	= substr($content, 0, $endPos);				
			if($content == ''){
				//$content = '0 mm.';
				$content = '-';
			}
			$forecastHourly[$i]['rain_mm'] = $content;

			// ------------------------- WX -------------------------
			$start = '<p class="wx">';
			$end = '</p>';
			$startPos 	= (int) strpos($hour, $start);
			$content 	= substr($hour, $startPos + strlen($start));
			$endPos 	= (int) strpos($content, $end);
			$content 	= substr($content, 0, $endPos);
			$forecastHourly[$i]['wx'] = $content;

			$forecastHourly[$i]['updated'] = date("Y.m.d. H:i:s");
			//debug($forecastHourly);

			//$i++;
		}

		//debug($forecastHourly); die();

		// ---------------- Mentés JSON file-ba -------------
		if(count($weathers)>0){
			$jsonFile = WWW_ROOT . "files" . DS . "forecast.json";
			if(file_exists($jsonFile)){
				unlink($jsonFile);
			}
			file_put_contents($jsonFile, json_encode($weathers));
		}

		// ---------------- Mentés JSON file-ba -------------
		if(count($forecastHourly)>0){
			$jsonFile = WWW_ROOT . "files" . DS . "hourly.json";
			if(file_exists($jsonFile)){
				unlink($jsonFile);
			}
			file_put_contents($jsonFile, json_encode($forecastHourly));
		}

		die();

/*
		Szélirány ikonok elhozva!
		// 'https://img-d.foreca.net/f/s/w-21x21/w000.png'

		for($i=0; $i<360; $i++){
			$url = 'https://img-d.foreca.net/f/s/w-21x21/w' . substr('000' . $i, -3) . '.png';
			$remoteFile = file_get_contents($url);
			//echo $url . "<br>";

			if($remoteFile){
				$localFile = WWW_ROOT . "files" . DS . 'w' . substr('000' . $i, -3) . '.png';
				if(file_exists($localFile)){
					unlink($localFile);
				}
				file_put_contents($localFile, $remoteFile);
			}
		}

		die('xxx');
*/

/*
		// ---------------------------- Ikonok letöltve! -------------------------------
		for($i=0; $i<1; $i++){
			//$url = 'https://www.foreca.hu/public/images/symbols/n' . substr('000' . $i, -3) . '.svg';
			$url = 'https://www.foreca.hu/public/images/symbols/d' . substr('000' . $i, -3) . '.svg';
			$remoteFile = file_get_contents($url);
			//echo $url . "<br>";

			if($remoteFile){
				$localFile = WWW_ROOT . "files" . DS . 'weather_day_' . substr('000' . $i, -3) . '.svg';
				if(file_exists($localFile)){
					unlink($localFile);
				}
				file_put_contents($localFile, $remoteFile);
			}
		}
		die('xxx');
*/


	}	// newForecast()









    /**
	 * http://192.168.254.215:8002/tests/forecast
	 // $daily_data =  "{mode: 'full',rows: 2,'10d': { '20220325120000': {dt: '25/03', dm: 'Március 25', d: 'Péntek', ds: 'P', tmax: '+18', txfg: '000', txbg: 'ffb340', tmin: '+8', symb: 'd000', wx: 'Tiszta', winds: 11, windd: '225', sr: '05:40', ss: '18:04', dlh: 12, dlm: 24, dayidx: 0},'20220326120000': {dt: '26/03', dm: 'Március 26', d: 'Szombat', ds: 'Szo', tmax: '+20', txfg: '000', txbg: 'ffb340', tmin: '+7', symb: 'd000', wx: 'Tiszta', winds: 14, windd: '270', sr: '05:38', ss: '18:05', dlh: 12, dlm: 27, dayidx: 1},'20220327120000': {dt: '27/03', dm: 'Március 27', d: 'Vasárnap', ds: 'V', tmax: '+20', txfg: '000', txbg: 'ffb340', tmin: '+6', symb: 'd300', wx: 'Enyhén borús', winds: 11, windd: '045', sr: '06:36', ss: '19:07', dlh: 12, dlm: 31, dayidx: 2},'20220328120000': {dt: '28/03', dm: 'Március 28', d: 'Hétfő', ds: 'H', tmax: '+21', txfg: '000', txbg: 'ffb340', tmin: '+7', symb: 'd000', wx: 'Tiszta', winds: 11, windd: '180', sr: '06:34', ss: '19:08', dlh: 12, dlm: 34, dayidx: 3},'20220329120000': {dt: '29/03', dm: 'Március 29', d: 'Kedd', ds: 'K', tmax: '+20', txfg: '000', txbg: 'ffb340', tmin: '+9', symb: 'd100', wx: 'Enyhén borús', winds: 18, windd: '270', sr: '06:32', ss: '19:09', dlh: 12, dlm: 37, dayidx: 4},'20220330120000': {dt: '30/03', dm: 'Március 30', d: 'Szerda', ds: 'Sze', tmax: '+19', txfg: '000', txbg: 'ffb340', tmin: '+9', symb: 'd300', wx: 'Felhős', winds: 22, windd: '225', sr: '06:30', ss: '19:11', dlh: 12, dlm: 41, dayidx: 5},'20220331120000': {dt: '31/03', dm: 'Március 31', d: 'Csütörtök', ds: 'Cs', tmax: '+17', txfg: '000', txbg: 'ffd940', tmin: '+9', symb: 'd410', wx: 'Borús és záporok ', winds: 11, windd: '180', sr: '06:28', ss: '19:12', dlh: 12, dlm: 44, dayidx: 6},'20220401120000': {dt: '01/04', dm: 'Április 1', d: 'Péntek', ds: 'P', tmax: '+19', txfg: '000', txbg: 'ffb340', tmin: '+9', symb: 'd410', wx: 'Borús és gyenge eső', winds: 18, windd: '225', sr: '06:26', ss: '19:13', dlh: 12, dlm: 47, dayidx: 7},'20220402120000': {dt: '02/04', dm: 'Április 2', d: 'Szombat', ds: 'Szo', tmax: '+12', txfg: '000', txbg: 'ffff40', tmin: '+6', symb: 'd410', wx: 'Borús és gyenge eső', winds: 11, windd: '000', sr: '06:24', ss: '19:15', dlh: 12, dlm: 51, dayidx: 8},'20220403120000': {dt: '03/04', dm: 'Április 3', d: 'Vasárnap', ds: 'V', tmax: '+10', txfg: '000', txbg: 'ffff40', tmin: '+2', symb: 'd400', wx: 'Felhős', winds: 29, windd: '315', sr: '06:23', ss: '19:16', dlh: 12, dlm: 53, dayidx: 9}},days: ['20220325120000','20220326120000','20220327120000','20220328120000','20220329120000','20220330120000','20220331120000','20220401120000','20220402120000','20220403120000'],hour: {'20220325110000': {time: '11:00', temp: '+15', winds: 10, windd: '135', windt: 'SO', flike: '+15', rain: 0.0, rainp: 2, rhum: 35, symb: 'd000', wx: 'Tiszta'},'20220325120000': {time: '12:00', temp: '+16', winds: 10, windd: '180', windt: 'S', flike: '+16', rain: 0.0, rainp: 2, rhum: 35, symb: 'd000', wx: 'Tiszta'},'20220325130000': {time: '13:00', temp: '+17', winds: 9, windd: '270', windt: 'V', flike: '+17', rain: 0.0, rainp: 2, rhum: 34, symb: 'd000', wx: 'Tiszta'},'20220325140000': {time: '14:00', temp: '+17', winds: 10, windd: '270', windt: 'V', flike: '+17', rain: 0.0, rainp: 2, rhum: 34, symb: 'd000', wx: 'Tiszta'},'20220325150000': {time: '15:00', temp: '+18', winds: 11, windd: '270', windt: 'V', flike: '+18', rain: 0.0, rainp: 2, rhum: 33, symb: 'd000', wx: 'Tiszta'},'20220325160000': {time: '16:00', temp: '+18', winds: 12, windd: '225', windt: 'SV', flike: '+18', rain: 0.0, rainp: 2, rhum: 34, symb: 'd000', wx: 'Tiszta'},'20220325170000': {time: '17:00', temp: '+16', winds: 9, windd: '225', windt: 'SV', flike: '+16', rain: 0.0, rainp: 2, rhum: 59, symb: 'd000', wx: 'Tiszta'},'20220325180000': {time: '18:00', temp: '+15', winds: 5, windd: '270', windt: 'V', flike: '+15', rain: 0.0, rainp: 2, rhum: 51, symb: 'd000', wx: 'Tiszta'},'20220325190000': {time: '19:00', temp: '+13', winds: 2, windd: '270', windt: 'V', flike: '+13', rain: 0.0, rainp: 2, rhum: 55, symb: 'n000', wx: 'Tiszta'},'20220325200000': {time: '20:00', temp: '+13', winds: 4, windd: '270', windt: 'V', flike: '+13', rain: 0.0, rainp: 2, rhum: 60, symb: 'n000', wx: 'Tiszta'},'20220325210000': {time: '21:00', temp: '+12', winds: 5, windd: '270', windt: 'V', flike: '+12', rain: 0.0, rainp: 2, rhum: 63, symb: 'n000', wx: 'Tiszta'},'20220325220000': {time: '22:00', temp: '+10', winds: 6, windd: '270', windt: 'V', flike: '+10', rain: 0.0, rainp: 2, rhum: 65, symb: 'n000', wx: 'Tiszta'},'20220325230000': {time: '23:00', temp: '+8', winds: 5, windd: '270', windt: 'V', flike: '+7', rain: 0.0, rainp: 2, rhum: 66, symb: 'n000', wx: 'Tiszta'},'20220326000000': {time: '00:00', temp: '+9', winds: 5, windd: '270', windt: 'V', flike: '+9', rain: 0.0, rainp: 2, rhum: 68, symb: 'n000', wx: 'Tiszta'},'20220326010000': {time: '01:00', temp: '+10', winds: 5, windd: '315', windt: 'NV', flike: '+10', rain: 0.0, rainp: 2, rhum: 69, symb: 'n000', wx: 'Tiszta'},'20220326020000': {time: '02:00', temp: '+9', winds: 6, windd: '315', windt: 'NV', flike: '+8', rain: 0.0, rainp: 2, rhum: 69, symb: 'n000', wx: 'Tiszta'},'20220326030000': {time: '03:00', temp: '+9', winds: 7, windd: '000', windt: 'N', flike: '+8', rain: 0.0, rainp: 2, rhum: 71, symb: 'n000', wx: 'Tiszta'},'20220326040000': {time: '04:00', temp: '+8', winds: 8, windd: '000', windt: 'N', flike: '+7', rain: 0.0, rainp: 2, rhum: 77, symb: 'n000', wx: 'Tiszta'},'20220326050000': {time: '05:00', temp: '+7', winds: 6, windd: '315', windt: 'NV', flike: '+6', rain: 0.0, rainp: 2, rhum: 79, symb: 'n000', wx: 'Tiszta'},'20220326060000': {time: '06:00', temp: '+7', winds: 5, windd: '315', windt: 'NV', flike: '+7', rain: 0.0, rainp: 2, rhum: 76, symb: 'd000', wx: 'Tiszta'},'20220326070000': {time: '07:00', temp: '+9', winds: 3, windd: '270', windt: 'V', flike: '+9', rain: 0.0, rainp: 2, rhum: 70, symb: 'd000', wx: 'Tiszta'},'20220326080000': {time: '08:00', temp: '+12', winds: 6, windd: '270', windt: 'V', flike: '+12', rain: 0.0, rainp: 2, rhum: 64, symb: 'd000', wx: 'Tiszta'},'20220326090000': {time: '09:00', temp: '+14', winds: 8, windd: '270', windt: 'V', flike: '+14', rain: 0.0, rainp: 2, rhum: 53, symb: 'd000', wx: 'Tiszta'},'20220326100000': {time: '10:00', temp: '+16', winds: 10, windd: '270', windt: 'V', flike: '+16', rain: 0.0, rainp: 2, rhum: 44, symb: 'd000', wx: 'Tiszta'},'20220326110000': {time: '11:00', temp: '+17', winds: 12, windd: '270', windt: 'V', flike: '+17', rain: 0.0, rainp: 2, rhum: 35, symb: 'd000', wx: 'Tiszta'},'20220326120000': {time: '12:00', temp: '+18', winds: 12, windd: '270', windt: 'V', flike: '+18', rain: 0.0, rainp: 2, rhum: 32, symb: 'd000', wx: 'Tiszta'},'20220326130000': {time: '13:00', temp: '+19', winds: 13, windd: '270', windt: 'V', flike: '+19', rain: 0.0, rainp: 2, rhum: 30, symb: 'd000', wx: 'Tiszta'},'20220326140000': {time: '14:00', temp: '+19', winds: 14, windd: '270', windt: 'V', flike: '+19', rain: 0.0, rainp: 2, rhum: 31, symb: 'd000', wx: 'Tiszta'},'20220326150000': {time: '15:00', temp: '+20', winds: 14, windd: '270', windt: 'V', flike: '+20', rain: 0.0, rainp: 2, rhum: 30, symb: 'd000', wx: 'Tiszta'},'20220326160000': {time: '16:00', temp: '+20', winds: 15, windd: '270', windt: 'V', flike: '+20', rain: 0.0, rainp: 2, rhum: 30, symb: 'd000', wx: 'Tiszta'},'20220326170000': {time: '17:00', temp: '+18', winds: 12, windd: '315', windt: 'NV', flike: '+18', rain: 0.0, rainp: 2, rhum: 39, symb: 'd000', wx: 'Tiszta'},'20220326180000': {time: '18:00', temp: '+15', winds: 8, windd: '315', windt: 'NV', flike: '+15', rain: 0.0, rainp: 2, rhum: 49, symb: 'd000', wx: 'Tiszta'},'20220326190000': {time: '19:00', temp: '+14', winds: 5, windd: '315', windt: 'NV', flike: '+14', rain: 0.0, rainp: 2, rhum: 54, symb: 'n000', wx: 'Tiszta'},'20220326200000': {time: '20:00', temp: '+14', winds: 6, windd: '315', windt: 'NV', flike: '+14', rain: 0.0, rainp: 2, rhum: 60, symb: 'n000', wx: 'Tiszta'},'20220326210000': {time: '21:00', temp: '+13', winds: 6, windd: '315', windt: 'NV', flike: '+13', rain: 0.0, rainp: 2, rhum: 64, symb: 'n000', wx: 'Tiszta'},'20220326220000': {time: '22:00', temp: '+12', winds: 7, windd: '315', windt: 'NV', flike: '+12', rain: 0.0, rainp: 2, rhum: 67, symb: 'n000', wx: 'Tiszta'},'20220326230000': {time: '23:00', temp: '+12', winds: 7, windd: '315', windt: 'NV', flike: '+12', rain: 0.0, rainp: 2, rhum: 68, symb: 'n000', wx: 'Tiszta'},'20220327000000': {time: '00:00', temp: '+12', winds: 7, windd: '315', windt: 'NV', flike: '+12', rain: 0.0, rainp: 2, rhum: 71, symb: 'n000', wx: 'Tiszta'},'20220327010000': {time: '01:00', temp: '+11', winds: 7, windd: '315', windt: 'NV', flike: '+11', rain: 0.0, rainp: 2, rhum: 70, symb: 'n000', wx: 'Tiszta'},'20220327030000': {time: '03:00', temp: '+11', winds: 8, windd: '315', windt: 'NV', flike: '+11', rain: 0.0, rainp: 2, rhum: 70, symb: 'n100', wx: 'Többnyire tiszta'},'20220327040000': {time: '04:00', temp: '+9', winds: 8, windd: '315', windt: 'NV', flike: '+8', rain: 0.0, rainp: 2, rhum: 69, symb: 'n100', wx: 'Többnyire tiszta'},'20220327050000': {time: '05:00', temp: '+9', winds: 9, windd: '315', windt: 'NV', flike: '+8', rain: 0.0, rainp: 2, rhum: 69, symb: 'n100', wx: 'Többnyire tiszta'},'20220327060000': {time: '06:00', temp: '+7', winds: 8, windd: '315', windt: 'NV', flike: '+6', rain: 0.0, rainp: 2, rhum: 69, symb: 'n000', wx: 'Tiszta'},'20220327070000': {time: '07:00', temp: '+6', winds: 7, windd: '315', windt: 'NV', flike: '+5', rain: 0.0, rainp: 2, rhum: 71, symb: 'd000', wx: 'Tiszta'},'20220327080000': {time: '08:00', temp: '+9', winds: 6, windd: '315', windt: 'NV', flike: '+8', rain: 0.0, rainp: 2, rhum: 64, symb: 'd000', wx: 'Tiszta'},'20220327090000': {time: '09:00', temp: '+13', winds: 8, windd: '000', windt: 'N', flike: '+13', rain: 0.0, rainp: 2, rhum: 58, symb: 'd000', wx: 'Tiszta'},'20220327100000': {time: '10:00', temp: '+15', winds: 9, windd: '000', windt: 'N', flike: '+15', rain: 0.0, rainp: 2, rhum: 53, symb: 'd000', wx: 'Tiszta'},'20220327110000': {time: '11:00', temp: '+16', winds: 11, windd: '045', windt: 'NO', flike: '+16', rain: 0.0, rainp: 2, rhum: 51, symb: 'd000', wx: 'Tiszta'},'20220327120000': {time: '12:00', temp: '+17', winds: 10, windd: '045', windt: 'NO', flike: '+17', rain: 0.0, rainp: 2, rhum: 49, symb: 'd300', wx: 'Felhős'},'20220327130000': {time: '13:00', temp: '+18', winds: 9, windd: '045', windt: 'NO', flike: '+18', rain: 0.0, rainp: 2, rhum: 47, symb: 'd300', wx: 'Felhős'},'20220327140000': {time: '14:00', temp: '+19', winds: 8, windd: '045', windt: 'NO', flike: '+19', rain: 0.0, rainp: 2, rhum: 45, symb: 'd300', wx: 'Felhős'},'20220327150000': {time: '15:00', temp: '+20', winds: 9, windd: '045', windt: 'NO', flike: '+20', rain: 0.0, rainp: 2, rhum: 45, symb: 'd300', wx: 'Felhős'},'20220327160000': {time: '16:00', temp: '+20', winds: 9, windd: '045', windt: 'NO', flike: '+20', rain: 0.0, rainp: 2, rhum: 45, symb: 'd300', wx: 'Felhős'},'20220327170000': {time: '17:00', temp: '+20', winds: 9, windd: '090', windt: 'O', flike: '+20', rain: 0.0, rainp: 2, rhum: 46, symb: 'd300', wx: 'Felhős'},'20220327200000': {time: '20:00', temp: '+15', winds: 4, windd: '045', windt: 'NO', flike: '+15', rain: 0.0, rainp: 2, rhum: 63, symb: 'n000', wx: 'Tiszta'},'20220327230000': {time: '23:00', temp: '+12', winds: 10, windd: '090', windt: 'O', flike: '+12', rain: 0.0, rainp: 2, rhum: 50, symb: 'n000', wx: 'Tiszta'},'20220328020000': {time: '02:00', temp: '+11', winds: 8, windd: '135', windt: 'SO', flike: '+11', rain: 0.0, rainp: 2, rhum: 63, symb: 'n100', wx: 'Többnyire tiszta'},'20220328050000': {time: '05:00', temp: '+8', winds: 8, windd: '180', windt: 'S', flike: '+7', rain: 0.0, rainp: 2, rhum: 73, symb: 'n000', wx: 'Tiszta'},'20220328080000': {time: '08:00', temp: '+8', winds: 5, windd: '135', windt: 'SO', flike: '+8', rain: 0.0, rainp: 2, rhum: 66, symb: 'd000', wx: 'Tiszta'},'20220328110000': {time: '11:00', temp: '+16', winds: 9, windd: '180', windt: 'S', flike: '+16', rain: 0.0, rainp: 2, rhum: 52, symb: 'd000', wx: 'Tiszta'},'20220328140000': {time: '14:00', temp: '+20', winds: 11, windd: '225', windt: 'SV', flike: '+20', rain: 0.0, rainp: 2, rhum: 41, symb: 'd000', wx: 'Tiszta'},'20220328170000': {time: '17:00', temp: '+21', winds: 12, windd: '180', windt: 'S', flike: '+21', rain: 0.0, rainp: 2, rhum: 40, symb: 'd000', wx: 'Tiszta'},'20220328200000': {time: '20:00', temp: '+16', winds: 6, windd: '180', windt: 'S', flike: '+16', rain: 0.0, rainp: 2, rhum: 67, symb: 'n000', wx: 'Tiszta'},'20220328230000': {time: '23:00', temp: '+12', winds: 10, windd: '225', windt: 'SV', flike: '+12', rain: 0.0, rainp: 2, rhum: 77, symb: 'n000', wx: 'Tiszta'},'20220329020000': {time: '02:00', temp: '+11', winds: 11, windd: '270', windt: 'V', flike: '+11', rain: 0.0, rainp: 2, rhum: 75, symb: 'n000', wx: 'Tiszta'},'20220329050000': {time: '05:00', temp: '+9', winds: 12, windd: '270', windt: 'V', flike: '+7', rain: 0.0, rainp: 2, rhum: 74, symb: 'n300', wx: 'Felhős'},'20220329080000': {time: '08:00', temp: '+10', winds: 12, windd: '270', windt: 'V', flike: '+8', rain: 0.0, rainp: 2, rhum: 68, symb: 'd000', wx: 'Tiszta'},'20220329110000': {time: '11:00', temp: '+16', winds: 20, windd: '270', windt: 'V', flike: '+16', rain: 0.0, rainp: 2, rhum: 45, symb: 'd000', wx: 'Tiszta'},'20220329140000': {time: '14:00', temp: '+19', winds: 17, windd: '270', windt: 'V', flike: '+19', rain: 0.0, rainp: 2, rhum: 36, symb: 'd100', wx: 'Többnyire tiszta'},'20220329170000': {time: '17:00', temp: '+20', winds: 18, windd: '270', windt: 'V', flike: '+20', rain: 0.0, rainp: 4, rhum: 34, symb: 'd300', wx: 'Felhős'},'20220329200000': {time: '20:00', temp: '+15', winds: 5, windd: '270', windt: 'V', flike: '+15', rain: 0.0, rainp: 2, rhum: 48, symb: 'n300', wx: 'Felhős'},'20220329230000': {time: '23:00', temp: '+12', winds: 13, windd: '270', windt: 'V', flike: '+12', rain: 0.0, rainp: 2, rhum: 48, symb: 'n200', wx: 'Enyhén borús'},'20220330020000': {time: '02:00', temp: '+11', winds: 11, windd: '270', windt: 'V', flike: '+11', rain: 0.0, rainp: 2, rhum: 70, symb: 'n300', wx: 'Felhős'},'20220330050000': {time: '05:00', temp: '+9', winds: 12, windd: '225', windt: 'SV', flike: '+7', rain: 0.0, rainp: 2, rhum: 75, symb: 'n300', wx: 'Felhős'},'20220330080000': {time: '08:00', temp: '+10', winds: 8, windd: '225', windt: 'SV', flike: '+9', rain: 0.0, rainp: 6, rhum: 69, symb: 'd300', wx: 'Felhős'},'20220330110000': {time: '11:00', temp: '+16', winds: 16, windd: '225', windt: 'SV', flike: '+16', rain: 0.0, rainp: 7, rhum: 50, symb: 'd400', wx: 'Borús'},'20220330140000': {time: '14:00', temp: '+19', winds: 21, windd: '225', windt: 'SV', flike: '+19', rain: 0.0, rainp: 11, rhum: 41, symb: 'd300', wx: 'Felhős'},'20220330170000': {time: '17:00', temp: '+16', winds: 14, windd: '225', windt: 'SV', flike: '+16', rain: 0.0, rainp: 25, rhum: 44, symb: 'd400', wx: 'Borús'},'20220330200000': {time: '20:00', temp: '+14', winds: 3, windd: '315', windt: 'NV', flike: '+14', rain: 0.0, rainp: 40, rhum: 69, symb: 'n400', wx: 'Borús'},'20220330230000': {time: '23:00', temp: '+13', winds: 10, windd: '180', windt: 'S', flike: '+13', rain: 0.0, rainp: 41, rhum: 66, symb: 'n400', wx: 'Borús'},'20220331020000': {time: '02:00', temp: '+12', winds: 8, windd: '180', windt: 'S', flike: '+12', rain: 0.2, rainp: 54, rhum: 48, symb: 'n410', wx: 'Borús és gyenge eső'},'20220331050000': {time: '05:00', temp: '+9', winds: 8, windd: '180', windt: 'S', flike: '+8', rain: 0.3, rainp: 67, rhum: 65, symb: 'n410', wx: 'Borús és gyenge eső'},'20220331080000': {time: '08:00', temp: '+10', winds: 9, windd: '135', windt: 'SO', flike: '+9', rain: 0.3, rainp: 80, rhum: 82, symb: 'd410', wx: 'Borús és gyenge eső'},'20220331110000': {time: '11:00', temp: '+15', winds: 10, windd: '180', windt: 'S', flike: '+15', rain: 0.6, rainp: 80, rhum: 63, symb: 'd410', wx: 'Borús és gyenge eső'},'20220331140000': {time: '14:00', temp: '+16', winds: 12, windd: '180', windt: 'S', flike: '+16', rain: 0.6, rainp: 80, rhum: 44, symb: 'd410', wx: 'Borús és gyenge eső'},'20220331170000': {time: '17:00', temp: '+17', winds: 8, windd: '090', windt: 'O', flike: '+17', rain: 1.1, rainp: 82, rhum: 58, symb: 'd420', wx: 'Borús és záporok '},'20220331200000': {time: '20:00', temp: '+12', winds: 3, windd: '045', windt: 'NO', flike: '+12', rain: 1.1, rainp: 84, rhum: 72, symb: 'n420', wx: 'Borús és záporok '},'20220331230000': {time: '23:00', temp: '+11', winds: 5, windd: '090', windt: 'O', flike: '+11', rain: 0.1, rainp: 71, rhum: 71, symb: 'n410', wx: 'Borús és gyenge eső'},'20220401020000': {time: '02:00', temp: '+11', winds: 7, windd: '180', windt: 'S', flike: '+11', rain: 0.1, rainp: 57, rhum: 69, symb: 'n410', wx: 'Borús és gyenge eső'},'20220401050000': {time: '05:00', temp: '+9', winds: 9, windd: '225', windt: 'SV', flike: '+8', rain: 0.2, rainp: 56, rhum: 77, symb: 'n410', wx: 'Borús és gyenge eső'},'20220401080000': {time: '08:00', temp: '+11', winds: 12, windd: '225', windt: 'SV', flike: '+11', rain: 0.2, rainp: 55, rhum: 85, symb: 'd410', wx: 'Borús és gyenge eső'},'20220401110000': {time: '11:00', temp: '+17', winds: 15, windd: '315', windt: 'NV', flike: '+17', rain: 0.1, rainp: 61, rhum: 70, symb: 'd410', wx: 'Borús és gyenge eső'},'20220401140000': {time: '14:00', temp: '+17', winds: 17, windd: '225', windt: 'SV', flike: '+17', rain: 0.1, rainp: 68, rhum: 55, symb: 'd410', wx: 'Borús és gyenge eső'},'20220401170000': {time: '17:00', temp: '+19', winds: 14, windd: '315', windt: 'NV', flike: '+19', rain: 0.4, rainp: 71, rhum: 88, symb: 'd410', wx: 'Borús és gyenge eső'},'20220401200000': {time: '20:00', temp: '+14', winds: 11, windd: '225', windt: 'SV', flike: '+14', rain: 0.4, rainp: 74, rhum: 64, symb: 'n410', wx: 'Borús és gyenge eső'},'20220401230000': {time: '23:00', temp: '+12', winds: 9, windd: '270', windt: 'V', flike: '+12', rain: 0.2, rainp: 68, rhum: 79, symb: 'n410', wx: 'Borús és gyenge eső'},'20220402020000': {time: '02:00', temp: '+11', winds: 8, windd: '225', windt: 'SV', flike: '+11', rain: 0.2, rainp: 61, rhum: 53, symb: 'n410', wx: 'Borús és gyenge eső'},'20220402050000': {time: '05:00', temp: '+8', winds: 6, windd: '315', windt: 'NV', flike: '+7', rain: 0.5, rainp: 68, rhum: 87, symb: 'n410', wx: 'Borús és gyenge eső'},'20220402080000': {time: '08:00', temp: '+8', winds: 4, windd: '000', windt: 'N', flike: '+8', rain: 0.5, rainp: 74, rhum: 78, symb: 'd410', wx: 'Borús és gyenge eső'},'20220402110000': {time: '11:00', temp: '+12', winds: 7, windd: '315', windt: 'NV', flike: '+12', rain: 0.4, rainp: 75, rhum: 47, symb: 'd410', wx: 'Borús és gyenge eső'},'20220402140000': {time: '14:00', temp: '+10', winds: 10, windd: '000', windt: 'N', flike: '+9', rain: 0.4, rainp: 75, rhum: 69, symb: 'd410', wx: 'Borús és gyenge eső'},'20220402170000': {time: '17:00', temp: '+12', winds: 7, windd: '315', windt: 'NV', flike: '+12', rain: 0.2, rainp: 70, rhum: 86, symb: 'd410', wx: 'Borús és gyenge eső'},'20220402200000': {time: '20:00', temp: '+8', winds: 4, windd: '000', windt: 'N', flike: '+8', rain: 0.2, rainp: 66, rhum: 72, symb: 'n410', wx: 'Borús és gyenge eső'},'20220402230000': {time: '23:00', temp: '+6', winds: 5, windd: '315', windt: 'NV', flike: '+5', rain: 0.0, rainp: 51, rhum: 84, symb: 'n310', wx: 'Felhős és gyenge eső'},'20220403020000': {time: '02:00', temp: '+5', winds: 6, windd: '000', windt: 'N', flike: '+4', rain: 0.0, rainp: 36, rhum: 63, symb: 'n300', wx: 'Felhős'},'20220403050000': {time: '05:00', temp: '+2', winds: 11, windd: '315', windt: 'NV', flike: '-1', rain: 0.0, rainp: 31, rhum: 77, symb: 'n400', wx: 'Borús'},'20220403080000': {time: '08:00', temp: '+3', winds: 16, windd: '315', windt: 'NV', flike: '-1', rain: 0.0, rainp: 26, rhum: 66, symb: 'd400', wx: 'Borús'},'20220403110000': {time: '11:00', temp: '+8', winds: 22, windd: '315', windt: 'NV', flike: '+5', rain: 0.0, rainp: 32, rhum: 51, symb: 'd400', wx: 'Borús'},'20220403140000': {time: '14:00', temp: '+8', winds: 27, windd: '315', windt: 'NV', flike: '+4', rain: 0.0, rainp: 38, rhum: 36, symb: 'd400', wx: 'Borús'},'20220403170000': {time: '17:00', temp: '+10', winds: 17, windd: '315', windt: 'NV', flike: '+8', rain: 0.0, rainp: 33, rhum: 53, symb: 'd300', wx: 'Felhős'},'20220403200000': {time: '20:00', temp: '+6', winds: 6, windd: '315', windt: 'NV', flike: '+5', rain: 0.0, rainp: 27, rhum: 36, symb: 'n300', wx: 'Felhős'},'20220403230000': {time: '23:00', temp: '+5', winds: 8, windd: '315', windt: 'NV', flike: '+3', rain: 0.0, rainp: 18, rhum: 75, symb: 'n400', wx: 'Borús'},'20220404020000': {time: '02:00', temp: '+4', winds: 9, windd: '315', windt: 'NV', flike: '+2', rain: 0.0, rainp: 10, rhum: 43, symb: 'n400', wx: 'Borús'},'20220404050000': {time: '05:00', temp: '+3', winds: 10, windd: '270', windt: 'V', flike: '+0', rain: 0.0, rainp: 8, rhum: 80, symb: 'n400', wx: 'Borús'},'20220404080000': {time: '08:00', temp: '+5', winds: 10, windd: '270', windt: 'V', flike: '+3', rain: 0.0, rainp: 6, rhum: 54, symb: 'd400', wx: 'Borús'},'20220404110000': {time: '11:00', temp: '+17', winds: 13, windd: '270', windt: 'V', flike: '+17', rain: 0.0, rainp: 18, rhum: 55, symb: 'd400', wx: 'Borús'},'20220404140000': {time: '14:00', temp: '+24', winds: 15, windd: '270', windt: 'V', flike: '+24', rain: 0.0, rainp: 29, rhum: 43, symb: 'd400', wx: 'Borús'},'20220404170000': {time: '17:00', temp: '+22', winds: 12, windd: '225', windt: 'SV', flike: '+22', rain: 0.0, rainp: 25, rhum: 40, symb: 'd400', wx: 'Borús'},'20220404200000': {time: '20:00', temp: '+14', winds: 9, windd: '225', windt: 'SV', flike: '+14', rain: 0.0, rainp: 21, rhum: 39, symb: 'n400', wx: 'Borús'},'20220404230000': {time: '23:00', temp: '+14', winds: 9, windd: '225', windt: 'SV', flike: '+14', rain: 0.0, rainp: 23, rhum: 57, symb: 'n400', wx: 'Borús'},'20220405020000': {time: '02:00', temp: '+16', winds: 9, windd: '225', windt: 'SV', flike: '+16', rain: 0.0, rainp: 25, rhum: 63, symb: 'n400', wx: 'Borús'},'20220405050000': {time: '05:00', temp: '+11', winds: 10, windd: '225', windt: 'SV', flike: '+11', rain: 0.0, rainp: 24, rhum: 71, symb: 'n400', wx: 'Borús'},'20220405080000': {time: '08:00', temp: '+10', winds: 11, windd: '225', windt: 'SV', flike: '+8', rain: 0.0, rainp: 24, rhum: 53, symb: 'd400', wx: 'Borús'}}}";
     */
    public function forecast($param = null)
	{
		$url = 'https://www.foreca.hu/Hungary/Baranya/B%C3%B3ly/10-day-forecast';
		
		$data = [];
		$forecast10days = [];
		
		$page = file_get_contents($url);
		//if($param == 'forecastToDay'){
			$pos = (int) strpos($page, 'var daily_data =');
			$content = substr($page, $pos + 17);
			$pos = (int) strpos($content, '}}};</script>');
			$content = trim(substr($content, 0, $pos + 3));
			$content = $this->fixingBadJson($content);
		//}

		$json = json_decode($content);

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
		
		$this->loadModel("Forecasts");
		$this->Forecasts->deleteAll([true]);
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

/*
		$days10 = json_encode($forecast10days);
*/
/*
		foreach( $json->hour as $key =>$hour){
			//echo $key;
			$dt = substr($key,0,4) . "-" . substr($key,4,2) . "-" . substr($key,6,2) . " " . substr($key,8,2) . ":" . substr($key,10,2) . ":" . substr($key,12,2);
			echo date("Y.m.d. H:i:s", strtotime($dt));
			echo "<br>";
			//debug($key);
			debug($hour);
		}
		//debug($json);
		
*/

		//return $this->redirect('/admin/forecasts');

		die("10 napos előrejelzés: OK\n");
	}










    public function horoscopes()
	{
		$horoscopes = [];
		$data = [];
		$date = '0000.00.00.';
		$content = '';
			
		foreach($this->zodiacs as $zodiac => $name){
			
			//echo "<b>" . $name . "</b><br>";

			$url = 'https://www.ezoworld.hu/horoszkopok/napi-horoszkop/napi-' . $zodiac. '-horoszkop';
			
			$page = file_get_contents($url);

			// Dátum lekérése
			$pos = (int) strpos($page, '<h1 class="page-title">');
			$content = substr($page, $pos);

			$pos = (int) strpos($content, '<br class="visible-xs">');
			$content = substr($content, $pos);

			$pos = (int) strpos($content, '</h1>');
			$content = substr($content, 0, $pos);
			
			$date = trim(strip_tags($content));
			//echo $date;
			//echo "<br>";
			// /.Dátum lekérése
			
			// Tartalom lekérése
			$pos = (int) strpos($page, '<!-- left column -->');
			$page = substr($page, $pos);
			
			$pos = (int) strpos($page, '<div class="col-sm-7">');
			$page = substr($page, $pos);

			$pos = (int) strpos($page, '<p>');
			$page = substr($page, $pos);

			$pos = (int) strpos($page, '<!-- horoscope text -->');
			$content = substr($page, 0, $pos);

			$content = trim(strip_tags($content));
			// /.Tartalom lekérése

			//echo $content;
			//echo "<hr>";
		
			$horoscopes[$zodiac] = [
				'name' 		=> $name,
				'key' 		=> $zodiac,
				'date' 		=> $date,
				'content' 	=> $content
			];
		}

		//debug($horoscopes); die();
		
		$this->loadModel("NewHoroscopes");

		foreach($this->zodiacs as $key => $content){
			$data['name'] 	 = $horoscopes[$key]['name'];
			$data['year'] 	 = (int) date("Y");
			$data['month'] 	 = (int) date("m");
			$data['day'] 	 = (int) date("d");
			$data['ckey'] 	 = $horoscopes[$key]['key'];	// ckey, mert az sql nem engedi...
			$data['date'] 	 = $horoscopes[$key]['date'];
			$data['content'] = $horoscopes[$key]['content'];
			$data['counter'] = 0;
			$horoscope 	= $this->NewHoroscopes->newEmptyEntity();
			$horoscope 	= $this->NewHoroscopes->patchEntity($horoscope, $data);
			//debug($horoscope->getErrors());
			$this->NewHoroscopes->save($horoscope);
		}

		if($this->request->referer() !== null){
		//	return $this->redirect('/admin/NewHoroscopes');
		}

		die("Új napi horoszkóp: OK\n");

	}

















	// EXCEL:
	// https://packagist.org/packages/shuchkin/simplexlsx	
	public function powerbreaks()
	{
		$url = 'https://fbapps.cloudwave.hu/eon/eonuzemzavar/page/xls';
		$rows = [];
		$data = [];
		
		$this->loadModel('Powerbreaks');

		$filename = WWW_ROOT . 'files' . DS . 'powerbreaks.xlsx';
		if(file_exists($filename)){
			unlink($filename);
		}

		if(!file_put_contents($filename, file_get_contents($url))){
			die("Sikertelen áramszünet XLSX elhozatal!\n");
		}

		
		if ( $xlsx = SimpleXLSX::parse($filename) ) {
			$rows = $xlsx->rows();
		} else {
			echo SimpleXLSX::parseError();
			die("Hibás áramszünet XLSX file!\n");
		}

		$this->Powerbreaks->deleteAll([true]);

		$i = 0;
		foreach($rows as $row){
			$i++;			
			if (in_array($row[1], $this->cities)) {
				$data['status']			= (string) $row[0];
				$data['name'] 			= (string) $row[1];
				$data['street'] 		= (string) $row[2];
				$data['place'] 			= (string) $row[3];
				$data['house_from']		= (string) $row[4];
				$data['house_to'] 		= (string) $row[5];
				$data['date'] 			= (string) str_replace("-",".", $row[6]) . ".";
				$data['time_from'] 		= (string) substr($row[7], 0, 5);
				$data['time_to'] 		= (string) substr($row[8], 0, 5);
				$data['comment'] 		= (string) $row[9];
				$data['comment2'] 		= (string) $row[10];
				$powerBreak 	= $this->Powerbreaks->newEmptyEntity();
				$powerBreak 	= $this->Powerbreaks->patchEntity($powerBreak, $data);
				$this->Powerbreaks->save($powerBreak);
			}
		}

		if($this->request->referer() !== null){
		//	return $this->redirect('/admin/powerbreaks');
			//return $this->redirect($this->request->referer());
		}

		die("Áramszünet: OK\n");
		
	}













	public function currencies(){
		########################################################################################################################
		############################                                               #############################################
		############################ VALUTA ÁRFOLYAMOK - http://net.cib.hu         #############################################
		############################                                               #############################################
		########################################################################################################################
		$toDelete = false;
		$this->loadModel('CibCurrencies');
		$this->loadModel('CurrencyLogs');
		
		// Sajnos nem működik. Régi adatokat közöl néha ... -> kiszűrni
		
		$url = 'https://net.cib.hu/%5Erss/valarf.xml';
		$xmlString = file_get_contents($url);
		$xmlArray = Xml::toArray(Xml::build($xmlString));
		
		//debug($xmlArray['rss']['channel']['item']); die();
		
		$items = $xmlArray['rss']['channel']['item'];
		
		//debug($items); die();
		
		// Van-e új tétel, az az aznapi legalább.
		foreach($items as $item){
			if( strtotime(date("Y-m-d", strtotime($item['pubDate']))) >= strtotime(date("Y-m-d")) ){	// 
				$toDelete = true;
				break;
			}
		}
		
		if($toDelete){
			//echo "TO DELETED...";
			$this->CibCurrencies->deleteAll([true]);
			//echo "DELETED!";
		
			foreach($items as $item){
				
				//debug($item); 
				//die();
				
				if(substr($item['title'], 0, 3) == 'EUR' || substr($item['title'], 0, 3) == 'USD' || substr($item['title'], 0, 3) == 'GBP' || substr($item['title'], 0, 3) == 'HRK'){
					$data['name'] 		= $item['title'];
					$data['category'] 	= $item['category'];
					$data['description']= $item['description'];
					$data['category'] 	= $item['category'];
					$data['pubDate'] 	= new FrozenTime($item['pubDate']);
					$data['guid'] 		= $item['guid'];
					$cibCurrency 	= $this->CibCurrencies->newEmptyEntity();
					$cibCurrency 	= $this->CibCurrencies->patchEntity($cibCurrency, $data);
					//debug($cibCurrency->getErrors());
					$this->CibCurrencies->save($cibCurrency);
					
					$currencyLog 	= $this->CibCurrencies->newEmptyEntity();
					$currencyLog 	= $this->CibCurrencies->patchEntity($currencyLog, $data);
					$this->CurrencyLogs->save($currencyLog);
					
				}
			}
		}

		########################################################################################################################
		########################################################################################################################
		########################################################################################################################
		########################################################################################################################
		########################################################################################################################

		if($this->request->referer() !== null){
		//	return $this->redirect('/admin/CibCurrencies');
			//return $this->redirect($this->request->referer());
		}
		
		die("CIB valutainformációk: OK\n");
		
	}






	
    public function lotteries(){
		$lotteries = [
			'otos' 			=> 'Ötös lottó',
			'hatos' 		=> 'Hatos lottó',
			'skandi' 		=> 'Skandináv lottó',
			'eurojackpot' 	=> 'Euro jackpot',
			'joker' 		=> 'Joker',
			'luxor' 		=> 'Luxor',
		];

		$this->loadModel('Lotteries');
		$this->Lotteries->deleteAll([true]);
		foreach($lotteries as $key => $name){
			$data = [];
			$url = "https://bet.szerencsejatek.hu/cmsfiles/" . $key . ".csv";
			$file = fopen($url, "r");
			if($file){
				$rec = fgetcsv($file, 10000, ";");
				//debug($rec);
				$lottery = $this->Lotteries->find('all', ['conditions' => ['name' => $key]])->first();

				if(!$lottery || $lottery->content != json_encode($rec)){
					if($lottery){
						$this->Lotteries->delete($lottery);
					}
					$data['name'] 	 = $key;
					$data['content'] = json_encode($rec);
					$lottery = $this->Lotteries->newEmptyEntity();
					$lottery = $this->Lotteries->patchEntity($lottery, $data);
					//debug($lottery->getErrors());
					debug($this->Lotteries->save($lottery));
				}
				//}
			}
		}

		if($this->request->referer() !== null){
		//	return $this->redirect('/admin/lotteries');
			//return $this->redirect($this->request->referer());
		}
		
		die("Lottószámok: OK\n");
		
	}
	



/*
  <16
     Url="http://rss.loc/rss/weathers.rss"
     Name="Idojaras_szoveges">
  </16>
*/


	// http://rss.loc/Collectrss/weathers/data

	// Params:
	// http://rss.loc/Collectrss/weathers/data
	// http://rss.loc/Collectrss/weathers/mapCurrentDegree
	// http://rss.loc/Collectrss/weathers/mapSatelit

    public function weathers($param = null)
    {
		if($param == 'data'){
			$data = [];
			$this->loadModel('HirstartWeathers');
			$url = 'https://www.hirstart.hu/site/publicrss.php?pos=balrovat&pid=378';
			$xmlString = file_get_contents($url);
			$xmlArray = Xml::toArray(Xml::build($xmlString));
			$items = $xmlArray['rss']['channel']['item'];
			
			//debug($items);
			//die();
			
			$this->HirstartWeathers->deleteAll([true]);
			
			foreach($items as $item){
				if($item['category'] == 'Időjárás'){
					$data['author'] 		= $item['author'];
					$data['title'] 			= $item['title'];
					$data['description'] 	= $item['description'];
					$data['category'] 		= $item['category'];
					$data['pubdate'] 		= new FrozenTime($item['pubDate']);
					//$data['imageUrl'] 		= $item['enclosure']['@url'];
					//$data['imageType'] 		= $item['enclosure']['@type'];
					$data['imageUrl'] 		= ''; //$item['enclosure']['@url'];
					$data['imageType'] 		= ''; //$item['enclosure']['@type'];
					$data['guid'] 			= $item['guid'];
					$hirstartWeather 		= $this->HirstartWeathers->newEmptyEntity();
					$hirstartWeather 		= $this->HirstartWeathers->patchEntity($hirstartWeather, $data);
					if($this->HirstartWeathers->save($hirstartWeather)){
					//	if(!file_exists(WWW_ROOT . 'images')){
					//		mkdir(WWW_ROOT . 'images');
					//	}
					//	if(!file_exists(WWW_ROOT . 'images' . DS . 'weathers')){
					//		mkdir(WWW_ROOT . 'images' . DS . 'weathers');
					//	}
					//	file_put_contents(WWW_ROOT . 'images' . DS . 'weathers' . DS . $hirstartWeather->guid . '.jpg', file_get_contents($hirstartWeather->imageUrl));
					
					}
				}
			}
		}



		/*
			CRON: 10 percenként hívni, hogy mindenképp belekerüljön az egy percbe.
			
			Aktuális hőmérséklet térkép elhozatala
		*/
		if($param == 'mapCurrentDegree'){

			if(!file_exists(WWW_ROOT . 'images')){
				mkdir(WWW_ROOT . 'images');
			}
			if(!file_exists(WWW_ROOT . 'images' . DS . 'weathers')){
				mkdir(WWW_ROOT . 'images' . DS . 'weathers');
			}

			$filename = WWW_ROOT . 'images' . DS . 'weathers' . DS . 'mapCurrentDegree.jpg';
			if(file_exists($filename)){
				unlink($filename);
			}
			file_put_contents($filename, file_get_contents("https://www.metnet.hu/pic/climate_maps.php?mt=act&map=temp&size=l"));
		}


		/*
			CRON: 30 másodpercenként hívni, hogy mindenképp belekerüljön az egy percbe.
		
			Minden óra 10, 25, 40 és 55. percében hozható csak el a kép.
		*/
		// https://www.metnet.hu/img/satellite/2022/03/10/hrvrgb_20220310_0655.jpg	- 10,25,40,55 percekben van ez 
		if($param == 'mapSatelit'){

			if(!file_exists(WWW_ROOT . 'images')){
				mkdir(WWW_ROOT . 'images');
			}
			if(!file_exists(WWW_ROOT . 'images' . DS . 'weathers')){
				mkdir(WWW_ROOT . 'images' . DS . 'weathers');
			}

			$filename = "";
			$now = FrozenTime::now();
			$now = $now->modify('-170 minutes');
			$time = getDate(strtotime($now->i18nFormat('yyyy-MM-dd HH:mm:ss')));
			//debug($time);
			//if($time['minutes'] == 10 || $time['minutes'] == 25 || $time['minutes'] == 40 || $time['minutes'] == 55){
				$filename = WWW_ROOT . 'images' . DS . 'weathers' . DS . 'mapSatelit.jpg';
				if(file_exists($filename . '.tmp')){
					unlink($filename . '.tmp');
				}
				if(file_exists($filename . '.test')){
					unlink($filename . '.test');
				}
				$hours	 = substr('0' . $time['hours'], -2);
				if($time['minutes'] < 10){
					$minutes = '55';
				}			
				if($time['minutes'] >= 10){
					$minutes = '10';
				}			
				if($time['minutes'] >= 25){
					$minutes = '25';
				}
				if($time['minutes'] >= 40){
					$minutes = '40';
				}
				
				if($time['minutes'] >= 55){
					$minutes = '55';
				}
				$url = "https://www.metnet.hu/img/satellite/" . $time['year'] . "/" . substr('0' . $time['mon'], -2) . "/" . substr('0' . $time['mday'], -2) . "/hrvrgb_" . $time['year'] . substr('0' . $time['mon'], -2) . substr('0' . $time['mday'], -2) . "_" . substr('0' . $hours, -2) . substr('0' . $minutes, -2) . ".jpg"; 
				// https://www.metnet.hu/img/satellite/2022/03/29/hrvrgb_20220329_1110.jpg
				
				// https://www.metnet.hu/img/satellite/2022/03/29/hrvrgb_20220329_1225.jpg
				
				//echo $url;
				//echo "<br>";
				
	/*
				if( file_put_contents($filename . '.test', file_get_contents($url)) ){
					echo "Van";
				}else{
					echo "Nincs";
				}
	*/
				if( file_put_contents($filename . '.tmp', file_get_contents($url)) ){
					if(file_exists($filename)){
						unlink($filename);
					}
					rename($filename . '.tmp', $filename);
				}
			//}
		}

		//die('xxx');

		if($param == 'data' && $this->request->referer() !== null){
		//	return $this->redirect('/admin/hirstart-weathers');
			//return $this->redirect($this->request->referer());
		}
		
		if($param == 'mapCurrentDegree'){
		//	return $this->redirect('http://192.168.254.215:8002/images/weathers/mapCurrentDegree.jpg');			
		}
		
		if($param == 'mapSatelit'){
		//	return $this->redirect('http://192.168.254.215:8002/images/weathers/mapSatelit.jpg');			
		}

		die("Időjárás: OK. Param: " . $param . "\n");
		
	}


	/*
		időjárás 10 napos előrejelzésnél az oldalban hibás JSON van megadva. Ezt javítja ki, hogy lehessen kezelni.
	*/
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
	
}
