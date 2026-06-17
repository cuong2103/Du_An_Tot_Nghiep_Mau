<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CareBook Notification</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 15px 20px; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 8px 8px; }
        .footer { margin-top: 20px; font-size: 12px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin: 0;">CareBook System</h2>
        </div>
        <div class="content">
            {!! nl2br(e($content)) !!}
        </div>
        <div class="footer">
            <p>Đây là email tự động từ hệ thống CareBook, vui lòng không trả lời.</p>
        </div>
    </div>
</body>
</html>
