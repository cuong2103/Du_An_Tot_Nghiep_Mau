<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChatbotIntent;
use App\Models\ChatbotResponse;
use App\Models\Faq;

class ChatbotSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo Kịch bản (Intents) & Câu trả lời (Responses)
        
        $intentGreeting = ChatbotIntent::create([
            'intent_name' => 'greeting',
            'description' => 'Chào hỏi người dùng',
            'example_phrases' => 'chào│hi│hello│xin chào│chào bạn',
            'action' => 'faq_lookup', // Trả lời trực tiếp nhưng set default
            'is_active' => true,
        ]);
        ChatbotResponse::create([
            'intent_id' => $intentGreeting->id,
            'content' => 'Xin chào! Mình là trợ lý ảo của CareBook. Bạn cần hỗ trợ đặt lịch khám hay tra cứu thông tin gì ạ?',
            'priority' => 1,
        ]);

        $intentPrice = ChatbotIntent::create([
            'intent_name' => 'ask_price',
            'description' => 'Hỏi về giá cả khám bệnh',
            'example_phrases' => 'giá khám│bao nhiêu tiền│chi phí khám│tốn tiền không│bảng giá',
            'action' => 'faq_lookup',
            'is_active' => true,
        ]);
        ChatbotResponse::create([
            'intent_id' => $intentPrice->id,
            'content' => 'Chào bạn, chi phí khám lâm sàng ban đầu tại bệnh viện dao động từ 150.000 VNĐ đến 300.000 VNĐ tùy theo chuyên khoa. Chi phí xét nghiệm hoặc chụp chiếu sẽ được bác sĩ chỉ định sau khi khám ạ.',
            'priority' => 1,
        ]);

        $intentBooking = ChatbotIntent::create([
            'intent_name' => 'guide_booking',
            'description' => 'Hướng dẫn cách đặt lịch',
            'example_phrases' => 'cách đặt lịch│muốn khám│hướng dẫn đặt khám│đăng ký khám│book lịch',
            'action' => 'guide_booking',
            'is_active' => true,
        ]);
        ChatbotResponse::create([
            'intent_id' => $intentBooking->id,
            'content' => 'Để đặt lịch khám, bạn vui lòng nhấp vào nút "Đặt Lịch Khám" ở thanh menu phía trên, hoặc để lại số điện thoại để nhân viên tổng đài hỗ trợ trực tiếp nhé.',
            'priority' => 1,
        ]);

        $intentLocation = ChatbotIntent::create([
            'intent_name' => 'ask_location',
            'description' => 'Hỏi địa chỉ bệnh viện',
            'example_phrases' => 'địa chỉ│ở đâu│chỗ nào│đường nào',
            'action' => 'faq_lookup',
            'is_active' => true,
        ]);
        ChatbotResponse::create([
            'intent_id' => $intentLocation->id,
            'content' => 'Bệnh viện Đa khoa CareBook nằm tại địa chỉ: 123 Đường Sức Khoẻ, Quận Bình Thủy, TP. Cần Thơ. Bạn có thể tra Google Maps để được hướng dẫn đường đi nhé!',
            'priority' => 1,
        ]);

        // 2. Tạo FAQs
        Faq::create([
            'question' => 'Khám răng hàm mặt giá bao nhiêu?',
            'answer' => 'Chào bạn, chi phí nhổ răng dao động từ 200k - 500k tùy tình trạng, các dịch vụ khác như niềng răng sẽ cần bác sĩ tư vấn trực tiếp.',
            'specialty_id' => 1, // Giả sử 1 là Răng Hàm Mặt
            'keywords' => 'răng,nhổ răng,đau răng,niềng răng',
            'is_active' => true,
        ]);

        Faq::create([
            'question' => 'Tôi bị đau dạ dày thì khám khoa nào?',
            'answer' => 'Đối với các triệu chứng đau dạ dày, buồn nôn, khó tiêu, bạn nên đặt lịch khám tại chuyên khoa Nội Tiêu Hóa nhé.',
            'specialty_id' => 2, // Giả sử 2 là Nội Tiêu Hóa
            'keywords' => 'dạ dày,tiêu hóa,đau bụng',
            'is_active' => true,
        ]);
    }
}
