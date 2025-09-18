<?php
header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => 'حدث خطأ في إرسال الطلب.');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // التحقق من وجود جميع الحقول المطلوبة
    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['service']) && isset($_POST['details'])) {
        
        // **قم بوضع رمز البوت ومعرف الدردشة الخاص بك هنا**
        $telegram_token = 'YOUR_TELEGRAM_BOT_TOKEN'; // استبدل هذا
        $chat_id = '1054158604'; // استبدل هذا

        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = isset($_POST['phone']) ? $_POST['phone'] : 'غير متوفر';
        $service = $_POST['service'];
        $details = $_POST['details'];

        // بناء محتوى الرسالة
        $message_text = "✨ طلب خدمة تقنية جديد ✨\n\n";
        $message_text .= "👤 الاسم: " . $name . "\n";
        $message_text .= "📧 البريد الإلكتروني: " . $email . "\n";
        $message_text .= "📱 رقم الهاتف: " . $phone . "\n";
        $message_text .= "🛠️ الخدمة المطلوبة: " . $service . "\n\n";
        $message_text .= "📝 تفاصيل الطلب:\n" . $details . "\n";

        // تهيئة الـ API
        $url = "https://api.telegram.org/bot{$telegram_token}/sendMessage";
        
        $data = [
            'chat_id' => $chat_id,
            'text'    => $message_text,
        ];
        
        // إعدادات الـ cURL
        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
        ];
        
        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $result = curl_exec($curl);
        curl_close($curl);
        
        $telegram_response = json_decode($result, true);

        // التحقق من نجاح الإرسال
        if ($telegram_response['ok']) {
            $response['status'] = 'success';
            $response['message'] = 'تم إرسال طلبك بنجاح! سيتم التواصل معك قريبًا.';
        } else {
            $response['message'] = 'فشل إرسال التنبيه. يرجى المحاولة لاحقًا.';
        }
    }
}

echo json_encode($response);
?>