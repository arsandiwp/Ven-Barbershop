<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

use App\Models\Qna;
use App\Models\TemplateWeb;

class BotManController extends Controller
{
    public function handle()
    {
        $botman = app('botman');

        $botman->hears('{message}', function (BotMan $bot, $message) {
            $token = request()->get('token');

            if ($message === 'mulai') {
                $this->startConversation($bot, $token);
            } else {
                $this->answerQuestion($bot, $message, $token);
            }
        });

        $botman->listen();
    }

    public function startConversation(BotMan $bot, $token)
    {
        $template = TemplateWeb::where('token', $token)->with('qnas')->first();

        if (!$template || $template->qnas->isEmpty()) {
            $bot->reply("Tidak ada pertanyaan yang tersedia.");
            return;
        }

        $question = Question::create("Silakan pilih pertanyaan:")
            ->addButtons(
                $template->qnas->map(function ($qna) {
                    return Button::create($qna->question)->value($qna->question);
                })->toArray()
            );

        $bot->reply($question);
    }

    public function answerQuestion(BotMan $bot, $question, $token)
    {
        $template = TemplateWeb::where('token', $token)->first();

        if (!$template) {
            $bot->reply("Token tidak valid.");
            return;
        }

        $qna = $template->qnas()->where('question', $question)->first();

        if ($qna) {
            $bot->reply("❓ *" . $question . "*");
            $bot->reply("✅ " . $qna->answer);
            $this->startConversation($bot, $token);
        } else {
            $keywords = explode(' ', strtolower($question));
            $query = $template->qnas();

            foreach ($keywords as $keyword) {
                $query->where(DB::raw('LOWER(question)'), 'LIKE', "%$keyword%");
            }

            $suggestions = $query->get();

            if ($suggestions->isEmpty()) {
                $bot->reply("🤖 Maaf, saya tidak mengenali pertanyaan ini.");
                return false;
            }

            $question = Question::create("Mungkin yang Anda maksud adalah?")
                ->addButtons(
                    $suggestions->map(function ($qna) {
                        return Button::create($qna->question)->value($qna->question);
                    })->toArray()
                );

            $bot->reply($question);
        }
    }

    public function botmanJs(Request $request)
    {
        $token = $request->token;

        if(!$token){
            return response()->make(
                "console.error('Token is missing! Make sure the URL contains ?token=...');"
            )->header('Content-Type', 'application/javascript');
        }

        return response()->make(
            "window.botmanWidget = {
                frameEndpoint: '/botman/chat',
                introMessage: 'Halo! Ketik \"mulai\" untuk melihat daftar pertanyaan.',
                chatServer: '/botman?token=$token',
                title: 'Chatbot QnA'
            };

            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js';
            script.async = true;
            document.body.appendChild(script);"
        )->header('Content-Type', 'application/javascript');
    }
}
