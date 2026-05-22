# DDEV Branch Databases Add-on

A DDEV add-on that brings isolated, dynamic database routing to your Drupal feature branches. Useful for tricky setups that can't easily use worktrees.

## Features
- **Clones Database**: When checking out a feature branch, it will make a clone for that branch.
- **Auto-Provisioning**: Prompts you `[y/N]` to clone your database when checking out new branches.
- **Smart Garbage Collection**: Automatically sweeps MariaDB and deletes 8GB orphaned databases the moment their corresponding PR is merged and deleted on GitHub (via Git `[gone]` tracking).
- **Private Hook Injection**: Injects the automation directly into your `.git/hooks/post-checkout` folder to avoid enforcing it upon teammates who don't want it.
- **PHP Safe Routing**: Automatically appends the necessary PDO check to your `settings.local.php` file during installation.

## Installation
Run this command from the root of your DDEV project:
```bash
ddev add-on get github-username/ddev-branch-databases
```

## Manual Commands
- `ddev branch-db-provision`: Manually triggers the checkout provisioner.
- `ddev branch-db-drop <branch_name>`: Drops the isolated database for a specific branch.
- `ddev branch-db-drop current`: Drops the isolated database for your active branch.
- `ddev branch-db-drop orphaned`: Forces a garbage collection scan for orphaned databases.
- `ddev branch-db-init [database_name] [file_path]`: Imports a database dump and runs the Drupal update pipeline (`updb`, `cim`, `cr`). If no `file_path` is provided, it automatically finds the newest `.sql.gz` dump in your customized search directory.

## Customization
By default, the `branch-db-init` script searches your project root for database dumps. If your team stores database dumps in a specific folder (e.g., `db_dumps/`), you can customize the search directory by creating a `.ddev/branch-db-config` file:

```bash
# .ddev/branch-db-config
DUMP_DIR="db_dumps"
```
