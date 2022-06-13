<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Powerbreak[]|\Cake\Collection\CollectionInterface $powerbreaks
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

	if(!isset($layoutPowerbreaksLastId)){
		$layoutPowerbreaksLastId = 0;
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
			  
				<?php //= __('Powerbreaks') ?>	
				<div class="card-body table-responsive p-0 powerbreaks">
<?php //debug($session->read()); ?>
					<table class="table table-hover table-striped table-bordered">
						<thead>
							<tr>
								<th class="row-id-anchor"></th>
<?php if(isset($config['index_show_id']) && $config['index_show_id']){ ?>
								<th class="id integer"><?= $this->Paginator->sort('id') ?></th>
<?php } ?>
								<th class="name string"><?= $this->Paginator->sort('name') ?></th>
								<th class="date string"><?= $this->Paginator->sort('date') ?></th>
								<th class="time-from string"><?= $this->Paginator->sort('time_from') ?></th>
								<th class="time-to string"><?= $this->Paginator->sort('time_to') ?></th>
								<th class="status string"><?= $this->Paginator->sort('status') ?></th>
								<th class="street string"><?= $this->Paginator->sort('street') ?></th>
								<th class="place string"><?= $this->Paginator->sort('place') ?></th>
								<th class="house-from string"><?= $this->Paginator->sort('house_from') ?></th>
								<th class="house-to string"><?= $this->Paginator->sort('house_to') ?></th>
								<th class="comment string"><?= $this->Paginator->sort('comment') ?> / <?= $this->Paginator->sort('comment2') ?></th>
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
					<?php foreach ($powerbreaks as $powerbreak): ?>
							<tr row-id="<?= $powerbreak->id ?>"<?php if($powerbreak->id == $layoutPowerbreaksLastId){ echo ' class="table-tr-last-id"'; } ?>  prefix="<?= $prefix ?>" controller="<?= $controller ?>" action="<?= $action ?>" aria-expanded="true">
								<td class="row-id-anchor" value="<?= $powerbreak->id ?>"><a class="anchor" name="<?= $powerbreak->id ?>"></a></td><!-- bake-0 -->
<?php if(isset($config['index_show_id']) && $config['index_show_id']){ ?>
								<td class="id integer" name="id" value="<?= $this->Number->format($powerbreak->id) ?>"><?= $this->Number->format($powerbreak->id) ?></td><!-- bake-3 -->
<?php } ?>
								<td class="name string text-bold" name="name" value="<?= $powerbreak->name ?>"><?= h($powerbreak->name) ?></td><!-- bake-2 -->
								<td class="date string text-bold text-center" name="date" value="<?= $powerbreak->date ?>"><?= h($powerbreak->date) ?></td><!-- bake-2 -->
								<td class="time-from string text-bold text-center" name="time-from" value="<?= $powerbreak->time_from ?>"><?= h($powerbreak->time_from) ?></td><!-- bake-2 -->
								<td class="time-to string text-bold text-center" name="time-to" value="<?= $powerbreak->time_to ?>"><?= h($powerbreak->time_to) ?></td><!-- bake-2 -->
								<td class="status string" name="status" value="<?= $powerbreak->status ?>"><?= h($powerbreak->status) ?></td><!-- bake-2 -->
								<td class="street string" name="street" value="<?= $powerbreak->street ?>"><?= h($powerbreak->street) ?></td><!-- bake-2 -->
								<td class="place string" name="place" value="<?= $powerbreak->place ?>"><?= h($powerbreak->place) ?></td><!-- bake-2 -->
								<td class="house-from string" name="house-from" value="<?= $powerbreak->house_from ?>"><?= h($powerbreak->house_from) ?></td><!-- bake-2 -->
								<td class="house-to string" name="house-to" value="<?= $powerbreak->house_to ?>"><?= h($powerbreak->house_to) ?></td><!-- bake-2 -->
								<td class="comment string" name="comment" value="<?= $powerbreak->comment ?>">
									• <?= h($powerbreak->comment) ?><br>
									• <?= h($powerbreak->comment2) ?>									
								</td><!-- bake-2 -->

<?php if(isset($config['index_show_created']) && $config['index_show_created'] || isset($config['index_show_modified']) && $config['index_show_modified']){ ?>
								<td class="datetime created-modified">
<?php 	if(isset($config['index_show_created']) && $config['index_show_created']){ ?>
									<?= h($powerbreak->created) ?>
<?php 	} ?>
<?php 	if(isset($config['index_show_created']) && $config['index_show_created'] && isset($config['index_show_modified']) && $config['index_show_modified']){ ?>
									<br>
<?php 	} ?>
<?php 	if(isset($config['index_show_modified']) && $config['index_show_modified']){ ?>
									<?= h($powerbreak->modified) ?>
<?php 	} ?>
								</td>
<?php } ?>


<?php if(isset($config['index_show_actions']) && $config['index_show_actions']){ ?>
								<td class="actions text-center">
<?php 	if(isset($config['index_enable_view']) && $config['index_enable_view']){ ?>					  
									<?= $this->Html->link('<i class="fas fa-eye"></i>', ['action' => 'view', $powerbreak->id], ['escape' => false, 'class' => 'btn btn-sm bg-gradient-warning action-button-view', 'data-bs-tooltip'=>'tooltip', 'data-bs-placement'=>'top', 'title' => __('View this record')]) ?>
<?php 	} ?>
<?php 	if(isset($config['index_enable_edit']) && $config['index_enable_edit']){ ?>					  
									<?= $this->Html->link('<i class="fas fa-edit"></i>', ['action' => 'edit', $powerbreak->id], ['escape' => false, 'class' => 'btn btn-sm bg-gradient-success action-button-edit', 'data-bs-tooltip'=>'tooltip', 'data-bs-placement'=>'top', 'title' => __('Edit this record')]) ?>
<?php 	} ?>			
<?php 	if(isset($config['index_enable_delete']) && $config['index_enable_delete']){ ?>					  
									<?php //= $this->Form->postLink('<i class="fas fa-remove"></i>', ['action' => 'delete', $powerbreak->id], ['escape' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $powerbreak->id), 'class' => 'btn btn-sm bg-gradient-danger action-button-delete']) ?>						
									<?= $this->Form->postLink('', ['action' => 'delete', $powerbreak->id], ['class'=>'crose-btn hide-postlink action-button-delete']) ?>
									<a href="javascript:;" class="btn btn-sm btn-danger delete postlink-delete" data-bs-tooltip="tooltip" data-bs-placement="top" title="<?= __("Delete this record!") ?>" text="<?= h($powerbreak->name) ?>" subText="<?= __("You will not be able to revert this!") ?>" confirmButtonText="<?= __("Yes, delete it!") ?>" cancelButtonText="<?= __("Cancel") ?>"><i class="icon-minus"></i></a>
									
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
<?php $url = $this->Url->build(['controller' => 'Powerbreaks', 'action' => $config['index_db_click_action']]); ?>
		$('tr').dblclick( function(){
<?php /* window.location.href = '/<?= $prefix ?>/powerbreaks/<?= $config['index_db_click_action'] ?>/'+$(this).attr('row-id'); */ ?>
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
		$(".pagination a[href^='<?= $base ?>/<?= $prefix ?>/powerbreaks?sort=']").each(function(){ 
			this.href = this.href.replace("<?= $base ?>/<?= $prefix ?>/powerbreaks?sort=", "<?= $base ?>/<?= $prefix ?>/powerbreaks?page=1&sort=");
		});
		$(".pagination a[href='<?= $base ?>/<?= $prefix ?>/powerbreaks']").each(function(){ 
			this.href = this.href.replace("<?= $base ?>/<?= $prefix ?>/powerbreaks", "<?= $base ?>/<?= $prefix ?>/powerbreaks?page=1");
		});
<?php } ?>

	});
	<?php $this->Html->scriptEnd(); ?>
