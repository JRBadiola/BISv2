<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BlotterModel;
use App\Models\UserModel;
use App\Libraries\EmailService;

class BlotterController extends BaseController
{
    protected BlotterModel $model;

    public function __construct()
    {
        $this->model = new BlotterModel();
    }

    // ============================================================
    // PUBLIC: Submit blotter report
    // ============================================================

    public function storePublic()
    {
        $lastName   = trim($this->request->getPost('complainant_last_name') ?? '');
        $firstName  = trim($this->request->getPost('complainant_first_name') ?? '');
        $middleName = trim($this->request->getPost('complainant_middle_name') ?? '');

        $nameParts = $firstName . ($middleName ? ' ' . $middleName : '');
        $complainantName = trim($lastName . ', ' . $nameParts);

        $complainantEmail   = trim($this->request->getPost('complainant_email') ?? '');
        $complainantContact = trim($this->request->getPost('contact_number') ?? '');
        $complainantAddress = trim($this->request->getPost('complainant_address') ?? '');

        $incidentType    = $this->request->getPost('incident_type');
        $incidentDate    = $this->request->getPost('incident_date');
        $incidentTime    = $this->request->getPost('incident_time');
        $location        = $this->request->getPost('location');
        $personsInvolved = $this->request->getPost('persons_involved');
        $narrative       = trim($this->request->getPost('narrative') ?? '');

        $appointmentDate = $this->request->getPost('appointment_date') ?: null;
        $appointmentTime = $this->request->getPost('appointment_time') ?: null;

        // --------------------------------------------------------
        // Validation
        // --------------------------------------------------------

        if (
            empty($lastName) ||
            empty($firstName) ||
            empty($complainantEmail) ||
            empty($incidentType) ||
            empty($narrative)
        ) {
            return redirect()
                ->back()
                ->with('error', 'Please fill in all required fields.')
                ->withInput();
        }

        if (!filter_var($complainantEmail, FILTER_VALIDATE_EMAIL)) {
            return redirect()
                ->back()
                ->with('error', 'Please provide a valid email address.')
                ->withInput();
        }

        // --------------------------------------------------------
        // Check appointment availability
        // --------------------------------------------------------

        if ($appointmentDate) {

            if ($this->isDateBooked($appointmentDate)) {
                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'The selected appointment date (' .
                        date('F d, Y', strtotime($appointmentDate)) .
                        ') is already fully booked. Please choose another date.'
                    )
                    ->withInput();
            }

            if ($appointmentTime) {

                $conflict = $this->getTimeConflict(
                    $appointmentDate,
                    $appointmentTime
                );

                if ($conflict) {
                    return redirect()
                        ->back()
                        ->with(
                            'error',
                            'The selected time (' .
                            date('h:i A', strtotime($appointmentTime)) .
                            ') conflicts with an existing event: "' .
                            $conflict .
                            '". Please choose a different time.'
                        )
                        ->withInput();
                }
            }
        }

        // --------------------------------------------------------
        // Get logged-in user if available
        // --------------------------------------------------------

        $userId = session()->get('user_id');

        if (!$userId) {
            $userId = session()->get('id');
        }

        if (!$userId) {
            $userId = session()->get('userId');
        }

        $userId = $userId ? (int) $userId : null;

        // --------------------------------------------------------
        // Save blotter report
        // --------------------------------------------------------

        $blotterId = $this->model->insert([
            'complainant_user_id' => $userId,
            'complainant_name'    => $complainantName,
            'complainant_email'   => $complainantEmail,
            'complainant_contact' => $complainantContact ?: null,

            'appointment_date' => $appointmentDate,
            'appointment_time' => $appointmentTime,

            'incident_type' => $incidentType,
            'incident_date' => $incidentDate ?: null,
            'incident_time' => $incidentTime ?: null,

            'location'         => $location ?: null,
            'persons_involved' => $personsInvolved ?: null,
            'narrative'        => $narrative,

            'respondent_address' => $complainantAddress ?: null,

            'status' => 'pending',
        ], true);

