<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class EmailTestController extends Controller
{
    public function index()
    {
        $recipient = 'johnroderick99@gmail.com';

        $email = Services::email();

        $email->clear();

        $fromEmail = env('email.fromEmail');
        $fromName  = env('email.fromName', 'BIS');

        // Make sure the sender email is configured
        if (empty($fromEmail)) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Email configuration error: sender email is not configured.'
                ]);
        }

        $email->setFrom($fromEmail, $fromName);
        $email->setTo($recipient);
        $email->setSubject('BIS - Gmail SMTP Test');

        $email->setMessage('
            <!DOCTYPE html>
            <html>
            <body style="
                font-family: Arial, sans-serif;
                padding: 30px;
            ">

                <h2>Barangay Information System</h2>

                <p>
                    This is a test email from the BIS.
                </p>

                <p>
                    If you received this message,
                    your SMTP configuration is working.
                </p>

                <p>
                    <strong>Email system test successful.</strong>
                </p>

            </body>
            </html>
        ');

        if ($email->send()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Email sent successfully.'
            ]);
        }

        return $this->response
            ->setStatusCode(500)
            ->setJSON([
                'success' => false,
                'message' => 'Email failed to send. Please check the SMTP configuration.'
            ]);
    }
}