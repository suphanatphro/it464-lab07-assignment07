<?php
// เปิดระบบโชว์ Error ไว้ชั่วคราว ถ้ามีอะไรพังจะได้เห็นตัวหนังสือ ไม่เจอหน้าขาว 500
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function readSecret($secretName) {
    $path = "/run/secrets/" . $secretName;
    return file_exists($path) ? trim(file_get_contents($path)) : null;
}

$host = 'db-server';
$user = 'app_user';
$pass = readSecret('db_user_pass');
// แก้ชื่อฐานข้อมูลให้ตรงกับไฟล์ .env และ seed-data.sql ครับ
$db   = 'suphanat_db';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("<div class='alert alert-danger m-5'>❌ Connection failed: " . $conn->connect_error . "</div>");
}

$result = $conn->query("SELECT * FROM students ORDER BY student_id ASC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Sarabun:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.9);
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        }
        
        body { 
            font-family: 'Plus Jakarta Sans', 'Sarabun', sans-serif; 
            background: #f3f4f6;
            background-image: radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0, transparent 50%), 
                              radial-gradient(at 100% 0%, rgba(168, 85, 247, 0.15) 0, transparent 50%);
            min-height: 100vh;
            padding-bottom: 50px;
        }

        .main-card {
            border: none;
            border-radius: 20px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .header-section {
            background: var(--primary-gradient);
            padding: 40px 20px;
            color: white;
            text-align: center;
            margin-bottom: -20px;
        }

        .table {
            --bs-table-bg: transparent;
            margin-bottom: 0;
        }

        .table thead th {
            background: #f8fafc;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #64748b;
            padding: 15px 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .table tbody td {
            padding: 18px 20px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }

        .badge-custom {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .bg-submitted { background: #dcfce7; color: #166534; }
        .bg-inprogress { background: #fef9c3; color: #854d0e; }
        .bg-pending { background: #f1f5f9; color: #475569; }

        .time-display {
            font-size: 0.85rem;
            color: #94a3b8;
        }
        
        .user-code {
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 6px;
            font-family: monospace;
            color: #6366f1;
        }
    </style>
</head>
<body>

<div class="header-section">
    <h2 class="fw-bold">Assignment 07: Infrastructure</h2>
    <p class="opacity-75">Containerized Application with Docker Configs & Secrets</p>
</div>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="main-card border-0 card">
                <div class="p-4 d-flex justify-content-between align-items-center bg-white border-bottom">
                    <div>
                        <h5 class="mb-0 fw-bold">Student Database</h5>
                        <small class="text-muted small">เรียงลำดับตาม ID (น้อยไปมาก)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge bg-dark rounded-pill"><i class="bi bi-shield-lock me-1"></i> Secrets Active</span>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th># ID</th>
                                <th>รหัสนักศึกษา</th>
                                <th>ชื่อ-นามสกุล</th>
                                <th>Username</th>
                                <th>อีเมล</th>
                                <th>สถานะงาน</th>
                                <th><i class="bi bi-clock me-1"></i> วันที่และเวลาที่บันทึก</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // เพิ่มการดักจับ (If) เพื่อป้องกัน Error 500 กรณีที่คิวรี่พังหรือไม่มีข้อมูล
                            if ($result && $result->num_rows > 0):
                                while($row = $result->fetch_assoc()): 
                                    $statusSlug = strtolower(str_replace(' ', '', $row['status']));
                                    // จัดรูปแบบวันที่ไทย
                                    $date = date("d/m/Y H:i:s", strtotime($row['submitted_at']));
                            ?>
                            <tr>
                                <td class="fw-bold text-primary"><?= sprintf("%02d", $row['id']) ?></td>
                                <td><span class="fw-semibold text-dark"><?= $row['student_id'] ?></span></td>
                                <td><?= $row['full_name'] ?></td>
                                <td><span class="user-code">@<?= $row['username'] ?></span></td>
                                <td><?= $row['email'] ?></td>
                                <td>
                                    <span class="badge-custom bg-<?= $statusSlug ?>">
                                        <i class="bi bi-circle-fill" style="font-size: 6px;"></i>
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                                <td class="time-display">
                                    <?= $date ?> น.
                                </td>
                            </tr>
                            <?php 
                                endwhile; 
                            else:
                            ?>
                                <tr>
                                    <td colspan="7" class="text-center p-4 text-muted">
                                        ไม่พบข้อมูลนักศึกษา กรุณาตรวจสอบการสร้างตารางใน Database
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-0 p-4 text-center">
                    <small class="text-muted">Database Engine: MariaDB 10.6 | Environment: Docker Compose v2</small>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
<?php 
if ($conn) {
    $conn->close(); 
}
?>