<h1>Users</h1>

<div><?= isset($alert) ? $alert : '' ?></div>

<div>
  <a href="<?= base_url('users/create') ?>">Create New User</a>
</div>

<div>
<table>
  <thead>
    <tr>
      <th>Name</th>
      <th>Age</th>
      <th>Gender</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($items as $item): ?>
      <tr>
        <td><?= $item->name ?></td>
        <td><?= $item->age ?></td>
        <td><?= $item->gender ?></td>
        <td>
          <span>
            <a href="<?= base_url('users/edit/' . $item->id) ?>">Edit</a>
          </span>
          <span>
            <?= form_open('users/delete/' . $item->id) ?>
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