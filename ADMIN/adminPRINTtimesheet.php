<?php
set_time_limit(60);
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();
date_default_timezone_set('Asia/Hong_Kong'); 

$printid = $_GET['id'];
$adminId = $_SESSION['adminId'];
// $payperiods =  $_SESSION['pperiod_range'];
$printfrom =  $_SESSION['payperiodfrom'];
$printto= $_SESSION['payperiodto'] ;
$daycount= 15;
// $printquery = "SELECT * FROM TIME_KEEPING, employees WHERE TIME_KEEPING.emp_id = employees.emp_id and TIME_KEEPING.emp_id = '$printid' AND TIME_KEEPING.timekeep_day BETWEEN '$printfrom' and '$printto' ORDER BY timekeep_day ASC";
$printquery = "SELECT * FROM TIME_KEEPING, employees 
               WHERE TIME_KEEPING.emp_id = employees.emp_id 
               AND TIME_KEEPING.emp_id = '$printid' 
               AND TIME_KEEPING.timekeep_day BETWEEN '{$_SESSION['payperiodfrom']}' AND '{$_SESSION['payperiodto']}' 
               ORDER BY timekeep_day ASC";
$printqueryexec = mysqli_query($conn,$printquery);
$printarray = mysqli_fetch_array($printqueryexec);
$d = strtotime("now");
$currtime = date ("Y-m-d H:i:s", $d);
// $payperiod = $_SESSION['payperiodrange'];

// $daycountquery = "SELECT payperiod_days FROM payperiods WHERE pperiod_range = '$payperiod'";
// $daycountexecquery = mysqli_query($conn,$daycountquery) or die ("CANT COUNT PERIOD DAYS ".mysqli_error($conn));
// $daycountarray = mysqli_fetch_array($daycountexecquery);

// if ($daycountarray){
// 	$daycount = $daycountarray['payperiod_days'];

// }


// Calculate the interval between two dates
// $interval = $startDate->diff($endDate);

// Get the total number of days
// $daycount = $interval->days;


if ($printarray){

  $prefix = $printarray['prefix_ID'];
  $idno = $printarray['emp_id'];
  $lname = $printarray['last_name'];
  $fname = $printarray['first_name'];
  $mname = $printarray['middle_name'];
  $dept = $printarray['dept_NAME'];
  $position = $printarray['position'];

  $name = "$lname, $fname $mname";
  $empID = "$prefix$idno";
}

$payperiodval = "SELECT undertime_hours,rh_hours,late_hours,timekeep_day,overtime_hours,hours_work,timekeep_remarks,(hours_work+overtime_hours) as totalhours FROM TIME_KEEPING WHERE emp_id = '$printid'  AND TIME_KEEPING.timekeep_day BETWEEN '{$_SESSION['payperiodfrom']}' AND '{$_SESSION['payperiodto']}' ORDER BY timekeep_day ASC";
$payperiodexec = mysqli_query($conn,$payperiodval) or die ("FAILED TO QUERY TIMEKEEP DETAILS ".mysqli_error($conn));

$totalot = "SELECT SUM(undertime_hours) as totalUT ,SUM(overtime_hours) as totalOT, SUM(hours_work) as totalWORKhours, SUM(late_hours) as totalLATEhours, SUM(hours_work+overtime_hours) as totalness FROM TIME_KEEPING WHERE emp_id = '$printid' AND timekeep_day  AND TIME_KEEPING.timekeep_day BETWEEN '{$_SESSION['payperiodfrom']}' AND '{$_SESSION['payperiodto']}' ORDER BY timekeep_day ASC";
$totalotexec =mysqli_query($conn,$totalot) or die ("OT ERROR ".mysqli_error($conn));
$totalotres = mysqli_fetch_array($totalotexec);

$absencesqry = "SELECT ('$daycount'-COUNT(timekeep_day)) as absences FROM TIME_KEEPING WHERE emp_id = '$printid' AND timekeep_day AND TIME_KEEPING.timekeep_day BETWEEN '{$_SESSION['payperiodfrom']}' AND '{$_SESSION['payperiodto']}' ORDER BY timekeep_day ASC";
$absencesqryexec = mysqli_query($conn,$absencesqry);
$absencesres = mysqli_fetch_array($absencesqryexec);

require_once("fpdf181/fpdf.php");

//A4 width: 219mm
//default margin : 10mm each side
//writable horizontal: 219.(10*2)= 189mm

$pdf = new FPDF ('P','mm','LETTER');

$pdf ->AddPage();

//set font arial, bold, 14pt
$pdf->SetFont('Arial','B',16);

//Spacer
$pdf->Cell(189,10,'',0,1);//end of line

//Cell (width,height,text,border,end line, [align])
$pdf->Cell(130,10,'WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM',0,0);
$pdf->Cell(55,10,'TIMESHEET',0,1);//end

//set font to arial, regular, 12pt
$pdf->SetFont('Arial','',12);

$pdf->Cell(130,5,'KAWIT CAVITE',0,0);
$pdf->Cell(59,5,'',0,1);//end of line

