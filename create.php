<?php include_once('./config.php')

?>
<?php include './header.php' ?>

<div class='create'>
  <form action='post.php' method='post' enctype='multipart/form-data'>
    <p>
      <label>タイトル:<input type='text' size="64" name='title' placeholder="64文字以内" maxlength="64" /></label>
    </p>
    <p>
      <label>概要:<textarea rows="20" name='overview' cols="80"></textarea></label>
    </p>
    <input type="hidden" name="act" value="create_room" />
    <input type='submit' value='作成' />
  </form>
</div>

<?php include './footer.php' ?>
