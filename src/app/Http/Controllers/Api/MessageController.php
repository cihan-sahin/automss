<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class MessageController extends Controller
{
    public function __construct(
        private MessageService $messageService
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/messages",
     *     summary="Get list of sent messages",
     *     description="Returns a list of all sent messages with their details including message IDs",
     *     operationId="getMessages",
     *     tags={"Messages"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="count", type="integer", example=5),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="phone_number", type="string", example="+905551111111"),
     *                     @OA\Property(property="content", type="string", example="Insider - Project"),
     *                     @OA\Property(property="message_id", type="string", example="67f2f8a8-ea58-4ed0-a6f9-ff217df4d849"),
     *                     @OA\Property(property="sent_at", type="string", format="date-time", example="2025-12-30T19:15:00+00:00"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-30T19:10:00+00:00")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $messages = $this->messageService->getSentMessages();

        return response()->json([
            'success' => true,
            'data' => $messages,
            'count' => count($messages)
        ]);
    }
}
