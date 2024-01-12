<?php
include("../DBCONFIG.PHP");
include("../LoginControl.php");
include("../BASICLOGININFO.PHP");

session_start();

if(isset($_SESSION['masterfilenotif'])){

$mfnotif = $_SESSION['masterfilenotif'];
?>  
<script>
alert("<?php echo $mfnotif;?>");
</script>
<?php
}

$results_perpage = 20;

               if (isset($_GET['page'])){

                    $page = $_GET['page'];
               } else {

                    $page=1;
               }

$currentempid = $_SESSION['empID'];

$userIdpage  = $_SESSION['empID'];

$pageViewed = basename($_SERVER['PHP_SELF']);
$pageInfo = pathinfo($pageViewed);

// Get the filename without extension
$pageViewed1 = $pageInfo['filename'];



// Log the page view
logPageView($conn, $userIdpage, $pageViewed1);

// Pagination setup
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$recordsPerPage = 10; // You can adjust this number based on your preference

$startFrom = ($page - 1) * $recordsPerPage;


  $searchquery ="SELECT * FROM empactivity_log WHERE emp_id = '$currentempid' LIMIT $startFrom, $recordsPerPage";
  $search_result = filterTable($searchquery);
  
  $countQuery = "SELECT COUNT(*) AS total FROM empactivity_log where emp_id = '$currentempid'";
  $countResult = mysqli_query($conn,$countQuery) or die ("FAILED TO EXECUTE COUNT QUERY ". mysql_error());      
  $countRow = mysqli_fetch_assoc($countResult);
  $totalRecords = $countRow['total'];


?>


<!DOCTYPE html>
<html lang="en">
<head>
<title>Employee Records</title>
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

<body>

<!--Header-part-->

<?php
INCLUDE ('empNAVBAR.php');
?>


<div id="content">

  <div id="content-header">
    <div id="breadcrumb"> <a href="empDASHBOARD.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a>
      <a href ="adminMasterfile.php" class="tip-bottom"><i class ="icon-calendar"></i> Attendance Records</a></div>
  </div>
  

  <div class="container-fluid">
    <div class ="row-fluid">
      <div class = "span10">
        <h3>Employee Records</h3>        
      </div>
    </div>
   
    <div class ="row-fluid">
     <div class="span12">
        <div class="widget-box">
          <div class="widget-title">
            <ul class="nav nav-tabs" id="myTab">
              <li><a href="empDASHBOARD.php"><i class="icon-user"></i> Profile</a></li>
              <li><a href="empAPPLYOvertime.php"><i class="icon-time"></i> Overtime</a></li>
              <li><a href="empAPPLYLeave.php"><i class="icon-calendar"></i> Leave</a></li>
              <li><a href="empATTENDANCErecords.php"><i class="icon-th"></i> My Records</a></li>
              <li class="active"><a href="empActivitylogs.php"><i class="icon-time"></i> Activity Logs</a></li>
              <li class=""><a href="empLoans.php"><i class="icon-file"></i> Loans</a></li>


              
            </ul>
          </div>
     
          <div class="widget-content tab-content">
          <div id="tab1" class="tab-pane fade in active"><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB-->
               
                
            </div>
                </div>
            

                <div class = "span2">


                  
                  </div>

                
                  </form>
                <div class = "span5">
                  <a href ="empactivitylogs.php" class = "btn btn-success" style = "float:right; margin-left: 4px;"><span class="icon"><i class="icon-refresh"></i></span> Refresh</a>
                  <!-- <small><?php echo $attrecordview; ?></small> -->
                </div>
              
            </div>

               <table class="table table-bordered data-table">
               <thead>
                <tr>
                  <th>Log ID</th>
                  <th>Employee ID</th>
                  <th>Activity</th>
                  <th>Timestamp</th>
         
                  
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
                  <td><?php echo $row1['log_id'];?></td>
                  <td><?php echo $row1['emp_id'];?></td>
                  <td><?php echo $row1['activity'];?></td>
                  <td><?php echo $row1['log_timestamp'];?></td>
                </tr>
              <?php endwhile;?>
              </tbody>
            </table>
            <div class="pagination">
                        <?php
                        $totalPages = ceil($totalRecords / $recordsPerPage);

                        for ($i = 1; $i <= $totalPages; $i++) {
                            echo "<a href='empActivitylogs.php?page=$i'>$i-</a> ";
                        }
                        ?>
                    </div>
          </div><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB--><!--EMPLOYEE TAB-->
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

<script src="../js/bootstrap.min.js"></script> 
<script src="../js/jquery.flot.min.js"></script> 
<script src="../js/jquery.flot.resize.min.js"></script> 
<script src="../js/jquery.peity.min.js"></script> 
<script src="../js/fullcalendar.min.js"></script> 
<script src="../js/maruti.js"></script> 
</body>
</html>

