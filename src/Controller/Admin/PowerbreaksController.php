<?php
// Baked at 2022.03.24. 10:41:11
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;

/**
 * Powerbreaks Controller
 *
 * @property \App\Model\Table\PowerbreaksTable $Powerbreaks
 * @method \App\Model\Entity\Powerbreak[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PowerbreaksController extends AppController
{

    /**
     * Initialize controller
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
		$this->set('title', __('Powerbreaks'));
		
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
		$powerbreaks = null;
		
		$this->set('title', __('Powerbreaks'));

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
				//'Powerbreaks.name' 		=> 1,
				//'Powerbreaks.visible' 		=> 1,
				//'Powerbreaks.created >= ' 	=> new \DateTime('-10 days'),
				//'Powerbreaks.modified >= '	=> new \DateTime('-10 days'),
			],
			/*
			// Nem tanácsos az order-t itt használni, mert pl az edit után az utolsó  ordert ugyan beálíltja, de
			// kiegészíti ezzel s így az utoljára mentett rekord nem lesz megtalálható az X-edik oldalon, mert az az elsőre kerül.
			// A felhasználó állítson be rendezettséget magának! Kivételes esetek persze lehetnek!
			*/
			'order' => [
				//'Powerbreaks.id' 			=> 'desc',
				//'Powerbreaks.name' 		=> 'asc',
				//'Powerbreaks.visible' 		=> 'desc',
				//'Powerbreaks.pos' 			=> 'desc',
				//'Powerbreaks.rank' 		=> 'asc',
				//'Powerbreaks.created' 		=> 'desc',
				//'Powerbreaks.modified' 	=> 'desc',
			],
			'limit' => $this->config['index_number_of_rows'],
			'maxLimit' => $this->config['index_number_of_rows'],
			//'sortableFields' => ['id', 'name', 'created', '...'],
			//'paramType' => 'querystring',
			//'fields' => ['Powerbreaks.id', 'Powerbreaks.name', ...],
			//'finder' => 'published',
        ];

		//$this->paging = $this->session->read('Layout.' . $this->controller . '.Paging');

		if( $this->paging === null){
			$this->paginate['order'] = [
				//'Powerbreaks.id' 			=> 'desc',
				//'Powerbreaks.name' 		=> 'asc',
				//'Powerbreaks.visible' 		=> 'desc',
				//'Powerbreaks.pos' 			=> 'desc',
				//'Powerbreaks.rank' 		=> 'asc',
				//'Powerbreaks.created' 		=> 'desc',
				//'Powerbreaks.modified' 	=> 'desc',
			];
		}else{
			if($this->request->getQuery('sort') === null && $this->request->getQuery('direction') === null){
				$this->paginate['order'] = [
					// If not in URL-ben, then come from sessinon...
					$this->paging['Powerbreaks']['sort'] => $this->paging['Powerbreaks']['direction']	
				];
			}
		}

		if($this->request->getQuery('page') === null && !isset($this->paging['Powerbreaks']['page']) ){
			$this->paginate['page'] = 1;
		}else{
			$this->paginate['page'] = (isset($this->paging['Powerbreaks']['page'])) ? $this->paging['Powerbreaks']['page'] : 1;
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
						//	'page'		=> $this->paging['Powerbreaks']['page'], 	// Vagy 1
						//	'sort'		=> $this->paging['Powerbreaks']['sort'], 
						//	'direction'	=> $this->paging['Powerbreaks']['direction'],
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
			//$this->paginate['conditions'] = ['Powerbreaks.name LIKE' => $q ];
			$this->paginate['conditions'][] = [
				'OR' => [
					['Powerbreaks.name LIKE' => $search['s'] ],
					//['Powerbreaks.title LIKE' => $search['s'] ], // ... just add more fields
				]
			];
			
		}
		// -- /.Filter --
		
		try {
			$powerbreaks = $this->paginate($this->Powerbreaks);
		} catch (NotFoundException $e) {
			$paging = $this->request->getAttribute('paging');
			if($paging['Powerbreaks']['prevPage'] !== null && $paging['Powerbreaks']['prevPage']){
				if($paging['Powerbreaks']['page'] !== null && $paging['Powerbreaks']['page'] > 0){
					return $this->redirect([
						'controller' => $this->controller, 
						'action' => 'index', 
						'?' => [
							'page'		=> 1,	//$this->paging['Powerbreaks']['page'],
							'sort'		=> $this->paging['Powerbreaks']['sort'],
							'direction'	=> $this->paging['Powerbreaks']['direction'],
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
		$this->set(compact('powerbreaks'));
		
	}


    /**
     * View method
     *
     * @param string|null $id Powerbreak id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->set('title', __('Powerbreak'));
		
        $powerbreak = $this->Powerbreaks->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

		$name = $powerbreak->name;

        $this->set(compact('powerbreak', 'id', 'name'));
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->set('title', __('Powerbreak'));
        $powerbreak = $this->Powerbreaks->newEmptyEntity();
        if ($this->request->is('post')) {
            $powerbreak = $this->Powerbreaks->patchEntity($powerbreak, $this->request->getData());
            if ($this->Powerbreaks->save($powerbreak)) {
                //$this->Flash->success(__('The powerbreak has been saved.'));
                $this->Flash->success(__('Has been saved.'));

				$this->session->write('Layout.' . $this->controller . '.LastId', $powerbreak->id);
	
                //return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> 1,
						'sort'		=> 'id',
						'direction'	=> 'desc',
					],
					'#' => $powerbreak->id	// Az állandó header miatt takarásban van még. Majd...
				]);

                return $this->redirect(['action' => 'index']);
            }
            //$this->Flash->error(__('The powerbreak could not be saved. Please, try again.'));
			$this->Flash->error(__('Could not be saved. Please, try again.'));
        }
        $this->set(compact('powerbreak'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Powerbreak id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->set('title', __('Powerbreak'));
        $powerbreak = $this->Powerbreaks->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

        if ($this->request->is(['patch', 'post', 'put'])) {
			//debug($this->request->getData()); //die();
            $powerbreak = $this->Powerbreaks->patchEntity($powerbreak, $this->request->getData());
            //debug($powerbreak); //die();
			if ($this->Powerbreaks->save($powerbreak)) {
                //$this->Flash->success(__('The powerbreak has been saved.'));
                $this->Flash->success(__('Has been saved.'));
				
				//return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> (isset($this->paging['Powerbreaks']['page'])) ? $this->paging['Powerbreaks']['page'] : 1, 		// or 1
						'sort'		=> (isset($this->paging['Powerbreaks']['sort'])) ? $this->paging['Powerbreaks']['sort'] : 'created', 
						'direction'	=> (isset($this->paging['Powerbreaks']['direction'])) ? $this->paging['Powerbreaks']['direction'] : 'desc',
					],
					'#' => $id
				]);
				
            }
            //$this->Flash->error(__('The powerbreak could not be saved. Please, try again.'));
            $this->Flash->error(__('Could not be saved. Please, try again.'));
        }

		$name = $powerbreak->name;

        $this->set(compact('powerbreak', 'id', 'name'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Powerbreak id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $powerbreak = $this->Powerbreaks->get($id);
        if ($this->Powerbreaks->delete($powerbreak)) {
            //$this->Flash->success(__('The powerbreak has been deleted.'));
            $this->Flash->success(__('Has been deleted.'));
        } else {
            //$this->Flash->error(__('The powerbreak could not be deleted. Please, try again.'));
            $this->Flash->error(__('Could not be deleted. Please, try again.'));
        }

        //return $this->redirect(['action' => 'index']);
		return $this->redirect([
			'controller' => $this->controller, 
			'action' => 'index', 
			'?' => [
				'page'		=> $this->paging['Powerbreaks']['page'], 
				'sort'		=> $this->paging['Powerbreaks']['sort'], 
				'direction'	=> $this->paging['Powerbreaks']['direction'],
			]
		]);
		
    }

}

