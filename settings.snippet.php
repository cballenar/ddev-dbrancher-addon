// #ddev-generated

/**
 * DDEV Branch-Routed Database Override
 * Automatically forces Drupal to use an isolated database for feature branches.
 */
$branch = trim(exec('git rev-parse --abbrev-ref HEAD 2>/dev/null'));
if ($branch && !in_array($branch, ['develop', 'master', 'main', 'HEAD'])) {
  $branch_db_name = 'db_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $branch);
  
  // Only route to the isolated database if it actually exists in MariaDB.
  // This prevents site crashes while the database is cloning, or if opted out of isolation.
  try {
    // DDEV web containers always use host 'db', port 3306, user 'db', pass 'db'
    $pdo = new PDO('mysql:host=db;port=3306', 'db', 'db');
    $stmt = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$branch_db_name'");
    if ($stmt && $stmt->fetchColumn() > 0) {
      $databases['default']['default']['database'] = $branch_db_name;
    }
  } catch (Exception $e) {
    // Silently fall back to 'db'
  }
}
