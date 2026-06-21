<?php

namespace App\Http\Controllers;

use App\Services\SessionService;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function __construct(
        protected SessionService $sessionService
    ) {}

    public function active()
    {
        $sessions = $this->sessionService->getActiveSessions(auth()->user());

        return view('account.sessions', [
            'sessions' => $sessions,
        ]);
    }

    public function terminate(Request $request, int $sessionId)
    {
        $success = $this->sessionService->terminateSession($sessionId, auth()->user());

        if (!$success) {
            return back()->withErrors('Session not found.');
        }

        return back()->with('message', 'Session terminated successfully.');
    }
}
