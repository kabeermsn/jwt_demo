#step :: 1 
composer install

# step :: 2 install site using config_installer module

drush site-install --verbose config_installer config_installer_sync_configure_form.sync_directory=../config/sync

Providing the database in root folder, you can skip step and 2, import the database and update the DB credentials in settings.php
