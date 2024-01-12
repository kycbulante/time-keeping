<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();


$uname = $_SESSION['uname'];
$empid = $_SESSION['empId'];


$getinfoqry = "SELECT * from employees WHERE user_name = '$uname'";
$getinfoexecqry = mysqli_query($conn,$getinfoqry) or die ("FAILED TO GET INFORMATION ".mysqli_error($conn));
$getinfoarray = mysqli_fetch_array($getinfoexecqry);
$getinforows = mysqli_num_rows($getinfoexecqry);
if ($getinfoarray && $getinforows !=0){

 $currprefixid = $getinfoarray['prefix_ID'];
 $currempid = $getinfoarray['emp_id'];
        //$currcardnumber = $getinfoarray['card_number'];
        $currfingerprintid = $getinfoarray['fingerprint_id'];
        $currusername = $getinfoarray['user_name'];
        $currlastname = $getinfoarray['last_name'];
        $currfirstname = $getinfoarray['first_name'];
        $currmiddlename = $getinfoarray['middle_name'];
        $currdateofbirth = $getinfoarray['date_of_birth'];
        $curraddress = $getinfoarray['emp_address'];
        $currnationality = $getinfoarray['emp_nationality'];
        $currdeptname = $getinfoarray['dept_NAME'];
        $currshiftsched = $getinfoarray['shift_SCHEDULE'];
        $currcontact = $getinfoarray['contact_number'];
        $currdatehired = $getinfoarray['date_hired'];
        $currdateregularized = $getinfoarray['date_regularized'];
        $currdateresigned = $getinfoarray['date_resigned'];
        $currimg = $getinfoarray['img_tmp'];
$_SESSION['empID'] = $currempid;
}


if (isset($_POST['pperiod_btn'])){

  $payperiod = $_POST['payperiod'];
  $_SESSION['payperiods'] = $_POST['payperiod'];
  $searchquery = "SELECT * FROM employees, PAY_PER_PERIOD WHERE employees.emp_id = PAY_PER_PERIOD.emp_id AND PAY_PER_PERIOD.emp_id = '$empid' AND PAY_PER_PERIOD.pperiod_range = '$payperiod' ORDER BY pperiod_range";
  $search_result = filterTable($searchquery);

} else  {
 $searchquery = "SELECT * from employees, PAY_PER_PERIOD WHERE employees.emp_id = PAY_PER_PERIOD.emp_id AND PAY_PER_PERIOD.emp_id = '$empid' ORDER BY PAY_PER_PERIOD.pperiod_range ";  
 $search_result = filterTable($searchquery);
 $_SESSION['payperiods'] = 'noset';
 }


?>






<!DOCTYPE html>
<html lang="en">
<head>
<title>Employee Home</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="../css/bootstrap.min.css" />
<link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="../css/fullcalendar.css" />
<link rel="stylesheet" href="../css/maruti-style.css" />
<link rel="stylesheet" href="../css/maruti-media.css" class="skin-color" />
<link rel="stylesheet" href="../jquery-ui-1.12.1/jquery-ui.css">
<script src="../jquery-ui-1.12.1/jquery-3.2.1.js"></script>
<script src="../jquery-ui-1.12.1/jquery-ui.js"></script>

<script type ="text/javascript">
  $( function() {
      $( "#datepickerfrom" ).datepicker({ dateFormat: 'yy-mm-dd'});
      } );
  $( function() {
      $( "#datepickerto" ).datepicker({ dateFormat: 'yy-mm-dd'});
      } );
  
</script>
<style>

.userinfo {
  margin-left:40px;
  
}

.uinfotab2 {
  display:block;
  float:right;
  margin-right: 40px;
}

.control-group{
  float:left;
  display: block;
}



</style>
</head>

<body>

<!--Header-part-->

