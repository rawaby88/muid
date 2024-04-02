<?php

namespace Rawaby88\Muid;

class MuidService
{
    protected int $length;

    protected ?string $prefix;

    protected array $sets;

    public function __construct($length, $prefix = null)
    {
        $this->length = $length;
        $this->prefix = $prefix;
        $this->getSetsFromConfig();
    }

    private function getSetsFromConfig()
    {
        $this->sets[] = config('muid.alfa_small');
        $this->sets[] = config('muid.digits');
        if (config('muid.allow_capital')) {
            $this->sets[] = config('muid.alfa_capital');
        }
    }

    public function generate(): string
    {
        return $this->randomString();
    }

    private function randomString(): string
    {
        $all = str_split(implode($this->sets));
        $string = $this->prefix.base_convert(time(), 10, 36);

        foreach ($this->sets as $set) {
            $string .= $set[array_rand(str_split($set))];
        }

        $iterationCount = $this->length - strlen($string);

        for ($i = 0; $i < $iterationCount; $i++) {
            $string .= $all[array_rand($all)];
        }

        return $string;
    }
}
