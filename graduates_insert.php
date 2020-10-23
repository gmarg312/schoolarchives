<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset="utf-8">
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


/***************************** insert file into temp table ******************************************/
	if(isset($_POST['filesubmit']))
    {
	 $action = $_POST['action'];
	 if ($action == "one") {
         $fname = $_FILES['importfile']['name'];
		 echo '<h4>Αρχείο προς εισαγωγή: '.$fname.' </h4>';
         //echo 'filename : '.$fname.'<br>';
     $chk_ext = explode(".",$fname);

     if(strtolower($chk_ext[1]) == "csv")
     {
		 	 echo '<table class="results"><tr><td>ΕΠΩΝΥΜΟ </td><td> ΟΝΟΜΑ</td><td> ΠΑΤΡΩΝΥΜΟ</td><td>ΚΩΔΙΚΟΣ ΒΙΒΛΙΟΥ </td><td>ΑΡΧΕΙΟ </td><td> ΣΧΟΛΙΚΟ ΕΤΟΣ</td> </tr> ';
       $filename = $_FILES['importfile']['tmp_name'];
       $handle = fopen($filename, "r");
			 $i=0;
			 $data1 = fgetcsv($handle,500, ";");
			 rewind($handle);
			 $data2 = fgetcsv($handle,500, ",");
			 rewind($handle);
			 if (count($data1) > count($data2)) {
				 $delimiterchar = ";";
			 } else {
				 $delimiterchar = ",";
			 }

       while ($data = fgetcsv($handle,500, $delimiterchar)) {
					$data0 = iconv("ISO-8859-7","UTF-8", $data[0]);
			 		if ($data0 != 'ΕΠΩΝΥΜΟ') {
						$query = "INSERT IGNORE INTO `graduates1` (`fname`, `sname`, `fathname`, `bookid`, `imgname`, `syear`) VALUES ( '$data[1]', '$data[0]','$data[2]', '$data[3]', '$data[4]', '$data[5]')";
						mysqli_query($dbc,"SET NAMES 'greek'");
						$result=mysqli_query($dbc,$query);
         }
			 }
			 $viewquery = "SELECT * FROM graduates1";
			 mysqli_query($dbc,"SET NAMES 'greek'");
			 $viewresult = mysqli_query($dbc, $viewquery);
			 while ($viewdata =mysqli_fetch_array($viewresult)) {
				 echo '<tr><td>'.iconv("ISO-8859-7","UTF-8", $viewdata[1]).'</td><td> '.iconv("ISO-8859-7","UTF-8", $viewdata[0]).'</td><td> '.iconv("ISO-8859-7","UTF-8", $viewdata[2]).'</td><td> '.iconv("ISO-8859-7","UTF-8", $viewdata[3]).'</td><td> '.iconv("ISO-8859-7","UTF-8", $viewdata[4]).'</td><td> '.iconv("ISO-8859-7","UTF-8", $viewdata[5]).'</tr>';
				 $i++;
			 }
         echo '</table>';

         if ($result) {
				 	echo '<br>Όλα εντάξει;<br>';
				 	echo ' Πρόκειται να εισαχθούν '.$i.' εγγραφές.<br><br>';
			 ?>

				 <form enctype="multipart/form-data" action='<?php echo $_SERVER["PHP_SELF"];?>' method='post'>
					<input type="hidden" value="two" name="action"/>
					<input type="hidden" value='<?php echo $i ?>' name="toinsert"/>
					<input type='submit' name='confirmsubmit' value='Προσθήκη στη βάση'>
					<input type='submit' name='cancelsubmit' value='Ακύρωση'>
				 </form>
				 <?php
			 }

          else {
			 echo 'Συνέβη σφάλμα'.mysqli_error($dbc);
		 }
		 fclose($handle);
		}
         else
         {
             echo "Invalid File";
         }
	 } //if action == one
    }
	if(isset($_POST['confirmsubmit'])) {
	 $action = $_POST['action'];
		 if ($action == "two") {
			$rowstoinsert = $_POST['toinsert'];

			$countquery = "SELECT COUNT(*) AS cnt FROM graduates";
			$resultcount=mysqli_query($dbc,$countquery);
			$countdata = mysqli_fetch_array($resultcount);
			$countbefore = $countdata['cnt'];


			$finalquery = "INSERT IGNORE INTO `graduates` (`fname`, `sname`, `fathname`, `bookid`, `imgname`, `syear`) SELECT  `fname`, `sname`, `fathname`, `bookid`, `imgname`, `syear` FROM graduates1";

				//mysqli_query($dbc,"SET NAMES 'greek'");
				$resultfinal=mysqli_query($dbc,$finalquery);

      if ($resultfinal) {
				 $countquery = "SELECT COUNT(*) AS cnt FROM graduates";
				 $resultcount=mysqli_query($dbc,$countquery);
				 $countdata = mysqli_fetch_array($resultcount);
				 $countafter = $countdata['cnt'];

				 $rowsinserted = $countafter - $countbefore;
				 echo "<h4>Εισαγωγή αρχείου επιτυχής. </h4>";
				 echo " Μπράβο το βάλατε.<br>";
				 echo ' Εισήχθησαν '.$rowsinserted.' εγγραφές.<br>';
				 if ($rowstoinsert > $rowsinserted) {
					$diff = $rowstoinsert - $rowsinserted;
					echo '<h5>'.$diff.' εγγραφές ήταν διπλότυπες και δεν εισήθχησαν. </h5>';
				 }
				 $deletequery = "DELETE FROM graduates1 WHERE 1";
				 $deleteresult=mysqli_query($dbc,$deletequery);
			 }
          else {
			 echo 'Συνέβη σφάλμα'.mysqli_error($dbc);
		 }
		}
	}
	if(isset($_POST['cancelsubmit'])) {
		$deletequery = "DELETE FROM graduates1 WHERE 1";
		$deleteresult=mysqli_query($dbc,$deletequery);
	}

if (!(isset($_POST['confirmsubmit'])) && !(isset($_POST['filesubmit']))) {
?>

<h4>Εισαγωγή αρχείου στη βάση του μητρώου.</h4>
<p class="small_text">Το αρχείο πρέπει να είναι σε μορφή csv οριοθετημένο με ; και να περιέχει τις εξής στήλες: ΕΠΩΝΥΜΟ, ΟΝΟΜΑ, ΠΑΤΡΩΝΥΜΟ, ΚΩΔΙΚΟΣ ΒΙΒΛΙΟΥ, ΟΝΟΜΑ ΑΡΧΕΙΟΥ ΕΙΚΟΝΑΣ, ΣΧΟΛΙΚΟ ΕΤΟΣ </p>
<p class="bottom_links"><a href="graduates.csv"> Πατήστε εδώ για να δείτε ένα υπόδειγμα αρχείου.</a> </p>

<form enctype="multipart/form-data" action='<?php echo $_SERVER["PHP_SELF"];?>' method='post'>
    <p> Επιλογή αρχείου: <input type='file' id='importfile' name='importfile' accept='.csv'>
	<input type="hidden" value="one" name="action"/>
    <input type='submit' name='filesubmit' value='Ανέβασμα αρχείου'>
	</p>
</form>

<?php
}
echo '<p class="bottom_links"><a href="index.php">Επιστροφή στην αρχική σελίδα</a></p>';
mysqli_close($dbc);
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
