<?php

/**
 * Backend - KumbiaPHP Backend
 * PHP version 5
 * LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * ERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Helper
 * @license http://www.gnu.org/licenses/agpl.txt GNU AFFERO GENERAL PUBLIC LICENSE version 3.
 * @author Manuel José Aguirre Garcia <programador.manuel@gmail.com>
 */
Load::models('menus');

/**
 * Crea el html para los menus de la app.
 */
class Menu {
    /**
     * Constante que define que solo va a mostrar los
     * Items del menu para app
     */
    const APP = 1;

    /**
     * Constante que define que solo va a mostrar los
     * Items del menu para el backend
     */
    const BACKEND = 2;

    /**
     * Id del usuario logueado actualmente
     *
     * @var int 
     */
    protected static $_id_user = NULL;

    /**
     * Crea los menus para la app.
     * 
     * @param  int $id_user 
     * @param  int $entorno 
     * @return string          
     */
    public static function render($id_user, $entorno = self::BACKEND) {
        self::$_id_user = $id_user;
        $rL = new Menus();
        $registros = $rL->obtener_menu_por_usuario($id_user, $entorno);
        $html = '';
        if ($registros) {
            $html .= '<ul class="nav nav-list">' . PHP_EOL;
            foreach ($registros as $e) {
                $html .= self::generarItems($e, $entorno);
            }
            $html .= '</ul>' . PHP_EOL;
        }
        return $html;
    }

    /**
     * Genera los items del menu.
     * 
     * @param  Model $objeto_menu 
     * @param  int $entorno     
     * @return string              
     */
    protected static function generarItems($objeto_menu, $entorno) {
        $sub_menu = $objeto_menu->get_sub_menus(self::$_id_user, $entorno);
        $class = 'menu_' . str_replace('/', '_', $objeto_menu->url);
		if ($objeto_menu->clases){
			$class = $objeto_menu->clases;// . (self::es_activa($objeto_menu->url) ? ' active' : '');	
		}else{
			$class = 'fa-list';
		}
        
        if ($sub_menu) {
        	
			$html = "<li class=''>" .
            			"<a href='#' class='dropdown-toggle'>
							<i class='menu-icon fa ".$class."'></i>
							<span class='menu-text'> ".$objeto_menu->nombre." </span>

							<b class='arrow fa fa-angle-down'></b>
						</a>

						<b class='arrow'></b>";
            
        } else {
        	
			
            $html = "<li class=''>".
            			"<a data-url='".$objeto_menu->url."' href='".PUBLIC_PATH.$objeto_menu->url."'>".
                    
                            '<i class="menu-icon fa '.$class.'"></i>'.
                            '<span class="menu-text"> '.$objeto_menu->nombre.' </span></a>'.
                            '<b class="arrow"></b>' . PHP_EOL;
                    
        }
        if ($sub_menu) {
            $html .= '<ul class="submenu">' . PHP_EOL;
            foreach ($sub_menu as $e) {
                $html .= self::generarItems($e, $entorno);
            }
            $html .= '</ul>' . PHP_EOL;
        }
        return $html . "</li>" . PHP_EOL;
    }

    /**
     * Verifica si el item es el de la url donde nos encontramos.
     * 
     * @param  string $url 
     * @return boolean      
     */
    protected static function es_activa($url) {
        $url_actual = substr(Router::get('route'), 1);
        return (strpos($url, $url_actual) !== false || strpos($url, "$url_actual/index") !== false);
    }

}