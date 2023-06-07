<x-mail::message>
# Forgot Password Reset Link

Dear {{ $data['email'] }},

You have requested for a password reset link. Click the below button.

<x-mail::button :url="$data['reset_password_link']">
Reset Password
</x-mail::button>

Alternatively, if the above button doesn't work, please copy the following link and paste it in your browser.

{!! $data['reset_password_link'] !!}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
