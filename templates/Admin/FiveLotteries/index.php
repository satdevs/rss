<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\FiveLottery[]|\Cake\Collection\CollectionInterface $fiveLotteries
 */
?>
<?php 
	use Cake\Core\Configure;
	
	//$session 			= $this->getRequest()->getSession();
	//$prefix 			= strtolower( $this->request->getParam('prefix') );	
	//$controller 		= $this->request->getParam('controller');	// for DB click on <tr>
	//$action 			= $this->request->getParam('action');		// for DB click on <tr>
	//$passedArgs 		= $this->request->getParam('pass');			// for DB click on <tr>
	
	$config = Configure::read('Theme.' . $prefix);	
	//-------> More config from \config\jeffadmin.php <------
	//$config['index_show_id'] 			= true;
	//$config['index_show_visible'] 	= true;
	//$config['index_show_pos'] 		= true;
	//$config['index_show_created'] 	= true;
	//$config['index_show_modified'] 	= true;
	//$config['index_show_counts'] 		= true;
	//$config['index_show_actions'] 	= true;	
	//$config['index_enable_view'] 		= true;
	//$config['index_enable_edit'] 		= true;
	//$config['index_enable_delete'] 	= true;
	//$config['index_enable_db_click'] 	= true;
	//$config['index_db_click_action'] 	= 'edit';	// edit, view
	//
	//$url = $this->Url->build(['prefix' => $prefix, 'controller' => $controller , 'action' => $config['index_db_click_action']]);

	if(!isset($layoutFiveLotteriesLastId)){
		$layoutFiveLotteriesLastId = 0;
	}

?>
		<div class="index col-12">
            <div class="card card-lightblue">
				<div class="card-header">
					<h4 class="card-title"><?= __('Index') ?>: <?= $title ?><?php
					if(isset($search) && $search != ''){
						echo " &rarr; " . __('filter') . ": <b>" . $search . "</b>";
					}
				?></h4>
					<div class="card-tools">
						<?= $this->element('JeffAdmin.paginateTop') ?>
					</div>				
				</div><!-- ./card-header -->
			  
				<?php //= __('FiveLotteries') ?>	
				<div class="card-body table-responsive p-0 fiveLotteries">
<?php //debug($session->read()); ?>
					<table class="table table-hover table-striped table-bordered text-nowrap">
						<thead>
							<tr>
								<th class="row-id-anchor"></th>
<?php if(isset($config['index_show_id']) && $config['index_show_id']){ ?>
								<th class="id integer"><?= $this->Paginator->sort('id') ?></th>
<?php } ?>
								<th class="year integer"><?= $this->Paginator->sort('year') ?></th>
								<th class="week integer"><?= $this->Paginator->sort('week') ?></th>
								<th class="pull-date date"><?= $this->Paginator->sort('pull_date') ?></th>
								<th class="results -5 integer"><?= $this->Paginator->sort('results _5') ?></th>
								<th class="results -5-price integer"><?= $this->Paginator->sort('results _5_price') ?></th>
								<th class="results -4 integer"><?= $this->Paginator->sort('results _4') ?></th>
								<th class="results -4-price integer"><?= $this->Paginator->sort('results _4_price') ?></th>
								<th class="results -3 integer"><?= $this->Paginator->sort('results _3') ?></th>
								<th class="results -3-price integer"><?= $this->Paginator->sort('results _3_price') ?></th>
								<th class="results -2 integer"><?= $this->Paginator->sort('results _2') ?></th>
								<th class="results -2-price integer"><?= $this->Paginator->sort('results _2_price') ?></th>
								<th class="number-1 tinyinteger"><?= $this->Paginator->sort('number_1') ?></th>
								<th class="number-2 tinyinteger"><?= $this->Paginator->sort('number_2') ?></th>
								<th class="number-3 tinyinteger"><?= $this->Paginator->sort('number_3') ?></th>
								<th class="number-4 tinyinteger"><?= $this->Paginator->sort('number_4') ?></th>
								<th class="number-5 tinyinteger"><?= $this->Paginator->sort('number_5') ?></th>
