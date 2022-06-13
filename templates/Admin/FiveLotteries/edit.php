<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\FiveLottery $fiveLottery
 */
?>
<?php // Baked at 2022.03.11. 14:39:49  ?>
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
		<div class="edit col-sm-10">
			<div class="card card-lightblue">
				<div class="card-header">
					<h3 class="card-title"><?= __('Edit') ?>: <?= $title ?><i id="card-loader-icon" class="icon-spin4 animate-spin" style="font-size: 24px; opacity: 1; color: white; font-weight: bold;"></i></h3>
				</div><!-- /.card-header -->

				<!-- form start -->
				<?= $this->Form->create($fiveLottery, ['id' => 'main-form', 'class'=>'form-horizontal']) ?>
			  
					<!-- card-body -->
					<div class="card-body" style="opacity: 0;">
						<!-- 4.a. integer -->
						<div class="form-group row">
							<label for="year" class="col-sm-2 col-form-label"><?= __('Year') ?>:</label>
							<div class="input-group col-xs-12 col-sm-10 col-md-8 col-lg-4 col-xl-3">
								<?= $this->Form->control('year', ['type' => 'number', 'class' => 'form-control number', 'label' => false, 'templates'=>[ 'inputContainer' => '{{content}}', 'inputContainerError' => '{{content}}{{error}}'], 'empty' => true, "required" => true]) ?>
							</div>
						</div>

						<!-- 4.a. integer -->
						<div class="form-group row">
							<label for="week" class="col-sm-2 col-form-label"><?= __('Week') ?>:</label>
							<div class="input-group col-xs-12 col-sm-10 col-md-8 col-lg-4 col-xl-3">
								<?= $this->Form->control('week', ['type' => 'number', 'class' => 'form-control number', 'label' => false, 'templates'=>[ 'inputContainer' => '{{content}}', 'inputContainerError' => '{{content}}{{error}}'], 'empty' => true, "required" => true]) ?>
							</div>
						</div>

						<?php // https://tempusdominus.github.io/bootstrap-4/Usage/ ?>
						<!-- 3. date -->
						<div class="form-group row">
							<label for="pull-date" class="col-sm-2 col-form-label"><?= __('Pull Date') ?>:</label>
							<div class="col-md-10 col-sm-10 col-xs-10">
								<div class="input-group date" id="pull-date" data-target-input="nearest">
									<?= $this->Form->control('pull_date', ['type'=>'text', 'label'=>false, 'placeholder' => __('Pull Date'), 'class'=>'form-control datetimepicker-input', 'data-target'=>'#pull-date', 'autocomplete'=>'off', 'data-validity-message'=>__('This field cannot be left empty'), 'empty' => true, "required" => true]); ?>
									<div class="input-group-append" data-target="#pull-date" data-toggle="datetimepicker">
										<div class="input-group-text"><i class="icon-calendar"></i></div>
									</div>
								</div>
							</div>
						</div>

						<!-- 4.a. integer -->
						<div class="form-group row">
							<label for="results -5" class="col-sm-2 col-form-label"><?= __('Results  5') ?>:</label>
							<div class="input-group col-xs-12 col-sm-10 col-md-8 col-lg-4 col-xl-3">
								<?= $this->Form->control('results _5', ['type' => 'number', 'class' => 'form-control number', 'label' => false, 'templates'=>[ 'inputContainer' => '{{content}}', 'inputContainerError' => '{{content}}{{error}}'], 'empty' => true, "required" => true]) ?>
							</div>
						</div>

						<!-- 4.a. integer -->
						<div class="form-group row">
							<label for="results -5-price" class="col-sm-2 col-form-label"><?= __('Results  5 Price') ?>:</label>
							<div class="input-group col-xs-12 col-sm-10 col-md-8 col-lg-4 col-xl-3">
								<?= $this->Form->control('results _5_price', ['type' => 'number', 'class' => 'form-control number', 'label' => false, 'templates'=>[ 'inputContainer' => '{{content}}', 'inputContainerError' => '{{content}}{{error}}'], 'empty' => true, "required" => true]) ?>
							</div>
						</div>

						<!-- 4.a. integer -->
						<div class="form-group row">
							<label for="results -4" class="col-sm-2 col-form-label"><?= __('Results  4') ?>:</label>
							<div class="input-group col-xs-12 col-sm-10 col-md-8 col-lg-4 col-xl-3">
								<?= $this->Form->control('results _4', ['type' => 'number', 'class' => 'form-control number', 'label' => false, 'templates'=>[ 'inputContainer' => '{{content}}', 'inputContainerError' => '{{content}}{{error}}'], 'empty' => true, "required" => true]) ?>
							</div>
						</div>

						<!-- 4.a. integer -->
						<div class="form-group row">
							<label for="results -4-price" class="col-sm-2 col-form-label"><?= __('Results  4 Price') ?>:</label>
							<div class="input-group col-xs-12 col-sm-10 col-md-8 col-lg-4 col-xl-3">
								<?= $this->Form->control('results _4_price', ['type' => 'number', 'class' => 'form-control number', 'label' => false, 'templates'=>[ 'inputContainer' => '{{content}}', 'inputContainerError' => '{{content}}{{error}}'], 'empty' => true, "required" => true]) ?>
							</div>
						</div>

						<!-- 4.a. integer -->
						<div class="form-group row">
							<label for="results -3" class="col-sm-2 col-form-label"><?= __('Results  3') ?>:</label>
							<div class="input-group col-xs-12 col-sm-10 col-md-8 col-lg-4 col-xl-3">
								<?= $this->Form->control('results _3', ['type' => 'number', 'class' => 'form-control number', 'label' => false, 'templates'=>[ 'inputContainer' => '{{content}}', 'inputContainerError' => '{{content}}{{error}}'], 'empty' => true, "required" => true]) ?>
							</div>
						</div>

						<!-- 4.a. integer -->
						<div class="form-group row">
							<label for="results -3-price" class="col-sm-2 col-form-label"><?= __('Results  3 Price') ?>:</label>
							<div class="input-group col-xs-12 col-sm-10 col-md-8 col-lg-4 col-xl-3">
								<?= $this->Form->control('results _3_price', ['type' => 'number', 'class' => 'form-control number', 'label' => false, 'templates'=>[ 'inputContainer' => '{{content}}', 'inputContainerError' => '{{content}}{{error}}'], 'empty' => true, "required" => true]) ?>
							</div>
						</div>

						<!-- 4.a. integer -->
						<div class="form-group row">
							<label for="results -2" class="col-sm-2 col-form-label"><?= __('Results  2') ?>:</label>
							<div class="input-group col-xs-12 col-sm-10 col-md-8 col-lg-4 col-xl-3">
								<?= $this->Form->control('results _2', ['type' => 'number', 'class' => 'form-control number', 'label' => false, 'templates'=>[ 'inputContainer' => '{{content}}', 'inputContainerError' => '{{content}}{{error}}'], 'empty' => true, "required" => true]) ?>
							</div>
						</div>

						<!-- 4.a. integer -->
						<div class="form-group row">
							<label for="results -2-price" class="col-sm-2 col-form-label"><?= __('Results  2 Price') ?>:</label>
							<div class="input-group col-xs-12 col-sm-10 col-md-8 col-lg-4 col-xl-3">
								<?= $this->Form->control('results _2_price', ['type' => 'number', 'class' => 'form-control number', 'label' => false, 'templates'=>[ 'inputContainer' => '{{content}}', 'inputContainerError' => '{{content}}{{error}}'], 'empty' => true, "required" => true]) ?>
							</div>
						</div>

						<!-- 4.a. tinyinteger -->
						<div class="form-group row">
							<label for="number-1" class="col-sm-2 col-form-label"><?= __('Number 1') ?>:</label>
							<div class="input-group col-xs-12 col-sm-10 col-md-8 col-lg-4 col-xl-3">
								<?= $this->Form->control('number_1', ['type' => 'number', 'class' => 'form-control number', 'label' => false, 'templates'=>[ 'inputContainer' => '{{content}}', 'inputContainerError' => '{{content}}{{error}}'], 'empty' => true, "required" => true]) ?>
							</div>
						</div>

						<!-- 4.a. tinyinteger -->
						<div class="form-group row">
							<label for="number-2" class="col-sm-2 col-form-label"><?= __('Number 2') ?>:</label>
							<div class="input-group col-xs-12 col-sm-10 col-md-8 col-lg-4 col-xl-3">
								<?= $this->Form->control('number_2', ['type' => 'number', 'class' => 'form-control number', 'label' => false, 'templates'=>[ 'inputContainer' => '{{content}}', 'inputContainerError' => '{{content}}{{error}}'], 'empty' => true, "required" => true]) ?>
							</div>
						</div>

						<!-- 4.a. tinyinteger -->
						<div class="form-group row">
							<label for="number-3" class="col-sm-2 col-form-label"><?= __('Number 3') ?>:</label>
							<div class="input-group col-xs-12 col-sm-10 col-md-8 col-lg-4 col-xl-3">
								<?= $this->Form->control('number_3', ['type' => 'number', 'class' => 'form-control number', 'label' => false, 'templates'=>[ 'inputContainer' => '{{content}}', 'inputContainerError' => '{{content}}{{error}}'], 'empty' => true, "required" => true]) ?>
							</div>
						</div>

						<!-- 4.a. tinyinteger -->
						<div class="form-group row">
							<label for="number-4" class="col-sm-2 col-form-label"><?= __('Number 4') ?>:</label>
							<div class="input-group col-xs-12 col-sm-10 col-md-8 col-lg-4 col-xl-3">
								<?= $this->Form->control('number_4', ['type' => 'number', 'class' => 'form-control number', 'label' => false, 'templates'=>[ 'inputContainer' => '{{content}}', 'inputContainerError' => '{{content}}{{error}}'], 'empty' => true, "required" => true]) ?>
							</div>
						</div>

						<!-- 4.a. tinyinteger -->
						<div class="form-group row">
							<label for="number-5" class="col-sm-2 col-form-label"><?= __('Number 5') ?>:</label>
							<div class="input-group col-xs-12 col-sm-10 col-md-8 col-lg-4 col-xl-3">
								<?= $this->Form->control('number_5', ['type' => 'number', 'class' => 'form-control number', 'label' => false, 'templates'=>[ 'inputContainer' => '{{content}}', 'inputContainerError' => '{{content}}{{error}}'], 'empty' => true, "required" => true]) ?>
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
		],
		['block' => true]);


	$this->Html->script(
		[
			"JeffAdmin./plugins/moment/moment.min",
			"JeffAdmin./plugins/moment/locale/hu",
			"JeffAdmin./plugins/bootstrap4-datetime-picker-rails-master/vendor/assets/javascripts/tempusdominus-bootstrap-4.min",
		],
		['block' => 'scriptBottom']
	);
?>

<?php $this->Html->scriptStart(['block' => 'javaScriptBottom']); ?>
		
		$(document).ready( function(){

			$('input[type="checkbox"]').iCheck({
				handle: 'checkbox',
				checkboxClass: 'icheckbox_flat-blue'
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
			
			$('#pull-date').datetimepicker({
				locale: moment.locale("hu"),	
				format: 'L',
<?php //if(isset($fiveLottery->pull_date) && $fiveLottery->pull_date != '00:00:00' && $fiveLottery->pull_date != '0:' ){ ?>
<?php if(!empty($fiveLottery->pull_date)){ ?>
				defaultDate: moment("<?= FrozenTime::parse($fiveLottery->pull_date)->i18nFormat('yyyy-MM-dd') ?>", "YYYY-MM-DD"),
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

