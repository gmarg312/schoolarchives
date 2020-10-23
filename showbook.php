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

if (isset($_GET['bookid'])){
	$bookid=$_GET['bookid'];

	if (isset($_GET['submit'])){

		$action = $_GET['action'];

		switch ($action) {
			case 'one':

			$bquery = "SELECT * from books WHERE bookid ='$bookid'";
			$bdata=mysqli_query($dbc,$bquery);
			if ($bdata) {
				$binfo = mysqli_fetch_array($bdata);
				$btitle = $binfo['booktitle'];
				echo 'Αποτελέσματα για: <span style="font-weight:bold;">'.$btitle.'</span></br></br>';
			}
			$bquery2 = "SELECT * FROM graduates WHERE bookid ='$bookid' ORDER BY sname,fname";
			//$bquery2 = "SELECT * FROM graduates WHERE bookid ='$bookid' ORDER BY imgname,sname,fname";
			$bdata2=mysqli_query($dbc,$bquery2);
			if ($bdata2) {
				$sum=0;
				echo '<table border="0" class="results"><tr class="headers_tr"><td>Επώνυμο</td><td>Όνομα</td> <td>Πατρώνυμο</td><td> Σχ. Έτος</td> <td>Κωδ. Βιβλίου </td><td>Όνομα αρχείου </td></tr>';
					while ($info = mysqli_fetch_array($bdata2))
					{
						$sum++;
						$sname = $info['sname'];
						$fname = $info['fname'];
						$fathname = $info['fathname'];
						$imgname = $info['imgname'];
						$bookid = $info['bookid'];
						$syear = $info['syear'];
						echo '<tr><td><a href="docimages/'.$bookid.'/'.$imgname.'.jpg"  target="_blank">'.$sname.'</a></td><td><a href="docimages/'.$bookid.'/'.$imgname.'.jpg"  target="_blank">'.$fname.'</a></td><td><a href="docimages/'.$bookid.'/'.$imgname.'.jpg"  target="_blank">'.$fathname.' </a></td><td><a href="docimages/'.$bookid.'/'.$imgname.'.jpg"  target="_blank">'.$syear.'</a></td><td> '.$bookid.'</td><td> '.$imgname.'</td></tr>';
					}
					echo '</table>';
					echo 'βρέθηκαν '.$sum.' εγγραφές.';
			}
				break;

			case 'two':
				// here is the big deal...
				$bookdirname = $_GET['bookdirname'];
				$showimagequery = "SELECT * FROM images WHERE bookid='$bookid'";
				$idata = mysqli_query($dbc, $showimagequery);
				if ($idata) {
					$imagesarray = array();
					while ($info = mysqli_fetch_array($idata)) {
						$imagesarray[] = $info['imgname'];
					}
				}
				else {
					echo mysqli_error($dbc);
				}
				//echo '<img src="docimages/'..'"';
				echo '<div id="listImages">';
				echo '<img src="images/arrown.png" onClick="nextImage()" />';
				echo $bookdirname.',';
				foreach ($imagesarray as $i) {
						echo $i.',';
				}
				echo '<img id="docimage" />';
				echo '<img src="images/arrowp.png" onClick="prevImage()" />';
				echo '</div>';

				break;
		}
	}//if (isset($_

	$folderquery = "SELECT * from books WHERE `bookid`='$bookid'";
	$result=mysqli_query($dbc,$folderquery);

	if ($result) {
		$info = mysqli_fetch_array($result);
		$bookid = $info['bookid'];
		$booktitle = $info['booktitle'];
		$bookdirname = $info['dirname'];
	}

	echo "<p>Δείτε τις εγγραφές του βιβλίου. ";
	echo '<form enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'" method="get">';
	echo '<input type="hidden" value="one" name="action"/>';
	echo '<input type="hidden" value="'.$bookid.'" name="bookid"/>';
	echo '<input type="submit" name="submit" value="OK">';
	echo '</form></p>';
	echo "<p>Δείτε το βιβλίο ανά εικόνα. ";
	echo '<form enctype="multipart/form-data" action="showbookimages.php" method="post">';
	echo '<input type="hidden" value="'.$bookid.'" name="bookid"/>';
	echo '<input type="hidden" value="'.$bookdirname.'" name="bookdirname"/>';
	echo '<input type="submit" name="submit" value="OK">';
	echo '</form></p>';

}//(isset($_GET['bookid'])
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
