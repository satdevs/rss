<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Restaurant[]|\Cake\Collection\CollectionInterface $restaurants
 */
?>
<style>
	.rotate {

	  transform: rotate(-90deg);

	  /* Legacy vendor prefixes that you probably don't need... */

	  /* Safari */
	  -webkit-transform: rotate(-90deg);

	  /* Firefox */
	  -moz-transform: rotate(-90deg);

	  /* IE */
	  -ms-transform: rotate(-90deg);

	  /* Opera */
	  -o-transform: rotate(-90deg);

	  /* Internet Explorer */
	  filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);

	}
</style>
<?php 
	use Cake\Core\Configure;
	use Cake\Utility\Text;
	use Cake\I18n\FrozenTime;
	
	//$session 			= $this->getRequest()->getSession();
	//$prefix 			= strtolower( $this->request->getParam('prefix') );	
	//$controller 		= $this->request->getParam('controller');	// for DB click on <tr>
	//$action 			= $this->request->getParam('action');		// for DB click on <tr>
	//$passedArgs 		= $this->request->getParam('pass');			// for DB click on <tr>
	
	$config = Configure::read('Theme.' . $prefix);	
	//-------> More config from \config\jeffadmin.php <------
	$config['index_show_id'] 			= false;
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

	if(!isset($layoutRestaurantsLastId)){
		$layoutRestaurantsLastId = 0;
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
			  
				<?php //= __('Restaurants') ?>	
				<div class="card-body table-responsive p-0 restaurants">
<?php //debug($session->read()); ?>
					<table class="table table-hover table-striped table-bordered">
						<thead>
							<tr>
								<th class="row-id-anchor"></th>
								<th style="padding-right: 3px; padding-left: 3px;">&nbsp;</th>
<?php if(isset($config['index_show_id']) && $config['index_show_id']){ ?>
								<th class="id integer"><?= $this->Paginator->sort('id') ?></th>
<?php } ?>
								<th class="days string"><?= $this->Paginator->sort('days_text', 'Napok') ?></th>
								<th class="text string">
									<?= $this->Paginator->sort('menu_from_to', 'tól-ig') ?> • <?= $this->Paginator->sort('text', 'Menü') ?> • (<?= $this->Paginator->sort('date_from', 'Tól') ?>&nbsp;-&nbsp;<?= $this->Paginator->sort('date_to', 'Ig') ?>)
								</th>
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
					<?php foreach ($restaurants as $restaurant): ?>
							<?php 
								$active = '';
								$activeText = '';
								if($restaurant->date_from <= FrozenTime::now() && $restaurant->date_to <= FrozenTime::now()){
									$active = 'background-color: #f77;';
									$activeText = 'Lejárt';
								}
								if($restaurant->date_from <= FrozenTime::now() && $restaurant->date_to >= FrozenTime::now()){
									$active = 'background-color: #7f7;';
									$activeText = 'Aktív';
								}
								if($restaurant->date_from >= FrozenTime::now() && $restaurant->date_to >= FrozenTime::now()){
									$active = 'background-color: #ccf;';
									$activeText = 'Új';
								}								
							?>
							<tr row-id="<?= $restaurant->id ?>"<?php if($restaurant->id == $layoutRestaurantsLastId){ echo ' class="table-tr-last-id"'; } ?>  prefix="<?= $prefix ?>" controller="<?= $controller ?>" action="<?= $action ?>" aria-expanded="true">
								<td class="row-id-anchor" value="<?= $restaurant->id ?>"><a class="anchor" name="<?= $restaurant->id ?>"></a></td><!-- bake-0 -->
								<td style="vertival-align: middle; width: 5px; font-size: 16px; font-weight: bold; padding-right: 3px; padding-left: 3px; <?= $active ?>" name="active"><div style="margin-top: 20px;" class="rotate"><?= $activeText ?></div></td><!-- bake-3 -->
<?php if(isset($config['index_show_id']) && $config['index_show_id']){ ?>
								<td class="id integer" name="id" value="<?= $this->Number->format($restaurant->id) ?>"><?= $this->Number->format($restaurant->id) ?></td><!-- bake-3 -->
<?php } ?>
								<td class="days string" name="days"><br>
									<?= $this->Text->autoParagraph($restaurant->days_text) ?>
								</td><!-- bake-2 -->
								<td class="name string" name="name">
									<b><?= h($restaurant->menu_from_to) ?></b> (<?= h($restaurant->date_from) ?>&nbsp;-&nbsp;<?= h($restaurant->date_to) ?>)<br>
									<?= $this->Text->autoParagraph($restaurant->text) ?>
								</td><!-- bake-2 -->

<?php if(isset($config['index_show_created']) && $config['index_show_created'] || isset($config['index_show_modified']) && $config['index_show_modified']){ ?>
								<td class="datetime created-modified">
<?php 	if(isset($config['index_show_created']) && $config['index_show_created']){ ?>
									<?= h($restaurant->created) ?>
<?php 	} ?>
<?php 	if(isset($config['index_show_created']) && $config['index_show_created'] && isset($config['index_show_modified']) && $config['index_show_modified']){ ?>
									<br>
<?php 	} ?>
<?php 	if(isset($config['index_show_modified']) && $config['index_show_modified']){ ?>
									<?= h($restaurant->modified) ?>
<?php 	} ?>
								</td>
<?php } ?>


<?php if(isset($config['index_show_actions']) && $config['index_show_actions']){ ?>
								<td class="actions text-center">
<?php 	if(isset($config['index_enable_view']) && $config['index_enable_view']){ ?>					  
									<?= $this->Html->link('<i class="fas fa-eye"></i>', ['action' => 'view', $restaurant->id], ['escape' => false, 'class' => 'btn btn-sm bg-gradient-warning action-button-view', 'data-bs-tooltip'=>'tooltip', 'data-bs-placement'=>'top', 'title' => __('View this record')]) ?>
<?php 	} ?>
<?php 	if(isset($config['index_enable_edit']) && $config['index_enable_edit']){ ?>					  
									<?= $this->Html->link('<i class="fas fa-edit"></i>', ['action' => 'edit', $restaurant->id], ['escape' => false, 'class' => 'btn btn-sm bg-gradient-success action-button-edit', 'data-bs-tooltip'=>'tooltip', 'data-bs-placement'=>'top', 'title' => __('Edit this record')]) ?>
<?php 	} ?>			
<?php 	if(isset($config['index_enable_delete']) && $config['index_enable_delete']){ ?>					  
									<?php //= $this->Form->postLink('<i class="fas fa-remove"></i>', ['action' => 'delete', $restaurant->id], ['escape' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $restaurant->id), 'class' => 'btn btn-sm bg-gradient-danger action-button-delete']) ?>						
									<?= $this->Form->postLink('', ['action' => 'delete', $restaurant->id], ['class'=>'crose-btn hide-postlink action-button-delete']) ?>
									<a href="javascript:;" class="btn btn-sm btn-danger delete postlink-delete" data-bs-tooltip="tooltip" data-bs-placement="top" title="<?= __("Delete this record!") ?>" text="<?= h($restaurant->name) ?>" subText="<?= __("You will not be able to revert this!") ?>" confirmButtonText="<?= __("Yes, delete it!") ?>" cancelButtonText="<?= __("Cancel") ?>"><i class="icon-minus"></i></a>
									
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
<?php $url = $this->Url->build(['controller' => 'Restaurants', 'action' => $config['index_db_click_action']]); ?>
		$('tr').dblclick( function(){
<?php /* window.location.href = '/<?= $prefix ?>/restaurants/<?= $config['index_db_click_action'] ?>/'+$(this).attr('row-id'); */ ?>
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
		$(".pagination a[href^='<?= $base ?>/<?= $prefix ?>/restaurants?sort=']").each(function(){ 
			this.href = this.href.replace("<?= $base ?>/<?= $prefix ?>/restaurants?sort=", "<?= $base ?>/<?= $prefix ?>/restaurants?page=1&sort=");
		});
		$(".pagination a[href='<?= $base ?>/<?= $prefix ?>/restaurants']").each(function(){ 
			this.href = this.href.replace("<?= $base ?>/<?= $prefix ?>/restaurants", "<?= $base ?>/<?= $prefix ?>/restaurants?page=1");
		});
<?php } ?>

	});
	<?php $this->Html->scriptEnd(); ?>
