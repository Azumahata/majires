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
  var postCommentFunction = function(parentId, commentType, $inputText) {
    return function() {
      $.post('./post.php', {
        act : 'post_comment',
        room_id : <?php echo $roomId ?>,
        parent_id : parentId,
        comment_type : parentId == 0 ? $('input[name=comment_type]:checked').val() : 3,
        content : $inputText.val(),
        success : function() {
          $inputText.val('');
        },
      })
    };
  };

  var postComment = document.getElementById('post_comment');
  postComment.addEventListener('click', postCommentFunction(0, null, $("#comment-textarea")));

  var currentOffset = 0;
  var $commentsList = $("#comments");
  var $commentTemplate = $(".comment-box.template");
  var commentObjs = {}; // comment_id : obj

  var commentObj = {
    _data : {},
    _obj : null,
    _$like : null,
    _$replyText : null,
    _$replyArea : null,
    _$replies :null,
    init : function($obj, data) {
      this._obj = $obj;
      this._obj.removeClass("template");
      this._$like = $(".like", this._obj);
      this._$replyText = $(".reply-text", this._obj);
      this._$replyArea = $(".reply-area", this._obj);
      this._$replies   = $(".replies", this._obj);

      this.updateData(data);

      return this;
    },
    updateData : function(data) {
      this.setComment(data['content']);
      this.setLikesCount(data['likes']);
      this._updateReplies(data['replies']);
      if (data.comment_type != 0) {
        this._$like.addClass("disp-none");
      }
      this._data = data;
    },
    setComment : function(comment) {
      if (this._data['content'] == comment) return;
      $(".content pre", this._obj).text(comment);
    },
    setLikesCount : function(count) {
      this._$like.removeClass("shake");
      if (this._data['likes'] == count) return;
      $(".count", this._$like).text(count);
      this._$like.addClass("shake");
    },
    binds : function() {
      this._$like.unbind("click", this._bindPostLike(this));
      this._$like.bind("click",   this._bindPostLike(this));
      this._$replyText.unbind("click", this._bindReplyAreaDispToggle(this));
      this._$replyText.bind("click",   this._bindReplyAreaDispToggle(this));
      $("button.send-reply", this._$replyArea).bind('click',
         postCommentFunction(this._data['id'], 1, $(".reply-comment", this._$replyArea))
      );
    },
    _bindPostLike : function(that) {
      return function() {
        postLike(that._data['id']);
        that._$like.css("background-color", "#888");
      }
    },
    _bindReplyAreaDispToggle : function(that) {
      return function() {
        that._$replyArea.toggleClass("disp-none");
      }
    },
    _updateReplies : function(replies) {
      if (!replies) return;
      this._$replies.empty();
      for (var i = 0; i < replies.length; ++i) {
        this._$replies.append($('<p>' + replies[i]['content'] + '</p>'));
      }
    },
    upsertObj : function(force) {
      if (force === true || !commentObjs[this._data['id']]) {
        this.binds();
        $commentsList.prepend(this._obj);
        commentObjs[this._data['id']] = this;
      } else {
        commentObjs[this._data['id']].updateData(this._data);
      }
    }
  }

  var getComments = function(isAll) {
    $.get('./get.php',{
      act : 'get_comments',
      room_id : <?php echo $roomId ?>,
      offset: isAll ? 0 : currentOffset,
    }, function(_data) {
      generateComments(_data);
    }, 'json');
  };

  var generateComments = function (comments) {
    $.each(comments, function(index, element) {
      if (~~element['id'] >= currentOffset) {
        currentOffset = ~~element['id'];
      }
      $.extend(true, {}, commentObj)
        .init($commentTemplate.clone(), element)
        .upsertObj()
      ;
    });
  };

  var pollingCount = 0;
  var pollingComments; pollingComments = function() {
    return function() { 
      getComments(++pollingCount % 5 == 0);
      setTimeout(pollingComments(), <?php echo POLLING_INTERVAL_MS ?>);
    }
  };
  pollingComments()();

  postLike = function(commentId) {
    $.post('./post.php', {
      act : 'post_like',
      comment_id : commentId,
      success : function() { getComments(true); pollingCount = 4; },
    });
  };

  var sortFunfcion = function(compareFnc) {
    $commentsList.empty();
    var sorted = Object.values(commentObjs);
    sorted.sort(compareFnc);
    for (var i = 0; i < sorted.length; ++i)
      sorted[i].upsertObj(true);
  };

  sortCreatedAt = function() {
    sortFunfcion(function(a, b) { return a._data['id'] - b._data['id']; })
  };
  sortLikesCount = function() {
    sortFunfcion(function(a, b) { return a._data['likes'] - b._data['likes'] });
  };
  sortRepliesCount = function() {
    sortFunfcion(function(a, b) { console.log(a._data['replies'].length); return a._data['replies'].length - b._data['replies'].length });
  };
});
</script>
<style type='text/css'>  
.template { display:none; }
.disp-none { display:none; }

