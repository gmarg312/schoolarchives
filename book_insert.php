<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="CSS/epal-styles2.css" type="text/css" />
<link href="images/favicon.ico" rel="shortcut icon" type="image/x-icon" />
<title>Πιστοποίηση τίτλων σπουδών - ΕΠΑΛ Καρπενησίου</title>
<script>
function checkInput() {
		var x = Number(document.getElementById("booknum").value);
		//var x = parseInt(document.getElementById("booknum").value);
		var a = document.getElementById("booktitle");
		var b = document.getElementById("booksubmit");
		if (Number.isInteger(x)) {
			a.disabled = false;
			b.disabled = false;
		} else {
			alert('Το πεδίο Αριθμός Βιβλίου πρέπει υποχρεωτικα να είναι ακέραιος αριθμός!');
			a.disabled = true;
			b.disabled = true;
			document.getElementById("booknum").focus();
		}
}
</script>
</head>
<body>
<div id="allcontent2">
<div id="header">
</div>
<div id="cont2">
	<h4>Εισαγωγή φακέλου εικόνων στη βάση του μητρώου.</h4>
	<h5>Οι εικόνες του κάθε βιβλίου πρέπει να βρίσκονται σε ένα φάκελο μέσα στη διαδρομή c:\xampp\htdocs\docimages.</h5>

