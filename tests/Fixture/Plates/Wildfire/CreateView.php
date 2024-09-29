<h1>Create New User</h1>

<?= form_open('users/create') ?>
  <div>
    <?= form_label('Name') ?>
    <?= form_input('name', set_value('name')) ?>
    <?= form_error('name', '<div><span>', '</span></div>') ?>
  </div>

  <div>
    <?= form_label('Age') ?>
    <?= form_input('age', set_value('age')) ?>
    <?= form_error('age', '<div><span>', '</span></div>') ?>
  </div>

  <div>
    <?= form_label('Gender') ?>
    <?= form_input('gender', set_value('gender')) ?>
    <?= form_error('gender', '<div><span>', '</span></div>') ?>
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