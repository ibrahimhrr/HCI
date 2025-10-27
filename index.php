<html>  
<head>  
    <title>OnlyPlans</title> 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />
<link rel="stylesheet" href="styles.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script> 
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
 <body>
  <h2 class="calendar-title">
    <span class="calendar-title__only">Only</span><span class="calendar-title__plans">Plans</span>
  </h2>
  <div class="container">
   <div id="calendar"></div>
  </div>
  <br>
</body>
</html>

<?php 
  include('connection.php');
  $fetch_event = mysqli_query($connection, "select * from table_event");
?>



<script>
   $(document).ready(function() {
   $('#calendar').fullCalendar({
           selectable: true,
           selectHelper: true,
           select: function()
           {
               $('#myModal').modal('toggle');
           },
           header:
           {
           left: 'month, agendaWeek, agendaDay, list',
           center: 'title',
           right: 'prev, today, next'
           },
           
           buttonText:
           {
           today: 'Today',
           month: 'Month',
           week: 'Week',
           day: 'Day',
           list: 'List'
           },

           events: [{
                title: 'Meet Saif',
                start: '2025-10-22T10:30:00',
                end: '2025-10-22T12:30:00'
           },
           <?php
       while($result = mysqli_fetch_array($fetch_event))
       { ?>
      {
          title: '<?php echo $result['title']; ?>',
          start: '<?php echo $result['start_date']; ?>',
          end: '<?php echo $result['end_date']; ?>',
          color: 'yellow',
          textColor: 'black'
       },
    <?php } ?>
        ]});
});

</script>

<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
       <!-- test-->
        <!-- testing-->
        
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Create Event</h4>
        </div>
        <div class="modal-body">
          <input type="text" class="form-control">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
     </div>
  </div>
</div>
