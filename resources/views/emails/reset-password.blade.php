<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Your Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; background-color:#f4f7fb; font-family:Arial, Helvetica, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f7fb; padding:40px 0;">
    <tr>
        <td align="center">

            <!-- Email Container -->
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:6px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.05);">

                <!-- Header -->
                <tr>
                    <td align="center" style="padding:30px 20px; border-bottom:3px solid #6c63ff;">
                        <h1 style="margin:0; color:#333;">Reset Your Password</h1>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:30px 40px; color:#555; font-size:15px; line-height:1.6;">
                        <p style="margin-top:0;">
                            Hi {{ $user->name ?? 'there' }},
                        </p>

                        <p>
                            Tap the button below to reset your account password.
                            If you didn’t request a new password, you can safely ignore this email.
                        </p>

                        <!-- Button -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin:30px 0;">
                            <tr>
                                <td align="center">
                                    <a href="{{ url('/reset-password/'.$token.'?email='.request('email')) }}"
                                       style="
                                           background:#6c63ff;
                                           color:#ffffff;
                                           text-decoration:none;
                                           padding:14px 28px;
                                           border-radius:4px;
                                           display:inline-block;
                                           font-size:16px;
                                           font-weight:bold;
                                       ">
                                        Reset Password
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <p style="font-size:13px; color:#777;">
                            If the button doesn’t work, copy and paste the link below into your browser:
                        </p>

                        <p style="word-break:break-all;">
                            <a href="{{ url('/reset-password/'.$token.'?email='.request('email')) }}" style="color:#6c63ff;">
                                Reset Password 
                            </a>
                        </p>

                        <p style="font-size:13px; color:#777;">
                            This password reset link will expire in <strong>24 hours</strong>.
                        </p>

                        <p style="margin-bottom:0;">
                            Thanks,<br>
                            <strong>{{ config('app.name') }}</strong>
                        </p>
                    </td>
                </tr>

            </table>

            <!-- Footer -->
            <p style="font-size:12px; color:#999; margin-top:20px;"> {{ config('app.name') }}
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>

        </td>
    </tr>
</table>

</body>
</html>
