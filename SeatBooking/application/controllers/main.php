<?php
class Main extends CI_Controller {
    function __construct() {
    	parent::__construct(); // Call the Controller constructor
    }
        
    function index() {
	    	$data['main']='main/index';
	    	$this->load->view('template', $data);
    }
    
    function admin() {
    	$data['main']='main/admin';
    	$this->load->view('template', $data);
    }
    
	function showShowtimes() {
		$this->load->library('table');
		$this->load->model('showtime_model');
		$showtimes = $this->showtime_model->get_showtimes();

		if ($showtimes->num_rows() > 0) {
//Prepare the array that will contain the data
			$table = array();	
			$table[] = array('Movie','Theater','Address','Date','Time','Available');
		
		   foreach ($showtimes->result() as $row) {
				$table[] = array($row->title,$row->name,$row->address,$row->date,$row->time,$row->available);
		   }
		   
//Next step is to place our created array into a new array variable, one that we are sending to the view.
			$data['showtimes'] = $table; 		   
		}
		
//Now we are prepared to call the view, passing all the necessary variables inside the $data array
		$data['main']='main/showtimes';
		$this->load->view('template', $data);
    }
    
    function showTickets() {
    	$this->load->library('table');
    	$this->load->model('ticket_model');
    	
    	$tickets = $this->ticket_model->get_tickets();
    	
    	if ($tickets->num_rows() > 0) {
    		$table = array();
    		$table[] = array('Movie','Theater','Seat','First','Last','Credit Card Number', 'Expiry Date');
    	
    		foreach ($tickets->result() as $row) {
    			$table[] = array($row->title,$row->name,$row->seat,$row->first,$row->last,$row->creditcardnumber,$row->creditcardexpiration);
    		}

    		$data['tickets'] = $table;
    	}
    	
    	$data['main']='main/tickets';
    	$this->load->view('template', $data);
    }
    
    function populate() {
	    $this->load->model('movie_model');
	    $this->load->model('theater_model');
	    $this->load->model('showtime_model');
	     
	    $this->movie_model->populate();
	    $this->theater_model->populate();
	    $this->showtime_model->populate();
	     
	    redirect('/admin.php', 'refresh'); // then we redirect to the admin page again
    }
    
    function delete() {
	    $this->load->model('movie_model');
	    $this->load->model('theater_model');
	    $this->load->model('showtime_model');
	    $this->load->model('ticket_model');
    	
	    $this->movie_model->delete();
	    $this->theater_model->delete();
	    $this->showtime_model->delete();
	    $this->ticket_model->delete();
	     
	    redirect('/admin.php', 'refresh'); // then we redirect to the admin page again
    }
    
    function selectShowtime() {
    	$this->load->library('table');
    	$this->load->model('showtime_model');
    	$this->load->model('movie_model');
    	$this->load->model('theater_model');
    	
    	$showtimes = $this->showtime_model->get_showtimes2();
    	$movie_objects = $this->movie_model->get_movies()->result();
    	$theaters = $this->theater_model->get_theaters();
    	
    	$dates = array();
    	$movies = array();
    	
    	for ($i=1; $i < 15; $i++)
    		$dates[] = date('Y-m-d', strtotime('+' . $i . 'days'));
    	
    	foreach($movie_objects as $movie)
    		$movies[$movie->id] = $movie->title;
    	
    	$data['showtimes'] = $showtimes;
    	$data['dates'] = $dates;
    	$data['movies'] = $movies;
    	
    	$selectedDate = $this->input->post('dropdown_dates');
    	if ( $selectedDate )
    		$data['selectedDate'] = $dates[$selectedDate];
    	else
    		$data['selectedDate'] = $dates[0];
    	
    	$selectedMovie = $this->input->post('dropdown_movies');
    	if ( $selectedMovie )
    		$data['selectedMovie'] = $movies[$selectedMovie];
    	else
    		$data['selectedMovie'] = array_shift(array_values($movies));;

    	$data['main']='main/selectShowtime';
    	$this->load->view('template', $data);
    }
    
    function selectSeat($showtime_id) {
    	$this->load->model('ticket_model');
    	$seats_taken = $this->ticket_model->get_seats($showtime_id);
    	
		$data['seats_taken'] = $seats_taken;
    	$data['showtime_id'] = $showtime_id;
    	$data['main']='main/selectSeat';
    	$this->load->view('template', $data);
    }
    
    function payment($showtime_id, $seat_number) {
    	$data['main']='main/payment';
    	$data['showtime_id'] = $showtime_id;
    	$data['seat_number'] = $seat_number;
    	$this->load->view('template', $data);
    }
    
    function validate($showtime_id, $seat_number) {
    	$this->load->library('form_validation');  
    	  	
    	if ($this->form_validation->run('validate') == FALSE) {
    		$data['main']='main/payment';
    		$data['showtime_id'] = $showtime_id;
    		$data['seat_number'] = $seat_number;
    	} else {
    		$first = $this->input->post('first');
    		$last = $this->input->post('last');
    		$cc_number = $this->input->post('cc_number');
    		$cc_exp_date = $this->input->post('cc_exp_date');
    	
// step 1: prepare information to display
    		$query = $this->db->query("select m.title, t.name, t.address, s.date, s.time
								from movie m, theater t, showtime s
								where s.id=" . $showtime_id ." and m.id=s.movie_id and t.id=s.theater_id");
    		
    		if ($query->num_rows() == 1) {
    			$row = $query->row(0);
    			
    			$data['title'] = $row->title;
    			$data['name'] = $row->name;
    			$data['address'] = $row->address;
    			$data['date'] = $row->date;
    			$data['time'] = $row->time;
    		}
    		
    		$data['seat'] = $seat_number;
    		$data['first'] = $first;
    		$data['last'] = $last;
    		
// step 2: start transaction to create the ticket and update seats
    		$this->load->model('ticket_model');
    		$this->load->model('showtime_model');
    		
    		$this->db->trans_start();
    		$this->showtime_model->update_seats($showtime_id);
    		$this->ticket_model->insert($first, $last, $cc_number, $cc_exp_date, $showtime_id, $seat_number);
    		
    	    if ( $this->db->trans_status() == FALSE) {
    			$_SESSION['err_msg'] = $this->db->_error_message();
    			$_SESSION['err_no'] = $this->db->_error_message();
    			$this->db->trans_rollback();
    		} else {
    			$this->db->trans_commit();
    		}
    		
    		$data['main'] ='main/summary';
    	}
    	
    	$this->load->view('template', $data);
    }
    
    function exp_check($exp_date) {
    	$exp =  str_split($exp_date, 2);
    	$month = $exp[0];
    	$year = $exp[1];
    	
    	if ( $month > 12 || $month < 1 || $year > 30 ) {
    		$this->form_validation->set_message('exp_check', 'Invalid Expiry Date!');
    		return FALSE;
    	} elseif ( $year < date('y') || ($year == date('y') && $month < date('m')) ) {
    		$this->form_validation->set_message('exp_check', 'Your credit card has expired!');
    		return FALSE;
    	}
    	
    	return TRUE;
    }
}

