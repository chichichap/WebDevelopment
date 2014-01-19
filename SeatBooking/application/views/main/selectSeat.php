<?php
	echo anchor('main/selectShowtime','Back') . "<br />";
	echo '<br> Select Your Seat: </br>';
?>

<canvas id="canvas1" width="40" height="40"></canvas>
<canvas id="canvas2" width="40" height="40"></canvas>
<canvas id="canvas3" width="40" height="40"></canvas>

<script>

window.onload = function() {
	var c1 = document.getElementById("canvas1");
	var c2 = document.getElementById("canvas2");
	var c3 = document.getElementById("canvas3");

	c1.onmousedown = seatOneSelected;
	c2.onmousedown = seatTwoSelected;
	c3.onmousedown = seatThreeSelected;

	var seatNumber = "<?php if (isset($_GET['seat'])) echo $_GET['seat']; ?>";

	seatSelected(seatNumber);
};

function seatOneTaken() {
	if (<?php if (in_array(1, $seats_taken)) echo 1; else echo 0; ?>) 	
		return true;
	else 													
		return false;
}

function seatTwoTaken() {
	if (<?php if (in_array(2, $seats_taken)) echo 1; else echo 0; ?>) 	
		return true;
	else 													
		return false;
}

function seatThreeTaken() {
	if (<?php if (in_array(3, $seats_taken)) echo 1; else echo 0; ?>) 	
		return true;
	else 													
		return false;
}

// return TRUE if seat i has been taken.
function seatTaken(i) {
	if (i == 1) 	  return seatOneTaken();
	else if (i == 2) return seatTwoTaken();
	else			  return seatThreeTaken();
}

function seatSelected(index) {
	for (var i=1; i < 4; i++) {
		var c=document.getElementById("canvas" + i);
		var ctx=c.getContext("2d");

		if ( seatTaken(i) )
			ctx.fillStyle = "Yellow"; 		// taken
		else if (i == index)
			ctx.fillStyle = "lightgreen";	// currently selected
		else 
			ctx.fillStyle = "white";		// available
		ctx.fillRect(0,0,40,40);
	}
}

function seatOneSelected(e) {
	if (!seatOneTaken()) {
		seatSelected(1);
		window.location.href="?seat=1";
	}
}

function seatTwoSelected(e) {
	if (!seatTwoTaken()) {
		seatSelected(2);
		window.location.href="?seat=2";
	}
}

function seatThreeSelected(e) {
	if (!seatThreeTaken()) {
		seatSelected(3);
		window.location.href="?seat=3";
	}
}

</script> 

<?php
	echo "<br><br/>";
	echo "- Occupied seats are show in yellow. <br />";
	echo "- Available seats are shown in white. <br />";
	echo "- Selected seat is shown in green. <br><br/>";

if(isset($_GET['seat'])) {
	$seat = $_GET['seat'];
	echo anchor('main/payment/' . $showtime_id . '/' . $seat,'Next') . "<br />";
}

?>

	