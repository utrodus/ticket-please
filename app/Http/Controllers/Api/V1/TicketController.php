<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(TicketFilter $filters)
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        try {
            $user = User::findOrFail($request->input('data.relationships.author.data.id'));
        } catch (ModelNotFoundException $e) {
            return $this->ok('User not found', [
                'error' => 'User with the specified ID does not exist.',
            ]);
        }

        return new TicketResource(Ticket::create($request->mappedAttributes()));
    }

    /**
     * Display the specified resource.
     */
    public function show($ticketId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);
            if ($this->include('author')) {
                return new TicketResource($ticket->load('author'));
            }
            return new TicketResource($ticket);
        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket cannot found', 404);
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, $ticketId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);
            $ticket->update($request->mappedAttributes());
            return new TicketResource($ticket);
        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket cannot found', 404);
        }
    }

    public function replace(ReplaceTicketRequest $request, $ticketId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);
            $ticket->update($request->mappedAttributes());
            return new TicketResource($ticket);
        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket cannot found', 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ticketId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);
            $ticket->delete();
            return $this->ok('Ticket deleted successfully');
        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket cannot found', 404);
        }
    }
}
