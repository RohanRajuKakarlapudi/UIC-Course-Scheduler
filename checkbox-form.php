<?php
  $file = fopen("shafigh.txt", "w");

  $aDoor = $_POST['formDoor'];
  if(empty($aDoor)) 
  {
    echo("You didn't select any buildings.");
  } 
  else 
  {
    $N = count($aDoor);
    echo("You selected $N door(s): ");
    fwrite($file,"You selected $N door(s): ");
    for($i=0; $i < $N; $i++)
    {
      echo($aDoor[$i] . " ");
      fwrite($file,$aDoor[$i] . " ");
    }
  }

  $aCountries = $_POST['formCountries'];
  if(empty($aCountries)) 
  {
    echo("<br><br>");
    echo("<p>You didn't select any countries!</p>\n");
  } 
  else 
  {
    $N = count($aCountries);
    echo("<br><br>");
    echo("You selected $N countries: ");
    fwrite($file,"\n\n");
    fwrite($file,"You selected $N countries: ");
    for($i=0; $i < $N; $i++)
    {
      echo($aCountries[$i] . " ");
      fwrite($file,$aCountries[$i] . " ");
    }
  }

  $aGender = $_POST['formGender'];
  if(empty($aGender)) 
  {
    echo("<br><br>");
    echo("<p>You didn't select any gender!</p>\n");
  } 
  else 
  {
    $N = count($aGender);
    echo("<br><br>");
    echo("You selected: ");
    fwrite($file,"\n\n");
    fwrite($file,"You selected: ");
    for($i=0; $i < $N; $i++)
    {
      echo($aGender[$i] . " ");
      fwrite($file,$aGender[$i] . " ");
    }
  }

  $ahahaha = $_POST['hahaha'];
  if(empty($ahahaha)) 
  {
    echo("<br><br>");
    echo("<p>You didn't enter your age!</p>\n");
  } 
  else 
  {
    echo("<br><br>");
    echo("Your age is: ");
    fwrite($file,"\n\n");
    fwrite($file,"Your age is: ");
    echo($ahahaha);
    fwrite($file,$ahahaha);
  }

  fclose($file);
  
  echo("<br>Reading c++ program<br>");
  exec("./a.out", $out);



echo("<br>From reading outfile.txt<br>");
$myfile = fopen("output.txt", "r") or die("Unable to open file!");
// Output one line until end-of-file
while(!feof($myfile)) {
  echo fgets($myfile) . "<br>";
}
fclose($myfile);
?>