<?php if(isset($config['index_show_created']) && $config['index_show_created'] || isset($config['index_show_modified']) && $config['index_show_modified']){ ?>
								<th class="datetime created-modified">
<?php 	if(isset($config['index_show_created']) && $config['index_show_created']){ ?>
									<?= $this->Paginator->sort('created') ?>
<?php 	} ?>
<?php 	if(isset($config['index_show_created']) && $config['index_show_created'] && isset($config['index_show_modified']) && $config['index_show_modified']){ ?>
									<br>
<?php 	} ?>
<?php 	if(isset($config['index_show_modified']) && $config['index_show_modified']){ ?>
									<?= $this->Paginator->sort('modified') ?>
<?php 	} ?>
								</th>
<?php } ?>



<?php if(isset($config['index_show_actions']) && $config['index_show_actions']){ ?>
								<th class="actions"><?= __('Actions') ?></th>
<?php } ?>
							</tr>
						</thead>
						<tbody>
					<?php foreach ($fiveLotteries as $fiveLottery): ?>
							<tr row-id="<?= $fiveLottery->id ?>"<?php if($fiveLottery->id == $layoutFiveLotteriesLastId){ echo ' class="table-tr-last-id"'; } ?>  prefix="<?= $prefix ?>" controller="<?= $controller ?>" action="<?= $action ?>" aria-expanded="true">
								<td class="row-id-anchor" value="<?= $fiveLottery->id ?>"><a class="anchor" name="<?= $fiveLottery->id ?>"></a></td><!-- bake-0 -->
<?php if(isset($config['index_show_id']) && $config['index_show_id']){ ?>
								<td class="id integer" name="id" value="<?= $this->Number->format($fiveLottery->id) ?>"><?= $this->Number->format($fiveLottery->id) ?></td><!-- bake-3 -->
<?php } ?>
								<td class="year integer" name="year" value="<?= $this->Number->format($fiveLottery->year) ?>"><?= $this->Number->format($fiveLottery->year) ?></td><!-- bake-3 -->
								<td class="week integer" name="week" value="<?= $this->Number->format($fiveLottery->week) ?>"><?= $this->Number->format($fiveLottery->week) ?></td><!-- bake-3 -->
								<td class="pull-date date" name="pull-date" value="<?= $fiveLottery->pull_date ?>"><?= h($fiveLottery->pull_date) ?></td><!-- bake-2 -->
								<td class="results -5 integer" name="results -5" value="<?= $this->Number->format($fiveLottery->results _5) ?>"><?= $this->Number->format($fiveLottery->results _5) ?></td><!-- bake-3 -->
								<td class="results -5-price integer" name="results -5-price" value="<?= $this->Number->format($fiveLottery->results _5_price) ?>"><?= $this->Number->format($fiveLottery->results _5_price) ?></td><!-- bake-3 -->
								<td class="results -4 integer" name="results -4" value="<?= $this->Number->format($fiveLottery->results _4) ?>"><?= $this->Number->format($fiveLottery->results _4) ?></td><!-- bake-3 -->
								<td class="results -4-price integer" name="results -4-price" value="<?= $this->Number->format($fiveLottery->results _4_price) ?>"><?= $this->Number->format($fiveLottery->results _4_price) ?></td><!-- bake-3 -->
								<td class="results -3 integer" name="results -3" value="<?= $this->Number->format($fiveLottery->results _3) ?>"><?= $this->Number->format($fiveLottery->results _3) ?></td><!-- bake-3 -->
								<td class="results -3-price integer" name="results -3-price" value="<?= $this->Number->format($fiveLottery->results _3_price) ?>"><?= $this->Number->format($fiveLottery->results _3_price) ?></td><!-- bake-3 -->
								<td class="results -2 integer" name="results -2" value="<?= $this->Number->format($fiveLottery->results _2) ?>"><?= $this->Number->format($fiveLottery->results _2) ?></td><!-- bake-3 -->
								<td class="results -2-price integer" name="results -2-price" value="<?= $this->Number->format($fiveLottery->results _2_price) ?>"><?= $this->Number->format($fiveLottery->results _2_price) ?></td><!-- bake-3 -->
								<td class="number-1 tinyinteger" name="number-1" value="<?= $this->Number->format($fiveLottery->number_1) ?>"><?= $this->Number->format($fiveLottery->number_1) ?></td><!-- bake-3 -->
								<td class="number-2 tinyinteger" name="number-2" value="<?= $this->Number->format($fiveLottery->number_2) ?>"><?= $this->Number->format($fiveLottery->number_2) ?></td><!-- bake-3 -->
								<td class="number-3 tinyinteger" name="number-3" value="<?= $this->Number->format($fiveLottery->number_3) ?>"><?= $this->Number->format($fiveLottery->number_3) ?></td><!-- bake-3 -->
								<td class="number-4 tinyinteger" name="number-4" value="<?= $this->Number->format($fiveLottery->number_4) ?>"><?= $this->Number->format($fiveLottery->number_4) ?></td><!-- bake-3 -->
								<td class="number-5 tinyinteger" name="number-5" value="<?= $this->Number->format($fiveLottery->number_5) ?>"><?= $this->Number->format($fiveLottery->number_5) ?></td><!-- bake-3 -->


