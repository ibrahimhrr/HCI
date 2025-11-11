# Smart Suggestions System - Documentation

## Overview
The Smart Suggestions system analyzes your calendar and intelligently finds available time slots that match your preferences. It helps you quickly schedule new events by suggesting the best available times.

## How It Works

### 1. User Input (smartSuggestionsForm.php)
The user provides:
- **Event Name**: What they want to schedule
- **Duration**: How long the event will last (30 min - 4 hours)
- **Search Range**: When to look for slots
  - Today only
  - Tomorrow only
  - This week (next 7 days)
  - Next week (days 8-14)
  - This month
  - Next month
- **Preferred Time Range**: What hours work best (e.g., 9 AM - 5 PM)
- **Number of Suggestions**: How many options to show (3-10)

### 2. Backend Processing (smartSuggestions.php)

#### Step 1: Calculate Date Range
```
Function: getSearchDateRange($range)
Purpose: Convert user's choice into actual start/end dates
Example: "this_week" becomes today + 7 days
```

#### Step 2: Get Existing Events
```
Function: getExistingEvents($connection, $startDate, $endDate)
Purpose: Fetch all events from database in the date range
Returns: Array of events with start and end times
```

#### Step 3: Generate Potential Slots
```
Function: generateTimeSlots($startDate, $endDate, $duration, $preferredStart, $preferredEnd)
Purpose: Create a list of all possible time slots

How it works:
- For each day in the range
- For each hour in the preferred time window
- Create a slot starting at that hour
- Make sure the slot (hour + duration) fits in the window
```

Example: If user wants 2-hour slots from 9 AM - 5 PM on Monday:
- 9:00 AM - 11:00 AM ✓
- 10:00 AM - 12:00 PM ✓
- 11:00 AM - 1:00 PM ✓
- ...
- 3:00 PM - 5:00 PM ✓ (last one that fits)

#### Step 4: Filter Out Conflicts
```
Function: filterAvailableSlots($potentialSlots, $existingEvents)
Purpose: Remove slots that overlap with existing events

Overlap Detection:
A slot conflicts if:
- Slot starts before event ends AND
- Slot ends after event starts

Visual Example:
Event:     |-------|
Slot 1:  |-----|        ✗ Conflicts (overlaps)
Slot 2:          |-----| ✓ No conflict (after event)
```

#### Step 5: Rank by Quality
```
Function: rankSlots($slots, $preferredStart, $preferredEnd)
Purpose: Rate each available slot by how good it is

Quality Ratings:
- Excellent: Within ideal time range (preferred start +1 to preferred end -2)
- Good: Within preferred time range but at edges
- Fair: Available but outside preferred hours

Scoring:
- Excellent = 100 points
- Good = 75 points
- Fair = 50 points
- Subtract days in the future (sooner is better)
```

### 3. Display Results
The frontend displays each suggestion as a card showing:
- Day and date
- Time range
- Quality badge
- Reason why it's a good slot
- Select button to create event

## File Structure

```
smartSuggestionsForm.php  - Frontend form and display
smartSuggestions.php       - Backend logic and database queries
createEventForm.php        - Where user goes after selecting a slot
```

## Data Flow

```
User fills form
    ↓
JavaScript sends JSON to backend
    ↓
Backend analyzes calendar
    ↓
Backend returns available slots
    ↓
JavaScript displays suggestions
    ↓
User clicks a slot
    ↓
Redirects to create event form with pre-filled data
```

## Key Concepts

### Time Overlap
Two events overlap if they share any moment in time.
```
Event A: 10:00 - 12:00
Event B: 11:00 - 13:00
These overlap from 11:00 - 12:00
```

### Quality Scoring
Better slots are:
1. Closer to the ideal time (middle of preferred range)
2. Sooner in the future (tomorrow is better than next week)
3. Within working hours (not too early/late)

### Search Ranges
- **Today**: Only slots remaining today
- **This Week**: Next 7 days from now
- **This Month**: From today until end of current month
- **Next Month**: All days in the following month

## Customization Options

### To Skip Weekends
In `generateTimeSlots()`, add after line getting $dayOfWeek:
```php
// Skip Saturdays (6) and Sundays (7)
if ($dayOfWeek >= 6) {
    $currentDate->modify('+1 day');
    continue;
}
```

### To Add Break Times
In `generateTimeSlots()`, skip lunch hours:
```php
// Skip lunch hour (12-13)
if ($hour == 12) {
    continue;
}
```

### To Change Slot Intervals
Currently slots are generated every hour. To do every 30 minutes:
```php
// Change the hour loop to include half-hours
for ($hour = $preferredStart; $hour < $preferredEnd; $hour++) {
    // Full hour
    // ... create slot at $hour:00 ...
    
    // Half hour
    // ... create slot at $hour:30 ...
}
```

## Troubleshooting

### No Suggestions Found
Possible reasons:
- Calendar is fully booked in that range
- Preferred time window is too narrow
- Event duration is too long

Solutions:
- Expand the search range
- Widen the preferred time window
- Reduce event duration

### Wrong Time Zone
If times are off, check your server's PHP timezone:
```php
// Add at top of smartSuggestions.php
date_default_timezone_set('America/New_York'); // Your timezone
```

### Slow Performance
If many events and long search range:
- Limit search range
- Add database index on start_date column
- Reduce number of suggestions

## Example Usage

User wants to schedule a 1-hour meeting this week during business hours:

1. **Input**:
   - Event: "Team Standup"
   - Duration: 1 hour
   - Range: This week
   - Time: 9 AM - 5 PM
   - Suggestions: 5

2. **Backend**:
   - Calculates: Today through +7 days
   - Gets existing events from database
   - Generates: ~56 potential slots (7 days × 8 hours)
   - Filters: Removes occupied slots
   - Ranks: Scores remaining slots
   - Returns: Top 5 suggestions

3. **Output**:
   - Option 1: Tomorrow at 10:00 AM ⭐ Excellent
   - Option 2: Wednesday at 2:00 PM ⭐ Excellent  
   - Option 3: Thursday at 9:00 AM 👍 Good
   - Option 4: Friday at 4:00 PM 👍 Good
   - Option 5: Monday at 3:00 PM ⭐ Excellent

4. **Selection**:
   - User clicks "Option 1"
   - Redirects to create event form
   - Form is pre-filled with that time
   - User confirms and saves

## Summary

The Smart Suggestions system makes scheduling easy by:
1. ✓ Checking your entire calendar automatically
2. ✓ Finding only truly available slots
3. ✓ Ranking slots by quality
4. ✓ Showing you the best options first
5. ✓ Pre-filling the event form when you select

It saves time and prevents double-booking!
