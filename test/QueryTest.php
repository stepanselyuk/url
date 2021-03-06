<?php

namespace League\Url\Test;

use ArrayIterator;
use League\Url\Query;
use PHPUnit_Framework_TestCase;
use StdClass;

/**
 * @group query
 */
class QueryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Query
     */
    protected $query;

    public function setUp()
    {
        $this->query = new Query('kingkong=toto');
    }

    public function testIterator()
    {
        $this->assertSame(['kingkong' => 'toto'], iterator_to_array($this->query, true));
    }

    public function testGetUriComponent()
    {
        $this->assertSame('?kingkong=toto', $this->query->getUriComponent());
    }

    public function testGetUriComponentWithoutArgument()
    {
        $query = new Query();
        $this->assertSame('', $query->getUriComponent());
    }

    public function testCreateFromArrayWithTraversable()
    {
        $query = Query::createFromArray(new ArrayIterator(['john' => 'doe the john']));
        $this->assertCount(1, $query);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateFromArrayFailed()
    {
        Query::createFromArray(new \StdClass);
    }

    public function testSameValueAs()
    {
        $empty_query = new Query();
        $this->assertFalse($empty_query->sameValueAs($this->query));
        $query = $empty_query->merge($this->query);
        $this->assertInstanceof('League\Url\Interfaces\Query', $query);
        $this->assertTrue($query->sameValueAs($this->query));
    }

    /**
     * @param $input
     * @param $expected
     * @dataProvider validMergeValue
     */
    public function testMerge($input, $expected)
    {
        $query = $this->query->merge($input);
        $this->assertSame($expected, (string) $query);
    }

    public function validMergeValue()
    {
        return [
            'with new data' => [
                Query::createFromArray(['john' => 'doe the john']),
                'kingkong=toto&john=doe%20the%20john',
            ],
            'with the same data' => [
                new Query('kingkong=tata'),
                'kingkong=tata',
            ],
            'without new data' => [
                new Query(''),
                'kingkong=toto',
            ],
            'with array' => [
                ['john' => 'doe the john'],
                'kingkong=toto&john=doe%20the%20john',
            ],
            'with empty array' => [
                [],
                'kingkong=toto',
            ],
        ];
    }

    public function testGetParameter()
    {
        $this->assertSame('toto', $this->query->getParameter('kingkong'));
    }

    public function testGetParameterWithDefaultValue()
    {
        $expected = 'toofan';
        $this->assertSame($expected, $this->query->getParameter('togo', $expected));
    }

    public function testHasOffset()
    {
        $this->assertTrue($this->query->hasOffset('kingkong'));
        $this->assertFalse($this->query->hasOffset('togo'));
    }

    public function testCountable()
    {
        $this->assertSame(1, count($this->query));
    }

    public function testKeys()
    {
        $query = Query::createFromArray([
            'foo' => 'bar',
            'baz' => 'troll',
            'lol' => 3,
            'toto' => 'troll'
        ]);
        $this->assertCount(0, $query->offsets('foo'));
        $this->assertSame(['foo'], $query->offsets('bar'));
        $this->assertCount(1, $query->offsets('3'));
        $this->assertSame(['lol'], $query->offsets('3'));
        $this->assertSame(['baz', 'toto'], $query->offsets('troll'));
    }

    public function testDotFromString()
    {
        $query = new Query('foo.bar=baz');
        $this->assertSame('foo.bar=baz', (string) $query);
    }

    public function testDotFromArray()
    {
        $query = Query::createFromArray(['foo.bar' => 'baz']);
        $this->assertSame('foo.bar=baz', (string) $query);
    }

    public function testStringWithoutContent()
    {
        $query = new Query('foo&bar&baz');

        $this->assertCount(3, $query->offsets());
        $this->assertSame(['foo', 'bar', 'baz'], $query->offsets());
        $this->assertSame('', $query->getParameter('foo'));
        $this->assertSame('', $query->getParameter('bar'));
        $this->assertSame('', $query->getParameter('baz'));
    }

    public function testToArray()
    {
        $expected = ['foo' => '', 'bar' => '', 'baz' => '', 'to.go' => 'toofan'];
        $query = new Query('foo&bar&baz&to.go=toofan');
        $this->assertSame($expected, $query->toArray());
        $this->assertSame($expected, json_decode(json_encode($query), true));
    }

    public function testWithout()
    {
        $query = new Query('foo&bar&baz&to.go=toofan');
        $res = $query->without(['foo', 'to.go']);
        $this->assertSame('bar&baz', (string) $res);
    }

    public function testWithoutNoDataModified()
    {
        $query = new Query('foo&bar&baz&to.go=toofan');
        $res = $query->without(['foo', 'unknown']);
        $this->assertEquals('bar&baz&to.go=toofan', (string) $res);
    }

    public function invalidQueryStrings()
    {
        return [
            'true'      => [ true ],
            'false'     => [ false ],
            'array'     => [ [ 'baz=bat' ] ],
            'object'    => [ (object) [ 'baz=bat' ] ],
        ];
    }

    /**
     * @dataProvider invalidQueryStrings
     * @expectedException InvalidArgumentException
     */
    public function testWithQueryRaisesExceptionForInvalidQueryStrings($query)
    {
        new Query($query);
    }
}
