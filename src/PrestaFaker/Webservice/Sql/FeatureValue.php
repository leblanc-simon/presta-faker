<?php

namespace PrestaFaker\Webservice\Sql;

class FeatureValue extends SqlAbstract implements SqlInterface
{
    public function build($id, array $values)
    {
        $replace = $this->getDefaults();
        $replace['id'] = $id;

        foreach ($values as $key => $value) {
            if ('value' === $key) {
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
            'id_feature' => 0, // required

            // Lang
            'id_lang' => 1,
            'value' => '', // required
        ];
    }

    const FEATURE_SQL = <<<EOF
INSERT INTO :prefix:feature_value (`id_feature_value`, `id_feature`, `custom`)
VALUES (:id:, :id_feature:, :custom:);
INSERT INTO :prefix:feature_value_lang (`id_feature_value`, `id_lang`, `value`)
VALUES (:id:, :id_lang:, :value:);

EOF;
}
