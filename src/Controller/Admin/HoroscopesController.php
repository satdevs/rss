<?php
// Baked at 2022.03.17. 09:04:08
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;

/**
 * Horoscopes Controller
 *
 * @property \App\Model\Table\HoroscopesTable $Horoscopes
 * @method \App\Model\Entity\Horoscope[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class HoroscopesController extends AppController
{

    /**
     * Initialize controller
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
		$this->set('title', __('Horoscopes'));
		
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
		$horoscopes = null;
		
		$this->set('title', __('Horoscopes'));

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
				//'Horoscopes.name' 		=> 1,
				//'Horoscopes.visible' 		=> 1,
				//'Horoscopes.created >= ' 	=> new \DateTime('-10 days'),
				//'Horoscopes.modified >= '	=> new \DateTime('-10 days'),
			],
			/*
			// Nem tanácsos az order-t itt használni, mert pl az edit után az utolsó  ordert ugyan beálíltja, de
			// kiegészíti ezzel s így az utoljára mentett rekord nem lesz megtalálható az X-edik oldalon, mert az az elsőre kerül.
			// A felhasználó állítson be rendezettséget magának! Kivételes esetek persze lehetnek!
			*/
			'order' => [
				'Horoscopes.id' 			=> 'desc',
				//'Horoscopes.name' 		=> 'asc',
				//'Horoscopes.visible' 		=> 'desc',
				//'Horoscopes.pos' 			=> 'desc',
				//'Horoscopes.rank' 		=> 'asc',
				//'Horoscopes.created' 		=> 'desc',
				//'Horoscopes.modified' 	=> 'desc',
			],
			'limit' => $this->config['index_number_of_rows'],
			'maxLimit' => $this->config['index_number_of_rows'],
			//'sortableFields' => ['id', 'name', 'created', '...'],
			//'paramType' => 'querystring',
			//'fields' => ['Horoscopes.id', 'Horoscopes.name', ...],
			//'finder' => 'published',
        ];

		//$this->paging = $this->session->read('Layout.' . $this->controller . '.Paging');

		if( $this->paging === null){
			$this->paginate['order'] = [
				//'Horoscopes.id' 			=> 'desc',
				//'Horoscopes.name' 		=> 'asc',
				//'Horoscopes.visible' 		=> 'desc',
				//'Horoscopes.pos' 			=> 'desc',
				//'Horoscopes.rank' 		=> 'asc',
				//'Horoscopes.created' 		=> 'desc',
				//'Horoscopes.modified' 	=> 'desc',
			];
		}else{
			if($this->request->getQuery('sort') === null && $this->request->getQuery('direction') === null){
				$this->paginate['order'] = [
					// If not in URL-ben, then come from sessinon...
					$this->paging['Horoscopes']['sort'] => $this->paging['Horoscopes']['direction']	
				];
			}
		}

		if($this->request->getQuery('page') === null && !isset($this->paging['Horoscopes']['page']) ){
			$this->paginate['page'] = 1;
		}else{
			$this->paginate['page'] = (isset($this->paging['Horoscopes']['page'])) ? $this->paging['Horoscopes']['page'] : 1;
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
						//	'page'		=> $this->paging['Horoscopes']['page'], 	// Vagy 1
						//	'sort'		=> $this->paging['Horoscopes']['sort'], 
						//	'direction'	=> $this->paging['Horoscopes']['direction'],
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
			//$this->paginate['conditions'] = ['Horoscopes.name LIKE' => $q ];
			$this->paginate['conditions'][] = [
				'OR' => [
					['Horoscopes.name LIKE' => $search['s'] ],
					//['Horoscopes.title LIKE' => $search['s'] ], // ... just add more fields
				]
			];
			
		}
		// -- /.Filter --
		
		try {
			$horoscopes = $this->paginate($this->Horoscopes);
		} catch (NotFoundException $e) {
			$paging = $this->request->getAttribute('paging');
			if($paging['Horoscopes']['prevPage'] !== null && $paging['Horoscopes']['prevPage']){
				if($paging['Horoscopes']['page'] !== null && $paging['Horoscopes']['page'] > 0){
					return $this->redirect([
						'controller' => $this->controller, 
						'action' => 'index', 
						'?' => [
							'page'		=> 1,	//$this->paging['Horoscopes']['page'],
							'sort'		=> $this->paging['Horoscopes']['sort'],
							'direction'	=> $this->paging['Horoscopes']['direction'],
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
		$this->set(compact('horoscopes'));
		
	}


    /**
     * View method
     *
     * @param string|null $id Horoscope id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->set('title', __('Horoscope'));
		
        $horoscope = $this->Horoscopes->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

		$name = $horoscope->name;

        $this->set(compact('horoscope', 'id', 'name'));
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->set('title', __('Horoscope'));
        $horoscope = $this->Horoscopes->newEmptyEntity();
        if ($this->request->is('post')) {
            $horoscope = $this->Horoscopes->patchEntity($horoscope, $this->request->getData());
            if ($this->Horoscopes->save($horoscope)) {
                //$this->Flash->success(__('The horoscope has been saved.'));
                $this->Flash->success(__('Has been saved.'));

				$this->session->write('Layout.' . $this->controller . '.LastId', $horoscope->id);
	
                //return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> 1,
						'sort'		=> 'id',
						'direction'	=> 'desc',
					],
					'#' => $horoscope->id	// Az állandó header miatt takarásban van még. Majd...
				]);

                return $this->redirect(['action' => 'index']);
            }
            //$this->Flash->error(__('The horoscope could not be saved. Please, try again.'));
			$this->Flash->error(__('Could not be saved. Please, try again.'));
        }
        $this->set(compact('horoscope'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Horoscope id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->set('title', __('Horoscope'));
        $horoscope = $this->Horoscopes->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

        if ($this->request->is(['patch', 'post', 'put'])) {
			//debug($this->request->getData()); //die();
            $horoscope = $this->Horoscopes->patchEntity($horoscope, $this->request->getData());
            //debug($horoscope); //die();
			if ($this->Horoscopes->save($horoscope)) {
                //$this->Flash->success(__('The horoscope has been saved.'));
                $this->Flash->success(__('Has been saved.'));
				
				//return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> (isset($this->paging['Horoscopes']['page'])) ? $this->paging['Horoscopes']['page'] : 1, 		// or 1
						'sort'		=> (isset($this->paging['Horoscopes']['sort'])) ? $this->paging['Horoscopes']['sort'] : 'created', 
						'direction'	=> (isset($this->paging['Horoscopes']['direction'])) ? $this->paging['Horoscopes']['direction'] : 'desc',
					],
					'#' => $id
				]);
				
            }
            //$this->Flash->error(__('The horoscope could not be saved. Please, try again.'));
            $this->Flash->error(__('Could not be saved. Please, try again.'));
        }

		$name = $horoscope->name;

        $this->set(compact('horoscope', 'id', 'name'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Horoscope id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $horoscope = $this->Horoscopes->get($id);
        if ($this->Horoscopes->delete($horoscope)) {
            //$this->Flash->success(__('The horoscope has been deleted.'));
            $this->Flash->success(__('Has been deleted.'));
        } else {
            //$this->Flash->error(__('The horoscope could not be deleted. Please, try again.'));
            $this->Flash->error(__('Could not be deleted. Please, try again.'));
        }

        //return $this->redirect(['action' => 'index']);
		return $this->redirect([
			'controller' => $this->controller, 
			'action' => 'index', 
			'?' => [
				'page'		=> $this->paging['Horoscopes']['page'], 
				'sort'		=> $this->paging['Horoscopes']['sort'], 
				'direction'	=> $this->paging['Horoscopes']['direction'],
			]
		]);
		
    }

}

