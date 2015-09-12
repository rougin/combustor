<?php $this->load->view('layout/header'); ?>
	<h1>
		<i class="fa fa-lg fa-list"></i> 
		{{ plural | title }}
	</h1>
	<?php echo form_open('{{ plural }}/edit/' . ${{ singular }}->{{ primaryKey }}(), 'class=""'); ?>
		<div class="{{ bootstrap.textRight }}">
			<button type="submit" class="{{ bootstrap.buttonPrimary }}">
				Submit
			</button>
			<a class="{{ bootstrap.button }}" href="<?php echo base_url('{{ plural }}'); ?>">
				Cancel
			</a>
		</div>
{% for column in columns if not column.isPrimaryKey and column.field != 'datetime_created' and column.field != 'datetime_updated' %}
{% set accessor = isCamel ? camel[column.field]['accessor'] : underscore[column.field]['accessor'] %}
		<div class="{{ bootstrap.formGroup }}">
{% if column.field in foreignKeys|keys %}
{% set field = foreignKeys[column.field ~ '_singular'] | lower | capitalize | replace({'_': ' '}) %}
{% elseif column.field != 'datetime_created' and column.field != 'datetime_updated' %}
{% set field = column.field | capitalize | replace({'_': ' '}) %}
{% endif %}
			<?php echo form_label('{{ field }}', '{{ column.field }}', array('class' => '{{ bootstrap.label }}')); ?>
			<div class="">
{% if column.field in foreignKeys|keys %}
				<?php echo form_dropdown('{{ column.field }}', ${{ foreignKeys[column.field] }}, set_value('{{ column.field }}', ${{ singular }}->{{ accessor }}()->{{ primaryKeys[column.field] }}()), 'class="{{ bootstrap
					.formControl }}" {{ column.isNull ? '' : 'required' }}'); ?>
{% elseif column.dataType == 'date' or column.dataType == 'datetime' %}
				<input type="date" name="{{ column.field }}" class="{{ bootstrap.formControl }}" {{ column.isNull ? '' : 'required' }} />
{% else %}
				<?php echo form_input('{{ column.field }}', set_value('{{ column.field }}', ${{ singular }}->{{ accessor }}()), 'class="{{ bootstrap.formControl }}" {{ column.isNull ? '' : 'required' }}'); ?>
{% endif %}
				<?php echo form_error('{{ column.field }}'); ?>
			</div>
		</div>
{% endfor %}
	<?php echo form_close(); ?>
<?php $this->load->view('layout/footer'); ?>