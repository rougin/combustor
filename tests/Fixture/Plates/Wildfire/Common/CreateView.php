<h1>Create New User</h1>

<?= form_open('users/create') ?>
  <div>
    <?= form_label('Email') ?>
    <?= form_input('email', set_value('email')) ?>
    <?= form_error('email', '<div><span>', '</span></div>') ?>
  </div>

  <div>
    <?= form_label('Name') ?>
    <?= form_input('name', set_value('name')) ?>
    <?= form_error('name', '<div><span>', '</span></div>') ?>
  </div>

  <div>
    <?= form_label('Year') ?>
    <?= form_input('year', set_value('year')) ?>
    <?= form_error('year', '<div><span>', '</span></div>') ?>
  </div>

  <div>
    <?= form_label('Admin') ?>
    <?= form_input('admin', set_value('admin')) ?>
    <?= form_error('admin', '<div><span>', '</span></div>') ?>
  </div>

  <div>
    <?= form_label('Remarks') ?>
    <?= form_input('remarks', set_value('remarks')) ?>
    <?= form_error('remarks', '<div><span>', '</span></div>') ?>
  </div>

  <div><?= isset($error) ? $error : '' ?></div>

  <?= anchor('users', 'Cancel') ?>
  <?= form_submit(null, 'Create') ?>
<?= form_close() ?>