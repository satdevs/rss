<?php
// Baked at 2022.03.09. 15:12:24
/*
	RSS Hívások:
	######################################################################################
	Időjárás, szövegesen:
	http://192.168.254.215:8002/rss/weathers.rss
	
	Lottószámok:
	http://192.168.254.215:8002/rss/lottery/otos.rss	// 5-ös lottószámok
	http://192.168.254.215:8002/rss/lottery/hatos.rss	// 6-os lottószámok
	http://192.168.254.215:8002/rss/lottery/skandi.rss	// 7-es lottószámok
	http://192.168.254.215:8002/rss/lottery/joker.rss	// Joker

	CIB Valuta árfolyamok:
	http://192.168.254.215:8002/rss/currency/eurv.rss , ahol eurv, mint EURO vételi árfolyam

		Egyéb paraméterek:
	
			'eurv' => 'EUR - valuta vételi árfolyam',
			'eurk' => 'EUR - valuta középárfolyam',
			'eure' => 'EUR - valuta eladási árfolyam',
			.....
	
	######################################################################################
	Belvárdi heti menü:
	http://192.168.254.215:8002/rss/restaurant/title.rss
	http://192.168.254.215:8002/rss/restaurant/days.rss
	http://192.168.254.215:8002/rss/restaurant/menu.rss
	http://192.168.254.215:8002/rss/restaurant/prices.rss
	######################################################################################
	Érdekességek óránként:
	http://192.168.254.215:8002/rss/curiosities.rss

	Viccek óránként:
	http://192.168.254.215:8002/rss/jokes.rss
	
	Ezen a napon történt óránként:
	http://192.168.254.215:8002/rss/today.rss
	
	Mai névnapok:
	http://192.168.254.215:8002/rss/namedays.rss			// Egyben az egész
	http://192.168.254.215:8002/rss/namedays/names.rss		// Csak a nevek
	http://192.168.254.215:8002/rss/namedays/meanings.rss	// Csak a jelentések
	######################################################################################
	Áramszünetek:
	http://192.168.254.215:8002/rss/powerbreaks.rss
	######################################################################################
	Napi horoszkópok:
	http://192.168.254.215:8002/rss/horoscopes/page1.rss
	http://192.168.254.215:8002/rss/horoscopes/page2.rss
	http://192.168.254.215:8002/rss/horoscopes/page3.rss
	######################################################################################
	Időjárás előrejelzés 10 napos. 10 nap egyesével kérdezhető le.
	// http://192.168.254.215:8002/rss/forecast/forecatDay1.rss   // forecatDay1-forecatDay10
	######################################################################################
	

*/
declare(strict_types=1);								// DOC LINKS AT BOTTOM THIS FILE!

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use Cake\Utility\Xml;
use Cake\I18n\FrozenTime;
use Cake\Database\Expression\QueryExpression;
use Cake\I18n\Number;
use Cake\View\Helper\NumberHelper;

/**
 * HirstartWeathers Controller
 *
 * @property \App\Model\Table\HirstartWeathersTable $HirstartWeathers
 * @method \App\Model\Entity\HirstartWeather[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */

class RssController extends AppController
{

