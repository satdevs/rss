<?php
// Baked at 2022.03.09. 14:37:27
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;

/**
 * HirstartWeathers Controller
 *
 * @property \App\Model\Table\HirstartWeathersTable $HirstartWeathers
 * @method \App\Model\Entity\HirstartWeather[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class HirstartWeathersController extends AppController
{

    /**
     * Initialize controller
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
		$this->set('title', __('HirstartWeathers'));
		
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
		$hirstartWeathers = null;
		
		$this->set('title', __('HirstartWeathers'));

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
				//'HirstartWeathers.name' 		=> 1,
				//'HirstartWeathers.visible' 		=> 1,
				//'HirstartWeathers.created >= ' 	=> new \DateTime('-10 days'),
				//'HirstartWeathers.modified >= '	=> new \DateTime('-10 days'),
			],
			/*
			// Nem tanácsos az order-t itt használni, mert pl az edit után az utolsó  ordert ugyan beálíltja, de
			// kiegészíti ezzel s így az utoljára mentett rekord nem lesz megtalálható az X-edik oldalon, mert az az elsőre kerül.
			// A felhasználó állítson be rendezettséget magának! Kivételes esetek persze lehetnek!
			*/
			'order' => [
				//'HirstartWeathers.id' 			=> 'desc',
				//'HirstartWeathers.pubdate'		=> 'desc',
				//'HirstartWeathers.visible' 		=> 'desc',
				//'HirstartWeathers.pos' 			=> 'desc',
				//'HirstartWeathers.rank' 			=> 'asc',
				//'HirstartWeathers.created' 		=> 'desc',
				//'HirstartWeathers.modified' 		=> 'desc',
			],
			'limit' => $this->config['index_number_of_rows'],
			'maxLimit' => $this->config['index_number_of_rows'],
			//'sortableFields' => ['id', 'name', 'created', '...'],
			//'paramType' => 'querystring',
			//'fields' => ['HirstartWeathers.id', 'HirstartWeathers.name', ...],
			//'finder' => 'published',
        ];

		//$this->paging = $this->session->read('Layout.' . $this->controller . '.Paging');

		if( $this->paging === null){
			$this->paginate['order'] = [
				//'HirstartWeathers.id' 			=> 'desc',
				//'HirstartWeathers.name' 			=> 'asc',
				//'HirstartWeathers.visible' 		=> 'desc',
				//'HirstartWeathers.pos' 			=> 'desc',
				//'HirstartWeathers.rank' 			=> 'asc',
				//'HirstartWeathers.created' 		=> 'desc',
				//'HirstartWeathers.modified' 		=> 'desc',
			];
		}else{
			if($this->request->getQuery('sort') === null && $this->request->getQuery('direction') === null){
				$this->paginate['order'] = [
					// If not in URL-ben, then come from sessinon...
					$this->paging['HirstartWeathers']['sort'] => $this->paging['HirstartWeathers']['direction']	
				];
			}
		}

		if($this->request->getQuery('page') === null && !isset($this->paging['HirstartWeathers']['page']) ){
			$this->paginate['page'] = 1;
		}else{
			$this->paginate['page'] = (isset($this->paging['HirstartWeathers']['page'])) ? $this->paging['HirstartWeathers']['page'] : 1;
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
						//	'page'		=> $this->paging['HirstartWeathers']['page'], 	// Vagy 1
						//	'sort'		=> $this->paging['HirstartWeathers']['sort'], 
						//	'direction'	=> $this->paging['HirstartWeathers']['direction'],
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
			//$this->paginate['conditions'] = ['HirstartWeathers.name LIKE' => $q ];
			$this->paginate['conditions'][] = [
				'OR' => [
					['HirstartWeathers.name LIKE' => $search['s'] ],
					//['HirstartWeathers.title LIKE' => $search['s'] ], // ... just add more fields
				]
			];
			
		}
		// -- /.Filter --
		
		try {
			$hirstartWeathers = $this->paginate($this->HirstartWeathers);
		} catch (NotFoundException $e) {
			$paging = $this->request->getAttribute('paging');
			if($paging['HirstartWeathers']['prevPage'] !== null && $paging['HirstartWeathers']['prevPage']){
				if($paging['HirstartWeathers']['page'] !== null && $paging['HirstartWeathers']['page'] > 0){
					return $this->redirect([
						'controller' => $this->controller, 
						'action' => 'index', 
						'?' => [
							'page'		=> 1,	//$this->paging['HirstartWeathers']['page'],
							'sort'		=> $this->paging['HirstartWeathers']['sort'],
							'direction'	=> $this->paging['HirstartWeathers']['direction'],
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

		//if(!isset($this->paginate['order']['']) && $this->paginate['order'][''] == null){
			$this->paginate['order'] = ['HirstartWeathers.pubdate' => 'desc'];
		//}
		//debug($this->paginate);
		//debug($this->paging); die();
		//debug($this->paginate['order']); die();
		
		$this->set('paging', $this->paging);
		$this->set('layout' . $this->controller . 'LastId', $this->session->read('Layout.' . $this->controller . '.LastId'));
		$this->set(compact('hirstartWeathers'));

		
	}


    /**
     * View method
     *
     * @param string|null $id Hirstart Weather id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->set('title', __('HirstartWeather'));
		
        $hirstartWeather = $this->HirstartWeathers->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

		$name = $hirstartWeather->name;

        $this->set(compact('hirstartWeather', 'id', 'name'));
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->set('title', __('HirstartWeather'));
        $hirstartWeather = $this->HirstartWeathers->newEmptyEntity();
        if ($this->request->is('post')) {
            $hirstartWeather = $this->HirstartWeathers->patchEntity($hirstartWeather, $this->request->getData());
            if ($this->HirstartWeathers->save($hirstartWeather)) {
                //$this->Flash->success(__('The hirstart weather has been saved.'));
                $this->Flash->success(__('Has been saved.'));

				$this->session->write('Layout.' . $this->controller . '.LastId', $hirstartWeather->id);
	
                //return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> 1,
						'sort'		=> 'id',
						'direction'	=> 'desc',
					],
					'#' => $hirstartWeather->id	// Az állandó header miatt takarásban van még. Majd...
				]);

                return $this->redirect(['action' => 'index']);
            }
            //$this->Flash->error(__('The hirstart weather could not be saved. Please, try again.'));
			$this->Flash->error(__('Could not be saved. Please, try again.'));
        }
        $this->set(compact('hirstartWeather'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Hirstart Weather id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->set('title', __('HirstartWeather'));
        $hirstartWeather = $this->HirstartWeathers->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

        if ($this->request->is(['patch', 'post', 'put'])) {
			//debug($this->request->getData()); //die();
            $hirstartWeather = $this->HirstartWeathers->patchEntity($hirstartWeather, $this->request->getData());
            //debug($hirstartWeather); //die();
			if ($this->HirstartWeathers->save($hirstartWeather)) {
                //$this->Flash->success(__('The hirstart weather has been saved.'));
                $this->Flash->success(__('Has been saved.'));
				
				//return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> (isset($this->paging['HirstartWeathers']['page'])) ? $this->paging['HirstartWeathers']['page'] : 1, 		// or 1
						'sort'		=> (isset($this->paging['HirstartWeathers']['sort'])) ? $this->paging['HirstartWeathers']['sort'] : 'created', 
						'direction'	=> (isset($this->paging['HirstartWeathers']['direction'])) ? $this->paging['HirstartWeathers']['direction'] : 'desc',
					],
					'#' => $id
				]);
				
            }
            //$this->Flash->error(__('The hirstart weather could not be saved. Please, try again.'));
            $this->Flash->error(__('Could not be saved. Please, try again.'));
        }

		$name = $hirstartWeather->name;

        $this->set(compact('hirstartWeather', 'id', 'name'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Hirstart Weather id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $hirstartWeather = $this->HirstartWeathers->get($id);
        if ($this->HirstartWeathers->delete($hirstartWeather)) {
            //$this->Flash->success(__('The hirstart weather has been deleted.'));
            $this->Flash->success(__('Has been deleted.'));
        } else {
            //$this->Flash->error(__('The hirstart weather could not be deleted. Please, try again.'));
            $this->Flash->error(__('Could not be deleted. Please, try again.'));
        }

        //return $this->redirect(['action' => 'index']);
		return $this->redirect([
			'controller' => $this->controller, 
			'action' => 'index', 
			'?' => [
				'page'		=> $this->paging['HirstartWeathers']['page'], 
				'sort'		=> $this->paging['HirstartWeathers']['sort'], 
				'direction'	=> $this->paging['HirstartWeathers']['direction'],
			]
		]);
		
    }

}

