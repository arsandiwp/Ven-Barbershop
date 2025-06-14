<?php

namespace App\Http\Controllers\Api;

use App\Exports\UsersExport;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Imports\UsersImport;
use App\Models\Booking;
use App\Services\AuthenticationService;
use App\Services\FileHandlerService;
use App\Services\MailService;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UserController extends Controller
{
    private $userService;
    private $authenticationService;
    private $mailService;
    private $fileHandlerService;

    public function __construct(UserService $userService, AuthenticationService $authenticationService, MailService $mailService, FileHandlerService $fileHandlerService)
    {
        $this->userService = $userService;
        $this->authenticationService = $authenticationService;
        $this->mailService = $mailService;
        $this->fileHandlerService = $fileHandlerService;
    }

    public function register(Request $request)
    {
        $isEmailExists = $this->userService->getByEmail($request->email);

        if ($isEmailExists->first()) {
            throw new CustomException("The email has been used by another user!");
        }

        $data = $request->only(Schema::getColumnListing('users'));

        if (!$request->password) {
            $data['password'] = $this->userService->makePassword();
        } else {
            $data['password'] = $this->userService->makePassword($request->password);
        }

        if (!($request->has('role_id'))) {
            throw new CustomException("Please select user role!");
        }

        $data['role_id'] = $request->role_id;
        $data['status'] = "Pending";

        return DB::transaction(function () use ($data) {
            $queryResultUser = $this->userService->create($data);

            if (!$queryResultUser) {
                throw new CustomException("Database fail to create a new customer");
            }

            return ResponseHelper::create($queryResultUser);
        });
    }

    public function login(Request $request)
    {
        $isEmailExists = $this->userService->getByEmail($request->email);

        if (count($isEmailExists->get()) == 0) {
            throw new CustomException("You don't have an account registered on the database, please register your account first!");
        }

        $authenticatedUserData = $this->authenticationService->authenticate($request->email, $request->password);

        if (!$authenticatedUserData) {
            return [
                'api_status' => 'fail',
                'api_title' => "Email or Password doesn't match!",
                'api_message' => "Please enter your matching login credential!!",
            ];
        }

        if ($authenticatedUserData->status == 'Pending') {
            return [
                'api_status' => 'fail',
                'api_title' => 'Account is inactive!',
                'api_message' => 'Please contact your Administrator to activate your account!! Your account is in "pending" status as an admin.',
            ];
        }

        $token = $this->authenticationService->generateToken();
        $this->authenticationService->setTokenData($token, $authenticatedUserData);

        return [
            'message' => 'success',
            'token' => $token,
            'data' => [$authenticatedUserData],
        ];
    }

    public function logout(Request $request)
    {
        $token = $request->header('Authorization');
        $this->authenticationService->removeToken($token);
    }

    public function checkToken(Request $request)
    {
        $token = $request->header('Authorization');
        $userData = $this->authenticationService->getTokenData($token);

        if (!$userData) {
            throw new CustomException("Token is not recognized by the server!");
        }

        return [
            'message' => 'success',
            'token' => $token,
            'data' => [$userData],
        ];
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = $this->userService->getByEmail($request->email)->first();

        if (!$user) {
            throw new CustomException("Email tidak ditemukan dalam catatan kami.");
        }

        $token = Str::random(60);

        $this->userService->storePasswordResetToken($user->id, $token);
        $this->mailService->sendResetPasswordEmail($user->email, $token);
    }

    public function resetPassword(Request $request, $token)
    {
        $request->validate([
            'new_password' => 'required|string|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        $user = $this->userService->getByToken($token);

        DB::transaction(function () use ($user, $request) {
            $user->password = $this->userService->makePassword($request->new_password);
            $user->save();

            $this->userService->removePasswordResetToken($user->id);

            return ResponseHelper::put();
        });
    }

    public function profile(Request $request)
    {
        $user_id = $request->_session['id'];
        $result = $this->userService->get($user_id);
        return ResponseHelper::get($result);
    }

    public function getPaginate(Request $request)
    {
        $per_page = @$request->per_page;
        $keyword = @$request->keyword;
        $result = $this->userService->getPaginate($per_page, $keyword);

        return $result;
    }

    public function get($user_id)
    {
        $result = $this->userService->get($user_id);
        return ResponseHelper::get($result);
    }

    public function create(Request $request)
    {
        $isEmailExists = $this->userService->getByEmail($request->email)->exists();

        if ($isEmailExists) {
            throw new CustomException("Email already used by another user!");
        }

        $data = $request->only(Schema::getColumnListing('users'));

        if (!$request->password) {
            $data['password'] = $this->userService->makePassword();
        } else {
            $data['password'] = $this->userService->makePassword($request->password);
        }

        if (!($request->has('role_id'))) {
            throw new CustomException("Please select user role!");
        }

        $data['role_id'] = $request->role_id;
        $data['status'] = "Pending";

        if ($request->hasFile('photo')) {
            $email = $request->email;
            $file = $request->file('photo');

            $data['photo'] = $this->fileHandlerService->saveFileToStorage($file, "/users/" . $email);
        }

        return DB::transaction(function () use ($data) {
            $queryResultUser = $this->userService->create($data);

            if (!$queryResultUser) {
                throw new CustomException("Database fail to create a new user");
            }

            return ResponseHelper::create($queryResultUser);
        });
    }

    public function update($user_id, Request $request)
    {
        $isEmailExists = $this->userService->getByEmail($request->email)
            ->where('id', '!=', $user_id)
            ->exists();

        if ($isEmailExists) {
            throw new CustomException("Email already used by another user!");
        }

        $existingData = $this->userService->get($user_id)->first();

        $data = $request->only(Schema::getColumnListing('users'));

        if ($request->hasFile('photo')) {
            $email = $existingData->email;
            $file = $request->file('photo');
            $dir_path = "public/users/" . $email;

            $this->fileHandlerService->deleteFileItemDirectory($dir_path);

            $data['photo'] = $this->fileHandlerService->saveFileToStorage($file, "/users/" . $email);
        }

        return DB::transaction(function () use ($data, $user_id, $request) {
            $this->userService->update($user_id, $data);
            return ResponseHelper::put();
        });
    }

    public function updateProfile(Request $request)
    {
        $id = $request->_session['id'];
        $isEmailExists = $this->userService->getByEmail($request->email)
            ->where('id', '!=', $id)
            ->exists();

        if ($isEmailExists) {
            throw new CustomException("Email already used by another user!");
        }

        $existingData = $this->userService->get($id)->first();

        $data = $request->only(Schema::getColumnListing('users'));

        if ($request->hasFile('photo')) {
            $email = $existingData->email;
            $file = $request->file('photo');
            $dir_path = "public/users/" . $email;

            $this->fileHandlerService->deleteFileItemDirectory($dir_path);

            $data['photo'] = $this->fileHandlerService->saveFileToStorage($file, "/users/" . $email);
        }

        return DB::transaction(function () use ($request, $id, $data) {
            $this->userService->update($id, $data);
            $userData = $request->_session;
            if ($userData['id'] == $id) {
                $token = $request->header('authorization')[0];
                $this->authenticationService->setTokenData($token, $data);
                $userData = array_merge($userData, $data);
            }

            return ResponseHelper::put();
        });
    }


    public function changePass(Request $request)
    {
        $user = $this->userService->get($request->_session['id'])[0];
        if (!$user) {
            throw new CustomException("Profile data not found!");
        }

        if (!$request->new_password || \strlen($request->new_password) < 6) {
            throw new CustomException("Please input your new password (at least 6 char) !!");
        }

        if ($request->confirm_password != $request->new_password) {
            throw new CustomException("Please confirm your new password!");
        }

        if (!Hash::check($request->old_password, $user->password)) {
            throw new CustomException("Old password is wrong!");
        }

        $new_pass = $request->new_password;

        return DB::transaction(function () use ($user, $new_pass) {
            $data['password'] = $this->userService->makePassword($new_pass);
            $this->userService->update($user->id, $data);
            // $this->mailService->sendNewPassword($user->email, $new_pass);
            return ResponseHelper::put();
        });
    }

    public function adminChangePass($user_id, Request $request)
    {
        $user = $this->userService->get($user_id)[0];
        if ($request->_session['role_id'] != 1) {
            return response("You are not authorized to do this action!!", 412);
        }

        if (!$request->new_password || \strlen($request->new_password) < 6) {
            throw new CustomException("Please input your new password (at least 6 char) !!");
        }

        if ($request->confirm_password != $request->new_password) {
            throw new CustomException("Please confirm your new password!");
        }

        $new_pass = $request->new_password;

        return DB::transaction(function () use ($user, $new_pass) {
            $data['password'] = $this->userService->makePassword($new_pass);
            $this->userService->update($user->id, $data);
            // $this->mailService->sendNewPassword($user->email, $new_pass);
            return ResponseHelper::put();
        });
    }

    public function delete($id, Request $request)
    {

        $userLoggedIn = $request->_session['id'];

        if ($id == $userLoggedIn) {
            return ResponseHelper::error([
                'message' => 'You cannot delete your own account while logged in.'
            ]);
        }

        DB::beginTransaction();

        $this->userService->deleteMemberItemStorage($id);
        $this->userService->delete($id);

        DB::commit();

        return ResponseHelper::delete();
    }

    public function restore($id)
    {
        return ResponseHelper::create($this->userService->restore($id));
    }

    public function export()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // max 10MB
        ]);

        try {
            Excel::import(new UsersImport, $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Data pengguna berhasil diimport'
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // Tangkap error validasi dari Excel
            $failures = $e->failures();
            $errors = [];

            foreach ($failures as $failure) {
                $errors[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors()
                ];
            }

            return response()->json([
                'success' => false,
                'message' => 'Terdapat kesalahan pada data import',
                'errors' => $errors
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error importing users: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat import data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['full_name', 'email', 'phone', 'address', 'photo', 'status'];
        $column = 'A';

        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ]
        ];

        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        // // Tambahkan data contoh
        // $sampleData = [
        //     ['John Doe', 'john.doe@example.com', '+6281234567890', 'Jl. Contoh No. 123, Jakarta', '=HYPERLINK("https://example.com/john.jpg","Photo")', 'Active'],
        //     ['Jane Smith', 'jane.smith@example.com', '081298765432', 'Jl. Sample No. 456, Bandung', 'https://example.com/jane.jpg', 'Active'],
        //     ['Robert Johnson', 'robert@example.com', '62812345678', '', '', 'Pending'],
        //     ['Sarah Williams', 'sarah@example.com', '', 'Jl. Test No. 789, Surabaya', '=HYPERLINK("https://example.com/sarah.jpg","Photo")', 'Active'],
        // ];

        // $row = 2;
        // foreach ($sampleData as $rowData) {
        //     $column = 'A';
        //     foreach ($rowData as $cellValue) {
        //         $sheet->setCellValue($column . $row, $cellValue);
        //         $column++;
        //     }
        //     $row++;
        // }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Petunjuk');

        $instructions = [
            ['PETUNJUK PENGISIAN TEMPLATE IMPORT USER', ''],
            ['', ''],
            ['Kolom', 'Keterangan'],
            ['full_name', 'Nama lengkap user (wajib diisi)'],
            ['email', 'Email user (wajib diisi, harus unik)'],
            ['phone', 'Nomor telepon (opsional, format akan distandarisasi ke awalan 62)'],
            ['address', 'Alamat lengkap (opsional)'],
            ['photo', 'URL foto atau format hyperlink Excel (opsional)'],
            ['status', 'Status user: Active atau Pending (opsional, default: Active)'],
            ['', ''],
            ['Catatan:', ''],
            ['1. Password default untuk semua user adalah 123456', ''],
            ['2. Format hyperlink di Excel: =HYPERLINK("https://example.com/photo.jpg","Photo")', ''],
            ['3. Nomor telepon akan otomatis diformat dengan awalan 62', ''],
        ];

        $row = 1;
        foreach ($instructions as $rowData) {
            $instructionSheet->setCellValue('A' . $row, $rowData[0]);
            $instructionSheet->setCellValue('B' . $row, $rowData[1]);
            $row++;
        }

        $instructionSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $instructionSheet->getStyle('A3:B3')->getFont()->setBold(true);
        $instructionSheet->getStyle('A3:B3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDEBF7');

        $instructionSheet->getColumnDimension('A')->setWidth(60);
        $instructionSheet->getColumnDimension('B')->setWidth(60);

        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'template_import_user.xlsx';
        $tempPath = storage_path('app/public/templates/' . $filename);

        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
