<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Forecast[]|\Cake\Collection\CollectionInterface $forecasts
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
	$config['index_show_created'] 	= true;
	$config['index_show_modified'] 	= true;
	//$config['index_show_counts'] 		= true;
	//$config['index_show_actions'] 	= true;	
	//$config['index_enable_view'] 		= true;
	//$config['index_enable_edit'] 		= true;
	//$config['index_enable_delete'] 	= true;
	//$config['index_enable_db_click'] 	= true;
	//$config['index_db_click_action'] 	= 'edit';	// edit, view
	//
	//$url = $this->Url->build(['prefix' => $prefix, 'controller' => $controller , 'action' => $config['index_db_click_action']]);

	if(!isset($layoutForecastsLastId)){
		$layoutForecastsLastId = 0;
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
			  
				<?php //= __('Forecasts') ?>	
				<div class="card-body table-responsive p-0 forecasts">
<?php //debug($session->read()); ?>
					<table class="table table-hover table-striped table-bordered text-nowrap">
						<thead>
							<tr>
								<th class="row-id-anchor"></th>
<?php if(isset($config['index_show_id']) && $config['index_show_id']){ ?>
								<th class="id integer"><?= $this->Paginator->sort('id') ?></th>
<?php } ?>
								<th class="date integer"><?= $this->Paginator->sort('date') ?></th>
								<th class="name string"><?= $this->Paginator->sort('name') ?></th>
								<th class="year integer"><?= $this->Paginator->sort('year') ?></th>
								<th class="day string"><?= $this->Paginator->sort('day') ?></th>
								<th class="d string"><?= $this->Paginator->sort('d') ?></th>
								<th class="tmin string"><?= $this->Paginator->sort('tmin') ?></th>
								<th class="tmax string"><?= $this->Paginator->sort('tmax') ?></th>
								<th class="wx string"><?= $this->Paginator->sort('wx') ?></th>
								<th class="sr string"><?= $this->Paginator->sort('sr') ?></th>
								<th class="ss string"><?= $this->Paginator->sort('ss') ?></th>
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
					<?php foreach ($forecasts as $forecast): ?>
							<tr row-id="<?= $forecast->id ?>"<?php if($forecast->id == $layoutForecastsLastId){ echo ' class="table-tr-last-id"'; } ?>  prefix="<?= $prefix ?>" controller="<?= $controller ?>" action="<?= $action ?>" aria-expanded="true">
								<td class="row-id-anchor" value="<?= $forecast->id ?>"><a class="anchor" name="<?= $forecast->id ?>"></a></td><!-- bake-0 -->
<?php if(isset($config['index_show_id']) && $config['index_show_id']){ ?>
								<td class="id integer" name="id" value="<?= $this->Number->format($forecast->id) ?>"><?= $this->Number->format($forecast->id) ?></td><!-- bake-3 -->
<?php } ?>
								<td class="date string" name="name" value="<?= $forecast->date ?>"><?= h($forecast->date) ?></td><!-- bake-2 -->
								<td class="name string" name="name" value="<?= $forecast->name ?>"><?= h($forecast->name) ?></td><!-- bake-2 -->
								<td class="year integer" name="year" value="<?= $this->Number->format($forecast->year) ?>"><?= $this->Number->format($forecast->year) ?></td><!-- bake-3 -->
								<td class="day string" name="day" value="<?= $forecast->day ?>"><?= h($forecast->day) ?></td><!-- bake-2 -->
								<td class="d string" name="d" value="<?= $forecast->d ?>"><?= h($forecast->d) ?></td><!-- bake-2 -->
								<td class="tmin string" name="tmin" value="<?= $forecast->tmin ?>"><?= h($forecast->tmin) ?></td><!-- bake-2 -->
								<td class="tmax string" name="tmax" value="<?= $forecast->tmax ?>"><?= h($forecast->tmax) ?></td><!-- bake-2 -->
								<td class="wx string" name="wx" value="<?= $forecast->wx ?>"><?= h($forecast->wx) ?></td><!-- bake-2 -->
								<td class="sr string" name="sr" value="<?= $forecast->sr ?>"><?= h($forecast->sr) ?></td><!-- bake-2 -->
								<td class="ss string" name="ss" value="<?= $forecast->ss ?>"><?= h($forecast->ss) ?></td><!-- bake-2 -->


<?php if(isset($config['index_show_created']) && $config['index_show_created'] || isset($config['index_show_modified']) && $config['index_show_modified']){ ?>
								<td class="datetime created-modified">
<?php 	if(isset($config['index_show_created']) && $config['index_show_created']){ ?>
									<?= h($forecast->created) ?>
<?php 	} ?>
<?php 	if(isset($config['index_show_created']) && $config['index_show_created'] && isset($config['index_show_modified']) && $config['index_show_modified']){ ?>
									<br>
<?php 	} ?>
<?php 	if(isset($config['index_show_modified']) && $config['index_show_modified']){ ?>
									<?= h($forecast->modified) ?>
<?php 	} ?>
								</td>
<?php } ?>


<?php if(isset($config['index_show_actions']) && $config['index_show_actions']){ ?>
								<td class="actions text-center">
<?php 	if(isset($config['index_enable_view']) && $config['index_enable_view']){ ?>					  
									<?= $this->Html->link('<i class="fas fa-eye"></i>', ['action' => 'view', $forecast->id], ['escape' => false, 'class' => 'btn btn-sm bg-gradient-warning action-button-view', 'data-bs-tooltip'=>'tooltip', 'data-bs-placement'=>'top', 'title' => __('View this record')]) ?>
<?php 	} ?>
<?php 	if(isset($config['index_enable_edit']) && $config['index_enable_edit']){ ?>					  
									<?= $this->Html->link('<i class="fas fa-edit"></i>', ['action' => 'edit', $forecast->id], ['escape' => false, 'class' => 'btn btn-sm bg-gradient-success action-button-edit', 'data-bs-tooltip'=>'tooltip', 'data-bs-placement'=>'top', 'title' => __('Edit this record')]) ?>
<?php 	} ?>			
<?php 	if(isset($config['index_enable_delete']) && $config['index_enable_delete']){ ?>					  
									<?php //= $this->Form->postLink('<i class="fas fa-remove"></i>', ['action' => 'delete', $forecast->id], ['escape' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $forecast->id), 'class' => 'btn btn-sm bg-gradient-danger action-button-delete']) ?>						
									<?= $this->Form->postLink('', ['action' => 'delete', $forecast->id], ['class'=>'crose-btn hide-postlink action-button-delete']) ?>
									<a href="javascript:;" class="btn btn-sm btn-danger delete postlink-delete" data-bs-tooltip="tooltip" data-bs-placement="top" title="<?= __("Delete this record!") ?>" text="<?= h($forecast->name) ?>" subText="<?= __("You will not be able to revert this!") ?>" confirmButtonText="<?= __("Yes, delete it!") ?>" cancelButtonText="<?= __("Cancel") ?>"><i class="icon-minus"></i></a>
									
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
<?php $url = $this->Url->build(['controller' => 'Forecasts', 'action' => $config['index_db_click_action']]); ?>
		$('tr').dblclick( function(){
<?php /* window.location.href = '/<?= $prefix ?>/forecasts/<?= $config['index_db_click_action'] ?>/'+$(this).attr('row-id'); */ ?>
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
		$(".pagination a[href^='<?= $base ?>/<?= $prefix ?>/forecasts?sort=']").each(function(){ 
			this.href = this.href.replace("<?= $base ?>/<?= $prefix ?>/forecasts?sort=", "<?= $base ?>/<?= $prefix ?>/forecasts?page=1&sort=");
		});
		$(".pagination a[href='<?= $base ?>/<?= $prefix ?>/forecasts']").each(function(){ 
			this.href = this.href.replace("<?= $base ?>/<?= $prefix ?>/forecasts", "<?= $base ?>/<?= $prefix ?>/forecasts?page=1");
		});
<?php } ?>

	});
	<?php $this->Html->scriptEnd(); ?>