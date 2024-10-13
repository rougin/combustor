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
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($items as $item): ?>
      <tr>
        <td><?= $item->email ?></td>
        <td><?= $item->name ?></td>
        <td><?= $item->year ?></td>
        <td><?= $item->admin ?></td>
        <td><?= $item->remarks ?></td>
        <td><?= $item->created_at ?></td>
        <td><?= $item->updated_at ?></td>
        <td><?= $item->deleted_at ?></td>
        <td>
          <div>
            <span>
              <a href="<?= base_url('users/edit/' . $item->id) ?>">Edit</a>
            </span>
            <span>
              <?= form_open('users/delete/' . $item->id) ?>
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
    const text = 'Do you want to delete the selected user?'

    if (confirm(text))
    {
      self.submit()
    }
  }
</script>