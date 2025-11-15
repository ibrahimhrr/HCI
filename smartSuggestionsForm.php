<!DOCTYPE html>
<html>
<head>
    <title>Smart Event Suggestions - OnlyPlans</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="styles.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <style>
        .smart-suggestions-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .suggestions-form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .suggestion-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border: none;
            border-radius: 10px;
            padding: 20px;
            margin: 10px 0;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }
        
        .suggestion-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .quality-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 5px 12px;
            border-radius: 20px;
            color: white;
            font-weight: bold;
            font-size: 11px;
        }
        
        .quality-excellent { background: #28a745; }
        .quality-good { background: #17a2b8; }
        .quality-fair { background: #ffc107; color: #333; }
        
        .time-display {
            font-size: 22px;
            font-weight: bold;
            color: #2c3e50;
            margin: 10px 0;
        }
        
        .slot-details {
            color: #666;
            font-size: 14px;
            margin-top: 8px;
        }
        
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 40px;
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
        }
        
        .back-button:hover {
            background: transparent;
            color: white;
            text-decoration: none;
        }
        
        .info-text {
            color: #666;
            font-size: 13px;
            margin-top: 5px;
        }
    </style>
    <script>
        (function() {
            const DARK_MODE_KEY = 'onlyplans-dark-mode';
            const HIGH_CONTRAST_KEY = 'onlyplans-high-contrast';

            function applyDisplayPreferences() {
                try {
                    const useDarkMode = localStorage.getItem(DARK_MODE_KEY) === 'true';
                    const useHighContrast = localStorage.getItem(HIGH_CONTRAST_KEY) === 'true';
                    document.body.classList.toggle('dark-mode', useDarkMode);
                    document.body.classList.toggle('high-contrast', useHighContrast);
                } catch (error) {
                    console.warn('Display preference unavailable:', error);
                }
            }

            document.addEventListener('DOMContentLoaded', applyDisplayPreferences);
        })();
    </script>
</head>
<body>
    <div class="smart-suggestions-container">
        <a href="index.php" class="back-button">← Back to Calendar</a>
        
        <h2 style="color: white; text-align: center; margin-bottom: 30px;">
            Smart Time Slot Finder
        </h2>
        
        <div class="suggestions-form">
            <h3 style="margin-bottom: 25px;">Find Available Time Slots</h3>
            <form id="smartSuggestionsForm">
                
                <!-- Event Name -->
                <div class="form-group">
                    <label for="eventName">Event Name</label>
                    <input type="text" class="form-control" id="eventName" name="eventName" 
                           placeholder="e.g., Team Meeting" required>
                    <p class="info-text">What would you like to schedule?</p>
                </div>
                
                <!-- Duration -->
                <div class="form-group">
                    <label for="duration">Duration (hours)</label>
                    <select class="form-control" id="duration" name="duration" required>
                        <option value="0.5">30 minutes</option>
                        <option value="1" selected>1 hour</option>
                        <option value="1.5">1.5 hours</option>
                        <option value="2">2 hours</option>
                        <option value="3">3 hours</option>
                        <option value="4">4 hours</option>
                    </select>
                    <p class="info-text">How long will your event last?</p>
                </div>
                
                <!-- Search Range -->
                <div class="form-group">
                    <label for="searchRange">Search Range</label>
                    <select class="form-control" id="searchRange" name="searchRange" required>
                        <option value="today">Today only</option>
                        <option value="tomorrow">Tomorrow only</option>
                        <option value="this_week" selected>This week (next 7 days)</option>
                        <option value="next_week">Next week (days 8-14)</option>
                        <option value="this_month">This month</option>
                        <option value="next_month">Next month</option>
                    </select>
                    <p class="info-text">When are you looking to schedule?</p>
                </div>
                
                <!-- Preferred Time Range -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="preferredStartTime">Preferred Start Time</label>
                            <select class="form-control" id="preferredStartTime" name="preferredStartTime">
                                <option value="0">Any time (00:00)</option>
                                <option value="6">Morning (06:00)</option>
                                <option value="9" selected>Business Hours (09:00)</option>
                                <option value="12">Afternoon (12:00)</option>
                                <option value="14">After Lunch (14:00)</option>
                                <option value="17">Evening (17:00)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="preferredEndTime">Preferred End Time</label>
                            <select class="form-control" id="preferredEndTime" name="preferredEndTime">
                                <option value="12">Before Noon (12:00)</option>
                                <option value="17" selected>End of Business (17:00)</option>
                                <option value="20">Evening (20:00)</option>
                                <option value="24">Any time (24:00)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <p class="info-text">What time of day works best for you?</p>
                
                <!-- Number of Suggestions -->
                <div class="form-group">
                    <label for="maxSuggestions">Number of Suggestions</label>
                    <select class="form-control" id="maxSuggestions" name="maxSuggestions">
                        <option value="3">Show 3 options</option>
                        <option value="5" selected>Show 5 options</option>
                        <option value="10">Show 10 options</option>
                    </select>
                    <p class="info-text">How many time slot options do you want to see?</p>
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg btn-block" 
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; margin-top: 20px;">
                    Find Available Time Slots
                </button>
            </form>
        </div>
        
        <div class="loading-spinner">
            <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem; border-width: 0.3em;">
                <span class="sr-only">Analyzing your schedule...</span>
            </div>
            <p style="color: white; margin-top: 20px; font-size: 16px;">
                Analyzing your calendar...
            </p>
        </div>
        
        <div id="suggestionsResults" style="display: none;">
            <div style="background: white; padding: 25px; border-radius: 10px;">
                <h3 style="color: #2c3e50; margin-bottom: 20px;">
                    Available Time Slots
                </h3>
                <div id="suggestionsList"></div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Handle form submission
            $('#smartSuggestionsForm').on('submit', function(e) {
                e.preventDefault();
                findSmartSuggestions();
            });
        });

        /**
         * Main function to find available time slots
         * Collects form data and sends to backend
         */
        function findSmartSuggestions() {
            // Collect all form data
            const formData = {
                eventName: $('#eventName').val(),
                duration: parseFloat($('#duration').val()),
                searchRange: $('#searchRange').val(),
                preferredStartTime: parseInt($('#preferredStartTime').val()),
                preferredEndTime: parseInt($('#preferredEndTime').val()),
                maxSuggestions: parseInt($('#maxSuggestions').val())
            };

            // Show loading spinner, hide other sections
            $('.suggestions-form').hide();
            $('.loading-spinner').show();
            $('#suggestionsResults').hide();

            // Send request to backend
            $.ajax({
                url: 'smartSuggestions.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                dataType: 'json',
                success: function(response) {
                    console.log('Success! Received response:', response);
                    console.log('Response type:', typeof response);
                    console.log('Is array?', Array.isArray(response));
                    
                    // Check if response has error property
                    if (response && response.error) {
                        alert('Error from server: ' + response.message + '\nFile: ' + response.file + '\nLine: ' + response.line);
                        $('.loading-spinner').hide();
                        $('.suggestions-form').show();
                        return;
                    }
                    
                    displaySuggestions(response, formData);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    console.error('Status:', status);
                    console.error('Status Code:', xhr.status);
                    console.error('Response Text:', xhr.responseText);
                    console.error('Response Type:', xhr.getResponseHeader('Content-Type'));
                    
                    let errorMsg = 'Error finding suggestions.\n\n';
                    errorMsg += 'Status: ' + xhr.status + ' ' + status + '\n';
                    errorMsg += 'Response: ' + xhr.responseText.substring(0, 500);
                    
                    alert(errorMsg);
                    // Show form again
                    $('.loading-spinner').hide();
                    $('.suggestions-form').show();
                }
            });
        }

        /**
         * Display the suggested time slots
         * @param {Array} suggestions - Array of available time slots
         * @param {Object} formData - Original form data for context
         */
        function displaySuggestions(suggestions, formData) {
            $('.loading-spinner').hide();
            $('#suggestionsResults').show();

            const suggestionsList = $('#suggestionsList');
            suggestionsList.empty();

            // Show message if no slots found
            if (suggestions.length === 0) {
                suggestionsList.html(`
                    <div class="alert alert-warning">
                        <strong>No available time slots found</strong><br>
                        Try adjusting your search criteria (different time range or preferred hours).
                    </div>
                    <button class="btn btn-primary" onclick="location.reload()">
                        Try Again
                    </button>
                `);
                return;
            }

            // Display each suggestion as a card
            suggestions.forEach(function(suggestion, index) {
                const card = createSuggestionCard(suggestion, index + 1, formData);
                suggestionsList.append(card);
            });

            // Add button to search again
            suggestionsList.append(`
                <div style="text-align: center; margin-top: 20px;">
                    <button class="btn btn-default" onclick="location.reload()">
                        Search Again
                    </button>
                </div>
            `);
        }

        /**
         * Create a suggestion card HTML element
         * @param {Object} suggestion - Time slot data
         * @param {Number} index - Card number
         * @param {Object} formData - Form data for creating event
         */
        function createSuggestionCard(suggestion, index, formData) {
            // Format the dates nicely
            const startDate = new Date(suggestion.start_datetime);
            const endDate = new Date(suggestion.end_datetime);
            
            const dayName = startDate.toLocaleDateString('en-US', { weekday: 'long' });
            const dateStr = startDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            const startTime = startDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
            const endTime = endDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

            // Determine quality badge
            let qualityClass = 'quality-excellent';
            let qualityText = 'Excellent';
            if (suggestion.quality === 'good') {
                qualityClass = 'quality-good';
                qualityText = 'Good';
            } else if (suggestion.quality === 'fair') {
                qualityClass = 'quality-fair';
                qualityText = 'Fair';
            }

            // Create the card
            return $(`
                <div class="suggestion-card" onclick="createEventFromSlot('${suggestion.start_datetime}', '${suggestion.end_datetime}', '${formData.eventName}', this)">
                    <span class="quality-badge ${qualityClass}">${qualityText}</span>
                    <h4 style="color: #2c3e50; margin-top: 0;">
                        Option ${index}: ${dayName}
                    </h4>
                    <div class="time-display">
                        ${dateStr}<br>
                        ${startTime} - ${endTime}
                    </div>
                    <div class="slot-details">
                        ${suggestion.reason}
                    </div>
                    <div style="margin-top: 15px; text-align: right;">
                        <button class="btn btn-success btn-sm" type="button">
                            Select This Slot
                        </button>
                    </div>
                </div>
            `);
        }

        /**
         * Create event directly from selected time slot
         * @param {String} startDateTime - Start date/time
         * @param {String} endDateTime - End date/time
         * @param {String} eventName - Event name
         * @param {HTMLElement} cardElement - The card that was clicked
         */
        function createEventFromSlot(startDateTime, endDateTime, eventName, cardElement) {
            // Parse the datetime strings
            const start = new Date(startDateTime);
            const end = new Date(endDateTime);
            
            // Format for the database
            const startDate = start.toISOString().split('T')[0];
            const startTime = start.toTimeString().slice(0, 5);
            const endDate = end.toISOString().split('T')[0];
            const endTime = end.toTimeString().slice(0, 5);
            
            // Disable the card and show loading
            $(cardElement).css('opacity', '0.6').css('pointer-events', 'none');
            $(cardElement).find('.btn-success').text('Creating...').prop('disabled', true);
            
            // Create the event via AJAX
            $.ajax({
                url: 'createEvent.php',
                method: 'POST',
                data: {
                    title: eventName,
                    start_date: startDate,
                    start_time: startTime,
                    end_date: endDate,
                    end_time: endTime,
                    event_color: '#3788d8' // Default color
                },
                success: function(response) {
                    console.log('Create event response:', response);
                    
                    // Check if response indicates success
                    if (response.success) {
                        // Show success message
                        $(cardElement).find('.btn-success')
                            .removeClass('btn-success')
                            .addClass('btn-primary')
                            .text('Event Created!');
                        
                        // Redirect to calendar after 1 second
                        setTimeout(function() {
                            window.location.href = 'index.php';
                        }, 1000);
                    } else {
                        // Show error message from server
                        alert('Error: ' + (response.message || 'Unknown error'));
                        
                        // Re-enable the card
                        $(cardElement).css('opacity', '1').css('pointer-events', 'auto');
                        $(cardElement).find('button').text('Select This Slot').prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error creating event:', error);
                    console.error('Response:', xhr.responseText);
                    alert('Error creating event: ' + xhr.responseText);
                    
                    // Re-enable the card
                    $(cardElement).css('opacity', '1').css('pointer-events', 'auto');
                    $(cardElement).find('button').text('Select This Slot').prop('disabled', false);
                }
            });
        }
    </script>
</body>
</html>
