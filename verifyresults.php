<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="CSS/epal-styles.css" type="text/css" />
<link href="images/favicon.ico" rel="shortcut icon" type="image/x-icon" />
<title>Πιστοποίηση τίτλων σπουδών - ΕΠΑΛ Καρπενησίου</title>
</head>
<body>
<div id="allcontent">
<div id="header">
</div>
<div id="cont2">
<?php
$dbc=mysqli_connect('localhost','root','','arxeiodb') or
die(mysqli_error());
mysqli_set_charset ($dbc, 'utf8mb4');

if (((isset($_POST['submit']))||(isset($_POST['submity']))) || ((isset($_GET['submit'])) || (isset($_GET['submity'])))) {
	if ((isset($_POST['submit']))||(isset($_GET['submit']))) {
	if (isset($_POST['sname'])) {
		$fname=$_POST['fname'];
		$sname=$_POST['sname'];
		$fathname = $_POST['fathname'];
		$searchterm = $sname.','.$fname.','.$fathname;
		$statquery="INSERT INTO otherstats (searchcode, searchterms) VALUES (1,'$searchterm')";
		$data=mysqli_query($dbc,$statquery);
	} elseif (isset($_GET['sname'])) {
		$fname=$_GET['fname'];
		$sname=$_GET['sname'];
		$fathname = $_GET['fathname'];
	}
	if ($sname!='') {
		echo 'Αποτελέσματα για: <span style="font-weight:bold;">'.$fname.' '.$sname.' '.$fathname.'</span></br></br>';
		$searchquery = "SELECT g.sname, g.fname, g.fathname, g.imgname,g.syear, b.bookid, b.booktitle FROM graduates g INNER JOIN books b USING(bookid) WHERE g.sname LIKE '$sname%' AND g.fname LIKE '$fname%' AND g.fathname LIKE '$fathname%'";
		//mysqli_query($dbc,"SET NAMES 'greek'");
		$data=mysqli_query($dbc,$searchquery);
		$sum=0;
		if ($data) {
		echo '<table border="0" class="results"><tr class="headers_tr"><td>Επώνυμο</td><td>Όνομα</td> <td>Πατρώνυμο</td> <td> Σχ. Έτος</td><td> Βιβλίο</td><td>Κωδ. Βιβλίου </td></tr>';
			while ($info = mysqli_fetch_array($data))
			{
				$sum++;
				$sname=$info['sname'];
				$fname = $info['fname'];
				$fathname = $info['fathname'];
				$imgname = $info['imgname'];
				$syear = $info['syear'];
				$bookid = $info['bookid'];
				$booktitle = $info['booktitle'];
				echo '<tr><td><a href="docimages/'.$bookid.'/'.$imgname.'.jpg"  target="_blank">'.$sname.'</a></td><td><a href="docimages/'.$bookid.'/'.$imgname.'.jpg"  target="_blank">'.$fname.'</a></td><td><a href="docimages/'.$bookid.'/'.$imgname.'.jpg"  target="_blank">'.$fathname.' </a></td><td><a href="docimages/'.$bookid.'/'.$imgname.'.jpg"  target="_blank">'.$booktitle.' </a></td><td> '.$bookid.'</td></tr>';
			}
			echo '</table>';
			if ($sum==0) {
				echo '<br><br>δε βρέθηκαν εγγραφές.';
				}
			elseif ($sum==1) {
				echo '<br><br>βρέθηκε '.$sum.' εγγραφή.';
				}
			elseif ($sum>1) {
				echo '<br><br>βρέθηκαν '.$sum.' εγγραφές.';
				}
		}
	}
	else {
		echo 'Παρακαλώ συμπληρώστε το όνομα και το επώνυμο';
	}
}//((isset($_POST['submit']))

if ((isset($_POST['submity'])) || (isset($_GET['submity']))) {
	if (isset($_POST['syear'])) {
			$syear=$_POST['syear'];
			$statquery="INSERT INTO otherstats (searchcode, searchterms) VALUES (2,'$syear')";
			$data=mysqli_query($dbc,$statquery);
	} elseif (isset($_GET['syear'])) {
		$syear=$_GET['syear'];
	}

echo 'Αποτελέσματα για το σχολικό έτος: <span style="font-weight:bold;">'.$syear.'</span></br></br>';
$searchquery = "SELECT g.sname, g.fname, g.fathname, g.imgname, b.booktitle, b.bookid FROM graduates g INNER JOIN books b USING(bookid)  WHERE g.syear = '$syear' ORDER BY g.sname ASC, g.fname";
		//mysqli_query($dbc,"SET NAMES 'greek'");
		$data=mysqli_query($dbc,$searchquery);
		$sum=0;
		if ($data) {
		echo '<table border="0" class="results"><tr class="headers_tr"><td>Επώνυμο</td><td>Όνομα</td> <td>Πατρώνυμο</td>  <td> Βιβλίο</td><td>Κωδ. Βιβλίου</td></tr>';
			while ($info = mysqli_fetch_array($data))
			{
				$sum++;
				$sname=$info['sname'];
				$fname = $info['fname'];
				$fathname = $info['fathname'];
				$imgname = $info['imgname'];
				$bookid =  $info['bookid'];
				$booktitle = $info['booktitle'];
				echo '<tr><td><a href="docimages/'.$bookid.'/'.$imgname.'.jpg"  target="_blank">'.$sname.'</a></td><td><a href="docimages/'.$bookid.'/'.$imgname.'.jpg"  target="_blank">'.$fname.'</a></td><td><a href="docimages/'.$bookid.'/'.$imgname.'.jpg"  target="_blank">'.$fathname.' </a></td><td><a href="docimages/'.$bookid.'/'.$imgname.'.jpg"  target="_blank">'.$booktitle.' </a></td><td> '.$bookid.'</td></tr>';
			}
			echo '</table>';
			if ($sum==0) {
				echo '<br><br>δε βρέθηκαν εγγραφές.';
				}
			elseif ($sum==1) {
				echo '<br><br>βρέθηκε '.$sum.' εγγραφή.';
				}
			elseif ($sum>1) {
				echo '<br><br>βρέθηκαν '.$sum.' εγγραφές.';
				}
		}
}//(isset($_POST['submity']))

}//((isset($_POST['submit']))||(isset($_POST['submity'])))
else {
	echo 'what are you doing here?';
	}

mysqli_close($dbc);
echo '<p class="bottom_links"><a href="index.php">Επιστροφή στην αρχική σελίδα</a></p>';
?>
</div>
<div id="footer2">
<?php
include 'footer2.php';
?>
</div>
</div>
</body>
</html>
