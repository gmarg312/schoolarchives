<?php

echo '<meta charset="utf-8">';

function connecttodb ($sn,$un,$pw) {
	$dbc = mysqli_connect($sn, $un, $pw);
	$usercheckquery = "SELECT EXISTS(SELECT 1 FROM mysql.user WHERE user = '$un')";
	mysqli_query($dbc, $usercheckquery);

	if (!$dbc) {
		die("Σφάλμα σύνδεσης: " . mysqli_error() );
		return false;
    }
	else {
		mysqli_set_charset ($dbc, 'utf8mb4');
		return $dbc;
	}

}

if (!isset($_POST['submit'])) {
  $servername = "localhost";
  $username = "root";
  $password = "";
  echo '<p style="font-weight:bold;">Εγκατάσταση εφαρμογής. </p>';
  echo 'Εισάγετε τα στοιχεία για τη σύνδεση με τον διακομιστή της βάσης. <br>Εάν η βάση πρόκειται να εγκατασταθεί τοπικά, τότε αφήστε τα ίδια και κατά πάσα πιθανότητα θα δουλέψει.<br><br>';
  echo '<form id="configform" name="configform" method="post" action="'.$_SERVER["PHP_SELF"].'"> ';
  echo '<table>';
  echo '<tr><td><label>Όνομα διακομιστή βάσης:</label></td><td><input type="text" id="servername" name="servername" size="30" value="'.$servername.'"/></td></tr>';
  echo '<tr><td><label>Όνομα χρήστη:</label></td><td><input type="text" id="username" name="username" size="30" value="'.$username.'"/></td></tr>';
  echo '<tr><td><label>Συνθηματικό:</label></td><td><input type="text" id="password" name="password" size="30" value="'.$password.'"/></td></tr>';
  echo '<input type="hidden" name="state" value="connection">';
  echo '<tr><td><input type="submit" name="submit" value="Σύνδεση με το διακομιστή"></td></tr></table>';
  echo '</form>';
}
elseif (isset($_POST['submit'])) {
  $state = $_POST['state'];
  $servername = $_POST['servername'];
  $username = $_POST['username'];
  $password = $_POST['password'];
  //$dbc = mysqli_connect($servername, $username, $password);
	$dbc = connecttodb($servername, $username, $password);

  switch ($state) {
    case "connection":

    if (!$dbc) {
  		die("Σφάλμα σύνδεσης 1: " . mysqli_connect_error());
    }
	else {
		echo '<p style="font-weight:bold;">Η σύνδεση με το διακομιστή της βάσης ήταν επιτυχής! </p>';

		//check if database exists
		$checkifexistsquery = "SHOW DATABASES LIKE 'arxeiodb'";
		$checkifexistsresult = mysqli_query($dbc, $checkifexistsquery);

      if (mysqli_num_rows($checkifexistsresult)) {
        echo '<p style="font-weight:bold;">Η βάση δεδομένων υπάρχει ήδη! Εάν προχωρήσετε, η παλιά βάση δεδομένων θα διαγραφεί! </p>';
        echo '<form id="confirmdropform" name="confirmdropform" method="post" action="'.$_SERVER["PHP_SELF"].'"> ';
        echo '<input type="hidden" name="servername" value="'.$servername.'">';
		echo '<input type="hidden" name="username" value="'.$username.'">';
		echo '<input type="hidden" name="password" value="'.$password.'">';
		echo '<input type="hidden" name="state" value="delete">';
		echo '<input type="submit" name="submit" value="Διαγραφή παλιάς βάσης και δημιουργία νέας"><br>';
        echo '</form>';

        echo '<form id="gotostartform" name="gotostartform" method="post" action="index.php"> ';
        echo '<br><input type="submit" name="gotostart" value="Ακύρωση δημιουργίας και μετάβαση στην αρχική σελίδα της εφαρμογής">';
        echo '</form>';
      }
      else {
        echo '<form id="confirmproceedform" name="confirmproceedform" method="post" action="'.$_SERVER["PHP_SELF"].'"> ';
		echo '<input type="hidden" name="state" value="create">';
        echo '<input type="hidden" name="servername" value="'.$servername.'">';
		echo '<input type="hidden" name="username" value="'.$username.'">';
		echo '<input type="hidden" name="password" value="'.$password.'">';
		echo '<table><tr><td>Όνομα σχολείου:</td><td><input type="text" name="schname" size="40" /></td></tr>';
		echo '<tr><td>Όνομα σχολείου (γενική):</td><td><input type="text" name="schnamegen" size="40" /></td></tr></table>';
        echo '<input type="submit" name="submit" value="Συνέχεια"><br>';
        echo '</form>';
      }
    }
    break;

    case "create":

		// Create database
		$schname = $_POST['schname'];
		$schnamegen = $_POST['schnamegen'];
		$createdbquery = "CREATE DATABASE arxeiodb CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
		if (mysqli_query($dbc, $createdbquery)) {
			echo '<p>Η βάση δεδομένων δημιουργήθηκε επιτυχώς. </p>';

			$dbc2=mysqli_connect($servername,$username,$password,'arxeiodb') or
			die("Σφάλμα σύνδεσης: " .mysqli_error());
			mysqli_set_charset ($dbc2, 'utf8mb4');

		  $createtablesstate=true;

		  $createbooksquery = "CREATE TABLE `books` (  `bookid` smallint(6) NOT NULL,  `booktitle` text NOT NULL,  `dirname` varchar(60) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
		  if (mysqli_query($dbc2,$createbooksquery)) {
			echo '<p>Ο πίνακας books δημιουργήθηκε επιτυχώς</p>';
		  } else {
			echo '<p>Σφάλμα στη δημιουργία του πίνακα books: '.mysqli_error().'</p>';
			$createtablesstate=false;
		  }

		  $creategraduatesquery = 'CREATE TABLE `graduates` (`fname` varchar(45) NOT NULL,  `sname` varchar(45) NOT NULL,  `fathname` varchar(45) DEFAULT NULL,  `bookid` smallint(6) NOT NULL,  `imgname` varchar(20) NOT NULL,  `syear` varchar(10) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;';
		  if (mysqli_query($dbc2,$creategraduatesquery)) {
			echo '<p>Ο πίνακας graduates δημιουργήθηκε επιτυχώς</p>';
		  } else {
			echo 'Σφάλμα στη δημιουργία του πίνακα graduates: '.mysqli_error().'<br>';
			$createtablesstate=false;
		  }

		  $creategraduates1query = 'CREATE TABLE `graduates1` (`fname` varchar(45) NOT NULL,  `sname` varchar(45) NOT NULL,  `fathname` varchar(45) DEFAULT NULL,  `bookid` smallint(6) NOT NULL,  `imgname` varchar(20) NOT NULL,  `syear` varchar(10) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;';
		  if (mysqli_query($dbc2,$creategraduates1query)) {
			echo '<p>Ο πίνακας graduates1 δημιουργήθηκε επιτυχώς</p>';
		  } else {
			echo 'Σφάλμα στη δημιουργία του πίνακα graduates1: '.mysqli_error().'<br>';
			$createtablesstate=false;
		  }

		  $createimagesquery = 'CREATE TABLE `images` (  `bookid` smallint(6) NOT NULL,  `imgname` varchar(20) CHARACTER SET utf8mb4 NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';
		  if (mysqli_query($dbc2,$createimagesquery)) {
			echo '<p>Ο πίνακας images δημιουργήθηκε επιτυχώς</p>';
		  } else {
			echo 'Σφάλμα στη δημιουργία του πίνακα images: '.mysqli_error().'<br>';
			$createtablesstate=false;
		  }

		 $createotherstatsquery = 'CREATE TABLE `otherstats` (  `searchecode` int(3) UNSIGNED NOT NULL,  `searchterms` varchar(60) CHARACTER SET utf8mb4 DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';
		 if (mysqli_query($dbc2,$createotherstatsquery)) {
		   echo '<p>Ο πίνακας otherstats δημιουργήθηκε επιτυχώς</p>';
		   } else {
		   echo 'Σφάλμα στη δημιουργία του πίνακα otherstats: '.mysqli_error().'<br>';
		   $createtablesstate=false;
		 }

		 $createyearsquery = 'CREATE TABLE `years` (  `bookid` smallint(6) NOT NULL,  `yearn` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;';
		 if (mysqli_query($dbc2,$createyearsquery)) {
		   echo '<p>Ο πίνακας years δημιουργήθηκε επιτυχώς</p>';
		   } else {
		   echo 'Σφάλμα στη δημιουργία του πίνακα years: '.mysqli_error().'<br>';
		   $createtablesstate=false;
		 }
		 $createschnamequery = 'CREATE TABLE `schoolname` ( `schname` varchar(60) NOT NULL,  `schnamegen` varchar(60) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;';
		 if (mysqli_query($dbc2,$createschnamequery)) {
		   echo '<p>Ο πίνακας schoolname δημιουργήθηκε επιτυχώς</p>';
		   } else {
		   echo 'Σφάλμα στη δημιουργία του πίνακα schoolname: '.mysqli_error().'<br>';
		   $createtablesstate=false;
		 }

		 $alterbooksquery = 'ALTER TABLE `books`  ADD PRIMARY KEY (`bookid`);';
		 if ($alterbooksres=mysqli_query($dbc2,$alterbooksquery)) {
		   echo '... ';
		 }
		 $alterimagesquery = 'ALTER TABLE `images`  ADD KEY `bookid` (`bookid`);';
		 if ($alterimagesres=mysqli_query($dbc2,$alterimagesquery)) {
		   echo '... ';
		 }
		 $alterimagesquery2 = 'ALTER TABLE `images`  ADD CONSTRAINT `images_ibfk_1` FOREIGN KEY (`bookid`) REFERENCES `books` (`bookid`) ON DELETE CASCADE ON UPDATE CASCADE;';
		 if ($alterimages2res=mysqli_query($dbc2,$alterimagesquery2)) {
		   echo '... ';
		 }
		 $schnamequery = "INSERT INTO schoolname VALUES ('$schname','$schnamegen')";
		 if ($insertinfores=mysqli_query($dbc2, $schnamequery)) {
			 echo '... ';
		 }

		 if ($createtablesstate && $alterbooksres && $alterimagesres && $alterimages2res) {
			echo '<br><p>Η δημιουργία της βάσης ολοκληρώθηκε επιτυχώς.</p>';
			echo '<form id="gotostartform" name="gotostartform" method="post" action="index.php"> ';
			echo '<input type="submit" name="submit" value="Πήγαινέ με στην αρχική σελίδα της εφαρμογής">';
		 }
		mysqli_close($dbc2);
    }
    else {
		echo 'Σφάλμα στη δημιουργία της βάσης: ' . mysqli_error($dbc);
		echo 'Σφάλμα στη δημιουργία της βάσης: ' . mysqli_error($dbc2);
    }

    break;

    case "delete":

		$dropdbquery = "DROP DATABASE IF EXISTS arxeiodb";
		mysqli_query($dbc, $dropdbquery);

		echo "Η βάση διαγράφηκε.";
		echo '<form id="confirmproceedform" name="confirmproceedform" method="post" action="'.$_SERVER["PHP_SELF"].'"> ';
		echo '<input type="hidden" name="servername" value="'.$servername.'">';
		echo '<input type="hidden" name="username" value="'.$username.'">';
		echo '<input type="hidden" name="password" value="'.$password.'">';
		echo '<input type="hidden" name="state" value="create">';
		echo '<table><tr><td>Όνομα σχολείου:</td><td><input type="text" name="schname" size="40" /></td></tr>';
		echo '<tr><td>Όνομα σχολείου (γενική):</td><td><input type="text" name="schnamegen" size="40" /></td></tr></table>';
		echo '<input type="submit" name="submit" value="Συνέχεια"><br>';
		echo '</form>';

    break;
	mysqli_close($dbc);
  }
}
?>
