<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Welcome to TeamWork</title>
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
        <td align="center" style="padding: 1px 0;">
            <table width="600" cellpadding="0" cellspacing="0" border="0"
                   style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">

                <tr>
                    <td style="padding: 8px; text-align: center;">
                        <img src="https://i.imgur.com/UzG5DSL.png" width="250" height="85" alt="Team Work logo"/>
                    </td>
                </tr>

                <tr>
                    <td style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); padding: 80px 20px; text-align: center; position: relative;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td align="center">
                                    <h1 style="color: white; font-size: 36px; font-weight: 800; line-height: 1.4; letter-spacing: -1px; margin: 0 0 30px 0;">
                                        {{ $title ?? 'Welcome to TeamWork!' }}
                                    </h1>
                                    <p style="color: white; font-size: 20px; font-weight: 300; opacity: 0.9; margin: 0;">
                                        We're excited to have you on board
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="background-color: #ffffff; padding: 0 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="width: 60%; vertical-align: top; padding: 80px 20px;">
                                    <p style="font-size: 24px; line-height: 1.6; color: #1a1a1a; margin: 0 0 40px 0; font-weight: 400;">
                                        {{ $description ?? '' }}
                                    </p>
                                    <table cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
                                        <tr>
                                            <td>
                                                <a href="{{ $url ?? '#' }}" class="button"
                                                   style="color: white; background-color: #8b5cf6; border-radius: 12px; padding: 14px 28px; font-size: 18px; font-weight: 700; text-decoration: none; display: block; text-align: center; width: 200px;">
                                                    {{ $button_title ?? 'Click Here' }}
                                                </a>

                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="width: 38%; vertical-align: top; text-align: center;">
                                    <img src="https://i.imgur.com/WIsaPbd.png" width="200" alt="svg shape"/>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="background-color: #ffffff; padding: 10px 0; text-align: center;">
                        <!-- Facebook -->
                        <a href="https://www.facebook.com/people/Team-Work-app/61576406606300/?mibextid=wwXIfr&rdid=nupjAdVGgJYFYaCa&share_url=https%3A%2F%2Fwww.facebook.com%2Fshare%2F18sNihAdCH%2F%3Fmibextid%3DwwXIfr"
                           target="_blank" style="display: inline-block; margin: 0 10px;">
                            <img src="https://cdn-icons-png.flaticon.com/128/5968/5968764.png"
                                 alt="Facebook" width="30" style="border: none; display: block;">
                        </a>

                        <!-- Instagram -->
                        <a href="https://www.instagram.com/teamwork.app/?igsh=MXM4b2hwdjhudGpidg%3D%3D&utm_source=qr#"
                           target="_blank" style="display: inline-block; margin: 0 10px;">
                            <img src="https://cdn-icons-png.flaticon.com/128/2111/2111463.png"
                                 alt="Instagram" width="30" style="border: none; display: block;">
                        </a>

                        <!-- TikTok -->
                        <a href="https://www.tiktok.com/@teamwork_app"
                           target="_blank" style="display: inline-block; margin: 0 10px;">
                            <img src="https://cdn-icons-png.flaticon.com/128/3046/3046121.png"
                                 alt="TikTok" width="30" style="border: none; display: block;">
                        </a>

                        <!-- WhatsApp -->
                        <a href="https://api.whatsapp.com/send/?phone=%2B96566445995&text&type=phone_number&app_absent=0"
                           target="_blank" style="display: inline-block; margin: 0 10px;">
                            <img src="https://cdn-icons-png.flaticon.com/128/15707/15707820.png"
                                 alt="WhatsApp" width="30" style="border: none; display: block;">
                        </a>
                    </td>
                </tr>



                <tr>
                    <td style="background-color: #f8f9fa; padding: 30px 20px; text-align: center; border-top: 1px solid #e9ecef;">
                        <p style="color: #6c757d; font-size: 14px; margin: 0;">
                            © 2025 TeamWork. All rights reserved.
                        </p>
                        <p style="color: #6c757d; font-size: 12px; margin: 10px 0 0 0;">
                            If you no longer wish to receive these emails, you can <a href="#" style="color: #8b5cf6;">unsubscribe
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
