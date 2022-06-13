<?php
// Baked at 2022.03.17. 09:06:57
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;

/**
 * Namedays Controller
 *
 * @property \App\Model\Table\NamedaysTable $Namedays
 * @method \App\Model\Entity\Nameday[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class NamedaysController extends AppController
{

    /**
     * Initialize controller
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
		$this->set('title', __('Namedays'));
		
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
		$namedays = null;
		
		$this->set('title', __('Namedays'));

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
				//'Namedays.name' 			=> 1,
				//'Namedays.visible' 		=> 1,
				//'Namedays.month' 			=> date("m"),
				//'Namedays.day' 				=> date("d"),
				//'Namedays.created >= ' 	=> new \DateTime('-10 days'),
				//'Namedays.modified >= '	=> new \DateTime('-10 days'),
			],
			/*
			// Nem tanácsos az order-t itt használni, mert pl az edit után az utolsó  ordert ugyan beálíltja, de
			// kiegészíti ezzel s így az utoljára mentett rekord nem lesz megtalálható az X-edik oldalon, mert az az elsőre kerül.
			// A felhasználó állítson be rendezettséget magának! Kivételes esetek persze lehetnek!
			*/
			'order' => [
				//'Namedays.id' 			=> 'desc',
				//'Namedays.name' 		=> 'asc',
				//'Namedays.visible' 		=> 'desc',
				//'Namedays.pos' 			=> 'desc',
				//'Namedays.rank' 		=> 'asc',
				//'Namedays.created' 		=> 'desc',
				//'Namedays.modified' 	=> 'desc',
			],
			'limit' => $this->config['index_number_of_rows'],
			'maxLimit' => $this->config['index_number_of_rows'],
			//'sortableFields' => ['id', 'name', 'created', '...'],
			//'paramType' => 'querystring',
			//'fields' => ['Namedays.id', 'Namedays.name', ...],
			//'finder' => 'published',
        ];

		//$this->paging = $this->session->read('Layout.' . $this->controller . '.Paging');

		if( $this->paging === null){
			$this->paginate['order'] = [
				//'Namedays.id' 			=> 'desc',
				//'Namedays.name' 		=> 'asc',
				//'Namedays.visible' 		=> 'desc',
				//'Namedays.pos' 			=> 'desc',
				//'Namedays.rank' 		=> 'asc',
				//'Namedays.created' 		=> 'desc',
				//'Namedays.modified' 	=> 'desc',
			];
		}else{
			if($this->request->getQuery('sort') === null && $this->request->getQuery('direction') === null){
				$this->paginate['order'] = [
					// If not in URL-ben, then come from sessinon...
					$this->paging['Namedays']['sort'] => $this->paging['Namedays']['direction']	
				];
			}
		}

		if($this->request->getQuery('page') === null && !isset($this->paging['Namedays']['page']) ){
			$this->paginate['page'] = 1;
		}else{
			$this->paginate['page'] = (isset($this->paging['Namedays']['page'])) ? $this->paging['Namedays']['page'] : 1;
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
						//	'page'		=> $this->paging['Namedays']['page'], 	// Vagy 1
						//	'sort'		=> $this->paging['Namedays']['sort'], 
						//	'direction'	=> $this->paging['Namedays']['direction'],
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
			//$this->paginate['conditions'] = ['Namedays.name LIKE' => $q ];
			$this->paginate['conditions'][] = [
				'OR' => [
					['Namedays.name LIKE' => $search['s'] ],
					//['Namedays.title LIKE' => $search['s'] ], // ... just add more fields
				]
			];
			
		}else{
			if(empty($this->paginate['conditions'])){
				$this->paginate['conditions'] = 
					[
						'Namedays.month' => date("m"),
						'Namedays.day' 	 => date("d")
					];
			}
		}
		// -- /.Filter --

		//debug($this->paging); die();
		//debug($this->paginate); die();
		
		try {
			$namedays = $this->paginate($this->Namedays);
		} catch (NotFoundException $e) {
			$paging = $this->request->getAttribute('paging');
			if($paging['Namedays']['prevPage'] !== null && $paging['Namedays']['prevPage']){
				if($paging['Namedays']['page'] !== null && $paging['Namedays']['page'] > 0){
					return $this->redirect([
						'controller' => $this->controller, 
						'action' => 'index', 
						'?' => [
							'page'		=> 1,	//$this->paging['Namedays']['page'],
							'sort'		=> $this->paging['Namedays']['sort'],
							'direction'	=> $this->paging['Namedays']['direction'],
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
		$this->set(compact('namedays'));
		
	}


    /**
     * View method
     *
     * @param string|null $id Nameday id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->set('title', __('Nameday'));
		
        $nameday = $this->Namedays->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

		$name = $nameday->name;

        $this->set(compact('nameday', 'id', 'name'));
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->set('title', __('Nameday'));
        $nameday = $this->Namedays->newEmptyEntity();
        if ($this->request->is('post')) {
            $nameday = $this->Namedays->patchEntity($nameday, $this->request->getData());
            if ($this->Namedays->save($nameday)) {
                //$this->Flash->success(__('The nameday has been saved.'));
                $this->Flash->success(__('Has been saved.'));

				$this->session->write('Layout.' . $this->controller . '.LastId', $nameday->id);
	
                //return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> 1,
						'sort'		=> 'id',
						'direction'	=> 'desc',
					],
					'#' => $nameday->id	// Az állandó header miatt takarásban van még. Majd...
				]);

                return $this->redirect(['action' => 'index']);
            }
            //$this->Flash->error(__('The nameday could not be saved. Please, try again.'));
			$this->Flash->error(__('Could not be saved. Please, try again.'));
        }
        $this->set(compact('nameday'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Nameday id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->set('title', __('Nameday'));
        $nameday = $this->Namedays->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

        if ($this->request->is(['patch', 'post', 'put'])) {
			//debug($this->request->getData()); //die();
            $nameday = $this->Namedays->patchEntity($nameday, $this->request->getData());
            //debug($nameday); //die();
			if ($this->Namedays->save($nameday)) {
                //$this->Flash->success(__('The nameday has been saved.'));
                $this->Flash->success(__('Has been saved.'));
				
				//return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> (isset($this->paging['Namedays']['page'])) ? $this->paging['Namedays']['page'] : 1, 		// or 1
						'sort'		=> (isset($this->paging['Namedays']['sort'])) ? $this->paging['Namedays']['sort'] : 'created', 
						'direction'	=> (isset($this->paging['Namedays']['direction'])) ? $this->paging['Namedays']['direction'] : 'desc',
					],
					'#' => $id
				]);
				
            }
            //$this->Flash->error(__('The nameday could not be saved. Please, try again.'));
            $this->Flash->error(__('Could not be saved. Please, try again.'));
        }

		$name = $nameday->name;

        $this->set(compact('nameday', 'id', 'name'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Nameday id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $nameday = $this->Namedays->get($id);
        if ($this->Namedays->delete($nameday)) {
            //$this->Flash->success(__('The nameday has been deleted.'));
            $this->Flash->success(__('Has been deleted.'));
        } else {
            //$this->Flash->error(__('The nameday could not be deleted. Please, try again.'));
            $this->Flash->error(__('Could not be deleted. Please, try again.'));
        }

        //return $this->redirect(['action' => 'index']);
		return $this->redirect([
			'controller' => $this->controller, 
			'action' => 'index', 
			'?' => [
				'page'		=> $this->paging['Namedays']['page'], 
				'sort'		=> $this->paging['Namedays']['sort'], 
				'direction'	=> $this->paging['Namedays']['direction'],
			]
		]);
		
    }

}