        // --------------------------------------------------------
        // Create calendar appointment
        // --------------------------------------------------------

        if ($appointmentDate && $blotterId) {

            $userModel = new UserModel();

            $captainUser = $userModel->getActiveByRole('captain');

            if ($captainUser) {

                $scheduleModel = new \App\Models\ScheduleModel();

                $caseNo = str_pad(
                    $blotterId,
                    4,
                    '0',
                    STR_PAD_LEFT
                );

                $scheduleModel->insert([
                    'title' =>
                        'Blotter Appointment #' .
                        $caseNo .
                        ' — ' .
                        $incidentType,

                    'description' =>
                        'Complainant: ' .
                        $complainantName .
                        ($complainantContact
                            ? ' · ' . $complainantContact
                            : '') .
                        "\n" .
                        'Re: ' .
                        $incidentType,

                    'event_date' => $appointmentDate,

                    'start_time' => $appointmentTime ?: null,

                    'end_time' => null,

                    'event_type' => 'appointment',

                    'color' => '#c0392b',

                    'location' => 'Barangay Hall',

                    'blotter_id' => $blotterId,

                    'created_by' => (int) $captainUser['id'],

                    'visibility' => 'private',

                    'shared_with' => null,
                ]);
            }
        }

        // --------------------------------------------------------
        // Notify barangay officials
        // --------------------------------------------------------

        try {

            $db = \Config\Database::connect();

            $officials = $db->table('users')
                ->whereIn('role', ['secretary', 'captain'])
                ->where('status', 'active')
                ->get()
                ->getResultArray();

            foreach ($officials as $official) {

                \App\Models\NotificationModel::push(
                    (int) $official['id'],
                    'new_blotter',
                    'New Blotter Report',
                    $complainantName .
                    ' filed a blotter report: ' .
                    $incidentType .
                    '.',
                    '/' . $official['role'] . '/blotter'
                );
            }

        } catch (\Throwable $e) {

            log_message(
                'error',
                'Blotter notification failed: ' . $e->getMessage()
            );
        }

        // --------------------------------------------------------
        // Success message
        // --------------------------------------------------------

        $msg =
            'Your blotter report has been submitted successfully. ' .
            'The barangay will contact you at ' .
            $complainantEmail .
            '.';

        if ($appointmentDate) {

            $msg .=
                ' Your appointment is set for ' .
                date('F d, Y', strtotime($appointmentDate));

            if ($appointmentTime) {
                $msg .=
                    ' at ' .
                    date('h:i A', strtotime($appointmentTime));
            }

            $msg .= '.';
        }

        // --------------------------------------------------------
        // IMPORTANT:
        // Keep logged-in resident inside resident dashboard.
        // --------------------------------------------------------

        $role = session()->get('role');

        if ($role === 'resident') {

            return redirect()
                ->to('/resident/dashboard')
                ->with('success', $msg);
        }

        if ($role === 'sk') {

            return redirect()
                ->to('/sk/blotter')
                ->with('success', $msg);
        }

