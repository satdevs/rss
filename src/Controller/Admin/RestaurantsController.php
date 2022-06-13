<?php
// Baked at 2022.03.17. 09:13:02
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use Cake\I18n\FrozenTime;
use Cake\Utility\Text;

/**
 * Restaurants Controller
 *
 * @property \App\Model\Table\RestaurantsTable $Restaurants
 * @method \App\Model\Entity\Restaurant[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class RestaurantsController extends AppController
{

    /**
     * Initialize controller
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
		$this->set('title', __('Restaurants'));
		
	}
	
    /**
     * Index method
     *
	 * @param string|null $param: if($param !== null && $param == 'clear-filter')...
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index($param = null)
    {
		$search = null;
		$restaurants = null;
		
		$this->set('title', __('Restaurants'));

		//$this->config['index_number_of_rows'] = 10;
		if($this->config['index_number_of_rows'] === null){
			$this->config['index_number_of_rows'] = 20;
		}
		
		// Clear filter from session
		if($param !== null && $param == 'clear-filter'){
			$this->session->delete('Layout.' . $this->controller . '.Search');
			$this->redirect( $this->request->referer() );
		}		
		
        $this->paginate = [
			'conditions' => [
				//'Restaurants.name' 		=> 1,
				//'Restaurants.visible' 		=> 1,
				//'Restaurants.created >= ' 	=> new \DateTime('-10 days'),
				//'Restaurants.modified >= '	=> new \DateTime('-10 days'),
			],
			/*
			// Nem tanácsos az order-t itt használni, mert pl az edit után az utolsó  ordert ugyan beálíltja, de
			// kiegészíti ezzel s így az utoljára mentett rekord nem lesz megtalálható az X-edik oldalon, mert az az elsőre kerül.
			// A felhasználó állítson be rendezettséget magának! Kivételes esetek persze lehetnek!
			*/
			'order' => [
				//'Restaurants.id' 			=> 'desc',
				//'Restaurants.name' 		=> 'asc',
				//'Restaurants.visible' 		=> 'desc',
				//'Restaurants.pos' 			=> 'desc',
				//'Restaurants.rank' 		=> 'asc',
				//'Restaurants.created' 		=> 'desc',
				//'Restaurants.modified' 	=> 'desc',
			],
			'limit' => $this->config['index_number_of_rows'],
			'maxLimit' => $this->config['index_number_of_rows'],
			//'sortableFields' => ['id', 'name', 'created', '...'],
			//'paramType' => 'querystring',
			//'fields' => ['Restaurants.id', 'Restaurants.name', ...],
			//'finder' => 'published',
        ];

		//$this->paging = $this->session->read('Layout.' . $this->controller . '.Paging');

		if( $this->paging === null){
			$this->paginate['order'] = [
				//'Restaurants.id' 			=> 'desc',
				//'Restaurants.name' 		=> 'asc',
				//'Restaurants.visible' 		=> 'desc',
				//'Restaurants.pos' 			=> 'desc',
				//'Restaurants.rank' 		=> 'asc',
				//'Restaurants.created' 		=> 'desc',
				//'Restaurants.modified' 	=> 'desc',
			];
		}else{
			if($this->request->getQuery('sort') === null && $this->request->getQuery('direction') === null){
				$this->paginate['order'] = [
					// If not in URL-ben, then come from sessinon...
					$this->paging['Restaurants']['sort'] => $this->paging['Restaurants']['direction']	
				];
			}
		}

		if($this->request->getQuery('page') === null && !isset($this->paging['Restaurants']['page']) ){
			$this->paginate['page'] = 1;
		}else{
			$this->paginate['page'] = (isset($this->paging['Restaurants']['page'])) ? $this->paging['Restaurants']['page'] : 1;
		}
		
		// -- Filter --
		if ($this->request->is('post') || $this->session->read('Layout.' . $this->controller . '.Search') !== null && $this->session->read('Layout.' . $this->controller . '.Search') !== []) {
				
			if( $this->request->is('post') ){
				$search = $this->request->getData();
				$this->session->write('Layout.' . $this->controller . '.Search', $search);
				if($search !== null && $search['s'] !== null && $search['s'] == ''){
					$this->session->delete('Layout.' . $this->controller . '.Search');
					return $this->redirect([
						'controller' => $this->controller, 
						'action' => 'index', 
						//'?' => [			// Not tested!!!
						//	'page'		=> $this->paging['Restaurants']['page'], 	// Vagy 1
						//	'sort'		=> $this->paging['Restaurants']['sort'], 
						//	'direction'	=> $this->paging['Restaurants']['direction'],
						//]
					]);
				}
			}else{
				if($this->session->check('Layout.' . $this->controller . '.Search')){
					$search = $this->session->read('Layout.' . $this->controller . '.Search');
				}
			}

			$this->set('search', $search['s']);
			
			$search['s'] = '%'.str_replace(' ', '%', $search['s']).'%';
			//$this->paginate['conditions'] = ['Restaurants.name LIKE' => $q ];
			$this->paginate['conditions'][] = [
				'OR' => [
					['Restaurants.name LIKE' => $search['s'] ],
					//['Restaurants.title LIKE' => $search['s'] ], // ... just add more fields
				]
			];
			
		}
		// -- /.Filter --

		$this->paginate['order'] = ['id' => 'desc'];
		
		try {
			$restaurants = $this->paginate($this->Restaurants);
		} catch (NotFoundException $e) {
			$paging = $this->request->getAttribute('paging');
			if($paging['Restaurants']['prevPage'] !== null && $paging['Restaurants']['prevPage']){
				if($paging['Restaurants']['page'] !== null && $paging['Restaurants']['page'] > 0){
					return $this->redirect([
						'controller' => $this->controller, 
						'action' => 'index', 
						'?' => [
							'page'		=> 1,	//$this->paging['Restaurants']['page'],
							'sort'		=> $this->paging['Restaurants']['sort'],
							'direction'	=> $this->paging['Restaurants']['direction'],
						],
					]);			
				}
			}
			
		}

		$paging = $this->request->getAttribute('paging');

		if($this->paging !== $paging){
			$this->paging = $paging;
			$this->session->write('Layout.' . $this->controller . '.Paging', $paging);
		}

		$this->set('paging', $this->paging);
		$this->set('layout' . $this->controller . 'LastId', $this->session->read('Layout.' . $this->controller . '.LastId'));
		$this->set(compact('restaurants'));
		
	}


    /**
     * View method
     *
     * @param string|null $id Restaurant id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->set('title', __('Restaurant'));
		
        $restaurant = $this->Restaurants->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

		$name = $restaurant->name;

        $this->set(compact('restaurant', 'id', 'name'));
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		
		$last = $this->Restaurants->find('all', ['order' => ['id' =>'desc']])->first();


		$days = $last->days_text;
		$prices = $last->prices;
		
		$this->set('days', $days);
		$this->set('prices', $prices);

		//FrozenTime::setToStringFormat("yyyy.MM.dd.");
		FrozenTime::setToStringFormat("yyyy-MM-dd");
		$date_from = new FrozenTime($last->date_to);
		$date_to = $date_from->modify('+7 day');
		$this->set('date_from', $date_from);
		$this->set('date_to', $date_to);

		$date_from_text = $date_from->modify('+3 day');
		$date_to_text = $date_from->modify('+7 day');
		$this->set('date_from_text', $date_from_text);
		$this->set('date_to_text', $date_to_text);
		
		//debug($date_from);
		//debug($date_to);
		//die();
		
		$this->set('title', __('Restaurant'));
        $restaurant = $this->Restaurants->newEmptyEntity();
        if ($this->request->is('post')) {
			$data = $this->request->getData();
			
			$data['date_from'] = substr($data['date_from'], 0, 10);
			$data['date_to'] = substr($data['date_to'], 0, 10);
			
			$data['date_from'] 	.= ' 13:00:00';
			$data['date_to'] 	.= ' 12:59:59';
			
			$data['date_from'] 	= str_replace('/', '-', $data['date_from']);
			$data['date_from'] 	= str_replace('.', '-', $data['date_from']);
			$data['date_to'] 	= str_replace('/', '-', $data['date_to']);
			$data['date_to'] 	= str_replace('.', '-', $data['date_to']);
			
            $restaurant = $this->Restaurants->patchEntity($restaurant, $data);
            if ($this->Restaurants->save($restaurant)) {
                //$this->Flash->success(__('The restaurant has been saved.'));
                $this->Flash->success(__('Has been saved.'));

				$this->session->write('Layout.' . $this->controller . '.LastId', $restaurant->id);
	
                //return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> 1,
						'sort'		=> 'id',
						'direction'	=> 'desc',
					],
					'#' => $restaurant->id	// Az állandó header miatt takarásban van még. Majd...
				]);

                return $this->redirect(['action' => 'index']);
            }
            //$this->Flash->error(__('The restaurant could not be saved. Please, try again.'));
			$this->Flash->error(__('Could not be saved. Please, try again.'));
        }
        $this->set(compact('restaurant'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Restaurant id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->set('title', __('Restaurant'));
        $restaurant = $this->Restaurants->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

        if ($this->request->is(['patch', 'post', 'put'])) {
			//debug($this->request->getData()); //die();
			$data = $this->request->getData();

			$data['date_from'] = substr($data['date_from'], 0, 10);
			$data['date_to'] = substr($data['date_to'], 0, 10);
			
			$data['date_from'] 	.= ' 13:00:00';
			$data['date_to'] 	.= ' 12:59:59';
			
			$data['date_from'] 	= str_replace('/', '-', $data['date_from']);
			$data['date_from'] 	= str_replace('.', '-', $data['date_from']);
			$data['date_to'] 	= str_replace('/', '-', $data['date_to']);
			$data['date_to'] 	= str_replace('.', '-', $data['date_to']);

            $restaurant = $this->Restaurants->patchEntity($restaurant, $data);
            //debug($restaurant); //die();
			if ($this->Restaurants->save($restaurant)) {
                //$this->Flash->success(__('The restaurant has been saved.'));
                $this->Flash->success(__('Has been saved.'));
				
				//return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> (isset($this->paging['Restaurants']['page'])) ? $this->paging['Restaurants']['page'] : 1, 		// or 1
						'sort'		=> (isset($this->paging['Restaurants']['sort'])) ? $this->paging['Restaurants']['sort'] : 'created', 
						'direction'	=> (isset($this->paging['Restaurants']['direction'])) ? $this->paging['Restaurants']['direction'] : 'desc',
					],
					'#' => $id
				]);
				
            }
            //$this->Flash->error(__('The restaurant could not be saved. Please, try again.'));
            $this->Flash->error(__('Could not be saved. Please, try again.'));
        }

		$name = $restaurant->name;

        $this->set(compact('restaurant', 'id', 'name'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Restaurant id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $restaurant = $this->Restaurants->get($id);
        if ($this->Restaurants->delete($restaurant)) {
            //$this->Flash->success(__('The restaurant has been deleted.'));
            $this->Flash->success(__('Has been deleted.'));
        } else {
            //$this->Flash->error(__('The restaurant could not be deleted. Please, try again.'));
            $this->Flash->error(__('Could not be deleted. Please, try again.'));
        }

        //return $this->redirect(['action' => 'index']);
		return $this->redirect([
			'controller' => $this->controller, 
			'action' => 'index', 
			'?' => [
				'page'		=> $this->paging['Restaurants']['page'], 
				'sort'		=> $this->paging['Restaurants']['sort'], 
				'direction'	=> $this->paging['Restaurants']['direction'],
			]
		]);
		
    }

}

