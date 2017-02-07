# dxw/result

Meant to be a drop-in replacement for [nikita2206/result](https://github.com/nikita2206/result), with one addition: `wrap()`.

`wrap()` was inspired by [github.com/pkg/errors](https://godoc.org/github.com/pkg/errors#Wrap).

## Installation

    composer require dxw/result

## Usage

Returning values:

    function myfunc(): \Dxw\Result\Result
    {
        if (/* error */) {
            // getErr() will return 'something went wrong'
            return \Dxw\Result\Result::err('something went wrong');
        }

        return \Dxw\Result\Result::ok($value);
    }

Handling `Result` values:

    $result = myfunc();
    if ($result->isErr()) {
        echo sprintf("Error: %s\n", $result->getErr());
        exit(1);
    }
    $value = $result->unwrap();

Returning errors from other errors:

    function anotherfunc(): \Dxw\Result\Result
    {
        $result = myfunc();
        if ($result->isErr()) {
            // getErr() will return 'got invalid value: something went wrong'
            return $result->wrap('got invalid value');
        }
        // do something with $result->unwrap()
    }