<?php
$dbc=mysqli_connect('localhost','root','','arxeiodb') or
die(mysqli_error());
mysqli_set_charset ($dbc, 'utf8mb4');
if(isset($_POST['submit']))	{
	$action = $_POST['action'];
	switch($action) {
		case "one":

			$dirname = $_POST['foldername'];
//Number.isInteger(x)
			echo '<p>Δώστε τα στοιχεία για το βιβλίο που αντιστοιχεί στο φάκελο  '.$dirname.' </p>';
			echo '<form enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'" method="post">';
			echo '<table><tr>';
			echo '<td>Αριθμός βιβλίου: </td>';
			echo '<td><input type="text" size="10" onblur="checkInput()" name="booknum" id="booknum"value="'.$dirname.'"/></td></tr>';
			echo '<tr><td>Τίτλος βιβλίου: </td>';
			echo '<td><input type="text" size="80" name="booktitle" id="booktitle" /></td></tr>';
			echo '<tr><td>Όνομα φακέλου: </td>';
			echo '<td>'.$dirname.'<input type="hidden" name="dirname" value="'.$dirname.'"/></td></tr>';
			echo '<input type="hidden" value="two" name="action"/>';
			echo '<tr><td><br><input type="submit" name="submit" id="booksubmit" value="Προσθήκη στη βάση"></td></tr>';
			echo '</table>';
			break;

		case "two":

			$dirname = $_POST['dirname'];
			$bookid = $_POST['booknum'];
			$booktitle = $_POST['booktitle'];

			$query = "INSERT IGNORE INTO `books` (`bookid`, `booktitle`, `dirname`) VALUES ('$bookid','$booktitle','$dirname')";
			$result=mysqli_query($dbc,$query);
			 	if ($result) {
				 	echo '<p>Η εισαγωγή του βιβλίου ήταν επιτυχής. </p>';
				 	echo '<p>Εισαγωγή εικόνων που βρέθηκαν στο φάκελο '.$dirname.'; </p>';
					echo '<form enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'" method="post">';
					echo '<input type="hidden" value="three" name="action"/>';
					echo '<input type="hidden" value="'.$bookid.'" name="booknum"/>';
					echo '<input type="hidden" name="dirname" value="'.$dirname.'"/>';
					echo '<input type="submit" name="submit" value="Εισαγωγή εικόνων">';
					echo '</form>';

			 	} else {
					echo mysqli_error($dbc);
			 		echo "<p>Η εισαγωγή δεν έγινε.</p>";
			 	}
				break;

		case "three":

				$dirname = 'docimages/'.$_POST['dirname'];
				$bookid = $_POST['booknum'];

				$contents = scandir($dirname);
				$onlyimagesarray = array();
				foreach($contents as $f) {
					$chk_ext = explode(".",$f);
					if((strtolower($chk_ext[1]) == "jpg")||(strtolower($chk_ext[1]) == "jpeg")||(strtolower($chk_ext[1]) == "png")||(strtolower($chk_ext[1]) == "tiff")) {
				 		$onlyimagesarray[] = $f;
					}
				}
				$imagesinserted=0;
				foreach ($onlyimagesarray as $i) {
					echo 'Εισαγωγή εικόνων για τον κωδικό βιβλίου '.$bookid;
					echo ': '.$i.'<br>';
						$imagequery = "INSERT INTO `images` (`bookid`, `imgname`) VALUES ('$bookid','$i')";
						$result=mysqli_query($dbc,$imagequery);
						 	if ($result) {
									$imagesinserted++;
							} else {
									echo mysqli_error($dbc);
									echo "<p>Η εισαγωγή των εικόνων απέτυχε. </p>";
							}
				}
				echo '<p>Εισήχθησαν '.$imagesinserted.' εικόνες. </p>';
				echo '<p class="bottom_links"><a href="book_insert.php">Εισαγωγή βιβλίου.</a></p>';
				break;

		case "update":

			break;
	}//switch

}//if(isset($_POST['submit']))
 else {

	$dir = "docimages";
	$contents = scandir($dir);
	$c=0;
	if (count($contents)>2) {
		echo '<p>Οι παρακάτω φάκελοι βρέθηκαν μέσα στο φάκελο docimages: </p>';
	}
	$onlyfoldersarray= array();
	foreach($contents as $f) {
		$dot = '.';
		$pos = strpos($f, $dot);
		if ($pos === false) {
     	$onlyfoldersarray[] = $f;
		}
	}
	foreach ($onlyfoldersarray as $f) {
		//echo 'f='.$f;
		//$f = iconv("ISO-8859-7","UTF-8", $f);
		$f = iconv("Windows-1253","UTF-8", $f);
		$c++;
		$folderquery = "SELECT * from books WHERE `dirname`='$f'";
		$result=mysqli_query($dbc,$folderquery);
		$isinserted = false;
		$rcnt = mysqli_num_rows($result);
		//echo $rcnt;
		if ($rcnt) {
			$info = mysqli_fetch_array($result);
			$bookid = $info['bookid'];
			$booktitle = $info['booktitle'];
			$isinserted = true;
		}
		echo '<table  class="books2">';
		echo '<tr><td>'.$c.')</td><td> <a href="docimages/'.$f.'">Φάκελος: '.$f.'</a></td>';
		echo '<td><form enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'" method="post">';
		echo '<input type="hidden" value="'.$f.'" name="foldername"/>';

		if (!$isinserted) {
			echo '<input type="hidden" value="one" name="action"/>';
			echo '<input type="submit" name="submit" value="Προσθήκη στη βάση">';
		} else {
			echo '<td class="plaintext">Ο φάκελος έχει εισαχθεί στη βάση για το βιβλίο '.$bookid.' </td><td class="smallitalics">"'.$booktitle.'" </td><td>';
			//echo '<input type="hidden" value="update" name="action"/>';
			//echo '<input type="submit" name="submit" value="Αλλαγή">';
		}
		echo '</form></td></tr></table>';
	}
} //else

echo '<p class="bottom_links"><a href="index.php">Επιστροφή στην αρχική σελίδα</a></p>';
//echo '<form enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'" method="post">';
//echo '<input type="submit" name="deletespam" value="Διαγραφή δοκιμαστικών δεδομένων">';
//echo '</form>';
if (isset($_POST['deletespam']))	{
	$deletespamquery = "DELETE FROM `books` WHERE `bookid` > 30";
	$deleteresult=mysqli_query($dbc,$deletespamquery);
	if ($deleteresult) {
		echo '<p>Τα δοκιμαστικά δεδομένα διαγράφηκαν. </p>';
		echo '<p class="bottom_links"><a href="book_insert.php">Εισαγωγή βιβλίου.</a></p>';
	}
}

mysqli_close($dbc);

echo '</div><div id="footer2">';

include 'footer2.php';

echo '</div></div></body></html>';
?>
