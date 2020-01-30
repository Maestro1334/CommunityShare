<?php require APPROOT . '/views/inc/header.php'; ?>
<main>
  <div class="container content">
    <h2 class="text-center pt-2">Upload and Export CVS</h2>
    <?php flash('alert'); ?>
    <div class="text-center pt-1">
      <form id="import_cvs" name="import_cvs" action="<?php echo URLROOT; ?>/pages/import" method="post" enctype="multipart/form-data">
        <input type="file" name="filename" id="filename"><br>
        <label class="btn btn-dark custom-file-label" for="filename">Choose file</label>
        <div class="form-group">
          <button class="btn btn-primary button-loading" data-loading-text="Uploading..." id="submit" type="submit" name="submit">Import CVS</button>
        </div>
      </form>
    </div>
  </div>


  <div class="mt-4">
    <table class="table table-bordered table-secondary">
      <thead class="thead-dark">
        <tr>
          <th scope="col">#</th>
          <th scope="col">Name</th>
          <th scope="col">Data</th>
        </tr>
      </thead>
      <?php $i = 1; foreach($data as $row) { ?>
        <tbody>
        <tr>
          <th scope="row"><?php echo $i ?></th>
          <td><?php echo $row->name ?></td>
          <td><?php echo $row->data ?></td>
        </tr>
      <?php $i++; } ?>
        </tbody>
    </table>
  </div>
  <div class="text-center export-button-div">
    <form id="export_cvs" name="export_cvs" class="form-horizontal" action="<?php echo URLROOT; ?>/pages/export" method="post" enctype="multipart/form-data">
      <input type="submit" name="Export" class="btn btn-success" value="Export to CVS">
    </form>
  </div>
</main>
<?php require APPROOT . '/views/inc/footer.php'; ?>

