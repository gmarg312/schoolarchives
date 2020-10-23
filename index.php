<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="CSS/epal-styles2.css" type="text/css" />
<link href="images/favicon.ico" rel="shortcut icon" type="image/x-icon" />
<title>To αρχείο</title>
</head>
<body>
  <?php
  $dbc=mysqli_connect('localhost','root','','arxeiodb') or
  die(mysqli_error());
  mysqli_set_charset ($dbc, 'utf8mb4');

  function findFolders() {
    $dir = "docimages";
	  $contents = scandir($dir);
	  $onlyfoldersarray= array();

	  foreach($contents as $f) {
		    $dot = '.';
		      $pos = strpos($f, $dot);
		        if ($pos === false) {
     	        $onlyfoldersarray[] = $f;
		         }
	   }
     return $onlyfoldersarray;
  }

  ?>

<div id="allcontent2">

<div id="header2">
</div>

  <h2>Ψηφιοποίηση αρχείου του ΕΠΑΛ Καρπενησίου </h2>
  <table class="layouttable2">
    <tr class="layouttable2_tr">
    <td class="layouttable2_td">
<div id="ena" >
  <h4>Αναζήτηση αποφοίτου με όνομα, επίθετο και πατρώνυμο</h4>
  <p class="small_text">Εισάγετε τους όρους της αναζήτησης με κεφαλαία γράμματα χωρίς σημεία στίξης. </p>
  <form id="namesearchform" name="namesearchform" method="post" action="verifyresults.php">
  <table class="simpleformtable">
  <tr>
    <td><label for "fname" class="small_text">Όνομα:</label></td>
    <td><input type="text" id="fname" name="fname" size="30"/></td>
    </tr>
    <tr>
    <td><label for "sname" class="small_text">Επίθετο:</label></td>
    <td><input type="text" id="sname" name="sname" size="30"/></td>
    </tr>
    <tr>
    <td><label for "fathname" class="small_text">Πατρώνυμο:</label></td>
    <td><input type="text" id="fathname" name="fathname" size="30"/></td>
    </tr>
    <tr><td>&nbsp;</td><td><input type="submit" value="Αναζήτηση" name="submit" /></td></tr>
  </table>
  <h5>Αναζήτηση με βάση το έτος</h5>
  <form id="yearsearchform" name="yearsearchform" method="post" action="verifyresults.php">
    <table class="simpleformtable">
      <tr><td>
        <label for "yearn" class="small_text">Έτος: </label>
      </td> <td>
        <select name="syear">
          <?php
          $yquery="SELECT DISTINCT syear FROM graduates ORDER BY syear ASC";
          //mysqli_query($dbc,"SET NAMES 'utf8mb4'");
          $yresult=mysqli_query($dbc,$yquery);
          if ($yresult) {
            while ($yinfo = mysqli_fetch_array($yresult)) {
              echo '<option value ="'.$yinfo['syear'].'">'.$yinfo['syear'].'</option>';
            }//while
          }
          ?>
        </select>
      </td> <td>
        <input type="submit" value="Αναζήτηση" name="submity" />
      </td></tr></table>
    </form>
</div>
</td>
<td class="layouttable2_td">
<div id="dyo" >
  <h4>Βιβλία που περιέχονται στη βάση: </h4>
  <?php
  $searchquery = "SELECT booktitle, bookid FROM books ORDER BY bookid";
  		//mysqli_query($dbc,"SET NAMES 'utf8mb4'");
  		$data=mysqli_query($dbc,$searchquery);
      $rowscount = mysqli_num_rows($data);
  		$bsum=0;
  		$totsum=0;
      $rowscompleted=0;
  		if ($data) {
  		echo '<table border="0" class="books2"><tr class="headers_tr2"><td>Βιβλίο</td><td>Κωδ.</td><td>Εικόνες </td> <td>Εγγραφές</td> <td>Ενσω-<br>μάτωση</td></tr>';
  			while ($info = mysqli_fetch_array($data))
  			{

  				$booktitle=$info['booktitle'];
  				$bookid = $info['bookid'];
          $booktitlelenght = strlen($booktitle);
          $rowscompleted += intval($booktitlelenght/24) +1;

  				$countquery = "SELECT distinct imgname FROM graduates WHERE bookid = '$bookid'";
  				$countdata=mysqli_query($dbc,$countquery);
  				$bookimgnum = mysqli_num_rows($countdata);

  				$numing =1;
  				$imgnumquery = "SELECT COUNT(imgname) AS numimg FROM images WHERE bookid='$bookid'";
  				$imgnumdata = mysqli_query($dbc,$imgnumquery);
  				if ($imgnumdata) {
  					$imgnuminfo = mysqli_fetch_array($imgnumdata);
  					$numimg = $imgnuminfo['numimg'];
  				}
  				else {
  					echo 'ton poulo'.mysqli_error($dbc);
  				}
          $numimg = ($numimg==0) ? 1 : $numimg;
  				$ensomatosi = $bookimgnum / $numimg*100;
  				$nombre = number_format($ensomatosi, 2, ',', ' ');
  				echo '<tr><td><a href="showbook.php?bookid='.$bookid.'">'.$booktitle.'</a></td><td>'.$bookid.'</td>';

  				$nquery = "SELECT COUNT( bookid ) AS cbook FROM graduates WHERE bookid = '$bookid' GROUP BY (bookid)";
  				$ndata=mysqli_query($dbc,$nquery);
  				$rsum=0;
  				if ($ndata) {
  					$ninfo = mysqli_fetch_array($ndata);
  					if ($ninfo['cbook']!='') {
  						$rsum = $ninfo['cbook'];
  					}
  				}

          $imagesumquery = "SELECT COUNT(bookid) AS sib FROM images WHERE bookid='$bookid' GROUP BY (bookid)";
          $idata=mysqli_query($dbc,$imagesumquery);
  				$isum=0;
  				if ($idata) {
  					$iinfo = mysqli_fetch_array($idata);
  					if ($iinfo['sib']!='') {
  						$isum = $iinfo['sib'];
  					}
  				}
  				echo '<td>'.$isum.'</td><td>'.$rsum.'</td><td>'.$nombre.'%</td></tr>';

  			}
  			echo '</table>';
  		}
  ?>
