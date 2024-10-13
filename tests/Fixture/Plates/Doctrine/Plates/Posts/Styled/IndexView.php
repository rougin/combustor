<h1>Posts</h1>

<?php if (isset($alert)): ?>
  <div class="alert alert-success"><?= $alert ?></div>
<?php endif ?>

<div class="my-3">
  <a class="btn btn-primary" href="<?= base_url('posts/create') ?>">Create New Post</a>
</div>

<div>
<table class="table table-hover">
  <thead>
    <tr>
      <th>Title</th>
      <th>Text</th>
      <th>User Id</th>
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
        <td><?= $item->get_user_id() ?></td>
        <td><?= $item->get_created_at() ?></td>
        <td><?= $item->get_updated_at() ?></td>
        <td><?= $item->get_deleted_at() ?></td>
        <td>
          <div class="d-flex">
            <span>
              <a class="btn btn-secondary btn-sm" href="<?= base_url('posts/edit/' . $item->get_id()) ?>">Edit</a>
            </span>
            <span>
              <?= form_open('posts/delete/' . $item->get_id()) ?>
                <?= form_hidden('_method', 'DELETE') ?>
                <a class="btn btn-link btn-sm text-danger text-decoration-none" href="javascript:void(0)" onclick="trash(this.parentElement)">Delete</a>
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