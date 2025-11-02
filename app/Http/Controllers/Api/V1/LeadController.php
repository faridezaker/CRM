<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lead\CheckContact;
use App\Http\Resources\LeadResource;
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
     * @OA\Get(
     *     path="/api/v1/leads",
     *     tags={"Leads"},
     *     summary="Get list of leads assigned to the authenticated sales person",
     *     description="Retrieve a list of leads related to the currently authenticated sales person.",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="List of leads retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Return of leads related to sales person successfully"),
     *             @OA\Property(
     *                 property="lead",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=17),
     *                     @OA\Property(property="status", type="string", example="active"),
     *                     @OA\Property(property="pipeline", type="string", example="Registered"),
     *                     @OA\Property(
     *                         property="contact",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=6),
     *                         @OA\Property(property="name", type="string", example="Verna Dibbert"),
     *                         @OA\Property(property="email", type="string", example="fay.hanna@example.net")
     *                     ),
     *                     @OA\Property(property="sales_person", type="string", example="Foster Ferry"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-02 10:44:51")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */

    public function index()
    {
        $data = $this->leadService->index();

        return response()->json([
            'lead' => LeadResource::collection($data),
            'message' => 'Return of leads related to sales person successfully',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CheckContact $request)
    {
        $result = $this->leadService->createLeadForContact($request->validated());

        if (! $result['status']) {
            return response()->json([
                'status' => false,
                'message' => $result['message'],
            ], $result['code']);
        }

        return response()->json([
            'lead' => new LeadResource($result['lead']),
            'message' => $result['message'],
        ], 201);

    }

}
