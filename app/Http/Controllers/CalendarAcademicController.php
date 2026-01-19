<?php

namespace App\Http\Controllers;

use App\Models\CalendarAcademic;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CalendarAcademicController extends Controller
{
    public function index()
    {
        return view('calendar-academic.index');
    }

    public function getEvents()
    {
        $events = CalendarAcademic::all()->map(function ($event) {
            return [
                'id'    => $event->id,
                'title' => $event->title,
                'start' => Carbon::parse($event->start)->toIso8601String(),
                'end'   => Carbon::parse($event->end)->toIso8601String(),
                'extendedProps' => [
                    'detail' => $event->detail,
                    'category' => $event->category ?? 'Event'
                ],
                'backgroundColor' => $this->getColor($event->category),
                'borderColor'     => $this->getColor($event->category),
                'textColor'       => '#ffffff'
            ];
        });

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required',
            'end' => 'required',
            'category' => 'required'
        ]);

        $event = new CalendarAcademic();
        $event->title = $request->title;
        $event->start = Carbon::parse($request->start)->format('Y-m-d H:i:s');
        $event->end = Carbon::parse($request->end)->format('Y-m-d H:i:s');
        $event->category = $request->category;
        $event->detail = $request->detail;
        $event->created_by = auth('staff')->id();
        $event->save();

        return redirect()->back()->with('success', 'Event added successfully!');
    }

    public function update(Request $request, $id)
    {
        $event = CalendarAcademic::findOrFail($id);
        
        $event->update([
            'title' => $request->title,
            'start' => Carbon::parse($request->start)->format('Y-m-d H:i:s'),
            'end' => Carbon::parse($request->end)->format('Y-m-d H:i:s'),
            'category' => $request->category,
            'detail' => $request->detail,
        ]);

        return response()->json(['message' => 'Event updated successfully!']);
    }

    public function destroy($id)
    {
        $event = CalendarAcademic::findOrFail($id);
        $event->delete();
        return response()->json(['message' => 'Event deleted successfully!']);
    }

    private function getColor($category)
    {
        return match ($category) {
            'Exam'  => '#fdaf4b', // Red
            'Holiday'  => '#f3545d', // Orange
            'Event'  => '#1572e8', // Blue
            default  => '#01c293', // Theme Green
        };
    }
}