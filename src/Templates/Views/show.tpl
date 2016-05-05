<?php $this->load->view('layout/header'); ?>
	<h1>
		<i class="fa fa-lg fa-list"></i> 
		{{ plural | title }}
	</h1>
	<div class="{{ bootstrap.textRight }}">
		<a class="{{ bootstrap.button }}" href="<?php echo base_url('{{ plural }}'); ?>">
			Cancel
		</a>
	</div>
{% for column in columns if not column.isPrimaryKey and column.field != 'datetime_created' and column.field != 'datetime_updated' %}
{% set accessor = isCamel ? camel[column.field]['accessor'] : underscore[column.field]['accessor'] %}
	<div class="{{ bootstrap.formGroup }}">
{% if column.field in foreignKeys|keys %}
{% set field = foreignKeys[column.field] | lower | capitalize | replace({'_': ' '}) %}
{% elseif column.field != 'datetime_created' and column.field != 'datetime_updated' %}
{% set field = column.field | capitalize | replace({'_': ' '}) %}
{% endif %}
		<?php echo form_label('{{ field }}', '{{ column.field }}', array('class' => '{{ bootstrap.label }}')); ?>
		<div class="">
{% if column.field in foreignKeys|keys %}
			<input type="text" class="{{ bootstrap.formControl }}" value="<?php echo ${{ singular }}->{{ accessor }}()->{{ primaryKeys[column.field] }}(); ?>" disabled>
{% else %}
			<input type="text" class="{{ bootstrap.formControl }}" value="<?php echo ${{ singular }}->{{ accessor }}(); ?>" disabled>
{% endif %}
		</div>
	</div>
{% endfor %}
<?php $this->load->view('layout/footer'); ?>