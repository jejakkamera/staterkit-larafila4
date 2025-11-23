<?php

namespace App\Livewire;

use Livewire\Component;
use OpenAI\Laravel\Facades\OpenAI;

class Chat extends Component
{
    public $messages = [];
    public $userMessage = '';
    public $isLoading = false;

    public function sendMessage()
    {
        if (empty(trim($this->userMessage))) {
            return;
        }

        $this->isLoading = true;

        try {
            // Add user message to chat
            $this->messages[] = [
                'role' => 'user',
                'content' => $this->userMessage,
            ];

            // Call OpenAI API
            $response = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => $this->messages,
                'temperature' => 0.7,
            ]);

            // Get AI response
            $aiResponse = $response->choices[0]->message->content;

            // Add AI message to chat
            $this->messages[] = [
                'role' => 'assistant',
                'content' => $aiResponse,
            ];

            // Clear input
            $this->userMessage = '';
        } catch (\Exception $e) {
            $this->messages[] = [
                'role' => 'error',
                'content' => 'Error: ' . $e->getMessage(),
            ];
        } finally {
            $this->isLoading = false;
        }
    }

    public function clearChat()
    {
        $this->messages = [];
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