<?php if(isset($config['index_show_created']) && $config['index_show_created'] || isset($config['index_show_modified']) && $config['index_show_modified']){ ?>
								<td class="datetime created-modified">
<?php 	if(isset($config['index_show_created']) && $config['index_show_created']){ ?>
									<?= h($fiveLottery->created) ?>
<?php 	} ?>
<?php 	if(isset($config['index_show_created']) && $config['index_show_created'] && isset($config['index_show_modified']) && $config['index_show_modified']){ ?>
									<br>
<?php 	} ?>
<?php 	if(isset($config['index_show_modified']) && $config['index_show_modified']){ ?>
									<?= h($fiveLottery->modified) ?>
<?php 	} ?>
								</td>
<?php } ?>


<?php if(isset($config['index_show_actions']) && $config['index_show_actions']){ ?>
								<td class="actions text-center">
<?php 	if(isset($config['index_enable_view']) && $config['index_enable_view']){ ?>					  
									<?= $this->Html->link('<i class="fas fa-eye"></i>', ['action' => 'view', $fiveLottery->id], ['escape' => false, 'class' => 'btn btn-sm bg-gradient-warning action-button-view', 'data-bs-tooltip'=>'tooltip', 'data-bs-placement'=>'top', 'title' => __('View this record')]) ?>
<?php 	} ?>
<?php 	if(isset($config['index_enable_edit']) && $config['index_enable_edit']){ ?>					  
									<?= $this->Html->link('<i class="fas fa-edit"></i>', ['action' => 'edit', $fiveLottery->id], ['escape' => false, 'class' => 'btn btn-sm bg-gradient-success action-button-edit', 'data-bs-tooltip'=>'tooltip', 'data-bs-placement'=>'top', 'title' => __('Edit this record')]) ?>
<?php 	} ?>			
<?php 	if(isset($config['index_enable_delete']) && $config['index_enable_delete']){ ?>					  
									<?php //= $this->Form->postLink('<i class="fas fa-remove"></i>', ['action' => 'delete', $fiveLottery->id], ['escape' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $fiveLottery->id), 'class' => 'btn btn-sm bg-gradient-danger action-button-delete']) ?>						
									<?= $this->Form->postLink('', ['action' => 'delete', $fiveLottery->id], ['class'=>'crose-btn hide-postlink action-button-delete']) ?>
									<a href="javascript:;" class="btn btn-sm btn-danger delete postlink-delete" data-bs-tooltip="tooltip" data-bs-placement="top" title="<?= __("Delete this record!") ?>" text="<?= h($fiveLottery->name) ?>" subText="<?= __("You will not be able to revert this!") ?>" confirmButtonText="<?= __("Yes, delete it!") ?>" cancelButtonText="<?= __("Cancel") ?>"><i class="icon-minus"></i></a>
									
<?php 	} ?>
								</td>					  
<?php } ?>
							</tr>
						<?php endforeach; ?>
						</tbody>
                </table>
              </div>
              <!-- /.card-body -->
			  
			  <div class="card-footer clearfix">
				<?= $this->element('JeffAdmin.paginateBottom') ?>
				<?php //= $this->Paginator->counter(__('Page  of , showing  record(s) out of  total')) ?>
              </div>			  
			  
            </div>
            <!-- /.card -->
        </div>

	<?php
	if(isset($config['index_show_actions']) && $config['index_show_actions'] && isset($config['index_enable_delete']) && $config['index_enable_delete']){ 
		$this->Html->script(
			[
				'JeffAdmin./dist/js/sweetalert_delete',
			],
			['block' => 'scriptBottom']
		);
	}	
	?>

