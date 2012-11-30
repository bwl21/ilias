<?php

namespace Igorw\Ilias;

class Environment implements \ArrayAccess
{
    private $vars;

    public function __construct(array $vars = array())
    {
        $this->vars = $vars;

        $this->vars['define']   = new Macro\DefineMacro();
        $this->vars['lambda']   = new Macro\LambdaMacro();
        $this->vars['if']       = new Macro\IfMacro();

        $this->vars['+'] = new Func\PlusFunc();
        $this->vars['-'] = new Func\MinusFunc();
        $this->vars['>'] = new Func\GreaterThanFunc();
        $this->vars['<'] = new Func\LessThanFunc();
    }

    public function offsetGet($key)
    {
        return isset($this->vars[$key]) ? $this->vars[$key] : null;
    }

    public function offsetSet($key, $value)
    {
        $this->vars[$key] = $value;
    }

    public function offsetExists($key)
    {
        return isset($this->vars[$key]);
    }

    public function offsetUnset($key)
    {
        unset($this->vars[$key]);
    }

    public function evaluate(array $ast)
    {
        $result = null;

        foreach ($ast as $sexpr) {
            $result = $this->evaluateExpr($sexpr);
        }

        return $result;
    }

    private function evaluateExpr($sexpr)
    {
        if (!is_array($sexpr)) {
            return $this->normalizeValue($sexpr);
        }

        $op = array_shift($sexpr);
        $op = is_array($op) ? $this->evaluateExpr($op) : $op;

        if (!isset($this->vars[$op])) {
            throw new \RuntimeException(sprintf('Tried to invoke non-existent function %s', $op));
        }

        $fn = $this->vars[$op];
        if ($fn instanceof Macro\Macro) {
            return $this->evaluateMacro($fn, $sexpr);
        }

        return $this->evaluateOp($fn, $sexpr);
    }

    private function normalizeValue($value)
    {
        if ($value instanceof QuotedValue) {
            return $value->get();
        }

        if (is_int($value)) {
            return $value;
        }

        if (is_string($value)) {
            return $this->vars[$value];
        }

        return $value;
    }

    private function evaluateOp($fn, array $args)
    {
        $args = $this->evaluateArgs($args);
        return call_user_func_array($fn, $args);
    }

    private function evaluateMacro($macro, array $args)
    {
        return $macro->invoke($this, $args);
    }

    private function evaluateArgs(array $args)
    {
        return array_map(
            function ($arg) {
                return $this->evaluateExpr($arg);
            },
            $args
        );
    }
}
