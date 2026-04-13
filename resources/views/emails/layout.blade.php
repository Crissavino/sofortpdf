<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('subject', 'sofortpdf.com')</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f8; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f6f8;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    {{-- Header --}}
                    <tr>
                        <td style="background-color: #1a56db; padding: 28px 40px; text-align: center;">
                            <span style="color: #ffffff; font-size: 26px; font-weight: 700; letter-spacing: -0.5px;">sofortpdf.com</span>
                        </td>
                    </tr>

                    {{-- Inhalt --}}
                    <tr>
                        <td style="padding: 40px;">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f9fafb; padding: 24px 40px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 8px 0; font-size: 13px; color: #6b7280; text-align: center;">
                                sofortpdf.com &mdash; Ihre Online-PDF-Tools
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #9ca3af; text-align: center;">
                                Sie erhalten diese E-Mail, weil Sie ein Konto bei sofortpdf.com haben.<br>
                                <a href="{{ url('/') }}" style="color: #1a56db; text-decoration: underline;">sofortpdf.com besuchen</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
