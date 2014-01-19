<?php

class Board extends CI_Controller {
     
    function __construct() {
    		// Call the Controller constructor
	    	parent::__construct();
	    	session_start();
    } 
          
    public function _remap($method, $params = array()) {
	    	// enforce access control to protected functions	
    		
    		if (!isset($_SESSION['user']))
   			redirect('account/loginForm', 'refresh'); //Then we redirect to the index page again
 	    	
	    	return call_user_func_array(array($this, $method), $params);
    }
    
    
    function index() {
		$user = $_SESSION['user'];
    		    	
	    	$this->load->model('user_model');
	    	$this->load->model('invite_model');
	    	$this->load->model('match_model');
	    	
	    	$user = $this->user_model->get($user->login);

	    	$invite = $this->invite_model->get($user->invite_id);
	    	
	    	if ($user->user_status_id == User::WAITING) {
	    		$invite = $this->invite_model->get($user->invite_id);
	    		$otherUser = $this->user_model->getFromId($invite->user2_id);
	    		$playerNumber = 1;
	    	}
	    	else if ($user->user_status_id == User::PLAYING) {
	    		$match = $this->match_model->get($user->match_id);
	    		if ($match->user1_id == $user->id) {
	    			$playerNumber = 2;
	    			$otherUser = $this->user_model->getFromId($match->user2_id);
	    		} else {
	    			$playerNumber = 1;
	    			$otherUser = $this->user_model->getFromId($match->user1_id);
	    		}
	    	}
	    	
	    	$data['user']=$user;
	    	$data['otherUser']=$otherUser;
	    	
	    	switch($user->user_status_id) {
	    		case User::PLAYING:	
	    			$data['status'] = 'playing';
	    			break;
	    		case User::WAITING:
	    			$data['status'] = 'waiting';
	    			break;
	    	}
	    	
	    	
	    	$data['playerNumber'] = $playerNumber;
	    	$data['turn'] = 1;
	    	
		$this->load->view('match/board',$data);
    }
    
    function quit() {
    	$user = $_SESSION['user'];
    	$this->load->model('user_model');
    	$this->user_model->updateStatus($user->id, User::AVAILABLE);
    	
    	redirect('arcade/index', 'refresh'); //redirect to main page
    	return;
    }
    
    function postTurn() {
    	$this->load->model('user_model');
    	$this->load->model('match_model');
    	
    	$user = $_SESSION['user'];
    		
    	$user = $this->user_model->getExclusive($user->login);
    	if ($user->user_status_id != User::PLAYING) {
    		$errormsg="Not in PLAYING state";
    		goto error;
    	}
    	
    	$match = $this->match_model->get($user->match_id);
    	if ($match->user1_id == $user->id)
    		$playerNumber = 2;
    	else 
    		$playerNumber = 1;
    		
// old code to serialize (PHP)
    	/* $boardState = unserialize($match->board_state);
    	 $boardState = array(
    	 		"turn" => $turn,
    	 		"board" => "haa",
    	 );*/
    	
// new code to unpack (JSON)
    	$boardState = new BoardState(); //BoardState::createFromJson( $match->board_state );
    	$boardState->cloneFromJSON( json_decode($match->board_state, true) );
// note: 2nd param means associative array, so use ['b'] instead of ->
    	
    	$columnSelected = intval($this->input->post('column')); 	
    	
    	if ( $boardState->dropDisc($columnSelected, $playerNumber) == true ) {
    		$row = $boardState->lastRow;
    		$boardState->turn = 3 - $boardState->turn; 
    	} else {
    		$errormsg = "Selected column is already full!";
    		goto error;
    	}
    	
    	$winner = 0;
    	if ( $boardState->fourDiscsConnected( $playerNumber ) == true ) {
    		$winner = $playerNumber;
    		if ($match->user1_id == $user->id) 
    			$this->match_model->updateStatus($user->match_id, Match::U1WON);
    		else
    			$this->match_model->updateStatus($user->match_id, Match::U2WON);
    	}

    	$this->match_model->updateBoardState($match->id, $boardState); // will be serialized inside the function
    	
    	echo json_encode(array('status'=>'success', 'row'=>$row, 'winner'=>$winner));
    	return;
    	
    error:
    	echo json_encode(array('status'=>'failure','message'=>$errormsg));
    }
    
