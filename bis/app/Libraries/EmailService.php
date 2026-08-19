<?php

namespace App\Libraries;

use Config\Email;

class EmailService
{
    protected $email;

    public function __construct()
    {
        $this->email = \Config\Services::email();
    }

    /**
     * Send registration verification OTP
     */
    public function sendVerificationEmail(
        string $recipient,
        string $name,
        string $otp
    ): bool {
        $subject = 'Barangay Information System - Email Verification';

        $message = $this->verificationTemplate(
            $name,
            $otp
        );

        return $this->send(
            $recipient,
            $name,
            $subject,
            $message
        );
    }

    /**
     * Send forgot-password OTP
     */
    public function sendPasswordResetOtp(
        string $recipient,
        string $name,
        string $otp
    ): bool {
        $subject = 'Barangay Information System - Password Reset Code';

        $message = $this->passwordResetTemplate(
            $name,
            $otp
        );

        return $this->send(
            $recipient,
            $name,
            $subject,
            $message
        );
    }

    /**
     * Common email sender
     */
    protected function send(
        string $recipient,
        string $name,
        string $subject,
        string $message
    ): bool {
        $this->email->clear(true);

        $this->email->setTo($recipient);

        $this->email->setFrom(
            config('Email')->fromEmail,
            config('Email')->fromName
        );

        $this->email->setSubject($subject);
        $this->email->setMessage($message);

        if (! $this->email->send()) {
            log_message(
                'error',
                'Email sending failed: ' . $this->email->printDebugger(
                    ['headers', 'subject', 'body']
                )
            );

            return false;
        }

        log_message(
            'info',
            'Email successfully sent to: ' . $recipient
        );

        return true;
    }

    /**
     * Registration verification email
     */
    protected function verificationTemplate(
        string $name,
        string $otp
    ): string {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Email Verification</title>
        </head>

        <body style="
            margin:0;
            padding:0;
            background:#f4f6f8;
            font-family:Arial,Helvetica,sans-serif;
        ">

            <div style="
                max-width:600px;
                margin:40px auto;
                background:#ffffff;
                border-radius:10px;
                padding:30px;
                box-shadow:0 2px 10px rgba(0,0,0,0.08);
            ">

                <h2 style="
                    color:#1f2937;
                    margin-top:0;
                ">
                    Barangay Information System
                </h2>

                <p>
                    Hello <strong>' . esc($name) . '</strong>,
                </p>

                <p>
                    Thank you for registering with the Barangay
                    Information System.
                </p>

                <p>
                    Use the verification code below to verify
                    your email address:
                </p>

                <div style="
                    text-align:center;
                    margin:30px 0;
                ">

                    <span style="
                        display:inline-block;
                        background:#f3f4f6;
                        border:1px solid #d1d5db;
                        border-radius:8px;
                        padding:18px 30px;
                        font-size:32px;
                        font-weight:bold;
                        letter-spacing:8px;
                        color:#111827;
                    ">
                        ' . esc($otp) . '
                    </span>

                </div>

                <p>
                    This verification code will expire in
                    <strong>15 minutes</strong>.
                </p>

                <p>
                    If you did not create this account,
                    you may safely ignore this email.
                </p>

                <hr style="
                    border:none;
                    border-top:1px solid #e5e7eb;
                    margin:30px 0;
                ">

                <p style="
                    font-size:12px;
                    color:#6b7280;
                ">
                    This is an automated message from the
                    Barangay Information System.
                    Please do not reply to this email.
                </p>

            </div>

        </body>
        </html>
        ';
    }

    /**
     * Password reset email
     */
    protected function passwordResetTemplate(
        string $name,
        string $otp
    ): string {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Password Reset</title>
        </head>

        <body style="
            margin:0;
            padding:0;
            background:#f4f6f8;
            font-family:Arial,Helvetica,sans-serif;
        ">

            <div style="
                max-width:600px;
                margin:40px auto;
                background:#ffffff;
                border-radius:10px;
                padding:30px;
                box-shadow:0 2px 10px rgba(0,0,0,0.08);
            ">

                <h2 style="color:#1f2937;">
                    Password Reset
                </h2>

                <p>
                    Hello <strong>' . esc($name) . '</strong>,
                </p>

                <p>
                    We received a request to reset your
                    Barangay Information System password.
                </p>

                <p>
                    Your password reset code is:
                </p>

                <div style="
                    text-align:center;
                    margin:30px 0;
                ">

                    <span style="
                        display:inline-block;
                        background:#f3f4f6;
                        border:1px solid #d1d5db;
                        border-radius:8px;
                        padding:18px 30px;
                        font-size:32px;
                        font-weight:bold;
                        letter-spacing:8px;
                        color:#111827;
                    ">
                        ' . esc($otp) . '
                    </span>

                </div>

                <p>
                    This code will expire in
                    <strong>15 minutes</strong>.
                </p>

                <p>
                    If you did not request a password reset,
                    please ignore this email.
                </p>

                <hr style="
                    border:none;
                    border-top:1px solid #e5e7eb;
                    margin:30px 0;
                ">

                <p style="
                    font-size:12px;
                    color:#6b7280;
                ">
                    This is an automated message from the
                    Barangay Information System.
                </p>

            </div>

        </body>
        </html>
        ';
    }
}