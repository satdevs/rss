<?php
// Baked at 2022.03.25. 14:01:48
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;

/**
 * Forecasts Controller
 *
 * @property \App\Model\Table\ForecastsTable $Forecasts
 * @method \App\Model\Entity\Forecast[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ForecastsController extends AppController
{

    /**
     * Initialize controller
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
		$this->set('title', __('Forecasts'));
		
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
		$forecasts = null;
		
		$this->set('title', __('Forecasts'));

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
				//'Forecasts.name' 		=> 1,
				//'Forecasts.visible' 		=> 1,
				//'Forecasts.created >= ' 	=> new \DateTime('-10 days'),
				//'Forecasts.modified >= '	=> new \DateTime('-10 days'),
			],
			/*
			// Nem tanácsos az order-t itt használni, mert pl az edit után az utolsó  ordert ugyan beálíltja, de
			// kiegészíti ezzel s így az utoljára mentett rekord nem lesz megtalálható az X-edik oldalon, mert az az elsőre kerül.
			// A felhasználó állítson be rendezettséget magának! Kivételes esetek persze lehetnek!
			*/
			'order' => [
				//'Forecasts.id' 			=> 'desc',
				//'Forecasts.name' 		=> 'asc',
				'Forecasts.date' 			=> 'desc',
				//'Forecasts.visible' 		=> 'desc',
				//'Forecasts.pos' 			=> 'desc',
				//'Forecasts.rank' 		=> 'asc',
				//'Forecasts.created' 		=> 'desc',
				//'Forecasts.modified' 	=> 'desc',
			],
			'limit' => $this->config['index_number_of_rows'],
			'maxLimit' => $this->config['index_number_of_rows'],
			//'sortableFields' => ['id', 'name', 'created', '...'],
			//'paramType' => 'querystring',
			//'fields' => ['Forecasts.id', 'Forecasts.name', ...],
			//'finder' => 'published',
        ];

		//$this->paging = $this->session->read('Layout.' . $this->controller . '.Paging');

		$this->paginate['order'] = ['Forecasts.date' => 'desc'];

		if( $this->paging === null){
			$this->paginate['order'] = [
				//'Forecasts.id' 			=> 'desc',
				//'Forecasts.name' 		=> 'asc',
				//'Forecasts.visible' 		=> 'desc',
				//'Forecasts.pos' 			=> 'desc',
				//'Forecasts.rank' 		=> 'asc',
				//'Forecasts.created' 		=> 'desc',
				//'Forecasts.modified' 	=> 'desc',
			];
		}else{
			if($this->request->getQuery('sort') === null && $this->request->getQuery('direction') === null){
				$this->paginate['order'] = [
					// If not in URL-ben, then come from sessinon...
					$this->paging['Forecasts']['sort'] => $this->paging['Forecasts']['direction']	
				];
			}
		}

		if($this->request->getQuery('page') === null && !isset($this->paging['Forecasts']['page']) ){
			$this->paginate['page'] = 1;
		}else{
			$this->paginate['page'] = (isset($this->paging['Forecasts']['page'])) ? $this->paging['Forecasts']['page'] : 1;
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
						//	'page'		=> $this->paging['Forecasts']['page'], 	// Vagy 1
						//	'sort'		=> $this->paging['Forecasts']['sort'], 
						//	'direction'	=> $this->paging['Forecasts']['direction'],
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
			//$this->paginate['conditions'] = ['Forecasts.name LIKE' => $q ];
			$this->paginate['conditions'][] = [
				'OR' => [
					['Forecasts.name LIKE' => $search['s'] ],
					//['Forecasts.title LIKE' => $search['s'] ], // ... just add more fields
				]
			];
			
		}
		// -- /.Filter --
		
		try {
			$forecasts = $this->paginate($this->Forecasts);
		} catch (NotFoundException $e) {
			$paging = $this->request->getAttribute('paging');
			if($paging['Forecasts']['prevPage'] !== null && $paging['Forecasts']['prevPage']){
				if($paging['Forecasts']['page'] !== null && $paging['Forecasts']['page'] > 0){
					return $this->redirect([
						'controller' => $this->controller, 
						'action' => 'index', 
						'?' => [
							'page'		=> 1,	//$this->paging['Forecasts']['page'],
							'sort'		=> $this->paging['Forecasts']['sort'],
							'direction'	=> $this->paging['Forecasts']['direction'],
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
		$this->set(compact('forecasts'));
		
	}


    /**
     * View method
     *
     * @param string|null $id Forecast id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->set('title', __('Forecast'));
		
        $forecast = $this->Forecasts->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

		$name = $forecast->name;

        $this->set(compact('forecast', 'id', 'name'));
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->set('title', __('Forecast'));
        $forecast = $this->Forecasts->newEmptyEntity();
        if ($this->request->is('post')) {
            $forecast = $this->Forecasts->patchEntity($forecast, $this->request->getData());
            if ($this->Forecasts->save($forecast)) {
                //$this->Flash->success(__('The forecast has been saved.'));
                $this->Flash->success(__('Has been saved.'));

				$this->session->write('Layout.' . $this->controller . '.LastId', $forecast->id);
	
                //return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> 1,
						'sort'		=> 'id',
						'direction'	=> 'desc',
					],
					'#' => $forecast->id	// Az állandó header miatt takarásban van még. Majd...
				]);

                return $this->redirect(['action' => 'index']);
            }
            //$this->Flash->error(__('The forecast could not be saved. Please, try again.'));
			$this->Flash->error(__('Could not be saved. Please, try again.'));
        }
        $this->set(compact('forecast'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Forecast id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->set('title', __('Forecast'));
        $forecast = $this->Forecasts->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

        if ($this->request->is(['patch', 'post', 'put'])) {
			//debug($this->request->getData()); //die();
            $forecast = $this->Forecasts->patchEntity($forecast, $this->request->getData());
            //debug($forecast); //die();
			if ($this->Forecasts->save($forecast)) {
                //$this->Flash->success(__('The forecast has been saved.'));
                $this->Flash->success(__('Has been saved.'));
				
				//return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> (isset($this->paging['Forecasts']['page'])) ? $this->paging['Forecasts']['page'] : 1, 		// or 1
						'sort'		=> (isset($this->paging['Forecasts']['sort'])) ? $this->paging['Forecasts']['sort'] : 'created', 
						'direction'	=> (isset($this->paging['Forecasts']['direction'])) ? $this->paging['Forecasts']['direction'] : 'desc',
					],
					'#' => $id
				]);
				
            }
            //$this->Flash->error(__('The forecast could not be saved. Please, try again.'));
            $this->Flash->error(__('Could not be saved. Please, try again.'));
        }

		$name = $forecast->name;

        $this->set(compact('forecast', 'id', 'name'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Forecast id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $forecast = $this->Forecasts->get($id);
        if ($this->Forecasts->delete($forecast)) {
            //$this->Flash->success(__('The forecast has been deleted.'));
            $this->Flash->success(__('Has been deleted.'));
        } else {
            //$this->Flash->error(__('The forecast could not be deleted. Please, try again.'));
            $this->Flash->error(__('Could not be deleted. Please, try again.'));
        }

        //return $this->redirect(['action' => 'index']);
		return $this->redirect([
			'controller' => $this->controller, 
			'action' => 'index', 
			'?' => [
				'page'		=> $this->paging['Forecasts']['page'], 
				'sort'		=> $this->paging['Forecasts']['sort'], 
				'direction'	=> $this->paging['Forecasts']['direction'],
			]
		]);
		
    }

}