<?php $this->Html->scriptStart(['block' => 'javaScriptBottom']); ?>

	$(document).ready( function(){
		
<?php //if(isset($config['index_enable_db_click']) && $config['index_enable_db_click'] && isset($config['index_enable_edit']) && $config['index_enable_edit'] && $config['index_db_click_action'] && isset($config['index_db_click_action']) ){ ?>
<?php 	if(isset($prefix) && isset($config['index_db_click_action']) && $config['index_db_click_action'] !== ''){ ?>
<?php $url = $this->Url->build(['controller' => 'FiveLotteries', 'action' => $config['index_db_click_action']]); ?>
		$('tr').dblclick( function(){
<?php /* window.location.href = '/<?= $prefix ?>/fiveLotteries/<?= $config['index_db_click_action'] ?>/'+$(this).attr('row-id'); */ ?>
			window.location.href = '<?= $url . '/' ?>' + $(this).attr('row-id');
		});
<?php 	} ?>
<?php //} ?>

<?php /*
	https://stackoverflow.com/questions/179713/how-to-change-the-href-attribute-for-a-hyperlink-using-jquery  -->
	A pagináció, ha a routerben szerepel az oldal főoldalként, akkor kell mert sessionben tárolódik néhány dolog és...
*/ ?>
<?php 
	$base = '';
	if($this->request->getAttribute('base') != '/'){
		$base = $this->request->getAttribute('base');
	}
?>
		$(".pagination a[href^='<?= $base ?>/<?= $prefix ?>?sort=']").each(function(){
			this.href = this.href.replace("<?= $base ?>/<?= $prefix ?>", "<?= $base ?>/<?= $prefix ?>?page=1&sort=");
		});
		$(".pagination a[href='<?= $base ?>/<?= $prefix ?>']").each(function(){ 
			this.href = this.href.replace("<?= $base ?>/<?= $prefix ?>", "<?= $base ?>/<?= $prefix ?>?page=1");
		});
<?php if(isset($controller)){ ?>
		$(".pagination a[href^='<?= $base ?>/<?= $prefix ?>/fiveLotteries?sort=']").each(function(){ 
			this.href = this.href.replace("<?= $base ?>/<?= $prefix ?>/fiveLotteries?sort=", "<?= $base ?>/<?= $prefix ?>/fiveLotteries?page=1&sort=");
		});
		$(".pagination a[href='<?= $base ?>/<?= $prefix ?>/fiveLotteries']").each(function(){ 
			this.href = this.href.replace("<?= $base ?>/<?= $prefix ?>/fiveLotteries", "<?= $base ?>/<?= $prefix ?>/fiveLotteries?page=1");
		});
<?php } ?>

	});
	<?php $this->Html->scriptEnd(); ?>
