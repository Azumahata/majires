<?php include_once('./config.php');
include_once('./get.php');
?>
<?php include './header.php' ?>

<div>
  <p><a href="./create.php">スレを作る</a></p>
<div>

<div class="rooms">
  <table>
     <tr><th>ID</th><th>TITLE</th><th>COMMENTS</th><th>CREATED_AT</th></tr>
     <?php foreach (getRooms() as $row) : ?>
     <tr>
       <td><?php echo $row['id'] ?></td>
       <td><a href='<?php echo getRoomLocation($row['id'])?>'><?php echo $row['title'] ?></a></td>
       <td><?php echo $row['cnt'] ?></td>
       <td><?php echo $row['created_at'] ?></td>
     </tr>
     <?php endforeach ?>
  </table>
</div>
<?php include './footer.php' ?>
