<?php
// Baked at 2022.03.17. 09:14:45
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;

/**
 * Todays Controller
 *
 * @property \App\Model\Table\TodaysTable $Todays
 * @method \App\Model\Entity\Today[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TodaysController extends AppController
{

    /**
     * Initialize controller
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
		$this->set('title', __('Todays'));
		
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
		$todays = null;
		
		$this->set('title', __('Todays'));

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
				//'Todays.name' 		=> 1,
				//'Todays.visible' 		=> 1,
				'Todays.month' 		=> date("m"),
				'Todays.day' 		=> date("d"),
				//'Todays.created >= ' 	=> new \DateTime('-10 days'),
				//'Todays.modified >= '	=> new \DateTime('-10 days'),
			],
			/*
			// Nem tanácsos az order-t itt használni, mert pl az edit után az utolsó  ordert ugyan beálíltja, de
			// kiegészíti ezzel s így az utoljára mentett rekord nem lesz megtalálható az X-edik oldalon, mert az az elsőre kerül.
			// A felhasználó állítson be rendezettséget magának! Kivételes esetek persze lehetnek!
			*/
			'order' => [
				//'Todays.id' 			=> 'desc',
				//'Todays.name' 		=> 'asc',
				//'Todays.visible' 		=> 'desc',
				//'Todays.pos' 			=> 'desc',
				//'Todays.rank' 		=> 'asc',
				//'Todays.created' 		=> 'desc',
				//'Todays.modified' 	=> 'desc',
			],
			'limit' => $this->config['index_number_of_rows'],
			'maxLimit' => $this->config['index_number_of_rows'],
			//'sortableFields' => ['id', 'name', 'created', '...'],
			//'paramType' => 'querystring',
			//'fields' => ['Todays.id', 'Todays.name', ...],
			//'finder' => 'published',
        ];

		//$this->paging = $this->session->read('Layout.' . $this->controller . '.Paging');

		if( $this->paging === null){
			$this->paginate['order'] = [
				//'Todays.id' 			=> 'desc',
				//'Todays.name' 		=> 'asc',
				//'Todays.visible' 		=> 'desc',
				//'Todays.pos' 			=> 'desc',
				//'Todays.rank' 		=> 'asc',
				//'Todays.created' 		=> 'desc',
				//'Todays.modified' 	=> 'desc',
			];
		}else{
			if($this->request->getQuery('sort') === null && $this->request->getQuery('direction') === null){
				$this->paginate['order'] = [
					// If not in URL-ben, then come from sessinon...
					$this->paging['Todays']['sort'] => $this->paging['Todays']['direction']	
				];
			}
		}

		if($this->request->getQuery('page') === null && !isset($this->paging['Todays']['page']) ){
			$this->paginate['page'] = 1;
		}else{
			$this->paginate['page'] = (isset($this->paging['Todays']['page'])) ? $this->paging['Todays']['page'] : 1;
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
						//	'page'		=> $this->paging['Todays']['page'], 	// Vagy 1
						//	'sort'		=> $this->paging['Todays']['sort'], 
						//	'direction'	=> $this->paging['Todays']['direction'],
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
			//$this->paginate['conditions'] = ['Todays.name LIKE' => $q ];
			$this->paginate['conditions'][] = [
				'OR' => [
					['Todays.name LIKE' => $search['s'] ],
					//['Todays.title LIKE' => $search['s'] ], // ... just add more fields
				]
			];
			
		}
		// -- /.Filter --
		
		try {
			$todays = $this->paginate($this->Todays);
		} catch (NotFoundException $e) {
			$paging = $this->request->getAttribute('paging');
			if($paging['Todays']['prevPage'] !== null && $paging['Todays']['prevPage']){
				if($paging['Todays']['page'] !== null && $paging['Todays']['page'] > 0){
					return $this->redirect([
						'controller' => $this->controller, 
						'action' => 'index', 
						'?' => [
							'page'		=> 1,	//$this->paging['Todays']['page'],
							'sort'		=> $this->paging['Todays']['sort'],
							'direction'	=> $this->paging['Todays']['direction'],
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
		$this->set(compact('todays'));
		
	}


    /**
     * View method
     *
     * @param string|null $id Today id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->set('title', __('Today'));
		
        $today = $this->Todays->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

		$name = $today->name;

        $this->set(compact('today', 'id', 'name'));
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->set('title', __('Today'));
        $today = $this->Todays->newEmptyEntity();
        if ($this->request->is('post')) {
            $today = $this->Todays->patchEntity($today, $this->request->getData());
            if ($this->Todays->save($today)) {
                //$this->Flash->success(__('The today has been saved.'));
                $this->Flash->success(__('Has been saved.'));

				$this->session->write('Layout.' . $this->controller . '.LastId', $today->id);
	
                //return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> 1,
						'sort'		=> 'id',
						'direction'	=> 'desc',
					],
					'#' => $today->id	// Az állandó header miatt takarásban van még. Majd...
				]);

                return $this->redirect(['action' => 'index']);
            }
            //$this->Flash->error(__('The today could not be saved. Please, try again.'));
			$this->Flash->error(__('Could not be saved. Please, try again.'));
        }
        $this->set(compact('today'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Today id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->set('title', __('Today'));
        $today = $this->Todays->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

        if ($this->request->is(['patch', 'post', 'put'])) {
			//debug($this->request->getData()); //die();
            $today = $this->Todays->patchEntity($today, $this->request->getData());
            //debug($today); //die();
			if ($this->Todays->save($today)) {
                //$this->Flash->success(__('The today has been saved.'));
                $this->Flash->success(__('Has been saved.'));
				
				//return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> (isset($this->paging['Todays']['page'])) ? $this->paging['Todays']['page'] : 1, 		// or 1
						'sort'		=> (isset($this->paging['Todays']['sort'])) ? $this->paging['Todays']['sort'] : 'created', 
						'direction'	=> (isset($this->paging['Todays']['direction'])) ? $this->paging['Todays']['direction'] : 'desc',
					],
					'#' => $id
				]);
				
            }
            //$this->Flash->error(__('The today could not be saved. Please, try again.'));
            $this->Flash->error(__('Could not be saved. Please, try again.'));
        }

		$name = $today->name;

        $this->set(compact('today', 'id', 'name'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Today id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $today = $this->Todays->get($id);
        if ($this->Todays->delete($today)) {
            //$this->Flash->success(__('The today has been deleted.'));
            $this->Flash->success(__('Has been deleted.'));
        } else {
            //$this->Flash->error(__('The today could not be deleted. Please, try again.'));
            $this->Flash->error(__('Could not be deleted. Please, try again.'));
        }

        //return $this->redirect(['action' => 'index']);
		return $this->redirect([
			'controller' => $this->controller, 
			'action' => 'index', 
			'?' => [
				'page'		=> $this->paging['Todays']['page'], 
				'sort'		=> $this->paging['Todays']['sort'], 
				'direction'	=> $this->paging['Todays']['direction'],
			]
		]);
		
    }

}