<?php
INCLUDE ('empNAVBAR.php');
?><div id="content">

  <div id="content-header">
    <div id="breadcrumb"> <a href="empDASHBOARD.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a>
      <a href="empDASHBOARD.php" title="Employee Info" class="tip-bottom"><i class="icon-user"></i> My Profile</a>
    </div>
  </div>

  <div class="container-fluid">
    <div class = "row-fluid">
      <span class ="span3">
      </span>
      <span class="span6">
        <h3>Employee Information</h3>
      </span>
    </div>
    <div class ="row-fluid">
     <div class="span12">
        <div class="widget-box">
          <div class="widget-title">
            <ul class="nav nav-tabs" id="myTab">
              <li class="active"><a href="empDASHBOARD.php"><i class="icon-user"></i> Profile</a></li>
              <li><a href="empAPPLYOvertime.php"><i class="icon-time"></i> Overtime</a></li>
              <li><a href="empAPPLYLeave.php"><i class="icon-calendar"></i> Leave</a></li>
              <li><a href="empATTENDANCErecords.php"><i class="icon-th"></i> My Records</a></li>
              <li><a href="empActivitylogs.php"><i class="icon-time"></i> Activity Logs</a></li>
              <li class=""><a href="empLoans.php"><i class="icon-file"></i> Loans</a></li>

              
            </ul>
          </div>

      <div class = "span3">
      </div>
      <span class="uinfotab2"><img height = "100" width="157" src="data:image;base64,<?php echo $currimg?>"></span>

      <!-- <div class ="span6">
        <div class="widget-box">
          <div class = "widget-title"><span class="icon"><i class ="icon-user"></i></span>
            <h5> Profile</h5>
          </div> -->

          <div class = "widget-content no padding">
          <table class="table table-bordered data-table">
               <thead>
                <tr>
                  <th>Employee ID</th>
                  <th>Last Name</th>
                  <th>First Name</th>
                  <th>Middle Name</th>
                  <th>Username</th>
                  <th>Department</th>
                  <th>Birthday</th>
                  <th>Nationality</th>
                  <th>Shift</th>
                  <th>Contact Number</th>
                  <th>Date Hired</th>
                  <th>Date Regularized</th>
                  <th>Date Resigned</th>
                </tr>
              </thead>
              <tbody> 
                  <tr class="gradeX">
                  <td><?php echo $currempid; ?></td>
                  <td><?php echo $currlastname; ?></td>
                  <td><?php echo $currfirstname; ?></td>
                  <td><?php echo $currmiddlename; ?></td>
                  <td><?php echo $currusername; ?></td>
                  <td><?php echo $currdeptname; ?></td>
                  <td><?php echo $currdateofbirth; ?></td>
                  <td><?php echo $currnationality; ?></td>
                  <td><?php echo $currshiftsched; ?></td>
                  <td><?php echo $currcontact; ?></td>
                  <td><?php echo $currdatehired;?></td>
                  <td><?php echo $currdateregularized; ?></td>
                  <td><?php echo $currdateresigned; ?></td>
                </tr>
              
              </tbody>
            </table>
            <span class = "uinfotab2"><a href ="empCHANGEPASSWORD.php" class = "btn btn-info"><span class="icon"><i class="icon-edit"></i> </span>Change Password</a></span><br>   
          </div>
        </div>
      </div>

      <div class ="span3">
      </div>  
    </div>
   
    <div class="row-fluid">
    <div id="content">

<div class="container-fluid">
  <div class ="row-fluid">
    <div class = "span10">
      <h3>Employee Records</h3>        
    </div>
  </div>
 
         
    <div class = "row-fluid">
        <div class = "span5">
            <div class ="control-group">
              <form action="<?php $_SERVER['PHP_SELF'];?>" method ="post">
              <?php
              $payperiodsquery = "SELECT * FROM payperiods";
              $payperiodsexecquery = mysqli_query($conn, $payperiodsquery) or die ("FAILED TO EXECUTE PAYPERIOD QUERY ".mysqli_error($conn));
              ?>
                  <label class="control-label">Select Payroll Period: </label>
                    <div class="controls">
                    <select name="payperiod">
                    <option value=""></option>
                    <?php while ($payperiodchoice = mysqli_fetch_array($payperiodsexecquery)): ?>
                        <?php
                        $selected = ($payperiodchoice['pperiod_range'] == $_SESSION['payperiods']) ? 'selected' : '';
                        ?>
                        <option value="<?php echo $payperiodchoice['pperiod_range']; ?>" <?php echo $selected; ?>>
                            <?php echo $payperiodchoice['pperiod_range']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                      <button type="submit" class="btn btn-success printbtn" name = "pperiod_btn">Go</button>
                    </div>
                </form>
                <a href="printpayslip.php" class="btn btn-success" role="button" target="_blank">Generate Payslip</a>
                <a href="printdtr.php" class="btn btn-success" role="button" target="_blank">View DTR</a>
                <a href="printtimesheet.php" class="btn btn-success" role="button" target="_blank">View Timesheet</a>
                <a href="printleaves.php" class="btn btn-success" role="button" target="_blank">Apply for Leave</a>

              </div>
          </div>

          <div class = "span2">


                
          </div>

              
    
          <div class = "span5">
                <a href ="empPAYROLLrecords.php" class = "btn btn-success" style = "float:right; margin-left: 4px;"><span class="icon"><i class="icon-refresh"></i></span> Refresh</a>
                <!-- <small><?php echo $attrecordview; ?></small> -->
              </div>
            
          </div>


    </div>
          

              
