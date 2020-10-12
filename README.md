# ducktyping
ducktyping is a simple **reflection-based** duck typing implementation for PHP

> If it walks like a duck and it quacks like a duck, then it must be a duck

* [Installation](#installation)
* [Basic usage](#basic-usage)
* [Explanation](#explanation)
* [Further](#further)

# Installation

With Composer:
```sh
composer require alexeyyashin/ducktyping
```

Without Composer:
* Copy repository files whereever you need
* Include autoload.php
```php
include_once 'ducktyping-master/autoload.php';
```

# Basic usage

Check methods implementation
```php
duck_check(Duck::class)->implementing(Bird::class);
```
Will return `true` if class `Duck` implements all that exist in class `Bird` 
(considering "static" modifier)

Note that you can use either class name (as `string`) or `object` both in `duck_check` function and `implementing` 
method

---
Check single method existence
```php
duck_check(Duck::class)->hasMethod('fly', $check_static)
```

# Explanation

Let's start with some basic independent classes
```php
class Animal
{   
    public function run() {}
}

class Plant
{
    public function growLeaves() {}
}
```
---
Then add some child class
```php
class Bird extends Animal
{
    public function fly() {}
}
```
---
It's obvious that
```php
var_dump(new Bird instanceof Animal::class); // bool(true)
```
---
But if we add some independent class
```php
class Duck
{
    public function run() {}
    
    public function fly() {}
    
    public function quackle() {}
}
```

we will get

```php
var_dump(new Duck instanceof Bird::class); // bool(false)
```
despite the fact it can both "run" and "fly" just because class `Duck` doesn't extend class `Bird` .

To check this by [duck typing (Wiki)](https://en.wikipedia.org/wiki/Duck_typing) we can see if class `Duck` has all 
the methods from class `Bird` using ducktyping
```php
var_dump(duck_check(Duck::class)->implementing(Bird::class)); // bool(true)

// BUT, Duck does not have "growLeaves" method, so

var_dump(duck_check(Duck::class)->implementing(Plant::class)) // bool(false)
```

---

Multiple class implementation bonus
```php
class Bulbasaur extends Pokemon
{
    public static $link = "https://en.wikipedia.org/wiki/Bulbasaur";
    
    public function run() {}
    
    public function growLeaves() {}
    
    public function fight() {}
}

$check = duck_check(new Bulbasaur);

$check->implementing(Pokemon::class) // true - because of inheritance
$check->implementing(Animal::class) // true - because of "run" method
$check->implementing(Plant::class) // true - because of "growLeaves" method
$check->implementing(Bird::class) // false - because of "fly" method absence
```

# Further

I wonder wheter we should check also public properties of testing classes/objects or not. So I need to uncomment that 
moment or remove it.

May be method signatures check will be added.
