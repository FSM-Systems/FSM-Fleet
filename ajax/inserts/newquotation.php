<?php
include "../../inc/session_test.php";
include "connection.inc";
include "ajax_security.php";
require "../../inc/phpmailer/PHPMailerAutoload.php";

$ret = "";
$blnemail = true;

// First check if we have our email set and customer email set
if(!isset($_SESSION["email"]) || $_SESSION["email"] == "") {
	$ret = "NOEMAIL";
	$blnemail = false;
	echo $ret;
	return;
}

// Do not update if resending
if(!isset($_REQUEST["resend"])) {
	pg_query($con, "begin;");
	// Clean vars
	$l = !empty($_REQUEST["qlength"]) ? $_REQUEST["qlength"] : "null";
	$w = !empty($_REQUEST["qwidth"]) ? $_REQUEST["qwidth"] : "null";
	$h = !empty($_REQUEST["qheight"]) ? $_REQUEST["qheight"] : "null";
	$wt = !empty($_REQUEST["qweight"]) ? $_REQUEST["qweight"] : "null";
	$p = !empty($_REQUEST["qpermit_id"]) ? $_REQUEST["qpermit_id"] : "null";
	$n = !empty($_REQUEST["qnotes"]) ? "'" .$_REQUEST["qnotes"] . "'" : "null";
	$ni = !empty($_REQUEST["qnotesinternal"]) ? "'" .$_REQUEST["qnotesinternal"] . "'" : "null";

	$strsql = "insert into quotations (
	  qclient,
	  qdestination,
	  qcargo,
	  qlength,
	  qwidth,
	  qheight,
	  qweight,
	  qvalue,
	  qpermits,
	  qnotes,
	  qnotesinternal,
	  qcountry
	)
	values (
	" . $_REQUEST["qclient_id"] . ",
	upper('" . $_REQUEST["qdestination"] . "'),
	upper('" . $_REQUEST["qcargo"] . "'),
	" . $l . ",
	" . $w . ",
	" . $h . ",
	" . $wt . ",
	" . $_REQUEST["qvalue"] . ",
	" . $p . ",
	" . $n. ",
	" . $ni . ",
	" . $_REQUEST["qcountry_id"] . "
	) returning qid
	" ;

	$res = pg_query($con, $strsql);

	if(pg_result_error($res) != "") {
		$ret = pg_result_error($res);
		// return immediately as this is big error
		echo $ret;
		return;
	} else {
		$quotid = pg_fetch_result($res, 0, 0);;
		$ret = pg_fetch_result($res, 0, 0);
	}

} else {
	$quotid = $_REQUEST["qid"]; //receive id from calling form
}

// Send email
// fecth quote info
if($_REQUEST["save"] == "false") {
$res = pg_query($con, "select * from quotations left join customers on qclient=cid left join countries on qcountry = cnid where qid=" . $quotid);
$quote = pg_fetch_assoc($res, 0);
			$mailbody = "<html>
			<body>";
			$mailbody .= "<img class='logo' src='" . WWWADDRESS . PREFIX . "/" . $_SESSION['logo'] . "' style='float: left;'>
			<div style='border-bottom-color:" . MAINCOLOR . ";border-bottom-style: solid;border-bottom-width: 2px;width:100%;text-align: center;line-height: 30px;font-size: 16px;font-weight: bold;'>" . $_SESSION['companyname'] . " - TRANSPORT QUOTATION</div>
			<br>
			<p>Dear customer, kindly find attached our best quote for the transport you have requested.<br><br>

			<table border='0' style='width: 600px;'>
				<tr><td style='vertical-align: top;'>Quotation Ref:</td><td>" . $quotid . "</td></tr>
				<tr><td style='vertical-align: top;'>Destination:</td><td>" . $quote["qdestination"] . " -> " . $quote["cncountry"] . "</td></tr>
				<tr><td style='vertical-align: top;'>Good to be transported:</td><td>" . $quote["qcargo"] . "</td></tr>
				<tr><td style='vertical-align: top;'>Dimensions:</td><td>(L x W x H) " . $quote["qlength"] . " x " . $quote["qwidth"] . " x " . $quote["qheight"] . "</td></tr>
				<tr><td style='vertical-align: top;'>Cargo weight:</td><td>" . number_format($quote["qweight"],0) . "</td></tr>
				<tr><td style='vertical-align: top;'>Our best offer:</td><td>$" . number_format($quote["qvalue"],2) . "</td></tr>
				<tr><td style='vertical-align: top;'>Remarks:</td><td>" . nl2br($quote["qnotes"]) . "</td></tr>
			</table>
			<br><br>
			<p>We appreciate your request for doing business with us. Should you require further information, contact us by replying to this email or  at <a href='mailto:" . $_SESSION['companyemail'] . "?subject=Quotation%20REF%20" . $quotid . "'>" . $_SESSION['companyemail'] . "</a></p>
			<br>
			<br>
			<label style='font-weight: bold; text-decoration: underline'>All business is conducted in accordance with our standard trading terms and conditions.</label>
			";

			$mailbody .= "</body></html>";

			//Create a new PHPMailer instance
			$mail = new PHPMailer;
			// HTML email!
			$mail->IsHTML(true);
			//Tell PHPMailer to use SMTP
			$mail->isSMTP();
			//Enable SMTP debugging
			// 0 = off (for production use)
			// 1 = client messages
			// 2 = client and server messages
			$mail->SMTPDebug = 0;
			//Ask for HTML-friendly debug output
			$mail->Debugoutput = 'html';
			//Set the hostname of the mail server
			$mail->Host = $_SESSION['smtp'];
			$mail->SMTPOptions = array(
    							'ssl' => array(
        							'verify_peer' => false,
        							'verify_peer_name' => false,
        							'allow_self_signed' => false
    							)
							);
			//Set the SMTP port number - likely to be 25, 465 or 587
			$mail->Port = 465;
			$mail->SMTPSecure = "ssl";
			//Whether to use SMTP authentication
			$mail->SMTPAuth = true;
			//Username to use for SMTP authentication
			$mail->Username = $_SESSION['smtpuser'];
			//Password to use for SMTP authentication
			$mail->Password = $_SESSION['smtppassword'];
			//Set who the message is to be sent from
			$mail->setFrom($_SESSION["email"], $_SESSION['companyname']);
			// HTMK Email body
			$mail->Body = $mailbody;
			// Attach modified agreement
			//$mail->AddAttachment("/tmp/" . $uniq . "_registeragreement.docx");
			// Send to who?
			// Loop through checkboxes
			foreach($_POST["email_list"] as $email) {
				$mail->addAddress($email);
			}
			// Add cc sender
			$mail->AddCC($_SESSION["email"], $_SESSION['companyname']);
			//$mail->addAddress("fabrizio@fsm.co.tz");
			//$mail->addAddress('fabrizio.mazzoni@gmail.com');
			//Set the subject line
			$mail->Subject = $_SESSION['companyname'] . ' Quotation - Ref. ' . $quotid;
			//send the message, check for errors
			if (!$mail->send()) {
				$ret = $mail->ErrorInfo;
			    echo "Mailer Error: " . $mail->ErrorInfo;
			} else {
				// Commit transaction if we are creating quote
				if(!isset($_REQUEST["resend"])) {
					pg_query($con, "commit;");
				}
				$ret .= " MAILOK";
			}

}
// Return error codes if any
echo $ret;
?>