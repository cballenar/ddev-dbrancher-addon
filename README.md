# DBrancher (DDEV Add-on)

A DDEV add-on that brings isolated, dynamic database routing and ultra-fast workspace switching to your Drupal feature branches. It leverages a **Git bare repository + worktrees strategy** to enable sub-second database suffix routing and source code swaps without container restarts, configuration pollution, or Mutagen sync re-indexing loops.

## Features
- **Bare Repo & Worktrees Architecture**: Run a master DDEV hub with flat, decoupled feature branch directories. Seamlessly swap code and databases instantly using lightweight worktrees.
- **Clones Database & Auto-Provisioning**: Prompts you to clone your database when checking out or switching to new branch workspaces.
- **Native DDEV Routing**: Swaps environments cleanly using DDEV's native docroot configuration, avoiding symlink conflicts and ensuring perfect Mutagen compatibility.
- **PHP Safe Routing**: Automatically forces Drupal to read an active state configuration file (`.dbranch-config`) for zero-overhead, isolated database connection logic.
- **Smart Branch Naming Wrangler**: Converts standard slash-separated git branch names (e.g., `feature/ticket-123`) automatically into flat directory structures (`feature-ticket-123`), keeping your workspace organized.

### Smart Garbage Collection
To ensure orphaned databases are deleted when their corresponding pull requests are merged, you must configure Git to automatically prune deleted remote branches:

```bash
git config --global fetch.prune true
```
Once configured, the add-on will automatically sweep the database and delete orphaned databases.

## Installation & Setup

If you already have a standard DDEV environment, you can install the addon and run the automated conversion wizard to safely restructure your workspace into the DBrancher hub layout:

```bash
ddev add-on get cballenar/ddev-dbrancher-addon
ddev dbranch-convert
```

If you are starting fresh on a new repository, simply run a normal `git clone`, enter the directory, and run the two commands above.

## Worktree Switching

With the bare repository strategy, switching contexts is seamless. 

The installation injects a local `git go` wrapper. You can use it, or the native ddev command:

```bash
# Using the native wrapper:
ddev dbranch switch <branch-or-folder-name>

# Using the git go alias (if installed):
git go <branch-or-folder-name>
```

This updates your DDEV configuration to point to the target worktree and automatically triggers a `ddev restart` to cleanly route traffic and database connections to your new environment.

## Manual Commands
- `ddev dbranch switch <worktree>`: Switches the active workspace to the specified branch directory and auto-provisions its database if needed.
- `ddev dbranch list`: Lists all isolated databases and their statuses (Active, Orphaned, Protected, etc).
- `ddev dbranch drop <branch_name>`: Drops the isolated database for a specific branch.
- `ddev dbranch drop current`: Drops the isolated database for your active branch.
- `ddev dbranch drop orphaned`: Forces a garbage collection scan for orphaned databases and removes them.
- `ddev dbranch init [file_path]`: Imports a database dump into your active isolated branch and runs the Drupal update pipeline (`updb`, `cim`, `cr`). If no `file_path` is provided, it searches for the newest `.sql.gz` dump.
- `ddev dbranch-convert`: Safely migrates a legacy non-bare DDEV environment into the optimal bare repository + worktree format.

## Customization
By default, the `dbranch init` script searches your current working directory for database dumps. You can customize the search directory by editing the generated `.ddev/.dbranch-config` file at the root of your hub:

```bash
# .ddev/.dbranch-config
DUMP_DIR="db_dumps"
```
