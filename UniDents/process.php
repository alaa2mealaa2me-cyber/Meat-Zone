<?php
// إظهار جميع الأخطاء - مهم جداً لتحديد المشكلة
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => 'حدث خطأ في إرسال الطلب.');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // التحقق من وجود جميع الحقول المطلوبة
    if (isset($_POST['clinic_number']) && isset($_POST['datetime']) && isset($_POST['service']) && isset($_POST['details'])) {
        
        // **قم بوضع رمز البوت ومعرف الدردشة الخاص بك هنا**
        $telegram_token = '8284674030:AAHBriEt2Bi9FxnZlLyfx0s1FSs_Q-YPAZA'; // استبدل هذا
        //$chat_id = '1054158604'; // استبدل هذا
        $chat_id = '-4754179379'; // استبدل هذا
       
        $clinic_number = $_POST['clinic_number'];
        $datetime = $_POST['datetime'];
        $phone = isset($_POST['phone']) ? $_POST['phone'] : 'غير متوفر';
        $service = $_POST['service'];
        $details = $_POST['details'];
        
        // تحويل التاريخ والوقت ليكون مقروءاً
        $formatted_datetime = date('Y-m-d H:i', strtotime($datetime));

        // بناء محتوى الرسالة
        $message_text = "✨ طلب خدمة ✨\n\n";
        $message_text .= "🏥 رقم او موقع الخدمة: " . $clinic_number . "\n";
        $message_text .= "📅 التاريخ والوقت: " . $formatted_datetime . "\n";
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
        
        // إضافة هذا الكود للحصول على رسالة خطأ محددة
        $curl_error = curl_error($curl);
        
        curl_close($curl);
        
        if ($curl_error) {
            $response['message'] = "فشل cURL: " . $curl_error;
        } else {
            $telegram_response = json_decode($result, true);
            // التحقق من نجاح الإرسال
            if ($telegram_response['ok']) {
                $response['status'] = 'success';
                $response['message'] = 'تم إرسال طلبك بنجاح! سيتم التواصل معك قريبًا.';
            } else {
                $response['message'] = "فشل إرسال التنبيه: " . json_encode($telegram_response);
            }
        }
    }
}

echo json_encode($response);
?>
