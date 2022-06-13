<?php
// Baked at 2022.03.11. 14:39:37
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;

/**
 * FiveLotteries Controller
 *
 * @property \App\Model\Table\FiveLotteriesTable $FiveLotteries
 * @method \App\Model\Entity\FiveLottery[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class FiveLotteriesController extends AppController
{

    /**
     * Initialize controller
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
		$this->set('title', __('FiveLotteries'));
		
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
		$fiveLotteries = null;
		
		$this->set('title', __('FiveLotteries'));

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
				//'FiveLotteries.name' 		=> 1,
				//'FiveLotteries.visible' 		=> 1,
				//'FiveLotteries.created >= ' 	=> new \DateTime('-10 days'),
				//'FiveLotteries.modified >= '	=> new \DateTime('-10 days'),
			],
			/*
			// Nem tanácsos az order-t itt használni, mert pl az edit után az utolsó  ordert ugyan beálíltja, de
			// kiegészíti ezzel s így az utoljára mentett rekord nem lesz megtalálható az X-edik oldalon, mert az az elsőre kerül.
			// A felhasználó állítson be rendezettséget magának! Kivételes esetek persze lehetnek!
			*/
			'order' => [
				//'FiveLotteries.id' 			=> 'desc',
				//'FiveLotteries.name' 		=> 'asc',
				//'FiveLotteries.visible' 		=> 'desc',
				//'FiveLotteries.pos' 			=> 'desc',
				//'FiveLotteries.rank' 		=> 'asc',
				//'FiveLotteries.created' 		=> 'desc',
				//'FiveLotteries.modified' 	=> 'desc',
			],
			'limit' => $this->config['index_number_of_rows'],
			'maxLimit' => $this->config['index_number_of_rows'],
			//'sortableFields' => ['id', 'name', 'created', '...'],
			//'paramType' => 'querystring',
			//'fields' => ['FiveLotteries.id', 'FiveLotteries.name', ...],
			//'finder' => 'published',
        ];

		//$this->paging = $this->session->read('Layout.' . $this->controller . '.Paging');

		if( $this->paging === null){
			$this->paginate['order'] = [
				//'FiveLotteries.id' 			=> 'desc',
				//'FiveLotteries.name' 		=> 'asc',
				//'FiveLotteries.visible' 		=> 'desc',
				//'FiveLotteries.pos' 			=> 'desc',
				//'FiveLotteries.rank' 		=> 'asc',
				//'FiveLotteries.created' 		=> 'desc',
				//'FiveLotteries.modified' 	=> 'desc',
			];
		}else{
			if($this->request->getQuery('sort') === null && $this->request->getQuery('direction') === null){
				$this->paginate['order'] = [
					// If not in URL-ben, then come from sessinon...
					$this->paging['FiveLotteries']['sort'] => $this->paging['FiveLotteries']['direction']	
				];
			}
		}

		if($this->request->getQuery('page') === null && !isset($this->paging['FiveLotteries']['page']) ){
			$this->paginate['page'] = 1;
		}else{
			$this->paginate['page'] = (isset($this->paging['FiveLotteries']['page'])) ? $this->paging['FiveLotteries']['page'] : 1;
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
						//	'page'		=> $this->paging['FiveLotteries']['page'], 	// Vagy 1
						//	'sort'		=> $this->paging['FiveLotteries']['sort'], 
						//	'direction'	=> $this->paging['FiveLotteries']['direction'],
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
			//$this->paginate['conditions'] = ['FiveLotteries.name LIKE' => $q ];
			$this->paginate['conditions'][] = [
				'OR' => [
					['FiveLotteries.name LIKE' => $search['s'] ],
					//['FiveLotteries.title LIKE' => $search['s'] ], // ... just add more fields
				]
			];
			
		}
		// -- /.Filter --
		
		try {
			$fiveLotteries = $this->paginate($this->FiveLotteries);
		} catch (NotFoundException $e) {
			$paging = $this->request->getAttribute('paging');
			if($paging['FiveLotteries']['prevPage'] !== null && $paging['FiveLotteries']['prevPage']){
				if($paging['FiveLotteries']['page'] !== null && $paging['FiveLotteries']['page'] > 0){
					return $this->redirect([
						'controller' => $this->controller, 
						'action' => 'index', 
						'?' => [
							'page'		=> 1,	//$this->paging['FiveLotteries']['page'],
							'sort'		=> $this->paging['FiveLotteries']['sort'],
							'direction'	=> $this->paging['FiveLotteries']['direction'],
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
		$this->set(compact('fiveLotteries'));
		
	}


    /**
     * View method
     *
     * @param string|null $id Five Lottery id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->set('title', __('FiveLottery'));
		
        $fiveLottery = $this->FiveLotteries->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

		$name = $fiveLottery->name;

        $this->set(compact('fiveLottery', 'id', 'name'));
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->set('title', __('FiveLottery'));
        $fiveLottery = $this->FiveLotteries->newEmptyEntity();
        if ($this->request->is('post')) {
            $fiveLottery = $this->FiveLotteries->patchEntity($fiveLottery, $this->request->getData());
            if ($this->FiveLotteries->save($fiveLottery)) {
                //$this->Flash->success(__('The five lottery has been saved.'));
                $this->Flash->success(__('Has been saved.'));

				$this->session->write('Layout.' . $this->controller . '.LastId', $fiveLottery->id);
	
                //return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> 1,
						'sort'		=> 'id',
						'direction'	=> 'desc',
					],
					'#' => $fiveLottery->id	// Az állandó header miatt takarásban van még. Majd...
				]);

                return $this->redirect(['action' => 'index']);
            }
            //$this->Flash->error(__('The five lottery could not be saved. Please, try again.'));
			$this->Flash->error(__('Could not be saved. Please, try again.'));
        }
        $this->set(compact('fiveLottery'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Five Lottery id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->set('title', __('FiveLottery'));
        $fiveLottery = $this->FiveLotteries->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

        if ($this->request->is(['patch', 'post', 'put'])) {
			//debug($this->request->getData()); //die();
            $fiveLottery = $this->FiveLotteries->patchEntity($fiveLottery, $this->request->getData());
            //debug($fiveLottery); //die();
			if ($this->FiveLotteries->save($fiveLottery)) {
                //$this->Flash->success(__('The five lottery has been saved.'));
                $this->Flash->success(__('Has been saved.'));
				
				//return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> (isset($this->paging['FiveLotteries']['page'])) ? $this->paging['FiveLotteries']['page'] : 1, 		// or 1
						'sort'		=> (isset($this->paging['FiveLotteries']['sort'])) ? $this->paging['FiveLotteries']['sort'] : 'created', 
						'direction'	=> (isset($this->paging['FiveLotteries']['direction'])) ? $this->paging['FiveLotteries']['direction'] : 'desc',
					],
					'#' => $id
				]);
				
            }
            //$this->Flash->error(__('The five lottery could not be saved. Please, try again.'));
            $this->Flash->error(__('Could not be saved. Please, try again.'));
        }

		$name = $fiveLottery->name;

        $this->set(compact('fiveLottery', 'id', 'name'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Five Lottery id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $fiveLottery = $this->FiveLotteries->get($id);
        if ($this->FiveLotteries->delete($fiveLottery)) {
            //$this->Flash->success(__('The five lottery has been deleted.'));
            $this->Flash->success(__('Has been deleted.'));
        } else {
            //$this->Flash->error(__('The five lottery could not be deleted. Please, try again.'));
            $this->Flash->error(__('Could not be deleted. Please, try again.'));
        }

        //return $this->redirect(['action' => 'index']);
		return $this->redirect([
			'controller' => $this->controller, 
			'action' => 'index', 
			'?' => [
				'page'		=> $this->paging['FiveLotteries']['page'], 
				'sort'		=> $this->paging['FiveLotteries']['sort'], 
				'direction'	=> $this->paging['FiveLotteries']['direction'],
			]
		]);
		
    }

}