</div>
</td></tr><tr class="layouttable2_tr">
  <td class="layouttable2_td">
<div id="tria" class="books2">
  <h4> Στατιστικά </h4>
  <?php
  //Count the books
  $bookquery = "SELECT COUNT(bookid) AS sbook FROM books";
  $sbookdata = mysqli_query($dbc, $bookquery);
  $sbookinfo = mysqli_fetch_array($sbookdata);
  $bsum = $sbookinfo['sbook'];

  //count the records - distinct
  $distquery = "SELECT DISTINCT fname,sname,fathname FROM graduates";
  $distdata=mysqli_query($dbc,$distquery);
  $distnum = 0;
  if ($distdata) {
    $distnum = mysqli_num_rows($distdata);
  }

  //count the records - all
  $recordsquery = "SELECT COUNT(*) AS countall FROM graduates";
  $recordsdata=mysqli_query($dbc,$recordsquery);
  $recordsinfo = mysqli_fetch_array($recordsdata);
  $totsum = $recordsinfo['countall'];

  echo '<p>'.$bsum.' βιβλία <br>'.$totsum.' εγγραφές <br>'.$distnum.' μοναδικές εγγραφές.</p>';

  //Count the images
  $imgquery = "SELECT count(imgname) AS cimg FROM `images` ";
  $imgdata=mysqli_query($dbc,$imgquery);
  if ($imgdata) {
    $info = mysqli_fetch_array($imgdata);
    $imgnum = $info['cimg'];
    echo '<p class="books2"> '.$imgnum.' φωτογραφημένες σελίδες σε '.count(findFolders()).' φακέλους.</p>';
  }
  //echo '<p>Διάφορες πληροφορίες γύρω από τα δεδομένα που υπάρχουν στη βάση...</p>';
  //Find the most used names-male
  $mostusednamesquerym = "SELECT fname, COUNT(fname) AS cfname FROM graduates WHERE `fname` LIKE '%Σ' GROUP BY (fname) ORDER BY cfname DESC LIMIT 3";
  $mostusednamesdatam = mysqli_query($dbc, $mostusednamesquerym);
  echo 'Ποιά είναι τα πιο συχνά ονόματα;<br>';
  while ($mostusednamesarraym = mysqli_fetch_array($mostusednamesdatam)) {
    echo $mostusednamesarraym['fname'].' -'. $mostusednamesarraym['cfname'].' φορές <br>';
  }

  //Find the most used names-female
  $mostusednamesqueryf = "SELECT fname, COUNT(fname) AS cfname FROM graduates WHERE `fname` LIKE '%Α' OR `fname` LIKE '%Η' GROUP BY (fname) ORDER BY cfname DESC LIMIT 3";
  $mostusednamesdataf = mysqli_query($dbc, $mostusednamesqueryf);
  while ($mostusednamesarrayf = mysqli_fetch_array($mostusednamesdataf)) {
    echo $mostusednamesarrayf['fname'].' -'. $mostusednamesarrayf['cfname'].' φορές <br>';
  }
  //Find the most used surnames
  $mostusedsurnamesquery = "SELECT sname, COUNT(sname) AS csname FROM graduates GROUP BY (sname) ORDER BY csname DESC LIMIT 3";
  $mostusedsurnamesdata = mysqli_query($dbc, $mostusedsurnamesquery);
  echo '<br>Ποιά είναι τα πιο συχνά επίθετα;<br>';
  while ($mostusedsurnamesarray = mysqli_fetch_array($mostusedsurnamesdata)) {
    echo $mostusedsurnamesarray['sname'].' -'. $mostusedsurnamesarray['csname'].' φορές <br>';
  }
  echo '<br>';
  $statsquery = "SELECT * FROM otherstats ORDER BY searchcode DESC";
  $statsresult = mysqli_query($dbc, $statsquery);
  $totalsearches = mysqli_num_rows($statsresult);
  echo 'Aριθμός αναζητήσεων που έχουν γίνει: '.$totalsearches.'<br>';
  echo 'Πρόσφατες αναζητήσεις:<br>';
  if ($statsresult) {
    $limit=0;
    while (($statsarray = mysqli_fetch_array($statsresult)) && ($limit<3)){
      if ($statsarray['searchcode']==1) {
        $nameterms=explode(",",$statsarray['searchterms']);
        echo '<a href="verifyresults.php?submit=recent&sname='.$nameterms[0].'&fname='.$nameterms[1].'&fathname='.$nameterms[2].'">';
        foreach ($nameterms as $nt) {
          echo $nt.' ';
        }
        echo '</a><br>';
      } elseif ($statsarray['searchcode']==2){
        echo '<a href="verifyresults.php?submity=recent&syear='.$statsarray['searchterms'].'">Σχολικό Έτος '.$statsarray['searchterms'].'</a><br>';
      }
      $limit++;
    }
  }
  ?>
