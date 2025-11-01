<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\LeadService;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    protected $leadService;
    public function __construct(LeadService $service)
    {
        $this->leadService = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->leadService->index();
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
}
