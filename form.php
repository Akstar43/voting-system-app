<?php
// form.php - shows forms for add_user, add_candidate, add_vote and handles submission
include 'config.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_action'])) {
    $fa = $_POST['form_action'];

    if ($fa === 'add_user') {
        $sql = "INSERT INTO Users (full_name, email, role, phone_number, username, password, Course) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $_POST['full_name'] ?: null,
            $_POST['email'] ?: null,
            $_POST['role'] ?: null,
            $_POST['phone_number'] ?: null,
            $_POST['username'] ?: null,
            $_POST['password'] ?: null,
            $_POST['Course'] ?: null
        ];
        $stmt = sqlsrv_query($conn, $sql, $params);
        $message = $stmt ? "User inserted successfully." : print_r(sqlsrv_errors(), true);
    }

    if ($fa === 'add_candidate') {
        $sql = "INSERT INTO Candidates (UserID, PositionID) VALUES (?, ?)";
        $params = [ (int)$_POST['user_id'], (int)$_POST['position_id'] ];
        $stmt = sqlsrv_query($conn, $sql, $params);
        $message = $stmt ? "Candidate inserted successfully." : print_r(sqlsrv_errors(), true);
    }

    if ($fa === 'add_vote') {
        $sql = "INSERT INTO Votes (VoterID, CandidateID, Votes) VALUES (?, ?, ?)";
        $params = [ (int)$_POST['voter_id'], (int)$_POST['candidate_id'], (int)$_POST['votes'] ];
        $stmt = sqlsrv_query($conn, $sql, $params);
        $message = $stmt ? "Vote inserted successfully." : print_r(sqlsrv_errors(), true);
    }
}

function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES); }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Form - VotingSystem</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f7f9fb; margin: 0; padding: 20px; }
    .wrap { max-width: 900px; margin: 0 auto; }
    .card { background: #fff; padding: 18px; border-radius: 8px; box-shadow: 0 3px 10px rgba(0,0,0,0.06); margin-bottom: 12px; }
    label { display: block; margin: 8px 0 4px; }
    input { padding: 8px; width: 100%; box-sizing: border-box; border-radius: 4px; border: 1px solid #ccc; }
    .row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .btn { background: #0078d7; color: #fff; padding: 10px 14px; border: none; border-radius: 6px; cursor: pointer; margin-top: 10px; }
    a.back { color: #0078d7; text-decoration: none; display: inline-block; margin-bottom: 8px; }
    .msg { padding: 10px; background: #e9f7ef; border-radius: 6px; color: #034d18; margin-bottom: 12px; }
    pre.err { background:#fee; padding:10px; border-radius:6px; }
  </style>
</head>
<body>
  <div class="wrap">
    <a class="back" href="index.php">&larr; Back to Dashboard</a>

    <?php if ($message): ?>
      <?php if (strpos($message, 'Array') !== false || strpos($message, 'SQL') !== false): ?>
        <pre class="err"><?php echo $message; ?></pre>
      <?php else: ?>
        <div class="msg"><?php echo h($message); ?></div>
      <?php endif; ?>
    <?php endif; ?>

    <?php if ($action === 'add_user' || $action === ''): ?>
      <div class="card">
        <h2>Add User (all columns)</h2>
        <form method="post">
          <input type="hidden" name="form_action" value="add_user">
          <label>Full name</label><input name="full_name" required>
          <label>Email</label><input name="email" type="email">
          <label>Role</label><input name="role" placeholder="Candidate / Voter / Admin">
          <label>Phone number</label><input name="phone_number">
          <label>Username</label><input name="username">
          <label>Password</label><input name="password" type="password">
          <label>Course</label><input name="Course">
          <button class="btn" type="submit">Insert User</button>
        </form>
      </div>
    <?php endif; ?>

    <?php if ($action === 'add_candidate'): ?>
      <div class="card">
        <h2>Add Candidate</h2>
        <form method="post">
          <input type="hidden" name="form_action" value="add_candidate">
          <label>UserID (existing user)</label><input name="user_id" type="number" required>
          <label>PositionID (existing position)</label><input name="position_id" type="number" required>
          <button class="btn" type="submit">Insert Candidate</button>
        </form>
      </div>
    <?php endif; ?>

    <?php if ($action === 'add_vote'): ?>
      <div class="card">
        <h2>Add Vote</h2>
        <form method="post">
          <input type="hidden" name="form_action" value="add_vote">
          <label>VoterID</label><input name="voter_id" type="number" required>
          <label>CandidateID</label><input name="candidate_id" type="number" required>
          <label>Votes</label><input name="votes" type="number" value="1" required>
          <button class="btn" type="submit">Insert Vote</button>
        </form>
      </div>
    <?php endif; ?>

  </div>
</body>
</html>
