<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: #2d6a4f; padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 22px; }
        .body { padding: 30px; color: #333333; line-height: 1.7; }
        .body h2 { color: #2d6a4f; }
        .details-box { background: #f0faf4; border-left: 4px solid #2d6a4f; padding: 15px 20px; margin: 20px 0; border-radius: 4px; }
        .details-box p { margin: 6px 0; }
        .payment-notice { background: #fff8e1; border-left: 4px solid #f9a825; padding: 15px 20px; margin: 20px 0; border-radius: 4px; }
        .payment-notice p { margin: 6px 0; color: #7a5c00; }
        .footer { background: #f4f4f4; text-align: center; padding: 20px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">

        {{-- Header --}}
        <div class="header">
            <h1>Consultation Booking Confirmed ✅</h1>
        </div>

        {{-- Body --}}
        <div class="body">
            <h2>Hello, {{ $data['first_name'] }} {{ $data['last_name'] }}!</h2>

            <p>
                We're pleased to let you know that your consultation has been successfully booked.
                Our doctor will attend to you as soon as possible. Please be patient — we're committed
                to providing you with the best care.
            </p>

            {{-- Booking Details --}}
            <div class="details-box">
                <p><strong>👤 Name:</strong> {{ $data['first_name'] }} {{ $data['last_name'] }}</p>
                <p><strong>🩺 Service:</strong> {{ $data['solution_name'] }}</p>
                <p><strong>💰 Consultation Fee:</strong> ${{ number_format($data['cost'], 2) }}</p>
            </div>

            {{-- Payment Notice --}}
            <div class="payment-notice">
                <p><strong>⚠️ Payment Notice</strong></p>
                <p>
                    Your payment is currently <strong>pending</strong>. You will <strong>not</strong> be
                    charged now — the consultation fee of <strong>${{ number_format($data['cost'], 2) }}</strong>
                    will only be debited from your account once your consultation has been
                    successfully completed.
                </p>
            </div>

            <p>If you have any questions or need to make changes, feel free to reach out to our support team.</p>

            <p>Thank you for trusting us with your health. 💚</p>

            <p>Warm regards,<br><strong>JJHealth Services Team</strong></p>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>© {{ date('Y') }} JJHealth Services. All rights reserved.</p>
            <p>This is an automated message, please do not reply directly to this email.</p>
        </div>

    </div>
</body>
</html>