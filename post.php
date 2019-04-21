<?php include_once('./config.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  errorRedirect();
}

function createRoom() {
  $pdo = getPDO();
  $pdo->beginTransaction();
  try {
    $stmt = $pdo->prepare('INSERT INTO rooms VALUES(NULL, ?, ?, NOW())');
    $stmt->execute(array($_POST['title'], $_POST['overview']));
    $lastInsertId = $pdo->lastInsertId('id');
    $pdo->commit();

    redirect('./room.php', array('room_id' => $lastInsertId));
  } catch(Exception $e) {
    $pdo->rollBack();
    echo $e->getMessage();
  }
}

function postComment() {
  if (!$_POST['content']) return;
  if (!is_numeric($_POST['parent_id'])) $_POST['parent_id'] = 0;
  $pdo = getPDO();
  $pdo->beginTransaction();
  try {
    $stmt = $pdo->prepare('INSERT INTO comments VALUES(NULL, ?, ?, ?, NOW())');
    $stmt->execute(array($_POST['parent_id'], $_POST['room_id'], $_POST['content']));
    $lastInsertId = $pdo->lastInsertId('id');
    $pdo->commit();

  } catch(Exception $e) {
    $pdo->rollBack();
    echo $e->getMessage();
  }
}

function postLike() {
  $pdo = getPDO();
  $pdo->beginTransaction();
  try {
    $stmt = $pdo->prepare('INSERT INTO likes VALUES(?, ?, NOW())');
    $stmt->execute(array($_POST['comment_id'], session_id()));
    $pdo->commit();

  } catch(Exception $e) {
    $pdo->rollBack();
    echo $e->getMessage();
  }
}

switch ($_POST['act']) {
  case 'create_room' : createRoom(); break;
  case 'post_comment' : postComment(); break;
  case 'post_like' : postLike(); break;
  default: errorRedirect(); break;
}

?>
