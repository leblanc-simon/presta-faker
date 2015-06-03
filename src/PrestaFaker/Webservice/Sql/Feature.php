<?php

namespace PrestaFaker\Webservice\Sql;

class Feature extends SqlAbstract implements SqlInterface
{
    static private $position = 0;

    public function build($id, array $values)
    {
        $replace = $this->getDefaults();
        $replace['id'] = $id;
        $replace['position'] = self::$position++;

        foreach ($values as $key => $value) {
            if ('name' === $key) {
                foreach ($value as $id_lang => $string) {
                    // Hummm... very, very dirty, but it's only for demo !
                    // You're not ok with that : Code it yourself !!!
                    $replace[$key] = $string;
                    $replace['id_lang'] = $id_lang;
                }
                continue;
            }

            $replace[$key] = $value;
        }

        return $this->replaceSql(self::FEATURE_SQL, $replace);
    }

    private function getDefaults()
    {
        return [
            // Feature
            'position' => 0,

            // Lang
            'id_lang' => 1,
            'name' => '', // required

            // Shop
            'id_shop' => 1,
        ];
    }

    const FEATURE_SQL = <<<EOF
INSERT INTO :prefix:feature (`id_feature`, `position`)
VALUES (:id:, :position:);
INSERT INTO :prefix:feature_lang (`id_feature`, `id_lang`, `name`)
VALUES (:id:, :id_lang:, :name:);
INSERT INTO :prefix:feature_shop (`id_feature`, `id_shop`)
VALUES (:id:, :id_shop:);

EOF;
}
