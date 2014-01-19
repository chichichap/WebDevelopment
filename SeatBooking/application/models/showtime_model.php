<?php
class Showtime_model extends CI_Model {

	function get_showtimes()
	{
		$query = $this->db->query("select m.title, t.name, t.address, s.date, s.time, s.available
								from movie m, theater t, showtime s
								where m.id = s.movie_id and t.id=s.theater_id");
		return $query;	
	}

	function get_showtimes2()
	{
		$query = $this->db->query("select m.title, t.name, t.address, s.date, s.time, s.available, s.id
								from movie m, theater t, showtime s
								where m.id = s.movie_id and t.id=s.theater_id");
		return $query;	
	}
	

	function populate() {
		
		$movies = $this->movie_model->get_movies();
		$theaters = $this->theater_model->get_theaters();
		
		//If it returns some results we continue
		if ($movies->num_rows() > 0 && $theaters->num_rows > 0){
			foreach ($movies->result() as $movie){
				foreach ($theaters->result() as $theater){
					for ($i=1; $i < 15; $i++) {
						for ($j=20; $j <= 22; $j+=2) {
							$this->db->query("insert into showtime (movie_id,theater_id,date,time,available)
									values ($movie->id,$theater->id,adddate(current_date(), interval $i day),'$j:00',3)");
								
						}
					}		
				}				
			}
		}		
	}

	function delete() {
		$this->db->query("delete from showtime");
	}
	
	function update_seats($id) {
		$query = $this->db->get_where('showtime', array('id' => $id));
		if ($query->num_rows() == 1)
    		$n_seat = $query->row(0)->available - 1;
		
		$this->db->where('id',$id);
		return $this->db->update('showtime',array('available'=>$n_seat));
	}
}