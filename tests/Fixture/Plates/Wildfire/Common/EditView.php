<h1>Update User</h1>

<?= form_open('users/edit/' . $item->id) ?>
  <?= form_hidden('_method', 'PUT') ?>

  <div>
    <?= form_label('Email') ?>
    <?= form_input('email', set_value('email', $item->email)) ?>
    <?= form_error('email', '<div><span>', '</span></div>') ?>
  </div>

  <div>
    <?= form_label('Name') ?>
    <?= form_input('name', set_value('name', $item->name)) ?>
    <?= form_error('name', '<div><span>', '</span></div>') ?>
  </div>

  <div>
    <?= form_label('Year') ?>
    <?= form_input('year', set_value('year', $item->year)) ?>
    <?= form_error('year', '<div><span>', '</span></div>') ?>
  </div>

  <div>
    <?= form_label('Admin') ?>
    <div>
      <?= form_checkbox('admin', true, set_value('admin', $item->admin)) ?>
    </div>
    <?= form_error('admin', '<div><span>', '</span></div>') ?>
  </div>

  <div>
    <?= form_label('Remarks') ?>
    <?= form_input('remarks', set_value('remarks', $item->remarks)) ?>
    <?= form_error('remarks', '<div><span>', '</span></div>') ?>
  </div>

  <?php if (isset($error)): ?>
    <div><?= $error ?></div>
  <?php endif ?>

  <?= anchor('users', 'Cancel') ?>
  <?= form_submit(null, 'Update') ?>
<?= form_close() ?>