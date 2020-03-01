# CBC Programming Test

This is a simple test to verify your familiarity with PHP and it's usage, it should take you a couple of hours.

This is the beginnings of a simple application that contains an invoice class with some associated unit tests.

## Tasks

1. Add a composer.json file - Finished
2. Add "autoload" definitions for the two classes -- Finished. Used classmap for autoload in composer. We could use psr-4
3. Add PHPUnit as a development dependency - Finished
4. Write documentation for all properties and methods in the `Invoice` class - Finished
5. Update one of the tests so it isn't "tricked" by the sample `getTotals()` implementation -- Finished
6. Complete the implementation of the `getTotals()` method so that taxes are calculated correctly -- Finished
7. Fix any bugs the PHPUnit tests reveal -- Finished
8. Add at least one test that verifies the behaviour of tax-free items -- Finished
9. Implement retrieval of invoice items, with calculated tax amounts, and add a test to verify this behaviour -- Finished
10. Complete the "index.php" file so that it outputs a HTML table using the invoice instance at the top of the file -- Finished

Once you're done, create a zip file of the entire directory, including the .git directory and composer files, then send it back to us.
