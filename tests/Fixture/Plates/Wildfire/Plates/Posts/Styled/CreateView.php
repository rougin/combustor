<h1>Create New Post</h1>

<?= form_open('posts/create') ?>
  <div class="mb-3">
    <?= form_label('Title', '', ['class' => 'form-label mb-0']) ?>
    <?= form_input('title', set_value('title'), 'class="form-control"') ?>
    <?= form_error('title', '<div><span class="text-danger small">', '</span></div>') ?>
  </div>

  <div class="mb-3">
    <?= form_label('Text', '', ['class' => 'form-label mb-0']) ?>
    <?= form_input('text', set_value('text'), 'class="form-control"') ?>
    <?= form_error('text', '<div><span class="text-danger small">', '</span></div>') ?>
  </div>

  <div class="mb-3">
    <?= form_label('User', '', ['class' => 'form-label mb-0']) ?>
    <?= form_dropdown('user_id', $users, set_value('user_id'), 'class="form-control"') ?>
    <?= form_error('user_id', '<div><span class="text-danger small">', '</span></div>') ?>
  </div>

  <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif ?>

  <?= anchor('posts', 'Cancel', 'class="btn btn-link text-secondary text-decoration-none"') ?>
  <?= form_submit(null, 'Create', 'class="btn btn-primary"') ?>
<?= form_close() ?>