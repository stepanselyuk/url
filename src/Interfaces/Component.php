<?php
/**
* This file is part of the League.url library
*
* @license http://opensource.org/licenses/MIT
* @link https://github.com/thephpleague/url/
* @version 4.0.0
* @package League.url
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
namespace League\Url\Interfaces;

/**
 * Value object representing simple URL component.
 *
 * @package  League.url
 * @since  4.0.0
 */
interface Component
{
    /**
     * Returns the component raw data. Can be null
     *
     * @return null|string
     */
    public function get();

    /**
     * Returns the component string representation
     *
     * @return string
     */
    public function __toString();

    /**
     * Returns the component URL string representation
     * with its optional URL delimiters
     *
     * @return string
     */
    public function getUriComponent();

    /**
     * Returns whether two component represent the same value
     * The Comparaison is based on the getUriComponent method
     *
     * @param Component $component
     *
     * @return bool
     */
    public function sameValueAs(Component $component);

    /**
     * Returns an instance with the specified string
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the modified data
     *
     * @param string $value
     *
     * @return static
     */
    public function withValue($value);
}
