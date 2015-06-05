<?php

/*
 * This file is part of the PrestaFaker package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PrestaFaker\Faker\Provider\Car;

use Faker\Provider\Base;
use PrestaFaker\Core\Config;
use PrestaFaker\Faker\Provider\LevelInterface;
use PrestaFaker\Prestashop\Link;
use Symfony\Component\Finder\Finder;

class Image
    extends Base
{
    static protected $images = array();


    public function init(LevelInterface $category)
    {
        $finder = new Finder();
        $finder->files()->in(Config::get('images_dir'));

        $all_categories = $category->getAll();
        $all_categories = $this->convertCategories($all_categories);

        foreach ($finder as $file) {
            $directory = str_replace(Config::get('images_dir').DIRECTORY_SEPARATOR, '', $file->getPath());

            $parent = $this->searchLastCategory($all_categories, $directory);
            $this->addImageInCategory($parent, $file);
        }
    }


    public function image($category)
    {
        $category = Link::rewrite($category);
        if (isset(static::$images[$category]) === false) {
            return null;
        }

        return $this->randomElement(static::$images[$category]);
    }


    private function convertCategories($categories)
    {
        $final_categories = array();

        foreach ($categories as $key => $value) {
            if (is_numeric($key) === false) {
                $key = Link::rewrite($key);
            }

            if (is_array($value) === true) {
                $value = $this->convertCategories($value);
            } else {
                $value = Link::rewrite($value);
            }

            $final_categories[$key] = $value;
        }

        return $final_categories;
    }


    private function searchLastCategory($all_categories, $directory)
    {
        $categories = explode('/', $directory);
        $depth = count($categories);

        $parent = $all_categories;
        for ($iterator = 0; $iterator < $depth; $iterator++) {
            if (isset($parent[$categories[$iterator]]) === true) {
                $parent = $parent[$categories[$iterator]];
            } else {
                break;
            }
        }

        return $parent;
    }


    private function addImageInCategory($category, \SplFileInfo $file)
    {
        if (is_array($category) === true) {
            foreach ($category as $value) {
                $this->addImageInCategory($value, $file);
            }
        } else {
            if (isset(self::$images[$category]) === false) {
                self::$images[$category] = array();
            }
            self::$images[$category][] = $file->getPathname();
        }
    }
}
