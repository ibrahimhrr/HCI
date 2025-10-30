<!DOCTYPE html>
<html>
<head>
    <title>Smart Event Suggestions - OnlyPlans</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="styles.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <style>
        .smart-suggestions-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        }
        
        .suggestion-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .confidence-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 20px;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }
        
        .confidence-high { background: #28a745; }
        .confidence-medium { background: #ffc107; }
        .confidence-low { background: #dc3545; }
        
        .time-display {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin: 10px 0;
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
    </style>
</head>
<body>
    <div class="smart-suggestions-container">
        <a href="index.php" class="back-button">← Back to Calendar</a>
        
        <h2 style="color: white; text-align: center; margin-bottom: 30px;">
            🤖 Smart Event Suggestions
        </h2>
        
        <div class="suggestions-form">
            <h3>Find the Perfect Time Slot</h3>
            <form id="smartSuggestionsForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="eventName">Event Name</label>
                            <input type="text" class="form-control" id="eventName" 
                                   placeholder="e.g., Team Meeting, Workout, Lunch with John" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="duration">Duration (minutes)</label>
                            <select class="form-control" id="duration">
                                <option value="30">30 minutes</option>
                                <option value="60" selected>1 hour</option>
                                <option value="90">1.5 hours</option>
                                <option value="120">2 hours</option>
                                <option value="180">3 hours</option>
                                <option value="240">4 hours</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="timeRange">Search Range</label>
                            <select class="form-control" id="timeRange">
                                <option value="day">Today</option>
                                <option value="week" selected>This Week</option>
                                <option value="month">This Month</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="preferredStartHour">Preferred Start Hour</label>
                            <select class="form-control" id="preferredStartHour">
                                <option value="6">6:00 AM</option>
                                <option value="7">7:00 AM</option>
                                <option value="8">8:00 AM</option>
                                <option value="9" selected>9:00 AM</option>
                                <option value="10">10:00 AM</option>
                                <option value="11">11:00 AM</option>
                                <option value="12">12:00 PM</option>
                                <option value="13">1:00 PM</option>
                                <option value="14">2:00 PM</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="preferredEndHour">Preferred End Hour</label>
                            <select class="form-control" id="preferredEndHour">
                                <option value="14">2:00 PM</option>
                                <option value="15">3:00 PM</option>
                                <option value="16">4:00 PM</option>
                                <option value="17" selected>5:00 PM</option>
                                <option value="18">6:00 PM</option>
                                <option value="19">7:00 PM</option>
                                <option value="20">8:00 PM</option>
                                <option value="21">9:00 PM</option>
                                <option value="22">10:00 PM</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg btn-block" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    🔍 Find Best Time Slots
                </button>
            </form>
        </div>
        
        <div class="loading-spinner">
            <div class="spinner-border text-light" role="status">
                <span class="sr-only">Analyzing your schedule...</span>
            </div>
            <p style="color: white; margin-top: 10px;">Analyzing your schedule...</p>
        </div>
        
        <div id="suggestionsResults" style="display: none;">
            <h3 style="color: white; text-align: center; margin-bottom: 20px;">
                ✨ Recommended Time Slots
            </h3>
            <div id="suggestionsList"></div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#smartSuggestionsForm').on('submit', function(e) {
                e.preventDefault();
                findSmartSuggestions();
            });
        });

        function findSmartSuggestions() {
            const formData = {
                eventName: $('#eventName').val(),
                duration: parseInt($('#duration').val()),
                timeRange: $('#timeRange').val(),
                preferredStartHour: parseInt($('#preferredStartHour').val()),
                preferredEndHour: parseInt($('#preferredEndHour').val())
            };

            // Show loading spinner
            $('.suggestions-form').hide();
            $('.loading-spinner').show();
            $('#suggestionsResults').hide();

            $.ajax({
                url: 'smartSuggestions.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                success: function(response) {
                    displaySuggestions(response, formData);
                },
                error: function() {
                    alert('Error finding suggestions. Please try again.');
                    $('.loading-spinner').hide();
                    $('.suggestions-form').show();
                }
            });
        }

        function displaySuggestions(suggestions, formData) {
            $('.loading-spinner').hide();
            $('#suggestionsResults').show();

            const suggestionsList = $('#suggestionsList');
            suggestionsList.empty();

            if (suggestions.length === 0) {
                suggestionsList.html(`
                    <div class="suggestion-card text-center">
                        <h4>No Available Time Slots Found</h4>
                        <p>Try expanding your search range or adjusting your preferred hours.</p>
                        <button class="btn btn-primary" onclick="$('.suggestions-form').show(); $('#suggestionsResults').hide();">
                            Try Again
                        </button>
                    </div>
                `);
                return;
            }

            suggestions.forEach((suggestion, index) => {
                const confidenceClass = getConfidenceClass(suggestion.confidence);
                const confidenceText = getConfidenceText(suggestion.confidence);
                
                const card = $(`
                    <div class="suggestion-card" style="position: relative;" onclick="selectTimeSlot('${suggestion.date}', '${suggestion.startTime}', '${suggestion.endTime}', '${formData.eventName}')">
                        <div class="confidence-badge ${confidenceClass}">${suggestion.confidence}% ${confidenceText}</div>
                        <div class="row">
                            <div class="col-md-8">
                                <h4 style="margin-top: 0;">${suggestion.dayOfWeek}, ${formatDate(suggestion.date)}</h4>
                                <div class="time-display">${formatTime(suggestion.startTime)} - ${formatTime(suggestion.endTime)}</div>
                                <p class="text-muted">Duration: ${formData.duration} minutes</p>
                            </div>
                            <div class="col-md-4 text-right">
                                <div style="margin-top: 20px;">
                                    <span class="label label-success">Available</span>
                                </div>
                            </div>
                        </div>
                        <div style="margin-top: 15px;">
                            <small class="text-muted">
                                ${getReasonText(suggestion, formData.eventName)}
                            </small>
                        </div>
                    </div>
                `);
                
                suggestionsList.append(card);
            });

            // Add a button to try again
            suggestionsList.append(`
                <div class="text-center" style="margin-top: 20px;">
                    <button class="btn btn-outline-light" onclick="$('.suggestions-form').show(); $('#suggestionsResults').hide();">
                        🔄 Try Different Parameters
                    </button>
                </div>
            `);
        }

        function selectTimeSlot(date, startTime, endTime, eventName) {
            if (confirm(`Create "${eventName}" on ${formatDate(date)} from ${formatTime(startTime)} to ${formatTime(endTime)}?`)) {
                // Redirect to create event form with pre-filled data
                const params = new URLSearchParams({
                    title: eventName,
                    start_date: date,
                    start_time: startTime,
                    end_date: date,
                    end_time: endTime,
                    from_suggestions: 'true'
                });
                
                window.location.href = `createEventForm.php?${params.toString()}`;
            }
        }

        function getConfidenceClass(confidence) {
            if (confidence >= 80) return 'confidence-high';
            if (confidence >= 60) return 'confidence-medium';
            return 'confidence-low';
        }

        function getConfidenceText(confidence) {
            if (confidence >= 80) return 'Excellent';
            if (confidence >= 60) return 'Good';
            return 'Fair';
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
        }

        function formatTime(timeString) {
            const [hours, minutes] = timeString.split(':');
            const hour12 = hours % 12 || 12;
            const ampm = hours >= 12 ? 'PM' : 'AM';
            return `${hour12}:${minutes} ${ampm}`;
        }

        function getReasonText(suggestion, eventName) {
            const reasons = [];
            
            if (suggestion.confidence >= 80) {
                reasons.push("Optimal time based on your schedule");
            }
            
            if (suggestion.startTime >= "09:00" && suggestion.startTime <= "11:00") {
                reasons.push("Good morning productivity hours");
            } else if (suggestion.startTime >= "14:00" && suggestion.startTime <= "16:00") {
                reasons.push("Good afternoon focus time");
            }
            
            if (suggestion.conflicts === 0) {
                reasons.push("No scheduling conflicts");
            }
            
            // Event-specific reasons
            const eventLower = eventName.toLowerCase();
            if (eventLower.includes('meeting') && suggestion.startTime >= "10:00" && suggestion.startTime <= "15:00") {
                reasons.push("Ideal time for meetings");
            } else if (eventLower.includes('lunch') && suggestion.startTime >= "12:00" && suggestion.startTime <= "13:00") {
                reasons.push("Perfect lunch time");
            } else if (eventLower.includes('workout') && (suggestion.startTime <= "08:00" || suggestion.startTime >= "17:00")) {
                reasons.push("Great time for fitness activities");
            }
            
            return reasons.length > 0 ? reasons.join(" • ") : "Available time slot";
        }
    </script>
</body>
</html>
