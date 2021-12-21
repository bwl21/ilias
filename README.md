# the problem

```
php macro-expand.php "(defmacro plus (a b) (list '+ a b))(defmacro pl (a b) (list 'plus a b))(defmacro p () (list 'pl 1 2) )" "(p)"
```

yields

```
gitpod /workspace/ilias/examples $ php macro-expand.php "(defmacro plus (a b) (list '+ a b))(defmacro pl (a b) (list 'plus a b))(defmacro p () (list 'pl 1 2) )" "(p)"PHP Fatal error:  Uncaught TypeError: Igorw\Ilias\Walker::expand(): Argument #2 ($form) must be of type Igorw\Ilias\Form\Form, int given, called in /workspace/ilias/src/Igorw/Ilias/Walker.php on line 88 and defined in /workspace/ilias/src/Igorw/Ilias/Walker.php:11
Stack trace:
#0 /workspace/ilias/src/Igorw/Ilias/Walker.php(88): Igorw\Ilias\Walker->expand()
#1 [internal function]: Igorw\Ilias\Walker->Igorw\Ilias\{closure}()
#2 /workspace/ilias/src/Igorw/Ilias/Walker.php(90): array_map()
#3 /workspace/ilias/src/Igorw/Ilias/Walker.php(80): Igorw\Ilias\Walker->expandList()
#4 /workspace/ilias/src/Igorw/Ilias/Walker.php(22): Igorw\Ilias\Walker->expandSubLists()
#5 /workspace/ilias/src/Igorw/Ilias/Walker.php(29): Igorw\Ilias\Walker->expand()
#6 /workspace/ilias/src/Igorw/Ilias/Walker.php(29): Igorw\Ilias\Walker->expand()
#7 /workspace/ilias/src/Igorw/Ilias/Walker.php(29): Igorw\Ilias\Walker->expand()
#8 /workspace/ilias/src/Igorw/Ilias/Program.php(28): Igorw\Ilias\Walker->expand()
#9 /workspace/ilias/examples/macro-expand.php(29): Igorw\Ilias\Program->evaluate()
#10 {main}
  thrown in /workspace/ilias/src/Igorw/Ilias/Walker.php on line 11
```

# Ilias

Naive LISP implementation in PHP. For something more complete, check out
[Lisphp](https://github.com/lisphp/lisphp).

Check out the [s-expression blog posts](https://igor.io/2012/12/06/sexpr.html)
explaining the implementation of Ilias.

## Usage

```php
use Igorw\Ilias\Program;
use Igorw\Ilias\Lexer;
use Igorw\Ilias\Reader;
use Igorw\Ilias\FormTreeBuilder;
use Igorw\Ilias\Walker;
use Igorw\Ilias\Environment;

$program = new Program(
    new Lexer(),
    new Reader(),
    new FormTreeBuilder(),
    new Walker()
);

$env = Environment::standard();
$value = $program->evaluate($env, '(+ 1 2)');
var_dump($value);
```

will output:

```
int(3)
```
