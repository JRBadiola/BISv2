<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Libraries\EmailService;

class AuthController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // =========================================================================
    // LOGIN
    // =========================================================================

    public function login()
    {
        $username = trim($this->request->getPost('username') ?? '');
        $password = $this->request->getPost('password') ?? '';

        if (empty($username) || empty($password)) {
            return redirect()
                ->to('/login')
                ->with('error', 'Please enter your username and password.');
        }

        $user = $this->userModel->findByCredentials($username, $password);

        if (! $user) {
            return redirect()
                ->to('/login')
                ->with('error', 'Invalid username or password.');
        }

        // ---------------------------------------------------------------------
        // EMAIL VERIFICATION
        // ---------------------------------------------------------------------

        if (! $user['email_verified']) {
            return redirect()
                ->to('/login')
                ->with(
                    'error',
                    'Please verify your email address first. Check your inbox for the verification code.'
                );
        }

        // ---------------------------------------------------------------------
        // PENDING ACCOUNT
        // ---------------------------------------------------------------------

        if ($user['status'] === 'pending') {
            return redirect()
                ->to('/login')
                ->with(
                    'error',
                    'Your account is pending approval by the barangay secretary.'
                );
        }

        // ---------------------------------------------------------------------
        // REJECTED ACCOUNT
        // ---------------------------------------------------------------------

        if ($user['status'] === 'rejected') {
            return redirect()
                ->to('/login')
                ->with(
                    'error',
                    'Your account registration was not approved. Please contact the barangay office.'
                );
        }

        // ---------------------------------------------------------------------
        // COMPOSE DISPLAY NAME
        // ---------------------------------------------------------------------

        $displayName = trim(
            $user['first_name'] . ' ' .
            ($user['middle_name'] ? $user['middle_name'] . ' ' : '') .
            $user['last_name']
        );

        // ---------------------------------------------------------------------
        // SESSION
        // ---------------------------------------------------------------------

        session()->set([
            'user_id'     => $user['id'],
            'username'    => $user['username'],
            'last_name'   => $user['last_name'],
            'first_name'  => $user['first_name'],
            'middle_name' => $user['middle_name'] ?? '',
            'full_name'   => $displayName,
            'role'        => $user['role'],
            'avatar'      => $user['avatar'] ?? null,
            'isLoggedIn'  => true,
        ]);

        return redirect()->to('/' . $user['role'] . '/dashboard');
    }

    // =========================================================================
    // LOGOUT
    // =========================================================================

    public function logout()
    {
        session()->destroy();

        return redirect()
            ->to('/login')
            ->with('success', 'You have been logged out.');
    }

    // =========================================================================
    // REGISTRATION
    // =========================================================================

    public function register()
    {
        $role = strtolower(
            trim($this->request->getPost('role') ?? '')
        );

        // Only resident and SK can self-register
        $allowedRoles = ['resident', 'sk'];

        if (! in_array($role, $allowedRoles, true)) {
            return redirect()
                ->to('/signup')
                ->with(
                    'error',
                    'That role cannot be self-registered. Please contact the barangay office.'
                );
        }

        // ---------------------------------------------------------------------
        // GET FORM DATA
        // ---------------------------------------------------------------------

        $lastName = trim(
            $this->request->getPost('last_name') ?? ''
        );

        $firstName = trim(
            $this->request->getPost('first_name') ?? ''
        );

        $middleName = trim(
            $this->request->getPost('middle_name') ?? ''
        );

        $email = trim(
            $this->request->getPost('email') ?? ''
        );

        $username = trim(
            $this->request->getPost('username') ?? ''
        );

        $password = $this->request->getPost('password') ?? '';

        $confirmPassword = $this->request->getPost('confirm_password') ?? '';

        // ---------------------------------------------------------------------
        // BASIC VALIDATION
        // ---------------------------------------------------------------------

        if (
            empty($lastName) ||
            empty($firstName) ||
            empty($email) ||
            empty($username) ||
            empty($password)
        ) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Please complete all required fields.'
                )
                ->withInput();
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Please enter a valid email address.'
                )
                ->withInput();
        }

        if ($password !== $confirmPassword) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Passwords do not match.'
                )
                ->withInput();
        }

        if (strlen($password) < 8) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Password must be at least 8 characters.'
                )
                ->withInput();
        }

        // ---------------------------------------------------------------------
        // CHECK DUPLICATE EMAIL
        // ---------------------------------------------------------------------

        $existingEmail = $this->userModel
            ->where('email', $email)
            ->first();

        if ($existingEmail) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'That email address is already registered.'
                )
                ->withInput();
        }

        // ---------------------------------------------------------------------
        // CHECK DUPLICATE USERNAME
        // ---------------------------------------------------------------------

        $existingUsername = $this->userModel
            ->where('username', $username)
            ->first();

        if ($existingUsername) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'That username is already taken.'
                )
                ->withInput();
        }

        // =========================================================================
        // RESIDENT CENSUS VERIFICATION
        // =========================================================================

        $householdNo = null;

        if ($role === 'resident') {

            $householdNo = trim(
                $this->request->getPost('household_no') ?? ''
            );

            if (empty($householdNo)) {
                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'Household number is required for resident registration.'
                    )
                    ->withInput();
            }

            // -----------------------------------------------------------------
            // FIND HOUSEHOLD
            // -----------------------------------------------------------------

            $householdModel = new \App\Models\HouseholdModel();

            $household = $householdModel->find($householdNo);

            if (! $household) {
                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'Household number ' .
                        esc($householdNo) .
                        ' was not found in the census. Please check your household number or contact the barangay office.'
                    )
                    ->withInput();
            }

            // -----------------------------------------------------------------
            // PREPARE ENTERED NAME
            // -----------------------------------------------------------------

            $enteredFull = strtoupper(
                trim($firstName . ' ' . $lastName)
            );

            $enteredFullAlt = strtoupper(
                trim($lastName . ' ' . $firstName)
            );

            // -----------------------------------------------------------------
            // CHECK HOUSEHOLD HEAD
            // -----------------------------------------------------------------

            $headFull = strtoupper(
                trim(
                    ($household['first_name'] ?? '') .
                    ' ' .
                    ($household['last_name'] ?? '')
                )
            );

            $headFullAlt = strtoupper(
                trim(
                    ($household['last_name'] ?? '') .
                    ' ' .
                    ($household['first_name'] ?? '')
                )
            );

            $nameFound =
                $enteredFull === $headFull ||
                $enteredFull === $headFullAlt ||
                $enteredFullAlt === $headFull ||
                $enteredFullAlt === $headFullAlt;

            // -----------------------------------------------------------------
            // CHECK HOUSEHOLD MEMBERS
            // -----------------------------------------------------------------

            if (! $nameFound) {

                $memberModel = new \App\Models\HouseholdMemberModel();

                $members = $memberModel
                    ->where('household_no', $householdNo)
                    ->findAll();

                foreach ($members as $member) {

                    $memberFull = strtoupper(
                        trim(
                            ($member['first_name'] ?? '') .
                            ' ' .
                            ($member['last_name'] ?? '')
                        )
                    );

                    $memberFullAlt = strtoupper(
                        trim(
                            ($member['last_name'] ?? '') .
                            ' ' .
                            ($member['first_name'] ?? '')
                        )
                    );

                    if (
                        $enteredFull === $memberFull ||
                        $enteredFull === $memberFullAlt ||
                        $enteredFullAlt === $memberFull ||
                        $enteredFullAlt === $memberFullAlt
                    ) {
                        $nameFound = true;
                        break;
                    }
                }
            }

            // -----------------------------------------------------------------
            // NAME NOT FOUND
            // -----------------------------------------------------------------

            if (! $nameFound) {
                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'Your name does not match any member recorded under Household #' .
                        esc($householdNo) .
                        '. Please check your name and household number, or contact the barangay office.'
                    )
                    ->withInput();
            }
        }

        // =========================================================================
        // GENERATE OTP
        // =========================================================================

        $otp = strval(
            random_int(100000, 999999)
        );

        $expires = date(
            'Y-m-d H:i:s',
            strtotime('+15 minutes')
        );

        // =========================================================================
        // SAVE USER
        // =========================================================================

        $saved = $this->userModel->save([
            'last_name'            => $lastName,
            'first_name'           => $firstName,
            'middle_name'          => $middleName ?: null,
            'email'                => $email,
            'username'             => $username,
            'password'             => password_hash(
                $password,
                PASSWORD_BCRYPT
            ),
            'role'                 => $role,
            'status'               => 'unverified',
            'email_verified'       => 0,
            'verify_token'         => $otp,
            'verify_token_expires' => $expires,
            'household_no'         => $householdNo,
        ]);

        if (! $saved) {

            $errors = implode(
                ' ',
                $this->userModel->errors()
            );

            return redirect()
                ->back()
                ->with(
                    'error',
                    $errors ?: 'Unable to create your account.'
                )
                ->withInput();
        }

        // =========================================================================
        // SEND VERIFICATION EMAIL
        // =========================================================================

        $displayName = trim(
            $firstName . ' ' . $lastName
        );

        try {

            $emailService = new EmailService();

            $sent = $emailService->sendVerificationEmail(
                $email,
                $displayName,
                $otp
            );

            if (! $sent) {

                log_message(
                    'error',
                    'Verification email failed for: ' . $email
                );

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'Your account was created, but we could not send the verification email. Please check your email address or contact the barangay office.'
                    )
                    ->withInput();
            }

        } catch (\Throwable $e) {

            log_message(
                'error',
                'Verification email exception: ' .
                $e->getMessage()
            );

            log_message(
                'error',
                $e->getTraceAsString()
            );

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Your account was created, but there was a problem sending the verification email.'
                )
                ->withInput();
        }

        // =========================================================================
        // STORE PENDING EMAIL IN SESSION
        // =========================================================================

        session()->set(
            'pending_verify_email',
            $email
        );

        return redirect()->to('/verify-email');
    }

    // =========================================================================
    // SHOW EMAIL VERIFICATION PAGE
    // =========================================================================

    public function showVerifyEmail()
    {
        $email = session()->get(
            'pending_verify_email'
        );

        if (! $email) {
            return redirect()
                ->to('/login');
        }

        return view('verify_email');
    }

    // =========================================================================
    // VERIFY EMAIL OTP
    // =========================================================================

    public function verifyEmail()
    {
        $email = session()->get(
            'pending_verify_email'
        );

        if (! $email) {
            return redirect()
                ->to('/login')
                ->with(
                    'error',
                    'Session expired. Please register again.'
                );
        }

        $enteredOtp = trim(
            $this->request->getPost('otp') ?? ''
        );

        if (
            empty($enteredOtp) ||
            ! preg_match('/^\d{6}$/', $enteredOtp)
        ) {
            return redirect()
                ->to('/verify-email')
                ->with(
                    'error',
                    'Please enter the 6-digit verification code.'
                );
        }

        // ---------------------------------------------------------------------
        // FIND USER
        // ---------------------------------------------------------------------

        $user = $this->userModel
            ->where('email', $email)
            ->where('email_verified', 0)
            ->first();

        if (! $user) {
            return redirect()
                ->to('/login')
                ->with(
                    'error',
                    'Account not found or already verified.'
                );
        }

        // ---------------------------------------------------------------------
        // CHECK EXPIRATION
        // ---------------------------------------------------------------------

        if (
            empty($user['verify_token_expires']) ||
            strtotime($user['verify_token_expires']) < time()
        ) {
            return redirect()
                ->to('/verify-email')
                ->with(
                    'error',
                    'Your verification code has expired. Please request a new code.'
                );
        }

        // ---------------------------------------------------------------------
        // CHECK OTP
        // ---------------------------------------------------------------------

        if (
            ! hash_equals(
                (string) $user['verify_token'],
                (string) $enteredOtp
            )
        ) {
            return redirect()
                ->to('/verify-email')
                ->with(
                    'error',
                    'Incorrect verification code. Please try again.'
                );
        }

        // ---------------------------------------------------------------------
        // MARK VERIFIED
        // ---------------------------------------------------------------------

        $this->userModel->markEmailVerified(
            $user['id']
        );

        session()->remove(
            'pending_verify_email'
        );

        return redirect()
            ->to('/login')
            ->with(
                'success',
                'Email verified! Your account is now pending approval by the barangay captain or secretary.'
            );
    }

    // =========================================================================
    // RESEND REGISTRATION OTP
    // =========================================================================

    public function resendOtp()
    {
        $email = session()->get(
            'pending_verify_email'
        );

        if (! $email) {
            return redirect()
                ->to('/login')
                ->with(
                    'error',
                    'Session expired. Please register again.'
                );
        }

        $user = $this->userModel
            ->where('email', $email)
            ->where('email_verified', 0)
            ->first();

        if (! $user) {
            return redirect()
                ->to('/login')
                ->with(
                    'error',
                    'Account not found or already verified.'
                );
        }

        // ---------------------------------------------------------------------
        // NEW OTP
        // ---------------------------------------------------------------------

        $otp = strval(
            random_int(100000, 999999)
        );

        $expires = date(
            'Y-m-d H:i:s',
            strtotime('+15 minutes')
        );

        $updated = $this->userModel->update(
            $user['id'],
            [
                'verify_token'         => $otp,
                'verify_token_expires' => $expires,
            ]
        );

        if (! $updated) {
            return redirect()
                ->to('/verify-email')
                ->with(
                    'error',
                    'Could not generate a new verification code.'
                );
        }

        $displayName = trim(
            $user['first_name'] .
            ' ' .
            $user['last_name']
        );

        // ---------------------------------------------------------------------
        // SEND EMAIL
        // ---------------------------------------------------------------------

        try {

            $emailService = new EmailService();

            $sent = $emailService->sendVerificationEmail(
                $email,
                $displayName,
                $otp
            );

            if (! $sent) {

                log_message(
                    'error',
                    'Resend verification email failed for: ' .
                    $email
                );

                return redirect()
                    ->to('/verify-email')
                    ->with(
                        'error',
                        'Could not send the verification code. Please try again.'
                    );
            }

        } catch (\Throwable $e) {

            log_message(
                'error',
                'Resend OTP exception: ' .
                $e->getMessage()
            );

            return redirect()
                ->to('/verify-email')
                ->with(
                    'error',
                    'Could not send the verification code. Please try again.'
                );
        }

        return redirect()
            ->to('/verify-email')
            ->with(
                'success',
                'A new verification code has been sent to your email.'
            );
    }

    // =========================================================================
    // FORGOT PASSWORD - SHOW FORM
    // =========================================================================

    public function showForgotPassword()
    {
        return view('forgot_password');
    }

    // =========================================================================
    // FORGOT PASSWORD - SEND OTP
    // =========================================================================

    public function sendForgotPasswordOtp()
    {
        $email = trim(
            $this->request->getPost('email') ?? ''
        );

        if (empty($email)) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Please enter your email address.'
                );
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Please enter a valid email address.'
                );
        }

        // ---------------------------------------------------------------------
        // FIND USER
        // ---------------------------------------------------------------------

        $user = $this->userModel
            ->select(
                'id, last_name, first_name, middle_name, email, status, email_verified'
            )
            ->where('email', $email)
            ->first();

        // ---------------------------------------------------------------------
        // ONLY SEND IF VALID ACTIVE USER
        // ---------------------------------------------------------------------

        if (
            $user &&
            $user['email_verified'] &&
            $user['status'] === 'active'
        ) {

            $otp = strval(
                random_int(100000, 999999)
            );

            $expires = date(
                'Y-m-d H:i:s',
                strtotime('+15 minutes')
            );

            $updated = $this->userModel->update(
                $user['id'],
                [
                    'verify_token'         => $otp,
                    'verify_token_expires' => $expires,
                ]
            );

            if (! $updated) {

                log_message(
                    'error',
                    'Could not save forgot-password OTP for user ID: ' .
                    $user['id']
                );
            } else {

                $displayName = trim(
                    $user['first_name'] .
                    ' ' .
                    $user['last_name']
                );

                try {

                    $emailService = new EmailService();

                    $sent = $emailService->sendPasswordResetOtp(
                        $user['email'],
                        $displayName,
                        $otp
                    );

                    if (! $sent) {
                        log_message(
                            'error',
                            'Password reset email failed for: ' .
                            $user['email']
                        );
                    }

                } catch (\Throwable $e) {

                    log_message(
                        'error',
                        'Forgot password email exception: ' .
                        $e->getMessage()
                    );
                }
            }
        }

        // ---------------------------------------------------------------------
        // ALWAYS STORE EMAIL
        //
        // This prevents revealing whether an account exists.
        // ---------------------------------------------------------------------

        session()->set(
            'fp_email',
            $email
        );

        return redirect()
            ->to('/forgot-password/verify')
            ->with(
                'success',
                'If that email is registered, a reset code has been sent.'
            );
    }

    // =========================================================================
    // FORGOT PASSWORD - SHOW OTP
    // =========================================================================

    public function showForgotPasswordOtp()
    {
        if (! session()->get('fp_email')) {
            return redirect()
                ->to('/forgot-password');
        }

        return view('reset_password_otp');
    }

    // =========================================================================
    // FORGOT PASSWORD - VERIFY OTP
    // =========================================================================

    public function verifyForgotPasswordOtp()
    {
        $email = session()->get(
            'fp_email'
        );

        if (! $email) {
            return redirect()
                ->to('/forgot-password');
        }

        $otp = trim(
            $this->request->getPost('otp') ?? ''
        );

        if (
            empty($otp) ||
            ! preg_match('/^\d{6}$/', $otp)
        ) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Please enter the 6-digit verification code.'
                );
        }

        $user = $this->userModel
            ->select(
                'id, verify_token, verify_token_expires'
            )
            ->where('email', $email)
            ->first();

        if (! $user) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Incorrect code. Please try again.'
                );
        }

        // ---------------------------------------------------------------------
        // CHECK EXPIRATION
        // ---------------------------------------------------------------------

        if (
            empty($user['verify_token_expires']) ||
            strtotime($user['verify_token_expires']) < time()
        ) {

            session()->remove([
                'fp_email',
                'fp_verified',
                'fp_user_id',
            ]);

            return redirect()
                ->to('/forgot-password')
                ->with(
                    'error',
                    'Your reset code has expired. Please request a new one.'
                );
        }

        // ---------------------------------------------------------------------
        // CHECK OTP
        // ---------------------------------------------------------------------

        if (
            empty($user['verify_token']) ||
            ! hash_equals(
                (string) $user['verify_token'],
                (string) $otp
            )
        ) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Incorrect code. Please try again.'
                );
        }

        // ---------------------------------------------------------------------
        // OTP VALID
        // ---------------------------------------------------------------------

        session()->set([
            'fp_verified' => true,
            'fp_user_id'  => $user['id'],
        ]);

        // Prevent OTP reuse
        $this->userModel->update(
            $user['id'],
            [
                'verify_token'         => null,
                'verify_token_expires' => null,
            ]
        );

        return redirect()
            ->to('/forgot-password/new-password');
    }

    // =========================================================================
    // FORGOT PASSWORD - RESEND OTP
    // =========================================================================

    public function resendForgotPasswordOtp()
    {
        $email = session()->get(
            'fp_email'
        );

        if (! $email) {
            return redirect()
                ->to('/forgot-password');
        }

        $user = $this->userModel
            ->select(
                'id, last_name, first_name, email, status, email_verified'
            )
            ->where('email', $email)
            ->first();

        if (
            $user &&
            $user['email_verified'] &&
            $user['status'] === 'active'
        ) {

            $otp = strval(
                random_int(100000, 999999)
            );

            $expires = date(
                'Y-m-d H:i:s',
                strtotime('+15 minutes')
            );

            $updated = $this->userModel->update(
                $user['id'],
                [
                    'verify_token'         => $otp,
                    'verify_token_expires' => $expires,
                ]
            );

            if (! $updated) {
                return redirect()
                    ->to('/forgot-password/verify')
                    ->with(
                        'error',
                        'Could not generate a new reset code.'
                    );
            }

            $displayName = trim(
                $user['first_name'] .
                ' ' .
                $user['last_name']
            );

            try {

                $emailService = new EmailService();

                $sent = $emailService->sendPasswordResetOtp(
                    $user['email'],
                    $displayName,
                    $otp
                );

                if (! $sent) {

                    log_message(
                        'error',
                        'Resend forgot-password email failed for: ' .
                        $user['email']
                    );

                    return redirect()
                        ->to('/forgot-password/verify')
                        ->with(
                            'error',
                            'Could not send the reset code. Please try again.'
                        );
                }

            } catch (\Throwable $e) {

                log_message(
                    'error',
                    'Resend forgot password exception: ' .
                    $e->getMessage()
                );

                return redirect()
                    ->to('/forgot-password/verify')
                    ->with(
                        'error',
                        'Could not send the reset code. Please try again.'
                    );
            }
        }

        return redirect()
            ->to('/forgot-password/verify')
            ->with(
                'success',
                'If that email is registered, a new reset code has been sent.'
            );
    }

    // =========================================================================
    // FORGOT PASSWORD - SHOW NEW PASSWORD
    // =========================================================================

    public function showNewPassword()
    {
        if (
            ! session()->get('fp_verified') ||
            ! session()->get('fp_user_id')
        ) {
            return redirect()
                ->to('/forgot-password');
        }

        return view('reset_password_new');
    }

    // =========================================================================
    // FORGOT PASSWORD - SAVE NEW PASSWORD
    // =========================================================================

    public function saveNewPassword()
    {
        if (
            ! session()->get('fp_verified') ||
            ! session()->get('fp_user_id')
        ) {
            return redirect()
                ->to('/forgot-password');
        }

        $newPassword = $this->request
            ->getPost('new_password') ?? '';

        $confirmPassword = $this->request
            ->getPost('confirm_password') ?? '';

        if (strlen($newPassword) < 8) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Password must be at least 8 characters.'
                );
        }

        if ($newPassword !== $confirmPassword) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Passwords do not match.'
                );
        }

        $userId = (int) session()->get(
            'fp_user_id'
        );

        $updated = $this->userModel->update(
            $userId,
            [
                'password' => password_hash(
                    $newPassword,
                    PASSWORD_BCRYPT
                ),
            ]
        );

        if (! $updated) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Unable to update your password. Please try again.'
                );
        }

        // ---------------------------------------------------------------------
        // CLEAR FORGOT PASSWORD SESSION
        // ---------------------------------------------------------------------

        session()->remove([
            'fp_email',
            'fp_verified',
            'fp_user_id',
        ]);

        return redirect()
            ->to('/login')
            ->with(
                'success',
                'Password reset successfully. You can now sign in with your new password.'
            );
    }

    // =========================================================================
    // PENDING ACCOUNTS
    // =========================================================================

    public function pendingAccounts()
    {
        $pending = $this->userModel
            ->getPendingAccounts();

        $role = session()->get('role');

        return view(
            'dashboard/' . $role . '/pending_accounts',
            [
                'pending' => $pending,
            ]
        );
    }

    // =========================================================================
    // APPROVE ACCOUNT
    // =========================================================================

    public function approveAccount(int $id)
    {
        $this->userModel->approveUser($id);

        $role = session()->get('role');

        return redirect()
            ->to('/' . $role . '/pending-accounts')
            ->with(
                'success',
                'Account approved successfully.'
            );
    }

    // =========================================================================
    // REJECT ACCOUNT
    // =========================================================================

    public function rejectAccount(int $id)
    {
        $this->userModel->rejectUser($id);

        $role = session()->get('role');

        return redirect()
            ->to('/' . $role . '/pending-accounts')
            ->with(
                'success',
                'Account rejected.'
            );
    }

    // =========================================================================
    // PROMOTE RESIDENT
    // =========================================================================

    public function promoteResident()
    {
        if (session()->get('role') !== 'secretary') {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Unauthorized.'
                );
        }

        $targetId = (int) $this->request
            ->getPost('user_id');

        $newRole = strtolower(
            trim(
                $this->request->getPost('role') ?? ''
            )
        );

        $allowed = [
            'captain',
            'secretary',
            'sk',
        ];

        if (! in_array($newRole, $allowed, true)) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Invalid role for promotion.'
                )
                ->withInput();
        }

        // ---------------------------------------------------------------------
        // FIND TARGET
        // ---------------------------------------------------------------------

        $target = $this->userModel
            ->find($targetId);

        if (
            ! $target ||
            $target['role'] !== 'resident' ||
            $target['status'] !== 'active'
        ) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Selected user is not an eligible active resident.'
                )
                ->withInput();
        }

        // ---------------------------------------------------------------------
        // AGE CHECK
        // ---------------------------------------------------------------------

        if (! empty($target['household_no'])) {

            $db = \Config\Database::connect();

            $memberRow = $db
                ->table('household_members')
                ->where(
                    'household_no',
                    $target['household_no']
                )
                ->where(
                    'UPPER(TRIM(first_name))',
                    strtoupper(
                        trim(
                            $target['first_name'] ?? ''
                        )
                    )
                )
                ->where(
                    'UPPER(TRIM(last_name))',
                    strtoupper(
                        trim(
                            $target['last_name'] ?? ''
                        )
                    )
                )
                ->get()
                ->getRowArray();

            $householdRow = $db
                ->table('households')
                ->where(
                    'household_no',
                    $target['household_no']
                )
                ->get()
                ->getRowArray();

            $dob = ! empty(
                $memberRow['date_of_birth'] ?? null
            )
                ? $memberRow['date_of_birth']
                : (
                    $householdRow['date_of_birth'] ?? null
                );

            if (! empty($dob)) {

                $age = (int) date_diff(
                    date_create($dob),
                    date_create('today')
                )->y;

                if ($age < 18) {
                    return redirect()
                        ->back()
                        ->with(
                            'error',
                            'The selected resident must be at least 18 years old.'
                        )
                        ->withInput();
                }
            }
        }

        // =========================================================================
        // SINGLE INSTANCE CHECK
        // =========================================================================

        if (
            in_array(
                $newRole,
                ['captain', 'secretary'],
                true
            )
        ) {

            // ---------------------------------------------------------------------
            // SECRETARY
            // ---------------------------------------------------------------------

            if ($newRole === 'secretary') {

                if (
                    session()->get('username') !==
                    'secretary_admin'
                ) {
                    return redirect()
                        ->back()
                        ->with(
                            'error',
                            'Only the default secretary admin can assign another secretary.'
                        )
                        ->withInput();
                }

                $existingNonAdmin = $this->userModel
                    ->where('role', 'secretary')
                    ->where('status', 'active')
                    ->where(
                        'username !=',
                        'secretary_admin'
                    )
                    ->first();

                if ($existingNonAdmin) {

                    $existingName = trim(
                        $existingNonAdmin['first_name'] .
                        ' ' .
                        $existingNonAdmin['last_name']
                    );

                    return redirect()
                        ->back()
                        ->with(
                            'error',
                            'An active Secretary already exists (' .
                            esc($existingName) .
                            '). Demote them first before promoting someone else.'
                        )
                        ->withInput();
                }

                if (
                    (int) session()->get('user_id') ===
                    $targetId
                ) {
                    return redirect()
                        ->back()
                        ->with(
                            'error',
                            'You cannot promote your own account.'
                        )
                        ->withInput();
                }
            }

            // ---------------------------------------------------------------------
            // CAPTAIN
            // ---------------------------------------------------------------------

            else {

                $existing = $this->userModel
                    ->getActiveByRole($newRole);

                if ($existing) {

                    $existingName = trim(
                        $existing['first_name'] .
                        ' ' .
                        $existing['last_name']
                    );

                    return redirect()
                        ->back()
                        ->with(
                            'error',
                            'An active ' .
                            ucfirst($newRole) .
                            ' already exists (' .
                            esc($existingName) .
                            '). Demote them first before promoting someone else.'
                        )
                        ->withInput();
                }
            }
        }

        // ---------------------------------------------------------------------
        // PROMOTE
        // ---------------------------------------------------------------------

        $this->userModel->update(
            $targetId,
            [
                'role' => $newRole,
            ]
        );

        $targetName = trim(
            $target['first_name'] .
            ' ' .
            $target['last_name']
        );

        return redirect()
            ->to('/secretary/create-account')
            ->with(
                'success',
                esc($targetName) .
                ' has been promoted to ' .
                ucfirst($newRole) .
                ' and can now access the ' .
                ucfirst($newRole) .
                ' dashboard.'
            );
    }

    // =========================================================================
    // DEMOTE OFFICIAL
    // =========================================================================

    public function demoteOfficial(int $targetId)
    {
        if (session()->get('role') !== 'secretary') {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Unauthorized.'
                );
        }

        // ---------------------------------------------------------------------
        // BLOCK SELF DEMOTION
        // ---------------------------------------------------------------------

        if (
            (int) session()->get('user_id') ===
            $targetId
        ) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'You cannot demote your own account.'
                );
        }

        $target = $this->userModel
            ->find($targetId);

        if (! $target) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'User not found.'
                );
        }

        // ---------------------------------------------------------------------
        // PROTECT DEFAULT SECRETARY
        // ---------------------------------------------------------------------

        if (
            ! empty($target['username']) &&
            $target['username'] === 'secretary_admin'
        ) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'The default secretary account cannot be demoted or revoked.'
                );
        }

        $officialRoles = [
            'captain',
            'secretary',
            'sk',
        ];

        if (
            ! in_array(
                $target['role'],
                $officialRoles,
                true
            )
        ) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'This user does not hold an official role.'
                );
        }

        $oldRole = $target['role'];

        $targetName = trim(
            $target['first_name'] .
            ' ' .
            $target['last_name']
        );

        // ---------------------------------------------------------------------
        // DEMOTE
        // ---------------------------------------------------------------------

        $this->userModel->update(
            $targetId,
            [
                'role' => 'resident',
            ]
        );

        return redirect()
            ->to('/secretary/create-account')
            ->with(
                'success',
                esc($targetName) .
                ' has been demoted from ' .
                ucfirst($oldRole) .
                ' back to Resident.'
            );
    }

    // =========================================================================
    // CREATE OFFICIAL ACCOUNT
    // =========================================================================

    public function createOfficialAccount()
    {
        $callerRole = session()->get('role');

        $role = strtolower(
            trim(
                $this->request->getPost('role') ?? ''
            )
        );

        // ---------------------------------------------------------------------
        // ALLOWED ROLES
        // ---------------------------------------------------------------------

        $allowedByRole = [
            'secretary' => [
                'captain',
                'resident',
                'sk',
            ],

            'captain' => [
                'secretary',
                'treasurer',
            ],
        ];

        $allowed =
            $allowedByRole[$callerRole] ?? [];

        if (! in_array($role, $allowed, true)) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Invalid role selected.'
                )
                ->withInput();
        }

        // ---------------------------------------------------------------------
        // SECRETARY ACCOUNT RESTRICTION
        // ---------------------------------------------------------------------

        if (
            $role === 'secretary' &&
            session()->get('username') !==
            'secretary_admin'
        ) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Only the default secretary admin can create a Secretary account.'
                )
                ->withInput();
        }

        // =========================================================================
        // SINGLE INSTANCE
        // =========================================================================

        if (
            in_array(
                $role,
                ['captain', 'secretary'],
                true
            )
        ) {

            // ---------------------------------------------------------------------
            // SECRETARY
            // ---------------------------------------------------------------------

            if ($role === 'secretary') {

                $existingNonAdmin = $this->userModel
                    ->where('role', 'secretary')
                    ->where('status', 'active')
                    ->where(
                        'username !=',
                        'secretary_admin'
                    )
                    ->first();

                if ($existingNonAdmin) {

                    $existingName = trim(
                        $existingNonAdmin['first_name'] .
                        ' ' .
                        $existingNonAdmin['last_name']
                    );

                    return redirect()
                        ->back()
                        ->with(
                            'error',
                            'An active Secretary account already exists (' .
                            esc($existingName) .
                            '). You must deactivate that account before creating a new one.'
                        )
                        ->withInput();
                }
            }

            // ---------------------------------------------------------------------
            // CAPTAIN
            // ---------------------------------------------------------------------

            else {

                $existing = $this->userModel
                    ->getActiveByRole($role);

                if ($existing) {

                    $existingName = trim(
                        $existing['first_name'] .
                        ' ' .
                        $existing['last_name']
                    );

                    return redirect()
                        ->back()
                        ->with(
                            'error',
                            'An active ' .
                            ucfirst($role) .
                            ' account already exists (' .
                            esc($existingName) .
                            '). You must deactivate that account before creating a new one.'
                        )
                        ->withInput();
                }
            }
        }

        // =========================================================================
        // PASSWORD
        // =========================================================================

        $password =
            $this->request->getPost('password') ?? '';

        $confirmPassword =
            $this->request->getPost('confirm_password') ?? '';

        if ($password !== $confirmPassword) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Passwords do not match.'
                )
                ->withInput();
        }

        if (strlen($password) < 8) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Password must be at least 8 characters.'
                )
                ->withInput();
        }

        // =========================================================================
        // FORM DATA
        // =========================================================================

        $lastName = trim(
            $this->request->getPost('last_name') ?? ''
        );

        $firstName = trim(
            $this->request->getPost('first_name') ?? ''
        );

        $middleName = trim(
            $this->request->getPost('middle_name') ?? ''
        );

        $email = trim(
            $this->request->getPost('email') ?? ''
        );

        $username = trim(
            $this->request->getPost('username') ?? ''
        );

        // ---------------------------------------------------------------------
        // EMAIL VALIDATION
        // ---------------------------------------------------------------------

        if (
            ! empty($email) &&
            ! filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Please enter a valid email address.'
                )
                ->withInput();
        }

        // ---------------------------------------------------------------------
        // DUPLICATE EMAIL
        // ---------------------------------------------------------------------

        if (! empty($email)) {

            $existingEmail = $this->userModel
                ->where('email', $email)
                ->first();

            if ($existingEmail) {
                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'That email address is already registered.'
                    )
                    ->withInput();
            }
        }

        // ---------------------------------------------------------------------
        // DUPLICATE USERNAME
        // ---------------------------------------------------------------------

        $existingUsername = $this->userModel
            ->where('username', $username)
            ->first();

        if ($existingUsername) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'That username is already taken.'
                )
                ->withInput();
        }

        // =========================================================================
        // HOUSEHOLD
        // =========================================================================

        $householdNo = null;

        if ($role === 'resident') {

            $householdNo = trim(
                $this->request->getPost('household_no') ?? ''
            );

            if (! empty($householdNo)) {

                $householdModel =
                    new \App\Models\HouseholdModel();

                if (
                    ! $householdModel->find(
                        $householdNo
                    )
                ) {
                    return redirect()
                        ->back()
                        ->with(
                            'error',
                            'Household number ' .
                            esc($householdNo) .
                            ' was not found in the census.'
                        )
                        ->withInput();
                }
            }
        }

        // =========================================================================
        // CREATE ACCOUNT
        // =========================================================================

        $saved = $this->userModel->save([
            'last_name' => $lastName,

            'first_name' => $firstName,

            'middle_name' =>
                $middleName ?: null,

            'email' => $email ?: null,

            'username' => $username,

            'password' => password_hash(
                $password,
                PASSWORD_BCRYPT
            ),

            'role' => $role,

            'status' => 'active',

            'email_verified' => 1,

            'household_no' => $householdNo,
        ]);

        if (! $saved) {

            $errors = implode(
                ' ',
                $this->userModel->errors()
            );

            return redirect()
                ->back()
                ->with(
                    'error',
                    $errors ?: 'Unable to create account.'
                )
                ->withInput();
        }

        // =========================================================================
        // REDIRECT
        // =========================================================================

        $redirectPath =
            '/' . $callerRole . '/create-account';

        return redirect()
            ->to($redirectPath)
            ->with(
                'success',
                ucfirst($role) .
                ' account created successfully.'
            );
    }
}