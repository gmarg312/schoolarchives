<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="CSS/epal-styles2.css" type="text/css" />
<link href="images/favicon.ico" rel="shortcut icon" type="image/x-icon" />
<title>Πιστοποίηση τίτλων σπουδών - ΕΠΑΛ Καρπενησίου</title>
<script>

		var imagesarray=[];
		var imagesraw='';
		var poso=0;
		var foldername='';
		var toggleState = 1;

		document.onkeydown = function(e) {
		    switch (e.keyCode) {
		        case 37:
		            prevImage();
		            break;
		        case 39:
		            nextImage();
		            break;
		    }
		};

		function initFunc() {
			imagesraw = document.getElementById("listImages").innerHTML;
			imagesarray = imagesraw.split(',');
			foldername = 'docimages/'+imagesarray[0];
			poso++;

			document.getElementById("showImages").style.display = "inline-block";
			document.getElementById("leftarrow").style.display = 'none';
			document.getElementById("docimage").src = foldername+'/'+imagesarray[1];
			document.getElementById("docimage").style.height = '100vh';
			//document.getElementById("testarea2").innerHTML = '<p>toggleState='+toggleState+'<br>foldername='+foldername+'<br>poso='+poso+'</p>';
		}

		function nextImage() {
			if (poso < imagesarray.length-2){
				 poso++;
				 document.getElementById("docimage").src = foldername+'/'+imagesarray[poso];
			}
			if (poso >= imagesarray.length-2) {
				document.getElementById("rightarrow").style.display = 'none';
			}
			//document.getElementById("testarea2").innerHTML = '<p>toggleState='+toggleState+'<br>foldername='+foldername+'<br>poso='+poso+'<br>img='+imagesarray[poso]+'</p>';
			document.getElementById("leftarrow").style.display = 'inline-block';
		}

		function prevImage() {
			if (poso > 1) {
					poso--;
					document.getElementById("rightarrow").style.display = 'inline-block';
					document.getElementById("docimage").src = foldername+'/'+imagesarray[poso];
			}
			if (poso<=1) {
				document.getElementById("leftarrow").style.display = 'none';
			}
			//document.getElementById("testarea2").innerHTML = '<p>toggleState='+toggleState+'<br>foldername='+foldername+'<br>poso='+poso+'<br>img='+imagesarray[poso]+'</p>';
		}

		function toggleImageSize() {
			if (toggleState) {
				document.getElementById("docimage").style.height = '';
				toggleState=0;
				changeMousePointer();
				//document.getElementById("testarea2").innerHTML = '<p>toggleState='+toggleState+'<br>foldername='+foldername+'<br>poso='+poso+'</p>';
      } else {
				document.getElementById("docimage").style.height = '100vh';
				toggleState=1;
				changeMousePointer();
				//document.getElementById("testarea2").innerHTML = '<p>toggleState='+toggleState+'<br>foldername='+foldername+'<br>poso='+poso+'</p>';
      }
		}
		function changeMousePointer() {
			if (!toggleState) {
				document.body.style.cursor = 'zoom-out';
      } else {
				document.body.style.cursor = 'zoom-in';
      }
		}

</script>
</head>
<body onload="initFunc()">

	<div id="allcontent_image">
		<div id="testarea2"><p class="bottom_links"><a href="index.php"><img src="images/icon-76x76.png" /></a><br><br><a href="index.php">Επιστροφή στην αρχική σελίδα</a></p>  </div>
	<div id="leftarrow">
		<img src="images/arrowp.png" onClick="prevImage()" onmouseover="document.body.style.cursor='pointer'" alt="Προηγούμενη εικόνα"/>
	</div>
	<div id="rightarrow">
		<img src="images/arrown.png" onClick="nextImage()" onmouseover="document.body.style.cursor='pointer'" alt="Επόμενη εικόνα"/>
	</div>

	<div id="showImages" >
			<img id="docimage" onclick="toggleImageSize()" onmouseover="changeMousePointer()" onmouseout="document.body.style.cursor='auto'"/>
	</div>

<?php
$dbc=mysqli_connect('localhost','root','','arxeiodb') or
die(mysqli_error());
mysqli_set_charset ($dbc, 'utf8mb4');

if (isset($_POST['submit'])){
	$bookid=$_POST['bookid'];
	$dirname=$_POST['bookdirname'];

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
	echo '<div id="listImages">';

	echo $dirname.',';
	foreach ($imagesarray as $i) {
			echo $i.',';
	}
	echo '</div>';


} else {
	echo 'what are you doing here?';
	}

mysqli_close($dbc);

?>

<div id="footer2">
<?php
include 'footer2.php';
?>
</div>
</div>
</body>
</html>
