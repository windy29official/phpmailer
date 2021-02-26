<?php 
require_once('koneksi.php');

$email = $_REQUEST['email'];

$cek_email	= mysqli_query($conn, "SELECT email FROM user WHERE email='$email'"); 
$data_email = mysqli_fetch_array($cek_email);
$email_data	= $data_email['email'];

if(empty($email_data)){
	$nama = str_replace("'",'`',$_REQUEST['nama']);
	$username = str_replace(' ','',$nama).date('zHi');
	$pass_tampil = generateRandomString();
	$pass = base64_encode($pass_tampil);

	$date	= date('ymd');
	$hasil = mysqli_query($conn, "SELECT max(iduser) as maxKode FROM user");
	$data = mysqli_fetch_array($hasil);
	$kdso = $data['maxKode'];
	$noUrut = (int) substr($kdso, 8, 7);
	if($noUrut == 9999999){ $noUrut = 0; } 
	else { $noUrut++; }
	$char = "US-";
	$kdso = $char . $date . sprintf("%07s", $noUrut);

	require 'PHPMailerold/PHPMailerAutoload.php';

	$mail = new PHPMailer;
	$mail->isSMTP();

		//untuk melihat error
//	 $mail->SMTPDebug = 2;
//	 $mail->Debugoutput = 'html';
//	 $mail->Timeout = 3600;
	 $mail->SMTPOptions = array(
		'ssl' => array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
		)
	);

	$mail->Host = 'smtp.gmail.com'; 
	$mail->Port = 587; 
	$mail->SMTPSecure = 'tsl'; 
	$mail->SMTPAuth = true;
	$mail->Username = 'pengaduanmobile@gmail.com'; 
	$mail->Password = 'pengaduan123'; 

	$mail->setFrom('pengaduanmobile@gmail.com', 'Untuk Kamu Sebagai Mahasiswa IST AKPRIND Yogyakarta');
	$mail->addAddress($email, $nama);
	$mail->isHTML(true);

	$mail->Subject = 'Informasi untuk mahasiwa baru yang terdaftar';

	$mail->Body = '<!DOCTYPE html>
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Email</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	</head>
	</html>
	<body style="margin: 0; padding: 0;">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">

	<tr>
	<td align="center" bgcolor="#4DA0DC">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
	<tr>
	<td align="center" valign="top" style="padding: 36px 24px;">
	<h2 style="margin: 0; font-size: 30px; font-weight: 700; font-color: #FFFFFF; letter-spacing: -1px; line-height: 40px;">Mahasiswa Baru</h2>
	</td>
	</tr>
	</table>
	</td>
	</tr>

	<tr>
	<td align="center" bgcolor="#e9ecef">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
	<tr>
	<td align="left" bgcolor="#ffffff" style="padding: 36px 24px 0; border-top: 3px solid #d4dadf;">
	<h1 style="margin: 0; font-size: 32px; font-weight: 700; letter-spacing: -1px; line-height: 48px;">Terimakasih anda telah menjadi bagian dari kami</h1>
	</td>
	</tr>
	</table>
	</td>
	</tr>

	<tr>
	<td align="center" bgcolor="#e9ecef">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">

	<tr>
	<td align="left" bgcolor="#ffffff" style="padding: 24px; font-size: 16px; line-height: 24px;">
	<p style="margin: 0;">Selamat kamu telah bergabung menjadi bagian dari kami.</p>
	</td>
	</tr>

	<tr>
	<td align="left" bgcolor="#ffffff" style="padding: 24px; font-size: 16px; line-height: 24px; text-align: center;">
	<p style="margin: 0;">USERNAME</p>
	<h3 style="margin: 0; font-size: 23px; font-weight: 700; letter-spacing: -1px; line-height: 48px;">'.$username.'</h3>
	</td>
	</tr>

	<tr>
	<td align="left" bgcolor="#ffffff" style="padding: 24px; font-size: 16px; line-height: 24px; text-align: center;">
	<p style="margin: 0;">PASSWORD</p>
	<h3 style="margin: 0; font-size: 23px; font-weight: 700; letter-spacing: -1px; line-height: 48px;">'.$pass_tampil.'</h3>
	</td>
	</tr>

	<tr>
	<td align="left" bgcolor="#ffffff">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
	<td align="center" bgcolor="#ffffff" style="padding: 12px;">
	<table border="0" cellpadding="0" cellspacing="0">
	</table>
	</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
	</body>
	</html>';

	if(!$mail->send()) {
		echo json_encode(array( 'response'=>'3', 'ket'=>'email tidak terkirim', ));
	} else {
		$query = mysqli_query($conn, "INSERT INTO user (iduser, nama, email, tgl_daftar, status_aktif, username, password) VALUES ('$kdso', '$nama', '$email', CURRENT_DATE, 'Y', '$username','$pass') ");
		if ($query) {
			echo json_encode(array( 'response'=>'1', 'ket'=>'berhasil tambah user', ));
		} else {
			echo json_encode(array( 'response'=>'0', 'ket'=>'query gagal', ));
		}
	}


} elseif(!empty($email_data)){
	echo json_encode(array( 'response'=>'2', 'ket'=>'email sudah dipakai', ));
}

function generateRandomString($length = 10) {
	$characters = '123456789ABCDEFGHJKMNPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

mysqli_close($conn);
?>