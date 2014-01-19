<?php
echo anchor('','Back') . "<br />";

//$select = array();

echo form_open('main/selectShowtime');
echo '<br> Select Date: ';
echo form_dropdown('dropdown_dates', $dates);

echo '<br> Select Movie: ';
echo form_dropdown('dropdown_movies', $movies);

echo '<br></br>';
echo form_submit('submit', 'Get Matching Showtimes');
echo form_close();

if ($showtimes->num_rows() > 0){
	echo "<table>";
	$this->table->set_heading(array('Movie','Theater','Address','Date','Time','Available'));
	
	foreach ($showtimes->result() as $showtime){
		if ( $selectedDate == $showtime->date && $selectedMovie == $showtime->title) {
			$row   = array($showtime->title, $showtime->name, $showtime->address, $showtime->date, $showtime->time, $showtime->available);
			
			if ( $showtime->available > 0 )
				$row[] = anchor("main/selectSeat/$showtime->id", 'Select');
			
			$this->table->add_row($row);
		}
	}
	
	echo $this->table->generate();
}

?>
