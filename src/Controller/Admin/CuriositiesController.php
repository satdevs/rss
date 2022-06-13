<?php
// Baked at 2022.03.17. 09:48:17
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AppController;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use Cake\Database\Expression\QueryExpression;

/**
 * Curiosities Controller
 *
 * @property \App\Model\Table\CuriositiesTable $Curiosities
 * @method \App\Model\Entity\Curiosity[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CuriositiesController extends AppController
{

    /**
     * Initialize controller
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
		$this->set('title', __('Curiosities'));
		
	}

/*
	use Cake\Database\Expression\QueryExpression;

	...

	function incrementCounters()
	{
		$expression = new QueryExpression('view_count = view_count + 1');
		$this->updateAll([$expression], ['published' => true]);
	}
*/

    public function setCurrent($id = null)
    {
		//$this->Curiosities->updateAll(['counter' => 0], [true]);
		$this->Curiosities->updateAll(['current' => 0], ['current' => 1]);
		$this->Curiosities->updateAll(['current' => 1], ['id' => $id]);
		$this->Curiosities->updateAll(['modified = NOW()'], ['current' => 1]);

		$expression = new QueryExpression('counter = counter + 1');
		$this->Curiosities->updateAll([$expression], ['current' => 1]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);
		return $this->redirect([
			'controller' => $this->controller, 
			'action' => 'index', 
			'?' => [
				'page'		=> $this->paging['Curiosities']['page'],
				'sort'		=> $this->paging['Curiosities']['sort'],
				'direction'	=> $this->paging['Curiosities']['direction'],
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
		$curiosities = null;
		
		$this->set('title', __('Curiosities'));

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
				//'Curiosities.name' 		=> 1,
				//'Curiosities.visible' 		=> 1,
				//'Curiosities.created >= ' 	=> new \DateTime('-10 days'),
				//'Curiosities.modified >= '	=> new \DateTime('-10 days'),
			],
			/*
			// Nem tanácsos az order-t itt használni, mert pl az edit után az utolsó  ordert ugyan beálíltja, de
			// kiegészíti ezzel s így az utoljára mentett rekord nem lesz megtalálható az X-edik oldalon, mert az az elsőre kerül.
			// A felhasználó állítson be rendezettséget magának! Kivételes esetek persze lehetnek!
			*/
			'order' => [
				//'Curiosities.id' 			=> 'desc',
				//'Curiosities.name' 		=> 'asc',
				//'Curiosities.visible' 		=> 'desc',
				//'Curiosities.pos' 			=> 'desc',
				//'Curiosities.rank' 		=> 'asc',
				//'Curiosities.created' 		=> 'desc',
				//'Curiosities.modified' 	=> 'desc',
			],
			'limit' => $this->config['index_number_of_rows'],
			'maxLimit' => $this->config['index_number_of_rows'],
			//'sortableFields' => ['id', 'name', 'created', '...'],
			//'paramType' => 'querystring',
			//'fields' => ['Curiosities.id', 'Curiosities.name', ...],
			//'finder' => 'published',
        ];

		//$this->paging = $this->session->read('Layout.' . $this->controller . '.Paging');

		if( $this->paging === null){
			$this->paginate['order'] = [
				//'Curiosities.id' 			=> 'desc',
				//'Curiosities.name' 		=> 'asc',
				//'Curiosities.visible' 		=> 'desc',
				//'Curiosities.pos' 			=> 'desc',
				//'Curiosities.rank' 		=> 'asc',
				//'Curiosities.created' 		=> 'desc',
				//'Curiosities.modified' 	=> 'desc',
			];
		}else{
			if($this->request->getQuery('sort') === null && $this->request->getQuery('direction') === null){
				$this->paginate['order'] = [
					// If not in URL-ben, then come from sessinon...
					$this->paging['Curiosities']['sort'] => $this->paging['Curiosities']['direction']	
				];
			}
		}

		if($this->request->getQuery('page') === null && !isset($this->paging['Curiosities']['page']) ){
			$this->paginate['page'] = 1;
		}else{
			$this->paginate['page'] = (isset($this->paging['Curiosities']['page'])) ? $this->paging['Curiosities']['page'] : 1;
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
						//	'page'		=> $this->paging['Curiosities']['page'], 	// Vagy 1
						//	'sort'		=> $this->paging['Curiosities']['sort'], 
						//	'direction'	=> $this->paging['Curiosities']['direction'],
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
			//$this->paginate['conditions'] = ['Curiosities.name LIKE' => $q ];
			$this->paginate['conditions'][] = [
				'OR' => [
					['Curiosities.name LIKE' => $search['s'] ],
					//['Curiosities.title LIKE' => $search['s'] ], // ... just add more fields
				]
			];
			
		}
		// -- /.Filter --
		
		try {
			$curiosities = $this->paginate($this->Curiosities);
		} catch (NotFoundException $e) {
			$paging = $this->request->getAttribute('paging');
			if($paging['Curiosities']['prevPage'] !== null && $paging['Curiosities']['prevPage']){
				if($paging['Curiosities']['page'] !== null && $paging['Curiosities']['page'] > 0){
					return $this->redirect([
						'controller' => $this->controller, 
						'action' => 'index', 
						'?' => [
							'page'		=> 1,	//$this->paging['Curiosities']['page'],
							'sort'		=> $this->paging['Curiosities']['sort'],
							'direction'	=> $this->paging['Curiosities']['direction'],
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
		$this->set(compact('curiosities'));
		
	}


    /**
     * View method
     *
     * @param string|null $id Curiosity id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->set('title', __('Curiosity'));
		
        $curiosity = $this->Curiosities->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

		$name = $curiosity->name;

        $this->set(compact('curiosity', 'id', 'name'));
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->set('title', __('Curiosity'));
        $curiosity = $this->Curiosities->newEmptyEntity();
        if ($this->request->is('post')) {
            $curiosity = $this->Curiosities->patchEntity($curiosity, $this->request->getData());
            if ($this->Curiosities->save($curiosity)) {
                //$this->Flash->success(__('The curiosity has been saved.'));
                $this->Flash->success(__('Has been saved.'));

				$this->session->write('Layout.' . $this->controller . '.LastId', $curiosity->id);
	
                //return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> 1,
						'sort'		=> 'id',
						'direction'	=> 'desc',
					],
					'#' => $curiosity->id	// Az állandó header miatt takarásban van még. Majd...
				]);

                return $this->redirect(['action' => 'index']);
            }
            //$this->Flash->error(__('The curiosity could not be saved. Please, try again.'));
			$this->Flash->error(__('Could not be saved. Please, try again.'));
        }
        $this->set(compact('curiosity'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Curiosity id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->set('title', __('Curiosity'));
        $curiosity = $this->Curiosities->get($id, [
            'contain' => [],
        ]);

		$this->session->write('Layout.' . $this->controller . '.LastId', $id);

        if ($this->request->is(['patch', 'post', 'put'])) {
			//debug($this->request->getData()); //die();
            $curiosity = $this->Curiosities->patchEntity($curiosity, $this->request->getData());
            //debug($curiosity); //die();
			if ($this->Curiosities->save($curiosity)) {
                //$this->Flash->success(__('The curiosity has been saved.'));
                $this->Flash->success(__('Has been saved.'));
				
				//return $this->redirect(['action' => 'index']);
                return $this->redirect([
					'controller' => $this->controller, 
					'action' => 'index', 
					'?' => [
						'page'		=> (isset($this->paging['Curiosities']['page'])) ? $this->paging['Curiosities']['page'] : 1, 		// or 1
						'sort'		=> (isset($this->paging['Curiosities']['sort'])) ? $this->paging['Curiosities']['sort'] : 'created', 
						'direction'	=> (isset($this->paging['Curiosities']['direction'])) ? $this->paging['Curiosities']['direction'] : 'desc',
					],
					'#' => $id
				]);
				
            }
            //$this->Flash->error(__('The curiosity could not be saved. Please, try again.'));
            $this->Flash->error(__('Could not be saved. Please, try again.'));
        }

		$name = $curiosity->name;

        $this->set(compact('curiosity', 'id', 'name'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Curiosity id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $curiosity = $this->Curiosities->get($id);
        if ($this->Curiosities->delete($curiosity)) {
            //$this->Flash->success(__('The curiosity has been deleted.'));
            $this->Flash->success(__('Has been deleted.'));
        } else {
            //$this->Flash->error(__('The curiosity could not be deleted. Please, try again.'));
            $this->Flash->error(__('Could not be deleted. Please, try again.'));
        }

        //return $this->redirect(['action' => 'index']);
		return $this->redirect([
			'controller' => $this->controller, 
			'action' => 'index', 
			'?' => [
				'page'		=> $this->paging['Curiosities']['page'], 
				'sort'		=> $this->paging['Curiosities']['sort'], 
				'direction'	=> $this->paging['Curiosities']['direction'],
			]
		]);
		
    }

}

