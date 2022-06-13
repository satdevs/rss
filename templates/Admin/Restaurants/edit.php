<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Restaurant $restaurant
 */
?>
s<?php // Baked at 2022.03.17. 09:13:07  ?>
<?php use Cake\Core\Configure; ?>
<?php use Cake\I18n\FrozenTime; ?>
<?php use Cake\I18n\I18n; ?>
<?php 
	$prefix = strtolower( $this->request->getParam('prefix') );	
	$config = Configure::read('Theme.' . $prefix);	
	$config['form_show_counts'] = false;
?>
<?php $locale = I18n::getLocale(); ?>
<?php //$formats = Configure::read('Formats'); ?>
<?php //$format = $formats[$locale]; ?>
		<div class="add col-sm-10">
			<div class="card card-lightblue">
				<div class="card-header">
					<h3 class="card-title"><?= __('Edit') ?>: <?= $title ?><i id="card-loader-icon" class="icon-spin4 animate-spin" style="font-size: 24px; opacity: 1; color: white; font-weight: bold;"></i></h3>
				</div><!-- /.card-header -->

				<!-- form start -->
				<?= $this->Form->create($restaurant, ['id' => 'main-form', 'class'=>'form-horizontal']) ?>
			  
					<!-- card-body -->
					<div class="card-body" style="opacity: 0;">
						<!-- 6. string -->
						<div class="form-group row">
							<label for="name" class="col-sm-2 col-form-label"><?= __('Név') ?>:</label>
							<div class="col-sm-9">
								<?= $this->Form->control('name', ['placeholder' => __(''), 'type'=>'text', 'class'=>'form-control', 'label' => false, 'empty' => true, 'autofocus' => false, "required" => true, 'readonly' => true]) ?>
							</div>
						</div>

						<?php // https://tempusdominus.github.io/bootstrap-4/Usage/ ?>
						<!-- 3. datetime -->
						<div class="form-group row">
							<label for="date-from" class="col-sm-2 col-form-label"><?= __('Dátum tól-ig') ?>:</label>
							<div class="col-md-3 col-sm-3 col-xs-3">
								<div class="input-group datetime" id="date-from" data-target-input="nearest">
									<?= $this->Form->control('date_from', ['type'=>'text', 'label'=>false, 'placeholder' => __('Date From'), 'class'=>'form-control datetimepicker-input', 'data-target'=>'#date-from', 'autocomplete'=>'off', 'data-validity-message'=>__('This field cannot be left empty'), 'empty' => true, "required" => true]); ?>
									<div class="input-group-append" data-target="#date-from" data-toggle="datetimepicker">
										<div class="input-group-text"><i class="icon-calendar"></i></div>
									</div>
								</div>
							</div>

							<div class="col-md-3 col-sm-3 col-xs-3">
								<div class="input-group datetime" id="date-to" data-target-input="nearest">
									<?= $this->Form->control('date_to', ['type'=>'text', 'label'=>false, 'placeholder' => __('Date To'), 'class'=>'form-control datetimepicker-input', 'data-target'=>'#date-to', 'autocomplete'=>'off', 'data-validity-message'=>__('This field cannot be left empty'), 'empty' => true, "required" => true]); ?>
									<div class="input-group-append" data-target="#date-to" data-toggle="datetimepicker">
										<div class="input-group-text"><i class="icon-calendar"></i></div>
									</div>
								</div>
							</div>
						</div>

						<!-- 6. string -->
						<div class="form-group row">
							<label for="menu-from-to" class="col-sm-2 col-form-label"><?= __('Tól-ig szöveggel') ?>:</label>
							<div class="col-sm-9">
								<?= $this->Form->control('menu_from_to', ['placeholder' => __(''), 'type'=>'text', 'class'=>'form-control', 'label' => false, 'empty' => true, 'autofocus' => true, "required" => true]) ?>
							</div>
						</div>

						<!-- 7. text -->
						<div class="form-group row">
							<label for="days-text" class="col-sm-2 col-form-label"><?= __('Heti menü') ?>:</label>
							<div class="col-sm-1">
								<?php $days = "Hétfő:\nKedd:\nSzerda:\nCsütörtök:\nPéntek:\n\nB menü:\nC menü:\nD menü:\nE menü:"; ?>
								<?= $this->Form->textarea('days_text', ['type'=>'textarea', 'class'=>'not-summernote', 'label' => false, 'placeholder'=>__('Place some text here'), 'empty' => true, 'style'=>'width: 100%; height: 249px;', "required" => true ]) ?>
							</div>
							<div class="col-sm-9">
								<?php $content = "* * * ELLENŐRIZNI A TÖBBI BEVITELI MEZŐT IS!!! * * *\n\n* * * ELLENŐRIZNI A TÖBBI BEVITELI MEZŐT IS!!! * * *\n\n* * * ELLENŐRIZNI A TÖBBI BEVITELI MEZŐT IS!!! * * *\n"; ?>
								<?= $this->Form->textarea('text', ['type'=>'textarea', 'class'=>'not-summernote', 'label' => false, 'placeholder'=>__('Place some text here'), 'empty' => true, 'style'=>'width: 100%; height: 249px;', "required" => true ]) ?>
							</div>
						</div>

						<!-- 7. text -->
						<div class="form-group row">
							<label for="prices" class="col-sm-2 col-form-label"><?= __('Árak') ?>:</label>
							<div class="col-sm-10">
								<?php $prices = "A, B, C, D, E menü ára: 1450 Ft\nA menüváltozás jogát fenntartjuk! Csomagolás: 100Ft/doboz"; ?>
								<?= $this->Form->textarea('prices', ['type'=>'textarea', 'class'=>'not-summernote', 'label' => false, 'placeholder'=>__('Place some text here'), 'empty' => true, 'style'=>'width: 100%; height: 70px;', "required" => true ]) ?>
							</div>
						</div>


					</div><!-- /.card-body -->
				
					<div class="card-footer">
						<button type="submit" class="offset-sm-2 btn btn-info" data-bs-tooltip="tooltip" data-bs-placement="top" title="<?= __('Save and back to list') ?>" ><span class="btn-label"><i class="fa fa-save"></i></span> <?= __('Save') ?></button>
						<?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class'=>'btn btn-default', 'role'=>'button', 'escape'=>false, 'data-bs-tooltip'=>'tooltip', 'data-bs-placement'=>'top', 'title' => __('Back to list without save') ] ) ?>
					</div><!-- /.card-footer -->

				<?= $this->Form->end() ?>

            </div>
        </div>

