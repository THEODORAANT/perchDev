# perch
Replaced the legacy ereg fallback with PCRE-based validation and simplified safe_stripslashes() in both PerchUtil libraries to drop deprecated magic quotes logic.

Removed runtime magic quotes handling from the MySQL helper and PHPMailer in both code paths so no deprecated functions are invoked during database quoting or file encoding.

Modernized Perch Shop helpers by using array_walk_recursive() for flattening and a closure in place of create_function() for delimiter conversion callbacks.
