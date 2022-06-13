<?php
// Baked at 2022.03.17. 08:53:39
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use Cake\Database\Expression\QueryExpression;

/**
 * Jokes Controller
 *
 * @property \App\Model\Table\JokesTable $Jokes
 * @method \App\Model\Entity\Joke[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class JokesController extends AppController
{

    /**
     * Initialize controller
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
		$this->set('title', __('Jokes'));
		
	}
	
    public function setCurrent($id = null)
    {
		$this->Jokes->updateAll(['current' => 0], ['current' => 1]);
		$this->Jokes->updateAll(['current' => 1], ['id' => $id]);
		$this->Jokes->updateAll(['modified = NOW()'], ['current' => 1]);
		
		$expression = new QueryExpression('counter = counter + 1');
		$this->Jokes->updateAll([$expression], ['current' => 1]);
		
		$this->session->write('Layout.' . $this->controller . '.LastId', $id);
		return $this->redirect([
			'controller' => $this->controller, 
			'action' => 'index', 
			'?' => [
				'page'		=> $this->paging['Jokes']['page'],
				'sort'		=> $this->paging['Jokes']['sort'],
				'direction'	=> $this->paging['Jokes']['direction'],
			],
			'#' => $id
		]);			
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
		$jokes = null;
		
		$this->set('title', __('Jokes'));

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
				//'Jokes.name' 		=> 1,
				//'Jokes.visible' 		=> 1,
				//'Jokes.created >= ' 	=> new \DateTime('-10 days'),
				//'Jokes.modified >= '	=> new \DateTime('-10 days'),
			],
			/*
			// Nem tanácsos az order-t itt használni, mert pl az edit után az utolsó  ordert ugyan beálíltja, de
			// kiegészíti ezzel s így az utoljára mentett rekord nem lesz megtalálható az X-edik oldalon, mert az az elsőre kerül.
			// A felhasználó állítson be rendezettséget magának! Kivételes esetek persze lehetnek!
			*/
			'order' => [
				//'Jokes.id' 			=> 'desc',
				//'Jokes.name' 		=> 'asc',
				//'Jokes.visible' 		=> 'desc',
				//'Jokes.pos' 			=> 'desc',
				//'Jokes.rank' 		=> 'asc',
				//'Jokes.created' 		=> 'desc',
				//'Jokes.modified' 	=> 'desc',
			],
			'limit' => $this->config['index_number_of_rows'],
			'maxLimit' => $this->config['index_number_of_rows'],
			//'sortableFields' => ['id', 'name', 'created', '...'],
			//'paramType' => 'querystring',
			//'fields' => ['Jokes.id', 'Jokes.name', ...],
			//'finder' => 'published',
        ];

		//$this->paging = $this->session->read('Layout.' . $this->controller . '.Paging');

		if( $this->paging === null){
			$this->paginate['order'] = [
				//'Jokes.id' 			=> 'desc',
				//'Jokes.name' 		=> 'asc',
				//'Jokes.visible' 		=> 'desc',
				//'Jokes.pos' 			=> 'desc',
				//'Jokes.rank' 		=> 'asc',
				//'Jokes.created' 		=> 'desc',
				//'Jokes.modified' 	=> 'desc',
			];
		}else{
			if($this->request->getQuery('sort') === null && $this->request->getQuery('direction') === null){
				$this->paginate['order'] = [
					// If not in URL-ben, then come from sessinon...
					$this->paging['Jokes']['sort'] => $this->paging['Jokes']['direction']	
				];
			}
		}

		if($this->request->getQuery('page') === null && !isset($this->paging['Jokes']['page']) ){
			$this->paginate['page'] = 1;
		}else{
			$this->paginate['page'] = (isset($this->paging['Jokes']['page'])) ? $this->paging['Jokes']['page'] : 1;
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
						//	'page'		=> $this->paging['Jokes']['page'], 	// Vagy 1
						//	'sort'		=> $this->paging['Jokes']['sort'], 
						//	'direction'	=> $this->paging['Jokes']['direction'],
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
			//$this->paginate['conditions'] = ['Jokes.name LIKE' => $q ];
			$this->paginate['conditions'][] = [
				'OR' => [
					['Jokes.name LIKE' => $search['s'] ],
					//['Jokes.title LIKE' => $search['s'] ], // ... just add more fields
				]
			];
			
		}
		// -- /.Filter --


		// ***
		$this->paginate['order'] = [
			'Jokes.current' 	=> 'desc',
			'Jokes.id' 			=> 'asc',
		];
		
		//debug($this->paginate); die();
		
		try {
			$jokes = $this->paginate($this->Jokes);
		} catch (NotFoundException $e) {
			$paging = $this->request->getAttribute('paging');
			if($paging['Jokes']['prevPage'] !== null && $paging['Jokes']['prevPage']){
				if($paging['Jokes']['page'] !== null && $paging['Jokes']['page'] > 0){
					return $this->redirect([
						'controller' => $this->controller, 
						'action' => 'index', 
						'?' => [
							'page'		=> 1,	//$this->paging['Jokes']['page'],
							'sort'		=> $this->paging['Jokes']['sort'],
							'direction'	=> $this->paging['Jokes']['direction'],
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
		$this->set(compact('jokes'));
		
	}


    /**
     * View method
     *
     * @param string|null $id Joke id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->set('title', __('Joke'));
		
        $joke = $this->Jokes->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

		$name = $joke->name;

        $this->set(compact('joke', 'id', 'name'));
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->set('title', __('Joke'));
        $joke = $this->Jokes->newEmptyEntity();
        if ($this->request->is('post')) {
            $joke = $this->Jokes->patchEntity($joke, $this->request->getData());
            if ($this->Jokes->save($joke)) {
                //$this->Flash->success(__('The joke has been saved.'));
                $this->Flash->success(__('Has been saved.'));

				$this->session->write('Layout.' . $this->controller . '.LastId', $joke->id);
	
                return $this->redirect(['action' => 'add']);
				
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> 1,
						'sort'		=> 'id',
						'direction'	=> 'desc',
					],
					'#' => $joke->id	// Az állandó header miatt takarásban van még. Majd...
				]);

                //return $this->redirect(['action' => 'index']);
            }
            //$this->Flash->error(__('The joke could not be saved. Please, try again.'));
			$this->Flash->error(__('Could not be saved. Please, try again.'));
        }
        $this->set(compact('joke'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Joke id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->set('title', __('Joke'));
        $joke = $this->Jokes->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

        if ($this->request->is(['patch', 'post', 'put'])) {
			//debug($this->request->getData()); //die();
            $joke = $this->Jokes->patchEntity($joke, $this->request->getData());
            //debug($joke); //die();
			if ($this->Jokes->save($joke)) {
                //$this->Flash->success(__('The joke has been saved.'));
                $this->Flash->success(__('Has been saved.'));
				
				//return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> (isset($this->paging['Jokes']['page'])) ? $this->paging['Jokes']['page'] : 1, 		// or 1
						'sort'		=> (isset($this->paging['Jokes']['sort'])) ? $this->paging['Jokes']['sort'] : 'created', 
						'direction'	=> (isset($this->paging['Jokes']['direction'])) ? $this->paging['Jokes']['direction'] : 'desc',
					],
					'#' => $id
				]);
				
            }
            //$this->Flash->error(__('The joke could not be saved. Please, try again.'));
            $this->Flash->error(__('Could not be saved. Please, try again.'));
        }

		$name = $joke->name;

        $this->set(compact('joke', 'id', 'name'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Joke id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $joke = $this->Jokes->get($id);
        if ($this->Jokes->delete($joke)) {
            //$this->Flash->success(__('The joke has been deleted.'));
            $this->Flash->success(__('Has been deleted.'));
        } else {
            //$this->Flash->error(__('The joke could not be deleted. Please, try again.'));
            $this->Flash->error(__('Could not be deleted. Please, try again.'));
        }

        //return $this->redirect(['action' => 'index']);
		return $this->redirect([
			'controller' => $this->controller, 
			'action' => 'index', 
			'?' => [
				'page'		=> $this->paging['Jokes']['page'], 
				'sort'		=> $this->paging['Jokes']['sort'], 
				'direction'	=> $this->paging['Jokes']['direction'],
			]
		]);
		
    }

}

