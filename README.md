# nia - SQL Operation

Component which contains simple CRUD operations using the `nia/sql-adapter` component.

## Installation

Require this package with Composer.

```bash
composer require nia/sql-operation
```
## Tests
To run the unit test use the following command:

```bash
$ cd /path/to/nia/component/
$ phpunit --bootstrap=vendor/autoload.php tests/
```

## How to use
The following sample shows you how to use the classes for simple CRUD operations.

```php
// create usert
$operation = new InsertOperation($writingAdapter);
$id = $operation->insert('user', [
    'email' => 'foo@bar.baz',
    'password' => password_hash($password, PASSWORD_DEFAULT)
]);

// update user (change email address)
$operation = new UpdateOperation($writingAdapter);
$operation->update('user', $id, [
    'email' => 'faz@baz.boo'
]);

// fetch user
$operation = new FetchOperation($readingAdapter);
$user = $operation->fetch('user', $id);

// delete user
$operation = new UpdateOperation($writingAdapter);
$operation->delete('user', $id);

```
