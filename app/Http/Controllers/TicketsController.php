<?php

namespace App\Http\Controllers;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Http\Requests\Tickets\CreateTicketRequest;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get Priority and status filter options
        $priorities = TicketPriority::toSelectArray();
        $statuses = TicketStatus::toSelectArray();

        // Get default 10 items if per_page parameter is empty
        $perPage = $request->input('per_page') ?? 10;

        // Filters query params
        $filters = $request->only([
            'searchInput', 'status', 'priority', 'date'
        ]);

        // Initiate query builder
        if ($filters['searchInput'] ?? false) {
            // If search input is provided, use search functionality
            $query = Ticket::search($filters['searchInput'])->query(function ($builder) {
                $builder->join('users', 'tickets.user_id', '=', 'users.id')->with('user');
            });
        } else {
            // If no search input, simply eager load user relationship
            $query = Ticket::with('user');
        }

        // Apply priority filter if provided
        if ($filters['priority'] ?? false) {
            $query->where('priority', $filters['priority']);
        }

        // Apply status filter if provided
        if ($filters['status'] ?? false) {
            $query->where('status', $filters['status']);
        }

        // Apply date range filter if provided
        if ($filters['date'] ?? false) {
            // Extract start and end dates from the date filter array
            $startDate = $filters['date'][0];
            $endDate = $filters['date'][1];
            // Filter records where created_at falls within the specified date range
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Paginate the results with the specified query parameters
        $tickets = $query->paginate($perPage)->withQueryString();

        return inertia('Tickets/Index', [
            'tickets' => $tickets,
            'priorities' => $priorities,
            'statuses' => $statuses,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $priorities = TicketPriority::toSelectArray();
        $statuses = TicketStatus::toSelectArray();

        return inertia('Tickets/CreateTicket', [
            'priorities' => $priorities,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTicketRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = auth()->id();

        Ticket::create($data);

        return redirect()->route('tickets.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ticket = Ticket::with('user', 'responses')->findOrFail($id);
        return inertia('Tickets/Show', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();
        return redirect()->route('tickets.index');
    }
}
