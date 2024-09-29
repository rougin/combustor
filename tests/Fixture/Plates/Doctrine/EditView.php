<h1>Update User</h1>

<?= form_open('users/edit/' . $item->get_id()) ?>
  <?= form_hidden('_method', 'PUT') ?>

  <div>
    <?= form_label('Name') ?>
    <?= form_input('name', set_value('name', $item->get_name())) ?>
    <?= form_error('name', '<div><span>', '</span></div>') ?>
  </div>

  <div>
    <?= form_label('Age') ?>
    <?= form_input('age', set_value('age', $item->get_age())) ?>
    <?= form_error('age', '<div><span>', '</span></div>') ?>
  </div>

  <div>
    <?= form_label('Gender') ?>
    <?= form_input('gender', set_value('gender', $item->get_gender())) ?>
    <?= form_error('gender', '<div><span>', '</span></div>') ?>
  </div>

  <div>
    <?= form_label('Remarks') ?>
    <?= form_input('remarks', set_value('remarks', $item->get_remarks())) ?>
    <?= form_error('remarks', '<div><span>', '</span></div>') ?>
  </div>

  <div><?= isset($error) ? $error : '' ?></div>

  <?= anchor('users', 'Cancel') ?>
  <?= form_submit(null, 'Update') ?>
<?= form_close() ?>