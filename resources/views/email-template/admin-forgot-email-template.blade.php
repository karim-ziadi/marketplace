<p>Dear {{ $admin->name }}</p>
<p>
    We are received a request to reset the password for Marketplace account associated with {{ $admin->email }}.
    You can reset your password by clicking the button beliw:
    <br>
    <a href="{{ $actionLink }}" target="_blank" style="color: wheat;border-color:#22bc66;boder-style:solid;border-width:5px 10px;background-color:#22bc66;display:inline-block;text-decoration:none;border-radius:3px;box-shadow:0 2px 3px rgba(0,0,0,0.16);webkit-text-adjust:none;border-sizing:border-box">Reset password</a>
    <br>
    <b>Nb:</b> This link will valid within 15 minutes
    <br>
    if you did not request for a password reset,pleasr ignore this email.
</p>
