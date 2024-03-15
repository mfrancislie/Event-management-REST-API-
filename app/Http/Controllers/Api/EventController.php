<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
   use CanLoadRelationships;

   private array $relations = ['user', 'attendees', 'attendees.user'];

    public function __contruct(){
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index()
    {
        $query = $this->loadRelationships(Event::query());
        return EventResource::collection(
        // $this->shouldIncludeRelation('user'), this related to dd($include)
            $query->latest()->paginate()
        );
        // return EventResource::collection(Event::all()); without user
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $event =  Event::create([
            ...$request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time'
            ]),
            'user_id' => $request->user()->id
            // $request->user()->id this an actual user id
        ]);

        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
         $event->update(
          $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time'
            ])
        );
        
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return response(status: 204);
    }
}
