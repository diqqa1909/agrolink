<?php
class RegisterController
{
    use Controller;
    use Database;

    private string $uploadDir = UPLOAD_DIR . 'assets/uploads/verification/';

    public function index($a = '', $b = '', $c = '')
    {
        $user = new UserModel;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($user->validate($_POST)) {
                $userId = $user->insert($_POST);
                $role   = $_POST['role'] ?? '';

                if ($role === 'buyer') {
                    (new BuyerModel())->createProfile($userId, []);
                    // Buyers need no verification
                    $user->setVerificationStatus($userId, 'not_required');

                } elseif ($role === 'farmer') {
                    (new FarmerModel())->createProfile($userId, []);
                    $this->handleUploads($userId, [
                        'nic'          => $_FILES['nic']          ?? null,
                        'bank_details' => $_FILES['bank_details'] ?? null,
                    ]);
                    $user->setVerificationStatus($userId, 'pending');

                } elseif ($role === 'transporter') {
                    (new TransporterModel())->createProfile($userId, []);
                    $this->handleUploads($userId, [
                        'driving_license'        => $_FILES['driving_license']        ?? null,
                        'vehicle_insurance'      => $_FILES['vehicle_insurance']      ?? null,
                        'vehicle_revenue_license'=> $_FILES['vehicle_revenue_license']?? null,
                    ]);
                    $user->setVerificationStatus($userId, 'pending');
                }

                redirect('login?registered=1');
            }
        }

        $data['errors'] = $user->errors;
        $this->view('register', $data);
    }

    private function handleUploads(int $userId, array $files): void
    {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }

        $docModel = new VerificationDocumentModel();

        foreach ($files as $docType => $file) {
            if (empty($file) || $file['error'] !== UPLOAD_ERR_OK) {
                continue;
            }

            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
            if (!in_array($file['type'], $allowed, true)) {
                continue;
            }

            $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = "doc_{$userId}_{$docType}_" . uniqid() . ".{$ext}";
            $dest     = $this->uploadDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $docModel->insert([
                    'user_id'   => $userId,
                    'doc_type'  => $docType,
                    'file_path' => 'assets/uploads/verification/' . $filename,
                    'status'    => 'pending',
                ]);
            }
        }
    }
}