        // Non-logged-in/public submission
        return redirect()
            ->to('/')
            ->with('blotter_success', $msg);
    }

    // ============================================================
    // PUBLIC: Return booked dates
    // ============================================================

    public function busyDates()
    {
        $db = \Config\Database::connect();

        $blotterDates = $db->table('blotter_reports')
            ->select('appointment_date AS date_val, COUNT(*) AS cnt')
            ->where('appointment_date IS NOT NULL')
            ->groupBy('appointment_date')
            ->get()
            ->getResultArray();

        $hearingDates = $db->table('blotter_reports')
            ->select('hearing_date AS date_val, COUNT(*) AS cnt')
            ->where('hearing_date IS NOT NULL')
            ->groupBy('hearing_date')
            ->get()
            ->getResultArray();

        $scheduleDates = $db->table('schedules')
            ->select('event_date AS date_val, COUNT(*) AS cnt')
            ->groupBy('event_date')
            ->get()
            ->getResultArray();

        $counts = [];

        foreach (
            array_merge(
                $blotterDates,
                $hearingDates,
                $scheduleDates
            ) as $row
        ) {

            $date = $row['date_val'];

            $counts[$date] =
                ($counts[$date] ?? 0) +
                (int) $row['cnt'];
        }

        $result = [];

        foreach ($counts as $date => $count) {

            $result[] = [
                'date'  => $date,
                'count' => $count,
                'busy'  => $count >= 3,
            ];
        }

        return $this->response->setJSON([
            'dates' => $result,
        ]);
    }

    // ============================================================
    // PUBLIC: Return occupied time slots
    // ============================================================

    public function busySlots()
    {
        $date = trim(
            $this->request->getGet('date') ?? ''
        );

        if (
            !$date ||
            !preg_match(
                '/^\d{4}-\d{2}-\d{2}$/',
                $date
            )
        ) {
            return $this->response->setJSON([
                'slots' => [],
            ]);
        }

        $db = \Config\Database::connect();

        $slots = [];

        // --------------------------------------------------------
        // Blotter appointments
        // --------------------------------------------------------

        $blotters = $db->table('blotter_reports')
            ->select(
                'appointment_time AS start_time, ' .
                'NULL AS end_time, ' .
                'incident_type AS label'
            )
            ->where('appointment_date', $date)
            ->where('appointment_time IS NOT NULL')
            ->get()
            ->getResultArray();

        foreach ($blotters as $blotter) {

            $slots[] = [
                'start' => $blotter['start_time'],
                'end'   => null,
                'label' =>
                    'Blotter Appointment: ' .
                    $blotter['label'],
            ];
        }

        // --------------------------------------------------------
        // Blotter hearings
        // --------------------------------------------------------

        $hearings = $db->table('blotter_reports')
            ->select(
                'hearing_time AS start_time, ' .
                'NULL AS end_time, ' .
                'incident_type AS label'
            )
            ->where('hearing_date', $date)
            ->where('hearing_time IS NOT NULL')
            ->get()
            ->getResultArray();

        foreach ($hearings as $hearing) {

            $slots[] = [
                'start' => $hearing['start_time'],
                'end'   => null,
                'label' =>
                    'Blotter Hearing: ' .
                    $hearing['label'],
            ];
        }

        // --------------------------------------------------------
        // Calendar events
        // --------------------------------------------------------

        $events = $db->table('schedules')
            ->select(
                'start_time, end_time, title AS label'
            )
            ->where('event_date', $date)
            ->where('start_time IS NOT NULL')
            ->get()
            ->getResultArray();

        foreach ($events as $event) {

            $slots[] = [
                'start' => $event['start_time'],
                'end'   => $event['end_time'],
                'label' => $event['label'],
            ];
        }

        return $this->response->setJSON([
            'slots' => $slots,
        ]);
    }

    // ============================================================
    // HELPER: Check if date is fully booked
    // ============================================================

    private function isDateBooked(string $date): bool
    {
        $db = \Config\Database::connect();

        $total = 0;

        $total += $db->table('blotter_reports')
            ->where('appointment_date', $date)
            ->countAllResults();

        $total += $db->table('blotter_reports')
            ->where('hearing_date', $date)
            ->countAllResults();

        $total += $db->table('schedules')
            ->where('event_date', $date)
            ->countAllResults();

        return $total >= 3;
    }

    // ============================================================
    // HELPER: Check time conflict
    // ============================================================

    private function getTimeConflict(
        string $date,
        string $time
    ): ?string {

        $db = \Config\Database::connect();

        $reqMin = $this->toMinutes($time);

        // Treat requested appointment as 1 hour
        $reqEnd = $reqMin + 60;

        // --------------------------------------------------------
        // Calendar events
        // --------------------------------------------------------

        $events = $db->table('schedules')
            ->select(
                'title, start_time, end_time'
            )
            ->where('event_date', $date)
            ->where('start_time IS NOT NULL')
            ->get()
            ->getResultArray();

        foreach ($events as $event) {

            $eventStart =
                $this->toMinutes($event['start_time']);

            $eventEnd =
                $event['end_time']
                    ? $this->toMinutes($event['end_time'])
                    : $eventStart + 60;

            if (
                $reqMin < $eventEnd &&
                $reqEnd > $eventStart
            ) {
                return $event['title'];
            }
        }

        // --------------------------------------------------------
        // Existing blotter appointments
        // --------------------------------------------------------

        $appointments = $db->table('blotter_reports')
            ->select(
                'incident_type, appointment_time AS slot_time'
            )
            ->where('appointment_date', $date)
            ->where('appointment_time IS NOT NULL')
            ->get()
            ->getResultArray();

        foreach ($appointments as $appointment) {

            $start =
                $this->toMinutes(
                    $appointment['slot_time']
                );

            if (
                $reqMin < ($start + 60) &&
                $reqEnd > $start
            ) {
                return
                    'Blotter Appointment: ' .
                    $appointment['incident_type'];
            }
        }

        // --------------------------------------------------------
        // Existing hearings
        // --------------------------------------------------------

        $hearings = $db->table('blotter_reports')
            ->select(
                'incident_type, hearing_time AS slot_time'
            )
            ->where('hearing_date', $date)
            ->where('hearing_time IS NOT NULL')
            ->get()
            ->getResultArray();

        foreach ($hearings as $hearing) {

            $start =
                $this->toMinutes(
                    $hearing['slot_time']
                );

            if (
                $reqMin < ($start + 60) &&
                $reqEnd > $start
            ) {
                return
                    'Blotter Hearing: ' .
                    $hearing['incident_type'];
            }
        }

        return null;
    }

    // ============================================================
    // HELPER: Convert time to minutes
    // ============================================================

    private function toMinutes(string $time): int
    {
        $parts = explode(':', $time);

        $hours = (int) ($parts[0] ?? 0);
        $minutes = (int) ($parts[1] ?? 0);

        return ($hours * 60) + $minutes;
    }

    // ============================================================
    // RESIDENT / SK: Submit blotter report
    // ============================================================

    public function store()
    {
        $userId = (int) session()->get('user_id');

        $userModel = new UserModel();

        $user = $userModel->find($userId);

        $role = session()->get('role');

        $incidentType =
            $this->request->getPost('incident_type');

        $incidentDate =
            $this->request->getPost('incident_date');

        $narrative =
            trim(
                $this->request->getPost('narrative') ?? ''
            );

        $respondentName =
            trim(
                $this->request->getPost('respondent_name') ?? ''
            );

        $contactNumber =
            trim(
                $this->request->getPost('contact_number') ?? ''
            );

        $appointmentDate =
            $this->request->getPost('appointment_date') ?: null;

        $appointmentTime =
            $this->request->getPost('appointment_time') ?: null;

        // --------------------------------------------------------
        // Build complainant information
        // --------------------------------------------------------

        $cLast =
            trim(
                $this->request->getPost(
                    'complainant_last_name'
                )
                ?? ($user['last_name'] ?? '')
            );

        $cFirst =
            trim(
                $this->request->getPost(
                    'complainant_first_name'
                )
                ?? ($user['first_name'] ?? '')
            );

        $cEmail =
            trim(
                $this->request->getPost(
                    'complainant_email'
                )
                ?? ($user['email'] ?? '')
            );

        $cName =
            trim(
                "$cFirst $cLast"
            );

        if ($cName === '') {
            $cName =
                trim(
                    ($user['first_name'] ?? '') .
                    ' ' .
                    ($user['last_name'] ?? '')
                );
        }

        // --------------------------------------------------------
        // Validation
        // --------------------------------------------------------

        if (
            empty($incidentType) ||
            empty($narrative)
        ) {
            return redirect()
                ->back()
                ->with(
                    'blotter_error',
                    'Please fill in the required fields.'
                )
                ->withInput();
        }

        // --------------------------------------------------------
        // Insert report
        // --------------------------------------------------------

        $insert = [
            'complainant_user_id' => $userId,

            'complainant_name' =>
                $cName,

            'complainant_email' =>
                $cEmail ?: ($user['email'] ?? ''),

            'complainant_contact' =>
                $contactNumber ?: null,

            'incident_type' =>
                $incidentType,

            'incident_date' =>
                $incidentDate ?: null,

            'narrative' =>
                $narrative,

            'status' =>
                'pending',
        ];

        if ($respondentName !== '') {
            $insert['respondent_name'] =
                $respondentName;
        }

        if ($appointmentDate) {

            $insert['appointment_date'] =
                $appointmentDate;

            $insert['appointment_time'] =
                $appointmentTime ?: null;
        }

        $blotterId =
            $this->model->insert(
                $insert,
                true
            );

        // --------------------------------------------------------
        // Notify secretary and captain
        // --------------------------------------------------------

        try {

            $db = \Config\Database::connect();

            $officials = $db->table('users')
                ->whereIn(
                    'role',
                    ['secretary', 'captain']
                )
                ->where(
                    'status',
                    'active'
                )
                ->get()
                ->getResultArray();

            foreach ($officials as $official) {

                \App\Models\NotificationModel::push(
                    (int) $official['id'],
                    'new_blotter',
                    'New Blotter Report',
                    $cName .
                    ' filed a blotter report: ' .
                    $incidentType .
                    '.',
                    '/' .
                    $official['role'] .
                    '/blotter'
                );
            }

        } catch (\Throwable $e) {

            log_message(
                'error',
                'Resident blotter notification failed: ' .
                $e->getMessage()
            );
        }

        // --------------------------------------------------------
        // Redirect based on role
        // --------------------------------------------------------

        $redirectBase =
            $role === 'sk'
                ? '/sk/blotter'
                : '/resident/dashboard';

        return redirect()
            ->to($redirectBase)
            ->with(
                'success',
                'Blotter report submitted successfully. ' .
                'The barangay will contact you shortly.'
            );
    }

    // ============================================================
    // ADMIN: List blotter reports
    // ============================================================

    public function adminIndex(string $role)
    {
        $statusFilter =
            $_GET['status'] ?? '';

        $search =
            $_GET['search'] ?? '';

        $db = \Config\Database::connect();

        $builder = $db->table(
            'blotter_reports b'
        )
            ->select(
                "b.*,
                CONCAT(
                    TRIM(COALESCE(u.first_name,'')),
                    ' ',
                    TRIM(COALESCE(u.last_name,''))
                ) AS complainant_full_name,
                u.email AS complainant_email_addr"
            )
            ->join(
                'users u',
                'u.id = b.complainant_user_id',
                'left'
            )
            ->orderBy(
                'b.created_at',
                'DESC'
            );

        if ($statusFilter !== '') {

            $builder->where(
                'b.status',
                $statusFilter
            );
        }

        if ($search !== '') {

            $builder->groupStart()
                ->like(
                    'u.last_name',
                    $search
                )
                ->orLike(
                    'u.first_name',
                    $search
                )
                ->orLike(
                    'b.incident_type',
                    $search
                )
                ->orLike(
                    'b.persons_involved',
                    $search
                )
                ->groupEnd();
        }

        $reports =
            $builder
                ->get()
                ->getResultArray();

        $pending =
            $this->model
                ->where(
                    'status',
                    'pending'
                )
                ->countAllResults();

        $investigating =
            $this->model
                ->where(
                    'status',
                    'under_investigation'
                )
                ->countAllResults();

        $resolved =
            $this->model
                ->where(
                    'status',
                    'resolved'
                )
                ->countAllResults();

        $total =
            $this->model
                ->countAll();

        $viewFile =
            ($role === 'captain')
                ? 'dashboard/captain/blotter'
                : 'dashboard/secretary/blotter';

        return view(
            $viewFile,
            [
                'reports' =>
                    $reports,

                'pending' =>
                    $pending,

                'investigating' =>
                    $investigating,

                'resolved' =>
                    $resolved,

                'total' =>
                    $total,

                'statusFilter' =>
                    $statusFilter,

                'search' =>
                    $search,
            ]
        );
    }

    // ============================================================
    // ADMIN: View single report
    // ============================================================

    public function show(int $id)
    {
        $role =
            (string) (
                session()->get('role')
                ?? 'captain'
            );

        $db = \Config\Database::connect();

        $report = $db->table(
            'blotter_reports b'
        )
            ->select(
                "b.*,
                CONCAT(
                    TRIM(COALESCE(u.first_name,'')),
                    ' ',
                    TRIM(COALESCE(u.last_name,''))
                ) AS complainant_full_name,
                u.email AS complainant_email_addr"
            )
            ->join(
                'users u',
                'u.id = b.complainant_user_id',
                'left'
            )
            ->where(
                'b.id',
                $id
            )
            ->get()
            ->getRowArray();

        if (!$report) {

            return redirect()
                ->to(
                    '/' .
                    $role .
                    '/blotter'
                )
                ->with(
                    'error',
                    'Report not found.'
                );
        }

        return view(
            'dashboard/captain/blotter_detail',
            [
                'report' => $report,
                'role'   => $role,
            ]
        );
    }

    // ============================================================
    // ADMIN: Update status
    // ============================================================

    public function updateStatus(int $id)
    {
        $role =
            (string) (
                session()->get('role')
                ?? 'captain'
            );

        $status =
            $this->request->getPost(
                'status'
            );

        $remarks =
            $this->request->getPost(
                'remarks'
            ) ?? '';

        $this->model->update(
            $id,
            [
                'status' =>
                    $status,

                'remarks' =>
                    $remarks,

                'processed_by' =>
                    session()->get(
                        'user_id'
                    ),
            ]
        );

        return redirect()
            ->to(
                '/' .
                $role .
                '/blotter/' .
                $id
            )
            ->with(
                'success',
                'Status updated.'
            );
    }

    // ============================================================
    // ADMIN: Send summons
    // ============================================================

    public function sendSummons(int $id)
    {
        $role =
            (string) (
                session()->get('role')
                ?? 'captain'
            );

        $report =
            $this->model->find($id);

        if (!$report) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Report not found.'
                );
        }

        $hearingDate =
            $this->request->getPost(
                'hearing_date'
            );

        $hearingTime =
            $this->request->getPost(
                'hearing_time'
            );

        $respondentName =
            trim(
                $this->request->getPost(
                    'respondent_name'
                ) ?? ''
            );

        $respondentEmail =
            trim(
                $this->request->getPost(
                    'respondent_email'
                ) ?? ''
            );

        $respondentAddr =
            trim(
                $this->request->getPost(
                    'respondent_address'
                ) ?? ''
            );

        if (
            empty($hearingDate) ||
            empty($hearingTime)
        ) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Please set a hearing date and time.'
                );
        }

        // --------------------------------------------------------
        // Save hearing information
        // --------------------------------------------------------

        $this->model->update(
            $id,
            [
                'respondent_name' =>
                    $respondentName,

                'respondent_email' =>
                    $respondentEmail,

                'respondent_address' =>
                    $respondentAddr,

                'hearing_date' =>
                    $hearingDate,

                'hearing_time' =>
                    $hearingTime,

                'status' =>
                    'under_investigation',

                'summons_sent_at' =>
                    date('Y-m-d H:i:s'),

                'processed_by' =>
                    session()->get(
                        'user_id'
                    ),
            ]
        );

        $caseNo =
            str_pad(
                $id,
                4,
                '0',
                STR_PAD_LEFT
            );

        $incidentType =
            $report['incident_type'];

        $hDate =
            date(
                'F d, Y',
                strtotime($hearingDate)
            );

        $hTime =
            date(
                'h:i A',
                strtotime($hearingTime)
            );

        $emailService =
            new EmailService();

        $errors = [];

        // --------------------------------------------------------
        // Send summons to complainant
        // --------------------------------------------------------

        try {

            $emailService->sendSummons(
                $report['complainant_email'],
                $report['complainant_name'],
                $caseNo,
                $incidentType,
                $hDate,
                $hTime,
                'complainant'
            );

        } catch (\Throwable $e) {

            $errors[] =
                'Could not send to complainant: ' .
                $e->getMessage();

            log_message(
                'error',
                'Summons to complainant failed: ' .
                $e->getMessage()
            );
        }

        // --------------------------------------------------------
        // Send summons to respondent
        // --------------------------------------------------------

        if (!empty($respondentEmail)) {

            try {

                $emailService->sendSummons(
                    $respondentEmail,
                    $respondentName ?: 'Respondent',
                    $caseNo,
                    $incidentType,
                    $hDate,
                    $hTime,
                    'respondent'
                );

            } catch (\Throwable $e) {

                $errors[] =
                    'Could not send to respondent: ' .
                    $e->getMessage();

                log_message(
                    'error',
                    'Summons to respondent failed: ' .
                    $e->getMessage()
                );
            }
        }

        if (!empty($errors)) {

            return redirect()
                ->to(
                    '/' .
                    $role .
                    '/blotter/' .
                    $id
                )
                ->with(
                    'error',
                    implode(
                        ' | ',
                        $errors
                    )
                );
        }

        return redirect()
            ->to(
                '/' .
                $role .
                '/blotter/' .
                $id
            )
            ->with(
                'success',
                'Hearing Schedule saved successfully.'
            );
    }

    // ============================================================
    // ADMIN: Reschedule hearing
    // ============================================================

    public function reschedule(int $id)
    {
        $role =
            (string) (
                session()->get('role')
                ?? 'captain'
            );

        $hearingDate =
            $this->request->getPost(
                'hearing_date'
            );

        $hearingTime =
            $this->request->getPost(
                'hearing_time'
            );

        $notes =
            trim(
                $this->request->getPost(
                    'hearing_notes'
                ) ?? ''
            );

        if (
            empty($hearingDate) ||
            empty($hearingTime)
        ) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Please provide both a date and time for the hearing.'
                );
        }

        $this->model->update(
            $id,
            [
                'hearing_date' =>
                    $hearingDate,

                'hearing_time' =>
                    $hearingTime,

                'hearing_notes' =>
                    $notes ?: null,

                'scheduled_by' =>
                    session()->get(
                        'user_id'
                    ),

                'status' =>
                    'under_investigation',
            ]
        );

        return redirect()
            ->to(
                '/' .
                $role .
                '/blotter/' .
                $id
            )
            ->with(
                'success',
                'Hearing schedule updated successfully.'
            );
    }

    // ============================================================
    // ADMIN: View / print summons letter
    // ============================================================

    public function viewLetter(int $id)
    {
        $role =
            (string) (
                session()->get('role')
                ?? 'captain'
            );

        $db = \Config\Database::connect();

        $report = $db->table(
            'blotter_reports b'
        )
            ->select(
                "b.*,
                CONCAT(
                    TRIM(COALESCE(u.first_name,'')),
                    ' ',
                    TRIM(COALESCE(u.last_name,''))
                ) AS complainant_full_name,
                u.email AS complainant_email_addr"
            )
            ->join(
                'users u',
                'u.id = b.complainant_user_id',
                'left'
            )
            ->where(
                'b.id',
                $id
            )
            ->get()
            ->getRowArray();

        if (!$report) {

            return redirect()
                ->to(
                    '/' .
                    $role .
                    '/blotter'
                )
                ->with(
                    'error',
                    'Report not found.'
                );
        }

        // Mark letter as issued
        $this->model->update(
            $id,
            [
                'letter_issued_at' =>
                    date('Y-m-d H:i:s'),
            ]
        );

        return view(
            'blotter_letter',
            [
                'report' => $report,
                'role'   => $role,
            ]
        );
    }
}