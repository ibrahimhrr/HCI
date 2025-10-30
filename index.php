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
  $fetch_event = mysqli_query($connection, "select * from table_event ORDER BY start_date ASC");
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
           
           // Add event click functionality for editing
           eventClick: function(calEvent, jsEvent, view) {
               // Populate edit modal with event data
               $('#editEventId').val(calEvent.id);
               $('#editEventTitle').val(calEvent.title);
               
               // Extract date and time from start/end
               var startMoment = moment(calEvent.start);
               var endMoment = moment(calEvent.end);
               
               $('#editStartDate').val(startMoment.format('YYYY-MM-DD'));
               $('#editStartTime').val(startMoment.format('HH:mm'));
               $('#editEndDate').val(endMoment.format('YYYY-MM-DD'));
               $('#editEndTime').val(endMoment.format('HH:mm'));
               
               // Show edit modal
               $('#editEventModal').modal('show');
           },
           header:
           {
           left: 'month, agendaWeek, agendaDay, list, createEventBtn, smartSuggestBtn',
           center: 'title',
           right: 'prev, today, next'
           },
           
           customButtons: {
               createEventBtn: {
                   text: '📝 Create Event',
                   click: function() {
                       window.location.href = 'createEventForm.php';
                   }
               },
               smartSuggestBtn: {
                   text: '🤖 Smart Suggestions', 
                   click: function() {
                       window.location.href = 'smartSuggestionsForm.php';
                   }
               }
           },
           
           // Add Bootstrap button classes to FullCalendar buttons
           viewRender: function(view, element) {
               // Style the custom buttons with Bootstrap classes
               $('.fc-createEventBtn-button').addClass('btn btn-primary').css({
                   'border-radius': '20px',
                   'padding': '6px 16px',
                   'margin-left': '8px',
                   'font-weight': '600'
               });
               $('.fc-smartSuggestBtn-button').addClass('btn btn-info').css({
                   'border-radius': '20px', 
                   'padding': '6px 16px',
                   'margin-left': '5px',
                   'font-weight': '600'
               });
               
               // Style other FullCalendar buttons
               $('.fc-button').not('.fc-createEventBtn-button, .fc-smartSuggestBtn-button').addClass('btn btn-default').css({
                   'border-radius': '18px',
                   'padding': '5px 12px',
                   'margin': '0 2px',
                   'font-weight': '500'
               });
               
               // Style today button specially
               $('.fc-today-button').removeClass('btn-default').addClass('btn btn-warning').css({
                   'border-radius': '18px'
               });
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
                id: 'sample-1',
                title: 'Meet Saif',
                start: '2025-10-22T10:30:00',
                end: '2025-10-22T12:30:00'
           },
           <?php
       while($result = mysqli_fetch_array($fetch_event))
       { 
           // Combine date and time for proper FullCalendar display
           $start_datetime = $result['start_date'];
           $end_datetime = $result['end_date'];
           
           // If you have separate time fields, combine them with the date
           if(isset($result['start_time']) && isset($result['end_time'])) {
               $start_datetime = $result['start_date'] . 'T' . $result['start_time'];
               $end_datetime = $result['end_date'] . 'T' . $result['end_time'];
           } else {
               // If datetime is already combined in start_date/end_date fields
               $start_datetime = date('Y-m-d\TH:i:s', strtotime($result['start_date']));
               $end_datetime = date('Y-m-d\TH:i:s', strtotime($result['end_date']));
           }
       ?>
      {
          id: '<?php echo $result['id'] ?? 'event-' . uniqid(); ?>',
          title: '<?php echo addslashes($result['title']); ?>',
          start: '<?php echo $start_datetime; ?>',
          end: '<?php echo $end_datetime; ?>',
          color: 'yellow',
          textColor: 'black'
       },
    <?php } ?>
        ]});
        
        // Handle edit form submission
        $('#editEventForm').on('submit', function(e) {
            e.preventDefault();
            
            var formData = {
                eventId: $('#editEventId').val(),
                title: $('#editEventTitle').val(),
                startDate: $('#editStartDate').val(),
                startTime: $('#editStartTime').val(),
                endDate: $('#editEndDate').val(),
                endTime: $('#editEndTime').val()
            };
            
            $.ajax({
                url: 'updateEvent.php',
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#editEventModal').modal('hide');
                        $('#calendar').fullCalendar('refetchEvents');
                        alert('Event updated successfully!');
                    } else {
                        alert('Error updating event: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error updating event. Please try again.');
                }
            });
        });
        
        // Handle delete event
        $('#deleteEventBtn').on('click', function() {
            if (confirm('Are you sure you want to delete this event?')) {
                var eventId = $('#editEventId').val();
                
                $.ajax({
                    url: 'deleteEvent.php',
                    method: 'POST',
                    data: { eventId: eventId },
                    success: function(response) {
                        if (response.success) {
                            $('#editEventModal').modal('hide');
                            $('#calendar').fullCalendar('refetchEvents');
                            alert('Event deleted successfully!');
                        } else {
                            alert('Error deleting event: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Error deleting event. Please try again.');
                    }
                });
            }
        });
        
        // Time input formatting for edit modal
        $('#editStartTime, #editEndTime').on('input', function() {
            var value = $(this).val().replace(/[^\d]/g, '');
            
            if (value.length >= 2) {
                var hours = value.substring(0, 2);
                var minutes = value.substring(2, 4);
                
                if (parseInt(hours) > 23) hours = '23';
                if (minutes && parseInt(minutes) > 59) minutes = '59';
                
                if (minutes) {
                    value = hours + ':' + minutes;
                } else if (value.length > 2) {
                    value = hours + ':';
                } else {
                    value = hours;
                }
                
                $(this).val(value);
            }
        });
        
        // Auto-add colon when typing in edit modal
        $('#editStartTime, #editEndTime').on('keyup', function(e) {
            var value = $(this).val();
            if (value.length === 2 && e.key !== ':' && e.key !== 'Backspace') {
                $(this).val(value + ':');
            }
        });
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

<!-- Edit Event Modal -->
<div class="modal fade" id="editEventModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <button type="button" class="close" data-dismiss="modal" style="color: white;">&times;</button>
                <h4 class="modal-title">✏️ Edit Event</h4>
            </div>
            <form id="editEventForm">
                <div class="modal-body" style="padding: 30px;">
                    <input type="hidden" id="editEventId" name="eventId">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="editEventTitle">Event Title</label>
                                <input type="text" class="form-control" id="editEventTitle" name="title" required
                                       style="border-radius: 8px; padding: 12px;">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editStartDate">Start Date</label>
                                <input type="date" class="form-control" id="editStartDate" name="startDate" required
                                       style="border-radius: 8px; padding: 12px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editStartTime">Start Time</label>
                                <input type="text" class="form-control" id="editStartTime" name="startTime" required
                                       placeholder="HH:MM (e.g., 14:30)" pattern="^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$"
                                       style="border-radius: 8px; padding: 12px;" maxlength="5">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editEndDate">End Date</label>
                                <input type="date" class="form-control" id="editEndDate" name="endDate" required
                                       style="border-radius: 8px; padding: 12px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editEndTime">End Time</label>
                                <input type="text" class="form-control" id="editEndTime" name="endTime" required
                                       placeholder="HH:MM (e.g., 16:30)" pattern="^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$"
                                       style="border-radius: 8px; padding: 12px;" maxlength="5">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="deleteEventBtn" style="float: left; border-radius: 20px;">
                        🗑️ Delete Event
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 20px;">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-success" style="border-radius: 20px;">
                        💾 Update Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
