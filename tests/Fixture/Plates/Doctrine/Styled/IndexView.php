<h1>Users</h1>

<?php if (isset($alert)): ?>
  <div><?= $alert ?></div>
<?php endif ?>

<div>
  <a href="<?= base_url('users/create') ?>">Create New User</a>
</div>

<div>
<table>
  <thead>
    <tr>
      <th>Email</th>
      <th>Name</th>
      <th>Year</th>
      <th>Admin</th>
      <th>Remarks</th>
      <th>Created At</th>
      <th>Updated At</th>
      <th>Deleted At</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($items as $item): ?>
      <tr>
        <td><?= $item->get_email() ?></td>
        <td><?= $item->get_name() ?></td>
        <td><?= $item->get_year() ?></td>
        <td><?= $item->get_admin() ?></td>
        <td><?= $item->get_remarks() ?></td>
        <td><?= $item->get_created_at() ?></td>
        <td><?= $item->get_updated_at() ?></td>
        <td><?= $item->get_deleted_at() ?></td>
        <td>
          <span>
            <a href="<?= base_url('users/edit/' . $item->get_id()) ?>">Edit</a>
          </span>
          <span>
            <?= form_open('users/delete/' . $item->get_id()) ?>
              <?= form_hidden('_method', 'DELETE') ?>
              <a href="javascript:void(0)" onclick="trash(this.parentElement)">Delete</a>
            <?= form_close() ?>
          </span>
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
    const text = 'Do you want to delete the selected user?'

    if (confirm(text))
    {
      self.submit()
    }
  }
</script>