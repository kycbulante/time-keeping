<?php
include("../../DBCONFIG.PHP");
include("../../LoginControl.php");
include("BASICLOGININFO.PHP");

if (isset($_GET['findTasks'])){
  $deptchecked = isset($_GET['dept']) ? $_GET['dept'] : '';
  $emptypechecked = isset($_GET['employmenttype']) ? $_GET['employmenttype'] : '';
  $shiftchecked = isset($_GET['shifts']) ? $_GET['shifts'] : '';
  $positionchecked = isset($_GET['position']) ? $_GET['position'] : '';
  $gender = isset($_GET['Gender']) ? $_GET['Gender'] : '';
  $employeeStatus = isset($_GET['employee_status']) ? $_GET['employee_status'] : '';
  $selectedMonth = isset($_GET['month']) ? $_GET['month'] : '';
  $selectedDay = isset($_GET['day']) ? $_GET['day'] : '';
  $selectedYear = isset($_GET['year']) ? $_GET['year'] : '';
  $filterBy = isset($_GET['filter_by']) ? $_GET['filter_by'] : '';  // New parameter
  $searchValue = isset($_GET['search_value']) ? $_GET['search_value'] : '';  // New parameter
  $payperiod = isset($_GET['payperiod']) ? $_GET['payperiod']:'';

  $deptFilter = $deptchecked ? $deptchecked : '';
  $emptypeFilter = $emptypechecked ? $emptypechecked : '';
  $shiftFilter = $shiftchecked ? $shiftchecked : '';
  $positionFilter = $positionchecked ? $positionchecked : '';
  $genderFilter = $gender ? "'" . $gender . "'" : ''; // Assuming gender is a string in the database
  $employeeStatusFilter = $employeeStatus ? "'" . $employeeStatus . "'" : ''; // Assuming employee_status is a string in the database

  $monthFilter = $selectedMonth ? "'" . $selectedMonth . "'" : '';
  $dayFilter = $selectedDay ? "'" . $selectedDay . "'" : '';
  $yearFilter = $selectedYear ? "'" . $selectedYear . "'" : '';

  $filterByFilter = $filterBy ? $filterBy : '';  // New parameter
  $searchValueFilter = $searchValue ? "" . $searchValue . "" : ''; 
  $payperiodFilter = $payperiod? "" . $payperiod . "" : ''; 


  
  $filterConditions = [];

    if ($deptFilter) {
        $filterConditions[] = "department.dept_ID IN ($deptFilter)";
    }

    if ($emptypeFilter) {
        $filterConditions[] = "employmenttypes.employment_ID IN ($emptypeFilter)";
    }

    if ($shiftFilter) {
        $filterConditions[] = "shift.shift_ID IN ($shiftFilter)";
    }
    if ($positionFilter) {
        $filterConditions[] = "position.position_id IN ($positionFilter)";
    }
    if ($genderFilter) {
      $filterConditions[] = "employees.emp_gender = $genderFilter";
    }

    if ($employeeStatusFilter) {
        $filterConditions[] = "employees.emp_status = $employeeStatusFilter";
    }

    if ($monthFilter) {
        $filterConditions[] = "MONTH(pay_per_period.pperiod_month) = $monthFilter";
    }
    
    // if ($dayFilter) {
    //     $filterConditions[] = "DAY(pay_per_period.) = $dayFilter";
    // }
    
    if ($yearFilter) {
        $filterConditions[] = "YEAR(pay_per_period.pperiod_year) = $yearFilter";
    }
    if ($payperiodFilter) {
        $filterConditions[] = "pay_per_period.pperiod_range = '$payperiodFilter'";
    }
  
    if ($filterByFilter && $searchValueFilter) {
      // Add a condition for the specific search based on the selected field
      $filterConditions[] = "LOWER(employees.$filterByFilter)  LIKE LOWER ('%$searchValueFilter%')";
  }
  if (!empty($filterConditions)) {
    $searchquery = "SELECT employees.emp_id, employees.*, payrollinfo.*, pay_per_period.*, department.*, employmenttypes.*, shift.*
    FROM employees
    JOIN department ON department.dept_NAME = employees.dept_NAME 
    JOIN employmenttypes ON employmenttypes.employment_TYPE = employees.employment_TYPE
    JOIN shift ON shift.shift_SCHEDULE = employees.shift_SCHEDULE
    JOIN payrollinfo ON payrollinfo.emp_id = employees.emp_id
    JOIN pay_per_period ON pay_per_period.emp_id = employees.emp_id
    LEFT JOIN position ON position.position_name = employees.position
    WHERE " . implode(" AND ", $filterConditions);


    
} else {
  $searchquery = "SELECT employees.emp_id, employees.*, payrollinfo.*, pay_per_period.*, department.*, employmenttypes.*, shift.*
    FROM employees
    JOIN department ON department.dept_NAME = employees.dept_NAME 
    JOIN employmenttypes ON employmenttypes.employment_TYPE = employees.employment_TYPE
    JOIN shift ON shift.shift_SCHEDULE = employees.shift_SCHEDULE
    JOIN payrollinfo ON payrollinfo.emp_id = employees.emp_id
    JOIN pay_per_period ON pay_per_period.emp_id = employees.emp_id
    LEFT JOIN position ON position.position_name = employees.position";

}



  // $pperiodquery = "SELECT * FROM payperiods WHERE payperiod_ID = '$payperiod'";
  // $pperiodexecquery = mysqli_query($conn, $pperiodquery) or die ("FAILED TO SET PAY PERIOD ".mysqli_error($conn));
  // $pperiodarray = mysqli_fetch_array($pperiodexecquery);
  // error_log($pperiodquery);

  //     if ($pperiodarray){
  //       $_SESSION['payperiodfrom'] = $pperiodarray['pperiod_start'];
  //       $_SESSION['payperiodto'] = $pperiodarray['pperiod_end'];
  //       $_SESSION['payperiodrange'] = $pperiodarray['pperiod_range'];
  //       $_SESSION['payperioddayss'] = $pperiodarray['payperiod_days'];
      


  //       $searchquery .= " AND pay_per_period.pperiod_range = '" . $_SESSION['payperiodrange'] . "'";


  //     }
      // echo "Generated Query: $searchquery<br>";

      $search_result = filterTable($searchquery);
      // echo $searchquery;


} else {

        // $searchvalue = $_GET['searchvalue'];
        $searchquery = "SELECT * FROM payrollinfo JOIN employees ON payrollinfo.emp_id = employees.emp_id JOIN pay_per_period ON pay_per_period.emp_id = employees.emp_id";
        $search_result = filterTable($searchquery);
        $search_result = filterTable($searchquery);
        // Generated Query: SELECT * from employees WHERE emp_id = '0' ORDER BY emp_id ASC LIMIT 0,20

}


