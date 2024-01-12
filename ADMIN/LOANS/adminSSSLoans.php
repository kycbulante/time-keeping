<?php
include("../../DBCONFIG.PHP");
include("../../LoginControl.php");
include("BASICLOGININFO.PHP");

session_start();
if(isset($_SESSION['masterfilenotif'])){

$mfnotif = $_SESSION['masterfilenotif'];
?>  
<script>
alert("<?php echo $mfnotif;?>");
</script>
<?php
}
if (isset($_GET['findTasks'])) {
  $deptchecked = isset($_GET['dept']) ? $_GET['dept'] : '';
  $emptypechecked = isset($_GET['employmenttype']) ? $_GET['employmenttype'] : '';
  $shiftchecked = isset($_GET['shifts']) ? $_GET['shifts'] : '';
  $positionchecked = isset($_GET['position']) ? $_GET['position'] : '';
  $gender = isset($_GET['Gender']) ? $_GET['Gender'] : '';
  $loanstatus = isset($_GET['loan_status']) ? $_GET['loan_status'] : '';
  $employeeStatus = isset($_GET['employee_status']) ? $_GET['employee_status'] : '';
  $selectedMonth = isset($_GET['month']) ? $_GET['month'] : '';
  $selectedDay = isset($_GET['day']) ? $_GET['day'] : '';
  $selectedYear = isset($_GET['year']) ? $_GET['year'] : '';
  $filterBy = isset($_GET['filter_by']) ? $_GET['filter_by'] : '';  // New parameter
  $searchValue = isset($_GET['search_value']) ? $_GET['search_value'] : '';  // New parameter

  $deptFilter = $deptchecked ? $deptchecked : '';
  $emptypeFilter = $emptypechecked ? $emptypechecked : '';
  $shiftFilter = $shiftchecked ? $shiftchecked : '';
  $positionFilter = $positionchecked ? $positionchecked : '';
  $genderFilter = $gender ? "'" . $gender . "'" : ''; // Assuming gender is a string in the database
  $employeeStatusFilter = $employeeStatus ? "'" . $employeeStatus . "'" : ''; // Assuming employee_status is a string in the database
  $loanStatusFilter = $loanstatus ? "'" . $loanstatus . "'" : ''; // Assuming employee_status is a string in the database

  $monthFilter = $selectedMonth ? "'" . $selectedMonth . "'" : '';
  $dayFilter = $selectedDay ? "'" . $selectedDay . "'" : '';
  $yearFilter = $selectedYear ? "'" . $selectedYear . "'" : '';

  $filterByFilter = $filterBy ? $filterBy : '';  // New parameter
  $searchValueFilter = $searchValue ? "" . $searchValue . "" : ''; 

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
      $filterConditions[] = "MONTH(employees.date_hired) = $monthFilter";
  }
  
  if ($dayFilter) {
      $filterConditions[] = "DAY(employees.date_hired) = $dayFilter";
  }
  
  if ($yearFilter) {
      $filterConditions[] = "YEAR(employees.date_hired) = $yearFilter";
  }
  if ($loanStatusFilter) {
      $filterConditions[] = "loangsis.status = $loanStatusFilter";
  }

  if ($filterByFilter && $searchValueFilter) {
    // Add a condition for the specific search based on the selected field
    $filterConditions[] = "LOWER(employees.$filterByFilter)  LIKE LOWER ('%$searchValueFilter%')";
}

  if (!empty($filterConditions)) {
      $searchquery = "SELECT * FROM employees
      JOIN department ON department.dept_NAME = employees.dept_NAME 
      JOIN employmenttypes ON employmenttypes.employment_TYPE = employees.employment_TYPE
      JOIN shift ON shift.shift_SCHEDULE = employees.shift_SCHEDULE
      JOIN LOANgsis ON LOANgsis.emp_id = employees.emp_id
      LEFT JOIN position ON position.position_name = employees.position
      WHERE " . implode(" AND ", $filterConditions);

      echo "Generated Query: $searchquery<br>";
      print_r($_GET);

      $searchresult = filterTable($searchquery);
      $_SESSION['printgsis_query'] = $searchquery;
      echo "Number of Rows: " . mysqli_num_rows($searchresult) . "<br>";
  } else {
      echo "No filters selected. Please select at least one filter.";
        $searchquery ="SELECT * FROM LOANgsis, employees WHERE employees.emp_id = LOANgsis.emp_id ORDER BY start_date DESC";
        $searchresult= filterTable($searchquery);
        $_SESSION['printgsis_query'] = $searchquery;
  }
  

}
else {
  $searchquery ="SELECT * FROM LOANgsis, employees WHERE employees.emp_id = LOANgsis.emp_id ORDER BY start_date DESC";
  $searchresult= filterTable($searchquery);
  $_SESSION['printgsis_query'] = $searchquery;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
<title>GSIS Loans</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="../../css/fullcalendar.css" />
<link rel="stylesheet" href="../../css/maruti-style.css" />
<link rel="stylesheet" href="../../css/maruti-media.css" class="skin-color" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</head>

<body>

<!--Header-part-->

<?php
INCLUDE ('NAVBAR.php');
?>


<div id="content">

  <div id="content-header">
    <div id="breadcrumb"> <a href="index.html" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a>
      <a href ="adminMasterfileDept.php" class="tip-bottom"><i class ="icon-th"></i> GSIS Loans</a></div>
  </div>

  <div class="container-fluid">
    <div class ="row-fluid">
       <!-- Sidebar -->
       <div class="span2">
       <form method="GET" action="">
        <?php
        $deptchecked = isset($_GET['dept']) ? $_GET['dept'] : '';
        $emptypechecked = isset($_GET['employmenttype']) ? $_GET['employmenttype'] : '';
        $shiftchecked = isset($_GET['shifts']) ? $_GET['shifts'] : '';
        $positionchecked = isset($_GET['position']) ? $_GET['position'] : '';
        $gender = isset($_GET['gender']) ? $_GET['gender'] : '';
        $loanstatus = isset($_GET['loan_status']) ? $_GET['loan_status'] : '';
        $employeeStatus = isset($_GET['employee_status']) ? $_GET['employee_status'] : '';
        $month = isset($_GET['month']) ? $_GET['month'] : '';
        $filterBy = isset($_GET['filter_by']) ? $_GET['filter_by'] : '';  // New parameter
        $searchValue = isset($_GET['search_value']) ? $_GET['search_value'] : '';  // New parameter
        
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
        <h6>Date Hired</h6>
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

              <select name="day">
          <option value="">Select Day</option>
          <?php
          // Adding options for days (assuming up to 31 for simplicity)
          for ($day = 1; $day <= 31; $day++) {
            $selected = (isset($_GET['day']) && $_GET['day'] == sprintf('%02d', $day)) ? 'selected' : '';
            echo '<option value="' . sprintf('%02d', $day) . '" ' . $selected . '>' . sprintf('%02d', $day) . '</option>';
          }
          ?>
      </select>

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
      <h6>Loan Status</h6>
        <select name="loan_status">
            <option value="" <?php if(isset($_GET['loan_status']) && $_GET['loan_status'] == '') echo 'selected'; ?>>Select Loan Status</option>
            <option value="On-Going" <?php if(isset($_GET['loan_status']) && $_GET['loan_status'] == 'On-Going') echo 'selected'; ?>>On-Going</option>
            <option value="Paid" <?php if(isset($_GET['loan_status']) && $_GET['loan_status'] == 'Paid') echo 'selected'; ?>>Paid</option>
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
                      <div class="container-fluid">
                      <a href="printsss.php?printAll" class="btn btn-info btn-mini" target="_blank"><span class="icon"><i class="icon-print"></i></span> Print All Masterlist</a>
                      <a href="printsss.php?printDisplayed" class="btn btn-info btn-mini" target="_blank"><span class="icon"><i class="icon-print"></i></span> Print Displayed Masterlist</a>

              </div>
        <!-- Your sidebar content goes here -->
      <div class = "span10">
        <h3>GSIS Loans</h3>        
      </div>
    </div>
   
<div class ="row-fluid">
  
     <div class="span12">
        <div class="widget-box">
          <div class="widget-title">
            <ul class="nav nav-tabs" id="myTab">
              <li class=""><a href="../adminPAYROLLINFO.php"><i class="icon-user"></i> Employees</a></li>
              <li class=""><a href="../GOVTTABLES/adminGOVTTables.php"><i class="icon-th"></i> Government Contribution Table</a></li>
              <li class="active"><a href="../LOANS/adminSSSLoans.php"><i class="icon-th"></i> GSIS Loans</a></li>
              <li class=""><a href="../LOANS/adminPAGIBIGLoans.php"><i class="icon-th"></i> Pagibig Loans</a></li>
              <li class=""><a href="../adminPAYROLLProcess.php"><i class="icon-user"></i> Process Employee Payrolls</a></li>
              <li class=""><a href="../admin13thmonth.php"><i class="icon-th"></i> Compute 13th Month Pay</a></li>
            </ul>
          </div>
          <div class="widget-content tab-content">
          <div id="tab1" class="tab-pane fade in active">
             <a href ="adminSSSLoans.php" class = "btn btn-success" style = "float:right; margin-left: 4px;"><span class="icon"><i class="icon-refresh"></i></span> Refresh</a>
               <a href ="adminADDSSSLoan.php" class = "btn btn-info" style = "float:right;"><span class="icon"><i class="icon-plus"></i></span> Add GSIS Loan</a>
               <br>
               <br>
               <table class="table table-bordered data-table">
               <thead>
                <tr>
                  <th>GSIS ID No.</th>
                  <th>Employee ID</th>
                  <th>Last Name</th>
                  <th>First Name</th>
                  <th>Middle Name</th>
                  <th>Department</th>
                  <th>Employment Type</th>
                  <th>Shift</th>
                  <th>Start Date</th>
                  <th>End Date</th>
                  <th>Loan Amount</th>
                  <th>Monthly Amount</th>
                  <th>Balance</th>
                  <th>Status</th>
                  <!-- <th>Action</th> -->
                </tr>
              </thead>
              <tbody> 
              <?php

             
               
              
               function filterTable($searchquery)
               {

                    $conn = mysqli_connect("localhost:3307","root","","masterdb");
                    $filter_Result = mysqli_query($conn,$searchquery) or die ("failed to query masterfile ".mysql_error());
                    return $filter_Result;
               }
               while($row1DEPT = mysqli_fetch_array($searchresult)):;
               ?>
                  <tr class="gradeX">
                  <td><?php echo $row1DEPT['gsis_idno']; ?></td>
                  <td><?php echo $row1DEPT['emp_id']; ?></td>
                  <td><?php echo $row1DEPT['emplastname']; ?></td>
                  <td><?php echo $row1DEPT['empfirstname']; ?></td>
                  <td><?php echo $row1DEPT['empmiddlename']; ?></td>
                  <td><?php echo $row1DEPT['dept_NAME']; ?></td>
                  <td><?php echo $row1DEPT['employment_TYPE']; ?></td>
                  <td><?php echo $row1DEPT['shift_SCHEDULE']; ?></td>
                  <td><?php echo $row1DEPT['start_date']; ?></td>
                  <td><?php echo $row1DEPT['end_date']; ?></td>
                  <td><?php echo $row1DEPT['loan_amount']; ?></td>
                  <td><?php echo $row1DEPT['monthly_deduct'];?></td>
                  <td><?php echo $row1DEPT['loan_balance'];?></td>
                  <td><?php echo $row1DEPT['status'];?></td>
                  <!-- <a href = "adminEDITSSSLoans.php?id=<?php echo $row1DEPT['emp_id']?>" class = "btn btn-info btn-mini"><span class="icon"><i class="icon-eye-open"></i></span> View</a> -->
                  <td><center>
                    <!-- <a href = "adminDELETEMasterfileDept.php?id=<?php echo $row1DEPT['emp_id'];?>" class = "btn btn-danger btn-mini"><span class="icon"><i class="icon-trash"></i></span> Delete</a></center></td> -->
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
     
<?php
unset($_SESSION['masterfilenotif']);
?>



<div class="row-fluid">
<div id="footer" class="span12"> 2023 &copy; WEB-BASED TIMEKEEPING AND PAYROLL SYSTEM USING FINGERPRINT BIOMETRICS</div>
</div>

<script src="../js/maruti.dashboard.js"></script> 
<script src="../js/excanvas.min.js"></script> 
<script src="../js/jquery.min.js"></script> 
<script src="../js/jquery.ui.custom.js"></script> 
<script src="../js/bootstrap.min.js"></script> 
<script src="../js/jquery.flot.min.js"></script> 
<script src="../js/jquery.flot.resize.min.js"></script> 
<script src="../js/jquery.peity.min.js"></script> 
<script src="../js/fullcalendar.min.js"></script> 
<script src="../js/maruti.js"></script> 
</body>
</html>

