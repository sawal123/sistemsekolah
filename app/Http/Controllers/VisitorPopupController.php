<?php

namespace App\Http\Controllers;

use App\Models\WebsitePopup;
use App\Services\VisitorPopupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisitorPopupController extends Controller
{
    public function store(Request $request, WebsitePopup $popup, VisitorPopupService $service): JsonResponse
    {
        abort_unless($popup->is_active, 404);

        return response()->json(['show' => $service->claim($popup, $request)])
            ->header('Cache-Control', 'no-store, private');
    }
}
