rm web/uploads/*
#php bin/console d:d:d --force --if-exists
/usr/local/php7.0/bin/php bin/console bin/console d:s:d --force
/usr/local/php7.0/bin/php bin/console bin/console d:s:c
/usr/local/php7.0/bin/php bin/console bin/console import:users equipe.csv
/usr/local/php7.0/bin/php bin/console bin/console import:questions questions.csv