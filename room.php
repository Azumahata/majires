<?php
include_once('./config.php');
include_once('./get.php');
$roomId = $_GET['room_id'];
$room = getRoom($roomId);
?>
<?php include './header.php' ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script type='text/javascript'> 
$(function() {
  var postComment = document.getElementById('post_comment');
  postComment.addEventListener('click', function() {
    var $comment = $("#comment");
    $.post('./post.php', {
      act : 'post_comment',
      room_id : <?php echo $roomId ?>,
      content : $comment.val(),
      success : function() {
        $comment.val('');
      },
    });
  });

  postLike = function(commentId) {
    $.post('./post.php', {
      act : 'post_like',
      comment_id : commentId,
      success : function() {
  
      },
    });
  };

  var currentOffset = 0;

  var getComments = function() {
    $.get('./get.php',{
      act : 'get_comments',
      room_id : <?php echo $roomId ?>,
      offset: currentOffset,
    }, function(_data) {
      generateComments(_data);
    }, 'json');
  };

  var generateComments = function (comments) {
    var $commentArea = $("#comments-table");
    $.each(comments, function(index, element) {
      if (~~element['id'] >= currentOffset) {
        currentOffset = ~~element['id'];
      }
      $commentArea.prepend(
         '<tr><td>' + element['content'] +
         '</td><td onclick="postLike(' + element['id'] + ')">' +  element['likes'] +
         '</td></tr>');
    })
  };

  var pollingComments; pollingComments = function() {
    console.log(currentOffset);
    return function() { 
      getComments();
      setTimeout(pollingComments(), 1000);
    }
  };
  pollingComments()();

});
</script>
<style type='text/css'>  
table {
  font-size: 0.8em;
  width: 99%;
  border: solid 1px #bbb;
  border-collapse: collapse;
}
table td, th {
  padding: 4px;
  border: solid 1px #bbb;
}
//table tr:nth-child(odd) { background-color: #eee }
</style>

<div class='room'>
  <h2><?php echo $room['title'] ?></h2>
  <div class="overview"><pre><?php echo $room['overview'] ?></pre></div>
</div>

<div class='comment'>
  <p>
    <label>コメント:<textarea id='comment' rows="3" name='overview' cols="80"></textarea></label>
  </p>
  <button id='post_comment'>送信</button>
</div>

<div id='comments'>
  <table><tr><th>コメント</th><th>いいね</th></tr>
    <tbody id="comments-table">
    </tbody>
  </table>
</div>

<?php include './footer.php' ?>