    /**
     * Initialize controller
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
	}


/*
	Az információ begyűjtése CRON-nal megy minden óra 10. percében:
							http://192.168.254.215:8002/Collectrss/newForecast
							

	0. napi összefoglaló: 	http://192.168.254.215:8002/rss/newForecast/forecast/day-0.rss
	+1 napi összefoglaló: 	http://192.168.254.215:8002/rss/newForecast/forecast/day-1.rss
	+2 napi összefoglaló: 	http://192.168.254.215:8002/rss/newForecast/forecast/day-2.rss
	+3 napi összefoglaló: 	http://192.168.254.215:8002/rss/newForecast/forecast/day-3.rss
	+4 napi összefoglaló: 	http://192.168.254.215:8002/rss/newForecast/forecast/day-4.rss
	
	Napkelte, napnyugta és nap hossz: 
							http://192.168.254.215:8002/rss/newForecast/hourly/sun.rss
	Aktuális hőfok:			http://192.168.254.215:8002/rss/newForecast/hourly/degree.rss
	Szél, eső, pára:		http://192.168.254.215:8002/rss/newForecast/hourly/wind-rain-rhum.rss
	Időjárás szöveggel:		http://192.168.254.215:8002/rss/newForecast/hourly/currentWeather.rss
	Óránkénti:				
							http://192.168.254.215:8002/rss/newForecast/hourly/hourly-all.rss
							http://192.168.254.215:8002/rss/newForecast/hourly/hour-0.rss
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
			$content = $f->month_short . " " . (int) $f->d . ".\n" . $f->day_name. "\n\n\n\n\n" . $f->max . "\n\n" . $f->min . "\n\n\n\n";
			$content .= $f->wind_speed . "\n\n" . $f->rain . "\n\n" . $f->weather;
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

			// Órák feltöltése
			$hours = [];
			for($i=0;$i<=23;$i++){
				$hours[] = $i . ':00';
			}
			
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
				case "currentWeather": 
					$content = $f->weather;
					break;
				case "hourly-all":
					$content = '';
					//debug($hourly); die();					
					for($i=0;$i<10;$i++){
						$f = $hourly[$i];
						if(in_array($f->hour, $hours)){
							$content .= sprintf("%5s",  $f->hour) . "           " . sprintf("%6s",  $f->degree) . "   " . sprintf("%6s",  $f->feel) . "        " . sprintf("%8s",  $f->wind_kmh) . "   " . sprintf("%4s",  $f->rhum) . "   " . sprintf("%8s",  $f->rain_mm) . "   " . $f->weather . "\n\n";
							//$content .= $f->hour . "           " . $f->degree . "   " . $f->feel . "        " . $f->wind_kmh . "   " . $f->rhum . "   " . $f->rain_mm . "   " . $f->weather . "\n\n";
						}
					}
					//echo $content; die();
					break;
				case substr($param, 0,4) == "hour": 
					$hour = (int) substr($param, -1);
					$f = $hourly[$hour];
					$content = $f->hour . "           " . $f->degree . "   " . $f->feel . "        " . $f->wind_kmh . "   " . $f->rhum . "   " . $f->rain_mm . "   " . $f->weather;
					break;
			}

			// IKON-ok másolása az órának megfelelően
			foreach($hourly as $k => $f){
			
				// =============================== időjárás kép ===============================			
				$src = WWW_ROOT . "images" . DS . "weathers" . DS . "forecasts" . DS . "empty.png";
				if(in_array($f->hour, $hours)){
					$src = WWW_ROOT . "images" . DS . "weathers" . DS . "forecasts" . DS . $f->symbol . ".png";
				}
				$dest = WWW_ROOT . "images" . DS . "weathers" . DS . "hour-" . $k . ".png";
				if(file_exists($dest)){
					unlink($dest);
				}
				if(file_exists($src)){
					copy($src, $dest);
				}

				// =============================== szélirány kép ===============================
				$src = WWW_ROOT . "images" . DS . "weathers" . DS . "forecasts" . DS . "empty.png";
				if(in_array($f->hour, $hours)){
					$src = WWW_ROOT . "images" . DS . "weathers" . DS . "forecasts" . DS . $f->wind_symbol . ".png";
				}
				$dest = WWW_ROOT . "images" . DS . "weathers" . DS . "hour-wind-" . $k . ".png";
				if(file_exists($dest)){
					unlink($dest);
				}
				if(file_exists($src)){
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




















	// FORRÁS: https://www.foreca.hu/Hungary/Baranya/B%C3%B3ly/10-day-forecast

	// http://192.168.254.215:8002/rss/forecast/forecatDay1.rss   // forecatDay1-forecatDay10
	
    public function forecast($param = null)
    {
		
		$aMonth = ['Január', 'Február', 'Március', 'Április', 'Május', 'Június', 'Július', 'Aug.', 'Szept.', 'Október', 'November', 'December'];
		
		$contents = '';
		$page = 1;

		if($param !== null){
			$page = (int) $param;
		}
		$page = (int) substr($param, 10);

		$atomLink = ['controller' => 'Rss', 'action' => 'forecast', '_ext' => 'rss']; // Example controller and action
		
		$this->loadModel('Forecasts');

		$f = $this->Forecasts->find('all', [
			'conditions' => [
				'date >= CURDATE()'
			],
			'order' => [
				'date' => 'asc'
			],
			'limit' => 1,
			'page' => $page
		])->first();
		
		//FrozenTime::setToStringFormat("yyyy-MM-dd H:mm");
		
		FrozenTime::setToStringFormat("M");
		$m = (int) (string) new FrozenTime($f->date);
		
		FrozenTime::setToStringFormat("d");
		$d = (int) (string) new FrozenTime($f->date);
		
		$content = $aMonth[$m - 1] . " " . $d . ".\n" . $f->d . "\n\n" . $f->tmax . "°C\n\n" . $f->tmin . "°C\n\n";
		
		$content .= $f->sr . "\n\n" . $f->ss . "\n\n" ;
		$content .= $f->wx;
		
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










    /**
		* http://192.168.254.215:8002/tests/horoscopes/page1.rss
		* http://192.168.254.215:8002/rss/horoscopes/page1.rss
	 
		Napi horoszkópok:
		http://192.168.254.215:8002/rss/horoscopes/page1.rss
		http://192.168.254.215:8002/rss/horoscopes/page2.rss
		http://192.168.254.215:8002/rss/horoscopes/page3.rss
    */
    public function horoscopes($param = null)
    {
		$horoscope = [];
		$contents = '';
		$date = '';
		$page = 1;
		
		if($param == 'page1'){ $page = 1; }
		if($param == 'page2'){ $page = 2; }
		if($param == 'page3'){ $page = 3; }

		$atomLink = ['controller' => 'Rss', 'action' => 'horoscopes', '_ext' => 'rss'];
		
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
		
		$ids = [];
		foreach($horoscopes as $h){
			$ids[] = $h->id;
		}
		
		$expression = new QueryExpression('counter = counter + 1');
		$this->NewHoroscopes->updateAll([$expression], [
			'id IN' => $ids
			//'year' => date("Y"),
			//'month' => date("m"),
			//'day' => date("d"),
		]);
		
		if($horoscopes !== null){
			foreach($horoscopes as $h){
				$date = $h->date;
				$contents .= $h->name . ":\n" . $h->content . "\n\n";
			}
			$contents = trim($date . "\n\n" . $contents);
		}else{
			$contents = "\n\nAz oldal szerkesztés alatt áll!\n\nA napi horoszkópot hamarosan megjelentetjük!\n\nKöszönjük türelmét!";
		}
		

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





	 // // ** http://192.168.254.215:8002/tests/powerbreaks.rss
	 // http://192.168.254.215:8002/rss/powerbreaks.rss

    public function powerbreaks($param = null)
    {
		$powerbreak = [];
		$contents = '';

		$atomLink = ['controller' => 'Rss', 'action' => 'powerbreaks', '_ext' => 'rss']; // Example controller and action
		
		$this->loadModel('Powerbreaks');

		$powerbreaks = $this->Powerbreaks->find('all', [
			'conditions' => [
				//'date >= ' => date('Y-m-d'),
				'date >= CURDATE()', // => FrozenTime::now(),
			],
			'order' => [
				'date' => 'asc',
			],
			'group' => ['name', 'date', 'time_from', 'time_to'],
			'limit' => 8
		]);
		
		foreach($powerbreaks as $pb){
			//$contents .= "• " . $pb->name . ' ' . $pb->date . ' ' . $pb->time_from . ' ' . $pb->time_to . "\n";
			//$contents .= $pb->date . '   ' . $pb->name . "\n";
			$contents .= $pb->date . '  ' . $pb->time_from . '-' . $pb->time_to . ':   ' . $pb->name . "\n";
		}


		$powerbreak['channel']['title'] = "Áramszünetek";
		$powerbreak['channel']['description'] = $contents;
		
		$rss = [
			'channel' => [
				'title' => $powerbreak['channel']['title'],
				'link' => 'http://192.168.254.215:8002/rss/powerbreaks.rss',
				'description' => $powerbreak['channel']['description'],
			],
			'items' => $powerbreak
		];

		$this->set(['rss' => $rss, '_serialize' => 'rss']);

    }









	// http://rss.loc/rss/curiosities.rss
    public function curiosities()
    {
		
		$curiosity = [];
		$atomLink = ['controller' => 'Rss', 'action' => 'curiosities', '_ext' => 'rss']; // Example controller and action		
		$this->loadModel('Curiosities');
		$curiosities = $this->Curiosities->find('all', ['conditions' => ['current' => 1]])->first(); //->toArray();
		
		FrozenTime::setToStringFormat("yyyy-MM-dd H:mm");
		$modified = (string) new FrozenTime($curiosities->modified);
		
		// Ha egy órája lett lekérve az utolsó egy rekord, akkor váltson újra
		//echo  strtotime("-1 hour") . " - " . strtotime($modified) . " = " . ( strtotime($modified) - strtotime("-1 hour") ) . "<hr>";
		//echo (strtotime("now") - strtotime("-1 hour"));		
		//echo strtotime("now") - strtotime($modified);		
		//if(strtotime($modified) < strtotime("-1 hour")){
			
		if((strtotime("now") - strtotime($modified)) > 3600){
			$next = $this->Curiosities->find('all', ['conditions' => ['id >' => $curiosities->id]])->first();
			$this->Curiosities->updateAll(['current' => 0], ['current' => 1]);
			if(isset($next->id)){
				$this->Curiosities->updateAll(['current' => 1], ['id' => $next->id]);
			}else{
				$this->Curiosities->updateAll(['current' => 1], ['id' => 1]);
			}
			
			$expression = new QueryExpression('counter = counter + 1');
			$this->Curiosities->updateAll([$expression], ['current' => 1]);
			
			$this->Curiosities->updateAll(['modified = NOW()'], ['current' => 1]);
			$curiosities = $this->Curiosities->find('all', ['conditions' => ['current' => 1]])->first(); //->toArray();
		}

		$curiosity['channel']['title'] = 'Érdekességek minden órában';
		$curiosity['channel']['description'] = '';
		
		//FrozenTime::setToStringFormat("yyyy.MM.dd. KK:mm");		
		FrozenTime::setToStringFormat("yyyy.MM.dd. H:mm");		
		
		//debug($jokes->toArray()); die();
		
		//foreach($jokes as $w){			
			$curiosity['channel']['description'] .=  $curiosities->body . "\n\n";
		//}
		
		//debug( $joke['channel'] ); die();
		
		$rss = [
			'channel' => [
				'title' => 'Érdekességek minden órában',
				'link' => 'http://192.168.1.4/rss/curiosities.rss',
				'description' => $curiosity['channel']['description'],
				//'description' => 'Időjárás adatok a helyi szerverről',
				//'atom:link' => ['@href' => $atomLink],
			],
			'items' => $curiosity
		];
		
		//debug($rss); die();

		$this->set(['rss' => $rss, '_serialize' => 'rss']);

    }

















	// http://rss.loc/rss/today.rss
    public function today()
    {
		$limit = 8;
		
		$page = 0;	// Start page
		
		FrozenTime::setToStringFormat("H");		
		$hour = (int) (string) FrozenTime::now();

		$items = [];
		$content = '';

		$atomLink = ['controller' => 'Rss', 'action' => 'today', '_ext' => 'rss'];
		
		$this->loadModel('Todays');

		$count = $this->Todays->find('all', [
			'conditions' => [
				'month' => date("m"),
				'day' 	=> date("d"),
			],
			'group' => 'title'
		])->count();

		$pages = floor($count / $limit);
		$pages = (int) $pages;

		for($i = 1; $i <= $hour; $i++){
			if($page >= $pages){
				$page = 1;
			}else{
				$page++;
			}
		}

		$todays = $this->Todays->find('all', [
			'conditions' => [
				'month' => date("m"),
				'day' 	=> date("d"),
			],
			'order' => [
				'year' => 'asc',
				'title' => 'asc'
			],
			'limit' => $limit,
			'page' => $page,
			'group' => 'title'
		]);

		$ids = [];
		foreach($todays as $t){
			$ids[] = $t->id;
		}
		
		$expression = new QueryExpression('counter = counter + 1');
		$this->Todays->updateAll([$expression], [
			'id IN' => $ids
			//'month' => date("m"),
			//'day' 	=> date("d"),
		]);


		$content = '';
		foreach($todays as $today){
			$content .= $today->year . ": " . $today->title . "\n\n";
		}

		$content .= "[" . $page . "/" . $pages . ". oldal]";

		$items['channel']['title'] = "Mai napont történt";
		$items['channel']['description'] = $content;
		
		$rss = [
			'channel' => [
				'title' => "Mai napont történt",
				'link' => 'http://192.168.1.4/rss/today.rss',
				'description' => $items['channel']['description'],
				//'description' => 'Időjárás adatok a helyi szerverről',
				//'atom:link' => ['@href' => $atomLink],
			],
			'items' => $items
		];

		$this->set(['rss' => $rss, '_serialize' => 'rss']);

    }







	
    /**
	 * @param string|null $param: days, prices, menu
     * @return \Cake\Http\Response|null|void Renders view
	 * http://rss.loc/rss/namedays.rss			// Egyben az egész
	 * http://rss.loc/rss/namedays/names.rss	// Csak a nevek
	 * http://rss.loc/rss/namedays/meanings.rss	// Csak a jelentések
     */
    public function namedays($param = null)
    {
		$items = [];
		$content = '';

		$atomLink = ['controller' => 'Rss', 'action' => 'namedays', '_ext' => 'rss'];
		
		$this->loadModel('Namedays');

		$namedays = $this->Namedays->find('all', [
			'conditions' => [
				'month' => date("m"),
				'day' 	=> date("d"),
			],
			'order' => [
				'name' => 'asc'
			]
		]);

		if($param == null || $param == "names"){
			$count = 0;
			foreach($namedays as $nameday){
				$count++;
				$content .= $nameday->name . ", ";
			}
			$content = substr($content, 0, -2);
			
			if($count > 1){
				$content = "Mai névnapok: " . $content;
			}else{
				$content = "Mai névnap: " . $content;
			}

			$content .= "\n\n";
		}

		if($param == null || $param == "meanings"){
			foreach($namedays as $nameday){
				$content .= $nameday->name . ":\n";
				$content .= "Jelentése: " . $nameday->source . "\n";
				$content .= $nameday->meaning . "\n";
				$content .= $nameday->name . " napok: " . substr(strip_tags($nameday->days), 0, -2) . "\n\n";
			}
		}


		$items['channel']['title'] = "Mai névnap(ok)";
		$items['channel']['description'] = $content;
		
		$rss = [
			'channel' => [
				'title' => "Mai névnap(ok)",
				'link' => 'http://192.168.1.4/rss/namedays.rss',
				'description' => $items['channel']['description'],
				//'description' => 'Időjárás adatok a helyi szerverről',
				//'atom:link' => ['@href' => $atomLink],
			],
			'items' => $items
		];

		$this->set(['rss' => $rss, '_serialize' => 'rss']);

    }









	
    /**
     * Weathers method
     *
	 * @param string|null $param: if($param !== null && $param == 'clear-filter')...
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function lottery($param = 'error')
    {
		$items = [];
		
		$var = [
			'error' 		=> 'Missing key!',
			'otos' 			=> 'Ötös lottó',
			'hatos' 		=> 'Hatos lottó',
			'skandi' 		=> 'Skandináv lottó',
			'eurojackpot' 	=> 'Euro Jackpot',
			'joker' 		=> 'Joker',
			'luxor' 		=> 'Luxor'
		];

		$atomLink = ['controller' => 'Rss', 'action' => 'lottery', '_ext' => 'rss']; // Example controller and action
		
		$this->loadModel('Lotteries');

		$lottery = $this->Lotteries->find('all', ['conditions' => ['name' => $param]])->first();
		
		if($lottery){
			$item = json_decode($lottery->content);
			
			//debug($item);
			//echo "<pre>";

			switch($param){
				case "otos":
					$content  = "Húzás dátuma: " . $item[2] . "\n\n";
					$content .= "Az ötös lottó nyerőszámai: " . $item[11] . ", " . $item[12] . ", " . $item[13] . ", " . $item[14] . ", " . $item[15] . "\n\n";

					$content .= "Találatok:" . "\n";
					$content .= "==========" . "\n";
					$content .= "5 találat: " . sprintf("[%8s]", $item[3]) . " db. " . sprintf("[%16s]",  $item[4]) . "\n";
					$content .= "4 találat: " . sprintf("[%8s]", $item[5]) . " db. " . sprintf("[%16s]",  $item[6]) . "\n";
					$content .= "3 találat: " . sprintf("[%8s]", $item[7]) . " db. " . sprintf("[%16s]",  $item[8]) . "\n";
					$content .= "4 találat: " . sprintf("[%8s]", $item[9]) . " db. " . sprintf("[%16s]", $item[10]);
					break;
					
				case "hatos":
					// Év	Hét	Húzásdátum	6 találat (db)	6 találat (Ft)	5+1 találat (db)	5+1 találat (Ft)	5 találat (db)	5 találat (Ft)	4 találat (db)	4 találat (Ft)	3 találat (db)	3 találat (Ft)	Számok

					$content  = "Húzás dátuma: " . $item[2] . "\n\n";
					$content .= "A hatos lottó nyerőszámai: " . $item[13] . ", " . $item[14] . ", " . $item[15] . ", " . $item[16] . ", " . $item[17] . ", " . $item[18] . "\n\n";

					$content .= "Találatok:" . "\n";
					$content .= "==========" . "\n";
					$content .= "  6 találat: " . sprintf("[%8s]", $item[3])  . " db. " . sprintf("[%16s]",  $item[4]) . "\n";
					$content .= "5+1 találat: " . sprintf("[%8s]", $item[5])  . " db. ";
					if(strpos($item[6], "Ft")){								// A CSV-ben egy ideje hiányzik az Ft innen
						$content .= sprintf("[%16s]",  $item[6]);
					}else{
						$content .= sprintf("[%13s]",  $item[6]) . " Ft";
					}
					$content .= "\n";
					$content .= "  5 találat: " . sprintf("[%8s]", $item[7])  . " db. " . sprintf("[%16s]",  $item[8]) . "\n";
					$content .= "  4 találat: " . sprintf("[%8s]", $item[9])  . " db. " . sprintf("[%16s]",  $item[10]) . "\n";
					$content .= "  3 találat: " . sprintf("[%8s]", $item[11]) . " db. " . sprintf("[%16s]",  $item[12]);
					break;
					
				case "skandi":
					$content  = "Húzás dátuma: " . $item[2] . "\n\n";
					$content .= "Gépi sorsolás: " . sprintf("[%2s]", $item[11]) . ", " . sprintf("[%2s]", $item[12]) . ", " . sprintf("[%2s]", $item[13]) . ", " . sprintf("[%2s]", $item[14]) . ", " . sprintf("[%2s]", $item[15]) . ", " . sprintf("[%2s]", $item[16]) . ", " . sprintf("[%2s]", $item[17]) . "\n\n";
					$content .= "Kézi sorsolás: " . sprintf("[%2s]", $item[18]) . ", " . sprintf("[%2s]", $item[19]) . ", " . sprintf("[%2s]", $item[20]) . ", " . sprintf("[%2s]", $item[21]) . ", " . sprintf("[%2s]", $item[22]) . ", " . sprintf("[%2s]", $item[23]) . ", " . sprintf("[%2s]", $item[24]) . "\n\n";

					$content .= "Találatok:" . "\n";
					$content .= "==========" . "\n";
					$content .= "7 találat: " . sprintf("[%8s]", $item[3])  . " db. " . sprintf("[%16s]",  $item[4]) . "\n";
					$content .= "6 találat: " . sprintf("[%8s]", $item[5])  . " db. " . sprintf("[%16s]",  $item[6]) . "\n";
					$content .= "5 találat: " . sprintf("[%8s]", $item[7])  . " db. " . sprintf("[%16s]",  $item[8]) . "\n";
					$content .= "4 találat: " . sprintf("[%8s]", $item[9])  . " db. " . sprintf("[%16s]",  $item[10]) . "\n";
					break;

				case "joker":
					$content  = "Húzás dátuma: " . $item[2] . "\n\n" ;
					$content .= "A Joker nyerőszámai: " . $item[13] . ", " . $item[14] . ", " . $item[15] . ", " . $item[16] . ", " . $item[17] . ", " . $item[18] . "\n\n" ;

					$content .= "Találatok:" . "\n";
					$content .= "==========" . "\n";
					$content .= "  6 találat: " . sprintf("[%8s]", $item[3])  . " db. " . sprintf("[%16s]",  $item[4]) . "\n";
					$content .= "  5 találat: " . sprintf("[%8s]", $item[5])  . " db. " . sprintf("[%16s]",  $item[6]) . "\n";
					$content .= "  4 találat: " . sprintf("[%8s]", $item[7])  . " db. " . sprintf("[%16s]",  $item[8]) . "\n";
					$content .= "  3 találat: " . sprintf("[%8s]", $item[9])  . " db. " . sprintf("[%16s]",  $item[10]). "\n";
					$content .= "  2 találat: " . sprintf("[%8s]", $item[11]) . " db. " . sprintf("[%16s]",  $item[12]);
					break;
					
/*
				case "eurojackpot":
					$content = "Húzás dátuma: " . $item[2] . "\n\n" . "Számok" . $item[11] ;
					break;

				case "luxor":
					$content = "Húzás dátuma: " . $item[2] . "\n\n" . "Számok" . $item[11];
					break;
*/

				default:
					$content = "- - -";
			}

