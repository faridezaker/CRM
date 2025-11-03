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
        $userId = auth()->user()->id;
        $data = $this->leadService->index($userId);

        return response()->json([
            'lead' => LeadResource::collection($data),
            'message' => 'Return of leads related to sales person successfully',
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/leads",
     *     tags={"Leads"},
     *     summary="Create new leads",
     *     description="Create new leads for contacts with optional marketing code",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"leads"},
     *             @OA\Property(
     *                 property="leads",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="contact_id", type="integer", example=1, description="ID of the contact"),
     *                     @OA\Property(property="marketing_code", type="string", example="4481820", description="Marketing code of the sales person (optional)")
     *                 ),
     *                 example={{
     *                     "contact_id": 1,
     *                     "marketing_code": "4481820"
     *                 }}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Lead created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lead created successfully"),
     *             @OA\Property(
     *                 property="lead",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=17),
     *                 @OA\Property(property="status", type="string", example="active"),
     *                 @OA\Property(property="pipeline", type="string", example="Registered"),
     *                 @OA\Property(
     *                     property="contact",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com")
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-03 10:44:51")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - validation failed or business logic error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Contact already has a lead")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The leads.0.contact_id field is required."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(CheckContact $request)
    {
        $result = $this->leadService->createLeadForContacts($request->validated());

        if (! $result['status']) {
            return response()->json([
                'status' => false,
                'message' => $result['message'],
            ], $result['code']);
        }

        return response()->json([
            'leads' =>  LeadResource::collection($result['leads']),
            'message' => $result['message'],
        ], 201);

    }

}
