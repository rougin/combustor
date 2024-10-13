<h1>Posts</h1>

<?php if (isset($alert)): ?>
  <div><?= $alert ?></div>
<?php endif ?>

<div>
  <a href="<?= base_url('posts/create') ?>">Create New Post</a>
</div>

<div>
<table>
  <thead>
    <tr>
      <th>Title</th>
      <th>Text</th>
      <th>User</th>
      <th>Created At</th>
      <th>Updated At</th>
      <th>Deleted At</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($items as $item): ?>
      <tr>
        <td><?= $item->get_title() ?></td>
        <td><?= $item->get_text() ?></td>
        <td><?= $item->get_user()->get_id() ?></td>
        <td><?= $item->get_created_at() ? $item->get_created_at()->format('Y-m-d H:i:s') : '' ?></td>
        <td><?= $item->get_updated_at() ? $item->get_updated_at()->format('Y-m-d H:i:s') : '' ?></td>
        <td><?= $item->get_deleted_at() ? $item->get_deleted_at()->format('Y-m-d H:i:s') : '' ?></td>
        <td>
          <div>
            <span>
              <a href="<?= base_url('posts/edit/' . $item->get_id()) ?>">Edit</a>
            </span>
            <span>
              <?= form_open('posts/delete/' . $item->get_id()) ?>
                <?= form_hidden('_method', 'DELETE') ?>
                <a href="javascript:void(0)" onclick="trash(this.parentElement)">Delete</a>
              <?= form_close() ?>
            </span>
          </div>
        </td>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>
  <?= $links ?>
</div>

<script>
  trash = function (self)
  {
    const text = 'Do you want to delete the selected post?'

    if (confirm(text))
    {
      self.submit()
    }
  }
</script>