<?php
// Baked at 2022.03.25. 13:36:53
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;

/**
 * CurrencyLogs Controller
 *
 * @property \App\Model\Table\CurrencyLogsTable $CurrencyLogs
 * @method \App\Model\Entity\CurrencyLog[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CurrencyLogsController extends AppController
{

    /**
     * Initialize controller
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
		$this->set('title', __('CurrencyLogs'));
		
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
		$currencyLogs = null;
		
		$this->set('title', __('CurrencyLogs'));

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
				//'CurrencyLogs.name' 		=> 1,
				//'CurrencyLogs.visible' 		=> 1,
				//'CurrencyLogs.created >= ' 	=> new \DateTime('-10 days'),
				//'CurrencyLogs.modified >= '	=> new \DateTime('-10 days'),
			],
			/*
			// Nem tanácsos az order-t itt használni, mert pl az edit után az utolsó  ordert ugyan beálíltja, de
			// kiegészíti ezzel s így az utoljára mentett rekord nem lesz megtalálható az X-edik oldalon, mert az az elsőre kerül.
			// A felhasználó állítson be rendezettséget magának! Kivételes esetek persze lehetnek!
			*/
			'order' => [
				//'CurrencyLogs.id' 			=> 'desc',
				//'CurrencyLogs.name' 		=> 'asc',
				//'CurrencyLogs.visible' 		=> 'desc',
				//'CurrencyLogs.pos' 			=> 'desc',
				//'CurrencyLogs.rank' 		=> 'asc',
				//'CurrencyLogs.created' 		=> 'desc',
				//'CurrencyLogs.modified' 	=> 'desc',
			],
			'limit' => $this->config['index_number_of_rows'],
			'maxLimit' => $this->config['index_number_of_rows'],
			//'sortableFields' => ['id', 'name', 'created', '...'],
			//'paramType' => 'querystring',
			//'fields' => ['CurrencyLogs.id', 'CurrencyLogs.name', ...],
			//'finder' => 'published',
        ];

		//$this->paging = $this->session->read('Layout.' . $this->controller . '.Paging');

		if( $this->paging === null){
			$this->paginate['order'] = [
				//'CurrencyLogs.id' 			=> 'desc',
				//'CurrencyLogs.name' 		=> 'asc',
				//'CurrencyLogs.visible' 		=> 'desc',
				//'CurrencyLogs.pos' 			=> 'desc',
				//'CurrencyLogs.rank' 		=> 'asc',
				//'CurrencyLogs.created' 		=> 'desc',
				//'CurrencyLogs.modified' 	=> 'desc',
			];
		}else{
			if($this->request->getQuery('sort') === null && $this->request->getQuery('direction') === null){
				$this->paginate['order'] = [
					// If not in URL-ben, then come from sessinon...
					$this->paging['CurrencyLogs']['sort'] => $this->paging['CurrencyLogs']['direction']	
				];
			}
		}

		if($this->request->getQuery('page') === null && !isset($this->paging['CurrencyLogs']['page']) ){
			$this->paginate['page'] = 1;
		}else{
			$this->paginate['page'] = (isset($this->paging['CurrencyLogs']['page'])) ? $this->paging['CurrencyLogs']['page'] : 1;
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
						//	'page'		=> $this->paging['CurrencyLogs']['page'], 	// Vagy 1
						//	'sort'		=> $this->paging['CurrencyLogs']['sort'], 
						//	'direction'	=> $this->paging['CurrencyLogs']['direction'],
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
			//$this->paginate['conditions'] = ['CurrencyLogs.name LIKE' => $q ];
			$this->paginate['conditions'][] = [
				'OR' => [
					['CurrencyLogs.name LIKE' => $search['s'] ],
					//['CurrencyLogs.title LIKE' => $search['s'] ], // ... just add more fields
				]
			];
			
		}
		// -- /.Filter --
		
		try {
			$currencyLogs = $this->paginate($this->CurrencyLogs);
		} catch (NotFoundException $e) {
			$paging = $this->request->getAttribute('paging');
			if($paging['CurrencyLogs']['prevPage'] !== null && $paging['CurrencyLogs']['prevPage']){
				if($paging['CurrencyLogs']['page'] !== null && $paging['CurrencyLogs']['page'] > 0){
					return $this->redirect([
						'controller' => $this->controller, 
						'action' => 'index', 
						'?' => [
							'page'		=> 1,	//$this->paging['CurrencyLogs']['page'],
							'sort'		=> $this->paging['CurrencyLogs']['sort'],
							'direction'	=> $this->paging['CurrencyLogs']['direction'],
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
		$this->set(compact('currencyLogs'));
		
	}


    /**
     * View method
     *
     * @param string|null $id Currency Log id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->set('title', __('CurrencyLog'));
		
        $currencyLog = $this->CurrencyLogs->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

		$name = $currencyLog->name;

        $this->set(compact('currencyLog', 'id', 'name'));
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->set('title', __('CurrencyLog'));
        $currencyLog = $this->CurrencyLogs->newEmptyEntity();
        if ($this->request->is('post')) {
            $currencyLog = $this->CurrencyLogs->patchEntity($currencyLog, $this->request->getData());
            if ($this->CurrencyLogs->save($currencyLog)) {
                //$this->Flash->success(__('The currency log has been saved.'));
                $this->Flash->success(__('Has been saved.'));

				$this->session->write('Layout.' . $this->controller . '.LastId', $currencyLog->id);
	
                //return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> 1,
						'sort'		=> 'id',
						'direction'	=> 'desc',
					],
					'#' => $currencyLog->id	// Az állandó header miatt takarásban van még. Majd...
				]);

                return $this->redirect(['action' => 'index']);
            }
            //$this->Flash->error(__('The currency log could not be saved. Please, try again.'));
			$this->Flash->error(__('Could not be saved. Please, try again.'));
        }
        $this->set(compact('currencyLog'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Currency Log id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->set('title', __('CurrencyLog'));
        $currencyLog = $this->CurrencyLogs->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

        if ($this->request->is(['patch', 'post', 'put'])) {
			//debug($this->request->getData()); //die();
            $currencyLog = $this->CurrencyLogs->patchEntity($currencyLog, $this->request->getData());
            //debug($currencyLog); //die();
			if ($this->CurrencyLogs->save($currencyLog)) {
                //$this->Flash->success(__('The currency log has been saved.'));
                $this->Flash->success(__('Has been saved.'));
				
				//return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> (isset($this->paging['CurrencyLogs']['page'])) ? $this->paging['CurrencyLogs']['page'] : 1, 		// or 1
						'sort'		=> (isset($this->paging['CurrencyLogs']['sort'])) ? $this->paging['CurrencyLogs']['sort'] : 'created', 
						'direction'	=> (isset($this->paging['CurrencyLogs']['direction'])) ? $this->paging['CurrencyLogs']['direction'] : 'desc',
					],
					'#' => $id
				]);
				
            }
            //$this->Flash->error(__('The currency log could not be saved. Please, try again.'));
            $this->Flash->error(__('Could not be saved. Please, try again.'));
        }

		$name = $currencyLog->name;

        $this->set(compact('currencyLog', 'id', 'name'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Currency Log id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $currencyLog = $this->CurrencyLogs->get($id);
        if ($this->CurrencyLogs->delete($currencyLog)) {
            //$this->Flash->success(__('The currency log has been deleted.'));
            $this->Flash->success(__('Has been deleted.'));
        } else {
            //$this->Flash->error(__('The currency log could not be deleted. Please, try again.'));
            $this->Flash->error(__('Could not be deleted. Please, try again.'));
        }

        //return $this->redirect(['action' => 'index']);
		return $this->redirect([
			'controller' => $this->controller, 
			'action' => 'index', 
			'?' => [
				'page'		=> $this->paging['CurrencyLogs']['page'], 
				'sort'		=> $this->paging['CurrencyLogs']['sort'], 
				'direction'	=> $this->paging['CurrencyLogs']['direction'],
			]
		]);
		
    }

}

