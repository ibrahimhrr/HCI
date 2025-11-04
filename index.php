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
   // Global function to add new event to calendar in real-time
   window.addEventToCalendar = function(eventData) {
       $('#calendar').fullCalendar('renderEvent', eventData, true);
   };
   
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
               
               // Set the color
               $('#editEventColor').val(calEvent.color || '#3788d8');
               updateEditColorPreview();
               
               // Show edit modal
               $('#editEventModal').modal('show');
           },
           
           // Enable drag and drop for rescheduling
           editable: true,
           droppable: true,
           
           // Event drag and drop functionality
           eventDrop: function(event, delta, revertFunc) {
               updateEventDateTime(event, revertFunc);
           },
           
           // Event resize functionality
           eventResize: function(event, delta, revertFunc) {
               updateEventDateTime(event, revertFunc);
           },
           
           // Double-click to create event
           dayClick: function(date, jsEvent, view) {
               if (jsEvent.detail === 2) { // Double click
                   var dateStr = date.format('YYYY-MM-DD');
                   var timeStr = date.format('HH:mm');
                   
                   // Pre-fill create form
                   window.location.href = `createEventForm.php?start_date=${dateStr}&start_time=${timeStr}&end_date=${dateStr}&end_time=${moment(date).add(1, 'hour').format('HH:mm')}`;
               }
           },
           
           // Enhanced event rendering with icons
           eventRender: function(event, element) {
               // Add icons based on event title keywords
               var icon = getEventIcon(event.title);
               var originalTitle = element.find('.fc-title').text();
               element.find('.fc-title').html(icon + ' ' + originalTitle);
               
               // Add hover tooltip
               element.attr('title', `${event.title}\n${moment(event.start).format('MMM DD, YYYY - HH:mm')} to ${moment(event.end).format('HH:mm')}`);
               
               // Add category-based styling
               var category = getEventCategory(event.title);
               element.addClass('event-category-' + category);
               
               // Add right-click context menu for quick actions
               element.on('contextmenu', function(e) {
                   e.preventDefault();
                   showEventContextMenu(e, event);
               });
               
               return element;
           },
           header:
           {
           left: 'month, agendaWeek, agendaDay, list, createEventBtn, smartSuggestBtn',
           center: 'title',
           right: 'prev, today, next'
           },
           
           customButtons: {
               createEventBtn: {
                   text: 'Create Event',
                   click: function() {
                       window.location.href = 'createEventForm.php';
                   }
               },
               smartSuggestBtn: {
                   text: 'Smart Suggestions', 
                   click: function() {
                       window.location.href = 'smartSuggestionsForm.php';
                   }
               }
           },
           
           // Add Bootstrap button classes to FullCalendar buttons
           viewRender: function(view, element) {
               // Style the custom buttons with Bootstrap classes and dark gradients
               $('.fc-createEventBtn-button').addClass('btn btn-primary').css({
                   'border-radius': '20px',
                   'padding': '6px 16px',
                   'margin-left': '8px',
                   'font-weight': '600',
                   'background': 'linear-gradient(135deg, #2c3e50 0%, #34495e 100%)',
                   'border': 'none',
                   'color': 'white'
               });
               $('.fc-smartSuggestBtn-button').addClass('btn btn-info').css({
                   'border-radius': '20px', 
                   'padding': '6px 16px',
                   'margin-left': '5px',
                   'font-weight': '600',
                   'background': 'linear-gradient(135deg, #2c3e50 0%, #34495e 100%)',
                   'border': 'none',
                   'color': 'white'
               });
               
               // Style other FullCalendar buttons
               $('.fc-button').not('.fc-createEventBtn-button, .fc-smartSuggestBtn-button').addClass('btn btn-default').css({
                   'border-radius': '18px',
                   'padding': '5px 12px',
                   'margin': '0 2px',
                   'font-weight': '500'
               });
               
               // Style today button with black gradient
               $('.fc-today-button').removeClass('btn-default').addClass('btn btn-warning').css({
                   'border-radius': '18px',
                   'background': 'linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%)',
                   'border': 'none',
                   'color': 'white'
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
          color: '<?php echo $result['color'] ?? '#3788d8'; ?>',
          textColor: '<?php echo (isset($result['color']) && $result['color'] === '#ffc107') ? 'black' : 'white'; ?>'
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
                endTime: $('#editEndTime').val(),
                color: $('#editEventColor').val()
            };
            
            // Show loading state
            $(this).find('button[type="submit"]').prop('disabled', true).html('🔄 Updating...');
            
            $.ajax({
                url: 'updateEvent.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#editEventModal').modal('hide');
                        
                        // Update the event immediately in the calendar
                        var eventToUpdate = $('#calendar').fullCalendar('clientEvents', formData.eventId)[0];
                        if (eventToUpdate) {
                            // Update event properties
                            eventToUpdate.title = formData.title;
                            eventToUpdate.start = moment(formData.startDate + 'T' + formData.startTime);
                            eventToUpdate.end = moment(formData.endDate + 'T' + formData.endTime);
                            eventToUpdate.color = formData.color;
                            eventToUpdate.textColor = (formData.color === '#ffc107') ? 'black' : 'white';
                            
                            // Update the event in the calendar
                            $('#calendar').fullCalendar('updateEvent', eventToUpdate);
                        }
                        
                        // Also refresh to ensure consistency
                        setTimeout(function() {
                            $('#calendar').fullCalendar('refetchEvents');
                        }, 100);
                        
                        showNotification('Event updated successfully!', 'success');
                    } else {
                        showNotification('Error updating event: ' + response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Update error:', xhr.responseText);
                    showNotification('Error updating event. Please try again.', 'error');
                },
                complete: function() {
                    // Reset button state
                    $('#editEventForm button[type="submit"]').prop('disabled', false).html('💾 Update Event');
                }
            });
        });
        
        // Color preview function for edit modal
        function updateEditColorPreview() {
            const selectedColor = $('#editEventColor').val();
            $('#editColorPreview').css('background-color', selectedColor);
        }
        
        // Update color preview when selection changes
        $('#editEventColor').on('change', updateEditColorPreview);
        
        // Handle delete event
        $('#deleteEventBtn').on('click', function() {
            var eventTitle = $('#editEventTitle').val();
            var eventId = $('#editEventId').val();
            
            // Enhanced confirmation dialog
            var confirmMessage = `Are you sure you want to delete "${eventTitle}"?\n\nThis action cannot be undone.`;
            
            if (confirm(confirmMessage)) {
                // Show loading state
                $('#deleteEventBtn').prop('disabled', true).html('🔄 Deleting...');
                
                $.ajax({
                    url: 'deleteEvent.php',
                    method: 'POST',
                    data: { eventId: eventId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#editEventModal').modal('hide');
                            
                            // Immediately remove the event from calendar view
                            $('#calendar').fullCalendar('removeEvents', eventId);
                            
                            // Also refresh to ensure consistency
                            setTimeout(function() {
                                $('#calendar').fullCalendar('refetchEvents');
                            }, 100);
                            
                            showNotification('Event deleted successfully!', 'success');
                        } else {
                            showNotification('Error deleting event: ' + response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Delete error:', xhr.responseText);
                        showNotification('Error deleting event. Please try again.', 'error');
                    },
                    complete: function() {
                        // Reset button state
                        $('#deleteEventBtn').prop('disabled', false).html('🗑️ Delete Event');
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
        
        // Helper function to get event icons
        function getEventIcon(title) {
            var titleLower = title.toLowerCase();
            if (titleLower.includes('meeting') || titleLower.includes('call')) return '💼';
            if (titleLower.includes('lunch') || titleLower.includes('dinner')) return '🍽️';
            if (titleLower.includes('workout') || titleLower.includes('gym')) return '💪';
            if (titleLower.includes('birthday') || titleLower.includes('party')) return '🎉';
            if (titleLower.includes('travel') || titleLower.includes('flight')) return '✈️';
            if (titleLower.includes('study') || titleLower.includes('class')) return '📚';
            if (titleLower.includes('doctor') || titleLower.includes('appointment')) return '🏥';
            if (titleLower.includes('shopping')) return '🛍️';
            if (titleLower.includes('coffee')) return '☕';
            if (titleLower.includes('presentation')) return '📊';
            return '📅'; // Default icon
        }
        
        // Helper function to categorize events
        function getEventCategory(title) {
            var titleLower = title.toLowerCase();
            if (titleLower.includes('meeting') || titleLower.includes('work') || titleLower.includes('call')) return 'work';
            if (titleLower.includes('lunch') || titleLower.includes('dinner') || titleLower.includes('personal')) return 'personal';
            if (titleLower.includes('workout') || titleLower.includes('gym') || titleLower.includes('fitness')) return 'fitness';
            if (titleLower.includes('study') || titleLower.includes('class') || titleLower.includes('course')) return 'education';
            if (titleLower.includes('party') || titleLower.includes('social') || titleLower.includes('celebration')) return 'social';
            if (titleLower.includes('travel') || titleLower.includes('vacation')) return 'travel';
            return 'general';
        }
        
        // Update event date/time via AJAX when dragged or resized
        function updateEventDateTime(event, revertFunc) {
            // Show loading indicator on the event
            var eventElement = $(`[data-event-id="${event.id}"]`);
            eventElement.css('opacity', '0.7').prepend('<span class="loading-indicator" style="position: absolute; top: 2px; right: 2px; font-size: 10px;">🔄</span>');
            
            $.ajax({
                url: 'updateEvent.php',
                method: 'POST',
                data: {
                    eventId: event.id,
                    title: event.title,
                    startDate: moment(event.start).format('YYYY-MM-DD'),
                    startTime: moment(event.start).format('HH:mm'),
                    endDate: moment(event.end).format('YYYY-MM-DD'),
                    endTime: moment(event.end).format('HH:mm'),
                    color: event.color || '#3788d8'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Remove loading indicator and restore opacity
                        eventElement.css('opacity', '1').find('.loading-indicator').remove();
                        showNotification('Event updated successfully!', 'success');
                    } else {
                        // Revert the change and show error
                        eventElement.css('opacity', '1').find('.loading-indicator').remove();
                        showNotification('Error updating event: ' + response.message, 'error');
                        revertFunc();
                    }
                },
                error: function(xhr, status, error) {
                    // Revert the change and show error
                    eventElement.css('opacity', '1').find('.loading-indicator').remove();
                    console.log('Update error:', xhr.responseText);
                    showNotification('Error updating event. Please try again.', 'error');
                    revertFunc();
                }
            });
        }
        
        // Show notification function
        function showNotification(message, type) {
            var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            var icon = type === 'success' ? '✅' : '❌';
            var notification = $(`
                <div class="alert ${alertClass} notification" style="position: fixed; top: 20px; right: 20px; z-index: 9999; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); max-width: 300px;">
                    <strong>${icon} ${message}</strong>
                </div>
            `);
            
            $('body').append(notification);
            
            setTimeout(function() {
                notification.fadeOut(function() {
                    notification.remove();
                });
            }, 3000);
        }
        
        // Show context menu for events
        function showEventContextMenu(e, event) {
            // Remove existing context menu
            $('.event-context-menu').remove();
            
            var contextMenu = $(`
                <div class="event-context-menu" style="
                    position: fixed;
                    top: ${e.clientY}px;
                    left: ${e.clientX}px;
                    background: white;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    z-index: 10000;
                    min-width: 150px;
                ">
                    <div class="context-menu-item" data-action="edit" style="padding: 10px 15px; cursor: pointer; border-bottom: 1px solid #eee;">
                        ✏️ Edit Event
                    </div>
                    <div class="context-menu-item" data-action="duplicate" style="padding: 10px 15px; cursor: pointer; border-bottom: 1px solid #eee;">
                        📋 Duplicate Event
                    </div>
                    <div class="context-menu-item" data-action="delete" style="padding: 10px 15px; cursor: pointer; color: #dc3545;">
                        🗑️ Delete Event
                    </div>
                </div>
            `);
            
            // Add hover effects
            contextMenu.find('.context-menu-item').hover(
                function() { $(this).css('background-color', '#f8f9fa'); },
                function() { $(this).css('background-color', 'white'); }
            );
            
            // Handle menu item clicks
            contextMenu.find('.context-menu-item').on('click', function() {
                var action = $(this).data('action');
                
                switch(action) {
                    case 'edit':
                        // Trigger existing edit functionality
                        $('#calendar').fullCalendar('clientEvents', event.id)[0] && 
                        $('#calendar').trigger('eventClick', [event, e, $('#calendar').fullCalendar('getView')]);
                        break;
                        
                    case 'duplicate':
                        duplicateEvent(event);
                        break;
                        
                    case 'delete':
                        quickDeleteEvent(event);
                        break;
                }
                
                $('.event-context-menu').remove();
            });
            
            $('body').append(contextMenu);
            
            // Remove context menu when clicking elsewhere
            $(document).one('click', function() {
                $('.event-context-menu').remove();
            });
        }
        
        // Quick delete without opening modal
        function quickDeleteEvent(event) {
            var confirmMessage = `Are you sure you want to delete "${event.title}"?\n\nThis action cannot be undone.`;
            
            if (confirm(confirmMessage)) {
                // Immediately hide the event with visual feedback
                var eventElement = $(`[data-event-id="${event.id}"]`);
                eventElement.fadeOut(300);
                
                $.ajax({
                    url: 'deleteEvent.php',
                    method: 'POST',
                    data: { eventId: event.id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Remove the event from calendar
                            $('#calendar').fullCalendar('removeEvents', event.id);
                            showNotification('Event deleted successfully!', 'success');
                        } else {
                            // Restore the event if deletion failed
                            eventElement.fadeIn(300);
                            showNotification('Error deleting event: ' + response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        // Restore the event if deletion failed
                        eventElement.fadeIn(300);
                        console.log('Quick delete error:', xhr.responseText);
                        showNotification('Error deleting event. Please try again.', 'error');
                    }
                });
            }
        }
        
        // Duplicate event functionality
        function duplicateEvent(event) {
            var newStartDate = moment(event.start).add(1, 'day');
            var newEndDate = moment(event.end).add(1, 'day');
            
            window.location.href = `createEventForm.php?title=${encodeURIComponent(event.title + ' (Copy)')}&start_date=${newStartDate.format('YYYY-MM-DD')}&start_time=${newStartDate.format('HH:mm')}&end_date=${newEndDate.format('YYYY-MM-DD')}&end_time=${newEndDate.format('HH:mm')}`;
        }
        
        // Toggle between different calendar views
        window.toggleView = function() {
            var currentView = $('#calendar').fullCalendar('getView').name;
            var nextView;
            
            switch(currentView) {
                case 'month':
                    nextView = 'agendaWeek';
                    break;
                case 'agendaWeek':
                    nextView = 'agendaDay';
                    break;
                case 'agendaDay':
                    nextView = 'listWeek';
                    break;
                case 'listWeek':
                    nextView = 'month';
                    break;
                default:
                    nextView = 'month';
            }
            
            $('#calendar').fullCalendar('changeView', nextView);
            showNotification(`Switched to ${nextView.replace('agenda', '').replace('Week', ' Week').replace('Day', ' Day')} view`, 'success');
        }
        
        // Show bulk delete modal
        window.showBulkDeleteModal = function() {
            $('#bulkDeleteModal').modal('show');
            loadEventsForBulkDelete();
        }
        
        // Load events for bulk delete
        function loadEventsForBulkDelete() {
            $.ajax({
                url: 'getEvents.php',
                method: 'GET',
                dataType: 'json',
                success: function(events) {
                    var eventsList = $('#eventsList');
                    eventsList.empty();
                    
                    if (events.length === 0) {
                        eventsList.html('<p class="text-center text-muted">No events found</p>');
                        return;
                    }
                    
                    events.forEach(function(event) {
                        var eventHtml = `
                            <div class="event-item" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin-bottom: 10px; background: white;">
                                <div class="row">
                                    <div class="col-md-1">
                                        <input type="checkbox" class="event-checkbox" value="${event.id}" style="transform: scale(1.2);">
                                    </div>
                                    <div class="col-md-8">
                                        <h5 style="margin: 0; font-weight: 600;">${getEventIcon(event.title)} ${event.title}</h5>
                                        <small class="text-muted">
                                            📅 ${moment(event.start_date).format('MMM DD, YYYY')} 
                                            🕒 ${event.start_time || '00:00'} - ${event.end_time || '23:59'}
                                        </small>
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <button class="btn btn-sm btn-outline-primary" onclick="editEventFromList('${event.id}')" style="border-radius: 15px; margin-right: 5px;">
                                            ✏️ Edit
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteEventFromList('${event.id}', '${event.title.replace(/'/g, "\\'")}')" style="border-radius: 15px;">
                                            🗑️ Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                        eventsList.append(eventHtml);
                    });
                    
                    // Add select all checkbox
                    var selectAllHtml = `
                        <div style="border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 15px;">
                            <label style="font-weight: 600;">
                                <input type="checkbox" id="selectAllEvents" style="transform: scale(1.2); margin-right: 10px;">
                                Select All Events (${events.length} total)
                            </label>
                        </div>
                    `;
                    eventsList.prepend(selectAllHtml);
                    
                    // Handle select all
                    $('#selectAllEvents').on('change', function() {
                        $('.event-checkbox').prop('checked', $(this).is(':checked'));
                    });
                },
                error: function() {
                    $('#eventsList').html('<p class="text-center text-danger">Error loading events</p>');
                }
            });
        }
        
        // Handle bulk delete
        $('#bulkDeleteBtn').on('click', function() {
            var selectedEvents = $('.event-checkbox:checked');
            
            if (selectedEvents.length === 0) {
                alert('Please select at least one event to delete');
                return;
            }
            
            var confirmMessage = `Are you sure you want to delete ${selectedEvents.length} event(s)?\n\nThis action cannot be undone.`;
            
            if (confirm(confirmMessage)) {
                var eventIds = [];
                selectedEvents.each(function() {
                    eventIds.push($(this).val());
                });
                
                // Show loading state and fade out selected events
                $('#bulkDeleteBtn').prop('disabled', true).html('🔄 Deleting...');
                selectedEvents.closest('.event-item').fadeOut(300);
                
                $.ajax({
                    url: 'bulkDeleteEvents.php',
                    method: 'POST',
                    data: { eventIds: eventIds },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#bulkDeleteModal').modal('hide');
                            
                            // Remove events from calendar immediately
                            eventIds.forEach(function(id) {
                                $('#calendar').fullCalendar('removeEvents', id);
                            });
                            
                            // Refresh to ensure consistency
                            setTimeout(function() {
                                $('#calendar').fullCalendar('refetchEvents');
                            }, 100);
                            
                            showNotification(`${response.deletedCount} event(s) deleted successfully!`, 'success');
                        } else {
                            // Restore the events if deletion failed
                            selectedEvents.closest('.event-item').fadeIn(300);
                            showNotification('Error deleting events: ' + response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        // Restore the events if deletion failed
                        selectedEvents.closest('.event-item').fadeIn(300);
                        console.log('AJAX error:', xhr.responseText);
                        showNotification('Error deleting events. Please try again.', 'error');
                    },
                    complete: function() {
                        $('#bulkDeleteBtn').prop('disabled', false).html('🗑️ Delete Selected');
                    }
                });
            }
        });
        
        // Edit event from list
        window.editEventFromList = function(eventId) {
            $('#bulkDeleteModal').modal('hide');
            
            // Find and trigger click on the calendar event
            var events = $('#calendar').fullCalendar('clientEvents');
            var targetEvent = events.find(e => e.id == eventId);
            
            if (targetEvent) {
                // Populate edit modal with event data
                $('#editEventId').val(targetEvent.id);
                $('#editEventTitle').val(targetEvent.title);
                
                var startMoment = moment(targetEvent.start);
                var endMoment = moment(targetEvent.end);
                
                $('#editStartDate').val(startMoment.format('YYYY-MM-DD'));
                $('#editStartTime').val(startMoment.format('HH:mm'));
                $('#editEndDate').val(endMoment.format('YYYY-MM-DD'));
                $('#editEndTime').val(endMoment.format('HH:mm'));
                
                $('#editEventModal').modal('show');
            }
        }
        
        // Delete event from list
        window.deleteEventFromList = function(eventId, eventTitle) {
            quickDeleteEvent({id: eventId, title: eventTitle});
            
            // Refresh the list
            setTimeout(function() {
                loadEventsForBulkDelete();
            }, 500);
        }
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
            <div class="modal-header" style="background: #2c3e50; color: white;">
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
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="editEventColor">Event Color <span class="color-preview" id="editColorPreview" style="width: 20px; height: 20px; border-radius: 4px; display: inline-block; margin-left: 10px; border: 2px solid #ddd; vertical-align: middle;"></span></label>
                                <select class="form-control" id="editEventColor" name="color" style="border-radius: 8px; padding: 12px;">
                                    <option value="#3788d8" style="background-color: #3788d8; color: white;">🔵 Blue </option>
                                    <option value="#28a745" style="background-color: #28a745; color: white;">🟢 Green </option>
                                    <option value="#dc3545" style="background-color: #dc3545; color: white;">🔴 Red </option>
                                    <option value="#ffc107" style="background-color: #ffc107; color: black;">🟡 Yellow </option>
                                    <option value="#6f42c1" style="background-color: #6f42c1; color: white;">🟣 Purple </option>
                                    <option value="#fd7e14" style="background-color: #fd7e14; color: white;">🟠 Orange</option>
                                    <option value="#20c997" style="background-color: #20c997; color: white;">🔵 Teal </option>
                                    <option value="#e83e8c" style="background-color: #e83e8c; color: white;">🩷 Pink </option>
                                    <option value="#6c757d" style="background-color: #6c757d; color: white;">⚫ Black</option>
                                </select>
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

<!-- Bulk Delete/Manage Events Modal -->
<div class="modal fade" id="bulkDeleteModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: #dc3545; color: white;">
                <button type="button" class="close" data-dismiss="modal" style="color: white;">&times;</button>
                <h4 class="modal-title">🗑️ Manage Events</h4>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <div id="eventsList" style="max-height: 400px; overflow-y: auto;">
                    <!-- Events will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 20px;">
                    Cancel
                </button>
                <button type="button" class="btn btn-danger" id="bulkDeleteBtn" style="border-radius: 20px;">
                    🗑️ Delete Selected
                </button>
            </div>
        </div>
    </div>
</div>
