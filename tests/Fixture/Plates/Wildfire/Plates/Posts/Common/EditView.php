<h1>Update Post</h1>

<?= form_open('posts/edit/' . $item->id) ?>
  <?= form_hidden('_method', 'PUT') ?>

  <div>
    <?= form_label('Title') ?>
    <?= form_input('title', set_value('title', $item->title)) ?>
    <?= form_error('title', '<div><span>', '</span></div>') ?>
  </div>

  <div>
    <?= form_label('Text') ?>
    <?= form_input('text', set_value('text', $item->text)) ?>
    <?= form_error('text', '<div><span>', '</span></div>') ?>
  </div>

  <div>
    <?= form_label('User') ?>
    <?= form_dropdown('user_id', $users, set_value('user_id', $item->user_id)) ?>
    <?= form_error('user_id', '<div><span>', '</span></div>') ?>
  </div>

  <?php if (isset($error)): ?>
    <div><?= $error ?></div>
  <?php endif ?>

  <?= anchor('posts', 'Cancel') ?>
  <?= form_submit(null, 'Update') ?>
<?= form_close() ?>