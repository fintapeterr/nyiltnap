<?php

require_once __DIR__ . '/../../backend/config/database.php';
$db = Database::getInstance()->getConnection();
$professions = $db->query("SELECT * FROM professions ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

$status = isset($_GET['status']) ? $_GET['status'] : '';
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nádasdy - Nyílt Nap Regisztráció</title>
    <style>
        :root {
            --primary: #0b0c0f; 
            --accent: #f59e0b;  /
            --card-bg: rgba(255, 255, 255, 0.95);
            --text: #1f2937;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        
        body { 
            min-height: 100vh;
            display: flex; 
            justify-content: center; 
            align-items: center; 
            padding: 40px 20px;
            position: relative;
            background: #000;
        }
        
        
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('suli-epulet.jpg'); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: blur(8px) brightness(0.7); 
            z-index: -1;
            transform: scale(1.05); 
        }
        
        
        .container { 
            width: 100%; 
            max-width: 768px; 
            background: var(--card-bg); 
            padding: 40px; 
            border-radius: 16px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.3); 
            border-top: 8px solid var(--primary);
            position: relative;
            z-index: 1;
        }
        
        
        .header { text-align: center; margin-bottom: 30px; }
        .logo-container { 
            width: 140px; 
            margin: 0 auto 15px; 
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .logo-container img { 
            width: 100%; 
            height: auto; 
            display: block;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.15)); 
        }
        
        h1 { color: var(--primary); font-size: 26px; margin-bottom: 5px; font-weight: 700; }
        p.subtitle { color: #4b5563; font-size: 15px; font-weight: 600; }
        
        
        .form-group { margin-bottom: 25px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: var(--primary); font-size: 16px; }
        input[type="text"] { width: 100%; padding: 16px; border: 2px solid #cbd5e1; border-radius: 8px; font-size: 16px; transition: all 0.3s; background: #f8fafc; }
        input[type="text"]:focus { border-color: var(--primary); background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(30,58,138,0.1); }
        
        
        .profession-grid { display: grid; grid-template-columns: 1fr; gap: 12px; margin-top: 8px; }
        @media(min-width: 600px) { .profession-grid { grid-template-columns: 1fr 1fr; } }
        
        .checkbox-btn, .radio-btn { display: flex; align-items: center; background: #f1f5f9; border: 2px solid #cbd5e1; padding: 16px; border-radius: 8px; cursor: pointer; transition: all 0.2s; font-size: 15px; user-select: none; }
        .checkbox-btn input, .radio-btn input { margin-right: 12px; transform: scale(1.3); }
        .checkbox-btn:hover, .radio-btn:hover { border-color: var(--accent); background: rgba(245,158,11,0.08); }
        
        
        .options-row { display: grid; grid-template-columns: 1fr; gap: 20px; }
        @media(min-width: 600px) { .options-row { grid-template-columns: 1fr 1fr; } }
        
        
        .btn-submit { width: 100%; background: var(--primary); color: white; border: none; padding: 18px; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer; transition: background 0.2s, transform 0.1s; margin-top: 15px; box-shadow: 0 4px 12px rgba(30,58,138,0.3); }
        .btn-submit:active { transform: scale(0.98); }
        .btn-submit:hover { background: #172554; }
        
        .admin-link { display: block; text-align: center; margin-top: 25px; color: var(--primary); text-decoration: none; font-weight: 600; font-size: 14px; }
        .admin-link:hover { text-decoration: underline; }

        
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 600; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #ef4444; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div class="logo-container">
            
            <img src="nadasdy-logo.png" alt="Nádasdy Logo">
        </div>
        <h1>Nádasdy Tamás Technikum</h1>
        <p class="subtitle">Nyílt Nap - Regisztrációs ív látogatóknak</p>
    </div>

    <?php if($status === 'success'): ?>
        <div class="alert alert-success">Sikeres regisztráció! Köszönjük érdeklődésedet.</div>
    <?php elseif($status === 'error'): ?>
        <div class="alert alert-error">Hiba történt! Kérjük, tölts ki minden mezőt és válassz legalább egy szakot.</div>
    <?php endif; ?>

    <form action="../../backend/controllers/FormController.php?action=register" method="POST">
        <div class="form-group">
            <label for="full_name">Teljes neved</label>
            <input type="text" id="full_name" name="full_name" placeholder="Pl.: Kovács Gábor" required>
        </div>

        <div class="form-group">
            <label for="current_school">Jelenlegi iskolád</label>
            <input type="text" id="current_school" name="current_school" placeholder="Pl.: Arany János Általános Iskola" required>
        </div>

        <div class="form-group">
            <label>Melyik szakok/szakmák érdekelnek? (Többet is választhatsz)</label>
            <div class="profession-grid">
                <?php foreach($professions as $prof): ?>
                    <label class="checkbox-btn">
                        <input type="checkbox" name="professions[]" value="<?= $prof['id'] ?>">
                        <?= htmlspecialchars($prof['name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="options-row">
            <div class="form-group">
                <label>Tervezett ellátás</label>
                <label class="radio-btn">
                    <input type="radio" name="accommodation_type" value="bejaros" checked> Bejárós lennék
                </label>
                <label class="radio-btn" style="margin-top: 8px;">
                    <input type="radio" name="accommodation_type" value="koleszos"> Kollégista lennék
                </label>
            </div>

            <div class="form-group">
                <label>Végzős vagy jelenleg?</label>
                <label class="radio-btn">
                    <input type="radio" name="is_graduate" value="1" checked> Igen, végzős vagyok
                </label>
                <label class="radio-btn" style="margin-top: 8px;">
                    <input type="radio" name="is_graduate" value="0"> Nem vagyok végzős
                </label>
            </div>
        </div>

        <button type="submit" class="btn-submit">Regisztráció beküldése</button>
    </form>

    <a href="admin.php" class="admin-link">📊 Ugrás az Adminisztrációs Panelre</a>
</div>

</body>
</html>
