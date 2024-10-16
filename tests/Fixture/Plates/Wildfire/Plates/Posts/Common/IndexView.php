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
        <td><?= $item->title ?></td>
        <td><?= $item->text ?></td>
        <td><?= $item->user_id ?></td>
        <td><?= $item->created_at ?></td>
        <td><?= $item->updated_at ?></td>
        <td><?= $item->deleted_at ?></td>
        <td>
          <div>
            <span>
              <a href="<?= base_url('posts/edit/' . $item->id) ?>">Edit</a>
            </span>
            <span>
              <?= form_open('posts/delete/' . $item->id) ?>
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