<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Employee Management System</title>
</head>

<body style="margin:0;padding:0;background-color:#f3f1ed;font-family:Arial,Helvetica,sans-serif;color:#263244;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
    style="width:100%;background-color:#f3f1ed;">
    <tr>
        <td align="center" style="padding:32px 12px;">

            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                   style="width:100%;max-width:720px;">

                <!-- Application Header -->
                <tr>
                    <td style="padding:0 0 28px;">
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td valign="middle"
                                    style="width:46px;height:46px;background-color:#17263d;border-radius:4px;text-align:center;color:#d4bd8a;font-size:15px;font-weight:700;letter-spacing:1px;">
                                    EMS
                                </td>
                                <td valign="middle" style="padding-left:12px;">
                                    <div style="font-size:19px;font-weight:700;color:#17263d;line-height:1.5;letter-spacing:.2px;">
                                        Employee Management System
                                    </div>
                                    <div style="font-size:12px;color:#7a7c80;letter-spacing:.4px;">
                                        PEOPLE &amp; OPERATIONS
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Page Title -->
                <tr>
                    <td style="padding:0 0 24px;">
                        <h1 style="margin:0 0 8px;font-size:28px;line-height:1.3;font-weight:700;color:#1e293b;">
                            Welcome to the team!
                        </h1>
                        <p style="margin:0;font-size:14px;line-height:1.7;color:#6e737b;">
                            We're delighted to have you as part of our organization.
                        </p>
                    </td>
                </tr>

                <!-- Welcome Card -->
                <tr>
                    <td style="background-color:#ffffff;border:1px solid #e5e1d9;border-radius:6px;padding:34px;">

                        <!-- Section Heading -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                               style="border-bottom:1px solid #e8e4dc;margin-bottom:24px;">
                            <tr>
                                <td style="padding-bottom:20px;">
                                    <h2 style="margin:0 0 8px;font-size:18px;font-weight:700;line-height:1.5;color:#17263d;">
                                        Your employee account is ready
                                    </h2>
                                    <p style="margin:0;font-size:14px;line-height:1.7;color:#70757d;">
                                        Your account has been successfully created in the Employee Management System.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Greeting -->
                        <p style="margin:0 0 16px;font-size:15px;line-height:1.8;">
                            Hello {{ $employee->name }},
                        </p>

                        <p style="margin:0 0 24px;font-size:14px;line-height:1.9;color:#4f5661;">
                            Welcome aboard! We're pleased to have you join our team.
                            We look forward to working with you and wish you success
                            in your journey with us.
                        </p>

                        <!-- Employee Details -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                               style="border:1px solid #e8e4dc;margin-bottom:24px;">

                            <tr>
                                <td colspan="2"
                                    style="padding:14px 16px;background-color:#faf8f3;border-bottom:1px solid #e8e4dc;">
                                    <strong style="font-size:14px;color:#17263d;">
                                        Your Employee Details
                                    </strong>
                                </td>
                            </tr>

                            <tr>
                                <td width="40%" style="padding:14px 16px;font-size:13px;color:#70757d;border-bottom:1px solid #eeeae3;">
                                    Employee Code
                                </td>
                                <td style="padding:14px 16px;font-size:14px;font-weight:700;color:#17263d;border-bottom:1px solid #eeeae3;">
                                    {{ $employee->employee_code }}
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:14px 16px;font-size:13px;color:#70757d;border-bottom:1px solid #eeeae3;">
                                    Full Name
                                </td>
                                <td style="padding:14px 16px;font-size:14px;color:#263244;border-bottom:1px solid #eeeae3;">
                                    {{ $employee->name }}
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:14px 16px;font-size:13px;color:#70757d;">
                                    Email Address
                                </td>
                                <td style="padding:14px 16px;font-size:14px;color:#263244;word-break:break-word;">
                                    {{ $employee->email }}
                                </td>
                            </tr>

                        </table>

                        <!-- Information Note -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td style="background-color:#faf8f3;border:1px solid #e9e3d7;border-left:4px solid #b5965a;border-radius:3px;padding:16px;">
                                    <p style="margin:0 0 6px;font-size:14px;font-weight:700;color:#5a4a2d;">
                                        What's next?
                                    </p>
                                    <p style="margin:0;font-size:13px;line-height:1.8;color:#665a45;">
                                        If you have received a separate password setup email,
                                        please follow its instructions to activate your account
                                        and access the Employee Management System.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Closing -->
                        <p style="margin:24px 0 0;font-size:14px;line-height:1.8;color:#4f5661;">
                            We're excited to have you on board. Welcome once again!
                        </p>

                        <p style="margin:22px 0 0;font-size:14px;line-height:1.8;color:#4f5661;">
                            Regards,<br>
                            <strong style="color:#17263d;">Employee Management System</strong>
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center" style="padding:24px 12px 8px;">
                        <p style="margin:0 0 7px;font-size:12px;line-height:1.7;color:#747980;">
                            This is an automated welcome notification.
                        </p>
                        <p style="margin:0;font-size:12px;line-height:1.7;color:#999b9e;">
                            &copy; {{ date('Y') }} Employee Management System.
                            All rights reserved.
                        </p>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
