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
        <td><?= $item->title ?></td>
        <td><?= $item->text ?></td>
        <td><?= $item->user_id ?></td>
        <td><?= $item->created_at ?></td>
        <td><?= $item->updated_at ?></td>
        <td><?= $item->deleted_at ?></td>
        <td>
          <div class="d-flex">
            <span>
              <a class="btn btn-secondary btn-sm" href="<?= base_url('posts/edit/' . $item->id) ?>">Edit</a>
            </span>
            <span>
              <?= form_open('posts/delete/' . $item->id) ?>
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