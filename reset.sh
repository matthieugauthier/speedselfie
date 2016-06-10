rm web/upload/*
php bin/console d:d:d --force --if-exists
php bin/console d:d:c
php bin/console d:s:c
php bin/console import:users equipe.csv
php bin/console import:questions questions.csv