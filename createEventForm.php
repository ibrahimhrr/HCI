<html>  
<head>  
    <title>OnlyPlans - Create Event</title>  
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <link rel="stylesheet" href="styles.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<style>
 .box
 {
  width:100%;
  max-width:600px;
  background-color:#f9f9f9;
  border:1px solid #ccc;
  border-radius:12px;
  padding:30px;
  margin:0 auto;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
 }
 
 .form-title {
  color: #333;
  margin-bottom: 30px;
  text-align: center;
  font-weight: 600;
 }
 
 .form-group label {
  font-weight: 600;
  color: #555;
  margin-bottom: 8px;
 }
 
 .form-control {
  border-radius: 8px;
  border: 2px solid #ddd;
  padding: 12px 15px;
  transition: border-color 0.3s ease;
  font-size: 14px;
  line-height: 1.4;
  vertical-align: top;
  height: auto;
  min-height: 44px;
  display: block;
  width: 100%;
  box-sizing: border-box;
  cursor: pointer;
 }

 /* Specific styling for time inputs */
 input[type="time"] {
  -webkit-appearance: none;
  -moz-appearance: textfield;
  appearance: none;
  background: white;
  cursor: pointer;
  position: relative;
 }

 input[type="time"]::-webkit-calendar-picker-indicator {
  background: transparent;
  bottom: 0;
  color: transparent;
  cursor: pointer;
  height: auto;
  left: 0;
  position: absolute;
  right: 0;
  top: 0;
  width: auto;
 }

 input[type="time"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
 }

 input[type="time"]::-webkit-clear-button {
  -webkit-appearance: none;
 }

 /* Firefox time input styling */
 input[type="time"]::-moz-focus-inner {
  border: 0;
  padding: 0;
 }
 
 .form-control:focus {
  border-color: #00a8ff;
  box-shadow: 0 0 0 3px rgba(0, 168, 255, 0.1);
 }
 
 .btn-success {
  background: linear-gradient(45deg, #00a8ff, #f06292);
  border: none;
  padding: 12px 30px;
  border-radius: 25px;
  font-weight: 600;
  transition: all 0.3s ease;
  width: 100%;
 }
 
 .btn-success:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 168, 255, 0.3);
 }
 
 .btn-back {
  background: #6c757d;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 20px;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.3s ease;
  display: inline-block;
  margin-bottom: 20px;
 }
 
 .btn-back:hover {
  background: #5a6268;
  color: white;
  text-decoration: none;
  transform: translateY(-1px);
 }
 
 .error
{
  color: #dc3545;
  font-weight: 600;
  text-align: center;
  margin-top: 15px;
  padding: 10px;
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
  border-radius: 8px;
} 

.success {
  color: #155724;
  font-weight: 600;
  text-align: center;
  margin-top: 15px;
  padding: 10px;
  background-color: #d4edda;
  border: 1px solid #c3e6cb;
  border-radius: 8px;
}

.time-helper {
  font-size: 12px;
  color: #6c757d;
  margin-top: 5px;
} 

/* Color selector styling */
#event_color {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background: white;
  border-radius: 8px;
  border: 2px solid #ddd;
  padding: 12px 15px;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s ease;
}

#event_color:focus {
  border-color: #00a8ff;
  box-shadow: 0 0 0 3px rgba(0, 168, 255, 0.1);
}

#event_color option {
  padding: 10px;
  font-weight: 600;
}

/* Preview color box */
.color-preview {
  width: 30px;
  height: 30px;
  border-radius: 6px;
  display: inline-block;
  margin-left: 10px;
  border: 2px solid #ddd;
  vertical-align: middle;
  transition: all 0.3s ease;
}
</style>
<?php 
include('connection.php');

// Check if coming from smart suggestions
$fromSuggestions = isset($_GET['from_suggestions']) && $_GET['from_suggestions'] === 'true';
$prefilled_title = $_GET['title'] ?? '';
$prefilled_start_date = $_GET['start_date'] ?? '';
$prefilled_start_time = $_GET['start_time'] ?? '';
$prefilled_end_date = $_GET['end_date'] ?? '';
$prefilled_end_time = $_GET['end_time'] ?? '';