</div>
</td>
<td class="layouttable2_td">
<div id="tessera">
  <h4> Πίνακας ελέγχου </h4>
  <?php
  $foldersarray = findFolders();

  foreach ($foldersarray as $f) {
		$f = iconv("ISO-8859-7","UTF-8", $f);
		$folderquery = "SELECT * from books WHERE `dirname`='$f'";
		$result=mysqli_query($dbc,$folderquery);
		$isinserted = false;
		$rcnt = mysqli_num_rows($result);

		if ($rcnt) {
			$info = mysqli_fetch_array($result);
			$bookid = $info['bookid'];
			$booktitle = $info['booktitle'];
			$isinserted = true;
      $dirname = 'docimages/'.$f;

      //find which images are in folder $f
      $contents = scandir($dirname);
				$folderimagesarray = array();
				foreach($contents as $imagefile) {
					$chk_ext = explode(".",$imagefile);
					if((strtolower($chk_ext[1]) == "jpg")||(strtolower($chk_ext[1]) == "jpeg")||(strtolower($chk_ext[1]) == "png")||(strtolower($chk_ext[1]) == "tiff")) {
				 		$folderimagesarray[] = $imagefile; //this array contains the images in folder
					}
				}
        asort($folderimagesarray);

        //find which images from $f are in database
        $dbimagesarray = array();
        $showimagequery = "SELECT * FROM images WHERE bookid='$bookid' ORDER BY imgname";
      	$idata = mysqli_query($dbc, $showimagequery);
      	if ($idata) {
      			while ($info = mysqli_fetch_array($idata)) {
      						$dbimagesarray[] = $info['imgname']; //this array contains the images in db
      			}
      	}
      $result1 = array_diff($folderimagesarray, $dbimagesarray);
      $result2 = array_diff($dbimagesarray, $folderimagesarray);
      if (count($result1)) {
        echo count($result1).' εικόνες βρίσκονται στο φάκελο '.$f.' αλλά δεν υπάρχουν στη βάση<br>';
        /*echo 'Οι παρακάτω εικόνες βρίσκονται στο φάκελο '.$f.' αλλά δεν υπάρχουν στη βάση<br>';
        foreach ($result1 as $r1) {
          echo $r1;
        }*/
      }
      if (count($result2)) {
        /*
        echo 'Οι παρακάτω εικόνες βρίσκονται  στη βάση αλλά δεν υπάρχουν στο φάκελο '.$f.'<br>';
        foreach ($result2 as $r2) {
          echo $r2;
        }*/
        echo  count($result2).' εικόνες βρίσκονται στη βάση αλλά δεν υπάρχουν στο φάκελο '.$f.'<br>';
      }

		}
    //Folder does not exist in database
    else {
      echo '<p class="books2">Ο φάκελος <a href="docimages/'.$f.'"> '.$f.'</a> δεν έχει εισαχθεί στη βάση.';
      echo '<form enctype="multipart/form-data" action="book_insert.php" method="post">';
		  echo '<input type="hidden" value="'.$f.'" name="foldername"/>';
      echo $f;
		  echo '<input type="hidden" value="one" name="action"/>';
		  echo '<input type="submit" name="submit" value="Προσθήκη στη βάση">';
      echo '</p>';
    }
  }
  ?>
  <p class="books2"> <a href="graduates_insert.php">Εισαγωγή αρχείου παλαιών μαθητών-αποφοίτων </a></p>
  <p class="books2"> <a href="book_insert.php">Εισαγωγή φωτογραφημένου αρχείου </a></p>
  τι θα έχει εδώ; ας πούμε, ειδοποίηση για νέες φωτογραφίες - φακέλους, λινκ για διαχείριση (μετατροπή του book_insert σε διαχείριση συσχέτισης φακέλων-βιβλίων)
</div>
</td></tr>
</table>

</div>

<?php
mysqli_close($dbc);
?>

</body>
</html>
