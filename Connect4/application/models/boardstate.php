<?php
class BoardState {
	public $winner;
	public $turn; 					// whose turn it is (player 1 or player 2)
	public $lastColumn; 			// column of opponent's last discs
	public $lastRow;				// row of opponent's last discs
	public $columns; 				// each column is an array of rows
	
	function __construct() {
		$this->winner = 0;
		$this->turn = 1;
		$this->lastColumn = 0;
		$this->lastRow = 0;
		$row = array_fill(1,6, 0);
		$this->columns = array_fill(1,7, $row);
	}
	
	function cloneFromJSON( $BoardStateString ) {
		$this->winner = $BoardStateString['winner'];
		$this->turn = $BoardStateString['turn'];
		$this->lastColumn = $BoardStateString['lastColumn'];
		$this->lastRow = $BoardStateString['lastRow'];
		$this->columns = $BoardStateString['columns'];
	}
	
	function dropDisc( $column, $playerNumber ) {
		if ($this->columns[$column][6] != 0)
			return false; // the column is full
		
		for ($r = 1; $r < 7; $r++) {
			if ($this->columns[$column][$r] == 0) {
				$this->columns[$column][$r] = $playerNumber;
				$this->lastColumn = $column;
				$this->lastRow = $r;
				return true;
			}
		}
		
		return true; // should never get here*/
	}
	
	function fourDiscsConnected( $playerNumber ) {
		$col = $this->lastColumn;
		$row = $this->lastRow;
		$count = 1;
// step 1: check vertically (down)
		for ($r = $row-1; $r >= 1; $r--) {
			if ( $this->columns[$col][$r] == $playerNumber ) 	$count++;
			else 												break;
		}
		
		if ($count >= 4) {
			$this->winner = $playerNumber;
			return true;
		} 			
											
		$count = 1;
// step 2A: check horizontally (to the left)
		for ($c = $col-1; $c >= 1; $c--) {
			if ( $this->columns[$c][$row] == $playerNumber ) 	$count++;
			else 												break;
		}

// step 2B: check horizontally (to the right)
		for ($c = $col+1; $c <= 7; $c++) {
			if ( $this->columns[$c][$row] == $playerNumber )   	$count++;
			else 												break;
		}
		
		if ($count >= 4) {
			$this->winner = $playerNumber;
			return true;
		}
		
		$count = 1;
// step 3A: check diagonally (to bottom left)
		for ($c = $col-1, $r = $row-1; $c >= 1 && $r >= 1; $c--, $r--) {
			if ( $this->columns[$c][$r] == $playerNumber )   	$count++;
			else 												break;
		}	
		
// step 3B: check diagonally (to top right)
		for ($c = $col+1, $r = $row+1; $c <= 7 && $r <= 6; $c++, $r++) {
			if ( $this->columns[$c][$r] == $playerNumber )   	$count++;
			else 												break;
		}	
		
		if ($count >= 4) {
			$this->winner = $playerNumber;
			return true;
		}
		
		$count = 1;
// step 4A: check diagonally (to bottom right)
		for ($c = $col+1, $r = $row-1; $c <= 7 && $r >= 1; $c++, $r--) {
			if ( $this->columns[$c][$r] == $playerNumber )   	$count++;
			else 												break;
		}
		
// step 4B: check diagonally (to top left)
		for ($c = $col-1, $r = $row+1; $c >= 1 && $r <= 6; $c--, $r++) {
			if ( $this->columns[$c][$r] == $playerNumber )   	$count++;
			else 												break;
		}
		
		if ($count >= 4) {
			$this->winner = $playerNumber;
			return true;
		}
		
		return false;
	}
}
