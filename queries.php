<?php
// queries.php - one file with all queries
include 'config.php';

function print_table($stmt) {
    if ($stmt === false) {
        echo "<pre>" . print_r(sqlsrv_errors(), true) . "</pre>";
        return;
    }

    $meta = sqlsrv_field_metadata($stmt);
    if (!$meta) {
        echo "<p>No data returned</p>";
        return;
    }

    echo "<table border='1' cellpadding='6' cellspacing='0' style='border-collapse:collapse;width:100%;margin-top:12px;'>";
    echo "<tr>";
    foreach ($meta as $m) echo "<th>" . htmlspecialchars($m['Name']) . "</th>";
    echo "</tr>";

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>";
        foreach ($row as $v) echo "<td>" . htmlspecialchars($v) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

$q = isset($_GET['query']) ? $_GET['query'] : '';
$sql = '';
$desc = '';

switch ($q) {
    case 'order_by':
        $desc = 'ORDER BY: list users alphabetically (excluding NULLs)';
        $sql = "SELECT UserID, full_name, email, role, phone_number, username, Course 
                FROM Users 
                WHERE full_name IS NOT NULL AND email IS NOT NULL 
                ORDER BY full_name ASC;";
        break;

    case 'inner_join':
        $desc = 'INNER JOIN: candidates with their positions and total votes (excluding NULLs)';
        $sql = "SELECT U.full_name AS Candidate, P.Position_name, ISNULL(SUM(V.Votes),0) AS TotalVotes
                FROM Candidates C
                JOIN Users U ON C.UserID = U.UserID
                JOIN Positions P ON C.PositionID = P.PositionID
                LEFT JOIN Votes V ON V.CandidateID = C.CandidateID
                WHERE U.full_name IS NOT NULL AND P.Position_name IS NOT NULL
                GROUP BY U.full_name, P.Position_name
                ORDER BY TotalVotes DESC;";
        break;

    case 'left_join':
        $desc = 'LEFT JOIN: show all candidates and their positions (excluding NULLs)';
        $sql = "SELECT C.CandidateID, U.full_name AS Candidate, P.Position_name
                FROM Candidates C
                LEFT JOIN Positions P ON C.PositionID = P.PositionID
                LEFT JOIN Users U ON C.UserID = U.UserID
                WHERE U.full_name IS NOT NULL AND P.Position_name IS NOT NULL;";
        break;

    case 'right_join':
        $desc = 'RIGHT JOIN: all positions and candidates (excluding NULLs)';
        $sql = "SELECT P.PositionID, P.Position_name, C.CandidateID, U.full_name AS Candidate
                FROM Candidates C
                RIGHT JOIN Positions P ON C.PositionID = P.PositionID
                LEFT JOIN Users U ON C.UserID = U.UserID
                WHERE P.Position_name IS NOT NULL AND U.full_name IS NOT NULL;";
        break;

    case 'full_join':
        $desc = 'FULL JOIN: union of candidates and positions (excluding NULLs)';
        $sql = "SELECT ISNULL(U.full_name,'N/A') AS Candidate, P.Position_name
                FROM Candidates C
                FULL JOIN Positions P ON C.PositionID = P.PositionID
                LEFT JOIN Users U ON C.UserID = U.UserID
                WHERE U.full_name IS NOT NULL AND P.Position_name IS NOT NULL;";
        break;

    case 'subquery':
        $desc = 'SUBQUERY (WHERE): candidates running for positions containing "President" (excluding NULLs)';
        $sql = "SELECT U.full_name AS Candidate
                FROM Users U
                WHERE U.UserID IN (
                    SELECT C.UserID FROM Candidates C
                    WHERE C.PositionID IN (
                        SELECT P.PositionID FROM Positions P WHERE P.Position_name LIKE '%President%'
                    )
                )
                AND U.full_name IS NOT NULL;";
        break;

    case 'between':
        $desc = 'BETWEEN: votes with Votes between 10 and 50 (excluding NULLs)';
        $sql = "SELECT VoteID, VoterID, CandidateID, Votes 
                FROM Votes 
                WHERE Votes BETWEEN 10 AND 50 
                AND VoterID IS NOT NULL 
                AND CandidateID IS NOT NULL;";
        break;

    case 'group_by':
        $desc = 'GROUP BY: total votes per candidate (excluding NULLs)';
        $sql = "SELECT U.full_name AS Candidate, ISNULL(SUM(V.Votes),0) AS TotalVotes
                FROM Candidates C
                JOIN Users U ON C.UserID = U.UserID
                LEFT JOIN Votes V ON V.CandidateID = C.CandidateID
                WHERE U.full_name IS NOT NULL
                GROUP BY U.full_name
                ORDER BY TotalVotes DESC;";
        break;

    case 'having':
        $desc = 'HAVING: candidates with total votes > 10 (excluding NULLs)';
        $sql = "SELECT U.full_name AS Candidate, ISNULL(SUM(V.Votes),0) AS TotalVotes
                FROM Candidates C
                JOIN Users U ON C.UserID = U.UserID
                LEFT JOIN Votes V ON V.CandidateID = C.CandidateID
                WHERE U.full_name IS NOT NULL
                GROUP BY U.full_name
                HAVING ISNULL(SUM(V.Votes),0) > 10
                ORDER BY TotalVotes DESC;";
        break;
    case 'view':
    $desc = 'CREATE VIEW: CandidateSummary (excluding NULLs)';
    $sql = "
    IF NOT EXISTS (SELECT * FROM sys.views WHERE name = 'CandidateSummary')
    BEGIN
        EXEC('
            CREATE VIEW CandidateSummary AS
            SELECT 
                Validation.status
                C.CandidateID, 
                U.full_name AS Candidate, 
                P.Position_name, 
                ISNULL(SUM(V.Votes), 0) AS TotalVotes
            FROM Candidates C
            JOIN Users U ON C.UserID = U.UserID
            JOIN Positions P ON C.PositionID = P.PositionID
            LEFT JOIN Votes V ON V.CandidateID = C.CandidateID
            RIGHT JOIN Validation ON Validation.UserID = U.UserID
            WHERE U.full_name IS NOT NULL AND P.Position_name IS NOT NULL
            GROUP BY C.CandidateID, U.full_name, P.Position_name, Validation.status
        ');
    END
    ";
    break;



    case 'view_as_relation':
        $desc = 'VIEW AS RELATION: read from CandidateSummary (excluding NULLs)';
        $sql = "SELECT * FROM CandidateSummary WHERE Candidate IS NOT NULL AND Position_name IS NOT NULL;";
        break;

    case 'trigger':
        $desc = 'CREATE TRIGGER: simple log after vote insert';
        $sql = "CREATE TRIGGER trg_LogVote
                ON Votes
                AFTER INSERT
                AS
                BEGIN
                    INSERT INTO Logs (UserID, [action])
                    SELECT i.VoterID, 'Cast vote' FROM inserted i WHERE i.VoterID IS NOT NULL;
                END;";
        break;

    default:
        $desc = 'Choose a query from the dashboard.';
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Query - <?php echo htmlspecialchars($q ?: 'none'); ?></title>
  <style>
    body { font-family: Arial, sans-serif; background: #f6f8fa; margin: 0; padding: 20px; }
    .wrap { max-width: 1100px; margin: 0 auto; }
    .card { background: #fff; padding: 18px; border-radius: 8px; box-shadow: 0 3px 10px rgba(0,0,0,0.06); margin-bottom: 12px; }
    pre.sql { background: #f3f3f3; padding: 12px; border-radius: 6px; overflow: auto; }
    a.back { color: #0078d7; text-decoration: none; display: inline-block; margin-bottom: 8px; }
    table { border-collapse: collapse; width: 100%; margin-top: 12px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #f0f0f0; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card"><a class="back" href="index.php">&larr; Back</a></div>

    <div class="card">
      <code><h2 style="color: red;">Query: <?php echo htmlspecialchars(strtoupper($q ?: 'none')); ?></h2></code>
      <p><?php echo htmlspecialchars($desc); ?></p>

      <?php if ($sql): ?>
        <h3>SQL being executed</h3>
        <code><pre class="sql"><?php echo htmlspecialchars($sql); ?></pre></code>

        <?php
        $stmt = sqlsrv_query($conn, $sql);
        if ($stmt === false) {
            echo "<pre>" . print_r(sqlsrv_errors(), true) . "</pre>";
        } else {
            $trim = ltrim($sql);
            $isSelect = strncasecmp($trim, 'select', 6) === 0;
            if ($isSelect) {
                print_table($stmt);
            } else {
                echo "<div style='padding:12px;background:#e9f7ef;border-radius:6px;color:#034d18'>✅ Statement executed successfully.</div>";
            }
        }
        ?>
      <?php else: ?>
        <p>No SQL selected. Go back to dashboard and choose a query.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
