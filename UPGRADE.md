This is a list of tasks that have to be done manually when upgrading to the
specified version from a lower one.

### 1.2.0
* The table `rb_comments` now contains a `spam` column. To add it, execute the
following sql : ```ALTER TABLE  `rb_comments` ADD  `spam` TINYINT NOT NULL DEFAULT  '0' AFTER  `visible`;```