$pdf->Cell(130,5,'Cavite, Philippines, 4104',0,0);
$pdf->Cell(12,5,'Date: ',0,0);
$pdf->Cell(47,5,$currtime,0,1);//end of line

//Spacer
$pdf->Cell(189,5,'',0,1);//end of line

$pdf->Cell(30,5,'Employee ID:',0,0);
$pdf->Cell(100,5,$empID,0,0);
$pdf->Cell(59,5,'Payroll Period: ',0,1);//end of line

$pdf->Cell(15, 5, 'Name:', 0, 0);
$pdf->Cell(115, 5, $name, 0, 0);
$pdf->Cell(40, 5, 'Department: Position: ', 0, 0);  // Corrected line
$pdf->Cell(100, 5, $dept, 0, 1);  // Corrected line

$pdf->Cell(40, 5, $printfrom, 0, 1);  // end of line
$pdf->Cell(59, 5, $printto , 0, 0);
$pdf->Cell(100, 5, $position, 0, 0);
$pdf->Cell(59, 5, '', 0, 1);  // end of line


//SPACER
$pdf->Cell(189,5,'',0,1);//end of line

$pdf->SetFont('Arial','',11);

$pdf->Cell(30,7,'DATE',1,0,'C');
$pdf->Cell(24,7,'HOURS',1,0,'C');
$pdf->Cell(24,7,'OT',1,0,'C');
$pdf->SetFont('Arial','',9);
$pdf->Cell(28,7,'TARDINESS',1,0,'C');
$pdf->Cell(24,7,'UT',1,0,'C');
$pdf->SetFont('Arial','',7);
$pdf->Cell(24,7,'DAILY TOTAL',1,0,'C');
$pdf->SetFont('Arial','',11);
$pdf->Cell(35,7,'REMARKS',1,1,'C');//end of line

//set font to arial, regular, 12pt
$pdf->SetFont('Arial','',10);

while ($payperiodarray = mysqli_fetch_array($payperiodexec)):;

$pdf->Cell(30,7,$payperiodarray['timekeep_day'],1,0,'C');
$pdf->Cell(24,7,$payperiodarray['hours_work'],1,0,'C');
$pdf->Cell(24,7,$payperiodarray['overtime_hours'],1,0,'C');
$pdf->Cell(28,7,$payperiodarray['late_hours'],1,0,'C');
$pdf->Cell(24,7,$payperiodarray['undertime_hours'],1,0,'C');
$pdf->Cell(24,7,$payperiodarray['totalhours'],1,0,'C');
$pdf->Cell(35,7,$payperiodarray['timekeep_remarks'],1,1,'C');//end of line
endwhile;






//spacer
$pdf->Cell(189,5,'',0,1);//end of line

//set font arial, bold, 12pt
$pdf->SetFont('Arial','B',12);

$pdf->Cell(189,5,'TOTAL:',0,1);//end of line
//spacer
$pdf->Cell(189,5,'',0,1);//end of line

//set font arial, regular, 12pt
$pdf->SetFont('Arial','',12);

$pdf->Cell(37.8,7,'HOURS',1,0,'C');
$pdf->Cell(37.8,7,'OT',1,0,'C');
$pdf->Cell(37.8,7,'TARDINESS',1,0,'C');
$pdf->Cell(37.8,7,'UT',1,0,'C');
$pdf->Cell(37.8,7,'',1,1,'C');



$pdf->Cell(37.8,6,$totalotres['totalWORKhours'],1,0,'C');
$pdf->Cell(37.8,6,$totalotres['totalOT'],1,0,'C');
$pdf->Cell(37.8,6,$totalotres['totalLATEhours'],1,0,'C');
$pdf->Cell(37.8,6,$totalotres['totalUT'],1,0,'C');
$pdf->Cell(37.8,6,'',1,1,'C');

$pdf->Cell(189,6,'',0,1);//end of line

$pdf->Cell(60,6,'',0,0,'C');
$pdf->Cell(69,6,'TOTAL HOURS',1,0,'C');
$pdf->Cell(60,6,'',0,1,'C');//end of line

$pdf->Cell(60,6,'',0,0,'C');
$pdf->Cell(69,6,$totalotres['totalness'],1,0,'C');
$pdf->Cell(60,6,'',0,1,'C');//end of line


$pdf->Cell(189,3,'',0,1);//end of line
//set font arial, italic , 12pt
$pdf->SetFont('Arial','I',12);
$pdf->Cell(189,7,'I hereby certify that the above records are true and correct.',0,1,'C');//end of line
//spacer
$pdf->Cell(189,20,'',0,1);//end of line

$pdf->Cell(110,5,'',0,0);
$pdf->Cell(79,5,'________________________________',0,1);//end of line

//set font arial, regular, 10
$pdf->SetFont('Arial','',10);
$pdf->Cell(189,5,'Printed by: ' . $adminId,0,1,'C');

$pdf->Cell(110,5,'',0,0);
$pdf->Cell(79,5,'Employee signature over printed name',0,1,'C');//end of line





$pdf->Output();
?>


