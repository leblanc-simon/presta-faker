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

class Product extends SqlAbstract implements SqlInterface
{
    public function build($id, array $values)
    {
        $replace = $this->getDefaults();
        $replace['id'] = $id;

        $replace_features = [
            'id' => $id,
        ];
        $sql_features = '';

        foreach ($values as $key => $value) {
            if (substr($key, 0, 5) === 'meta_' || in_array($key, ['name', 'link_rewrite', 'description']) === true) {
                foreach ($value as $id_lang => $string) {
                    // Hummm... very, very dirty, but it's only for demo !
                    // You're not ok with that : Code it yourself !!!
                    $replace[$key] = $string;
                    $replace['id_lang'] = $id_lang;
                }
                continue;
            }

            if ('associations' === $key) {
                foreach ($value as $association => $association_datas) {
                    if ('product_feature' === $association) {
                        foreach ($association_datas as $relation) {
                            $replace_features['id_feature'] = $relation['id'];
                            $replace_features['id_feature_value'] = $relation['id_feature_value'];

                            $sql_features .= $this->replaceSql(self::PRODUCT_FEATURE_SQL, $replace_features);
                        }
                    }
                }
                continue;
            }

            $replace[$key] = $value;
        }

        return $this->replaceSql(self::PRODUCT_SQL, $replace).$sql_features;
    }

    private function getDefaults()
    {
        return [
            // Product
            'id_supplier' => 0,
            'id_manufacturer' => 0,
            'id_category_default' => 0, // required
            'id_shop_default' => 1,
            'id_tax_rules_group' => 0,
            'on_sale' => 0,
            'online_only' => 0,
            'ean13' => '',
            'upc' => '',
            'ecotax' => 0,
            'quantity' => 0,
            'minimal_quantity' => 0,
            'price' => 0, // required
            'wholesale_price' => 0,
            'unity' => '',
            'unit_price_ratio' => 0,
            'additional_shipping_cost' => 0,
            'reference' => '0',
            'supplier_reference' => '',
            'location' => '',
            'width' => 0,
            'height' => 0,
            'depth' => 0,
            'weight' => 0,
            'out_of_stock' => 2,
            'quantity_discount' => 0,
            'customizable' => 0,
            'uploadable_files' => 0,
            'text_fields' => 0,
            'active' => 1,
            'redirect_type' => '',
            'id_product_redirected' => 0,
            'available_for_order' => 1,
            'available_date' => '0000-00-00',
            'condition' => 'new',
            'show_price' => 1,
            'indexed' => 1,
            'visibility' => 'both',
            'cache_is_pack' => 0,
            'cache_has_attachments' => 0,
            'is_virtual' => 0,
            'cache_default_attribute' => 0,
            'advanced_stock_management' => 0,

            // Lang
            'id_shop' => 1,
            'id_lang' => 1,
            'description' => '',
            'description_short' => '',
            'link_rewrite' => '', // required
            'meta_description' => '',
            'meta_keywords' => '',
            'meta_title' => '',
            'name' => '', // required
            'available_now' => '',
            'available_later' => '',
        ];
    }

    const PRODUCT_SQL = <<<EOF
INSERT INTO :prefix:product (`id_product`, `id_supplier`, `id_manufacturer`, `id_category_default`, `id_shop_default`, `id_tax_rules_group`, `on_sale`, `online_only`, `ean13`, `upc`, `ecotax`, `quantity`, `minimal_quantity`, `price`, `wholesale_price`, `unity`, `unit_price_ratio`, `additional_shipping_cost`, `reference`, `supplier_reference`, `location`, `width`, `height`, `depth`, `weight`, `out_of_stock`, `quantity_discount`, `customizable`, `uploadable_files`, `text_fields`, `active`, `redirect_type`, `id_product_redirected`, `available_for_order`, `available_date`, `condition`, `show_price`, `indexed`, `visibility`, `cache_is_pack`, `cache_has_attachments`, `is_virtual`, `cache_default_attribute`, `date_add`, `date_upd`, `advanced_stock_management`)
VALUES (:id:, :id_supplier:, :id_manufacturer:, :id_category_default:, :id_shop_default:, :id_tax_rules_group:, :on_sale:, :online_only:, :ean13:, :upc:, :ecotax:, :quantity:, :minimal_quantity:, :price:, :wholesale_price:, :unity:, :unit_price_ratio:, :additional_shipping_cost:, :reference:, :supplier_reference:, :location:, :width:, :height:, :depth:, :weight:, :out_of_stock:, :quantity_discount:, :customizable:, :uploadable_files:, :text_fields:, :active:, :redirect_type:, :id_product_redirected:, :available_for_order:, :available_date:, :condition:, :show_price:, :indexed:, :visibility:, :cache_is_pack:, :cache_has_attachments:, :is_virtual:, :cache_default_attribute:, NOW(), NOW(), :advanced_stock_management:);
INSERT INTO :prefix:product_lang (`id_product`, `id_shop`, `id_lang`, `description`, `description_short`, `link_rewrite`, `meta_description`, `meta_keywords`, `meta_title`, `name`, `available_now`, `available_later`)
VALUES (:id:, :id_shop:, :id_lang:, :description:, :description_short:, :link_rewrite:, :meta_description:, :meta_keywords:, :meta_title:, :name:, :available_now:, :available_later:);
INSERT INTO :prefix:product_shop (`id_product`, `id_shop`, `id_category_default`, `id_tax_rules_group`, `on_sale`, `online_only`, `ecotax`, `minimal_quantity`, `price`, `wholesale_price`, `unity`, `unit_price_ratio`, `additional_shipping_cost`, `customizable`, `uploadable_files`, `text_fields`, `active`, `redirect_type`, `id_product_redirected`, `available_for_order`, `available_date`, `condition`, `show_price`, `indexed`, `visibility`, `cache_default_attribute`, `advanced_stock_management`, `date_add`, `date_upd`)
VALUES (:id:, :id_shop:, :id_category_default:, :id_tax_rules_group:, :on_sale:, :online_only:, :ecotax:, :minimal_quantity:, :price:, :wholesale_price:, :unity:, :unit_price_ratio:, :additional_shipping_cost:, :customizable:, :uploadable_files:, :text_fields:, :active:, :redirect_type:, :id_product_redirected:, :available_for_order:, :available_date:, :condition:, :show_price:, :indexed:, :visibility:, :cache_default_attribute:, :advanced_stock_management:, NOW(), NOW());
INSERT INTO :prefix:category_product (`id_product`, `id_category`, `position`)
VALUES (:id:, :id_category_default:, 0);

EOF;
    const PRODUCT_FEATURE_SQL = <<<EOF
INSERT INTO :prefix:feature_product (`id_product`, `id_feature`, `id_feature_value`)
VALUES (:id:, :id_feature:, :id_feature_value:);

EOF;
}
