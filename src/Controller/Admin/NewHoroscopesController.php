<?php
// Baked at 2022.03.24. 15:03:09
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;

/**
 * NewHoroscopes Controller
 *
 * @property \App\Model\Table\NewHoroscopesTable $NewHoroscopes
 * @method \App\Model\Entity\NewHoroscope[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class NewHoroscopesController extends AppController
{

    /**
     * Initialize controller
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
		$this->set('title', __('NewHoroscopes'));
		
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
		$newHoroscopes = null;
		
		$this->set('title', __('NewHoroscopes'));

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
				//'NewHoroscopes.name' 		=> 1,
				//'NewHoroscopes.visible' 		=> 1,
				'NewHoroscopes.year' 		=> date('Y'),
				'NewHoroscopes.month' 		=> date('m'),
				'NewHoroscopes.day' 		=> date('d'),
				//'NewHoroscopes.created >= ' 	=> new \DateTime('-10 days'),
				//'NewHoroscopes.modified >= '	=> new \DateTime('-10 days'),
			],
			/*
			// Nem tanácsos az order-t itt használni, mert pl az edit után az utolsó  ordert ugyan beálíltja, de
			// kiegészíti ezzel s így az utoljára mentett rekord nem lesz megtalálható az X-edik oldalon, mert az az elsőre kerül.
			// A felhasználó állítson be rendezettséget magának! Kivételes esetek persze lehetnek!
			*/
			'order' => [
				//'NewHoroscopes.id' 			=> 'desc',
				//'NewHoroscopes.name' 		=> 'asc',
				//'NewHoroscopes.visible' 		=> 'desc',
				//'NewHoroscopes.pos' 			=> 'desc',
				//'NewHoroscopes.rank' 		=> 'asc',
				//'NewHoroscopes.created' 		=> 'desc',
				//'NewHoroscopes.modified' 	=> 'desc',
			],
			'limit' => $this->config['index_number_of_rows'],
			'maxLimit' => $this->config['index_number_of_rows'],
			//'sortableFields' => ['id', 'name', 'created', '...'],
			//'paramType' => 'querystring',
			//'fields' => ['NewHoroscopes.id', 'NewHoroscopes.name', ...],
			//'finder' => 'published',
        ];

		//$this->paging = $this->session->read('Layout.' . $this->controller . '.Paging');

		if( $this->paging === null){
			$this->paginate['order'] = [
				//'NewHoroscopes.id' 			=> 'desc',
				//'NewHoroscopes.name' 		=> 'asc',
				//'NewHoroscopes.visible' 		=> 'desc',
				//'NewHoroscopes.pos' 			=> 'desc',
				//'NewHoroscopes.rank' 		=> 'asc',
				//'NewHoroscopes.created' 		=> 'desc',
				//'NewHoroscopes.modified' 	=> 'desc',
			];
		}else{
			if($this->request->getQuery('sort') === null && $this->request->getQuery('direction') === null){
				$this->paginate['order'] = [
					// If not in URL-ben, then come from sessinon...
					$this->paging['NewHoroscopes']['sort'] => $this->paging['NewHoroscopes']['direction']	
				];
			}
		}

		if($this->request->getQuery('page') === null && !isset($this->paging['NewHoroscopes']['page']) ){
			$this->paginate['page'] = 1;
		}else{
			$this->paginate['page'] = (isset($this->paging['NewHoroscopes']['page'])) ? $this->paging['NewHoroscopes']['page'] : 1;
		}
		
		// -- Filter --
		if ($this->request->is('post') || $this->session->read('Layout.' . $this->controller . '.Search') !== null && $this->session->read('Layout.' . $this->controller . '.Search') !== []) {
				
			$this->paginate['conditions'] = [];
				
			if( $this->request->is('post') ){
				$search = $this->request->getData();
				$this->session->write('Layout.' . $this->controller . '.Search', $search);
				if($search !== null && $search['s'] !== null && $search['s'] == ''){
					$this->session->delete('Layout.' . $this->controller . '.Search');
					return $this->redirect([
						'controller' => $this->controller, 
						'action' => 'index', 
						//'?' => [			// Not tested!!!
						//	'page'		=> $this->paging['NewHoroscopes']['page'], 	// Vagy 1
						//	'sort'		=> $this->paging['NewHoroscopes']['sort'], 
						//	'direction'	=> $this->paging['NewHoroscopes']['direction'],
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
			//$this->paginate['conditions'] = ['NewHoroscopes.name LIKE' => $q ];
			$this->paginate['conditions'][] = [
				'OR' => [
					['NewHoroscopes.name LIKE' => $search['s'] ],
					//['NewHoroscopes.title LIKE' => $search['s'] ], // ... just add more fields
				]
			];
			
		}
		// -- /.Filter --
		
		try {
			$newHoroscopes = $this->paginate($this->NewHoroscopes);
		} catch (NotFoundException $e) {
			$paging = $this->request->getAttribute('paging');
			if($paging['NewHoroscopes']['prevPage'] !== null && $paging['NewHoroscopes']['prevPage']){
				if($paging['NewHoroscopes']['page'] !== null && $paging['NewHoroscopes']['page'] > 0){
					return $this->redirect([
						'controller' => $this->controller, 
						'action' => 'index', 
						'?' => [
							'page'		=> 1,	//$this->paging['NewHoroscopes']['page'],
							'sort'		=> $this->paging['NewHoroscopes']['sort'],
							'direction'	=> $this->paging['NewHoroscopes']['direction'],
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
		$this->set(compact('newHoroscopes'));
		
	}


    /**
     * View method
     *
     * @param string|null $id New Horoscope id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->set('title', __('NewHoroscope'));
		
        $newHoroscope = $this->NewHoroscopes->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

		$name = $newHoroscope->name;

        $this->set(compact('newHoroscope', 'id', 'name'));
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->set('title', __('NewHoroscope'));
        $newHoroscope = $this->NewHoroscopes->newEmptyEntity();
        if ($this->request->is('post')) {
            $newHoroscope = $this->NewHoroscopes->patchEntity($newHoroscope, $this->request->getData());
            if ($this->NewHoroscopes->save($newHoroscope)) {
                //$this->Flash->success(__('The new horoscope has been saved.'));
                $this->Flash->success(__('Has been saved.'));

				$this->session->write('Layout.' . $this->controller . '.LastId', $newHoroscope->id);
	
                //return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> 1,
						'sort'		=> 'id',
						'direction'	=> 'desc',
					],
					'#' => $newHoroscope->id	// Az állandó header miatt takarásban van még. Majd...
				]);

                return $this->redirect(['action' => 'index']);
            }
            //$this->Flash->error(__('The new horoscope could not be saved. Please, try again.'));
			$this->Flash->error(__('Could not be saved. Please, try again.'));
        }
        $this->set(compact('newHoroscope'));
    }


    /**
     * Edit method
     *
     * @param string|null $id New Horoscope id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->set('title', __('NewHoroscope'));
        $newHoroscope = $this->NewHoroscopes->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

        if ($this->request->is(['patch', 'post', 'put'])) {
			//debug($this->request->getData()); //die();
            $newHoroscope = $this->NewHoroscopes->patchEntity($newHoroscope, $this->request->getData());
            //debug($newHoroscope); //die();
			if ($this->NewHoroscopes->save($newHoroscope)) {
                //$this->Flash->success(__('The new horoscope has been saved.'));
                $this->Flash->success(__('Has been saved.'));
				
				//return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> (isset($this->paging['NewHoroscopes']['page'])) ? $this->paging['NewHoroscopes']['page'] : 1, 		// or 1
						'sort'		=> (isset($this->paging['NewHoroscopes']['sort'])) ? $this->paging['NewHoroscopes']['sort'] : 'created', 
						'direction'	=> (isset($this->paging['NewHoroscopes']['direction'])) ? $this->paging['NewHoroscopes']['direction'] : 'desc',
					],
					'#' => $id
				]);
				
            }
            //$this->Flash->error(__('The new horoscope could not be saved. Please, try again.'));
            $this->Flash->error(__('Could not be saved. Please, try again.'));
        }

		$name = $newHoroscope->name;

        $this->set(compact('newHoroscope', 'id', 'name'));
    }


    /**
     * Delete method
     *
     * @param string|null $id New Horoscope id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $newHoroscope = $this->NewHoroscopes->get($id);
        if ($this->NewHoroscopes->delete($newHoroscope)) {
            //$this->Flash->success(__('The new horoscope has been deleted.'));
            $this->Flash->success(__('Has been deleted.'));
        } else {
            //$this->Flash->error(__('The new horoscope could not be deleted. Please, try again.'));
            $this->Flash->error(__('Could not be deleted. Please, try again.'));
        }

        //return $this->redirect(['action' => 'index']);
		return $this->redirect([
			'controller' => $this->controller, 
			'action' => 'index', 
			'?' => [
				'page'		=> $this->paging['NewHoroscopes']['page'], 
				'sort'		=> $this->paging['NewHoroscopes']['sort'], 
				'direction'	=> $this->paging['NewHoroscopes']['direction'],
			]
		]);
		
    }

}

