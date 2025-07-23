<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Reports</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="container">
<?php
include '../../db/dbconnect.php';

// Site overview stats
$userCount = $conn->query("SELECT COUNT(*) AS count FROM Users")->fetch_assoc()['count'];
$novelCount = $conn->query("SELECT COUNT(*) AS count FROM Novels")->fetch_assoc()['count'];
$fanficCount = $conn->query("SELECT COUNT(*) AS count FROM Fanfic")->fetch_assoc()['count'];
// $requestCount = $conn->query("SELECT COUNT(*) AS count FROM BookRequests")->fetch_assoc()['count'];

// Role breakdown
// $readerCount = $conn->query("SELECT COUNT(*) AS count FROM Users WHERE role = 'reader'")->fetch_assoc()['count'];
// $writerCount = $conn->query("SELECT COUNT(*) AS count FROM Users WHERE role = 'writer'")->fetch_assoc()['count'];

// $totalRoles = $readerCount + $writerCount;
// $readerPercent = $totalRoles ? round(($readerCount / $totalRoles) * 100, 1) : 0;
// $writerPercent = $totalRoles ? round(($writerCount / $totalRoles) * 100, 1) : 0;

// Top 5 users
// $topUsersQuery = "SELECT username, uploads, comments, views FROM Users ORDER BY uploads DESC LIMIT 5";
// $topUsersResult = $conn->query($topUsersQuery);

// Book Requests
// $requestsQuery = "SELECT * FROM BookRequests ORDER BY request_date DESC";
// $requestsResult = $conn->query($requestsQuery);

// Reports
// $reportsQuery = "SELECT * FROM Reports ORDER BY report_date DESC";
// $reportsResult = $conn->query($reportsQuery);
?>

<h1 class="mt-4">Admin Reports</h1>

<div class="row text-center">
  <div class="col">Users: <?= $userCount ?></div>
  <div class="col">Novels: <?= $novelCount ?></div>
  <div class="col">Fanfics: <?= $fanficCount ?></div>
  <div class="col">Book Requests: <?= $requestCount ?></div>
</div>

<!-- Role Breakdown Chart -->
<canvas id="roleChart"></canvas>
<script>
  const ctxRole = document.getElementById('roleChart');
  new Chart(ctxRole, {
    type: 'pie',
    data: {
      labels: ['Readers', 'Writers'],
      datasets: [{
        data: [<?= $readerPercent ?>, <?= $writerPercent ?>],
        backgroundColor: ['#ffc107', '#17a2b8']
      }]
    }
  });
</script>

<!-- Top Users -->
<h4 class="mt-4">Top 5 Active Users</h4>
<ul class="list-group">
  <?php while ($user = $topUsersResult->fetch_assoc()): ?>
    <li class="list-group-item">
      <?= htmlspecialchars($user['username']) ?> 
      - Uploads: <?= $user['uploads'] ?>, Comments: <?= $user['comments'] ?>, Views: <?= $user['views'] ?>
    </li>
  <?php endwhile; ?>
</ul>

<!-- Book Requests Table -->
<h4 class="mt-4">User Book Requests</h4>
<table class="table">
  <thead><tr><th>ID</th><th>User</th><th>Title</th><th>Author</th><th>Date</th><th>Status</th></tr></thead>
  <tbody>
    <?php while ($req = $requestsResult->fetch_assoc()): ?>
      <tr>
        <td><?= $req['id'] ?></td>
        <td><?= htmlspecialchars($req['username']) ?></td>
        <td><?= htmlspecialchars($req['title']) ?></td>
        <td><?= htmlspecialchars($req['author']) ?></td>
        <td><?= $req['request_date'] ?></td>
        <td><?= $req['status'] ?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<!-- Reports Table -->
<h4 class="mt-4">Reported Content</h4>
<table class="table">
  <thead><tr><th>Type</th><th>Content</th><th>Reason</th><th>By</th><th>Date</th><th>Action</th></tr></thead>
  <tbody>
    <?php while ($report = $reportsResult->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($report['report_type']) ?></td>
        <td><?= htmlspecialchars($report['content_id']) ?></td>
        <td><?= htmlspecialchars($report['reason']) ?></td>
        <td><?= htmlspecialchars($report['reported_by']) ?></td>
        <td><?= $report['report_date'] ?></td>
        <td><?= htmlspecialchars($report['action_taken']) ?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

</body>
</html>
