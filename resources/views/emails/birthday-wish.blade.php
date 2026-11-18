<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Happy Birthday!</title>
</head>
<body style="margin:0; padding:30px 15px; background:#f3f4f6; font-family:Arial,sans-serif;">

    <div style="max-width:600px; margin:auto; background:#ffffff; border-radius:12px; overflow:hidden;">

        <div style="background:#4f46e5; padding:35px 20px; text-align:center; color:#ffffff;">
            <div style="font-size:45px;">🎂</div>
            <h1 style="margin:15px 0 0; font-size:28px;">
                Happy Birthday!
            </h1>
            <p style="margin:10px 0 0; font-size:15px;">
                A special day for a special team member
            </p>
        </div>

        <div style="padding:35px 30px; text-align:center; color:#374151;">

            <h2 style="color:#111827; font-size:24px;">
                Dear {{ $employee->name }},
            </h2>

            <p style="font-size:16px; line-height:1.7;">
                Wishing you a very Happy Birthday! 🎉
            </p>

            <p style="font-size:15px; line-height:1.8; color:#6b7280;">
                May your special day be filled with happiness,
                success, good health, and wonderful memories.
            </p>

            <p style="font-size:16px; line-height:1.7;">
                We are happy to have you as part of our team.
                Have an amazing year ahead!
            </p>

            <div style="margin:30px 0; padding:18px; background:#eef2ff; border-radius:8px;">
                <strong style="color:#4338ca; font-size:17px;">
                    Best Wishes from the HR Team
                </strong>
            </div>

        </div>

        <div style="padding:18px; background:#f9fafb; text-align:center; color:#9ca3af; font-size:12px;">
            Employee Management System<br>
            This is an automated birthday greeting.
        </div>

    </div>

</body>
</html>
