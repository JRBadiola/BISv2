<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClearanceRequestModel;
use App\Models\DocumentTemplateModel;
use App\Models\BarangaySettingsModel;
use App\Models\HouseholdModel;
use App\Models\HouseholdMemberModel;
use App\Models\NotificationModel;
use App\Models\UserModel;

class ClearanceController extends BaseController
{
    protected ClearanceRequestModel $model;

    public function __construct()
    {
        $this->model = new ClearanceRequestModel();
    }

    /*
    |--------------------------------------------------------------------------
    | RESIDENT / SK - CLEARANCE PAGE
    |--------------------------------------------------------------------------
    */

    public function residentIndex()
    {
        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')
                ->with('error', 'User account not found.');
        }

        $members = [];
        $householdTotalIncome = 0.0;
        $occupation = '';

        /*
        |--------------------------------------------------------------------------
        | Load household information
        |--------------------------------------------------------------------------
        */

        if (!empty($user['household_no'])) {

            $householdModel = new HouseholdModel();

            $head = $householdModel->find(
                $user['household_no']
            );

            $memberModel = new HouseholdMemberModel();

            $rawMembers = $memberModel
                ->where(
                    'household_no',
                    $user['household_no']
                )
                ->findAll();

            /*
            |--------------------------------------------------------------------------
            | Household Head
            |--------------------------------------------------------------------------
            */

            if ($head) {

                $members[] = [
                    'name' =>
                        trim(
                            ($head['first_name'] ?? '') .
                            ' ' .
                            ($head['last_name'] ?? '')
                        ),

                    'relationship' =>
                        'Household Head',
                ];

                $householdTotalIncome +=
                    (float) (
                        $head['monthly_income'] ?? 0
                    );

                $occupation =
                    trim(
                        $head['occupation'] ?? ''
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Household Members
            |--------------------------------------------------------------------------
            */

            foreach ($rawMembers as $member) {

                $members[] = [
                    'name' =>
                        trim(
                            ($member['first_name'] ?? '') .
                            ' ' .
                            ($member['last_name'] ?? '')
                        ),

                    'relationship' =>
                        ucfirst(
                            trim(
                                $member['relationship'] ?? ''
                            )
                        ),
                ];

                $householdTotalIncome +=
                    (float) (
                        $member['monthly_income'] ?? 0
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Determine Employment Status
        |--------------------------------------------------------------------------
        |
        | Used for Certificate of Indigency / FTJS eligibility.
        |
        */

        $occupationLower =
            strtolower(
                trim($occupation)
            );

        $isEmployed =
            $occupationLower !== '' &&
            !in_array(
                $occupationLower,
                [
                    'none',
                    'n/a',
                    'unemployed',
                    'student',
                    'out-of-school',
                    'out of school',
                ],
                true
            );

        /*
        |--------------------------------------------------------------------------
        | User Requests
        |--------------------------------------------------------------------------
        */

        $requests =
            $this->model->getByUser(
                $userId
            );

        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        $role = session()->get('role');

        $view =
            $role === 'sk'
                ? 'dashboard/sk/clearance'
                : 'dashboard/resident/clearance';

        return view($view, [
            'requests' =>
                $requests,

            'members' =>
                $members,

            'user' =>
                $user,

            'householdTotalIncome' =>
                $householdTotalIncome,

            'occupation' =>
                $occupation,

            'isEmployed' =>
                $isEmployed,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | RESIDENT / SK - SUBMIT CLEARANCE REQUEST
    |--------------------------------------------------------------------------
    */

    public function store()
    {
        $userId =
            (int) session()->get('user_id');

        if ($userId <= 0) {
            return redirect()->to('/login');
        }

        /*
        |--------------------------------------------------------------------------
        | User
        |--------------------------------------------------------------------------
        */

        $userModel = new UserModel();

        $user =
            $userModel->find($userId);

        if (!$user) {
            return redirect()->back()
                ->with(
                    'error',
                    'User account not found.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Form Data
        |--------------------------------------------------------------------------
        */

        $forMember =
            trim(
                $this->request->getPost(
                    'for_member'
                ) ?? ''
            );

        $memberRelationship =
            trim(
                $this->request->getPost(
                    'member_relationship'
                ) ?? ''
            );

        $documentType =
            trim(
                $this->request->getPost(
                    'document_type'
                ) ?? ''
            );

        $purpose =
            trim(
                $this->request->getPost(
                    'purpose'
                ) ?? ''
            );

        $notes =
            trim(
                $this->request->getPost(
                    'notes'
                ) ?? ''
            );

        /*
        |--------------------------------------------------------------------------
        | Required Fields
        |--------------------------------------------------------------------------
        */

        if (
            $forMember === '' ||
            $documentType === '' ||
            $purpose === ''
        ) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Please fill in all required fields.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Certificate of Indigency Eligibility
        |--------------------------------------------------------------------------
        |
        | Household net monthly income must not exceed ₱12,000.
        |
        */

        if (
            $documentType ===
            'Certificate of Indigency' &&
            !empty($user['household_no'])
        ) {

            $householdModel =
                new HouseholdModel();

            $head =
                $householdModel->find(
                    $user['household_no']
                );

            $memberModel =
                new HouseholdMemberModel();

            $householdMembers =
                $memberModel
                    ->where(
                        'household_no',
                        $user['household_no']
                    )
                    ->findAll();

            /*
            |--------------------------------------------------------------------------
            | Household Head Income
            |--------------------------------------------------------------------------
            */

            $headIncome = 0.0;

            if ($head) {
                $headIncome =
                    (float) (
                        $head['monthly_income'] ?? 0
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Household Member Income
            |--------------------------------------------------------------------------
            */

            $memberIncome = 0.0;

            foreach (
                $householdMembers
                as $member
            ) {
                $memberIncome +=
                    (float) (
                        $member['monthly_income'] ?? 0
                    );
            }

            $totalIncome =
                $headIncome +
                $memberIncome;

            /*
            |--------------------------------------------------------------------------
            | Automatic Rejection
            |--------------------------------------------------------------------------
            */

            if ($totalIncome > 12000) {

                $remarks =
                    'Automatically rejected: household monthly income of ₱' .
                    number_format(
                        $totalIncome,
                        2
                    ) .
                    ' exceeds the ₱12,000.00 indigency threshold.';

                $inserted =
                    $this->model->insert([
                        'user_id' =>
                            $userId,

                        'household_no' =>
                            $user['household_no'] ?? null,

                        'for_member' =>
                            $forMember,

                        'member_relationship' =>
                            $memberRelationship,

                        'document_type' =>
                            $documentType,

                        'purpose' =>
                            $purpose,

                        'notes' =>
                            $notes !== ''
                                ? $notes
                                : null,

                        'status' =>
                            'rejected',

                        'remarks' =>
                            $remarks,

                        'processed_by' =>
                            null,

                        'processed_at' =>
                            date(
                                'Y-m-d H:i:s'
                            ),

                        'est_release_date' =>
                            null,
                    ]);

                if (!$inserted) {

                    return redirect()
                        ->back()
                        ->withInput()
                        ->with(
                            'error',
                            'Unable to process the indigency request.'
                        );
                }

                $role =
                    session()->get('role');

                $role =
                    $role === 'sk'
                        ? 'sk'
                        : 'resident';

                return redirect()
                    ->to(
                        '/' .
                        $role .
                        '/clearance'
                    )
                    ->with(
                        'error',
                        'Your request for a Certificate of Indigency was automatically rejected. ' .
                        'Your household monthly income of ₱' .
                        number_format(
                            $totalIncome,
                            2
                        ) .
                        ' exceeds the ₱12,000.00 eligibility threshold.'
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Estimated Release Date
        |--------------------------------------------------------------------------
        |
        | Two weekdays from today.
        |
        */

        $estRelease =
            date(
                'Y-m-d',
                strtotime(
                    '+2 weekdays'
                )
            );

        /*
        |--------------------------------------------------------------------------
        | Insert Pending Request
        |--------------------------------------------------------------------------
        */

        $inserted =
            $this->model->insert([
                'user_id' =>
                    $userId,

                'household_no' =>
                    $user['household_no'] ?? null,

                'for_member' =>
                    $forMember,

                'member_relationship' =>
                    $memberRelationship,

                'document_type' =>
                    $documentType,

                'purpose' =>
                    $purpose,

                'notes' =>
                    $notes !== ''
                        ? $notes
                        : null,

                'status' =>
                    'pending',

                'remarks' =>
                    null,

                'processed_by' =>
                    null,

                'processed_at' =>
                    null,

                'est_release_date' =>
                    $estRelease,
            ]);

        if (!$inserted) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to submit your clearance request. Please try again.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        $role =
            session()->get('role');

        $role =
            $role === 'sk'
                ? 'sk'
                : 'resident';

        return redirect()
            ->to(
                '/' .
                $role .
                '/clearance'
            )
            ->with(
                'success',
                'Request submitted successfully! Estimated release: ' .
                date(
                    'M d, Y',
                    strtotime($estRelease)
                )
            );
    }


    /*
    |--------------------------------------------------------------------------
    | CAPTAIN / SECRETARY - CLEARANCE LIST
    |--------------------------------------------------------------------------
    */

    public function adminIndex(string $role)
    {
        /*
        |--------------------------------------------------------------------------
        | Validate Role
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $role,
                [
                    'captain',
                    'secretary',
                ],
                true
            )
        ) {
            return redirect()
                ->to('/login')
                ->with(
                    'error',
                    'Unauthorized access.'
                );
        }

        $db =
            \Config\Database::connect();

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $pending =
            $this->model
                ->where(
                    'status',
                    'pending'
                )
                ->countAllResults();

        $approved =
            $this->model
                ->where(
                    'status',
                    'approved'
                )
                ->countAllResults();

        $rejected =
            $this->model
                ->where(
                    'status',
                    'rejected'
                )
                ->countAllResults();

        $total =
            $this->model->countAll();

        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */

        $statusFilter =
            trim(
                $this->request->getGet(
                    'status'
                ) ?? ''
            );

        $typeFilter =
            trim(
                $this->request->getGet(
                    'type'
                ) ?? ''
            );

        $search =
            trim(
                $this->request->getGet(
                    'search'
                ) ?? ''
            );

        /*
        |--------------------------------------------------------------------------
        | Main Query
        |--------------------------------------------------------------------------
        */

        $builder =
            $db->table(
                'clearance_requests cr'
            );

        $builder->select("
            cr.user_id,

            CONCAT(
                TRIM(COALESCE(u.first_name, '')),
                ' ',
                TRIM(COALESCE(u.last_name, ''))
            ) AS resident_name,

            u.username,

            h.zone,
            h.address,
            h.contact_number,

            COUNT(cr.id) AS total_requests,

            SUM(
                CASE
                    WHEN cr.status = 'pending'
                    THEN 1
                    ELSE 0
                END
            ) AS pending_count,

            SUM(
                CASE
                    WHEN cr.status = 'approved'
                    THEN 1
                    ELSE 0
                END
            ) AS approved_count,

            SUM(
                CASE
                    WHEN cr.status = 'rejected'
                    THEN 1
                    ELSE 0
                END
            ) AS rejected_count,

            MAX(cr.created_at) AS latest_filed
        ");

        $builder->join(
            'users u',
            'u.id = cr.user_id',
            'left'
        );

        $builder->join(
            'households h',
            'h.household_no = u.household_no',
            'left'
        );

        /*
        |--------------------------------------------------------------------------
        | Document Type Filter
        |--------------------------------------------------------------------------
        */

        if ($typeFilter !== '') {

            $builder->where(
                'cr.document_type',
                $typeFilter
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {

            $builder->groupStart();

            $builder->like(
                'u.first_name',
                $search
            );

            $builder->orLike(
                'u.last_name',
                $search
            );

            $builder->orLike(
                'u.username',
                $search
            );

            $builder->groupEnd();
        }

        /*
        |--------------------------------------------------------------------------
        | Group By Resident
        |--------------------------------------------------------------------------
        */

        $builder->groupBy(
            'cr.user_id'
        );

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($statusFilter !== '') {

            $safeStatus =
                $db->escape(
                    $statusFilter
                );

            $builder->having(
                "SUM(
                    CASE
                        WHEN cr.status = {$safeStatus}
                        THEN 1
                        ELSE 0
                    END
                ) > 0",
                null,
                false
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $builder->orderBy(
            'latest_filed',
            'DESC'
        );

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $perPage = 10;

        $page =
            max(
                1,
                (int) (
                    $this->request->getGet(
                        'page'
                    ) ?? 1
                )
            );

        $offset =
            ($page - 1) *
            $perPage;

        /*
        |--------------------------------------------------------------------------
        | Count Filtered Residents
        |--------------------------------------------------------------------------
        */

        $countBuilder =
            clone $builder;

        $filteredTotal =
            $countBuilder
                ->countAllResults();

        /*
        |--------------------------------------------------------------------------
        | Get Residents
        |--------------------------------------------------------------------------
        */

        $residents =
            $builder
                ->limit(
                    $perPage,
                    $offset
                )
                ->get()
                ->getResultArray();

        /*
        |--------------------------------------------------------------------------
        | Captain Information
        |--------------------------------------------------------------------------
        */

        $userModel =
            new UserModel();

        $captainRow =
            $userModel
                ->getActiveByRole(
                    'captain'
                );

        $captainName =
            'PUNONG BARANGAY';

        if ($captainRow) {

            $captainName =
                strtoupper(
                    trim(
                        ($captainRow['first_name'] ?? '') .
                        ' ' .
                        ($captainRow['middle_name'] ?? '') .
                        ' ' .
                        ($captainRow['last_name'] ?? '')
                    )
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Document Templates
        |--------------------------------------------------------------------------
        */

        $templateModel =
            new DocumentTemplateModel();

        if (
            $templateModel->tableExists()
        ) {

            $templates =
                $templateModel
                    ->getTemplatesIndexedByKey();

        } else {

            $templates =
                $templateModel
                    ->getDefaultTemplates();
        }

        /*
        |--------------------------------------------------------------------------
        | Barangay Settings
        |--------------------------------------------------------------------------
        */

        $settingsModel =
            new BarangaySettingsModel();

        $barangaySettings =
            $settingsModel->getAll();

        if (
            !empty(
                $barangaySettings['captain_name']
            )
        ) {
            $captainName =
                $barangaySettings['captain_name'];
        }

        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        $viewFile =
            $role === 'captain'
                ? 'dashboard/captain/clearance'
                : 'dashboard/secretary/clearance';

        return view(
            $viewFile,
            [
                'residents' =>
                    $residents,

                'pending' =>
                    $pending,

                'approved' =>
                    $approved,

                'rejected' =>
                    $rejected,

                'total' =>
                    $total,

                'filteredTotal' =>
                    $filteredTotal,

                'perPage' =>
                    $perPage,

                'currentPage' =>
                    $page,

                'statusFilter' =>
                    $statusFilter,

                'typeFilter' =>
                    $typeFilter,

                'search' =>
                    $search,

                'captainName' =>
                    $captainName,

                'templates' =>
                    $templates,

                'barangaySettings' =>
                    $barangaySettings,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CAPTAIN / SECRETARY - RESIDENT DETAIL
    |--------------------------------------------------------------------------
    */

    public function residentDetail(int $userId)
    {
        $role =
            session()->get('role');

        if (
            !in_array(
                $role,
                [
                    'captain',
                    'secretary',
                ],
                true
            )
        ) {
            return redirect()
                ->to('/login')
                ->with(
                    'error',
                    'Unauthorized access.'
                );
        }

        $db =
            \Config\Database::connect();

        /*
        |--------------------------------------------------------------------------
        | Resident
        |--------------------------------------------------------------------------
        */

        $userModel =
            new UserModel();

        $user =
            $userModel->find(
                $userId
            );

        if (!$user) {

            return redirect()
                ->to(
                    '/' .
                    $role .
                    '/clearance'
                )
                ->with(
                    'error',
                    'Resident not found.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Household
        |--------------------------------------------------------------------------
        */

        $household = null;

        if (
            !empty(
                $user['household_no']
            )
        ) {

            $householdModel =
                new HouseholdModel();

            $household =
                $householdModel->find(
                    $user['household_no']
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Requests
        |--------------------------------------------------------------------------
        */

        $requests =
            $db
                ->table(
                    'clearance_requests'
                )
                ->where(
                    'user_id',
                    $userId
                )
                ->orderBy(
                    'created_at',
                    'DESC'
                )
                ->get()
                ->getResultArray();

        /*
        |--------------------------------------------------------------------------
        | Captain
        |--------------------------------------------------------------------------
        */

        $captainRow =
            $userModel
                ->getActiveByRole(
                    'captain'
                );

        $captainName =
            'PUNONG BARANGAY';

        if ($captainRow) {

            $captainName =
                strtoupper(
                    trim(
                        ($captainRow['first_name'] ?? '') .
                        ' ' .
                        ($captainRow['middle_name'] ?? '') .
                        ' ' .
                        ($captainRow['last_name'] ?? '')
                    )
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Barangay Settings
        |--------------------------------------------------------------------------
        */

        $settingsModel =
            new BarangaySettingsModel();

        $barangaySettings =
            $settingsModel->getAll();

        if (
            !empty(
                $barangaySettings['captain_name']
            )
        ) {
            $captainName =
                $barangaySettings['captain_name'];
        }

        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        $viewFile =
            $role === 'captain'
                ? 'dashboard/captain/clearance_detail'
                : 'dashboard/secretary/clearance_detail';

        return view(
            $viewFile,
            [
                'role' =>
                    $role,

                'user' =>
                    $user,

                'household' =>
                    $household,

                'requests' =>
                    $requests,

                'requestId' =>
                    $userId,

                'captainName' =>
                    $captainName,

                'barangaySettings' =>
                    $barangaySettings,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CAPTAIN / SECRETARY - APPROVE
    |--------------------------------------------------------------------------
    */

    public function approve(int $id)
    {
        $role =
            session()->get('role');

        if (
            !in_array(
                $role,
                [
                    'captain',
                    'secretary',
                ],
                true
            )
        ) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Unauthorized action.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Find Request
        |--------------------------------------------------------------------------
        */

        $request =
            $this->model->find(
                $id
            );

        if (!$request) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Clearance request not found.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent Invalid Approval
        |--------------------------------------------------------------------------
        */

        if (
            ($request['status'] ?? '') ===
            'rejected'
        ) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'A rejected request cannot be approved.'
                );
        }

        if (
            ($request['status'] ?? '') ===
            'approved'
        ) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'This request has already been approved.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $updated =
            $this->model->update(
                $id,
                [
                    'status' =>
                        'approved',

                    'processed_by' =>
                        session()->get(
                            'user_id'
                        ),

                    'processed_at' =>
                        date(
                            'Y-m-d H:i:s'
                        ),
                ]
            );

        if (!$updated) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Unable to approve the request.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Notify Resident
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $request['user_id']
            )
        ) {

            $estimatedRelease = '';

            if (
                !empty(
                    $request[
                        'est_release_date'
                    ]
                )
            ) {

                $estimatedRelease =
                    ' Estimated release: ' .
                    date(
                        'M d, Y',
                        strtotime(
                            $request[
                                'est_release_date'
                            ]
                        )
                    ) .
                    '.';
            }

            NotificationModel::push(

                (int)
                    $request['user_id'],

                'clearance_approved',

                'Request Approved — ' .
                (
                    $request[
                        'document_type'
                    ] ??
                    'Clearance'
                ),

                'Your ' .
                (
                    $request[
                        'document_type'
                    ] ??
                    'clearance'
                ) .
                ' request has been approved.' .
                $estimatedRelease .
                ' You may pick it up at the barangay hall during office hours.',

                '/resident/clearance'
            );
        }

        return redirect()
            ->back()
            ->with(
                'success',
                'Request approved.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | CAPTAIN / SECRETARY - REJECT
    |--------------------------------------------------------------------------
    */

    public function reject(int $id)
    {
        $role =
            session()->get('role');

        if (
            !in_array(
                $role,
                [
                    'captain',
                    'secretary',
                ],
                true
            )
        ) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Unauthorized action.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Find Request
        |--------------------------------------------------------------------------
        */

        $request =
            $this->model->find(
                $id
            );

        if (!$request) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Clearance request not found.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent Rejecting Already Rejected
        |--------------------------------------------------------------------------
        */

        if (
            ($request['status'] ?? '') ===
            'rejected'
        ) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'This request has already been rejected.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Remarks
        |--------------------------------------------------------------------------
        */

        $remarks =
            trim(
                $this->request->getPost(
                    'remarks'
                ) ?? ''
            );

        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $updated =
            $this->model->update(
                $id,
                [
                    'status' =>
                        'rejected',

                    'remarks' =>
                        $remarks !== ''
                            ? $remarks
                            : null,

                    'processed_by' =>
                        session()->get(
                            'user_id'
                        ),

                    'processed_at' =>
                        date(
                            'Y-m-d H:i:s'
                        ),
                ]
            );

        if (!$updated) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Unable to reject the request.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Notify Resident
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $request['user_id']
            )
        ) {

            $reason = '';

            if ($remarks !== '') {
                $reason =
                    ' Reason: ' .
                    $remarks;
            }

            NotificationModel::push(

                (int)
                    $request['user_id'],

                'clearance_rejected',

                'Request Not Approved — ' .
                (
                    $request[
                        'document_type'
                    ] ??
                    'Clearance'
                ),

                'Your ' .
                (
                    $request[
                        'document_type'
                    ] ??
                    'clearance'
                ) .
                ' request could not be approved.' .
                $reason .
                ' Please visit the barangay hall for assistance.',

                '/resident/clearance'
            );
        }

        return redirect()
            ->back()
            ->with(
                'success',
                'Request rejected.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | RESIDENT / SK - CANCEL
    |--------------------------------------------------------------------------
    */

    public function cancel(int $id)
    {
        $userId =
            (int) session()->get(
                'user_id'
            );

        if ($userId <= 0) {
            return redirect()
                ->to('/login');
        }

        /*
        |--------------------------------------------------------------------------
        | Find Request
        |--------------------------------------------------------------------------
        */

        $request =
            $this->model->find(
                $id
            );

        if (!$request) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Clearance request not found.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Ownership
        |--------------------------------------------------------------------------
        */

        if (
            (int) (
                $request['user_id'] ?? 0
            ) !== $userId
        ) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'You are not authorized to cancel this request.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if (
            ($request['status'] ?? '') !==
            'pending'
        ) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Only pending requests can be cancelled.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Delete
        |--------------------------------------------------------------------------
        */

        $deleted =
            $this->model->delete(
                $id
            );

        if (!$deleted) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Unable to cancel the request.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        $role =
            session()->get('role');

        $role =
            $role === 'sk'
                ? 'sk'
                : 'resident';

        return redirect()
            ->to(
                '/' .
                $role .
                '/clearance'
            )
            ->with(
                'success',
                'Request cancelled successfully.'
            );
    }
}