// $searchquery = "SELECT * FROM payrollinfo JOIN employees ON payrollinfo.emp_id = employees.emp_id JOIN pay_per_period ON pay_per_period.emp_id = employees.emp_id";
// $search_result = filterTable($searchquery);
echo "Generated Query: $searchquery<br>";


?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Government Contribution Tables</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="../../css/fullcalendar.css" />
<link rel="stylesheet" href="../../css/maruti-style.css" />
<link rel="stylesheet" href="../../css/maruti-media.css" class="skin-color" />
<link rel="stylesheet" href="../../jquery-ui-1.12.1/jquery-ui.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="../../jquery-ui-1.12.1/jquery-3.2.1.js"></script>
<script src="../../jquery-ui-1.12.1/jquery-ui.js"></script>
<script type ="text/javascript">
  $( function() {
      $( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd'});
      } );
  
</script>
</head>

<body>

<!--Header-part-->

<?php
INCLUDE ('NAVBAR.php');

?>


<div id="content">

  <div id="content-header">
    <div id="breadcrumb"> <a href="../adminDASHBOARD.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a>
      <a href ="adminOT.php" class="tip-bottom"><i class ="icon-th"></i> Government Contribution Tables</a></div>
  </div>

  <div class="container-fluid">
    <div class ="row-fluid">
      <div class = "span10">
        <h3>Government Contribution Tables</h3>        
      </div>
    </div>

    <div class ="row-fluid">
     <div class="span12">
        <div class="widget-box">
          <div class="widget-title">
            <ul class="nav nav-tabs" id="myTab">
            <li class=""><a href="../adminPAYROLLINFO.php"><i class="icon-user"></i> Employees</a></li>
              <li class="active"><a href="./GOVTTABLES/adminGOVTTables.php"><i class="icon-th"></i> Government Contribution Table</a></li>
              <li class=""><a href="../LOANS/adminSSSLoans.php"><i class="icon-th"></i> GSIS Loans</a></li>
              <li class=""><a href="../LOANS/adminPAGIBIGLoans.php"><i class="icon-th"></i> Pagibig Loans</a></li>
              <li class=""><a href="../adminPAYROLLProcess.php"><i class="icon-user"></i> Process Employee Payrolls</a></li>
              <li class=""><a href="../admin13thmonth.php"><i class="icon-th"></i> Compute 13th Month Pay</a></li>
            </ul>
          </div>
   
<div class ="row-fluid">
     <div class="span12">
        <div class="widget-box">
          <div class="widget-title">
            <ul class="nav nav-tabs" id="myTab">
              <li class="active"><a href="adminGOVTTables.php"><i class="icon-th"></i> GSIS </a></li>
              <li><a href="adminPHILHEALTH.php"><i class="icon-th"></i> Philhealth</a></li>
              <li><a href="adminWITHHOLDINGTAX.php"><i class="icon-th"></i> Pag-Ibig</a></li>
            </ul>
          </div>
          <div class="widget-content tab-content">
          <div id="tab1" class="tab-pane fade in active"><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB-->
               
              <div class = "row-fluid">
                
                <div class = "span 10">
                  <center>
                    <img src="ssscontributiontable2023.png">
                  </center>
                </div>
                <div class="container-fluid">
    <div class="row-fluid">
      <!-- Sidebar -->
      <div class="span2">
      <form method="GET" action="">
        <?php
       $deptchecked = isset($_GET['dept']) ? $_GET['dept'] : '';
       $emptypechecked = isset($_GET['employmenttype']) ? $_GET['employmenttype'] : '';
       $shiftchecked = isset($_GET['shifts']) ? $_GET['shifts'] : '';
       $positionchecked = isset($_GET['position']) ? $_GET['position'] : '';
       $gender = isset($_GET['gender']) ? $_GET['gender'] : '';
       $employeeStatus = isset($_GET['employee_status']) ? $_GET['employee_status'] : '';
       $month = isset($_GET['month']) ? $_GET['month'] : '';
       $filterBy = isset($_GET['filter_by']) ? $_GET['filter_by'] : '';  // New parameter
       $searchValue = isset($_GET['search_value']) ? $_GET['search_value'] : '';  // New parameter
       $payperiod = isset($_GET['payperiod']) ? $_GET['payperiod']:'';
        
        $query ="SELECT * FROM department";
        $total_row = mysqli_query($conn,$query) or die('error');
        ?> <h6>Department</h6> 
        <select name="dept">
        <option value="">Select Department</option><?php
        if (mysqli_num_rows($total_row) > 0) {
            foreach ($total_row as $row) {
                ?>
                <option value="<?php echo $row['dept_ID']; ?>" <?php if ($deptchecked == $row['dept_ID']) echo "selected"; ?>>
                    <?php echo $row['dept_NAME']; ?>
                </option>
                <?php
            }
        } else {
            echo 'No Data Found';
        }
        ?>
        </select>
        <?php
        $query1 ="SELECT * FROM employmenttypes";
        $total_row = mysqli_query($conn,$query1) or die('error');
        ?> <h6>Employment Type</h6>
        <select name="employmenttype" id="employmenttype">
        <option value="">Select Employment Type</option><?php
          if(mysqli_num_rows($total_row)>0){
              foreach($total_row as $row){
                ?>
                <!-- <li style="color:#333; font-size:12px; font-family: 'Roboto', sans-serif;"> -->             
                <option value="<?php echo $row['employment_ID']; ?>" <?php if ($emptypechecked == $row['employment_ID']) echo "selected"; ?>>
                <?php echo $row['employment_TYPE']; ?>
                </option>
                <?php

              }
            }else{
              echo'No Data Found';
            }
            ?>
        </select>
        
        <?php
        $query3 ="SELECT * FROM position";
        $total_row = mysqli_query($conn,$query3) or die('error');
        ?> <h6>Position</h6>
        <select name="position" id="position">
        <option value="">Select Position</option><?php
          if(mysqli_num_rows($total_row)>0){
              foreach($total_row as $row){
                ?>
                <!-- <li style="color:#333; font-size:12px; font-family: 'Roboto', sans-serif;"> -->             
                <option value="<?php echo $row['position_id']; ?>" <?php if ($positionchecked == $row['position_id']) echo "selected"; ?>>
                <?php echo $row['position_name']; ?>
                </option>
                <?php

              }
            }else{
              echo'No Data Found';
            }
            ?>
        </select>
       
        
        <?php
        $query2 ="SELECT * FROM shift";
        $total_row = mysqli_query($conn,$query2) or die('error');
        ?> <h6>Shift</h6>
        <select name="shifts" disabled>
        <!-- <option value="">Select Shift</option> -->
        <option value="2">8AM to 5PM</option>
        <?php
          if(mysqli_num_rows($total_row)>0){
              foreach($total_row as $row){
                ?>
                <!-- <li style="color:#333; font-size:12px; font-family: 'Roboto', sans-serif;"> -->

                <option value="<?php echo $row['shift_ID']; ?>" <?php if ($positionchecked == $row['shift_ID']) echo "selected"; ?>>
                <?php echo $row['shift_SCHEDULE']; ?>
            </option>
                <?php

              }
            }else{
              echo'No Data Found';
            }
        ?>
        </select>
        <h6>Gender</h6>
        <select name="Gender">
            <option value="" <?php if(isset($_GET['Gender']) && $_GET['Gender'] == '') echo 'selected'; ?>>Select Gender</option>
            <option value="Male" <?php if(isset($_GET['Gender']) && $_GET['Gender'] == 'Male') echo 'selected'; ?>>Male</option>
            <option value="Female" <?php if(isset($_GET['Gender']) && $_GET['Gender'] == 'Female') echo 'selected'; ?>>Female</option>
        </select>

        <h6>Employee Status</h6>
        <select name="employee_status">
            <option value="" <?php if(isset($_GET['employee_status']) && $_GET['employee_status'] == '') echo 'selected'; ?>>Select Employee Status</option>
            <option value="Active" <?php if(isset($_GET['employee_status']) && $_GET['employee_status'] == 'Active') echo 'selected'; ?>>Active</option>
            <option value="Inactive" <?php if(isset($_GET['employee_status']) && $_GET['employee_status'] == 'Inactive') echo 'selected'; ?>>Inactive</option>
        </select>
        <h6>Date</h6>
        <select name="month">
          <option value="">Select Month</option>
          <?php
          $months = [
              'January' => 1, 'February' => 2, 'March' => 3, 'April' => 4, 'May' => 5, 'June' => 6,
              'July' => 7, 'August' => 8, 'September' => 9, 'October' => 10, 'November' => 11, 'December' => 12
          ];

          foreach ($months as $monthName => $monthNumber) {
              $selected = (isset($_GET['month']) && $_GET['month'] == $monthNumber) ? 'selected' : '';
              echo '<option value="' . $monthNumber . '" ' . $selected . '>' . $monthName . '</option>';
          }
          ?>
      </select>

              <!-- <select name="day">
          <option value="">Select Day</option>
          <?php
          // Adding options for days (assuming up to 31 for simplicity)
          for ($day = 1; $day <= 31; $day++) {
            $selected = (isset($_GET['day']) && $_GET['day'] == sprintf('%02d', $day)) ? 'selected' : '';
            echo '<option value="' . sprintf('%02d', $day) . '" ' . $selected . '>' . sprintf('%02d', $day) . '</option>';
          }
          ?>
      </select> -->

      <select name="year">
          <option value="">Select Year</option>
          <?php
          // Adding options for years (current year - 5 to current year + 5)
          $currentYear = date("Y");
          $startYear = $currentYear - 5;
          $endYear = $currentYear + 5;

          for ($year = $startYear; $year <= $endYear; $year++) {
            $selected = (isset($_GET['year']) && $_GET['year'] == $year) ? 'selected' : '';
            echo '<option value="' . $year . '" ' . $selected . '>' . $year . '</option>';
          }
          ?>
      </select>
      <?php
        $query5 ="SELECT * FROM payperiods";
        $total_row = mysqli_query($conn,$query5) or die('error');
        ?> <h6>Payroll Period</h6>
        <select name="payperiod">
        <option value="">Select Payroll Period</option>
        <?php
          if(mysqli_num_rows($total_row)>0){
              foreach($total_row as $row){
                ?>
                <!-- <li style="color:#333; font-size:12px; font-family: 'Roboto', sans-serif;"> -->

                <option value="<?php echo $row['pperiod_range']; ?>" <?php if ($payperiod == $row['pperiod_range']) echo "selected"; ?>>
                <?php echo $row['pperiod_range']; ?>
            </option>
                <?php

              }
            }else{
              echo'No Data Found';
            }
        ?>
        </select>
      <h6>Search by:</h6>
        <select name="filter_by">
            <option value="" <?php if(isset($_GET['filter_by']) && $_GET['filter_by'] == '') echo 'selected'; ?>>Search by</option>
            <option value="emp_id" <?php if(isset($_GET['filter_by']) && $_GET['filter_by'] == 'emp_id') echo 'selected'; ?>>Employee ID</option>
            <option value="last_name" <?php if(isset($_GET['filter_by']) && $_GET['filter_by'] == 'last_name') echo 'selected'; ?>>Last Name</option>
            <option value="first_name" <?php if(isset($_GET['filter_by']) && $_GET['filter_by'] == 'first_name') echo 'selected'; ?>>First Name</option>
            <option value="user_name" <?php if(isset($_GET['filter_by']) && $_GET['filter_by'] == 'user_name') echo 'selected'; ?>>Username</option>
        </select>



        <div id ="search">
          <input type="text" placeholder="Search" name="search_value" value="<?php echo isset($_GET['search_value']) ? htmlspecialchars($_GET['search_value']) : ''; ?>" />

          </div>
          </div>

    <div class="span5">

        

</div>

<div class ="span1">
                  
        <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-sm" name="findTasks">Apply</button>
                        </form>
                      </div>
                
              
              </div>
              <a href ="adminGOVTTables.php" class = "btn btn-success" style = "float:right; margin-left: 4px;"><span class="icon"><i class="icon-refresh"></i></span> Refresh</a>


           
          </div><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB-->
          <div>
          <table class="table table-bordered data-table">
            <thead>
            <tr>
              <th>Payroll Period</th>
              <th>Emp ID</th>
              <th>Last Name</th>
              <th>First Name</th>
              <th>Middle Name</th>
              <th>GSIS</th>
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
?>
   <tr class="gradeX">
   <td><?php echo $row1['pperiod_range'];?></td>
   <td><a href = "adminVIEWprofile.php?id=<?php echo $row1['emp_id']; ?>"><?php echo $row1['prefix_ID'];?><?php echo $row1['emp_id'];?></a></td>
   <td><?php echo $row1['last_name'];?></td>
   <td><?php echo $row1['first_name'];?></td>
   <td><?php echo $row1['middle_name']; ?></td>
   <td><?php echo $row1['sss_deduct'];?></td>
 </tr>
<?php endwhile;?>

</tbody>
          </table>
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

<script src="../../js/maruti.dashboard.js"></script> 
<script src="../../js/excanvas.min.js"></script> 

<script src="../../js/bootstrap.min.js"></script> 
<script src="../../js/jquery.flot.min.js"></script> 
<script src="../../js/jquery.flot.resize.min.js"></script> 
<script src="../../js/jquery.peity.min.js"></script> 
<script src="../../js/fullcalendar.min.js"></script> 
<script src="../../js/maruti.js"></script> 
</body>
</html>
