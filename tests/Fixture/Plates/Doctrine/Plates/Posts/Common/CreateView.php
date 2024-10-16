<h1>Create New Post</h1>

<?= form_open('posts/create') ?>
  <div>
    <?= form_label('Title') ?>
    <?= form_input('title', set_value('title')) ?>
    <?= form_error('title', '<div><span>', '</span></div>') ?>
  </div>

  <div>
    <?= form_label('Text') ?>
    <?= form_input('text', set_value('text')) ?>
    <?= form_error('text', '<div><span>', '</span></div>') ?>
  </div>

  <div>
    <?= form_label('User') ?>
    <?= form_dropdown('user_id', $users, set_value('user_id')) ?>
    <?= form_error('user_id', '<div><span>', '</span></div>') ?>
  </div>

  <?php if (isset($error)): ?>
    <div><?= $error ?></div>
  <?php endif ?>

  <?= anchor('posts', 'Cancel') ?>
  <?= form_submit(null, 'Create') ?>
<?= form_close() ?>