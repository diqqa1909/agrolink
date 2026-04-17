<?php
class VerificationDocumentModel
{
    use Database;
    protected string $table = 'verification_documents';

    public function insert(array $data): int
    {
        $query = "
            INSERT INTO verification_documents (user_id, doc_type, file_path, status)
            VALUES (:user_id, :doc_type, :file_path, :status)
        ";

        $result = $this->write($query, $data);
        return $result !== false ? (int) $result : 0;
    }

    public function getByUser(int $userId): array
    {
        $query = "SELECT * FROM verification_documents WHERE user_id = ? ORDER BY created_at DESC";
        $result = $this->query($query, [$userId]);

        return $result !== false ? $result : [];
    }

    public function updateStatus(int $id, string $status, ?string $reason = null, int $reviewedBy = 0): void
    {
        $query = "
            UPDATE verification_documents
            SET status = :status, rejection_reason = :reason,
                reviewed_by = :reviewed_by, reviewed_at = NOW()
            WHERE id = :id
        ";

        $this->write($query, [
            'status' => $status,
            'reason' => $reason,
            'reviewed_by' => $reviewedBy,
            'id' => $id
        ]);
    }

    public function getUserVerificationDetails(int $userId): array
    {
        $query = "
            SELECT
                u.id as user_id,
                u.name,
                u.email,
                u.role,
                u.verification_status,
                u.created_at as registered_at,
                vd.id as doc_id,
                vd.file_path,
                vd.status as doc_status,
                vd.rejection_reason,
                vd.created_at as doc_uploaded_at,
            FROM users u
            LEFT JOIN verification_documents vd ON u.id = vd.user_id
            WHERE u.id = :user_id AND u.role IN ('farmer', 'transporter')
            ORDER BY vd.created_at DESC
                ";

        $result = $this->query($query, ['user_id' => $userId]);
        return $result ? $result : [];
    }

    public function getVerificationsWithCounts(): array
    {
        $query = "
            SELECT 
                u.id as user_id,
                u.name,
                u.email,
                u.role,
                u.verification_status,
                u.created_at as registered_at,
                COUNT(vd.id) as doc_count,
                SUM(CASE WHEN vd.status = 'approved' THEN 1 ELSE 0 END) as approved_docs,
                SUM(CASE WHEN vd.status = 'rejected' THEN 1 ELSE 0 END) as rejected_docs,
                SUM(CASE WHEN vd.status = 'pending' THEN 1 ELSE 0 END) as pending_docs,
                -- Get document types as a comma-separated string
                GROUP_CONCAT(DISTINCT vd.doc_type) as document_types,
                -- Get document statuses as a comma-separated string
                GROUP_CONCAT(DISTINCT vd.status) as document_statuses
            FROM users u
            LEFT JOIN verification_documents vd ON u.id = vd.user_id
            WHERE u.role IN ('farmer', 'transporter')
            GROUP BY u.id
            ORDER BY FIELD(u.verification_status, 'pending', 'approved', 'rejected'), u.created_at DESC
        ";
        
        $result = $this->query($query);
        return $result ? $result : [];
    }

    /**
     * Alternative: Get all documents for all verification users
     * This gives you full document details for each user
     */
    public function getAllVerificationsWithDocuments(): array
    {
        $query = "
            SELECT 
                u.id as user_id,
                u.name,
                u.email,
                u.role,
                u.verification_status,
                u.created_at as registered_at,
                vd.id as doc_id,
                vd.doc_type,
                vd.file_path,
                vd.status as doc_status,
                vd.rejection_reason,
                vd.created_at as doc_uploaded_at
            FROM users u
            LEFT JOIN verification_documents vd ON u.id = vd.user_id
            WHERE u.role IN ('farmer', 'transporter')
            AND u.verification_status != 'not_required'
            ORDER BY u.id, vd.created_at DESC
        ";
        
        $result = $this->query($query);
        
        // Group documents by user
        $usersWithDocs = [];
        if ($result) {
            foreach ($result as $row) {
                $userId = $row->user_id;
                if (!isset($usersWithDocs[$userId])) {
                    $usersWithDocs[$userId] = [
                        'user_id' => $row->user_id,
                        'name' => $row->name,
                        'email' => $row->email,
                        'role' => $row->role,
                        'verification_status' => $row->verification_status,
                        'registered_at' => $row->registered_at,
                        'documents' => []
                    ];
                }
                
                // Add document if exists
                if ($row->doc_id) {
                    $usersWithDocs[$userId]['documents'][] = [
                        'doc_id' => $row->doc_id,
                        'doc_type' => $row->doc_type,
                        'file_path' => $row->file_path,
                        'status' => $row->doc_status,
                        'rejection_reason' => $row->rejection_reason,
                        'uploaded_at' => $row->doc_uploaded_at
                    ];
                }
            }
        }
        
        return array_values($usersWithDocs);
    }

    /**
     * Get pending verifications count (for dashboard badge)
     */
    public function getPendingVerificationsCount(): int
    {
        $query = "
            SELECT COUNT(*) as total
            FROM users u
            WHERE u.role IN ('farmer', 'transporter') 
            AND u.verification_status = 'pending'
        ";
        
        $result = $this->query($query);
        return $result ? (int)($result[0]->total ?? 0) : 0;
    }
}