pre { white-space: pre-wrap;  }
.room .overview pre { background-color:#eee; padding:8px 16px; margin:8px; width:80%; }

.comment-box {
  width:70%; height:100%; opacity:1;
  box-shadow: 2px 2px 2px rgba(0,0,0,0.2);
  border: 1px solid #888;
  border-radius: 5px;
  padding:4px 8px;
  animation: fadeIn 1s 0s ease;
  margin:12px;
}
@keyframes fadeIn { 0% { opacity:0; } 99.9%, 100% { opacity:1; } }

.comment-box .like {
  width:48px; height:48px;
  color:#eee;
  text-shadow: 1px 1px #000;
  background-color:#fa0;
  border: 1px double #88f;
  border-radius: 24px;
  float:left;
}
.comment-box .like .icon  { font-size:1.5em; }
.comment-box .like .count { font-size:0.5em; margin: -5px;
}
.shake { animation: shake 250ms 0s ease; }
@keyframes shake {
  10%, 90% { transform: translate3d(-1px, 0, 0); }
  20%, 80% { transform: translate3d(2px, 0, 0); }
  30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
  40%, 60% { transform: translate3d(4px, 0, 0); }
}

.comment-box .content {
  width:70%; padding-left:16px;
  line-height: 128%; 
  float:left;
  font-size: 2em;
}

.comment-box .reply-text {
  float:right;
}
.comment-box .reply-text:before { content: '↩'; }
.comment-box .replies p {
  text-align:right;
  width:80%;
  font-size: 1em;
  padding:4px 8px;
  color: #808;
  border-top: 1px solid #888; 
}

</style>

<div class='room'>
  <h2><?php echo htmlspecialchars($room['title']) ?></h2>
  <div class="overview"><pre><?php echo $room['overview'] ?></pre></div>
</div>

<div class='comment'>
  <p>
    <label>コメント:<textarea id='comment-textarea' class="textlines" rows="3" name='overview' cols="80"></textarea>
      <button id='post_comment'>送信</button>
      <input type="radio" name="comment_type" value="0" checked>質問</input>
      <input type="radio" name="comment_type" value="1">コメント</input>
    </label>
  </p>
  <button onclick="sortCreatedAt()">⏰</button>
  <button onclick="sortLikesCount()">👍</button>
  <button onclick="sortRepliesCount()">↩</button>
</div>

<div class="comment-box template">
  <div class="like tac">
     <div class='icon'>👍</div>
     <div class='count'>128</div>
  </div>
  <div class="content"><pre></pre></div>
  <div class="reply-text tar">返信</div>
  <div class="cb"></div>
  <div class="reply-area tar disp-none">
    <input type="text" class="reply-comment" size="40"/>
    <button class='send-reply'>↩</button>
  </div>
  <div class="replies">
  </div>
</div>
<div id='comments'>
</div>

<?php include './footer.php' ?>
