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

        $email->setFrom(
            env('email.fromEmail', ''),
            env('email.fromName', 'BIS')
        );

        $email->setTo($recipient);

        $email->setSubject(
            'BIS - Gmail SMTP Test'
        );

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
                'message' => 'Email failed to send.',
                'debug' => $email->printDebugger([
                    'headers',
                    'subject',
                    'body'
                ])
            ]);
    }
}