<html>  
<head>  
    <title>Create Event - OnlyPlans</title>  
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <link rel="stylesheet" href="styles.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<style>
 body {
  background-color: #f8f9fb;
 }
 
 .create-event-container {
  max-width: 900px;
  margin: 50px auto;
  padding: 20px;
  background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
 }
 
 .back-button {
  background: white;
  color: #667eea;
  border: 2px solid white;
  padding: 10px 20px;
  border-radius: 25px;
  text-decoration: none;
  margin-bottom: 20px;
  display: inline-block;
  transition: all 0.3s ease;
  font-weight: 500;
 }
 
 .back-button:hover {
  background: transparent;
  color: white;
  text-decoration: none;
 }
 
 .form-container {
  background: white;
  padding: 30px;
  border-radius: 10px;
 }
 
 .form-title {
  color: #2c3e50;
  margin-bottom: 25px;
  font-weight: 600;
  font-size: 24px;
 }
 
 .form-group label {
  font-weight: 600;
  color: #333;
  margin-bottom: 8px;
  display: block;
 }
 
 .info-text {
  color: #666;
  font-size: 13px;
  margin-top: 5px;
 }
 
 .form-control {
  border-radius: 8px;
  border: 2px solid #ddd;
  padding: 12px 15px;
  transition: border-color 0.3s ease;
  font-size: 14px;
 }
 
 .form-control:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  outline: none;
 }
 
 input[type="date"] {
  padding: 8px 12px;
  height: 42px;
  line-height: 1.5;
 }
 
 .btn-submit {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  padding: 12px 30px;
  border-radius: 25px;
  font-weight: 600;
  transition: all 0.3s ease;
  width: 100%;
  color: white;
  font-size: 16px;
  margin-top: 20px;
 }
 
 .btn-submit:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
  color: white;
 }
 
 .color-preview {
  width: 20px;
  height: 20px;
  border-radius: 4px;
  display: inline-block;
  margin-left: 10px;
  border: 2px solid #ddd;
  vertical-align: middle;
 }
 
 select.form-control {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  padding: 8px 12px;
  padding-right: 35px;
  height: 42px;
  line-height: 1.5;
 }
 
 #event_color {
  padding: 8px 12px;
  height: 42px;
  line-height: 1.5;
 }
 
 .error {
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
  <div class="create-event-container">
    <a href="index.php" class="back-button">← Back to Calendar</a>
    
    <h2 style="color: white; text-align: center; margin-bottom: 30px;">
        Create New Event
    </h2>
    
    <?php if ($fromSuggestions): ?>
    <div style="background: white; color: #2c3e50; border-radius: 10px; padding: 15px; margin-bottom: 20px; text-align: center;">
        <strong>Smart Suggestion Applied!</strong><br>
        <span style="font-size: 14px;">This time slot was automatically selected based on your calendar analysis.</span>
    </div>
    <?php endif; ?>
    
    <div class="form-container">
        <h3 class="form-title">Event Details</h3>
        
        <form method="post" id="event-form">  
            <!-- Event Title -->
            <div class="form-group">
                <label for="title">Event Title</label>
                <input type="text" name="title" id="title" placeholder="e.g., Team Meeting" required 
                       class="form-control" value="<?php echo htmlspecialchars($prefilled_title); ?>"/>
                <p class="info-text">What is this event about?</p>
            </div>
            
            <!-- Start Date and Time Row -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" name="start_date" id="start_date" required class="form-control" 
                               value="<?php echo htmlspecialchars($prefilled_start_date); ?>"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="start_time">Start Time</label>
                        <input type="text" name="start_time" id="start_time" required class="form-control" 
                               placeholder="HH:MM (e.g., 14:30)" pattern="^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$" 
                               maxlength="5" value="<?php echo htmlspecialchars($prefilled_start_time); ?>"/>
                    </div>
                </div>
            </div>
            <p class="info-text">When does your event start?</p>
            
            <!-- End Date and Time Row -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" name="end_date" id="end_date" required class="form-control"
                               value="<?php echo htmlspecialchars($prefilled_end_date); ?>"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="end_time">End Time</label>
                        <input type="text" name="end_time" id="end_time" required class="form-control" 
                               placeholder="HH:MM (e.g., 16:30)" pattern="^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$" 
                               maxlength="5" value="<?php echo htmlspecialchars($prefilled_end_time); ?>"/>
                    </div>
                </div>
            </div>
            <p class="info-text">When does your event end?</p>
            
            <!-- Event Color -->
            <div class="form-group">
                <label for="event_color">Event Color <span class="color-preview" id="colorPreview"></span></label>
                <select name="event_color" id="event_color" class="form-control">
                    <option value="#3788d8" style="background-color: #3788d8; color: white;">🔵 Blue (Default)</option>
                    <option value="#28a745" style="background-color: #28a745; color: white;">🟢 Green (Work)</option>
                    <option value="#dc3545" style="background-color: #dc3545; color: white;">🔴 Red (Important)</option>
                    <option value="#ffc107" style="background-color: #ffc107; color: black;">🟡 Yellow (Personal)</option>
                    <option value="#6f42c1" style="background-color: #6f42c1; color: white;">🟣 Purple (Health)</option>
                    <option value="#fd7e14" style="background-color: #fd7e14; color: white;">🟠 Orange (Social)</option>
                    <option value="#20c997" style="background-color: #20c997; color: white;">🔵 Teal (Study)</option>
                    <option value="#e83e8c" style="background-color: #e83e8c; color: white;">🩷 Pink (Family)</option>
                    <option value="#6c757d" style="background-color: #6c757d; color: white;">⚫ Black</option>
                </select>
                <p class="info-text">Choose a color to organize your events</p>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" name="submit" class="btn-submit">
                Create Event
            </button>
            
            <!-- Messages -->
            <div id="message"></div>
        </form>
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