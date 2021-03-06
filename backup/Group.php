
<?php
        require_once 'dbconfig.php';
        //DB Connection
        $db = new PDO("mysql:host=$host;dbname=$dbname",
                            $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        session_start();
        $Id=$_SESSION["Id"];
        $GroupId=$_SESSION["groupId"];

        $ar = array('Event 1', '2015-02-19', '2015-02-28' );
         json_encode($ar);

        echo "$Id and group is $GroupId<br/>";
        try {
        // execute the stored procedure
        $sql = 'CALL SelectTask(:exGroupID)';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':exGroupID', $GroupId, PDO::PARAM_INT);     
        $stmt->execute();
        //$stmt->closeCursor();
        $arrVal = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($arrVal as $key => $val) {
            $taskId = $val['TaskEventID'];
            $taskBackLog = $val['Backlog'];
            $taskEndDate = $val['EndDate'];
            $taskStartDate = $val['StartDate'];
            $taskTitle = $val['Title'];
            //echo "<br/>$taskId $taskBackLog $taskEndDate $taskStartDate $taskTitle<br/>";
            echo "$taskTitle";
        }//end foreach for task

            //proc for event SelectEvent
        $sql = 'CALL SelectEvent(:exGroupID)';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':exGroupID', $GroupId, PDO::PARAM_INT);     
        $stmt->execute();
        //$stmt->closeCursor();
        $arrVal = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($arrVal as $key => $val) {
            $eventId = $val['TaskEventID'];
            $eventBackLog = $val['Backlog'];
            $eventStartDate = $val['StartDate'];
            $eventTitle = $val['Title'];
            echo "<br/>$taskId $taskBackLog $taskEndDate $taskStartDate $taskTitle<br/>";
       }
   //    echo '<script type="text/javascript">'
   //         , 'addCalanderEvent();'
   //        , '</script>';

       //project details
         
         $sql = 'CALL ProjectDetailsForGroup(:exGroupID)';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':exGroupID', $GroupId, PDO::PARAM_INT);     
        $stmt->execute();
        //$stmt->closeCursor();
        $arrPrj = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($arrPrj as $key => $prj) {
            $prjId = $prj['ProjectID'];
            $prjDesc = $prj['Description'];
            $prjName = $prj['ProjectName'];
         }
           // echo "$prjId and $prjName";

     }
      catch (PDOException $pe){
        die("<br/>caught Error occurred:" . $pe->getMessage());
      } 



if (isset($_POST['acceptbut'])) {
  ?>
  <script type="text/javascript">
    addCalanderEvent();
  </script>
  <?php
// Start the session
//session_start();
echo "accept";
		//  try {        
         //INSERT SPROC
        //$selectevent = $_POST['selectevent'];
        $taskEventTitle = $_POST['Title'];
        $backlog = $_POST['backlog'];
        if ($backlog == null) {
        	$backlogResult = 0;
        	echo "not backlog";
        }
        else{
        	$backlogResult = 1;
        }

        $taskEventStart = $_POST['Startdate'];
        $taskEventEnd = $_POST['Enddate'];
             echo "<br>START DATE : $taskEventStart</br>";
        $startParsed = date_parse($taskEventStart);
        //$taskEventTime = $_POST['endTimepicker'];
       // $taskEventDescription = $_POST['Note'];

       // $input = "2015-02-19";
       // $info = date_parse($input);
           
        $sql = 'CALL InsertTaskEvent0(:exTitle, :exBacklog, :exStartDate, :exEndDate, :GroupID)';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':exTitle', $taskEventTitle, PDO::PARAM_STR,  50);
        $stmt->bindParam(':exBacklog', $backlogResult, PDO::PARAM_LOB );
        $stmt->bindParam(':exStartDate', $taskEventStart, PDO::PARAM_LOB);
        $stmt->bindParam(':exEndDate', $taskEventEnd, PDO::PARAM_LOB);
        $stmt->bindParam(':GroupID', $GroupId, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
        echo "inserted";

        $note = $_POST['Note'];
        $url = $_POST['Link'];

        $sql = 'CALL InsertNoteUrl0(:exTitle, :exNote, :exUrl, @iTaskEventID)';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':exTitle', $taskEventTitle, PDO::PARAM_STR,  50);
        $stmt->bindParam(':exNote', $note, PDO::PARAM_LOB );
        $stmt->bindParam(':exUrl', $url, PDO::PARAM_LOB);
        $stmt->execute();
        $stmt->closeCursor();
        $resu = $db->query("SELECT @iTaskEventID AS Id")->fetch(PDO::FETCH_ASSOC);

}

