<?php

namespace App\Http\Controllers\Employee;

use App\Models\MstEvent;
use App\Models\MstTagEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    // Check authentication before accessing
    public function __construct()
    {
        if (session('user_type') !== 'Employee') {
            return redirect('/employee/login');
        }
    }

    // ============ EVENT CRUD ============

    /**
     * Display all events with tag count
     */
    public function index()
    {
        $events = DB::select('
            SELECT e.*, 
                   COUNT(t.tag_id) as tag_count
            FROM mst_events e
            LEFT JOIN mst_tag_events t ON e.event_id = t.event_id
            GROUP BY e.event_id, e.event_name, e.description, e.location, 
                     e.status, e.created_at, e.updated_at, e.created_by, e.updated_by
            ORDER BY e.event_id DESC
        ');

        return view('employee.events.index', compact('events'));
    }

    /**
     * Show form for creating new event
     */
    public function create()
    {
        return view('employee.events.create');
    }

    /**
     * Store new event in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|string|max:50|unique:mst_events',
            'event_name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'status' => 'required|in:Upcoming,Ongoing,Completed,Cancelled',
        ]);

        $validated['created_by'] = session('employee_id');
        $validated['updated_by'] = session('employee_id');
        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        MstEvent::create($validated);

        return redirect()->route('employee.events.index')
                       ->with('success', 'Event created successfully!');
    }

    /**
     * Show form for editing event
     */
    public function edit($id)
    {
        $event = DB::select(
            'SELECT * FROM mst_events WHERE event_id = ?',
            [$id]
        );

        if (empty($event)) {
            return redirect()->route('employee.events.index')
                           ->with('error', 'Event not found!');
        }

        return view('employee.events.edit', ['event' => $event[0]]);
    }

    /**
     * Update event in database
     */
    public function update(Request $request, $id)
    {
        $event = MstEvent::findOrFail($id);

        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'status' => 'required|in:Upcoming,Ongoing,Completed,Cancelled',
        ]);

        $validated['updated_by'] = session('employee_id');
        $validated['updated_at'] = now();

        $event->update($validated);

        return redirect()->route('employee.events.index')
                       ->with('success', 'Event updated successfully!');
    }

    /**
     * Delete event and all its tags
     */
    public function destroy($id)
    {
        $event = MstEvent::findOrFail($id);

        // Delete all tags first
        MstTagEvent::where('event_id', $id)->delete();

        // Delete event
        $event->delete();

        return redirect()->route('employee.events.index')
                       ->with('success', 'Event and all tags deleted successfully!');
    }

    // ============ TAG EVENT CRUD ============

    /**
     * Display all tags for an event
     */
    public function indexTag($eventId)
    {
        // Check if event exists
        $event = DB::select(
            'SELECT * FROM mst_events WHERE event_id = ?',
            [$eventId]
        );

        if (empty($event)) {
            return redirect()->route('employee.events.index')
                           ->with('error', 'Event not found!');
        }

        $tags = DB::select(
            'SELECT * FROM mst_tag_events WHERE event_id = ? ORDER BY tag_id DESC',
            [$eventId]
        );

        // Get all available tag codes for the select2 dropdown
        $availableTags = DB::select('
            SELECT DISTINCT tag_code FROM mst_tag_events
            WHERE tag_code IS NOT NULL
            ORDER BY tag_code
        ');

        return view('employee.events.index-tag', [
            'event' => $event[0],
            'tags' => $tags,
            'availableTags' => $availableTags
        ]);
    }

    /**
     * Show form for creating multiple tags at once (Select2 multiple)
     */
    public function createTag($eventId)
    {
        // Check if event exists
        $event = DB::select(
            'SELECT * FROM mst_events WHERE event_id = ?',
            [$eventId]
        );

        if (empty($event)) {
            return redirect()->route('employee.events.index')
                           ->with('error', 'Event not found!');
        }

        // Get all available tag codes
        $availableTags = DB::select('
            SELECT DISTINCT tag_code FROM mst_tag_events
            WHERE tag_code IS NOT NULL
            ORDER BY tag_code
        ');

        // Get already assigned tags
        $assignedTags = DB::select(
            'SELECT tag_code FROM mst_tag_events WHERE event_id = ?',
            [$eventId]
        );

        $assignedTagCodes = array_map(function($tag) {
            return $tag->tag_code;
        }, $assignedTags);

        return view('employee.events.create-tag', [
            'event' => $event[0],
            'availableTags' => $availableTags,
            'assignedTags' => $assignedTagCodes
        ]);
    }

    /**
     * Store multiple tags for event (handles Select2 multiple selection)
     */
    public function storeTag(Request $request, $eventId)
    {
        $validated = $request->validate([
            'tag_codes' => 'required|array|min:1',
            'tag_codes.*' => 'required|string|max:50',
        ]);

        // Get existing tags for this event
        $existingTags = DB::select(
            'SELECT tag_code FROM mst_tag_events WHERE event_id = ?',
            [$eventId]
        );

        $existingTagCodes = array_map(function($tag) {
            return $tag->tag_code;
        }, $existingTags);

        // Determine which tags to add and which to remove
        $tagsToAdd = array_diff($validated['tag_codes'], $existingTagCodes);
        $tagsToRemove = array_diff($existingTagCodes, $validated['tag_codes']);

        // Remove old tags
        if (!empty($tagsToRemove)) {
            DB::delete(
                'DELETE FROM mst_tag_events WHERE event_id = ? AND tag_code IN (' . 
                implode(',', array_fill(0, count($tagsToRemove), '?')) . ')',
                array_merge([$eventId], $tagsToRemove)
            );
        }

        // Add new tags
        foreach ($tagsToAdd as $tagCode) {
            $tagId = $eventId . '_' . $tagCode;

            MstTagEvent::create([
                'tag_id' => $tagId,
                'event_id' => $eventId,
                'tag_code' => $tagCode,
                'created_by' => session('employee_id'),
                'updated_by' => session('employee_id'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('employee.events.tag', $eventId)
                       ->with('success', count($tagsToAdd) . ' tag(s) added successfully!');
    }

    /**
     * Delete a specific tag from event
     */
    public function destroyTag($eventId, $tagId)
    {
        $tag = MstTagEvent::findOrFail($tagId);
        $tag->delete();

        return redirect()->route('employee.events.tag', $eventId)
                       ->with('success', 'Tag deleted successfully!');
    }
}
