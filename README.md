# wp_cli_countfix
Fix terms and comments count plugin for wp-cli

# Install
1) create, if not exist, ~/.wp-cli/commands (mkdir -p ~/.wp-cli/commands)
2) clone at ~/.wp-cli/commands (cd ~/.wp-cli/commands; git clone https://github.com/jacsonp/wp_cli_countfix
3) Activate the plugin, using one of this methods:
  3.1) append to your ~/.wp-cli/config.yml after required:
  require:
    - commands/wp_cli_countfix/WP_CLI_Countfix.php
  3.2) Or if you do not have a config.uml, simple copy the example from the repository 
    cp -i ~/.wp-cli/commands/wp_cli_countfix/config.yml ~/.wp-cli/config.yml
