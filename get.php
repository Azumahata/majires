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
  id,
  room_id,
  content,
  created_at,
  IFNULL(likes, 0) as likes
FROM
  comments
  LEFT OUTER JOIN (SELECT comment_id, COUNT(1) likes FROM likes GROUP BY  comment_id) as likes
  ON (comments.id = likes.comment_id)
WHERE
  id > ? AND room_id = ?
GROUP BY 
  id,
  room_id,
  content,
  created_at
ORDER BY created_at
SQL;
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array($offset ? $offset : 0, $roomId));
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

switch ($_GET['act']) {
  case 'get_comments' : echo json_encode(getComments($_GET['room_id'], $_GET['offset'])); break;
  default: break;
}
?>

