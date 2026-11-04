<?php


/**
 * php/modules/trait/accessor.php
 * 
 * Getter/Setter Magic Accessor Traits
 *
 * @author Harald Hemm <hemm@nexgo.de>
 * @copyright 2026 Harald Hemm Landstuhl (HaHeLa)
 * @license CC BY-NC-ND HaHeLa
 * @version 1.0.0
 * @since 1.0.0
 */


declare(strict_types=1);


namespace HaHeLa;


/**
 * Getter/Setter Magic Accessor Traits
 * 
 * @throws \BadMethodCallException
 */
trait AccessorTrait
{
    public function __call(string $method, array $args): mixed
    {
        if (preg_match('/^(get|set)([A-Z][A-Za-z0-9]*)$/', $method, $m)) {
            $property = $this->methodToProperty($m[2]);
        
            if (!property_exists($this, $property)) {
                throw new \BadMethodCallException(
                    sprintf('Property %s::$%s existiert nicht.', static::class, $property)
                );
            }
        
            if ($m[1] === 'get') {
                return $this->{$property};
            }
        
            // Setter: fluent interface
            $this->{$property} = $args[0] ?? null;
            return $this;
        }
        
        throw new \BadMethodCallException(
            sprintf('Methode %s::%s() existiert nicht.', static::class, $method)
        );
    }
    
        
    /** Private (Hilfs-)Funktionen ******************************************/
    
    
    /**
     * Wandelt den Methodenbestandteil in einen Property-Namen um:
     *   "NameEn"   -> "name_en"   (CamelCase -> snake_case)
     *   "Name"     -> "name"
     *   "NameURL"  -> "name_url" (auch Akronyme)
     * 
     * @param string $part
     * @return string
     */
    private function methodToProperty(string $part): string
    {
        // 1. Trenne den Präfix (get/set/has/is) ab, falls vorhanden 
        // (z.B. "getAlpha2" -> "Alpha2")
        $part = preg_replace('/^(get|set|has|is)/', '', $part);

        // 2. Unterstrich vor Großbuchstaben einfuegen, wenn ein 
        // Kleinbuchstabe oder eine Zahl vorausgeht
        $snake = preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', $part);
        
        // 3. Unterstrich VOR einer Zahl einfuegen, wenn ein Buchstabe 
        // vorausgeht (für Alpha2 -> Alpha_2)
        $snake = preg_replace('/([a-zA-Z])([0-9])/', '$1_$2', $snake);

        // 4. Unterstrich NACH einer Zahl einfuegen, falls danach ein 
        // Buchstabe folgt (z.B. 2Alpha -> 2_Alpha)
        $snake = preg_replace('/([0-9])([a-zA-Z])/', '$1_$2', $snake);

        // 5. Alles in Kleinbuchstaben umwandeln
        return strtolower(lcfirst($snake));
    }
}