///update task event-----------------------------------------------------------------------------------------------------
//$sql = 'CALL UpdateTaskEvent0(:exTitle, :exBacklog, :exStartDate, :exEndDate, :GroupID, :exDesc, :exTaskEventID)';
//        $stmt = $db->prepare($sql);
//        $stmt->bindParam(':exTitle', $taskEventTitle, PDO::PARAM_STR,  50);
//        $stmt->bindParam(':exBacklog', $backlog, PDO::PARAM_LOB );
//        $stmt->bindParam(':exStartDate', $taskEventStart, PDO::PARAM_LOB);
//        $stmt->bindParam(':exEndDate', $taskEventEnd, PDO::PARAM_LOB);
 //       $stmt->bindParam(':GroupID', $groupId, PDO::PARAM_INT);
 //       $stmt->bindParam(':exDesc', $taskEventDescription, PDO::PARAM_STR, 1000);
 //       $stmt->bindParam(':exTaskEventID', $taskEventID, PDO::PARAM_INT);
 //       $stmt->execute();
 //       $stmt->closeCursor();

?>
<form id="Group" action="Group.php" method="POST">
<!DOCTYPE html>
<html>
<head>
	<title></title>

</head>

<body>
<!--<body onload="addCalanderEvent('myEvent', 2015-02-19, 2015-02-22)">-->

<?php include 'Header.html'; ?>

<div id='Project Desc'>
	<h1><?php echo "$prjName";?></h1>
	<p> <?php echo "$prjDesc";?></p>

</div>

<div id='bodyCal'>
<div id='wrap'>

		<div id='external-events'>
			<h4>Backlog Events</h4>
			<div class='fc-event'>My Event 1</div>
			<div class='fc-event'>My Event 2</div>
			<div class='fc-event'>My Event 3</div>
			<div class='fc-event'>My Event 4</div>
			<div class='fc-event'>My Event 5</div>
			<p>
				<input hidden type='checkbox' checked id='drop-remove' />
				<label hidden for='drop-remove'>remove after drop</label>
			</p>

		</div>
		<div id='Students'>

			<h4>students</h4>
				<!--<select name="Students" size="4" style="width: 110px; overflow:hidden;" >-->
			<?php
         require_once 'dbconfig.php';

        // $db = new PDO("mysql:host=$host;dbname=$dbname",
       //            $username, $password);
       // $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        

        $sql = 'CALL StudentsinGroup0(:exGroupID)';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':exGroupID', $GroupId, PDO::PARAM_INT);     
        $stmt->execute();
       // $stmt->closeCursor();

        $arrVal = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<select name='Students' size='4' style='width: 110px; overflow:hidden;'></option>";

        foreach ($arrVal as $key => $val) {
            $UserID = $val['UserID'];
            $uName = $val['uName'];
            echo "<option value=$UserID>$uName</option>"; 
          }
          echo "</select>";// Closing of list box
        


          ?>
		</div>
		
