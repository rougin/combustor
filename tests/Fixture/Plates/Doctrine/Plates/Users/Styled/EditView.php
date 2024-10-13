<h1>Update User</h1>

<?= form_open('users/edit/' . $item->get_id()) ?>
  <?= form_hidden('_method', 'PUT') ?>

  <div class="mb-3">
    <?= form_label('Email', '', ['class' => 'form-label mb-0']) ?>
    <?= form_input(['type' => 'email', 'name' => 'email', 'value' => set_value('email', $item->get_email()), 'class' => 'form-control']) ?>
    <?= form_error('email', '<div><span class="text-danger small">', '</span></div>') ?>
  </div>

  <div class="mb-3">
    <?= form_label('Name', '', ['class' => 'form-label mb-0']) ?>
    <?= form_input('name', set_value('name', $item->get_name()), 'class="form-control"') ?>
    <?= form_error('name', '<div><span class="text-danger small">', '</span></div>') ?>
  </div>

  <div class="mb-3">
    <?= form_label('Year', '', ['class' => 'form-label mb-0']) ?>
    <?= form_input('year', set_value('year', $item->get_year()), 'class="form-control"') ?>
    <?= form_error('year', '<div><span class="text-danger small">', '</span></div>') ?>
  </div>

  <div class="mb-3">
    <?= form_label('Admin', '', ['class' => 'form-label mb-0']) ?>
    <div>
      <?= form_checkbox('admin', true, set_value('admin', $item->is_admin()), 'class="form-check-input"') ?>
    </div>
    <?= form_error('admin', '<div><span class="text-danger small">', '</span></div>') ?>
  </div>

  <div class="mb-3">
    <?= form_label('Remarks', '', ['class' => 'form-label mb-0']) ?>
    <?= form_input('remarks', set_value('remarks', $item->get_remarks()), 'class="form-control"') ?>
    <?= form_error('remarks', '<div><span class="text-danger small">', '</span></div>') ?>
  </div>

  <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif ?>

  <?= anchor('users', 'Cancel', 'class="btn btn-link text-secondary text-decoration-none"') ?>
  <?= form_submit(null, 'Update', 'class="btn btn-primary"') ?>
<?= form_close() ?>