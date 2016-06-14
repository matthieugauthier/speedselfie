# Init at last time before session!
rm web/uploads/*
rm var/logs/*
rm var/cache/*
#php bin/console d:d:d --force --if-exists
/usr/local/php7.0/bin/php bin/console c:c --env=prod
/usr/local/php7.0/bin/php bin/console d:s:d --force --env=prod
/usr/local/php7.0/bin/php bin/console d:s:c --env=prod
/usr/local/php7.0/bin/php bin/console import:users equipe.csv --env=prod
/usr/local/php7.0/bin/php bin/console import:questions questions.csv --env=prod