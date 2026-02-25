<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\StoreTicketRequest;
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
        $model = [
            'title' => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'status' => $request->input('data.attributes.status'),
            'user_id' => $authorId,
        ];

        return new TicketResource(Ticket::create($model));
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
