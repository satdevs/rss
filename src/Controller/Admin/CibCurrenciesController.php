<?php
// Baked at 2022.03.11. 13:20:21
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;

/**
 * CibCurrencies Controller
 *
 * @property \App\Model\Table\CibCurrenciesTable $CibCurrencies
 * @method \App\Model\Entity\CibCurrency[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CibCurrenciesController extends AppController
{

    /**
     * Initialize controller
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
		$this->set('title', __('CibCurrencies'));
		
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
		$cibCurrencies = null;
		
		$this->set('title', __('CibCurrencies'));

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
				//'CibCurrencies.name' 		=> 1,
				//'CibCurrencies.visible' 		=> 1,
				//'CibCurrencies.created >= ' 	=> new \DateTime('-10 days'),
				//'CibCurrencies.modified >= '	=> new \DateTime('-10 days'),
			],
			/*
			// Nem tanácsos az order-t itt használni, mert pl az edit után az utolsó  ordert ugyan beálíltja, de
			// kiegészíti ezzel s így az utoljára mentett rekord nem lesz megtalálható az X-edik oldalon, mert az az elsőre kerül.
			// A felhasználó állítson be rendezettséget magának! Kivételes esetek persze lehetnek!
			*/
			'order' => [
				//'CibCurrencies.id' 			=> 'desc',
				//'CibCurrencies.name' 		=> 'asc',
				//'CibCurrencies.visible' 		=> 'desc',
				//'CibCurrencies.pos' 			=> 'desc',
				//'CibCurrencies.rank' 		=> 'asc',
				//'CibCurrencies.created' 		=> 'desc',
				//'CibCurrencies.modified' 	=> 'desc',
			],
			'limit' => $this->config['index_number_of_rows'],
			'maxLimit' => $this->config['index_number_of_rows'],
			//'sortableFields' => ['id', 'name', 'created', '...'],
			//'paramType' => 'querystring',
			//'fields' => ['CibCurrencies.id', 'CibCurrencies.name', ...],
			//'finder' => 'published',
        ];

		//$this->paging = $this->session->read('Layout.' . $this->controller . '.Paging');

		if( $this->paging === null){
			$this->paginate['order'] = [
				//'CibCurrencies.id' 			=> 'desc',
				//'CibCurrencies.name' 		=> 'asc',
				//'CibCurrencies.visible' 		=> 'desc',
				//'CibCurrencies.pos' 			=> 'desc',
				//'CibCurrencies.rank' 		=> 'asc',
				//'CibCurrencies.created' 		=> 'desc',
				//'CibCurrencies.modified' 	=> 'desc',
			];
		}else{
			if($this->request->getQuery('sort') === null && $this->request->getQuery('direction') === null){
				$this->paginate['order'] = [
					// If not in URL-ben, then come from sessinon...
					$this->paging['CibCurrencies']['sort'] => $this->paging['CibCurrencies']['direction']	
				];
			}
		}

		if($this->request->getQuery('page') === null && !isset($this->paging['CibCurrencies']['page']) ){
			$this->paginate['page'] = 1;
		}else{
			$this->paginate['page'] = (isset($this->paging['CibCurrencies']['page'])) ? $this->paging['CibCurrencies']['page'] : 1;
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
						//	'page'		=> $this->paging['CibCurrencies']['page'], 	// Vagy 1
						//	'sort'		=> $this->paging['CibCurrencies']['sort'], 
						//	'direction'	=> $this->paging['CibCurrencies']['direction'],
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
			//$this->paginate['conditions'] = ['CibCurrencies.name LIKE' => $q ];
			$this->paginate['conditions'][] = [
				'OR' => [
					['CibCurrencies.name LIKE' => $search['s'] ],
					//['CibCurrencies.title LIKE' => $search['s'] ], // ... just add more fields
				]
			];
			
		}
		// -- /.Filter --
		
		try {
			$cibCurrencies = $this->paginate($this->CibCurrencies);
		} catch (NotFoundException $e) {
			$paging = $this->request->getAttribute('paging');
			if($paging['CibCurrencies']['prevPage'] !== null && $paging['CibCurrencies']['prevPage']){
				if($paging['CibCurrencies']['page'] !== null && $paging['CibCurrencies']['page'] > 0){
					return $this->redirect([
						'controller' => $this->controller, 
						'action' => 'index', 
						'?' => [
							'page'		=> 1,	//$this->paging['CibCurrencies']['page'],
							'sort'		=> $this->paging['CibCurrencies']['sort'],
							'direction'	=> $this->paging['CibCurrencies']['direction'],
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
		$this->set(compact('cibCurrencies'));
		
	}


    /**
     * View method
     *
     * @param string|null $id Cib Currency id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->set('title', __('CibCurrency'));
		
        $cibCurrency = $this->CibCurrencies->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

		$name = $cibCurrency->name;

        $this->set(compact('cibCurrency', 'id', 'name'));
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->set('title', __('CibCurrency'));
        $cibCurrency = $this->CibCurrencies->newEmptyEntity();
        if ($this->request->is('post')) {
            $cibCurrency = $this->CibCurrencies->patchEntity($cibCurrency, $this->request->getData());
            if ($this->CibCurrencies->save($cibCurrency)) {
                //$this->Flash->success(__('The cib currency has been saved.'));
                $this->Flash->success(__('Has been saved.'));

				$this->session->write('Layout.' . $this->controller . '.LastId', $cibCurrency->id);
	
                //return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> 1,
						'sort'		=> 'id',
						'direction'	=> 'desc',
					],
					'#' => $cibCurrency->id	// Az állandó header miatt takarásban van még. Majd...
				]);

                return $this->redirect(['action' => 'index']);
            }
            //$this->Flash->error(__('The cib currency could not be saved. Please, try again.'));
			$this->Flash->error(__('Could not be saved. Please, try again.'));
        }
        $this->set(compact('cibCurrency'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Cib Currency id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->set('title', __('CibCurrency'));
        $cibCurrency = $this->CibCurrencies->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

        if ($this->request->is(['patch', 'post', 'put'])) {
			//debug($this->request->getData()); //die();
            $cibCurrency = $this->CibCurrencies->patchEntity($cibCurrency, $this->request->getData());
            //debug($cibCurrency); //die();
			if ($this->CibCurrencies->save($cibCurrency)) {
                //$this->Flash->success(__('The cib currency has been saved.'));
                $this->Flash->success(__('Has been saved.'));
				
				//return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> (isset($this->paging['CibCurrencies']['page'])) ? $this->paging['CibCurrencies']['page'] : 1, 		// or 1
						'sort'		=> (isset($this->paging['CibCurrencies']['sort'])) ? $this->paging['CibCurrencies']['sort'] : 'created', 
						'direction'	=> (isset($this->paging['CibCurrencies']['direction'])) ? $this->paging['CibCurrencies']['direction'] : 'desc',
					],
					'#' => $id
				]);
				
            }
            //$this->Flash->error(__('The cib currency could not be saved. Please, try again.'));
            $this->Flash->error(__('Could not be saved. Please, try again.'));
        }

		$name = $cibCurrency->name;

        $this->set(compact('cibCurrency', 'id', 'name'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Cib Currency id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cibCurrency = $this->CibCurrencies->get($id);
        if ($this->CibCurrencies->delete($cibCurrency)) {
            //$this->Flash->success(__('The cib currency has been deleted.'));
            $this->Flash->success(__('Has been deleted.'));
        } else {
            //$this->Flash->error(__('The cib currency could not be deleted. Please, try again.'));
            $this->Flash->error(__('Could not be deleted. Please, try again.'));
        }

        //return $this->redirect(['action' => 'index']);
		return $this->redirect([
			'controller' => $this->controller, 
			'action' => 'index', 
			'?' => [
				'page'		=> $this->paging['CibCurrencies']['page'], 
				'sort'		=> $this->paging['CibCurrencies']['sort'], 
				'direction'	=> $this->paging['CibCurrencies']['direction'],
			]
		]);
		
    }

}

