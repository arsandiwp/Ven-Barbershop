<?php

namespace App\Http\Controllers\Api;

use App\Models\ChatHistory;
use App\Services\OpenRouterService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    protected $openRouter;

    public function __construct(OpenRouterService $openRouter)
    {
        $this->openRouter = $openRouter;
    }

    public function chat2(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'model' => 'string|nullable',
            'session_id' => 'string|nullable',
            'expire_minutes' => 'integer|nullable'
        ]);

        $userLoggedIn = $request->_session['id'];
        $sessionId = $request->session_id;
        $history = [];
        // $expireMinutes = 120 ?? $request->expire_minutes;
        $expireMinutes = $request->expire_minutes;

        // return $expireMinutes;
        // dd($expireMinutes);

        if ($sessionId) {
            $historyResponse = $this->openRouter->getChatHistory($sessionId);
            if ($historyResponse['success']) {
                $history = $historyResponse['history'];
                $modelId = $request->model ?? $historyResponse['model_id'] ?? null;
            }
        } else {
            $modelId = $request->model;
        }

        return $this->openRouter->chat2(
            $request->message,
            $modelId,
            $history,
            $sessionId,
            $userLoggedIn,
            $expireMinutes
        );
    }

    public function chatBot(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'model' => 'string|nullable',
            'session_id' => 'string|nullable',
            'expire_minutes' => 'integer|nullable'
        ]);

        $userLoggedIn = null;
        $sessionId = null;
        $history = [];
        $expireMinutes = null;

        if ($sessionId) {
            $historyResponse = $this->openRouter->getChatHistory($sessionId);
            if ($historyResponse['success']) {
                $history = $historyResponse['history'];
                $modelId = $request->model ?? $historyResponse['model_id'] ?? null;
            }
        } else {
            $modelId = $request->model;
        }

        return $this->openRouter->chat2(
            $request->message,
            $modelId,
            $history,
            $sessionId,
            $userLoggedIn,
            $expireMinutes
        );
    }

    // jangan dihapus noted
    // public function chat(Request $request)
    // {
    //     $request->validate([
    //         'message' => 'required|string',
    //         'model' => 'string|nullable',
    //         'session_id' => 'string|nullable'
    //     ]);

    //     $userLoggedIn = $request->_session['id'];
    //     $sessionId = $request->session_id;
    //     $history = [];

    //     if ($sessionId) {
    //         $historyResponse = $this->openRouter->getChatHistory($sessionId);
    //         if ($historyResponse['success']) {
    //             $history = $historyResponse['history'];
    //             // Use the model from the history if not specified in request
    //             $modelId = $request->model ?? $historyResponse['model_id'] ?? null;
    //         }
    //     } else {
    //         $modelId = $request->model;
    //     }

    //     return $this->openRouter->chat(
    //         $request->message,
    //         $modelId,
    //         $history,
    //         $sessionId,
    //         $userLoggedIn
    //     );
    // }


    public function models()
    {
        return $this->openRouter->getAvailableModels();
    }

    public function savedModels()
    {
        return $this->openRouter->getSavedModels();
    }

    public function sessions(Request $request)
    {
        $userId = $request->_session['id'];

        return $this->openRouter->getChatSessions($userId);
    }

    public function history($sessionId)
    {
        return $this->openRouter->getChatHistory($sessionId);
    }

    // public function continueChat(Request $request)
    // {
    //     $request->validate([
    //         'message' => 'required|string',
    //         'session_id' => 'required|string',
    //         'model' => 'string|nullable',
    //     ]);

    //     $userId = $request->_session['id'];
    //     $sessionId = $request->session_id;

    //     // Get existing chat history from database
    //     $historyResponse = $this->openRouter->getChatHistory($sessionId);
    //     $history = $historyResponse['success'] ? $historyResponse['history'] : [];

    //     // Use the model from the history if available and not specified in request
    //     $modelId = $request->model ?? $historyResponse['model_id'] ?? null;

    //     // Send chat with existing history
    //     return $this->openRouter->chat(
    //         $request->message,
    //         $modelId,
    //         $history,
    //         $sessionId,
    //         $userId
    //     );
    // }


    public function setChatExpiry(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'expire_minutes' => 'required|integer'
        ]);

        return $this->openRouter->setChatExpiry(
            $request->session_id,
            $request->expire_minutes
        );
    }

    public function deleteSession($sessionId)
    {
        return $this->openRouter->deleteSession($sessionId);
    }
}