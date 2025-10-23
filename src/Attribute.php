<?php

namespace Nmc\Validation;

class Attribute
{
    /** @var array<string, Rule> */
    protected $rules = [];

    /** @var bool */
    protected $required = false;

    /** @var Attribute|null */
    protected $primaryAttribute;

    /** @var list<Attribute> */
    protected $otherAttributes = [];

    /** @var list<string> */
    protected $keyIndexes = [];

    /**
     * @param  array<array-key, Rule>  $rules
     */
    public function __construct(
        protected Validation $validation,
        protected string $key,
        protected ?string $alias = null,
        array $rules = []
    ) {
        foreach ($rules as $rule) {
            $this->addRule($rule);
        }
    }

    /**
     * Set the primary attribute
     */
    public function setPrimaryAttribute(Attribute $primaryAttribute): void
    {
        $this->primaryAttribute = $primaryAttribute;
    }

    /**
     * Set key indexes
     *
     * @param  list<string>  $keyIndexes
     */
    public function setKeyIndexes(array $keyIndexes): void
    {
        $this->keyIndexes = $keyIndexes;
    }

    /**
     * Get primary attributes
     *
     * @return Attribute|null
     */
    public function getPrimaryAttribute()
    {
        return $this->primaryAttribute;
    }

    /**
     * Set other attributes
     *
     * @param  list<Attribute>  $otherAttributes
     */
    public function setOtherAttributes(array $otherAttributes): void
    {
        $this->otherAttributes = [];
        foreach ($otherAttributes as $otherAttribute) {
            $this->addOtherAttribute($otherAttribute);
        }
    }

    /**
     * Add other attributes
     */
    public function addOtherAttribute(Attribute $otherAttribute): void
    {
        $this->otherAttributes[] = $otherAttribute;
    }

    /**
     * Get other attributes
     *
     * @return list<Attribute>
     */
    public function getOtherAttributes(): array
    {
        return $this->otherAttributes;
    }

    /**
     * Add rule
     */
    public function addRule(Rule $rule): void
    {
        $rule->setAttribute($this);
        $rule->setValidation($this->validation);
        $this->rules[$rule->getKey()] = $rule;
    }

    public function getRule(string $ruleKey): ?Rule
    {
        return $this->rules[$ruleKey] ?? null;
    }

    /**
     * @return array<string, Rule>
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Check the $ruleKey has in the rule
     */
    public function hasRule(string $ruleKey): bool
    {
        return isset($this->rules[$ruleKey]);
    }

    /**
     * Set required
     */
    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    /**
     * Set rule is required
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * Get key
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get key indexes
     *
     * @return list<string>
     */
    public function getKeyIndexes(): array
    {
        return $this->keyIndexes;
    }

    /**
     * Get value
     *
     * @return mixed
     */
    public function getValue(?string $key = null)
    {
        if ($key && $this->isArrayAttribute()) {
            $key = $this->resolveSiblingKey($key);
        }

        if (! $key) {
            $key = $this->getKey();
        }

        return $this->validation->getValue($key);
    }

    /**
     * Get that is array attribute
     */
    public function isArrayAttribute(): bool
    {
        return $this->getKeyIndexes() !== [];
    }

    /**
     * Check this attribute is using dot notation
     */
    public function isUsingDotNotation(): bool
    {
        return str_contains($this->getKey(), '.');
    }

    /**
     * Resolve sibling key
     */
    public function resolveSiblingKey(string $key): string
    {
        $indexes = $this->getKeyIndexes();
        $keys = explode('*', $key);
        $countAsterisks = count($keys) - 1;
        if (count($indexes) < $countAsterisks) {
            $indexes = array_merge($indexes, array_fill(0, $countAsterisks - count($indexes), '*'));
        }

        $args = array_merge([str_replace('*', '%s', $key)], $indexes);

        return sprintf(...$args);
    }

    /**
     * Get humanize key
     */
    public function getHumanizedKey(): string
    {
        $primaryAttribute = $this->getPrimaryAttribute();
        $key = str_replace('_', ' ', $this->key);

        // Resolve key from array validation
        if ($primaryAttribute) {
            $split = explode('.', $key);
            $key = implode(' ', array_map(function ($word): string {
                if (is_numeric($word)) {
                    $word += 1;
                }

                return Helper::snakeCase((string) $word, ' ');
            }, $split));
        }

        return ucfirst($key);
    }

    /**
     * Set alias
     */
    public function setAlias(string $alias): void
    {
        $this->alias = $alias;
    }

    /**
     * Get alias
     *
     * @return string|null
     */
    public function getAlias()
    {
        return $this->alias;
    }
}
