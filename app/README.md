Make sure
---
1. storage is writable to www-data
2. docker/mysql/data is writable to mysql


# Bad staff

- every migration is added to composer/autoload_classmap.php
    which is bad for performance