
<!DOCTYPE html>

<html>
	<head>
	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<script src="<?= base_url() ?>/js/jquery.timers.js"></script>
	<script>
		var canvas;
		var turn = <?=$turn?>;
		var playerNum = <?=$playerNumber?>;
	
		window.onload = function() {
			canvas = document.getElementById("canvas1");
			canvas.onmousedown = canvasOnMouseDown;
			
			var context = canvas.getContext("2d"); 		// draw the empty board
			context.fillStyle = "blue";	
			context.fillRect(0,0,280,240);

			for (var col=1; col < 8; col++) {
				for (var row=1; row < 7; row++) {
					context.beginPath();
				    context.arc(col*40 - 20, row*40 - 20, 15, 0, 2 * Math.PI, false);
					context.fillStyle = "White";		
					context.fill();
				}
			}	
		};

		function canvasOnMouseDown(e) {
			var x = e.pageX - canvas.offsetLeft;

			if ( <?=$playerNumber?> == turn ) 
				columnSelected( 1 + Math.floor(x / 40) );
		}

		function columnSelected(index) {
			var arguments = "column=" + index;
			var url = "<?= base_url() ?>board/postTurn";

			$.post(url,arguments, function (data,textStatus,jqXHR) {
				var dataObject = JSON.parse(data); // "data" is somehow a string and not an object here
				
				if (dataObject && dataObject.status=='success') {					
					var context = canvas.getContext("2d");		// draw the disc dropped
					var col = index;
					var row = 7 - dataObject.row;				// y-axis is reversed
						
					context.beginPath();
					context.arc(col*40 - 20, row*40 - 20, 15, 0, 2 * Math.PI, false);
					context.fillStyle = "Yellow";
					context.fill();
					
					if ( dataObject.winner == playerNum ) {
						$('#status').html('Status: You won! ' + '<?= anchor('board/quit','(Back)') ?>');
						turn = 0;
					} else {
						$('#status').html('Status: Waiting for ' + otherUser + ' (his turn)');
						turn = 3 - playerNum; // switch turn
					}
				}
			});
		}

//end of added javascript		
		var otherUser = "<?= $otherUser->login ?>";
		var user = "<?= $user->login ?>";
		var status = "<?= $status ?>";
		
		$(function(){
			$('body').everyTime(2000,function(){
					if (status == 'waiting') {
						$.getJSON('<?= base_url() ?>arcade/checkInvitation',function(data, text, jqZHR) {
							if (data && data.status=='rejected') {
								alert("Sorry, your invitation to play was declined!");
								window.location.href = '<?= base_url() ?>arcade/index';
							}
								
							if (data && data.status=='accepted') {
								status = 'playing';
								$('#status').html('Status: Playing with ' + otherUser + " (your turn)");
							}
						});
					}
					
					var url2 = "<?= base_url() ?>board/getTurn";
					$.getJSON(url2, function (data,text,jqXHR) {
						if (data && data.status=='success') {
							if (turn != data.turn && data.winner != playerNum) {
								var context = canvas.getContext("2d");		// draw the disc dropped
								var col = data.column;
								var row = 7 - data.row;						// y-axis is reversed
									
								context.beginPath();
								context.arc(col*40 - 20, row*40 - 20, 15, 0, 2 * Math.PI, false);
								context.fillStyle = "Red";
								context.fill();

								if ( data.winner == (3 - playerNum) ) {
									$('#status').html('Status: You lost! '+'<?= anchor('board/quit','(Back)') ?>');
								} else {
									$('#status').html('Status: Playing with '+ otherUser +" (your turn)");
									turn = data.turn; // now is your turn
								}
							}
						}
					});
					
					var url = "<?= base_url() ?>board/getMsg";
					$.getJSON(url, function (data,text,jqXHR) {
						if (data && data.status=='success') {
							var conversation = $('[name=conversation]').val();
							var msg = data.message;
							if (msg.length > 0)
								$('[name=conversation]').val(conversation + "\n" + otherUser + ": " + msg);
						}
					});
			});

			$('form').submit(function(){
				var arguments = $(this).serialize();
				var url = "<?= base_url() ?>board/postMsg";
				$.post(url,arguments, function (data,textStatus,jqXHR){
						var conversation = $('[name=conversation]').val();
						var msg = $('[name=msg]').val();
						$('[name=conversation]').val(conversation + "\n" + user + ": " + msg);
						});
				return false;
				});	
		});
	</script>
	</head> 
<body>  
	<h1>Game Area</h1>

	<div>
	Hello <?= $user->fullName() ?>  <?= anchor('account/logout','(Logout)') ?>  
	</div>
	
	<br>
	<div id='status'> 
	<?php 
		if ($status == 'waiting')
			echo "Status: Waiting for " . $otherUser->login . " to join the game.";
		else {
			if ($turn == $playerNumber)
				echo "Status: Playing with " . $otherUser->login . " (your turn)";
			else
				echo "Status: Waiting for " . $otherUser->login . " (his turn)";
		}
	?>
	</div>
	<div id='quit'>
	</div>

	<canvas id="canvas1" width="280" height="240"></canvas>
	<br>
<?php 
	echo "<br> Chat: <br/>";
	echo form_textarea('conversation');
	
	echo form_open();
	echo form_input('msg');
	echo form_submit('Send','Send');
	echo form_close();
	
?>
</body>
</html>
