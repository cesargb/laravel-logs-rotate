# Upgrading

Because there are many breaking changes an upgrade is not that easy. There are many edge cases this guide does not cover. We accept PRs to improve this guide.

## From v1 to v2

- rename config params from `foreing_files` to `foreign_files`, if you have a config file with the old name, rename it to `config/rotate.php`
- remove feat archive.
- remove config params `archive_dir`
