<?php
class Movie_model extends CI_Model {

	function get_movies()
	{
		$query = $this->db->query("select * from movie");
		return $query;	
	}  

	function populate() {
		$this->db->query("insert into movie (title) values ('Men with Brooms')"); 
		$this->db->query("insert into movie (title) values ('Juno')");
		$this->db->query("insert into movie (title) values ('Barney\'s Version')");
		$this->db->query("insert into movie (title) values ('Canadian Bacon')");
	}

	function delete() {
		$this->db->query("delete from movie");
	}
	
	
}