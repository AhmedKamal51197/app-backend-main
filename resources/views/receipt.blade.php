<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Payment Receipt</title>
    <style>
        .button:hover {
            background: #7c3aed !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4) !important;
        }
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4;">
    <tr>
        <td align="center" style="padding: 10px 0;">
            <table width="600" cellpadding="0" cellspacing="0" border="0"
                   style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

                <!-- Logo -->
                <tr>
                    <td style="padding: 20px; text-align: center;">
                        <img src="https://i.imgur.com/UzG5DSL.png" width="200" alt="Team Work Logo"/>
                    </td>
                </tr>

                <!-- Header -->
                <tr>
                    <td style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); padding: 60px 20px; text-align: center;">
                        <h1 style="color: white; font-size: 32px; font-weight: 700; margin: 0 0 10px 0;">
                            {{ $title ?? 'Payment Receipt' }}
                        </h1>
                        <p style="color: white; font-size: 18px; margin: 0;">
                            Thank you for your payment!
                        </p>
                    </td>
                </tr>

                <!-- Receipt Details -->
                <tr>
                    <td style="padding: 40px 20px; text-align: left;">
                        <p style="font-size: 18px; margin: 0 0 10px 0;">Hi <strong>{{ $name }}</strong>,</p>
                        <p style="font-size: 16px; margin: 0 0 30px 0;">
                            We’ve received your payment successfully. Here are the details:
                        </p>

                        <table width="100%" cellpadding="10" cellspacing="0" border="0"
                               style="border: 1px solid #e2e8f0; border-radius: 8px;">
                            <tr>
                                <td style="font-weight: 600; width: 40%;">Amount:</td>
                                <td>{{ $amount }}</td>
                            </tr>
                            <tr style="background-color: #f9fafb;">
                                <td style="font-weight: 600;">Payment Date:</td>
                                <td>{{ $date }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600;">Name:</td>
                                <td>{{ $name ?? 'N/A' }}</td>
                            </tr>
                        </table>

                        <!-- Button -->
                        <table cellpadding="0" cellspacing="0" border="0" style="margin: 30px 0 0 0;">
                            <tr>
                                <td>
                                    <a href="{{ $url ?? '#' }}" class="button"
                                       style="color: white; background-color: #8b5cf6; border-radius: 12px; padding: 14px 28px; font-size: 18px; font-weight: 700; text-decoration: none; display: block; text-align: center; width: 200px;">
                                        Go to dashboard
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background-color: #f8f9fa; padding: 30px 20px; text-align: center; border-top: 1px solid #e9ecef;">
                        <p style="color: #6c757d; font-size: 14px; margin: 0;">
                            © 2025 TeamWork. All rights reserved.
                        </p>
                        <p style="color: #6c757d; font-size: 12px; margin: 5px 0 0 0;">
                            If you no longer wish to receive these emails, you can <a href="#" style="color: #10b981;">unsubscribe
                                here</a>.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