<?php
	$this->Html->css(
		[
			"JeffAdmin./plugins/fontello/css/animation",
			"JeffAdmin./plugins/summernote/summernote-bs4.min",
		],
		['block' => true]);


	$this->Html->script(
		[
			"JeffAdmin./plugins/icheck-1.x/icheck.min",
			"JeffAdmin./plugins/moment/moment.min",
			"JeffAdmin./plugins/moment/locale/hu",
			"JeffAdmin./plugins/bootstrap4-datetime-picker-rails-master/vendor/assets/javascripts/tempusdominus-bootstrap-4.min",
			"JeffAdmin./plugins/bootstrap-input-spinner-master/src/bootstrap-input-spinner",
			"JeffAdmin./plugins/summernote/summernote-bs4.min",
			"JeffAdmin./plugins/summernote/lang/summernote-hu-HU.min",
		],
		['block' => 'scriptBottom']
	);
?>

<?php $this->Html->scriptStart(['block' => 'javaScriptBottom']); ?>
		
		$(document).ready( function(){
			$('.summernote').summernote({
				height: 180,
				lang: 'hu-HU'
			});



<?php 		/* //$("input[type='number']").inputSpinner({ */ ?>
			$(".spinner").inputSpinner({
				decrementButton: "<strong>-</strong>",
				incrementButton: "<strong>+</strong>",
				groupClass: "", 						// css class of the resulting input-group
				buttonsClass: "btn-outline-secondary",
				buttonsWidth: "2.5rem",
				textAlign: "center",
				autoDelay: 500, 						// ms holding before auto value change
				autoInterval: 100, 						// speed of auto value change
				boostThreshold: 10, 					// boost after these steps
				boostMultiplier: "auto" 				// you can also set a constant number as multiplier
			});			
<?php /* https://tempusdominus.github.io/bootstrap-4/Usage/ */ ?>
			
			tooltips = {
				today: 			'<?= __('Go to today') ?>',
				clear: 			'<?= __('Clear selection') ?>',
				close: 			'<?= __('Close the picker') ?>',
				selectMonth: 	'<?= __('Select Month') ?>',
				prevMonth: 		'<?= __('Previous Month') ?>',
				nextMonth: 		'<?= __('Next Month') ?>',
				selectYear: 	'<?= __('Select Year') ?>',
				prevYear: 		'<?= __('Previous Year') ?>',
				nextYear: 		'<?= __('Next Year') ?>',
				selectDecade: 	'<?= __('Select Decade') ?>',
				prevDecade: 	'<?= __('Previous Decade') ?>',
				nextDecade: 	'<?= __('Next Decade') ?>',
				prevCentury: 	'<?= __('Previous Century') ?>',
				nextCentury: 	'<?= __('Next Century') ?>',
				incrementHour: 	'<?= __('Increment Hour') ?>',
				pickHour: 		'<?= __('Pick Hour') ?>',
				decrementHour:	'<?= __('Decrement Hour') ?>',
				incrementMinute:'<?= __('Increment Minute') ?>',
				pickMinute: 	'<?= __('Pick Minute') ?>',
				decrementMinute:'<?= __('Decrement Minute') ?>',
				incrementSecond:'<?= __('Increment Second') ?>',
				pickSecond: 	'<?= __('Pick Second') ?>',
				decrementSecond:'<?= __('Decrement Second') ?>'
			}
			
			$('#date-from').datetimepicker({
				locale: moment.locale("hu"),	
				format: 'L',
<?php //if(isset($restaurant->date_from) && $restaurant->date_from != '00:00:00' && $restaurant->date_from != '0:' ){ ?>
<?php if(!empty($restaurant->date_from)){ ?>
				defaultDate: moment("<?= FrozenTime::parse($restaurant->date_from)->i18nFormat('yyyy-MM-dd') ?>", "YYYY-MM-DD"),
<?php } ?>
				//locale: moment.locale(),
				buttons: {
					showToday: true,
					showClear: true,
					showClose: true
				},				
				//viewDate: true,
				icons: {
					time: "icon-clock",
					date: "icon-calendar",
					up: "icon-up-big",
					down: "icon-down-big",
	                previous: 'icon-left-big',
	                next: 'icon-right-big',
	                today: 'icon-calendar',
	                clear: 'icon-trash-empty',
	                close: 'icon-window-close-o'
				},
				tooltips: tooltips
			});

			$('#date-to').datetimepicker({
				locale: moment.locale("hu"),	
				format: 'L',
<?php //if(isset($restaurant->date_from) && $restaurant->date_from != '00:00:00' && $restaurant->date_from != '0:' ){ ?>
<?php if(!empty($restaurant->date_to)){ ?>
				defaultDate: moment("<?= FrozenTime::parse($restaurant->date_to)->i18nFormat('yyyy-MM-dd') ?>", "YYYY-MM-DD"),
<?php } ?>
				//locale: moment.locale(),
				buttons: {
					showToday: true,
					showClear: true,
					showClose: true
				},				
				//viewDate: true,
				icons: {
					time: "icon-clock",
					date: "icon-calendar",
					up: "icon-up-big",
					down: "icon-down-big",
	                previous: 'icon-left-big',
	                next: 'icon-right-big',
	                today: 'icon-calendar',
	                clear: 'icon-trash-empty',
	                close: 'icon-window-close-o'
				},
				tooltips: tooltips
			});


<?php /*	// ----------- talán ----------
			$("input[data-bootstrap-switch]").each(function(){
				$(this).bootstrapSwitch('state', $(this).prop('checked'));
			});
*/ ?>

			$('#button-submit').click( function(){
				$('#main-form').submit();
			});			

			// --- to bottom ---
			$('.card-body').animate({opacity: '1'}, 500, 'linear');
			$('#card-loader-icon').animate({opacity: '0'}, 1000, 'linear');
			
		});
		
<?php $this->Html->scriptEnd(); ?>

