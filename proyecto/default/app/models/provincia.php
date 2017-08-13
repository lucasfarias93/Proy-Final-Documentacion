    <?php

    class Provincia extends ActiveRecord

    {

        /**

         * Retorna los menu para ser paginado

         *

         */

        public function getProvincia($page, $ppage=20)

        {

            return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');

        }

    }