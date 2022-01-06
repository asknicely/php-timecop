--TEST--
Function overrideing test for date_create_immutable
--SKIPIF--
<?php
$required_version = "5.5";
$required_func = array("timecop_freeze", "timecop_orig_strtotime");
include(__DIR__."/tests-skipcheck.inc.php");
--INI--
date.timezone=America/Los_Angeles
timecop.func_override=1
--FILE--
<?php
require __DIR__.'/functions.inc.php';

timecop_freeze(timecop_orig_strtotime("2012-02-29 01:23:45"));

// checking class name of instance
$dt0 = date_create_immutable();
var_dump(get_class($dt0));

$dts = array(
    // constuctor with 0 argument
    date_create_immutable(),

    // constuctor with 1 argument(null)
    execute(decorateIfTrue(PHP_VERSION_ID >= 80100, function (\Closure $callback) {
        return decorateIgnoreDeprecation(
            'timecop_date_create_immutable(): Passing null to parameter #1 ($datetime) of type string is deprecated',
            $callback
        );
    }, function () {
        return date_create_immutable(null);
    })),

    // constuctor with 1 argument(empty string)
    date_create_immutable(""),

    // constuctor with 1 argument(absolute format)
    date_create_immutable("2012-03-31 12:34:56"),

    // constuctor with 1 argument(relative format)
    date_create_immutable("+3days"),

    // constuctor with 1 argument(including timezone info)
    date_create_immutable("1970-01-01 19:00:00 EST"),

    // constuctor with 1 argument(unix time)
    date_create_immutable("@86400"),

    // constuctor with 2 argument
    date_create_immutable("now", new DateTimeZone("Asia/Tokyo")),
);

foreach ($dts as $dt) {
    var_dump($dt->format("c"));
}

--EXPECT--
string(17) "DateTimeImmutable"
string(25) "2012-02-29T01:23:45-08:00"
string(25) "2012-02-29T01:23:45-08:00"
string(25) "2012-02-29T01:23:45-08:00"
string(25) "2012-03-31T12:34:56-07:00"
string(25) "2012-03-03T01:23:45-08:00"
string(25) "1970-01-01T19:00:00-05:00"
string(25) "1970-01-02T00:00:00+00:00"
string(25) "2012-02-29T18:23:45+09:00"
