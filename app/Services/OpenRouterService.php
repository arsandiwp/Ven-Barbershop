<?php

namespace App\Services;

use App\Models\ChatHistory;
use App\Models\ModelSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OpenRouterService
{
    protected $apiKey;
    protected $apiUrl;
    protected $defaultModel = 'google/gemini-2.0-flash-thinking-exp:free';

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
        $this->apiUrl = config('services.openrouter.api_url');
    }

    public function chat2($message, $modelId = null, $history = [], $sessionId = null, $userId = null, $expireMinutes = null)
    {
        try {
            if (!$modelId) {
                $modelSetting = ModelSetting::first();
                // return $modelSetting;
                $modelId = $modelSetting ? $modelSetting->model_id : $this->defaultModel;

                $expiresAt = $modelSetting ? $modelSetting->expires_at : null;
                // dd($$expiresAt);
                // return $expiresAt;
            } else {
                $modelSetting = ModelSetting::where('model_id', $modelId)->first();
                if (!$modelSetting && $modelId != $this->defaultModel) {
                    $modelSetting = ModelSetting::first();
                    $modelId = $modelSetting ? $modelSetting->model_id : $this->defaultModel;
                }

                $expiresAt = $modelSetting ? $modelSetting->expires_at : null;
            }

            if (!$sessionId) {
                $sessionId = Str::uuid()->toString();
            }

            $existingChat = ChatHistory::where('session_id', $sessionId)->first();
            if ($existingChat && $existingChat->expires_at && $existingChat->expires_at->isPast()) {
                return [
                    'success' => false,
                    'error' => 'Chat session has expired',
                    'expired' => true
                ];
            }

            // $expiresAt = null;
            // if ($expireMinutes !== null && is_numeric($expireMinutes) && $expireMinutes > 0) {
            //     $expiresAt = now()->addMinutes($expireMinutes);
            // } elseif ($existingChat && $existingChat->expires_at) {
            //     $expiresAt = $existingChat->expires_at;
            // }
            // if ($expireMinutes !== null && is_numeric($expireMinutes) && $expireMinutes > 0) {
            //     $expiresAt = now()->addMinutes($expireMinutes);  // Tentukan expiredAt dengan expireMinutes
            // } elseif ($existingChat && $existingChat->expires_at) {
            //     $expiresAt = $existingChat->expires_at;  // Gunakan expiredAt dari chat sebelumnya jika ada
            // }

            // Atur expire time
            if ($expireMinutes !== null && is_numeric($expireMinutes) && $expireMinutes > 0) {
                // Gunakan expire minutes dari request
                $expiresAt = now()->addMinutes($expireMinutes);
            } elseif ($existingChat && $existingChat->expires_at) {
                // Gunakan existing expire time dari chat sebelumnya
                $expiresAt = $existingChat->expires_at;
            } elseif ($modelSetting && $modelSetting->expires_at) {
                // Gunakan default expire dari model setting
                // Pastikan nilai di database adalah angka menit, bukan timestamp
                if (is_numeric($modelSetting->expires_at)) {
                    $expiresAt = now()->addMinutes($modelSetting->expires_at);
                } else {
                    // Coba parse jika nilai adalah timestamp atau format tanggal
                    try {
                        $expiresAt = Carbon::parse($modelSetting->expires_at);
                    } catch (\Exception $e) {
                        // Default ke 120 menit jika gagal parse
                        $expiresAt = now()->addMinutes(120);
                    }
                }
            } else {
                // Default ke 120 menit jika tidak ada setting
                $expiresAt = now()->addMinutes(120);
            }

            // Pastikan format $expiresAt adalah objek Carbon yang valid
            if (!($expiresAt instanceof \Carbon\Carbon)) {
                $expiresAt = now()->addMinutes(120);
            }

            $currentHistory = $history;
            $currentHistory[] = [
                'role' => 'user',
                'content' => $message
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'HTTP-Referer' => config('app.url'),
                'X-Title' => 'Laravel Chat App'
            ])->post($this->apiUrl . '/chat/completions', [
                        'model' => $modelId,
                        'messages' => $currentHistory,
                        'temperature' => 0.7,
                        'max_tokens' => 500
                    ]);

            if ($response->successful()) {
                $assistantResponse = $response->json('choices.0.message.content');
                $usage = $response->json('usage');

                $currentHistory[] = [
                    'role' => 'assistant',
                    'content' => $assistantResponse
                ];

                if ($userId) {
                    $conversation = ChatHistory::updateOrCreate(
                        ['session_id' => $sessionId],
                        [
                            'user_id' => $userId,
                            'model_id' => $modelId,
                            'history' => json_encode($currentHistory),
                            'usage' => json_encode($usage),
                            'expires_at' => $expiresAt
                        ]
                    );
                }

                return [
                    'success' => true,
                    'message' => $assistantResponse,
                    'history' => $currentHistory,
                    'usage' => $usage,
                    'model' => $modelId,
                    'session_id' => $sessionId,
                    'expires_at' => $expiresAt
                ];
            }

            return [
                'success' => false,
                'error' => 'API Error: ' . ($response->json('error.message') ?? 'Unknown error')
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Service Error: ' . $e->getMessage()
            ];
        }
    }


    // jangan dihapus noted
    // public function chat($message, $modelId = null, $history = [], $sessionId = null, $userId = null)
    // {
    //     try {
    //         // Model selection logic remains the same
    //         if (!$modelId) {
    //             $modelSetting = ModelSetting::first();
    //             $modelId = $modelSetting ? $modelSetting->model_id : $this->defaultModel;
    //         } else {
    //             $modelSetting = ModelSetting::where('model_id', $modelId)->first();
    //             if (!$modelSetting && $modelId != $this->defaultModel) {
    //                 $modelSetting = ModelSetting::first();
    //                 $modelId = $modelSetting ? $modelSetting->model_id : $this->defaultModel;
    //             }
    //         }

    //         // Generate session ID if not provided
    //         if (!$sessionId) {
    //             $sessionId = Str::uuid()->toString();
    //         }

    //         // Add user message to history array
    //         $currentHistory = $history;
    //         $currentHistory[] = [
    //             'role' => 'user',
    //             'content' => $message
    //         ];

    //         // Make API request
    //         $response = Http::withHeaders([
    //             'Authorization' => 'Bearer ' . $this->apiKey,
    //             'HTTP-Referer' => config('app.url'),
    //             'X-Title' => 'Laravel Chat App'
    //         ])->post($this->apiUrl . '/chat/completions', [
    //                     'model' => $modelId,
    //                     'messages' => $currentHistory,
    //                     'temperature' => 0.7,
    //                     'max_tokens' => 500
    //                 ]);

    //         if ($response->successful()) {
    //             $assistantResponse = $response->json('choices.0.message.content');
    //             $usage = $response->json('usage');

    //             // Add assistant response to history array
    //             $currentHistory[] = [
    //                 'role' => 'assistant',
    //                 'content' => $assistantResponse
    //             ];

    //             // Update or create a single conversation record
    //             // Instead of adding a new record each time
    //             $conversation = ChatHistory::updateOrCreate(
    //                 ['session_id' => $sessionId],
    //                 [
    //                     'user_id' => $userId,
    //                     'model_id' => $modelId,
    //                     'history' => json_encode($currentHistory),
    //                     'usage' => json_encode($usage)
    //                 ]
    //             );

    //             return [
    //                 'success' => true,
    //                 'message' => $assistantResponse,
    //                 'history' => $currentHistory,
    //                 'usage' => $usage,
    //                 'model' => $modelId,
    //                 'session_id' => $sessionId
    //             ];
    //         }

    //         return [
    //             'success' => false,
    //             'error' => 'API Error: ' . ($response->json('error.message') ?? 'Unknown error')
    //         ];

    //     } catch (\Exception $e) {
    //         return [
    //             'success' => false,
    //             'error' => 'Service Error: ' . $e->getMessage()
    //         ];
    //     }
    // }


    public function getSavedModels()
    {
        $models = ModelSetting::all();

        return [
            'success' => true,
            'models' => $models
        ];
    }

    public function getAvailableModels()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey
            ])->get($this->apiUrl . '/models');

            if ($response->successful()) {
                $savedModels = ModelSetting::all()->keyBy('model_id')->toArray();
                $apiModels = $response->json('data');

                foreach ($apiModels as &$model) {
                    $modelId = $model['id'];
                    $model['is_saved'] = isset($savedModels[$modelId]);
                    if ($model['is_saved']) {
                        $model['saved_name'] = $savedModels[$modelId]['name'];
                    }
                }

                return [
                    'success' => true,
                    'models' => $apiModels,
                    'saved_models' => ModelSetting::all()
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to fetch models'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Service Error: ' . $e->getMessage()
            ];
        }
    }

    public function getChatSessions($userId = null)
    {
        try {
            $query = ChatHistory::query();

            if ($userId) {
                $query->where('user_id', $userId);
            }

            $sessionInfo = $query->select('session_id', 'created_at', 'history')
                ->orderBy('created_at', 'desc')
                ->get();

            $sessions = [];

            foreach ($sessionInfo as $session) {
                $history = json_decode($session->history, true);
                $title = 'Chat Session';

                if (is_array($history)) {
                    foreach ($history as $message) {
                        if (isset($message['role']) && $message['role'] === 'user') {
                            $title = substr($message['content'], 0, 50);
                            break;
                        }
                    }
                }

                $sessions[] = [
                    'session_id' => $session->session_id,
                    'last_activity' => $session->created_at,
                    'title' => $title,
                    'message_count' => is_array($history) ? count($history) : 0
                ];
            }

            return [
                'success' => true,
                'sessions' => $sessions
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Error retrieving sessions: ' . $e->getMessage()
            ];
        }
    }

    public function getChatHistory($sessionId)
    {
        try {
            $conversation = ChatHistory::where('session_id', $sessionId)->first();

            if (!$conversation) {
                return [
                    'success' => false,
                    'error' => 'Conversation not found'
                ];
            }

            $history = json_decode($conversation->history, true);

            return [
                'success' => true,
                'history' => $history,
                'model_id' => $conversation->model_id,
                'usage' => json_decode($conversation->usage, true)
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Error retrieving history: ' . $e->getMessage()
            ];
        }
    }

    public function deleteSession($sessionId)
    {
        try {
            ChatHistory::where('session_id', $sessionId)->delete();

            return [
                'success' => true,
                'message' => 'Session berhasil dihapus'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to delete session: ' . $e->getMessage()
            ];
        }
    }

    public function setChatExpiry($sessionId, $expireMinutes)
    {
        try {
            $conversation = ChatHistory::where('session_id', $sessionId)->first();

            if (!$conversation) {
                return [
                    'success' => false,
                    'error' => 'Chat session not found'
                ];
            }

            // Hitung waktu expired berdasarkan menit
            $expiresAt = null;
            if ($expireMinutes !== null && is_numeric($expireMinutes)) {
                if ($expireMinutes > 0) {
                    $expiresAt = now()->addMinutes($expireMinutes);
                } else {
                    // Jika 0, hapus batas waktu
                    $expiresAt = null;
                }
            } else {
                return [
                    'success' => false,
                    'error' => 'Invalid expire_minutes value'
                ];
            }

            // Update waktu expired
            $conversation->expires_at = $expiresAt;
            $conversation->save();

            return [
                'success' => true,
                'message' => 'Chat expiry time updated successfully',
                'session_id' => $sessionId,
                'expires_at' => $expiresAt
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Error setting expiry time: ' . $e->getMessage()
            ];
        }
    }
}
