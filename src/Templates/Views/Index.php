<?php $this->load->view('layout/header'); ?>
	<h1>
		<i class="fa fa-lg fa-list"></i> 
		{{ plural | title }}
	</h1>
	<div class="{{ bootstrap.textRight }}">
		<a class="{{ bootstrap.button }}" href="<?php echo base_url('{{ plural }}/create'); ?>">
			Create A New {{ singular | title }}
		</a>
	</div>
	<?php if (${{ plural }}): ?>
		<table class="{{ bootstrap.table }}">
			<thead>
				<tr>
{% for column in columns if not column.isPrimaryKey and column.field != 'datetime_created' and column.field != 'datetime_updated' %}
					<td>{{ column.field | replace({'_id': '', '_': ' '}) | title }}</td>
{% endfor %}
				</tr>
			</thead>
			<tbody>
				<?php foreach (${{ plural }} as ${{ singular }}): ?>
					<tr>
{% for column in columns if not column.isPrimaryKey and column.field != 'datetime_created' and column.field != 'datetime_updated' %}
{% set accessor = isCamel ? camel[column.field]['accessor'] : underscore[column.field]['accessor'] %}
{% if column.field in foreignKeys|keys %}
						<td><?php echo ${{ singular }}->{{ accessor }}()->{{ primaryKeys[column.field] }}(); ?></td>
{% else %}
						<td><?php echo ${{ singular }}->{{ accessor }}(); ?></td>
{% endif %}
{% endfor %}
						<td>
							<a href="<?php echo base_url('{{ plural }}/edit/' . ${{ singular }}->{{ primaryKey }}()); ?>">
								<i class="fa fa-edit fa-2x"></i>
							</a>
							<a href="<?php echo base_url('{{ plural }}/delete/' . ${{ singular }}->{{ primaryKey }}()); ?>">
								<i class="fa fa-trash fa-2x"></i>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php echo $links; ?>
	<?php elseif ($this->input->get('keyword')): ?>
		Your search - <b><?php echo $this->input->get('keyword') ?></b> - did not match any {{ plural | lower | replace({'_': ' '}) }}.
	<?php else: ?>
		There are no {{ plural | lower | replace({'_': ' '}) }} that are currently available.
	<?php endif; ?>
<?php $this->load->view('layout/footer'); ?>