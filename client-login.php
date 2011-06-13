<?php 
require_once 'Zend/Loader.php';

Zend_Loader::loadClass('Zend_Http_Client');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');	

// Gmail User Email
$G_Email = "[Set with Users Email Logging In]";

// Gmail User Password
$G_Pass = "[Set with Users Password Logging In]";
			
//Actually Register the Printer
$client = Zend_Gdata_ClientLogin::getHttpClient($G_Email, $G_Pass, 'cloudprint');
 
// Get the Token 
$_SESSION['Client_Login_Token'] = $client->getClientLoginToken(); 
?>
	