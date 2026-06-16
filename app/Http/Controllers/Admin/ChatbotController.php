<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatbotIntent;
use App\Models\ChatbotResponse;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use Illuminate\Validation\Rule;

class ChatbotController extends Controller
{
    public function index(Request $request)
    {
        $intents = ChatbotIntent::withCount('responses')
            ->orderBy('intent_name')
            ->get();

        $responsesQuery = ChatbotResponse::with('intent')->orderBy('intent_id')->orderBy('priority');
        if ($request->filled('intent_id')) {
            $responsesQuery->where('intent_id', $request->intent_id);
        }
        $responses = $responsesQuery->paginate(15)->withQueryString();

        $totalSessions = ChatSession::count();
        $totalMessages = ChatMessage::count();
        $sessionsToday = ChatSession::whereDate('created_at', today())->count();

        return view('admin.chatbot.index', compact('intents', 'responses', 'totalSessions', 'totalMessages', 'sessionsToday'));
    }

    public function storeIntent(Request $request)
    {
        $request->validate([
            'intent_name' => 'required|string|max:100|unique:chatbot_intents,intent_name',
            'description' => 'required|string|max:255',
            'example_phrases' => 'nullable|string',
            'action' => 'required|in:faq_lookup,guide_booking,introduce_specialty,transfer_staff',
            'is_active' => 'boolean',
        ]);

        ChatbotIntent::create([
            'intent_name' => $request->intent_name,
            'description' => $request->description,
            'example_phrases' => $request->example_phrases,
            'action' => $request->action,
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Đã thêm Intent thành công.');
    }

    public function updateIntent(Request $request, $id)
    {
        $intent = ChatbotIntent::findOrFail($id);

        $request->validate([
            'intent_name' => ['required', 'string', 'max:100', Rule::unique('chatbot_intents')->ignore($intent->id)],
            'description' => 'required|string|max:255',
            'example_phrases' => 'nullable|string',
            'action' => 'required|in:faq_lookup,guide_booking,introduce_specialty,transfer_staff',
            'is_active' => 'boolean',
        ]);

        $intent->update([
            'intent_name' => $request->intent_name,
            'description' => $request->description,
            'example_phrases' => $request->example_phrases,
            'action' => $request->action,
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Đã cập nhật Intent thành công.');
    }

    public function toggleIntentActive($id)
    {
        $intent = ChatbotIntent::findOrFail($id);
        $intent->is_active = !$intent->is_active;
        $intent->save();

        return back()->with('success', 'Đã thay đổi trạng thái Intent.');
    }

    public function destroyIntent($id)
    {
        $intent = ChatbotIntent::withCount('responses')->findOrFail($id);
        
        if ($intent->responses_count > 0) {
            return back()->with('error', 'Không thể xoá Intent đang có câu trả lời (Responses). Vui lòng xoá các câu trả lời trước.');
        }

        $intent->delete();

        return back()->with('success', 'Đã xoá Intent thành công.');
    }

    public function storeResponse(Request $request)
    {
        $request->validate([
            'intent_id' => 'required|exists:chatbot_intents,id',
            'content' => 'required|string',
            'priority' => 'nullable|integer|min:1|max:10',
            'is_active' => 'boolean',
        ]);

        ChatbotResponse::create([
            'intent_id' => $request->intent_id,
            'content' => $request->content,
            'priority' => $request->priority ?? 1,
            'is_active' => $request->has('is_active'),
            'use_count' => 0,
        ]);

        return back()->with('success', 'Đã thêm Phản hồi thành công.');
    }

    public function updateResponse(Request $request, $id)
    {
        $response = ChatbotResponse::findOrFail($id);

        $request->validate([
            'intent_id' => 'required|exists:chatbot_intents,id',
            'content' => 'required|string',
            'priority' => 'nullable|integer|min:1|max:10',
            'is_active' => 'boolean',
        ]);

        $response->update([
            'intent_id' => $request->intent_id,
            'content' => $request->content,
            'priority' => $request->priority ?? 1,
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Đã cập nhật Phản hồi thành công.');
    }

    public function toggleResponseActive($id)
    {
        $response = ChatbotResponse::findOrFail($id);
        $response->is_active = !$response->is_active;
        $response->save();

        return back()->with('success', 'Đã thay đổi trạng thái Phản hồi.');
    }

    public function destroyResponse($id)
    {
        $response = ChatbotResponse::findOrFail($id);
        $response->delete();

        return back()->with('success', 'Đã xoá Phản hồi thành công.');
    }

    public function testChat(Request $request)
    {
        $message = $request->input('message');
        if (empty($message)) {
            return response()->json(['error' => 'Message is required'], 400);
        }

        $apiKey = env('GEMINI_API_KEY');
        if (empty($apiKey)) {
            return response()->json(['reply' => 'Lỗi: Chưa cấu hình GEMINI_API_KEY trong file .env.']);
        }

        // Gather all active intents and responses to build context
        $intents = ChatbotIntent::with(['responses' => function($q) {
            $q->where('is_active', true)->orderBy('priority');
        }])->where('is_active', true)->get();

        $context = "Bạn là trợ lý ảo của phòng khám Carebook. Hãy trả lời dựa trên các dữ liệu sau:\n";
        foreach ($intents as $intent) {
            $context .= "Ý định: " . $intent->intent_name . " (Hành động: " . $intent->action . ")\n";
            $context .= "Câu hỏi mẫu: " . $intent->example_phrases . "\n";
            $context .= "Câu trả lời gợi ý: \n";
            foreach ($intent->responses as $resp) {
                $context .= "- " . $resp->content . "\n";
            }
            $context .= "\n";
        }
        $context .= "Nếu người dùng hỏi nằm ngoài phạm vi này, hãy nói rằng bạn không hiểu và khuyên họ liên hệ tổng đài.";

        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'json' => [
                    'system_instruction' => [
                        'parts' => [
                            ['text' => $context]
                        ]
                    ],
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $message]
                            ]
                        ]
                    ]
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            $reply = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Xin lỗi, tôi không thể trả lời lúc này.';

            return response()->json(['reply' => $reply]);
        } catch (\Exception $e) {
            return response()->json(['reply' => 'Lỗi kết nối AI: ' . $e->getMessage()]);
        }
    }
}
