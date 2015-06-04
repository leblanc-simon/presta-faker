<?php

/*
 * This file is part of the PrestaFaker package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PrestaFaker\Webservice\Sql;

class Image extends SqlAbstract implements SqlInterface
{
    public function build($id, array $values)
    {
        $replace = $this->getDefaults();
        $replace['id'] = $id;

        foreach ($values as $key => $value) {
            $replace[$key] = $value;
        }

        return $this->replaceSql(self::IMAGE_SQL, $replace);
    }

    private function getDefaults()
    {
        return [
            // Image
            'id_product' => 0, // required
            'position' => 0,
            'cover' => 0,

            // Lang
            'id_lang' => 1,
            'legend' => '',

            // Shop
            'id_shop' => 1,
        ];
    }

    const IMAGE_SQL = <<<EOF
INSERT INTO :prefix:image (`id_image`, `id_product`, `position`, `cover`)
VALUES (:id:, :id_product:, :position:, :cover:);
INSERT INTO :prefix:image_lang (`id_image`, `id_lang`, `legend`)
VALUES (:id:, :id_lang:, :legend:);
INSERT INTO :prefix:image_shop (`id_image`, `id_shop`, `cover`)
VALUES (:id:, :id_shop:, :cover:);

EOF;
}