<!-- 
             <table class="table table-bordered data-table">
             <thead>
              <tr>
                
                <th>Last Name</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Pay Period</th>
                <th>Basic Pay</th>
                <th>OT Pay</th>
                <th>Reg. Holiday</th>
                <th>Special Non-Working Holiday</th>
                <th>Gross Salary</th>
                <th>Philhealth</th>
                <th>GSIS</th>
                <th>PAG-IBIG/HDMF</th>
                <th>GSIS Loan</th>
                <th>PAG-IBIG Loan</th>
                <th>Total Deductions</th>
                <th>Net Pay</th>
                                
                
              </tr>
            </thead>
            <tbody> 

             <?php

            

             function filterTable($searchquery)
             {

                  $conn1 = mysqli_connect("localhost:3307","root","","masterdb");
                  $filter_Result = mysqli_query($conn1,$searchquery) or die ("failed to query masterfile ".mysqli_error($conn1));
                  return $filter_Result;
             }
             while($row1 = mysqli_fetch_array($search_result)):;
             $basepay = $row1['reg_pay'];
             $otpay = $row1['ot_pay'];
             $shdaypay = $row1['shday_pay'];
             $hdaypay  =$row1['hday_pay'];

             $grosspay = ($basepay + $otpay + $shdaypay + $hdaypay);
             $gpay = number_format((float)$grosspay,2,'.','');
             $philhealth = $row1['philhealth_deduct'];
             $sss = $row1['sss_deduct'];
             $pagibig = $row1['pagibig_deduct'];
             $sssloan = $row1['sssloan_deduct'];
             $pagibigloan = $row1['pagibigloan_deduct'];
             $withholdingtax = $row1['tax_deduct'];
             $totaldeduct = $row1['total_deduct'];

             $netpay = ($grosspay - $totaldeduct);
             $npay = number_format((float)$netpay,2,'.',''); 

             
            
                    
             ?>
                <tr class="gradeX">
                <td><?php echo $row1['last_name'];?></td>
                <td><?php echo $row1['first_name'];?></td>
                <td><?php echo $row1['middle_name']; ?></td>
                <td><?php echo $row1['pperiod_range'];?></td>
                <td><?php echo $basepay;?></td>
                <td><?php echo $otpay;?></td>
                <td><?php echo $hdaypay;?></td>
                <td><?php echo $shdaypay;?></td>
                <td><?php echo $gpay;?></td>
                <td><?php echo $philhealth; ?></td>
                <td><?php echo $sss; ?></td>
                <td><?php echo $pagibig; ?></td>
                <td><?php echo $sssloan; ?></td>
                <td><?php echo $pagibigloan; ?></td>
                <td><?php echo $totaldeduct; ?></td>
                <td><center><b>&#8369; <?php echo $npay;?></td>

                
              </tr>
              <?php endwhile;?>
            </tbody>
          </table> -->
             
        </div><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB-->
        </div>
        
      </div>
    </div>
  </div>
</div>
</div>
      

    </div>
  </div>
</div>
</div>
</div>
<div class="row-fluid">
  <div id="footer" class="span12"> 2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS</div>
</div>
<?php
unset($_SESSION['changepassnotif']);
?>
<script src="../js/maruti.dashboard.js"></script> 
<script src="../js/excanvas.min.js"></script> 

<script src="../js/bootstrap.min.js"></script> 
<script src="../js/jquery.flot.min.js"></script> 
<script src="../js/jquery.flot.resize.min.js"></script> 
<script src="../js/jquery.peity.min.js"></script> 
<script src="../js/fullcalendar.min.js"></script> 
<script src="../js/maruti.js"></script> 
<canvas id="myPieChart" width="400" height="400"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch data from PHP script
        fetch('fetch_data.php')
            .then(response => response.json())
            .then(data => createPieChart(data))
            .catch(error => console.error("Error fetching data:", error));

        // Function to create a pie chart
        function createPieChart(data) {
            var labels = data.map(item => item.label);
            var values = data.map(item => item.value);

            var ctx = document.getElementById('myPieChart').getContext('2d');
            var myPieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: getRandomColors(values.length),
                    }],
                },
            });
        }

        // Function to generate random colors
        function getRandomColors(count) {
            var colors = [];
            for (var i = 0; i < count; i++) {
                var hue = (360 / count) * i;
                colors.push(`hsl(${hue}, 70%, 60%)`);
            }
            return colors;
        }
    });
</script>



</body>
</html>