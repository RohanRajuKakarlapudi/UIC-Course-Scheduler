<style>
        .myTable {
            border-collapse: collapse;
            background-color: white;
        }
		.myTable th {
			border-collapse: collapse;
		}
    </style>
	<?php
	function debug_to_console($data) {
		$output = $data;
		if (is_array($output))
			$output = implode(',', $output);
	
		echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
	}
	function changeDivBoxSize($WidthPixels) {
		echo "    <script>
		document.body.style.width = '$WidthPixels';
    </script>
        ";
	}
	?>
<?php
session_start();
$inputFileName ="input_".(string)$_POST['inputFile'].".txt";
$file = fopen($inputFileName, "w"); // this should create the new file

// $major = $_POST['major'];
$major = $_POST['majorText'];  // changed on 2/14 for out text formatting, 
// if (empty($major)) {

// } else {
//     fwrite($file, $major . "\n");
// }

// changes on 2/14 for out text formatting, this is the new way of writing the major 
// to the input file, it should fix the issue of out text formatting
if (!empty($major)) {
    fwrite($file, trim($major) . "\n");
}

$minHours = $_POST['min-hours'];
if (empty($minHours)) {
    fwrite($file,"mincredit = 0\n");
} else {
    fwrite($file,"mincredit = $minHours\n");
}

$maxHours = $_POST['max-hours'];
if (empty($maxHours)) {
    fwrite($file,"maxcredit = 0\n");
} else {
    fwrite($file,"maxcredit = $maxHours\n");
}

$balance = $_POST['balance'];
if (empty($balance)) {
    fwrite($file,"balance = 0\n");
} else {
    fwrite($file,"balance = $balance\n");
}
// changes on 2/14 MODIFYING FOR OUT TEXT FORMATTING
// $classes = $_POST['chosenClasses'];
// if (empty($classes)) {
// } else {
//     foreach ($classes as $class) {
//         fwrite($file, $class."\n");
//     }
//     fwrite($file, $major."  XXX");
//     fwrite($file, "\n");
// }

$classes = $_POST['chosenClasses'];
if (!empty($classes)) {
    foreach ($classes as $class) {
        fwrite($file, trim($class) . "\n");
    }
}

fclose($file);
/*
// clears the output.txt file
$fh = fopen( 'output_1.txt', 'w' );
fclose($fh);
$fh2 = fopen( 'output_2.txt', 'w' );
fclose($fh2);
*/
// output files are declared early so I have something to pass into the schedule generator
$outputFileName1 = "output_".$_POST['inputFile']."_1.txt";  // output 1
$outputFileName2 = "output_".$_POST['inputFile']."_2.txt"; // output 2
$outputFileName3 = "output_".$_POST['inputFile']."_3.txt"; // output 3
$outputFileName4 = "output_".$_POST['inputFile']."_4.txt"; // output 4

$sessionName = $_POST['inputFile'];
session_write_close();

exec("./schedule_05 $sessionName", $out);  // passes the input filename, output1, and output2 to ArgV


//------- Output_1.txt -----
$file = fopen($outputFileName1, "r");
$outputline = array();
$a = 1;
while (!feof($file)) {
   $outputline[] = fgets($file);
   $a = $a + 1;
}
fclose($file);
//----------

$cpsem = "";
$totcr = "";
$skey = "";
$skey1 = "";
$xx = 0;
$yy = 1;
$y = $a - 3;
$z = $a - 2;
for ($x = 0, $y = $a - 3, $z = $a - 2; $x < $a-1; $x++) 
{
	$output1 = $outputline[$x];
	if ($x == $xx) { $skey = $output1;}
	if ($x == $yy) { $skey1 = $output1;}
	
	if ($x == $y) { $cpsem = $output1;}
	if ($x == $z) { $totcr = $output1;}
}
	$searchkey = preg_split("/[\s,]+/", $skey);
	$searchtxt = "";
	$searchtxt = $searchkey[0];