			$content = str_replace("[","", $content);
			$content = str_replace("]","", $content);
			
			$items[] = [
				'title' => $var[$param],
				'description' => $content,
			];
			
		}else{
			$items[] = [
				'title' => 'Missing record!',
				'description' => '0',
			];
		}
		
		//debug($items[0]); die();

		$lottery['channel']['title'] = $var[$param];
		$lottery['channel']['description'] = 'Lottószámok';
		
		//FrozenTime::setToStringFormat("yyyy.MM.dd. KK:mm");		
		FrozenTime::setToStringFormat("yyyy.MM.dd. H:mm");		
		/*
		foreach($weathers as $w){
			
			$datetime = new FrozenTime($w->pubdate);
			
			$items['channel']['title'] = $w->title . " /" . $datetime . "/\n" . $w->description . "\n\n";
			$items['channel']['description'] = $w->title . " /" . $datetime . "/\n" . $w->description . "\n\n";
			
		}
		*/
		//debug( $weather['channel']['description'] ); die();
		
		$rss = [
			'channel' => [
				'title' => $var[$param],
				'link' => 'http://192.168.1.4/rss/lottery/' . $param . '.rss',
				'description' => 'Lottószámok',
				//'atom:link' => ['@href' => $atomLink],
			],
			'items' => $items
		];

		$this->set(['rss' => $rss, '_serialize' => 'rss']);

    }





    /**
     * Jokes method
     *
	 * @param string|null $param: if($param !== null && $param == 'clear-filter')...
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function jokes($param = null)
    {
		$joke = [];
		$atomLink = ['controller' => 'Rss', 'action' => 'jokes', '_ext' => 'rss']; // Example controller and action		
		$this->loadModel('Jokes');
		$jokes = $this->Jokes->find('all', ['conditions' => ['current' => 1]])->first(); //->toArray();
		
		FrozenTime::setToStringFormat("yyyy-MM-dd H:mm");
		$modified = (string) new FrozenTime($jokes->modified);
		
		// Ha egy órája lett lekérve az utolsó egy rekord, akkor váltson újra
		//echo  strtotime("-1 hour") . " - " . strtotime($modified) . " = " . ( strtotime($modified) - strtotime("-1 hour") ) . "<hr>";
		//echo (strtotime("now") - strtotime("-1 hour"));		
		//echo strtotime("now") - strtotime($modified);		
		//if(strtotime($modified) < strtotime("-1 hour")){
			
		if((strtotime("now") - strtotime($modified)) > 3600){
			$next = $this->Jokes->find('all', ['conditions' => ['id >' => $jokes->id]])->first();			
			$this->Jokes->updateAll(['current' => 0], ['current' => 1]);
			if(isset($next->id)){
				$this->Jokes->updateAll(['current' => 1], ['id' => $next->id]);
			}else{
				$this->Jokes->updateAll(['current' => 1], ['id' => 1]);
			}
			
			$expression = new QueryExpression('counter = counter + 1');
			$this->Jokes->updateAll([$expression], ['current' => 1]);
			
			$this->Jokes->updateAll(['modified = NOW()'], ['current' => 1]);
			$jokes = $this->Jokes->find('all', ['conditions' => ['current' => 1]])->first(); //->toArray();
		}

		$joke['channel']['title'] = 'Új viccek minden órában';
		$joke['channel']['description'] = '';
		
		//FrozenTime::setToStringFormat("yyyy.MM.dd. KK:mm");		
		FrozenTime::setToStringFormat("yyyy.MM.dd. H:mm");		
		
		//debug($jokes->toArray()); die();
		
		//foreach($jokes as $w){			
			$joke['channel']['description'] .=  $jokes->title . "\n" . $jokes->body . "\n\n";
		//}
		
		//debug( $joke['channel'] ); die();
		
		$rss = [
			'channel' => [
				'title' => 'Új viccek minden órában',
				'link' => 'http://192.168.1.4/rss/jokes.rss',
				'description' => $joke['channel']['description'],
				//'description' => 'Időjárás adatok a helyi szerverről',
				//'atom:link' => ['@href' => $atomLink],
			],
			'items' => $joke
		];

		$this->set(['rss' => $rss, '_serialize' => 'rss']);

    }




    /**
     * Currency method 
     *
	 * @param string|null $param: if($param !== null && $param == 'clear-filter')...
     * @return \Cake\Http\Response|null|void Renders view
     */
	 
	// http://rss.loc/rss/currency/eurv.rss , ahol eurv, mint EURO vételi árfolyam
	// http://192.168.254.215:8002/rss/currency/eurv.rss
	 
    public function currency($param = 'error')
    {
		$this->loadModel('CibCurrencies');

		$items = [];
		$currency = [];
		
		$var = [
			'error'=> 'Missing key!',
			'eurv' => 'EUR - valuta vételi árfolyam',
			'eurk' => 'EUR - valuta középárfolyam',
			'eure' => 'EUR - valuta eladási árfolyam',
			'gbpv' => 'GBP - valuta vételi árfolyam',
			'gbpk' => 'GBP - valuta középárfolyam',
			'gbpe' => 'GBP - valuta eladási árfolyam',
			'hrkv' => 'HRK - valuta vételi árfolyam',
			'hrkk' => 'HRK - valuta középárfolyam',
			'hrke' => 'HRK - valuta eladási árfolyam',
			'usdv' => 'USD - valuta vételi árfolyam',
			'usdk' => 'USD - valuta középárfolyam',
			'usde' => 'USD - valuta eladási árfolyam',
			'date' => 'pubDate',
		];

		FrozenTime::setToStringFormat("yyyy.MM.dd. H:mm");
		
		if(!array_key_exists($param, $var)){
			$items[] = [
				'title' => 'Missing key!',
				'description' => '0',
				'date' => '0000-00-00 0:00',
			];
			$param = 'error';
		}else{
			if($param == 'date'){
				$currency = $this->CibCurrencies->find('all')->first();
				$description = $currency->pubDate;
			}else{
				$currency = $this->CibCurrencies->find('all', ['conditions' => ['name' => $var[$param]]])->first();
				$description = $currency->description;
			}

			$pubDate = new FrozenTime($currency->pubDate);

			if($currency){
				if($param == 'date'){
					$items[] = [
						'title' => $currency->name,
						'description' => $description,
						'date' => $pubDate,
					];
				}else{
					$value = (float) $description;
					$items[] = [
						'title' => $currency->name,
						'description' => number_format($value, 2, ',', ' ') . " Ft.",
						'date' => $pubDate,
					];
				}
			}else{
				$items[] = [
					'title' => 'Adatok lekérdezés alatt!',
					'description' => '0',
					'date' => '0000-00-00 0:00',
				];			
			}
				
		}
		
		$rss = [
			'channel' => [
				'title' => $var[$param],
				//'link' => 'https://net.cib.hu/%5Erss/valarf.xml',
				'link' => 'http://rss.loc/rss/currency/' . $param . '.rss',
				'description' => $description,
				'pubDate' => $pubDate,
				'date' => new FrozenTime($pubDate),
				// 'description' => 'CIB Bank RSS',
			],
			'items' => $items
		];

		$this->set(['rss' => $rss, '_serialize' => 'rss']);

    }



	
    /**
     * Weathers method
     *
	 * @param string|null $param: if($param !== null && $param == 'clear-filter')...
     * @return \Cake\Http\Response|null|void Renders view
	 * http://rss.loc/rss/weathers.rss
     */
    public function weathers()
    {
		$weather = [];

		$atomLink = ['controller' => 'Rss', 'action' => 'weather', '_ext' => 'rss']; // Example controller and action
		
		$this->loadModel('HirstartWeathers');

		$weathers = $this->HirstartWeathers->find('all')->order(['pubdate' => 'desc'])->limit(3)->toArray();

/*
		$weather = [
			'channel' => [
				'title' => 'Hírstart időjárás',
				'link' => 'http://192.168.1.4/rss/weathers',
				'description' => 'Időjárás adatok a helyi szerverről',
				'atom:link' => ['@href' => $atomLink],
			],
			'items' => $weathers
		];
*/

		$weather['channel']['title'] = 'Időjárás: hirstart.hu';
		$weather['channel']['description'] = '';
		
		//FrozenTime::setToStringFormat("yyyy.MM.dd. KK:mm");		
		FrozenTime::setToStringFormat("yyyy.MM.dd. H:mm");		
		
		foreach($weathers as $w){			
			$datetime = new FrozenTime($w->pubdate);			
			$weather['channel']['description'] .=  $datetime . "\n" . $w->title . ".\n" . $w->description . "\n\n";			
		}
		
		//debug( $weather['channel']['description'] ); die();
		
		$rss = [
			'channel' => [
				'title' => 'Hírstart időjárás',
				'link' => 'http://192.168.1.4/rss/weathers.rss',
				'description' => $weather['channel']['description'],
				//'description' => 'Időjárás adatok a helyi szerverről',
				//'atom:link' => ['@href' => $atomLink],
			],
			'items' => $weather
		];

		$this->set(['rss' => $rss, '_serialize' => 'rss']);

    }



	
    /**
	 * http://rss.loc/rss/restaurant/title.rss
	 * http://rss.loc/rss/restaurant/days.rss
	 * http://rss.loc/rss/restaurant/menu.rss
	 * http://rss.loc/rss/restaurant/prices.rss
     */
    public function restaurant($param = null)
    {
		$restaurant = [];

		$atomLink = ['controller' => 'Rss', 'action' => 'restaurant', '_ext' => 'rss']; // Example controller and action
		
		$this->loadModel('Restaurants');

		$rest = $this->Restaurants->find('all', [
			'conditions' => [
				'date_from <= ' => FrozenTime::now(),
				'date_to >= ' 	=> FrozenTime::now(),
			]
		])->first();

		//debug($rest->toArray());

		if(!empty($rest)){
			switch($param){
				case "title":
					$title = 'Belvardi Fogadó - Napok';
					$description = $rest->menu_from_to;
					break;

				case "days":
					$title = 'Belvardi Fogadó - Napok';
					$description = $rest->days_text;
					break;
					
				case "prices":
					$title = 'Belvardi Fogadó - Árak';
					$description = $rest->prices;
					break;
					
				case "menu":
					$title = 'Belvardi Fogadó - Menü';
					$description = $rest->text;
					break;
				
				default:
					$description = 'Wrong parameter!';
			}
		}else{
			$title = "Belvardi Fogadó - Menü";
			$description = "* * * A menü összeállítás alatt! * * *";
		}



		$restaurant['channel']['title'] = $title;
		$restaurant['channel']['description'] = $description;
		
		$rss = [
			'channel' => [
				'title' => $title,
				'link' => 'http://192.168.1.4/rss/reataurant/' . $param . '.rss',
				'description' => $restaurant['channel']['description'],
				//'description' => 'Időjárás adatok a helyi szerverről',
				//'atom:link' => ['@href' => $atomLink],
			],
			'items' => $restaurant
		];

		$this->set(['rss' => $rss, '_serialize' => 'rss']);

    }












}

// https://github.com/dereuromark/cakephp-feed/blob/master/docs/README.md
// https://www.hirstart.hu/site/publicrss.php?pos=balrovat&pid=378
// https://github.com/dereuromark/cakephp-feed/blob/master/docs/View/Rss.md



