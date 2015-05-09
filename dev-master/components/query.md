---
layout: default
title: The Query Component
---

# The Query component

The library proves a `League\Url\Query` class to ease complex query manipulation.

## Query creation

### Using the default constructor

Just like any other component, a new `League\Url\Query` object can be instantiated using [the default constructor](/dev-master/components/overview/#component-instantation).

~~~php
use League\Url\Query;

$query = new Query('foo=bar&p=yolo&z=');
echo $query; //display 'foo=bar&p=yolo&z'
~~~

<p class="message-warning">When using the default constructor do not prepend your query delimiter to the string as it will be considered as part of the first parameter name.</p>

<p class="message-warning">If the submitted value is not a valid query an <code>InvalidArgumentException</code> will be thrown.</p>

### Using a named constructor

In PHP, a query is assimilated to an array. So it is possible to create a `Query` object using an array or a `Traversable` object with the `Query::createFromArray` method.

~~~php
use League\Url\Query;

$query =  Query::createFromArray(['foo' => 'bar', 'p' => 'yolo', 'z' => '']);
echo $query; //display 'foo=bar&p=yolo&z'

$query =  Query::createFromArray(['foo' => 'bar', 'p' => null, 'z' => '']);
echo $query; //display 'foo=bar&z'
~~~

<p class="message-info">if a given parameter is <code>null</code> it won't be taken into account when building the <code>Query</code> obejct</p>


## Query representations

### String representation

Basic query representations is done using the following methods:

~~~php
use League\Url\Query;

$query = new Query('foo=bar&p=y+olo&z=');
$query->get();             //return 'foo=bar&p=y%20olo&z'
$query->__toString();      //return 'foo=bar&p=y%20olo&z'
$query->getUriComponent(); //return '?foo=bar&p=y%20olo&z'
~~~

### Array representation

A query can be represented as an array of its internal parameters. Through the use of the `Query::toArray` method the class returns the object array representation.

~~~php
use League\Url\Query;

$query = new Query('foo=bar&p=y+olo&z=');
$query->toArray();
// returns [
//     'foo' => 'bar',
//     'p'   => 'y olo',
//     'z'   => '',
// ]
~~~

## Accessing Query content

### Countable and IteratorAggregate

The class provides several methods to works with its parameters. The class implements PHP's `Countable` and `IteratorAggregate` interfaces. This means that you can count the number of parameters and use the `foreach` construct to iterate overs them.

~~~php
use League\Url\Query;

$query = new Query('foo=bar&p=y+olo&z=');
count($query); //return 4
foreach ($query as $parameter => $value) {
    //do something meaningful here
}
~~~

### Parameter name

If you are interested in getting all the parametes name you can do so using the `Query::offsets` method like show below:

~~~php
use League\Url\Query;

$query = new Query('foo=bar&p=y+olo&z=');
$query->offsets();        //returns ['foo', 'p', 'z'];
$query->offsets('bar');   //returns ['foo'];
$query->offsets('gweta'); //returns [];
~~~

The methods returns all the parameters name, but if you supply an argument, only the parameters name whose value equals the argument are returned.

If you want to be sure that a parameter name exists before using it you can do so using the `Query::hasOffset` method which returns `true` if the submitted parameter name exists in the current object.

~~~php
use League\Url\Query;

$query = new Query('foo=bar&p=y+olo&z=');
$query->hasOffset('p');    //returns true
$query->hasOffset('john'); //returns false
~~~

### Parameter value

If you are only interested in a given parameter you can access it directly using the `Query::getParameter` method as show below:

~~~php
use League\Url\Query;

$query = new Query('foo=bar&p=y+olo&z=');
$query->getParameter('foo');          //returns 'bar'
$query->getParameter('gweta');        //returns null
$query->getParameter('gweta', 'now'); //returns 'now'
~~~

The method returns the value of a specific parameter name. If the offset does not exists it will return the value specified by the second argument which default to `null`.

## Modifying a query

<p class="message-notice">If the modifications does not change the current object, it is returned as is, otherwise, a new modified object is returned.</p>

<p class="message-warning">When a modification fails a <code>InvalidArgumentException</code> is thrown.</p>

### Remove parameters

To remove parameters from the current object and returns a new `Query` object without them you can use the `Query::without` method. This methods expected a single argument `$offsets` which is an array containing a list of parameter names to remove.

~~~php
use League\Url\Query;

$query    = new Query('foo=bar&p=y+olo&z=');
$newQuery = $query->without(['foo', 'p']);
echo $newQuery; //displays 'z'
~~~

### Add or Update parameters

If you want to add or update the query parameters you need to use the `Query::merge` method. This method expects a single argument in form of an `array` or a `Traversable` object.

<p class="message-warning">Before merging parameters whose value equals <code>null</code> are filter out.</p>

~~~php
use League\Url\Query;

$query = Query::createFromArray(['foo' => 'bar', 'baz' => 'toto']);
$new = $alt->merge(['foo' => 'jane', 'baz' => null, 'r' => '']);
$new->get(); //returns foo=jane&baz=toto&r
// the 'r' parameter was added
// the 'baz' parameter was not changed
~~~