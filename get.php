<?php include_once('./config.php');
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  errorRedirect();
}

function getRooms() {
  $pdo = getPDO();
  $sql = <<<SQL
SELECT
  id,
  title,
  IFNULL(cnt, 0) as cnt,
  created_at
FROM
  rooms
  LEFT OUTER JOIN (
    SELECT
      room_id,
      COUNT(1) AS cnt
    FROM
      comments
    GROUP BY
      room_id
  ) comments_cnt
  ON (rooms.id = comments_cnt.room_id)
ORDER BY
  created_at DESC
SQL;
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRoom($roomId) {
  $pdo = getPDO();
  $sql = ' SELECT * FROM rooms WHERE id = ? ORDER BY created_at DESC';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array($roomId));
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getComments($roomId, $offset) {
  $pdo = getPDO();
  $sql = <<<SQL
SELECT
  comments.id,
  comments.parent_comment_id,
  comments.content,
  comments.created_at,
  IFNULL(COUNT(likes.comment_id), 0) as likes
FROM
  comments
  LEFT OUTER JOIN likes
  ON (comments.id = likes.comment_id)
WHERE
  id > ? AND room_id = ?
GROUP BY 
  comments.id,
  comments.parent_comment_id,
  comments.content,
  comments.created_at
ORDER BY comments.created_at
SQL;
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array($offset ? $offset : 0, $roomId));
  $allComments = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $comments = array();
  $replies  = array();
  foreach ($allComments as $comment) {
    if ($comment['parent_comment_id']) {
      $replies[] = $comment;
    } else {
      $comments[$comment['id']] = $comment;
    }
  }
  foreach ($replies as $reply) {
    if (is_null($comments[$reply['parent_comment_id']])) continue;
    if (is_null($comments[$reply['parent_comment_id']]['replies'])) {
      $comments[$reply['parent_comment_id']]['replies'] = array();
    }
    $comments[$reply['parent_comment_id']]['replies'][] = $reply;
  }
  return array_values($comments);
}

function getReplies($commentIds) {
}

switch ($_GET['act']) {
  case 'get_comments' : echo json_encode(getComments($_GET['room_id'], $_GET['offset'])); break;
  default: break;
}
?>

