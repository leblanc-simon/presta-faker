<?php

/*
 * This file is part of the PrestaFaker package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PrestaFaker\Core;

/**
 * Classe gérant la configuration de l'application
 *
 * @package     PrestaFaker
 * @subpackage  PrestaFaker\Core
 * @author      Simon Leblanc <contact@leblanc-simon.eu>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 */
class Config
{
    /**
     * Propriété contenant l'ensemble de la configuration
     * @var   array
     * @access private
     * @static
     */
    private static $config = array();


    /**
     * Méthode permettant d'ajouter un ensemble de configuration en une fois
     *
     * @param   array   $datas  Le tableau contenant plusieurs configuration (écrase les anciennes conf)
     * @access  public
     * @static
     */
    public static function add($datas)
    {
        foreach ($datas as $key => $value) {
            self::set($key, $value, true);
        }
    }


    /**
     * Méthode permettant d'ajouter une configuration
     *
     * @param   string  $name   Le nom de la configuration
     * @param   mixed   $value  La valeur de la configuration
     * @param   bool    $force  true pour forcer la mise à jour si la configuration existe déjà, false sinon
     * @return  bool            true si la mise à jour à bien eu lieu, false sinon
     * @access  public
     * @static
     */
    public static function set($name, $value, $force = true)
    {
        $name = (string)$name;

        if (isset(self::$config[$name]) === true && $force === false) {
            return false;
        }

        self::$config[$name] = $value;
        return true;
    }


    /**
     * Récupère une valeur de la configuration
     *
     * @param   string  $name     Le nom de la configuration à récupèrer
     * @param   mixed   $default  La valeur par défaut si la configuration est inexistante
     * @return  mixed             La valeur de la configuration
     * @access  public
     * @static
     */
    public static function get($name, $default = null)
    {
        $name = (string)$name;

        if (isset(self::$config[$name]) === false) {
            return $default;
        }

        return self::$config[$name];
    }
}