if(isset($_REQUEST['save-event']))
{
  $title = $_REQUEST['title'];
  $start_date = $_REQUEST['start_date'];
  $start_time = $_REQUEST['start_time'];
  $end_date = $_REQUEST['end_date'];
  $end_time = $_REQUEST['end_time'];
  $event_color = $_REQUEST['event_color'] ?? '#3788d8'; // Default blue color
  
  // Combine date and time for database storage as datetime
  $start_datetime = $start_date . ' ' . $start_time . ':00';
  $end_datetime = $end_date . ' ' . $end_time . ':00';

    $insert_query = mysqli_query(
    $connection,
    "INSERT INTO table_event 
     (title, start_date, start_time, end_date, end_time, color)
     VALUES ('$title', '$start_date', '$start_time', '$end_date', '$end_time', '$event_color')"
  );
  if($insert_query)
  {
    header('location:index.php');
  }
  else
  {
    $msg = "Event not created";
  }
}
?>
<body>  
    <div class="container">  
      <div class="table-responsive">  
        <a href="index.php" class="btn-back">← Back to Calendar</a>
        <h3 class="form-title">Create New Event</h3>
        
        <?php if ($fromSuggestions): ?>
        <div class="alert alert-info" style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); color: white; border: none; border-radius: 10px; padding: 15px; margin-bottom: 20px; text-align: center;">
            <strong>Smart Suggestion Applied!</strong><br>
            This time slot was automatically selected based on your calendar analysis.
        </div>
        <?php endif; ?>
        
        <div class="box">
         <form method="post" id="event-form">  
           <div class="form-group">
           <label for="title">Event Title</label>
           <input type="text" name="title" id="title" placeholder="Enter event title" required 
           class="form-control" value="<?php echo htmlspecialchars($prefilled_title); ?>"/>
          </div>
          <div class="form-group">
           <label for="start_date">Start Date</label>
           <input type="date" name="start_date" id="start_date" required class="form-control" 
                  value="<?php echo htmlspecialchars($prefilled_start_date); ?>"/>
           <div class="time-helper">Select the date when your event begins</div>
          </div>
          
          <div class="form-group">
           <label for="start_time">Start Time</label>
           <input type="text" name="start_time" id="start_time" required class="form-control" 
                  placeholder="HH:MM (e.g., 14:30)" pattern="^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$" 
                  title="Please enter time in HH:MM format (00:00 to 23:59)" maxlength="5"
                  value="<?php echo htmlspecialchars($prefilled_start_time); ?>"/>
           <div class="time-helper">Type time in 24-hour format (e.g., 14:30 for 2:30 PM)</div>
          </div>
          
          <div class="form-group">
           <label for="end_date">End Date</label>
           <input type="date" name="end_date" id="end_date" required class="form-control"
                  value="<?php echo htmlspecialchars($prefilled_end_date); ?>"/>
           <div class="time-helper">Select the date when your event ends</div>
          </div>
          
          <div class="form-group">
           <label for="end_time">End Time</label>
           <input type="text" name="end_time" id="end_time" required class="form-control" 
                  placeholder="HH:MM (e.g., 16:30)" pattern="^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$" 
                  title="Please enter time in HH:MM format (00:00 to 23:59)" maxlength="5"
                  value="<?php echo htmlspecialchars($prefilled_end_time); ?>"/>
           <div class="time-helper">Type time in 24-hour format (e.g., 16:30 for 4:30 PM)</div>
          </div>
          
           <div class="form-group">
           <label for="event_color">Event Color <span class="color-preview" id="colorPreview"></span></label>
           <select name="event_color" id="event_color" class="form-control" style="height: 50px;">
               <option value="#3788d8" style="background-color: #3788d8; color: white;">🔵 Blue (Default)</option>
               <option value="#28a745" style="background-color: #28a745; color: white;">🟢 Green (Work)</option>
               <option value="#dc3545" style="background-color: #dc3545; color: white;">🔴 Red (Important)</option>
               <option value="#ffc107" style="background-color: #ffc107; color: black;">🟡 Yellow (Personal)</option>
               <option value="#6f42c1" style="background-color: #6f42c1; color: white;">🟣 Purple (Health)</option>
               <option value="#fd7e14" style="background-color: #fd7e14; color: white;">🟠 Orange (Social)</option>
               <option value="#20c997" style="background-color: #20c997; color: white;">🔵 Teal (Study)</option>
               <option value="#e83e8c" style="background-color: #e83e8c; color: white;">🩷 Pink (Family)</option>
               <option value="#6c757d" style="background-color: #6c757d; color: white;">⚫ Gray (Meetings)</option>
           </select>
           <div class="time-helper">Choose a color to categorize your event</div>
          </div>
          <div class="form-group">
           <input type="submit" id="save-event" name="save-event" value="Save Event" class="btn btn-success" />
           </div>
           <div id="message-container"></div>
         </form>
         </div>
       </div>  
      </div>
      
    <script>
    $(document).ready(function() {
        // Initialize color preview
        function updateColorPreview() {
            const selectedColor = $('#event_color').val();
            $('#colorPreview').css('background-color', selectedColor);
        }
        
        // Set initial color preview
        updateColorPreview();
        
        // Update color preview when selection changes
        $('#event_color').on('change', updateColorPreview);
        
        // Handle form submission with AJAX
        $('#event-form').on('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            $('#save-event').prop('disabled', true).val('🔄 Creating Event...');
            $('#message-container').empty();
            
            $.ajax({
                url: 'createEvent.php',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        $('#message-container').html(`
                            <div class="success">
                                ✅ ${response.message}<br>
                                <small>Redirecting to calendar...</small>
                            </div>
                        `);
                        
                        // Redirect to calendar after short delay
                        setTimeout(function() {
                            window.location.href = 'index.php';
                        }, 1500);
                    } else {
                        $('#message-container').html(`
                            <div class="error">❌ ${response.message}</div>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    $('#message-container').html(`
                        <div class="error">❌ Error creating event. Please try again.</div>
                    `);
                },
                complete: function() {
                    // Reset button state
                    $('#save-event').prop('disabled', false).val('Save Event');
                }
            });
        });
        
        // Time input formatting and validation
        function formatTimeInput(input) {
            let value = input.val().replace(/[^\d]/g, ''); // Remove non-digits
            
            if (value.length >= 2) {
                let hours = value.substring(0, 2);
                let minutes = value.substring(2, 4);
                
                // Validate hours (00-23)
                if (parseInt(hours) > 23) {
                    hours = '23';
                }
                
                // Validate minutes (00-59)
                if (minutes && parseInt(minutes) > 59) {
                    minutes = '59';
                }
                
                // Format with colon
                if (minutes) {
                    value = hours + ':' + minutes;
                } else if (value.length > 2) {
                    value = hours + ':';
                } else {
                    value = hours;
                }
                
                input.val(value);
            }
        }

        // Real-time formatting for time inputs
        $('#start_time, #end_time').on('input', function() {
            formatTimeInput($(this));
        });

        // Validate time format on blur
        $('#start_time, #end_time').on('blur', function() {
            const timeValue = $(this).val();
            const timePattern = /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/;
            
            if (timeValue && !timePattern.test(timeValue)) {
                alert('Please enter a valid time in HH:MM format (00:00 to 23:59)');
                $(this).focus();
            }
        });

        // Auto-add colon when typing
        $('#start_time, #end_time').on('keyup', function(e) {
            let value = $(this).val();
            
            // Add colon automatically after 2 digits
            if (value.length === 2 && e.key !== ':' && e.key !== 'Backspace') {
                $(this).val(value + ':');
            }
        });

        // Auto-set end date and time when start is selected
        $('#start_date, #start_time').on('change', function() {
            const startDate = $('#start_date').val();
            const startTime = $('#start_time').val();
            
            if (startDate && startTime && !$('#end_date').val() && /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/.test(startTime)) {
                // Set end date to same as start date
                $('#end_date').val(startDate);
                
                // Add 1 hour to start time
                const [hours, minutes] = startTime.split(':');
                const endHour = (parseInt(hours) + 1) % 24;
                const endTime = String(endHour).padStart(2, '0') + ':' + minutes;
                $('#end_time').val(endTime);
            }
        });
        
        // Validate that end time is after start time
        $('#end_date, #end_time').on('change', function() {
            const startDate = $('#start_date').val();
            const startTime = $('#start_time').val();
            const endDate = $('#end_date').val();
            const endTime = $('#end_time').val();
            
            if (startDate && startTime && endDate && endTime) {
                const startDateTime = new Date(startDate + 'T' + startTime);
                const endDateTime = new Date(endDate + 'T' + endTime);
                
                if (endDateTime <= startDateTime) {
                    alert('End time must be after start time!');
                    $(this).focus();
                }
            }
        });

        // Allow only numbers and colon
        $('#start_time, #end_time').on('keypress', function(e) {
            const allowedKeys = [8, 9, 27, 13, 46, 58]; // backspace, tab, escape, enter, delete, colon
            const key = e.which;
            
            if (allowedKeys.indexOf(key) !== -1 || 
                (key >= 48 && key <= 57) || // numbers 0-9
                key === 58) { // colon
                return true;
            }
            e.preventDefault();
        });
    });
    </script>
 </body>  
</html>  