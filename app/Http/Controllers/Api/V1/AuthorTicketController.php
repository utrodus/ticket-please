<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class AuthorTicketController extends ApiController
{
    public function index($author_id, TicketFilter $filters)
    {
        return TicketResource::collection(Ticket::where('user_id', $author_id)->filter($filters)->paginate());
    }

    public function store($authorId, StoreTicketRequest $request)
    {
        return new TicketResource(Ticket::create($request->mappedAttributes()));
    }

    public function update(UpdateTicketRequest $request, $authorId, $ticketId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);
            if ($ticket->user_id == $authorId) {

                $ticket->update($request->mappedAttributes());
                return new TicketResource($ticket);
            }
        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket cannot be found', 404);
        }
    }
    public function replace(ReplaceTicketRequest $request, $authorId, $ticketId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);
            if ($ticket->user_id == $authorId) {
                $ticket->update($request->mappedAttributes());
                return new TicketResource($ticket);
            }
            // TODO: ticket doesn't belong to user
        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket cannot be found', 404);
        }
    }

    /**
     * 
     * Remove the specified resource from storage.
     */
    public function destroy($authorId, $ticketId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);

            if ($ticket->user_id == $authorId) {
                $ticket->delete();
                return $this->ok('Ticket deleted successfully');
            } else {
                return $this->error('Ticket cannot found', 404);
            }
        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket cannot found', 404);
        }
    }
}
