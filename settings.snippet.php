// #ddev-generated

/**
 * DDEV Branch-Routed Database Override
 * Automatically forces Drupal to use an isolated database for feature branches.
 */
$dbranch_branch = '';
$dbranch_protected_branches = ['develop', 'main', 'master', 'HEAD'];

$dbranch_config_file = DRUPAL_ROOT . '/../.ddev/.dbranch-config';
if (file_exists($dbranch_config_file) && is_readable($dbranch_config_file)) {
  $dbranch_config = parse_ini_file($dbranch_config_file);
  if (!empty($dbranch_config['ACTIVE_BRANCH'])) {
    $dbranch_branch = trim($dbranch_config['ACTIVE_BRANCH']);
  }
  if (!empty($dbranch_config['PROTECTED_BRANCHES'])) {
    $dbranch_protected_branches = array_map('trim', explode(',', $dbranch_config['PROTECTED_BRANCHES']));
  }
}

if (empty($dbranch_branch)) {
  $dbranch_branch = trim(exec('git rev-parse --abbrev-ref HEAD 2>/dev/null'));
}

if ($dbranch_branch && !in_array($dbranch_branch, $dbranch_protected_branches)) {
  $dbranch_db_name = 'db_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $dbranch_branch);

  // Only route to the isolated database if it actually exists in MariaDB.
  // This prevents site crashes while the database is cloning, or if we opted out of isolation.
  try {
    // DDEV web containers always use host 'db', port 3306, user 'db', pass 'db'
    $dbranch_pdo = new PDO('mysql:host=db;port=3306', 'db', 'db');
    $dbranch_stmt = $dbranch_pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbranch_db_name'");
    if ($dbranch_stmt && $dbranch_stmt->fetchColumn() > 0) {
      $databases['default']['default']['database'] = $dbranch_db_name;
    }
  } catch (Exception $dbranch_e) {
    // Silently fall back to the default 'db' connection
  }
}
