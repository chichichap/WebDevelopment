<script type="text/javascript" src="jquery-2.0.3.js"></script>
<script>
function checkExpiryDate() {
	var num = $("#exp_date").val();
	var a = num.match(/.{1,2}/g);
	var month = parseInt(a[0]);
	//var year = parseInt(a[1]);

	if (month > 12 || month < 1) {
		num.get(0).setCustomValidity(""); 	// All good, clear error message
		return true;
	} else {
		num.get(0).setCustomValidity("Invalid Date!");
		return false;
	}
}

$(function() {
	$("#exp_date").input( 'checkExpiryDate()' );
});

</script>

<?php
	echo anchor('main/selectSeat/' . $showtime_id,'Back') . "<br />";
	
	echo '<br>';
	
	echo 'Ticket Price: $6 <br><br>';
	echo validation_errors();
	echo form_open('main/validate/' . $showtime_id . '/' . $seat_number);
	
	echo form_label('First Name:');
	echo form_input('first', set_value('first'), "required");
	
	echo form_label('Last Name:');
	echo form_input('last', set_value('last'), "required");
	
	echo '<br>';
	echo form_label('Credit Card Number:');
	echo form_input('cc_number', set_value('cc_number'), "required pattern='\d{16}' title='must have 16 digits' ");
	
	echo form_label('Expiry Date:');
	echo form_input('cc_exp_date', set_value('cc_exp_date'), "id='exp_date' required pattern='\d{4}' oninput='checkExpiryDate();' title='MM/YY'");
	
	echo '<br>';
	echo form_submit('submit', 'Make a payment');
	echo form_close();
?>