    function getTurn() {
    	$this->load->model('user_model');
    	$this->load->model('match_model');
    	
    	$user = $_SESSION['user'];
    	
    	$user = $this->user_model->get($user->login);
    	if ($user->user_status_id != User::PLAYING) {
    		$errormsg="Not in PLAYING state";
    		goto error;
    	}
 	
    	$match = $this->match_model->get($user->match_id);
    	$boardState = new BoardState();
    	$boardState->cloneFromJSON( json_decode($match->board_state, true) );
    	$turn = $boardState->turn;
    	$row = $boardState->lastRow;
    	$column = $boardState->lastColumn;
    	$winner = $boardState->winner;
    	
// old code to unserialize (PHP)
    	/*$boardState = unserialize($match->board_state);
    	$turn = $boardState['turn'];*/
    	
    	echo json_encode(array('status'=>'success','turn'=>$turn, 'column'=>$column, 'row'=>$row, 'winner'=>$winner));
		return;
		
		error:
		echo json_encode(array('status'=>'failure','message'=>$errormsg));
    }

 	function postMsg() {
 		$this->load->library('form_validation');
 		$this->form_validation->set_rules('msg', 'Message', 'required');
 		
 		if ($this->form_validation->run() == TRUE) {
 			$this->load->model('user_model');
 			$this->load->model('match_model');

 			$user = $_SESSION['user'];
 			 
 			$user = $this->user_model->getExclusive($user->login);
 			if ($user->user_status_id != User::PLAYING) {	
				$errormsg="Not in PLAYING state";
 				goto error;
 			}
 			
 			$match = $this->match_model->get($user->match_id);			
 			
 			$msg = $this->input->post('msg');
 			
 			if ($match->user1_id == $user->id)  {
 				$msg = $match->u1_msg == ''? $msg :  $match->u1_msg . "\n" . $msg; // if we have old message then append new message to it
 				$this->match_model->updateMsgU1($match->id, $msg);
 			}
 			else {
 				$msg = $match->u2_msg == ''? $msg :  $match->u2_msg . "\n" . $msg;
 				$this->match_model->updateMsgU2($match->id, $msg);
 			}
 				
 			echo json_encode(array('status'=>'success'));
 			 
 			return;
 		}
		
 		$errormsg="Missing argument";
 		
		error:
			echo json_encode(array('status'=>'failure','message'=>$errormsg));
 	}
 
	function getMsg() {
 		$this->load->model('user_model');
 		$this->load->model('match_model');
 			
 		$user = $_SESSION['user'];
 		 
 		$user = $this->user_model->get($user->login);
 		if ($user->user_status_id != User::PLAYING) {	
 			$errormsg="Not in PLAYING state";
 			goto error;
 		}
 		
// start transactional mode  
 		$this->db->trans_begin();
 			
 		$match = $this->match_model->getExclusive($user->match_id);			
 			
 		if ($match->user1_id == $user->id) {
			$msg = $match->u2_msg;
 			$this->match_model->updateMsgU2($match->id,"");
 		}
 		else {
 			$msg = $match->u1_msg;
 			$this->match_model->updateMsgU1($match->id,"");
 		}

 		if ($this->db->trans_status() === FALSE) {
 			$errormsg = "Transaction error";
 			goto transactionerror;
 		}
 		
// if all went well commit changes
 		$this->db->trans_commit();
 		
 		echo json_encode(array('status'=>'success','message'=>$msg));
		return;
		
		transactionerror:
		$this->db->trans_rollback();
		
		error:
		echo json_encode(array('status'=>'failure','message'=>$errormsg));
 	}
 }

