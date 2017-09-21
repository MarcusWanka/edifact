<?php

namespace Metroplex\Edifact;

use Metroplex\Edifact\Control\Characters as ControlCharacters;
use Metroplex\Edifact\Control\CharactersInterface as ControlCharactersInterface;

/**
 * Serialize a bunch of segments into an EDI message string.
 */
class Serializer
{
    private $characters;

    public function __construct(ControlCharactersInterface $characters = null)
    {
        if ($characters === null) {
            $characters = new ControlCharacters;
        }
        $this->characters = $characters;
    }


    /**
     * Serialize all the passed segments.
     *
     * @return string
     */
    public function serialize($segments)
    {
        $message = "UNA";
        $message .= $this->characters->getComponentSeparator();
        $message .= $this->characters->getDataSeparator();
        $message .= $this->characters->getDecimalPoint();
        $message .= $this->characters->getEscapeCharacter();
        $message .= " ";
        $message .= $this->characters->getSegmentTerminator();

        foreach ($segments as $segment) {
            $message .= $segment->getName();
            foreach ($segment->getAllElements() as $element) {
                $message .= $this->characters->getDataSeparator();

                if (is_array($element)) {
                    $message .= implode($this->characters->getComponentSeparator(), array_map([$this, 'escape'], $element));
                } else {
                    $message .= $this->escape($element);
                }
            }

            $message .= $this->characters->getSegmentTerminator();
        }

        return $message;
    }


    /**
     * Escapes control characters.
     *
     * @param string $string The string to be escaped
     *
     * @return string
     */
    public function escape($string)
    {
        $characters = [
            $this->characters->getEscapeCharacter(),
            $this->characters->getComponentSeparator(),
            $this->characters->getDataSeparator(),
            $this->characters->getSegmentTerminator(),
        ];

        $search = [];
        $replace = [];
        foreach ($characters as $character) {
            $search[] = $character;
            $replace[] = $this->characters->getEscapeCharacter() . $character;
        }

        return str_replace($search, $replace, $string);
    }
}
