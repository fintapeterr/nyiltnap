<?php

require_once __DIR__ . '/../../backend/config/database.php';
$db = Database::getInstance()->getConnection();


if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $studentId = intval($_GET['id']);
    $stmt = $db->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$studentId]);
    header('Location: admin.php?status=deleted');
    exit;
}


$statsQuery = "
    SELECT p.id, p.name AS profession_name, COUNT(sp.student_id) AS total_interested,
    SUM(CASE WHEN s.is_graduate = 1 THEN 1 ELSE 0 END) AS graduate_count,
    SUM(CASE WHEN s.accommodation_type = 'koleszos' THEN 1 ELSE 0 END) AS kolesz_count,
    SUM(CASE WHEN s.accommodation_type = 'koleszos' AND s.is_graduate = 1 THEN 1 ELSE 0 END) AS graduate_kolesz_count
    FROM professions p
    LEFT JOIN student_professions sp ON p.id = sp.profession_id
    LEFT JOIN students s ON sp.student_id = s.id
    GROUP BY p.id, p.name ORDER BY total_interested DESC";
$stats = $db->query($statsQuery)->fetchAll(PDO::FETCH_ASSOC);


$studentsQuery = "
    SELECT s.id, s.full_name, s.current_school, s.accommodation_type, s.is_graduate,
    GROUP_CONCAT(p.name SEPARATOR ', ') AS interested_professions
    FROM students s
    LEFT JOIN student_professions sp ON s.id = sp.student_id
    LEFT JOIN professions p ON sp.profession_id = p.id
    GROUP BY s.id ORDER BY s.id DESC";
$students = $db->query($studentsQuery)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Nyílt Nap - Admin Panel</title>
    <style>
        :root { --primary: #1e3a8a; --accent: #f59e0b; --bg: #f3f4f6; --card-bg: #ffffff; --text: #1f2937; --danger: #ef4444; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: sans-serif; }
        body { background-color: var(--bg); color: var(--text); padding: 30px; }
        .container { width: 100%; max-width: 1200px; margin: 0 auto; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: var(--primary); padding: 20px; border-radius: 12px; color: white; }
        .btn-back { background: var(--accent); color: var(--primary); padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; }
        .stats-grid { display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 40px; }
        @media(min-width: 768px) { .stats-grid { grid-template-columns: repeat(3, 1fr); } }
        .stat-card { background: var(--card-bg); padding: 20px; border-radius: 10px; border-top: 4px solid var(--accent); box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .stat-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
        .table-container { background: var(--card-bg); padding: 20px; border-radius: 10px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 14px; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; color: var(--primary); }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-kolesz { background: #e0f2fe; color: #0369a1; }
        .badge-grad { background: #fef3c7; color: #d97706; }
        .btn-delete { background: var(--danger); color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>
<div class="container">
    <div class="admin-header">
        <h1>📊 Adminisztrációs Panel</h1>
        <a href="register.php" class="btn-back">⬅ Vissza a Formhoz</a>
    </div>

    <h2>Szakmai Jelentkezések Statisztikája</h2>
    <div class="stats-grid">
        <?php foreach ($stats as $row): ?>
            <div class="stat-card">
                <h3 style="color: var(--primary); margin-bottom: 10px; min-height: 40px;"><?= htmlspecialchars($row['profession_name']) ?></h3>
                <div class="stat-row"><span>Összes érdeklődő:</span><b><?= intval($row['total_interested']) ?> fő</b></div>
                <div class="stat-row"><span>Ebből végzős:</span><b><?= intval($row['graduate_count']) ?> fő</b></div>
                <div class="stat-row"><span>Ebből kollégista:</span><b><?= intval($row['kolesz_count']) ?> fő</b></div>
                <div class="stat-row"><span>Ebből végzős koleszos:</span><b style="color:var(--accent);"><?= intval($row['graduate_kolesz_count']) ?> fő</b></div>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>Regisztrált Diákok Listája</h2>
    <div class="table-container">
        <table>
            <thead><tr><th>Név</th><th>Iskola</th><th>Szakok</th><th>Ellátás</th><th>Státusz</th><th>Művelet</th></tr></thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><b><?= htmlspecialchars($student['full_name']) ?></b></td>
                        <td><?= htmlspecialchars($student['current_school']) ?></td>
                        <td><?= htmlspecialchars($student['interested_professions']) ?></td>
                        <td><span class="badge <?= $student['accommodation_type']=='koleszos'?'badge-kolesz':'' ?>"><?= ucfirst($student['accommodation_type']) ?></span></td>
                        <td><span class="badge <?= $student['is_graduate']=='1'?'badge-grad':'' ?>"><?= $student['is_graduate']=='1'?'Végzős':'Alsóbb éves' ?></span></td>
                        <td><a href="admin.php?action=delete&id=<?= $student['id'] ?>" class="btn-delete" onclick="return confirm('Biztos törlöd?');">Törlés</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
