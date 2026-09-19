<?php
include_once '../config.php';

if ($_POST['reportId'] !='') {

 $query = "SELECT * FROM Batch_Report WHERE BatchId = '".$_POST['reportId']."'";

 $result = mysqli_query($conn,$query);
 $response = "<table border='0' width='100%' class='TFtable'>";
 while( $row = mysqli_fetch_array($result) ){
    $StartDate =$row['StartDate'];
	
	$Time      =$row['Time'];
	$Day       =$row['Day'];
	$expday    =explode(',',$Day);
	$ReportName=$row['ReportName'];
	$UserType  =$row['UserType'];
	$UserName  =$row['UserName'];
	$Subject   =$row['Subject'];
	$recurrence_pattern = $row['recurrence_pattern'];
	$months = $row['months'];
	$monthly_days = $row['monthly_days'];
	$Type      =$row['Type'];
	$no_of_days = $row['no_of_days'];
  $last_run_date = $row['last_run_date'];
  $fullDirectoryPath = $row['directoryPath'];
  $sftp = $row['sftp_id'];
  if(strlen($row['directoryPath']) > 50) {
  $directoryPath = substr($row['directoryPath'], 0, 50). '...';
  }
  else
  {
    $directoryPath = $row['directoryPath'];
  }
	$CreatedBy =$row['CreatedBy'];
	$id = $row['BatchId'];
  if($UserType==1){
    $usertypename='AACA';
  }else if($UserType==2){
	  $usertypename='Firm';
	}else if($UserType==3){
	   $usertypename='Client';
	}else if($UserType==4){
	   $usertypename='Agency';
	}

  if($row['status'] == 0)
  {
    $status = 'Scheduled';
  }else if($row['status'] == 1){
    $status = 'In Execution';
  }else if($row['status'] == 2){
    $status = 'Completed';
  }

	if($row['EndDate'] != '')
    {
      $EndDate = $row['EndDate'];
      $endDate = date('m-d-Y', strtotime($row['EndDate']));
    }
    else
    {
      $EndDate = 'No end date';
      $endDate = 'No end date';
    }

    // if($row['Subject'] != '')
    // {
    //   $Subject = $row['Subject'];
    // }
    // else
    // {
    //   $Subject = 'No Subject available';
    // }

    // if($row['Type'] != '')
    // {
    //   $Type = $row['Type'];
    // }
    // else
    // {
    //   $Type = 'No Type available';
    // }

    if($recurrence_pattern != '')
    {
      $recurrence_pattern = $recurrence_pattern;
    }
    else
    {
       $recurrence_pattern = 'No pattern available';
    }


    if($months != '')
    {
       $months = $months;
    }
    else
    {
       $months = 'Months not available';
    }


    if($monthly_days != '')
    {
       $monthly_days = $monthly_days;
    }
    


    if($Day != '')
     {

	$weekDays = array();

	foreach($expday as $newday){
	    if($newday==1){
	      $weekDays[] = "Monday ";
	    }else if($newday==2){
	      $weekDays[] = "Tuesday ";
	    }else if($newday==3){
	      $weekDays[] = "Wednesday ";
	    }else if($newday==4){
	      $weekDays[] = "Thursday ";
	    }else if($newday==5){
	      $weekDays[] = "Friday ";
	    }else if($newday==6){
	      $weekDays[] = "Saturday ";
	    }else if($newday==7){
	      $weekDays[] = "Sunday ";
	    }
	    } 

	    $imp_day = implode(', ',$weekDays);

   	}
   	else
   	{
   		$imp_day = "No day available";
   	}




 $response .= "<tr>";
 $response .= "<td>Start Date: </td><td>".date('m-d-Y', strtotime($StartDate))."</td>";
 $response .= "</tr>";

 $response .= "<tr>";
 $response .= "<td>End Date: </td><td>".$endDate."</td>";
 $response .= "</tr>";
 if($recurrence_pattern == 'Customization'){
 $response .= "<tr>";
 $response .= "<td>Time: </td><td>".$Time."</td>";
 $response .= "</tr>";
 }

 // $response .= "<tr>";
 // $response .= "<td>Day: </td><td>".$imp_day."</td>";
 // $response .= "</tr>";

 if($recurrence_pattern == 'Daily' && $row['EndDate'] == '')
 {

  $newStartDate = date("M d,Y", strtotime($StartDate));
  $newTime = date('g:i a', strtotime($Time));

 $response .= "<tr>";
 $response .= "<td>Frequency: </td><td>Report will be scheduled for everyday Starting from ".$newStartDate."</td>";
 $response .= "</tr>";

 }

 if($recurrence_pattern == 'Daily' && $row['EndDate'] != '')
 {

  $newStartDate = date("M d,Y", strtotime($StartDate));
  $newEndDate = date("M d,Y", strtotime($EndDate));
  $newTime = date('g:i a', strtotime($Time));

 $response .= "<tr>";
 $response .= "<td>Frequency: </td><td>Report will be scheduled for everyday Starting from ".$newStartDate." till ".$newEndDate."</td>";
 $response .= "</tr>";

 }

 if($recurrence_pattern == 'Weekly' && $row['EndDate'] == '')
 {

  $newStartDate = date("M d,Y", strtotime($StartDate));
  $newTime = date('g:i a', strtotime($Time));

 $response .= "<tr>";
 $response .= "<td>Frequency: </td><td>Report will be scheduled for every ".$imp_day." of every week Starting ".$newStartDate."</td>";
 $response .= "</tr>";

 }

 if($recurrence_pattern == 'Weekly' && $row['EndDate'] != '')
 {

  $newStartDate = date("M d,Y", strtotime($StartDate));
  $newEndDate = date("M d,Y", strtotime($EndDate));
  $newTime = date('g:i a', strtotime($Time));

 $response .= "<tr>";
 $response .= "<td>Frequency: </td><td>Report will be scheduled for every ".$imp_day." of every week Starting from ".$newStartDate." and Ending at ".$newEndDate."</td>";
 $response .= "</tr>";

 }

 if($recurrence_pattern == 'Monthly' && !empty($row['EndDate']))
 {

  $newStartDate = date("M d,Y", strtotime($StartDate));
  $newEndDate = date("M d,Y", strtotime($EndDate));
  $newTime = date('g:i a', strtotime($Time));

  $array_dates = explode(",",$monthly_days);

  foreach($array_dates as $row)
  {
  if($row == 32)
   {
	$array_new = array(" month end");
	end($array_dates);        
	$key = key($array_dates); 
	unset($array_dates[$key]);

	$array_dates = array_merge($array_dates, $array_new);
   }
  }


  $last  = array_slice($array_dates, -1);
  $first = join(', ', array_slice($array_dates, 0, -1));
  $both  = array_filter(array_merge(array($first), $last), 'strlen');

  $final_dates = join(' and ', $both);

 $response .= "<tr>";
 $response .= "<td>Frequency: </td><td>Report will be scheduled for every ".$final_dates." of every months Starting from ".$newStartDate." and Ending at ".$newEndDate."</td>";
 $response .= "</tr>";
 }

 if($recurrence_pattern == 'Monthly' && empty($row['EndDate']))
 {

  $newStartDate = date("M d,Y", strtotime($StartDate));
  
  $newTime = date('g:i a', strtotime($Time));

  $array_dates = explode(",",$monthly_days);

  foreach($array_dates as $row)
  {
  if($row == 32)
   {
	$array_new = array(" month end");
	end($array_dates);        
	$key = key($array_dates); 
	unset($array_dates[$key]);

	$array_dates = array_merge($array_dates, $array_new);
   }
  }

  $last  = array_slice($array_dates, -1);
  $first = join(', ', array_slice($array_dates, 0, -1));
  $both  = array_filter(array_merge(array($first), $last), 'strlen');

  $final_dates = join(' and ', $both);
	

 $response .= "<tr>";
 $response .= "<td>Frequency: </td><td>Report will be scheduled for every ".$final_dates." of every months Starting from ".$newStartDate."</td>";
 $response .= "</tr>";
 }


 if($recurrence_pattern == 'Quarterly' && $row['EndDate'] == '')
 {
 	
  $newStartDate = date("M d,Y", strtotime($StartDate));
  $newTime = date('g:i a', strtotime($Time));
  $monthly_days = $row['quarterly_days'];
  $array_dates = explode(",",$monthly_days);
  $quarterly_months = $row['months'];

  foreach($array_dates as $row)
  {
  if($row == 32)
   {
	$array_new = array(" month end");
	end($array_dates);        
	$key = key($array_dates); 
	unset($array_dates[$key]);

	$array_dates = array_merge($array_dates, $array_new);
   }
  }


  $last  = array_slice($array_dates, -1);
  $first = join(', ', array_slice($array_dates, 0, -1));
  $both  = array_filter(array_merge(array($first), $last), 'strlen');

  $final_dates = join(' and ', $both);

 $response .= "<tr>";
 $response .= "<td>Frequency: </td><td>Report will be scheduled for every ".$final_dates." of following months ".$quarterly_months." Starting from ".$newStartDate."</td>";
 $response .= "</tr>";

 }

  if($recurrence_pattern == 'Quarterly' && $row['EndDate'] != '')
 {
  $newStartDate = date("M d,Y", strtotime($StartDate));
  $newEndDate = date("M d,Y", strtotime($EndDate));
  $newTime = date('g:i a', strtotime($Time));
  $monthly_days = $row['quarterly_days'];

  $quarterly_months = $row['months'];

  $array_dates = explode(",",$monthly_days);

  foreach($array_dates as $row)
  {
  if($row == 32)
   {
	$array_new = array(" month end");
	end($array_dates);        
	$key = key($array_dates); 
	unset($array_dates[$key]);

	$array_dates = array_merge($array_dates, $array_new);
   }
  }

  $last  = array_slice($array_dates, -1);
  $first = join(', ', array_slice($array_dates, 0, -1));
  $both  = array_filter(array_merge(array($first), $last), 'strlen');

  $final_dates = join(' and ', $both);
	

 $response .= "<tr>";
 $response .= "<td>Frequency: </td><td>Report will be scheduled for every ".$final_dates." of following months ".$quarterly_months." Starting from ".$newStartDate." till ".$newEndDate."</td>";
 $response .= "</tr>";

 }

 if($recurrence_pattern == 'semi_monthly' && $row['EndDate'] == '')
 {
  $newStartDate = date("M d,Y", strtotime($StartDate));
  $newTime = date('g:i a', strtotime($Time));
 
  $semi_months = $row['semi_month_days'];

  if($semi_months == '1,15')
  {
  	$final_dates = '1<sup>st</sup> and 15<sup>th</sup>';
  }
  else if ($semi_months == '15,32')
  {
  	$final_dates = '15<sup>th</sup> and month end';
  }

 $response .= "<tr>";
 $response .= "<td>Frequency: </td><td>Report will be scheduled for every ".$final_dates." of every months Starting from ".$newStartDate."</td>";
 $response .= "</tr>";

 }

 if($recurrence_pattern == 'semi_monthly' && $row['EndDate'] != '')
 {
  $newStartDate = date("M d,Y", strtotime($StartDate));
  $newEndDate = date("M d,Y", strtotime($EndDate));
  $newTime = date('g:i a', strtotime($Time));
  $semi_months = $row['semi_month_days'];

  if($semi_months == '1,15')
  {
  	$final_dates = '1<sup>st</sup> and 15<sup>th</sup>';
  }
  else if ($semi_months == '15,32')
  {
  	$final_dates = '15<sup>th</sup> and month end';
  }
	

 $response .= "<tr>";
 $response .= "<td>Frequency: </td><td>Report will be scheduled for every ".$final_dates." of every months Starting from ".$newStartDate." till ".$newEndDate."</td>";
 $response .= "</tr>";

 }

 if($recurrence_pattern == 'Annually' && $row['EndDate'] == '')
 {
  $newStartDate = date("M d,Y", strtotime($StartDate));
  $newTime = date('g:i a', strtotime($Time));
  
  $array_dates = explode(",",$monthly_days);
  $anually_months = $row['months'];

  foreach($array_dates as $row)
  {
  if($row == 32)
   {
	$array_new = array(" month end");
	end($array_dates);        
	$key = key($array_dates); 
	unset($array_dates[$key]);

	$array_dates = array_merge($array_dates, $array_new);
   }
  }


  $last  = array_slice($array_dates, -1);
  $first = join(', ', array_slice($array_dates, 0, -1));
  $both  = array_filter(array_merge(array($first), $last), 'strlen');

  $final_dates = join(' and ', $both);

 $response .= "<tr>";
 $response .= "<td>Frequency: </td><td>Report will be scheduled for every ".$final_dates." of following months ".$anually_months." Starting from ".$newStartDate."</td>";
 $response .= "</tr>";
 }

 if($recurrence_pattern == 'Annually' && $row['EndDate'] != '')
 {
  $newStartDate = date("M d,Y", strtotime($StartDate));
  $newEndDate = date("M d,Y", strtotime($EndDate));
  $newTime = date('g:i a', strtotime($Time));
 
  $anually_months = $row['months'];

  $array_dates = explode(",",$monthly_days);

  foreach($array_dates as $row)
  {
  if($row == 32)
   {
	$array_new = array(" month end");
	end($array_dates);        
	$key = key($array_dates); 
	unset($array_dates[$key]);

	$array_dates = array_merge($array_dates, $array_new);
   }
  }

  $last  = array_slice($array_dates, -1);
  $first = join(', ', array_slice($array_dates, 0, -1));
  $both  = array_filter(array_merge(array($first), $last), 'strlen');

  $final_dates = join(' and ', $both);
	

 $response .= "<tr>";
 $response .= "<td>Frequency: </td><td>Report will be scheduled for every ".$final_dates." of following months ".$anually_months." Starting from ".$newStartDate." till ".$newEndDate."</td>";
 $response .= "</tr>";
 }

 if($recurrence_pattern == 'Customization' && empty($row['EndDate']))
 {

  $newStartDate = date("M d,Y", strtotime($StartDate));
  $newTime = date('g:i a', strtotime($Time));

  $array_dates = explode(",",$no_of_days);

  foreach($array_dates as $row)
  {
  if($row == 1)
   {
	$array_new_dates[] = '1<sup>st</sup>'; 
   }
   if($row == 2)
   {
	$array_new_dates[] = '2<sup>nd</sup>'; 
   }
   if($row == 3)
   {
	$array_new_dates[] = '3<sup>rd</sup>'; 
   }
   if($row == 4)
   {
	$array_new_dates[] = '4<sup>th</sup>'; 
   }
   if($row == 5)
   {
	$array_new_dates[] = '5<sup>th</sup>'; 
   }
   if($row == 6)
   {
	$array_new_dates[] = '6<sup>th</sup>'; 
   }
   if($row == 7)
   {
	$array_new_dates[] = '7<sup>th</sup>'; 
   }
   if($row == 8)
   {
	$array_new_dates[] = '8<sup>th</sup>'; 
   }
   if($row == 9)
   {
	$array_new_dates[] = '9<sup>th</sup>'; 
   }
   if($row == 10)
   {
	$array_new_dates[] = '10<sup>th</sup>'; 
   }
   if($row == 11)
   {
	$array_new_dates[] = '11<sup>th</sup>'; 
   }
   if($row == 12)
   {
	$array_new_dates[] = '12<sup>st</sup>'; 
   }
   if($row == 13)
   {
	$array_new_dates[] = '13<sup>th</sup>'; 
   }
   if($row == 14)
   {
	$array_new_dates[] = '14<sup>th</sup>'; 
   }
   if($row == 15)
   {
	$array_new_dates[] = '15<sup>th</sup>'; 
   }
   if($row == 16)
   {
	$array_new_dates[] = '16<sup>th</sup>'; 
   }
   if($row == 17)
   {
	$array_new_dates[] = '17<sup>th</sup>'; 
   }
   if($row == 18)
   {
	$array_new_dates[] = '18<sup>th</sup>'; 
   }
   if($row == 19)
   {
	$array_new_dates[] = '19<sup>th</sup>'; 
   }
   if($row == 20)
   {
	$array_new_dates[] = '20<sup>th</sup>'; 
   }
   if($row == 21)
   {
	$array_new_dates[] = '21<sup>st</sup>'; 
   }
   if($row == 22)
   {
	$array_new_dates[] = '22<sup>nd</sup>'; 
   }
   if($row == 23)
   {
	$array_new_dates[] = '23<sup>rd</sup>'; 
   }
   if($row == 32)
   {
	$array_new_dates[] = 'last'; 
   }
  }


  $last  = array_slice($array_new_dates, -1);
  $first = join(', ', array_slice($array_new_dates, 0, -1));
  $both  = array_filter(array_merge(array($first), $last), 'strlen');

  $final_dates = join(' and ', $both);

 $response .= "<tr>";
 $response .= "<td>Frequency: </td><td>Report will be scheduled for  ".$final_dates." buisiness day of every months Starting from ".$newStartDate." at ".$newTime."</td>";
 $response .= "</tr>";
 }


 if($recurrence_pattern == 'Customization' && !empty($row['EndDate']))
 {

  $newStartDate = date("M d,Y", strtotime($StartDate));
  $newEndDate = date("M d,Y", strtotime($EndDate));
  $newTime = date('g:i a', strtotime($Time));

  $array_dates = explode(",",$no_of_days);

  foreach($array_dates as $row)
  {
  if($row == 1)
   {
	$array_new_dates[] = '1<sup>st</sup>'; 
   }
   if($row == 2)
   {
	$array_new_dates[] = '2<sup>nd</sup>'; 
   }
   if($row == 3)
   {
	$array_new_dates[] = '3<sup>rd</sup>'; 
   }
   if($row == 4)
   {
	$array_new_dates[] = '4<sup>th</sup>'; 
   }
   if($row == 5)
   {
	$array_new_dates[] = '5<sup>th</sup>'; 
   }
   if($row == 6)
   {
	$array_new_dates[] = '6<sup>th</sup>'; 
   }
   if($row == 7)
   {
	$array_new_dates[] = '7<sup>th</sup>'; 
   }
   if($row == 8)
   {
	$array_new_dates[] = '8<sup>th</sup>'; 
   }
   if($row == 9)
   {
	$array_new_dates[] = '9<sup>th</sup>'; 
   }
   if($row == 10)
   {
	$array_new_dates[] = '10<sup>th</sup>'; 
   }
   if($row == 11)
   {
	$array_new_dates[] = '11<sup>th</sup>'; 
   }
   if($row == 12)
   {
	$array_new_dates[] = '12<sup>st</sup>'; 
   }
   if($row == 13)
   {
	$array_new_dates[] = '13<sup>th</sup>'; 
   }
   if($row == 14)
   {
	$array_new_dates[] = '14<sup>th</sup>'; 
   }
   if($row == 15)
   {
	$array_new_dates[] = '15<sup>th</sup>'; 
   }
   if($row == 16)
   {
	$array_new_dates[] = '16<sup>th</sup>'; 
   }
   if($row == 17)
   {
	$array_new_dates[] = '17<sup>th</sup>'; 
   }
   if($row == 18)
   {
	$array_new_dates[] = '18<sup>th</sup>'; 
   }
   if($row == 19)
   {
	$array_new_dates[] = '19<sup>th</sup>'; 
   }
   if($row == 20)
   {
	$array_new_dates[] = '20<sup>th</sup>'; 
   }
   if($row == 21)
   {
	$array_new_dates[] = '21<sup>st</sup>'; 
   }
   if($row == 22)
   {
	$array_new_dates[] = '22<sup>nd</sup>'; 
   }
   if($row == 23)
   {
	$array_new_dates[] = '23<sup>rd</sup>'; 
   }
   if($row == 32)
   {
	$array_new_dates[] = 'last'; 
   }
  }


  $last  = array_slice($array_new_dates, -1);
  $first = join(', ', array_slice($array_new_dates, 0, -1));
  $both  = array_filter(array_merge(array($first), $last), 'strlen');

  $final_dates = join(' and ', $both);

 $response .= "<tr>";
 $response .= "<td>Frequency: </td><td>Report will be scheduled for ".$final_dates." buisiness day of every months Starting from ".$newStartDate." at ".$newTime." till ".$newEndDate."</td>";
 $response .= "</tr>";
 }



 $response .= "<tr>";
 $response .= "<td>Report Name: </td><td>".$ReportName."</td>";
 $response .= "</tr>";

 $response .= "<tr>";
 $response .= "<td>User Type: </td><td>".$usertypename."</td>";
 $response .= "</tr>";

 // $response .= "<tr>";
 // $response .= "<td>User Name: </td><td>".$UserName."</td>";
 // $response .= "</tr>";

 // $response .= "<tr>";
 // $response .= "<td>Subject: </td><td>".$Subject."</td>";
 // $response .= "</tr>";

 // $response .= "<tr>";
 // $response .= "<td>Type: </td><td>".$Type."</td>";
 // $response .= "</tr>";

 $response .= "<tr>";
 $response .= "<td>Created By: </td><td>".$CreatedBy."</td>";
 $response .= "</tr>";

 $response .= "<tr>";
 $response .= "<td>Directory Path: </td><td><span data-title='".$fullDirectoryPath."'>".$directoryPath."</span></td>";
 $response .= "</tr>";

 if($sftp != 0)
 {

$sftp_query = "SELECT * FROM SCHEDULER_SFTP WHERE id IN (".$sftp.") ";
$result2 = mysqli_query($conn,$sftp_query);

while( $data = mysqli_fetch_array($result2) ){
  $arr[] = $data['path'];
  $sftpPath = $data['path'];
}
$sftpDirectory = implode(" ,",$arr);

 $response .= "<tr>";
 $response .= "<td>SFTP Directory Path: </td><td><span data-title='".$sftpDirectory."'>".$sftpPath."</span></td>";
 $response .= "</tr>"; 
 }

if($last_run_date != '')
{
 $response .= "<tr>";
 $response .= "<td>Last Run Date & Time: </td><td>".date('m-d-Y H:i:s', strtotime($last_run_date))."</td>";
 $response .= "</tr>";
}

 $response .= "<tr>";
 $response .= "<td><b style='font-weight: 900;'>Job Status: </b></td><td>".$status."</td>";
 $response .= "</tr>";

}
$response .= "</table>";

echo $response;
exit;
}

?>