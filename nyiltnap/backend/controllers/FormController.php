<?php

require_once __DIR__ . '/../config/database.php';

class FormController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function handleRegistration() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../../frontend/views/register.php');
            exit;
        }

        $fullName = trim($_POST['full_name'] ?? '');
        $currentSchool = trim($_POST['current_school'] ?? '');
        $accommodationType = $_POST['accommodation_type'] ?? 'bejaros';
        $isGraduate = isset($_POST['is_graduate']) ? intval($_POST['is_graduate']) : 0;
        $selectedProfessions = $_POST['professions'] ?? [];

        if (empty($fullName) || empty($currentSchool) || empty($selectedProfessions)) {
            
            header('Location: ../../frontend/views/register.php?status=error');
            exit;
        }

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("INSERT INTO students (full_name, current_school, accommodation_type, is_graduate) VALUES (?, ?, ?, ?)");
            $stmt->execute([$fullName, $currentSchool, $accommodationType, $isGraduate]);
            $studentId = $this->db->lastInsertId();

            $stmtProf = $this->db->prepare("INSERT INTO student_professions (student_id, profession_id) VALUES (?, ?)");
            foreach ($selectedProfessions as $profId) {
                $stmtProf->execute([$studentId, intval($profId)]);
            }

            $this->db->commit();
            
            header('Location: ../../frontend/views/register.php?status=success');
            exit;
        } catch (Exception $e) {
            $this->db->rollBack();
            header('Location: ../../frontend/views/register.php?status=error');
            exit;
        }
    }
}

$controller = new FormController();
if (isset($_GET['action']) && $_GET['action'] === 'register') {
    $controller->handleRegistration();
}
