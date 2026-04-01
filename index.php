<?php
// index.php
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>OShwal Voting System Dashboard</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f6f8fb; margin: 0; padding: 20px; }
    .wrap { max-width: 1000px; margin: 0 auto; }
    h1 { text-align: center; color: #03396c; }
    .panel { background: #fff; padding: 18px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.06); margin-bottom: 18px; }
    .buttons { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; }
    .btn { background: #0078d7; color: #fff; padding: 10px 14px; border: none; border-radius: 6px; text-decoration: none; display: inline-block; }
    .btn:hover { background: #005fa3; }
    p.note { text-align: center; color: #666; font-size: 0.95rem; }
  </style>
</head>
<body>
  <div class="wrap">
    <h1>Oshwal Voting System (OVS) Dashboard</h1>

    <div class="panel">
      <h2>Inputs</h2>
      <div class="buttons">
        <a class="btn" href="form.php?action=add_user">Add User</a>
        <a class="btn" href="form.php?action=add_candidate">Add Candidate</a>
        <a class="btn" href="form.php?action=add_vote">Add Vote</a>
      </div>
      <p class="note">Click a button to open the appropriate form page</p>
    </div>

    <div class="panel">
      <h2>Queries (click to open)</h2>
      <div class="buttons">
        <a class="btn" href="queries.php?query=order_by">ORDER BY</a>
        <a class="btn" href="queries.php?query=inner_join">INNER JOIN</a>
        <a class="btn" href="queries.php?query=left_join">LEFT JOIN</a>
        <a class="btn" href="queries.php?query=right_join">RIGHT JOIN</a>
        <a class="btn" href="queries.php?query=full_join">FULL JOIN</a>
        <a class="btn" href="queries.php?query=subquery">SUBQUERY (WHERE)</a>
        <a class="btn" href="queries.php?query=between">BETWEEN</a>
        <a class="btn" href="queries.php?query=group_by">GROUP BY</a>
        <a class="btn" href="queries.php?query=having">HAVING</a>
        <a class="btn" href="queries.php?query=view">CREATE VIEW</a>
        <a class="btn" href="queries.php?query=view_as_relation">VIEW AS RELATION</a>
        <a class="btn" href="queries.php?query=trigger">CREATE TRIGGER</a>
      </div>
      <p class="note">Each query button opens a page that shows the SQL being executed and the result</p>
    </div>

  </div>
</body>
</html>
