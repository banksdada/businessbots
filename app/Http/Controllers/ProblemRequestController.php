<?php

namespace App\Http\Controllers;

use App\Models\ProblemRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProblemRequestController extends Controller
{
    public function show(Request $request, ProblemRequest $problemRequest): View
    {
        // Clients only see requests from their own business.
        abort_unless(
            $request->user()->businesses()->whereKey($problemRequest->business_id)->exists(),
            404
        );

        return view('problems.show', ['problem' => $problemRequest]);
    }
}
