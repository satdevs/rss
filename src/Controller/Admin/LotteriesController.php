<?php
// Baked at 2022.03.16. 07:39:32
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;

/**
 * Lotteries Controller
 *
 * @property \App\Model\Table\LotteriesTable $Lotteries
 * @method \App\Model\Entity\Lottery[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class LotteriesController extends AppController
{

    /**
     * Initialize controller
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
		$this->set('title', __('Lotteries'));
		
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
		$lotteries = null;
		
		$this->set('title', __('Lotteries'));

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
				//'Lotteries.name' 		=> 1,
				//'Lotteries.visible' 		=> 1,
				//'Lotteries.created >= ' 	=> new \DateTime('-10 days'),
				//'Lotteries.modified >= '	=> new \DateTime('-10 days'),
			],
			/*
			// Nem tanácsos az order-t itt használni, mert pl az edit után az utolsó  ordert ugyan beálíltja, de
			// kiegészíti ezzel s így az utoljára mentett rekord nem lesz megtalálható az X-edik oldalon, mert az az elsőre kerül.
			// A felhasználó állítson be rendezettséget magának! Kivételes esetek persze lehetnek!
			*/
			'order' => [
				//'Lotteries.id' 			=> 'desc',
				//'Lotteries.name' 		=> 'asc',
				//'Lotteries.visible' 		=> 'desc',
				//'Lotteries.pos' 			=> 'desc',
				//'Lotteries.rank' 		=> 'asc',
				//'Lotteries.created' 		=> 'desc',
				//'Lotteries.modified' 	=> 'desc',
			],
			'limit' => $this->config['index_number_of_rows'],
			'maxLimit' => $this->config['index_number_of_rows'],
			//'sortableFields' => ['id', 'name', 'created', '...'],
			//'paramType' => 'querystring',
			//'fields' => ['Lotteries.id', 'Lotteries.name', ...],
			//'finder' => 'published',
        ];

		//$this->paging = $this->session->read('Layout.' . $this->controller . '.Paging');

		if( $this->paging === null){
			$this->paginate['order'] = [
				//'Lotteries.id' 			=> 'desc',
				//'Lotteries.name' 		=> 'asc',
				//'Lotteries.visible' 		=> 'desc',
				//'Lotteries.pos' 			=> 'desc',
				//'Lotteries.rank' 		=> 'asc',
				//'Lotteries.created' 		=> 'desc',
				//'Lotteries.modified' 	=> 'desc',
			];
		}else{
			if($this->request->getQuery('sort') === null && $this->request->getQuery('direction') === null){
				$this->paginate['order'] = [
					// If not in URL-ben, then come from sessinon...
					$this->paging['Lotteries']['sort'] => $this->paging['Lotteries']['direction']	
				];
			}
		}

		if($this->request->getQuery('page') === null && !isset($this->paging['Lotteries']['page']) ){
			$this->paginate['page'] = 1;
		}else{
			$this->paginate['page'] = (isset($this->paging['Lotteries']['page'])) ? $this->paging['Lotteries']['page'] : 1;
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
						//	'page'		=> $this->paging['Lotteries']['page'], 	// Vagy 1
						//	'sort'		=> $this->paging['Lotteries']['sort'], 
						//	'direction'	=> $this->paging['Lotteries']['direction'],
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
			//$this->paginate['conditions'] = ['Lotteries.name LIKE' => $q ];
			$this->paginate['conditions'][] = [
				'OR' => [
					['Lotteries.name LIKE' => $search['s'] ],
					//['Lotteries.title LIKE' => $search['s'] ], // ... just add more fields
				]
			];
			
		}
		// -- /.Filter --
		
		try {
			$lotteries = $this->paginate($this->Lotteries);
		} catch (NotFoundException $e) {
			$paging = $this->request->getAttribute('paging');
			if($paging['Lotteries']['prevPage'] !== null && $paging['Lotteries']['prevPage']){
				if($paging['Lotteries']['page'] !== null && $paging['Lotteries']['page'] > 0){
					return $this->redirect([
						'controller' => $this->controller, 
						'action' => 'index', 
						'?' => [
							'page'		=> 1,	//$this->paging['Lotteries']['page'],
							'sort'		=> $this->paging['Lotteries']['sort'],
							'direction'	=> $this->paging['Lotteries']['direction'],
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
		$this->set(compact('lotteries'));
		
	}


    /**
     * View method
     *
     * @param string|null $id Lottery id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->set('title', __('Lottery'));
		
        $lottery = $this->Lotteries->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

		$name = $lottery->name;

        $this->set(compact('lottery', 'id', 'name'));
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->set('title', __('Lottery'));
        $lottery = $this->Lotteries->newEmptyEntity();
        if ($this->request->is('post')) {
            $lottery = $this->Lotteries->patchEntity($lottery, $this->request->getData());
            if ($this->Lotteries->save($lottery)) {
                //$this->Flash->success(__('The lottery has been saved.'));
                $this->Flash->success(__('Has been saved.'));

				$this->session->write('Layout.' . $this->controller . '.LastId', $lottery->id);
	
                //return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> 1,
						'sort'		=> 'id',
						'direction'	=> 'desc',
					],
					'#' => $lottery->id	// Az állandó header miatt takarásban van még. Majd...
				]);

                return $this->redirect(['action' => 'index']);
            }
            //$this->Flash->error(__('The lottery could not be saved. Please, try again.'));
			$this->Flash->error(__('Could not be saved. Please, try again.'));
        }
        $this->set(compact('lottery'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Lottery id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->set('title', __('Lottery'));
        $lottery = $this->Lotteries->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

        if ($this->request->is(['patch', 'post', 'put'])) {
			//debug($this->request->getData()); //die();
            $lottery = $this->Lotteries->patchEntity($lottery, $this->request->getData());
            //debug($lottery); //die();
			if ($this->Lotteries->save($lottery)) {
                //$this->Flash->success(__('The lottery has been saved.'));
                $this->Flash->success(__('Has been saved.'));
				
				//return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> (isset($this->paging['Lotteries']['page'])) ? $this->paging['Lotteries']['page'] : 1, 		// or 1
						'sort'		=> (isset($this->paging['Lotteries']['sort'])) ? $this->paging['Lotteries']['sort'] : 'created', 
						'direction'	=> (isset($this->paging['Lotteries']['direction'])) ? $this->paging['Lotteries']['direction'] : 'desc',
					],
					'#' => $id
				]);
				
            }
            //$this->Flash->error(__('The lottery could not be saved. Please, try again.'));
            $this->Flash->error(__('Could not be saved. Please, try again.'));
        }

		$name = $lottery->name;

        $this->set(compact('lottery', 'id', 'name'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Lottery id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $lottery = $this->Lotteries->get($id);
        if ($this->Lotteries->delete($lottery)) {
            //$this->Flash->success(__('The lottery has been deleted.'));
            $this->Flash->success(__('Has been deleted.'));
        } else {
            //$this->Flash->error(__('The lottery could not be deleted. Please, try again.'));
            $this->Flash->error(__('Could not be deleted. Please, try again.'));
        }

        //return $this->redirect(['action' => 'index']);
		return $this->redirect([
			'controller' => $this->controller, 
			'action' => 'index', 
			'?' => [
				'page'		=> $this->paging['Lotteries']['page'], 
				'sort'		=> $this->paging['Lotteries']['sort'], 
				'direction'	=> $this->paging['Lotteries']['direction'],
			]
		]);
		
    }

}

