<?php if ( ! defined( 'BASEPATH' )) exit( 'No direct script access allowed' );

$config = array( 'validate'=>array(	array('field'=>'first', 'label'=>'First Name:', 'rules'=> 'required|alpha'),
				      				   	array('field'=>'last', 'label'=>'Last Name:', 'rules'=>'required|alpha'),
										array('field'=>'cc_number', 'label'=>'Credit Card Number:', 'rules'=>'required|numeric|exact_length[16]'),
										array('field'=>'cc_exp_date', 'label'=>'Expiry Date:', 'rules'=>'required|numeric|exact_length[4]|callback_exp_check')));