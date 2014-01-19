<?php
class Ticket_model extends CI_Model {

	function get_tickets()
	{
		$query = $this->db->query("select m.title, t.name, tic.seat, tic.first, tic.last, tic.creditcardnumber, tic.creditcardexpiration
								from movie m, theater t, showtime s, ticket tic
								where s.id=tic.showtime_id and m.id = s.movie_id and t.id=s.theater_id");
		return $query;		
	}  

	
	function insert($first, $last, $ccnumber, $ccexpiration, $showtime_id, $seat) {
		$data = array( 
					'first' => $first,
					'last' => $last,
					'creditcardnumber' => $ccnumber,
					'creditcardexpiration' => $ccexpiration,
					'showtime_id' => $showtime_id,
					'seat' => $seat
				);
		
		return $this->db->insert('ticket', $data);
	}
	
	function delete() {
		$this->db->query("delete from ticket");
	}
	
	// get the availability of each seat as an array
	function get_seats($showtime_id) {
		$query = $this->db->query("select tic.seat
								   from ticket tic
								   where tic.showtime_id=" . $showtime_id);
		
		$seats = array();
		
		foreach ($query->result() as $row){
			$seats[] = $row->seat;
		}
		
		return $seats;
	}
}