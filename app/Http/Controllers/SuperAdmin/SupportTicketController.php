<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }

    /**
     * Reply to a support ticket.
     */
    public function reply(Request $request, string $ticket)
    {
        //
    }

    /**
     * Assign a support ticket to an agent.
     */
    public function assign(Request $request, string $ticket)
    {
        //
    }

    /**
     * Resolve a support ticket.
     */
    public function resolve(Request $request, string $ticket)
    {
        //
    }

    /**
     * Close a support ticket.
     */
    public function close(Request $request, string $ticket)
    {
        //
    }
}