if ($searchtxt == "TIME" || $searchtxt == '' || $searchtxt != "Major" )
	{
		echo("<div class = 'myTable' id = 'myTable'>");
	echo (
		"<table id = 'scheduleTable' style=' width: 400px; margin-left: 50px', border='2'>
    		<tr>
       		<td style='text-align:left;Font-size:18;Font-Weight:normal'> The selected conditions can't be satisfied, so no schedule could be generated! </td>
       		</tr>
       	</table>
       	");
		   echo("</div>");
	}
	else
	{
	$array = array(
	    1 => "",
	    2 => "",
	    3 => "",
	    4 => "",
	    5 => "",
	    6 => "",
	    7 => "",
	    8 => "",
		9 => "",
		10 => "",
		11 => "",
	    12 => "",
	    13 => "",
	    14 => "",
	    15 => "",
		16 => "",
	);
	$arraylen = array(
	    1 => 0,
	    2 => 0,
	    3 => 0,
	    4 => 0,
	    5 => 0,
	    6 => 0,
	    7 => 0,
	    8 => 0,
		9 => 0,
		10 => 0,
		11 => 0,
	    12 => 0,
	    13 => 0,
	    14 => 0,
		15 => 0,
		16 => 0,

	);
	$outputfile2 = fopen($outputFileName3, "r") or die("Unable to open file!");
	$lineNum = 0;
	
		while (!feof($outputfile2)) 
		{
	    $temp = fgets($outputfile2);
	    $string = explode(",", $temp);
		for($i = 0; $i < count($string) - 1; $i++) {
			$array[$lineNum] .= '<td>' . $string[$i] . '</td>';
			$arraylen[$lineNum]++;  // add 1 to the number of classes in this semester
		}
		$lineNum++;
		}
	fclose($outputfile2);
	// build table
	// starting with years
	$numYears = 1;  // the base number of years will start at 1
	$tableRowYears = "";
	for ($i = 3; $i <= $arraylen[1]; $i += 2) {
			$numYears++;
			$tableRowYears .= "<th colspan='2' width='20%' bgcolor = '#FFFFFF'>Year " . $numYears . "</th>";
	}
	debug_to_console("numyears:".$numYears);
	// building the number of semesters
	$tableRowSemesters = "";
	$numSemesters = $numYears * 2;
	$tempvar = 100 / $numSemesters;
	$tempvar2 = $tempvar . "%";
	for ($i = 3; $i <= $numSemesters; $i++) {
		if ($i == 3 || $i == 4 || $i == 7 || $i == 8 || $i == 11 || $i == 12 || $i == 15 || $i == 16) {
			$tableRowSemesters .= "<th width='$tempvar2' bgcolor = '#56ca7c'>". $i . "</th>";
		} else {  // odd years
			$tableRowSemesters .= "<th width='$tempvar2' bgcolor = '#0e83cd'>" . $i . "</th>";
		}
	}
	// adding fillers to take place in the rows/cols
	for ($i = 1; $i <= $numSemesters; $i++) {
		if ($arraylen[$i] < $numSemesters && $arraylen[$i] != 0 && $array[$i] != "") {
			for($j = $arraylen[$i]; $j < $numSemesters; $j++) {
				$array[$i] .= "<td> </td>";
			}
		}
	}
	// assigning the classes
	$one = $array[1];
	$two = $array[2];
	$three = $array[3];
	$four = $array[4];
	$five = $array[5];
	$six = $array[6];
	$seven = $array[7];
	$eight = $array[8];
	$nine = $array[9];
	$ten = $array[10];
	$eleven = $array[11];
	$twelve = $array[12];
	$thirteen = $array[13];
	$fourteen = $array[14];
	$fifteen = $array[15];
	$sixteen = $array[16];

	$keywords = preg_split("/[\s,]+/", $cpsem);
	$keywords1 = preg_split("/[\s,]+/", $totcr);
	$intval = (int)$keywords1[1]; 
	// -----------
	$x = 0;
	do
	{

	     $skeyword = explode(" ", $outputline[$x]);
	     $ax2 = $skeyword[1];
	     $ax3 = $skeyword[2];
	     if ($ax2 == "XXX" || $ax3 == "XXX") 
	     {
		    $dx = 0;
        	$sk = 0;
	      	do
		       {
	 		      if ($skeyword[$sk] == "1")
                    {
                        $arraypointer = $sk;
                       	$intval = $intval - 1;
	 	 	            break;
	 				};
	             $sk = $sk + 1;
	 	       } while ($sk < 62);
  		};
  		$x = $x + 1;
	} while ($x < $a-4 );
	if ($intval < 0) {$intval = 0; };  

	if ($arraypointer == 6 || $arraypointer == 7) { $keywords[1] = (int)$keywords[1] - 1 ;};
	if ($arraypointer == 11 || $arraypointer == 12) { $keywords[2] = (int)$keywords[2] - 1 ;};
	if ($arraypointer == 16 || $arraypointer == 17) { $keywords[3] = (int)$keywords[3] - 1 ;};
	if ($arraypointer == 21 || $arraypointer == 22) { $keywords[4] = (int)$keywords[4] - 1 ;};
	if ($arraypointer == 26 || $arraypointer == 27) { $keywords[5] = (int)$keywords[5] - 1 ;};
	if ($arraypointer == 31 || $arraypointer == 32) { $keywords[6] = (int)$keywords[6] - 1 ;};
	if ($arraypointer == 36 || $arraypointer == 37) { $keywords[7] = (int)$keywords[7] - 1 ;};
	if ($arraypointer == 41 || $arraypointer == 42) { $keywords[8] = (int)$keywords[8] - 1 ;};
	if ($arraypointer == 46 || $arraypointer == 47) { $keywords[9] = (int)$keywords[9] - 1 ;};
	if ($arraypointer == 51 || $arraypointer == 52) { $keywords[10] = (int)$keywords[10] - 1 ;};
	// building dynamic table
	// building classes
	$tableRowClasses = "";
	if ($arraylen[3] != 0) {
		$tableRowClasses .= "<tr> $three </tr>";
		$tableRowClasses .= "<tr> $four </tr>";
	}
	if ($arraylen[5] != 0) {
		$tableRowClasses .= "<tr> $five </tr>";
		$tableRowClasses .= "<tr> $six </tr>";
	}
	if ($arraylen[7] != 0) {
		$tableRowClasses .= "<tr> $seven </tr>";
		$tableRowClasses .= "<tr> $eight </tr>";
	}
	if ($arraylen[9] != 0) {
		$tableRowClasses .= "<tr> $nine </tr>";
		$tableRowClasses .= "<tr> $ten </tr>";
	}
	if ($arraylen[11] != 0) {
		$tableRowClasses .= "<tr> $eleven </tr>";
		$tableRowClasses .= "<tr> $twelve </tr>";
	}
	if ($arraylen[13] != 0) {
		$tableRowClasses .= "<tr> $thirteen </tr>";
		$tableRowClasses .= "<tr> $fourteen </tr>";
	}
	if ($arraylen[15] != 0) {
		$tableRowClasses .= "<tr> $fifteen </tr>";
		$tableRowClasses .= "<tr> $sixteen </tr>";
	}
	// cp/sem fix
	$intval = 0;
	for ($i = 1; $i <= $numSemesters; $i++) {
		if ($keywords[$i] < 0) {
			$keywords[$i] = 0;
		}
		$intval += $keywords[$i];
	}
	// building table total credits
	$tableRowTotalCredits = "";
	for ($i = 3; $i <= $numSemesters; $i++) {
		if ($i == 3 || $i == 4 || $i == 7 || $i == 8 || $i == 11 || $i == 12 || $i == 15 || $i == 16) {
			$tableRowTotalCredits .= "<th bgcolor = '#56ca7c'>$keywords[$i]</th>";
		} else {
			$tableRowTotalCredits .= "<th bgcolor = '#0e83cd'>$keywords[$i]</th>";
		}
	}
	// getting rid of excess zeros in the totalcredits
	for ($ncr = 1; $ncr <= 16; $ncr++ ){
		if ($keywords[$ncr] == "0" || $keywords[$ncr] == 0) {
			$keywords[$ncr] = "";
		}
	}
	debug_to_console("num semesters: ".$numSemesters);
	echo("<div style = 'margin-left: 42px' class = 'myTable' id = 'myTable'>");
	echo 
	(
		"<table id = 'scheduleTable' style='border-collapse: collapse;width:100%; border-top: 2px solid #0e83cd; border-bottom: 2px solid #0e83cd; font-family: Arial; box-shadow: rgba(0, 0, 0, 0.25) 0px 54px 55px, rgba(0, 0, 0, 0.12) 0px -12px 30px, rgba(0, 0, 0, 0.12) 0px 4px 6px, rgba(0, 0, 0, 0.17) 0px 12px 13px, rgba(0, 0, 0, 0.09) 0px -3px 5px;'>
    	<tr >
    	    <th colspan='2' width='20%' style = 'margin-left: 50px' bgcolor ='#FFFFFF'>Year 1</th>
			$tableRowYears
    	</tr>
    	<tr >
    		<th width='$tempvar2' bgcolor = '#0e83cd'>1</th>
    		<th width='$tempvar2' bgcolor = '#0e83cd'>2</th>
			$tableRowSemesters
    	</tr>
    	<tr>
		$one
    	</tr>
		<tr bgcolor = '#F3F3F3'>
		$two
    	</tr>
		$tableRowClasses
    	<tr >
    	    <th colspan='$numSemesters' bgcolor = 'White'>Credits Per Semester</th>
    	</tr>
    	<tr>
    	    <th bgcolor = '#0e83cd'>$keywords[1]</th>
    	    <th bgcolor = '#0e83cd'>$keywords[2]</th>
			$tableRowTotalCredits
    	</tr>
		<tr >
    	     <th colspan='$numYears' bgcolor='#F3F3F3'>Total Credits for All Semester</th>
    	     <th colspan='$numYears' bgcolor='#FFFFFF'>$intval</th>
    	</tr>

	</table>
	");
	echo("</div>");
};
changeDivBoxSize($numSemesters * 100);
// unlink all files that have been generated
$mastercourseFileName = "mastercourse_gen_".(string)$_POST['inputFile'].".txt";
$courseOfferingsFileName = "courseofferings_gen_".(string)$_POST['inputFile'].".txt";
$prerequisitesFileName = "prerequisites_gen_".(string)$_POST['inputFile'].".txt";
$mastercourselistFileName= "mastercourselist_gen_".(string)$_POST['inputFile'].".txt";
unlink($inputFileName);
unlink($outputFileName1);
unlink($outputFileName2);
unlink($outputFileName3);
unlink($outputFileName4);
unlink($mastercourseFileName);
unlink($courseOfferingsFileName);
unlink($prerequisitesFileName);
unlink($mastercourselistFileName);

?>