<button type="button" id='Addevent' class="btn btn-info btn-sm" onclick="EventAdd()"> Add Event 
      <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>
    </button> 

    <div id='calendar'></div>

    <div style='clear:both'></div>
    
    <div class="popup">
    <h3>Add to the Calander</h3>
    <form id="popform">
    <!--onchange="eventoption(this.value)" class="js-example-basic-single" -->
    <select id="selectevent" name="selectevent"   >
          <option value="0" selected disabled >Please Select One</option>
          <option disabled>---------------------</option>
          <option value="1" onclick="$('#Enddatepicker').attr('disabled', 'disabled');">All Day Event</option>
          <option value="2" onmousedown="$('#Enddatepicker').attr('disabled', '');">Long Event </option>
          <!--<option value="3">Time event</option>-->
    </select>
    <br />
    <br />
    <input type="checkbox" id="backlog">is this back log??</input>
    <br />
    <br />
    <section> Event Title: </section>
    <aside><input type="text" id="Title" placeholder="Project Title" ></aside>
    <br />
    <br />
    <section> Start Date: </section>
    <aside><input type="text" id="Startdatepicker" placeholder="dd/mm/yyyy"></aside>
    <br/>
    <br/>
    <section id="endname"> End Date: </section>
    <aside><input disabled type="text" id="Enddatepicker" style="visability:hidden;" placeholder="dd/mm/yyyy"></aside>  
    <br/>
    <br/>
    <!--<label id="lbstarttime" for="startTimepicker" > Start Time:</label>
    <aside><input  type="text" id="startTimepicker" style="visability:hidden;" placeholder="am/pm"></aside> 
    <br/>
    <br/>
    <label id="lbendtime" for="endTimepicker" > End Time:</label>
    <aside><input  type="text" id="endTimepicker" style="visability:hidden;" placeholder="am/pm"></aside>-->
    <button type="button" id="acceptbut" onclick="Choice()">Accept</button>
    <button type="button" id="cancelbut" onclick="pophide()">Cancel</button>

    <section id="notecont"> Notes :
    <aside><TEXTAREA id="Note"  type="text" rows="10" maxlenght="1000" wrap="hard" placeholder="Enter text here..........."></TEXTAREA></aside></section>
    
    <section id="linkcont"> Relevent Link :
    <aside><input  id="Link" type="url" placeholder="www.Website.com"></input></aside></section>
    </form>
    </div>

    <div class="popupupdate">
    <h3>You clicked then event</h3>
    <form id="popform">
    <!--onchange="eventoption(this.value)" class="js-example-basic-single" -->
    
    <button type="button" id="acceptbut" onclick = "pophideupdate()" style="">Accept</button>
    <button type="button" id="cancelbut" onclick = "pophideupdate()" style="">Cancel</button>
    </form>
    </div>

    <div class="popupdrop">
    <h3>You droped the event</h3>
    <form id="popform">
    <!--onchange="eventoption(this.value)" class="js-example-basic-single" -->
    
    <button type="button" id="acceptbut" onclick="pophidedrop()" style="">Accept</button>
    <button type="button" id="cancelbut" onclick="pophidedrop()" style="">Cancel</button>
    </form>
    </div>

  </div>

</div>

<div id="footer" class="navbar navbar-default navbar-fixed-bottom">
  <div class="container">
    <p>
          HRPM Project:Made my Tomás Mc Mahon,Greg Sheerin,Cormac Hallinan,John Mc Gowan.Made for PJR300 
          <a href="about.html">About</a>
      <a onclick="div_showcontact()">Contact</a>
      <a onclick="div_show()">Register?</a>
    </p>
  </div>
</div>



</body>
<meta charset='utf-8' />
<link href="Calender/fullcalendar.css" rel="stylesheet" />
<link href="Calender/fullcalendar.print.css" rel="stylesheet" media="print" />
<link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" />
<link href="Css/CalCss.css" rel="stylesheet" />
<link href="Css/datepicker.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="js.select2-3.5.2/select2.css"></link>
<link href="bootstrap/css/bootstrap.css" rel="stylesheet" />
<link href="Css/jquery.timepicker.css" rel="stylesheet" />
<script src="lib/moment.min.js"></script>
<script src="lib/jquery.min.js"></script>
<script src='lib/jquery-ui.custom.min.js'></script>
<script src='lib/bootstrap-datepicker.js'></script>
<script src='less/datepicker.less'></script>
<script src='Calender/CalCustom.js'></script>
<script src="Calender/jquery.timepicker.js"></script>
<script src="Calender/fullcalendar.min.js"></script>
<script src="bootstrap/js/bootstrap.js"></script>

<script type="text/javascript">
   
  </script>
<script type="text/javascript">
var eventName = <?php echo json_encode($ar[0]) ?>;
var eventstart = <?php echo json_encode($ar[1]) ?>;
var eventEnd = <?php echo json_encode($ar[2]) ?>;
alert("the event name is " +eventName +" start " + eventstart + " end " + eventEnd);

window.onload = function addCalanderEvent( title, start, end)
{
    

    var eventObject = {
    title: eventName,
    start: eventstart,
    end: eventEnd
    };

    $('#calendar').fullCalendar('renderEvent', eventObject, true);
    return eventObject;
}
</script>
<script>

function Choice(){
  alert('poop'); 
 /* var Foo = foo
  if (Foo == 1){
    //addCalanderEvent(); 
    alert('poop');  
  }
  else {
    //addCalanderTask();
    alert('greg');  
  }*/};

function addCalanderEvent( start, end, title)
{
    var eventObject = {
    title: "Event",
    start: '2015-02-24',
    end: '2015-02-25'
    };

    $('#calendar').fullCalendar('renderEvent', eventObject, true);
    return eventObject;

function addCalanderTask( start, end, title)
{
    var eventObject = {
    title: "Task",
    start: '2015-02-24',
    end: '2015-02-25'
    };

    $('#calendar').fullCalendar('renderEvent', eventObject, false);
    return eventObject;
</script>
</html>
</form>